<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Phonex\Http\Requests;
use Phonex\Http\Requests\CreateUserRequest;
use Phonex\Http\Requests\UpdateUserRequest;
use Phonex\License;
use Phonex\LicenseType;
use Phonex\SipUser;
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


	public function index()
	{
        $limit = InputGet::getInteger('limit', 15);

		$query = User::sortable();
        if(InputGet::has('username')){
            $query = $query->where('username', 'LIKE', "%" . InputGet::getAlphaNum('username') . "%");
        }

        $users = $query->paginate($limit);
		return view('user.index', compact('users'));
	}

	public function create()
	{
		$licenseTypes = LicenseType::all()->sortBy('order');
//        dd($licenseTypes);
//        all();
		return view('user.create', compact('licenseTypes'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(CreateUserRequest $request)
	{
		// all data should be valid at the moment (see @CreateUserRequest#rules)
		$user = new User();
		$user->username = InputPost::get('username');
		$user->email = $user->username . "@phone-x.net"; //InputPost::get('email');
		if (InputPost::has('has_access')){
			$user->password = bcrypt(InputPost::get('password'));
			$user->has_access = 1;
		} else {
			$user->has_access = 0;
		}
		$user->save();

        // store license
        if (InputPost::has('issue_license')){
            $licenseType = LicenseType::find(InputPost::getInteger('license_type_id'));
            $issuer = User::where('username', InputPost::get('issuer_username'))->first();

            $startsAt = InputPost::getCarbonTime('starts_at')->toDateTimeString();
            $expiresAt = InputPost::getCarbonTime('starts_at')->addDays($licenseType->days);

            $license = new License();
            $license->user_id = $user->id;
            $license->license_type_id = $licenseType->id;
            $license->issuer_id = $issuer->id;
            $license->comment = InputPost::get('comment');
            $license->starts_at = $startsAt;
            $license->expires_at = $expiresAt;
            $license->save();

            $sipUser = SipUser::createSubscriber($user->username, InputPost::get('sip_default_password'), $startsAt, $expiresAt);
            $sipUser->save();

//            dd($sipUser->id);

            $user->subscriber_id = $sipUser->id;
            $user->save();
//            update(['subscriber_id' => $sipUser->id]);

            return Redirect::route('users.index')
                ->with('success', 'The new user ' . $user->username . ' with license has been created.');
        }

		return Redirect::route('users.index')
			->with('success', 'The new user ' . $user->username . ' has been created.');
	}

	public function show($id){
//		$user = User::find($id);
        // Dot is used for nested loading
		$user = User::with(['licenses.licenseType', 'issuedLicenses.licenseType'])->find($id);
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

	public function edit($id){
        $user = User::with('licenses.licenseType')->find($id);
        if ($user == null){
            throw new NotFoundHttpException;
        }

        return view('user.edit', compact('user'));
	}

	public function update($id, UpdateUserRequest $request){
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

}
