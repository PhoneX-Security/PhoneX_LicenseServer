<?php

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
Route::group(['middleware' => ['auth', 'acl']], function() {
    Route::get('/', 'HomeController@index');

	Route::resource('users', 'UserController');
    Route::patch('users/{users}/change-sip-pass', ['as' => 'users.change_sip_pass', 'uses' => 'UserController@patchChangeSipPassword']);
    Route::patch('users/{users}/add-user-to-cl', ['as' => 'users.add_user_to_cl', 'uses' => 'UserController@patchAddUserToCl']);

    Route::resource('groups', 'GroupController');

    // contact lists
    Route::delete('users/{user}/cl/{contactUser}', ['uses' => 'UserController@deleteContact']);
    // alternative via get
    Route::get('users/{user}/cl/delete/{contactUser}', ['uses' => 'UserController@deleteContact']);

    Route::resource('licenses', 'LicenseController', ['only' => ['index', 'edit', 'update']]);

    Route::controller('bcodes', 'BusinessCodeController');

    /* Admin only pages */
    Route::get('logs', ['acl-resource' => 'logs',
        'uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'
    ]);
    Route::any('adminer', ['acl-resource' => 'adminer',
        'uses' => '\Miroc\LaravelAdminer\AdminerController@index']);
});

//Route::get('test_i', function(){
////    $u =  User::getByUsername('zas03');
////    $licFuncType = LicenseFuncType::getFull();
////    $licType = LicenseType::findByName('year');
////    $c = new IssueLicense($u, $licType, $licFuncType);
//
//});


//Route::get('test_connect', function(){
////    $prefix = "miro";
////    $masterNum = 5;
////    $poolNum = 20;
////
////    $masters = [];
////    $pools = [];
////
////    for ($i=1; $i<=5; $i++){
////        $masters[] = $prefix . '0' . $i;
////    }
////
////    for ($i=6; $i<=9; $i++){
////        $pools[] = $prefix . '0' . $i;
////    }
////
////    for ($i=10; $i<=25; $i++){
////        $pools[] = $prefix . $i;
////    }
////
////    $mainUser = User::where('username', 'miro01')->first();
////    $masterUsers = [];
////    $poolUsers = [];
////
////    foreach($masters as $name){
////        $masterUsers[] = User::where('username', $name)->first();
////    }
////
////    foreach($pools as $name){
////        $poolUsers[] = User::where('username', $name)->first();
////    }
////
//////    // pool licenses have only main user
//////    foreach($poolUsers as $user){
//////        ContactList::addUsersToContactListMutually($mainUser, $user);
//////    }
////
////    foreach($masterUsers as $u){
////        foreach($masterUsers as $uu){
////            if ($u != $uu){
//////                echo $u->username . ' + ' . $uu->username . '<br />';
////                $u->addToContactList($uu);
////            }
////        }
////    }
//});