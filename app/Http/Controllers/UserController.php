<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Phonex\ContactList;
use Phonex\Events\AuditEvent;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\AddUserToClRequest;
use Phonex\Http\Requests\CreateUserRequest;
use Phonex\Http\Requests\DeleteContactRequest;
use Phonex\Http\Requests\NewLicenseRequest;
use Phonex\Http\Requests\UpdateUserRequest;
use Phonex\Jobs\CreateUserWithSubscriber;
use Phonex\Jobs\IssueProductLicense;
use Phonex\Jobs\RefreshSubscribers;
use Phonex\Model\ErrorReport;
use Phonex\Model\FoafProductType;
use Phonex\Model\Product;
use Phonex\Role;
use Phonex\Subscriber;
use Phonex\TrialRequest;
use Phonex\User;
use Phonex\Utils\DateRangeValidator;
use Phonex\Utils\InputGet;
use Phonex\Utils\InputPost;
use Phonex\Utils\Stats;
use Queue;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller {

	public function __construct()
	{
	}

	public function index(Request $request){
        $filteredProducts = [];
        if($request->has('products')){
            $filteredProducts = $request->get('products');
        }

        // Tuned-up query to load things quickly and enable sorting and filtering
        // TODO do native query to speed up things -- a lot is done in PHP code
        $query = User::select(['users.*'])
            ->join('subscriber_view', 'subscriber_view.username' ,'=' ,'users.username')
            ->leftJoin('licenses','licenses.user_id','=','users.id')
            ->leftJoin('products','products.id','=','licenses.product_id')
            ->groupBy('users.id')
            ->sortable()
            ->with('subscriber', 'groups', 'roles',
                'licenseProducts', 'licenseProducts.product', 'licenseProducts.product.translations');
        // avoid qa users
        $query = $query->where('qa_trial', false);

        // Lots of conditions
        if (!empty($request->get('last_activity_from'))){
            $lastActivityFrom = carbonFromInput($request->get('last_activity_from'));
            $query = $query->where('date_last_activity', '>=', $lastActivityFrom);
        }
        if ($request->has('username')){
            $query = $query->where('users.username', 'LIKE', "%" . $request->get('username') . "%");
        }

        if (!empty($filteredProducts)){
            $query = $query
                ->whereIn('products.id', $filteredProducts)
                ->whereNotNull('licenses.id');
        } else if ($request->get('with_licenses') == 1){
            $query = $query->whereNotNull('licenses.id');
        }

        /* Render FOAF Force layout graph using D3 */
        if($request->get('foaf') == '1'){
            $users = $query->get();
            return $this->getFoaf($users, true, $request->has('with-contacts-only'));
        }

        $products = Product::with('translations')->get();
        foreach($products as $product){
            $product->selected = in_array($product->id, $filteredProducts);
            $product->xname = $product->display_name ? $product->display_name : $product->name;
        }
        $products = $products->sortBy('xname');

        // this doesn't work because of complicated query
        $users = $query->paginate($request->get('limit', 20));
		return view('user.index', compact('users', 'products'));
    }


    private function getFoaf(Collection $users, $omitSupport = true, $onlyWithContacts = false)
    {
        $nodes = [];
        $links = [];
        $supportUser = "phonex-support";

        // First iteration - add all selected users (group 1)
        foreach($users as $user){
            $subscriber = $user->subscriber;
            if (!$subscriber){
                continue;
            }
            if ($omitSupport && $subscriber->username == $supportUser){
                continue;
            }

            if ($onlyWithContacts){
                // contacts count excluding support
                $contactsCount = count($subscriber->subscribersInContactList
                        ->filter(function($item) {
                            return $item->username != "phonex-support";
                        }));
                if ($contactsCount <= 0){
                    continue;
                }
            }

            $nodes[$subscriber->id] = $this->newFoafNode($subscriber->username, $subscriber->id, 'main', $user);

            // Add neighbor users (group 2)
            foreach($subscriber->subscribersInContactList as $friendSubscriber){
                if ($omitSupport && $friendSubscriber->username == $supportUser){
                    continue;
                }
                if (!isset($nodes[$friendSubscriber->id])){
                    $nodes[$friendSubscriber->id] = $this->newFoafNode($friendSubscriber->username, $friendSubscriber->id, 'slave');
                }
                $links[] = $this->newFoafLink($subscriber->id, $friendSubscriber->id);
            }
        }

        // Remove links that lead to users not present in nodes
//        foreach ($nodes as $node){
//            $links[] = $this->newFoafLink($subscriber->id, $friendSubscriber->id);
//        }

        // find out mapping between ids and array index (nodes and links are referenced by array index, not user id in D3)
        $ids = array_keys($nodes);
        $idToIndex = [];
        $d3Nodes = [];
        $d3Links = [];
        foreach ($nodes as $id => $node){
            $index = array_search($id, $ids);
            $idToIndex[$id] = $index;
            $d3Nodes[$index] = $node;
        }
        foreach ($links as $link){
            $d3Links[] = $this->newFoafLink($idToIndex[$link->source], $idToIndex[$link->target]);
        }

        $o = new \stdClass();
        $o->nodes = $d3Nodes;
        $o->links = $d3Links;
//        return json_encode($o);
//        return view('user.foaf_xml', ["graphData" => json_encode($o)]);
        return view('user.foaf_xml2', ["graphData" => json_encode($o)]);
    }

    private function newFoafLink($source, $target)
    {
        $e = new \stdClass();
        $e->source = $source;
        $e->target = $target;
        return $e;
    }

    private function extractJsonExtraData(User $user)
    {
        $lics = [];
        foreach ($user->licenseProducts as $licProd){
            $start = $licProd->starts_at ? $licProd->starts_at->format('d.m.Y') : "";
            $end = $licProd->expires_at ? $licProd->expires_at->format('d.m.Y') : "";
            $x = [
                'product'=>$licProd->product->display_name_or_name,
                'starts_at' => $start,
                'expires_at' => $end
            ];
            if ($licProd->comment){
                $x['comment'] = $licProd->comment;
            }
            $lics[] = $x;
        }

        $arr = ['username' => $user->username];
        if ($user->subscriber){
            $arr['last_activity'] = $user->subscriber->date_last_activity ? $user->subscriber->date_last_activity->format('d.m.Y') : "never";
            $arr['first_authentication'] = $user->subscriber->date_first_authCheck ? $user->subscriber->date_first_authCheck->format('d.m.Y') : "never";
        }
        $arr['licenses'] = $lics;

        if($user->comment){
            $arr['comment'] = $user->comment;
        }

        return json_encode($arr, JSON_PRETTY_PRINT);
    }

    private function newFoafNode($username, $userId, $group, $user = null)
    {
        $score = 1;
        $size = 1;
        $nodeColor = null;
        $extra = [];

        if ($group == 'main'){
//            $score = 0.9;
            $extra = $this->extractJsonExtraData($user);

            $license = RefreshSubscribers::getActiveLicenseWithLatestExpiration($user);
            $foafProductType = FoafProductType::getTypeByLicenseProduct($license);
            switch($foafProductType){
                case FoafProductType::TRIAL_UP_TO_YEAR:
                    $nodeColor = "Orange";
                    break;
                case FoafProductType::TRIAL_WEEK_AND_LESS:
                    $nodeColor = "Yellow";
                    break;
                case FoafProductType::INFINITE:
                    $nodeColor = "DarkSalmon";
                    break;
                case FoafProductType::FULL_B2B:
                    $nodeColor = "Red";
                    break;
                case FoafProductType::INAPP_B2C:
                    $nodeColor = "Blue";
                    break;
                case FoafProductType::NO_PRODUCT_AND_TESTS:
                default:
                    $nodeColor = "LightGray";
                    break;
            }
            $size = 20;
        } else if ($group == 'slave'){
//            $score = 0;
            $nodeColor = "LightGray";
            $size = 4;
        }
        $toRet = [
            'username' => $username,
            'user_id' =>$userId,
            'group' => $group,
            'type'=>'circle',
            'score'=>$score,
            'size'=>$size,
            'extra'=>$extra
        ];
        if ($nodeColor){
            $toRet['node_color'] = $nodeColor;
        }
        return $toRet;
    }


    //        $groups = Group::all();
//        foreach($groups as $group){
//            $group->selected = in_array($group->id, $filteredGroups);
//        }
//
// TODO when including groups, total count may not be correct, currently it's all 1, fix it
    //        $filteredGroups = [];
//        // Filter groups
//        if (\Request::has('user_group')){
//            $filteredGroups = \Request::get('user_group');
//            $query = User::select('users.*')
//                ->join('user_group', 'users.id', '=', 'user_group.user_id')
//                ->groupBy('users.id')
//                ->whereIn('group_id', $filteredGroups)
//                ->sortable()
//                ->with('subscriber', 'groups');
//        }

	public function create()
	{
        $products = Product::allForDirectSalePlatform()->sortBy('onum');
        $groups = Group::all();
        $roles = Role::all();

        $randomPassword = getRandomString(8, 'abcdefghjkmnpqrstuvwxyz23456789');
		return view('user.create', compact('products', 'groups', 'roles', 'randomPassword'));
	}


	public function store(CreateUserRequest $request)
    {
//        dd('check if roles+groups are valid and retrieve them');
        // TODO retrieve role + group IDs
        $groupIds = [];
        $roleIds = [];


        $defaultPassword = $request->get('password');
        $username = mb_strtolower($request->get('username'));
        $userRequest = new CreateUserWithSubscriber($username, $defaultPassword);
//        $userRequest->addAccess();
        if ($roleIds){
            $userRequest->addRoles($roleIds);
        }
        if ($groupIds){
            $userRequest->addGroups($groupIds);
        }

        if ($request->has('user-comment')){
            $userRequest->addComment($request->get('user-comment'));
        }

        $user = $this->dispatch($userRequest);
        $product = Product::find($request->get('product_id'));

        $licRequest = new IssueProductLicense($user, $product);
        $startsAt = carbonFromInput($request->get('starts_at'));//Carbon::createFromFormat("d-m-Y", $request->get('starts_at'));
        $licRequest->startingAt($startsAt);
        if ($request->has('comment')){
            $licRequest->setComment($request->get('comment'));
        }

        $this->dispatch($licRequest);

        // add support to contact list
        $supportAdded = ContactList::addSupportToContactListMutually($user);
        return Redirect::route('users.index')
            ->with('success', 'The new user ' . $user->username . ' + license has been created.' . ($supportAdded ? "Support account has been mutually added to contact list" : ""));
	}

	public function show($id)
    {
		$user = $this->getUserOr404($id);
		return view('user.show', compact('user'));
	}

    public function edit($id)
    {
        $user = $this->getUserOr404($id);
        return view('user.edit', compact('user'));
    }

    public function showLicenses($id)
    {
        $user = User::with([
            'licenses.licenseType',
            'licenses.product',
            'issuedLicenses.licenseType',
            'issuedLicenses.product',
            'subscriber.subscribersInContactList.user'])->find($id);

        if ($user == null){
            throw new NotFoundHttpException;
        }
        return view('user.show-lic', compact('user'));
    }

    public function showErrorReports($id)
    {
        $user = User::find($id);

        if ($user == null){
            throw new NotFoundHttpException;
        }

        $reports = ErrorReport::where('userName', $user->email)->orderBy('date_created','desc')->get();
        return view('user.show-error-reports', compact('user', 'reports'));
    }

    public function getNewLicense($id)
    {
        $user = $this->getUserOr404($id);
//        $products = Product::allForDirectSalePlatform()->sortBy('onum');
        $products = Product::allAvailable()->sortBy('name');
        return view('user.new-license', compact('user', 'products'));
    }

    public function postNewLicense($id, NewLicenseRequest $request)
    {
        $user = $this->getUserOr404($id);
        $product = Product::find($request->get('product_id'));

        $licRequest = new IssueProductLicense($user, $product);
        if ($request->has('comment')){
            $licRequest->setComment($request->get('comment'));
        }

        $startsAt = Carbon::createFromFormat("d-m-Y", $request->get('starts_at'));
        $licRequest->startingAt($startsAt);
        $this->dispatch($licRequest);

        return Redirect::route('users.licenses', [$id])
            ->with('success', 'New license to user ' . $user->username . ' has been issued.');
    }

    public function showCl($id)
    {
        $user = User::with([
            'subscriber.subscribersInContactList.user'])->find($id);

        if ($user == null){
            throw new NotFoundHttpException;
        }
        return view('user.show-cl', compact('user'));
    }

    public function showStats($id, Stats $stats)
    {
        $user = User::find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }

        $days = 90;

        $data = $stats->userLastActivity($user, $days);
        $labels = $stats->labelsPer($days, Stats::DAY);
        $labels = array_map(function($item){
            return '"' . $item . '"';
        }, $labels);

        return view('user.show-stats', compact('user', 'labels', 'data', 'days'));
    }

    public function showRegStats($id, Request $request, Stats $stats)
    {
        $user = User::find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }

        //'MM/DD hh:mm'
        $format = 'm/d H:i';
        $separator = '~';

        // subtract to be day ago - end of sunday
        $dateTo = Carbon::now()->addMinute();
        $dateFrom = Carbon::now()->subHours(1);
        if ($request->has('daterange')){
            list($dateFrom, $dateTo) = DateRangeValidator::retrieveDates($request->get('daterange'), $format, $separator);
//            $dateTo = $dateTo->endOfDay();
//            $dateFrom = $dateFrom->startOfDay();
        }
//        dd($dateTo);
        $daterange = $dateFrom->format($format) . " " . $separator . " " . $dateTo->format($format);
        $logs = $stats->regMonitorStats($user, $dateFrom, $dateTo);

        $dataPort = [];
        $dataCseq = [];
        $dataSockState = [];
        $dataNumRegs = [];
        $labels1 = [];
        foreach ($logs as $log){
            //dd($log);
            $dataPort[] = $log->port;
            $dataCseq[] = $log->cseq;
            $dataSockState[] = $log->sock_state ? 1 : 0;
            $dataNumRegs[] = $log->num_registrations;
            $labels1[] = $log->created_at->toTimeString();
        }

        $dataPort = json_encode($dataPort);
        $dataCseq = json_encode($dataCseq);
        $dataSockState = json_encode($dataSockState);
        $dataNumRegs = json_encode($dataNumRegs);
        $labels1 = json_encode($labels1);

        return view('user.show-reg-stats', compact('user', 'daterange',
            'labels1', 'dataPort', 'dataCseq', 'dataSockState', 'dataNumRegs'));
    }


	public function update($id, UpdateUserRequest $request)
    {
        $user = User::find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }

        $has_access = InputPost::has('has_access') ? 1 : 0;

        if ($user->has_access != ($has_access)){
            if ($has_access == 1){
                // Adding access
                $user->password = bcrypt(InputPost::get('password'));
                $user->has_access = 1;
                $user->save();
            } else {
                // Removing access
                $user->password = '';
                $user->has_access = 0;
                $user->save();
            }
            return Redirect::route('users.show', [$user->id])
                ->with('success', 'User has been updated.');
        }

        return Redirect::route('users.show', [$user->id]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


    /* Few actions */

    /**
     * Delete all successful trial requests for user with this username
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetTrialCounter($id)
    {
        $user = User::find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }

        $counter = TrialRequest::where(["username" => $user->username, "isApproved"=>1])->delete();

        return redirect()
            ->back()
            ->with("success", 'Trial counter has been reset (' . $counter . ' row(s) were deleted).');
    }

    /**
     * For testing purposes
     */
    public function pushLicUpdate($id)
    {
        $user = User::find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }

        RefreshSubscribers::refreshSingleUser($user, false);
        Queue::push('licenseUpdated', ['username' => $user->username."@phone-x.net"], 'users');

        return redirect()
            ->back()
            ->with("success", 'License push has been sent.');
    }

    /**
     * Logout user on all devices
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceLogout($id)
    {
        $user = User::find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }
        // force logout of users on all devices -- aka kill switch
        Queue::push('logout', ['username' => $user->email], 'users');
        return redirect()
            ->back()
            ->with("success", 'User has been logged out on all devices.');
    }

    public function changePassword($id, Request $request)
    {
        $newPassword = $request->get('password');
        $data = ['password' => $newPassword, 'id' => $id];

        $v = Validator::make($data, [
            'id' => 'required|exists:users,id',
            'password' => 'required|min:8',
        ]);

        if ($v->fails()){
            return redirect()
                ->back()
                ->withInput($data)
                ->withErrors($v->errors());
        }

        $user = User::find($id);
        if(!$user->subscriber_id){
            // redirect for naughty boys
            return redirect()->back();
        }

        $sipUser = Subscriber::find($user->subscriber_id);
        $sipUser->setPasswordFields($newPassword);
        $sipUser->forcePasswordChange = 1;
        $sipUser->save();

        // audit this
        event(AuditEvent::update('subscriber', $user->subscriber_id, 'password'));

        return Redirect::route('users.edit', [$user->id])
            ->with('success', 'User SIP password has been reset.');
    }

    public function addUserToCl($user_id, AddUserToClRequest $request)
    {
        $user = User::find($user_id);
        $userToAdd = User::findByUsername($request->get('username'));
        try {
            if ($user->subscriber->subscribersInContactList->contains($userToAdd->subscriber)){
                return redirect()
                    ->back()
                    ->withErrors(['User is already in contact list.']);
            } else {
                if (!$request->has('alias')){
                    $user->addToContactList($userToAdd);
                } else {
                    $user->addToContactList($userToAdd, $request->get('alias'));
                }
            }
        } catch (\Exception $e) {
            Log::error("patchAddUserToCl; cannot check if user is in cl", [$user->username, $userToAdd->username, $e]);
            return redirect()
                ->back()
                ->withErrors(['Server error: Cannot add user to contact list']);

        }
        return Redirect::route('users.show', [$user->id])
            ->with('success', 'User has been added to contact list');

    }

    public function deleteContact($userId, $contactUserId, DeleteContactRequest $request){
        $user = User::find($userId);
        $userToRemove = User::find($contactUserId);
        try {
            $user->removeFromContactList($userToRemove);
        } catch (\Exception $e){

            Log::error("deleteContact; problem", [$userId, $contactUserId, $e]);
            return redirect()
                ->back()
                ->withErrors(['Server error: Cannot remove user']);

        }
        return Redirect::route('users.show', [$user->id])
            ->with('success', 'User has been removed from contact list');
    }

    private function getUserOr404($id){
        $user = User::find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }
        return $user;
    }
}
