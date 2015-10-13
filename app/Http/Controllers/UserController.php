<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
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
use Phonex\Model\ErrorReport;
use Phonex\Model\Product;
use Phonex\Role;
use Phonex\Subscriber;
use Phonex\TrialRequest;
use Phonex\User;
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

	public function index(){
        $limit = InputGet::getInteger('limit', 15);

        $query = User::sortable()->with('subscriber', 'groups', 'roles');
        $filteredGroups = [];
        // Filter groups
        if (\Request::has('user_group')){
            $filteredGroups = \Request::get('user_group');
            $query = User::select('users.*')
                ->join('user_group', 'users.id', '=', 'user_group.user_id')
                ->groupBy('users.id')
                ->whereIn('group_id', $filteredGroups)
                ->sortable()
                ->with('subscriber', 'groups');
        }

        // avoid qa users
        $query = $query->where('qa_trial', false);

        if(InputGet::has('username')){
            $query = $query->where('username', 'LIKE', "%" . InputGet::getAlphaNum('username') . "%");
        }

        $users = $query->paginate($limit);
        // TODO when including groups, total count may not be correct, currently it's all 1, fix it


        $groups = Group::all();
        foreach($groups as $group){
            $group->selected = in_array($group->id, $filteredGroups);
        }

		return view('user.index', compact('users', 'groups'));
	}

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
        $username = $request->get('username');
        $userRequest = new CreateUserWithSubscriber($username, $defaultPassword);
        $userRequest->addAccess();
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
        $products = Product::allAvailable()->sortBy('onum');
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

    public function showRegStats($id, Stats $stats)
    {
        $user = User::find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }

        $days = 2;
        $logs = $stats->regMonitorStats($user);

        $dataPort = [];
        $dataCseq = [];
        $dataSockState = [];
        $labels1 = [];
        foreach ($logs as $log){
            //dd($log);
            $dataPort[] = $log->port;
            $dataCseq[] = $log->cseq;
            $dataSockState[] = $log->sock_state ? 1 : 0;
            $labels1[] = $log->created_at->toTimeString();
        }

        $dataPort = json_encode($dataPort);
        $dataCseq = json_encode($dataCseq);
        $dataSockState = json_encode($dataSockState);
        $labels1 = json_encode($labels1);

        return view('user.show-reg-stats', compact('user', 'days',
            'labels1', 'dataPort', 'dataCseq', 'dataSockState'));
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
