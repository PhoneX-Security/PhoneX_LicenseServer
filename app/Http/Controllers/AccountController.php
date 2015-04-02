<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Phonex\BusinessCode;
use Phonex\ContactList;
use Phonex\License;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\TrialRequest;
use Phonex\User;
use Securimage;

class AccountController extends Controller {
    const VERSION = 1;

    const RESP_OK = 200;
    const RESP_ERR_INVALID_REQUEST = 400;
    const RESP_ERR_BAD_CAPTCHA = 401;
    const RESP_ERR_TRIAL_EXISTS = 402;
    const RESP_ERR_UNKNOWN_ERROR = 403;
    const RESP_ERR_EXISTING_USERNAME = 404;
    const RESP_ERR_USERNAME_BAD_FORMAT = 405;
    const RESP_ERR_BAD_BUSINESS_CODE = 406;
    const RESP_ERR_ALREADY_USED_BUSINESS_CODE = 407;
    const RESP_ERR_UNSUPPORTED_VERSION = 408;

    const USERNAME_REGEX = "/^[A-Za-z0-9_-]{3,18}$/";

    const TEST_NON_QA_IP = "127.0.0.2";

    private $securimage;
    private $phonexIP;

    function __construct(){
        $this->securimage = new Securimage();
    }

    public function getCaptcha(){
        $img = new Securimage();

        $captchaHeight =  intval(Request::input("height", 120));
        $captchaHeight = min($captchaHeight, 384);

        // You can customize the image by making changes below, some examples are included - remove the "//" to uncomment
        //$img->session_name = 'phone-x';
        //$img->ttf_file        = './Quiff.ttf';
        //$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
        //$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
        $img->image_height    = $captchaHeight;                                // height in pixels of the image
        $img->image_width     = $img->image_height * M_E;          // a good formula for image size based on the height
        $img->perturbation    = 1.4;                               // 1.0 = high distortion, higher numbers = more distortion
        //$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color
        //$img->text_color      = new Securimage_Color("#EAEAEA");   // captcha text color
//        $img->num_lines       = 15;                                 // how many lines to draw over the image
        //$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image
        //$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image
        //$img->signature_color = new Securimage_Color(rand(0, 64),
        //                                             rand(64, 128),
        //                                             rand(128, 255));  // random signature color

        // see securimage.php for more options that can be set

        $img->show();  // outputs the image and content headers to the browser
        // alternate use:
        // $img->show('/path/to/background_image.jpg');
    }

    /**
     * GET alias for creating trial account
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTrial(Request $request){
        return $this->postTrial($request);
    }

    /**
     * POST for creating trial account
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postTrial(Request $request){
        $username = null;
        try {
            $this->checkCompulsoryFields($request, ['version', 'captcha', 'imei']);
            $this->checkValidRequestVersion($request);
            $this->checkCorrectCaptcha($request);

            if ($request->has('username')){
                // username has to be lowercase
                $username = strtolower($request->get('username'));
                // check username validity
                $this->checkUsernameValid($username);
            }
        } catch (\Exception $e){
            Log::error("postTrial; verification checked failed", [$e]);
            return $this->responseError($e->getCode());
        }

        $isValid = $this->isValidRequest($request);

        $trialRequest = new TrialRequest();
        $trialRequest->fill($request->all());
        $trialRequest->ip = $request->getClientIp();
        $trialRequest->isApproved = $isValid;
        if ($username){
            $trialRequest->username = $username;
        }
        $trialRequest->save();

        if ($isValid) {
            return $this->issueTrial($trialRequest);
        } else {
            Log::error("Request is not valid.");
            return $this->responseError(self::RESP_ERR_TRIAL_EXISTS);
        }
    }

    /**
     * POST for creating account using business code
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function postCreateCodeAccount(Request $request){
        try {
            $this->checkCompulsoryFields($request, ['version', 'captcha', 'imei', 'username', 'bcode']);
            $this->checkValidRequestVersion($request);
            $this->checkCorrectCaptcha($request);
            $this->checkUsernameValid($request->get('username'));
            $this->checkCorrectBusinessCode($request);

            // TODO add transaction code here

        } catch (\Exception $e){
            return $this->responseError($e->getCode());
        }
        return $this->issueCodeAccount($request);
    }

    private function issueCodeAccount(Request $request){
        $isQaTrial = $this->isPhonexIp();
        $trialNumber = $this->getMaxTrialNumber($isQaTrial) + 1;

        $username = (!$isQaTrial) ? $request->get('username') : 'trial' . $trialNumber;
        $password = rand(100000, 999999);
        $businessCode = BusinessCode::where('code', $request->get('bcode'))->first();

        try {
            $user = $this->issueAccount($username,
                $password,
                $businessCode->licenseType(),
                [$businessCode->group->id],
                $isQaTrial,
                $trialNumber);

            // TODO add contact list mappings

        } catch (\Exception $e){
            return $this->responseError($e->getCode());
        }

        $expiresAtUnixTime = strtotime($user->subscriber()->expires_on);
        return $this->responseOk($user->username, $user->email, $password, $expiresAtUnixTime);
    }

    private function issueTrial(TrialRequest $trialRequest){
        $isQaTrial = $this->isPhonexIp(\Input::getClientIp());
        $trialNumber = $this->getMaxTrialNumber($isQaTrial) + 1;

        $username = ($trialRequest->username) ? $trialRequest->username : 'trial' . $trialNumber;

        if ($isQaTrial){
            $username = 'qa_trial' . $trialNumber;
        }
        $licenseType = LicenseType::find(1); // MAGIC ID - trial is #1
        $password = rand(100000, 999999);

        try {
            $user = $this->issueAccount($username, $password, $licenseType, array(), $isQaTrial, $trialNumber);
        } catch (\Exception $e){
            Log::error("issueTrial; cannot create trial account", [$e]);
            return $this->responseError(self::RESP_ERR_UNKNOWN_ERROR);
        }

        // save user id to trial request
        $trialRequest->phonexUserId = $user->id;
        $trialRequest->save();

        $expiresAtUnixTime = strtotime($user->subscriber->expires_on);

        return $this->responseOk($user->username, $user->email, $password, $expiresAtUnixTime);
    }

    /**
     * Main function for account creation
     * TODO Should be unified with account creation in UserController and moved to Commands folder as a command type
     * @param $username
     * @param $password
     * @param LicenseType $licenseType
     * @param array $groupsId
     * @param bool $isQaTrial
     * @param null $trialNumber
     * @return User
     * @throws \Exception
     */
    private function issueAccount($username, $password, LicenseType $licenseType, $groupsId = array(), $isQaTrial = false, $trialNumber = null){
        $user = new User();
        $user->username = $username;
        $user->email = $user->username . "@phone-x.net";
        $user->has_access = 0;
        $user->trialNumber = $trialNumber;
        $user->confirmed = 1;
        $user->qa_trial = $isQaTrial ? 1 : 0;
        $saved = $user->save();

        // allow user to try again
        if(!$saved){
            Log::error("Cannot create record in PhoneX_users table");
            throw new \Exception("Cannot save User");
        }

        // assign groups
        if (!empty($groupsId)){
            $user->groups()->attach($groupsId);
        }

        $startsAt = Carbon::now()->toDateTimeString();
        $c_expiresAt = Carbon::now()->addDays($licenseType->days);
        $expiresAt = $c_expiresAt->toDateTimeString();

        $license = new License();
        $license->user_id = $user->id;
        $license->license_type_id = $licenseType->id;
        $license->starts_at = $startsAt;
        $license->expires_at = $expiresAt;
        $license->save();


        // Create a new user on the SOAP server
        $subscriber = Subscriber::createSubscriber($user->username, $password, $startsAt, $expiresAt);
        $savedSipUser = $subscriber->save();

        $user->subscriber_id = $subscriber->id;
        $user->save();

        // if sip user creation fails, allow to try again
        if (!$savedSipUser) {
            Log::error("Cannot create subscriber in SOAP subscriber list.");
            throw new \Exception("Cannot save User");
        }

        // add support to contact list
        if (!$isQaTrial){
            ContactList::addSupportToContactListMutually($user);
        }

        return $user;
    }

    private function checkCorrectBusinessCode(Request $request){
        $codeParam = $request->get('bcode');
        $code = null;
        // parity check
        try {
            if (!BusinessCode::parityCheck($codeParam)){
                Log::error("Requested business code has bad parity", [$request->all()]);
                throw new \Exception("", self::RESP_ERR_BAD_BUSINESS_CODE);
            }
            $code = BusinessCode::where('code', $codeParam)->firstOrFail();
        } catch (\Exception $e){
            Log::error("Requested business code was not found in DB or has invalid format", [$e, $request->all()]);
            throw new \Exception("", self::RESP_ERR_BAD_BUSINESS_CODE);
        }

        $numberOfUsers = count($code->users);
        if ($numberOfUsers >= $code->users_limit){
            Log::error("Requested business code is already used by #" . $numberOfUsers . " users", [$request->all()]);
            throw new \Exception("", self::RESP_ERR_BAD_BUSINESS_CODE);
        }
    }

    private function responseError($code){
        return response()->json(['version' => self::VERSION, 'responseCode' => $code]);
    }

    private function checkValidRequestVersion(Request $request){
        $version = intval($request->get('version'));
        if ($version != AccountController::VERSION){
            Log::error("Invalid version ".$version.", only ". AccountController::VERSION . " is supported.");
            throw new \Exception("", self::RESP_ERR_UNSUPPORTED_VERSION);
        }
    }

    private function checkUsernameValid($username){
        if (!preg_match(AccountController::USERNAME_REGEX, $username)){
            Log::error("Username " . $username . " doesn't match username regex.");
            throw new \Exception("", self::RESP_ERR_USERNAME_BAD_FORMAT);
        } else if (User::where('username', $username)->count() > 0 ||
            Subscriber::where('username', $username)->count() > 0){
            Log::error("Username with name " . $username . " already exists");
            throw new \Exception("", self::RESP_ERR_EXISTING_USERNAME);
        }
    }

    private function checkCompulsoryFields(Request $request, array $fields){
        foreach($fields as $field){
            if (!$request->has($field)){
                Log::error("Missing some of the compulsory fields", $fields, $_REQUEST);
                throw new \Exception("", self::RESP_ERR_INVALID_REQUEST);
            }
        }
    }

    private function checkCorrectCaptcha(Request $request) {
        // for testing purposes
        if (\Input::getClientIp() == self::TEST_NON_QA_IP){
            return;
        }

        $captcha = $request->get('captcha');

        $this->isPhonexIp();

        if ($this->isPhonexIp() && $captcha === 'captcha'){
            return;
        }

        if ($this->securimage->check($captcha) == false) {
            Log::error("Bad captcha entered [received=" . $captcha . " ]");
            throw new \Exception("", self::RESP_ERR_BAD_CAPTCHA);
        }
    }

    private function isValidRequest(Request $request){
        $ip = $request->getClientIp();
        $imei = $request->get('imei');
        $dateThresholdStart = dbDatetime(strtotime('-14 days'));
        $dateThresholdEnd = dbDatetime(strtotime('-6 days'));

        $count =  TrialRequest::where('isApproved', 1)
            ->where('imei', $imei)
            ->where('dateCreated', "<=", $dateThresholdStart)
            ->where('dateCreated', ">=",$dateThresholdEnd )
            ->count();

        if ($count > 0){
            return false;
        } else {
            return true;
        }
    }


    private function getMaxTrialNumber($isQaTrial = false){
        return User::where('qa_trial', ($isQaTrial ? "1" : "0"))
            ->max('trialNumber');
    }

    private function responseOk($username, $sip, $password, $expirationTimestamp){
        return response()->json(
            [
                'version' => self::VERSION,
                'responseCode' => self::RESP_OK,
                'username' => $username,
                'sip' => $sip,
                'password' => $password,
                'expirationTimestamp' => $expirationTimestamp
            ]
        );
    }

    private function getPhonexIp(){
        if (!$this->phonexIP){
            $this->phonexIP = gethostbyname('ioffice.phone-x.net');
        }

        return $this->phonexIP;
    }

    private function isPhonexIp(){
        $remote = \Input::getClientIp();
        return $remote == $this->getPhonexIp() || $remote == '127.0.0.1';
    }
}
