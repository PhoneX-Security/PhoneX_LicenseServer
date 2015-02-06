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

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('test', function(){
//	return 'test';
	return \Phonex\User::all();
	//return View::make('test');


//    dd('test');
//    $hasher = new \Illuminate\Hashing\BcryptHasher();
//
//    $user = User::where('username', 'gorila')->first();
//    $user->password = $hasher->make('buso');
//    $x = $user->save();
});
