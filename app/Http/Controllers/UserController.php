<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Phonex\Commands\CreateSubscriberWithLicense;
use Phonex\Commands\CreateUser;
use Phonex\ContactList;
use Phonex\Events\AuditEvent;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\CreateUserRequest;
use Phonex\Http\Requests\UpdateUserRequest;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\User;
use Phonex\Utils\InputGet;
use Phonex\Utils\InputPost;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller {


	public function __construct()
	{
		// Automatically added in routes.php
//		$this->middleware('auth');
	}


	public function index(){
        $limit = InputGet::getInteger('limit', 15);

        $query = User::sortable()->with('subscriber', 'groups');
        $filteredGroups = [];
        // Filter groups
        if (\Request::has('user_group')){
            $filteredGroups = \Request::get('user_group');
            $query = User::join('user_group', 'phonex_users.id', '=', 'user_group.user_id')
                ->groupBy('phonex_users.id')
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
		return view('user.create', compact('licenseTypes', 'licenseFuncTypes'));
	}


	public function store(CreateUserRequest $request)	{
		// all data should be valid at the moment (see @CreateUserRequest#rules)
        $userRequest = new CreateUser($request->get('username'));
        if ($request->has('has_access')){
            $userRequest->addAccess($request->get('password'));
        }
        $user = $this->dispatch($userRequest);

        if (InputPost::has('issue_license')){
            $licenseType = LicenseType::find($request->get('license_type_id'));
            $licenseFuncType = LicenseFuncType::find($request->get('license_func_type_id'));
            $defaultSipPass = $request->get('sip_default_password');

            $licRequest = new CreateSubscriberWithLicense($user, $licenseType, $licenseFuncType, $defaultSipPass);
            $this->dispatch($licRequest);

            // add support to contact list
            $supportAdded = ContactList::addSupportToContactListMutually($user);
            return Redirect::route('users.index')
                ->with('success', 'The new user ' . $user->username . ' + license has been created.' . ($supportAdded ? "Support account has been mutually added to contact list" : ""));
        }

		return Redirect::route('users.index')
			->with('success', 'The new user ' . $user->username . ' has been created.');
	}

	public function show($id)
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

		return view('user.show', compact('user'));
	}

	public function edit($id)
    {
        $user = User::with('licenses.licenseType')->find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }

        return view('user.edit', compact('user'));
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

    public function patchChangeSipPassword($id)
    {
        $newPass = InputPost::get('sip_default_password');

        $data = ['sip_default_password' => $newPass, 'id' => $id];

        $v = Validator::make($data, [
            'id' => 'required|exists:phonex_users,id',
            'sip_default_password' => 'required|min:8',
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
        $sipUser->setPasswordFields($newPass);
        $sipUser->forcePasswordChange = 1;
        $sipUser->save();

        // audit this
        event(AuditEvent::update('subscriber', $user->subscriber_id, 'password'));

        return Redirect::route('users.show', [$user->id])
            ->with('success', 'User SIP password has been reset.');
    }

}
