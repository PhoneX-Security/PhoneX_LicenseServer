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
use Phonex\ContactList;
use Phonex\LicenseFuncType;
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
    //eue::push('ContactListUpdated', ['username'=>$user], 'users');
    //echo 'amqp message sent to requested user ';
});


Route::get('test_code', function(){
    $code = BusinessCode::getCode('qqqqqqq');
    echo "$code";
});

Route::get('test_issue', function(){
//    $user = User::getByUsername('smoulinka');
//    $licType = LicenseType::where('name', 'month')->first();
//    $licFuncType = LicenseFuncType::getFull();
//
//    Bus::dispatch(new IssueLicense($user, $licType, $licFuncType));
});



Route::get('test_connect', function(){
//    $prefix = "miro";
//    $masterNum = 5;
//    $poolNum = 20;
//
//    $masters = [];
//    $pools = [];
//
//    for ($i=1; $i<=5; $i++){
//        $masters[] = $prefix . '0' . $i;
//    }
//
//    for ($i=6; $i<=9; $i++){
//        $pools[] = $prefix . '0' . $i;
//    }
//
//    for ($i=10; $i<=25; $i++){
//        $pools[] = $prefix . $i;
//    }
//
//    $mainUser = User::where('username', 'miro01')->first();
//    $masterUsers = [];
//    $poolUsers = [];
//
//    foreach($masters as $name){
//        $masterUsers[] = User::where('username', $name)->first();
//    }
//
//    foreach($pools as $name){
//        $poolUsers[] = User::where('username', $name)->first();
//    }
//
////    // pool licenses have only main user
////    foreach($poolUsers as $user){
////        ContactList::addUsersToContactListMutually($mainUser, $user);
////    }
//
//    foreach($masterUsers as $u){
//        foreach($masterUsers as $uu){
//            if ($u != $uu){
////                echo $u->username . ' + ' . $uu->username . '<br />';
//                $u->addToContactList($uu);
//            }
//        }
//    }
});