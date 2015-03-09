<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Log;
use Phonex\License;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\TrialRequest;
use Phonex\User;
use Request;
use Securimage;

class TrialController extends Controller {

    const VERSION = 1;
    const invalidRequestJson = '{"version":1,"responseCode":400}';
    const badCaptchaJson = '{"version":1,"responseCode":401}';
    const trialExistsJson = '{"version":1,"responseCode":402}';
    const unknownErrorJson = '{"version":1,"responseCode":403}';

    private $securimage;
    private $phonexIP;

    function __construct(){
        $this->securimage = new Securimage();
    }

    public function getCaptcha(){
        $img = new Securimage();
        // You can customize the image by making changes below, some examples are included - remove the "//" to uncomment
        //$img->session_name = 'phone-x';
        //$img->ttf_file        = './Quiff.ttf';
        //$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
        //$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
        $img->image_height    = 120;                                // height in pixels of the image
        $img->image_width     = $img->image_height * M_E;          // a good formula for image size based on the height
        $img->perturbation    = 1.2;                               // 1.0 = high distortion, higher numbers = more distortion
        //$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color
        //$img->text_color      = new Securimage_Color("#EAEAEA");   // captcha text color
        $img->num_lines       = 15;                                 // how many lines to draw over the image
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

    public function getTrial(){
        $version = intval(Request::get('version'));
        $captcha = Request::input('captcha');
        $imei = Request::input('imei');

        if (!$version || !$captcha || !$imei){
            Log::error("Missing some of the request fields (version, captcha, imei)", compact('version', 'captcha', 'imei'));
            return TrialController::invalidRequestJson;
        }

        if ($version != TrialController::VERSION){
            Log::error("Invalid version ".$version.", only ". TrialController::VERSION . " is supported.");
            return TrialController::invalidRequestJson;
        }

        // can be ipv4 or ipv6, depends on
        $ip = $_SERVER['REMOTE_ADDR'];

        if (!$this->correctCaptcha($captcha)){
            Log::error("Bad captcha entered [received=" . $captcha . ", correct=" . $this->securimage->getCode() . "]");
            return TrialController::badCaptchaJson;
        }
        $isValid = $this->isValidRequest($imei, $ip);

        $trialRequest = new TrialRequest();
        $trialRequest->captcha = $captcha;
        $trialRequest->ip = $ip;
        $trialRequest->imei = $imei;
        $trialRequest->isApproved = $isValid;
        $trialRequest->save();

        if ($isValid) {
            return $this->issueTrial($trialRequest);
        } else {
            Log::error("Request is not valid.");
            return TrialController::trialExistsJson;
        }
    }

    private function isValidRequest($imei, $ip){
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

    private function issueTrial(TrialRequest $trialRequest){
        $trialNumber = $this->getMaxTrialNumber($this->isPhonexIp()) + 1;

        $username = 'trial' . $trialNumber;

        if ($this->isPhonexIp()){
            $username = 'qa_' . $username;
        }

        // all data should be valid at the moment
        $user = new User();
        $user->username = $username;
        $user->email = $user->username . "@phone-x.net";
        $user->has_access = 0;
        $user->trialNumber = $trialNumber;
        $user->confirmed = 1;
        $saved = $user->save();

        // allow user to try again
        if(!$saved){
            Log::error("Cannot create record in PhoneX_users table");
            return TrialController::unknownErrorJson;
        }

        // Create license
        $licenseType = LicenseType::find(1); // MAGIC ID - trial is #1

        $startsAt = Carbon::now()->toDateTimeString();
        $expiresAt = Carbon::now()->addDays($licenseType->days)->toDateTimeString();

        $license = new License();
        $license->user_id = $user->id;
        $license->license_type_id = $licenseType->id;
        $license->starts_at = $startsAt;
        $license->expires_at = $expiresAt;
        $license->save();

        $password = rand(100000, 999999);

        // Create a new user on the SOAP server
        $subscriber = Subscriber::createSubscriber($user->username, $password, $startsAt, $expiresAt);
        $savedSipUser = $subscriber->save();

        $user->subscriber_id = $subscriber->id;
        $user->save();

        // if sip user creation fails, allow to try again
        if (!$savedSipUser) {
            Log::error("Cannot create subscriber in SOAP subscriber list.");
            return TrialController::unknownErrorJson;
        }

        // save user id to trial request
        $trialRequest->phonexUserId = $user->id;
        $trialRequest->save();

        return $this->responseOk($user->username, $subscriber->email_address, $password, $expiresAt);
    }

    private function getMaxTrialNumber($isQaTrial = false){
        return User::where('qa_trial', ($isQaTrial ? "1" : "0"))
            ->max('trialNumber');
    }

    private function responseOk($username, $sip, $password, $trialEnd){
        $data = array(
            'version' => 1,
            'responseCode' => 200,
            'username' => $username,
            'sip' => $sip,
            'password' => $password,
            'expirationTimestamp' => $trialEnd
        );

        return json_encode($data);
    }


    private function getPhonexIp(){
        if (!$this->phonexIP){
            $this->phonexIP = gethostbyname('ioffice.phone-x.net');
        }

        return $this->phonexIP;
    }

    private function isPhonexIp(){
        $remote = $_SERVER['REMOTE_ADDR'];
        return $remote == $this->getPhonexIp();
    }

    private function correctCaptcha($captcha) {
        if ($this->isPhonexIp() && $captcha == 'captcha'){
            return true;
        }

        if ($this->securimage->check($captcha) == false) {
            return false;
        } else {
            return true;
        }
    }
}
