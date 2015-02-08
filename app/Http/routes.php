<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Phonex\User;

//Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');
Route::get('/', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('login', 'Auth\AuthController@getLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

Route::resource('users', 'UserController');

//Route::get('test', function(){
//	return 'test';
//	return \Phonex\User::all();
//return View::make('test');
//	$hasher = new \Illuminate\Hashing\BcryptHasher();
//    dd($hasher);
//    $user = User::where('username', 'gorila')->first();
//    $user->password = $hasher->make('buso');
//	$user->has_access = true;
//	$user->email = 'gorila@phone-x.net';
//    $x = $user->save();
//	dd($x);
//});
