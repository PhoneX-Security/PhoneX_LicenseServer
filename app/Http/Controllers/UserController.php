<?php namespace Phonex\Http\Controllers;

use Phonex\Http\Requests;
use Phonex\Http\Requests\CreateUserRequest;
use Phonex\LicenseType;
use Phonex\User;
use Phonex\Utils\InputGet;
use Phonex\Utils\InputPost;
use Redirect;

class UserController extends Controller {


	public function __construct()
	{
//		$this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = User::sortable()->paginate(15);
		return view('user.index', compact('users'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{

		$licenseTypes = LicenseType::all();
//		dd($licenseTypes);


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
		$user->email = InputPost::get('email');
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
		//
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
