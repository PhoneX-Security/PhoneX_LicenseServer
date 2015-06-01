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

// Non authenticated pages
Route::resource('products', 'Api\ProductController', ['only' => ['show', 'index']]);
Route::controller('qa', 'QaController'); // qa tools

// Authenticated pages
Route::group(['middleware' => 'auth'], function() {
	Route::resource('users', 'UserController');
    Route::resource('groups', 'GroupController');
    Route::patch('users/{users}/change-sip-pass', ['as' => 'users.change_sip_pass', 'uses' => 'UserController@patchChangeSipPassword']);
    Route::patch('users/{users}/add-user-to-cl', ['as' => 'users.add_user_to_cl', 'uses' => 'UserController@patchAddUserToCl']);
	Route::resource('licenses', 'LicenseController', ['only' => ['index', 'edit', 'update']]);

    // dev tools
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'); // logs viewer
    Route::controller('bcodes', 'BusinessCodeController');
});





Route::get('x', function(){
    $x = User::findByUsername('test318');
    $x->password = bcrypt('bbbbbbbb1');
    $x->save();


//    $code = BusinessCode::getCode('qqqqqqq');
//    echo "$code";
});

//Route::get('test_i', function(){
//    $u =  User::getByUsername('w1pko');
//    $licFuncType = LicenseFuncType::getFull();
//    $licType = LicenseType::findByName('half_year');
//
//    $c = new IssueLicense($u, $licType, $licFuncType);
//    Bus::dispatch($c);
//});


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