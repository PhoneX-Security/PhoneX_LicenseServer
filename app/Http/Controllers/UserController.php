<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Phonex\Http\Requests;
use Phonex\Http\Requests\CreateUserRequest;
use Phonex\LicenseType;
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
		$users = User::sortable()->paginate(15);
		return view('user.index', compact('users'));
	}

	public function create()
	{
		$licenseTypes = LicenseType::all();
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

		}

		return Redirect::route('users.index')
			->with('success', 'The new user ' . $user->username . ' has been created.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
//		$user = User::find($id);
        // Dot is used for nested loading
		$user = User::with('licenses.licenseType')->find($id);
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

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
