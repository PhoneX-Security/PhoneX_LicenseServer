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

// Non authenticated pages
use Phonex\Jobs\CreateUserWithSubscriber;
use Phonex\Jobs\IssueProductLicense;
use Phonex\Jobs\RefreshSubscribers;
use Phonex\Model\Product;
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

Route::resource('products', 'Api\ProductControllerLegacy', ['only' => ['show', 'index']]);
Route::controller('qa', 'QaController'); // qa tools
Route::get('api/support-notifications/batch', 'Api\SupportNotificationsController@getBatch');
Route::post('api/support-notifications/batch', 'Api\SupportNotificationsController@postBatch');
Route::get('api/support-notification/{user}/{notification}', 'Api\SupportNotificationsController@getNotificationForUser');

Route::any('api/version-check', 'Api\VersionCheckController@versionCheck');

// Authenticated API using client certs
Route::group(['prefix'=>'api/auth/', 'middleware'=>['auth.client_cert']], function(){
    Route::get('products/apple', 'Api\ProductController@getAppleProducts');
    Route::get('products/google', 'Api\ProductController@getGoogleProducts');

    Route::get('products/available', 'Api\ProductController@getAvailableProducts');
    Route::get('products/purchased', 'Api\ProductController@getPurchasedProducts');
    Route::get('products/list-from-licenses', 'Api\ProductController@getLicensesProducts'); // List arbitrary user's products by providing license ids
    Route::any('purchase/appstore/payment-verif', 'Api\PurchaseController@postApplePaymentVerification');
    Route::any('purchase/play/payment-verif', 'Api\PurchaseController@postAndroidPaymentVerification');
    Route::any('purchase/play/payments-verif', 'Api\PurchaseController@postAndroidPaymentsVerification');
});

// Authenticated pages using client credentials
Route::group(['middleware' => ['auth', 'acl']], function() {
    Route::get('/', 'HomeController@index');

    /* Users + details */
	Route::resource('users', 'UserController');
    Route::post('users/{user}/reset-trial-counter', ['as'=>'users.reset-trial-counter', 'uses'=>'UserController@resetTrialCounter']);
    Route::get('users/{user}/push-lic-update', ['as'=>'users.push-lic-update', 'uses'=>'UserController@pushLicUpdate']);
    Route::post('users/{user}/force-logout', ['as'=>'users.force-logout', 'uses'=>'UserController@forceLogout']);
    // CL
    Route::get('users/{user}/licenses', ['as' => 'users.licenses', 'uses' => 'UserController@showLicenses']);
    Route::get('users/{user}/cl', ['as' => 'users.cl', 'uses' => 'UserController@showCl']);
    Route::get('users/{user}/stats', ['as' => 'users.stats', 'uses' => 'UserController@showStats']);
    Route::get('users/{user}/reg-stats', ['as' => 'users.reg-stats', 'uses' => 'UserController@showRegStats']);
    Route::get('users/{user}/error-reports', ['as' => 'users.error-reports', 'uses' => 'UserController@showErrorReports']);

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

    /* Reports */
    Route::get('reports/last-errors', 'ReportsController@lastErrors');
    Route::get('reports/last-trial-requests', 'ReportsController@lastTrialRequests');
    Route::post('reports/reset-trial-counter', 'ReportsController@resetTrialCounter');

    /* Admin only pages */
    Route::get('logs', ['acl-resource' => 'logs',
        'uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'
    ]);
    Route::any('adminer', ['acl-resource' => 'adminer',
        'uses' => '\Miroc\LaravelAdminer\AdminerController@index']);
});
