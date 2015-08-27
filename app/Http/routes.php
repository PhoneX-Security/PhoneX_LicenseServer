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
use Phonex\BusinessCode;
use Phonex\BusinessCodesExport;
use Phonex\ContactList;
use Phonex\Jobs\IssueLicense;
use Phonex\Group;
use Phonex\Jobs\NewCodePairsExport;
use Phonex\Jobs\RefreshSubscribers;
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

// API for auto notifications via support account
Route::get('api/support-notifications/batch', 'Api\SupportNotificationsController@getBatch');
Route::post('api/support-notifications/batch', 'Api\SupportNotificationsController@postBatch');

// Authenticated pages
Route::group(['middleware' => ['auth', 'acl']], function() {
    Route::get('/', 'HomeController@index');

    /* Users + details */
	Route::resource('users', 'UserController');
    // CL
    Route::get('users/{user}/licenses', ['as' => 'users.licenses', 'uses' => 'UserController@showLicenses']);
    Route::get('users/{user}/cl', ['as' => 'users.cl', 'uses' => 'UserController@showCl']);
    Route::get('users/{user}/stats', ['as' => 'users.stats', 'uses' => 'UserController@showStats']);

    // Contact lists + alternative via get
    Route::delete('users/{user}/cl', ['uses' => 'UserController@deleteContact']);
    Route::delete('users/{user}/cl/{contactUser}', ['uses' => 'UserController@deleteContact']);
    Route::get('users/{user}/cl/delete/{contactUser}', ['uses' => 'UserController@deleteContact']);

    Route::get('users/{users}/new-license', ['as' => 'users.new-license', 'uses' => 'UserController@getNewLicense']);
    Route::post('users/{users}/new-license', ['as' => 'users.new-license', 'uses' => 'UserController@postNewLicense']);
    Route::patch('users/{users}/change-password', ['as' => 'users.change_password', 'uses' => 'UserController@changePassword']);
    Route::patch('users/{users}/add-user-to-cl', ['as' => 'users.add_user_to_cl', 'uses' => 'UserController@addUserToCl']);

    /* Group */
    Route::resource('groups', 'GroupController');
    Route::get('groups/{user}/users', ['as' => 'groups.users', 'uses' => 'GroupController@showUsers']);
    Route::get('groups/{user}/bcodes', ['as' => 'groups.bcodes', 'uses' => 'GroupController@showCodes']);

    /* Licenses */
    Route::resource('licenses', 'LicenseController', ['only' => ['index', 'edit', 'update', 'destroy']]);
    Route::get('licenses/delete/{id}', ['as'=>'licenses.delete', 'uses' => 'LicenseController@destroy']);

    /* Business Codes */
    Route::controller('bcodes', 'BusinessCodeController');

    /* Statistics */
    Route::controller('stats', 'StatsController');

    /* Admin only pages */
    Route::get('logs', ['acl-resource' => 'logs',
        'uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'
    ]);
    Route::any('adminer', ['acl-resource' => 'adminer',
        'uses' => '\Miroc\LaravelAdminer\AdminerController@index']);
});
