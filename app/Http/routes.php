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

use Phonex\BusinessCode;
use Phonex\Commands\IssueLicense;
use Phonex\LicenseType;
use Phonex\User;

Route::get('home', 'HomeController@index');
Route::get('/', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
    'trial' => 'AccountController',
    'account' => 'AccountController', //alias
]);

Route::get('login', 'Auth\AuthController@getLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

// Authenticated pages
Route::group(['middleware' => 'auth'], function() {
	Route::resource('users', 'UserController');
    Route::resource('groups', 'GroupController');
    Route::patch('users/{users}/change-sip-pass', ['as' => 'users.change_sip_pass', 'uses' => 'UserController@patchChangeSipPassword']);
	Route::resource('licenses', 'LicenseController', ['only' => ['index', 'edit', 'update']]);

    // dev tools
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'); // logs viewer

    Route::controller('bcodes', 'BusinessCodeController');
});


/* Helper routes */
Route::get('x', function(){
    //$user = Request::get('where', "test610@phone-x.net");
    //Queue::push('ContactListUpdated', ['username'=>$user], 'users');
    //echo 'amqp message sent to requested user ';
});

Route::get('test_del', function(){
//    $u = User::where('username', 'qa_trial128')->first();
//    $u->deleteWithLicenses();
});

Route::get('test_issue', function(){
//    $licType = LicenseType::find(3); // month license
//
//    $u1 = User::where('username', 'trial01')->first();
//    $u2 = User::where('username', 'trial02')->first();
//
//    $command1 = new IssueLicense($u1, $licType);
//    $command2 = new IssueLicense($u2, $licType);
//    Bus::dispatch($command1);
//    Bus::dispatch($command2);
});


Route::get('test_code', function(){
    $code = BusinessCode::getCode('qqqqqqq');
    echo "$code";
});


//Route::get('passr', function(){
//    $user = User::where('email', 'test318@phone-x.net')->first();
//    $user->password = bcrypt("to be done");
////    $user->save();
//});
