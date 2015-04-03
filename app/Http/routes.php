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
    $user = Request::get('where', "test610@phone-x.net");
    Queue::push('ContactListUpdated', ['username'=>$user], 'users');
    echo 'amqp message sent to requested user ';
});

Route::get('test_del', function(){
//    $u = User::where('username', 'qa_trial128')->first();
//    $u->deleteWithLicenses();
});


Route::get('test_code', function(){
    $code = BusinessCode::getCode('qqqqqqq');
    echo "$code";
});

Route::get('test_mail', function(){
    $email = 'svitok@phone-x.net';

    $r = Mail::raw('check me out2', function($message) use ($email)
    {
        $message->from('license-server@phone-x.net', 'License server');
        $message->to($email)->subject('Mobil Pohotovost: new code pairs');
    });
    dd($r);
});




//Route::get('passr', function(){
//    $user = User::where('email', 'test318@phone-x.net')->first();
//    $user->password = bcrypt("to be done");
////    $user->save();
//});
