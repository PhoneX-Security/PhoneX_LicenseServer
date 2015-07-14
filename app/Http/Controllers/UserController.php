<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Phonex\Jobs\CreateSubscriberWithLicense;
use Phonex\Jobs\CreateUser;
use Phonex\Jobs\IssueLicense;
use Phonex\ContactList;
use Phonex\Events\AuditEvent;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\AddUserToClRequest;
use Phonex\Http\Requests\CreateUserRequest;
use Phonex\Http\Requests\DeleteContactRequest;
use Phonex\Http\Requests\NewLicenseRequest;
use Phonex\Http\Requests\UpdateUserRequest;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Role;
use Phonex\Subscriber;
use Phonex\User;
use Phonex\Utils\InputGet;
use Phonex\Utils\InputPost;
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
		$licenseTypes = LicenseType::all()->sortBy('order');
        $licenseFuncTypes = LicenseFuncType::all()->sortBy('order');
        $groups = Group::all();
        $roles = Role::all();

        $randomPassword = getRandomString(8, 'abcdefghjkmnpqrstuvwxyz23456789');
		return view('user.create', compact('licenseTypes', 'licenseFuncTypes', 'groups', 'roles', 'randomPassword'));
	}


	public function store(CreateUserRequest $request)
    {
//        dd('check if roles+groups are valid and retrieve them');
        // TODO retrieve role + group IDs
        $groupIds = [];
        $roleIds = [];

        $defaultPassword = $request->get('password');
        $userRequest = new CreateUser($request->get('username'), $groupIds);
        $userRequest->addAccess($defaultPassword);
        if ($roleIds){
            $userRequest->addRoles($roleIds);
        }

        $user = $this->dispatch($userRequest);

        $licenseType = LicenseType::find($request->get('license_type_id'));
        $licenseFuncType = LicenseFuncType::find($request->get('license_func_type_id'));

        $licRequest = new CreateSubscriberWithLicense($user, $licenseType, $licenseFuncType, $defaultPassword);
        $startsAt = Carbon::createFromFormat("d-m-Y", $request->get('starts_at'));
        $licRequest->startingAt($startsAt);
        $this->dispatch($licRequest);

        // add support to contact list
        $supportAdded = ContactList::addSupportToContactListMutually($user);
        return Redirect::route('users.index')
            ->with('success', 'The new user ' . $user->username . ' + license has been created.' . ($supportAdded ? "Support account has been mutually added to contact list" : ""));
	}

	public function show($id)
    {
		$user = $user = $this->getUserOr404($id);
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
            'issuedLicenses.licenseType',
            'subscriber.subscribersInContactList.user'])->find($id);

        if ($user == null){
            throw new NotFoundHttpException;
        }

        foreach($user->licenses as $license){
            if (!$license->expires_at || Carbon::now()->gt(Carbon::parse($license->expires_at))) {
                $license->active = false;
            } else {
                $license->active = true;
            }
        }

        return view('user.show-lic', compact('user'));
    }

    public function getNewLicense($id)
    {
        $user = $this->getUserOr404($id);
        $licenseTypes = LicenseType::all()->sortBy('order');
        $licenseFuncTypes = LicenseFuncType::all()->sortBy('order');
        return view('user.new-license', compact('user', 'licenseTypes', 'licenseFuncTypes'));
    }

    public function postNewLicense($id, NewLicenseRequest $request)
    {
        $user = $this->getUserOr404($id);
        $licenseType = LicenseType::find($request->get('license_type_id'));
        $licenseFuncType = LicenseFuncType::find($request->get('license_func_type_id'));

        $licRequest = new IssueLicense($user, $licenseType, $licenseFuncType);
        if ($request->has('comment')){
            $licRequest->setComment($request->get('comment'));
        }

        $startsAt = Carbon::createFromFormat("d-m-Y", $request->get('starts_at'));
        $licRequest->startingAt($startsAt);
        $this->dispatch($licRequest);

        return Redirect::route('users.licenses', [$id])
            ->with('success', 'New license to user ' . $user->username . ' has been issued.');
    }

    public function showCl($id){
        $user = User::with([
            'subscriber.subscribersInContactList.user'])->find($id);

        if ($user == null){
            throw new NotFoundHttpException;
        }
        return view('user.show-cl', compact('user'));
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
                redirect()
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
