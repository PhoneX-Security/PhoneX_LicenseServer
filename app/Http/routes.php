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

use Carbon\Carbon;
use Phonex\TrialRequest;

Route::get('home', 'HomeController@index');
Route::get('/', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
    'trial' => 'TrialController'
]);

Route::get('login', 'Auth\AuthController@getLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

// Authenticated pages
Route::group(['middleware' => 'auth'], function() {
	Route::resource('users', 'UserController');
    Route::patch('users/{users}/change-sip-pass', ['as' => 'users.change_sip_pass', 'uses' => 'UserController@patchChangeSipPassword']);
	Route::resource('licenses', 'LicenseController', ['only' => ['index', 'edit', 'update']]);

    // dev tools
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'); // logs viewer
});

Route::get('x', function(){
    $user = Request::get('where', "test610@phone-x.net");
    Queue::push('ContactListUpdated', ['username'=>$user], 'users');
    echo 'amqp message sent to user ' . $user;
});
