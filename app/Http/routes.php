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
//Route::get('/', 'WelcomeController@index');

use Carbon\Carbon;
use Phonex\SipUser;
use Phonex\User;

Route::get('home', 'HomeController@index');
Route::get('/', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('login', 'Auth\AuthController@getLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

// Authenticated pages
Route::group(['middleware' => 'auth'], function() {
	Route::resource('users', 'UserController');
	Route::resource('licenses', 'LicenseController', ['only' => ['index', 'edit', 'update']]);
});

Route::get('x', function(){

//    $p = SipUser::createSubscriber('buso', 'buso', Carbon::now()->toDateTimeString(), Carbon::now()->toDateTimeString());

    $x = User::where('username', 'test84')->first();
    $x->has_access = 1;
    $x->password = bcrypt('MercedeS30422');
    $x->save();

    dd($x);
});
