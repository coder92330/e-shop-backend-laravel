<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where the routes are registered for our application.
|
*/

// Named route required for SendsPasswordResetEmails.

Route::group([
    'middleware' => 'cors',
    'prefix' => ''
], function ($router) {
    Route::post('login', 'Auth\LoginController@loginOp');
    Route::get('profile/view', 'Auth\LoginController@indexOp');
    Route::post('logout', 'Auth\LogoutController@logout');
    // Route::post('register', 'Auth\RegisterController@register');
    // Route::post('forgot-password', 'Auth\ForgotPasswordController@email');
    // Route::post('password-reset', 'Auth\ResetPasswordController@reset');
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'level'
], function ($router) {
    Route::get("/view", "LevelController@indexOp");
    Route::post("/get", "LevelController@getLevelById");
    Route::post("/add", "LevelController@addOp");
    Route::post("/edit", "LevelController@editOp");
    Route::post("/delete", "LevelController@deleteOp");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'member'
], function ($router) {
    Route::get("/view", "MemberController@indexOp");
    Route::post("/add", "MemberController@addOp");
    Route::post("/edit", "MemberController@editOp");
    Route::post("/delete", "MemberController@deleteOp");
    Route::post("/family", "MemberController@getFamily");
    Route::post("/get", "MemberController@getMemberById");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'goods'
], function ($router) {
    Route::get("/view", "GoodsController@indexOp");
    Route::post("/add", "GoodsController@addOp");
    Route::post("/edit", "GoodsController@editOp");
    Route::post("/delete", "GoodsController@deleteOp");
    Route::post("/get", "GoodsController@getGoodById");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'proxy'
], function ($router) {
    Route::get("/view", "ProxyController@indexOp");
    Route::post("/add", "ProxyController@addOp");
    Route::post("/edit", "ProxyController@editOp");
    Route::post("/delete", "ProxyController@deleteOp");
    Route::post("/get", "ProxyController@getProxyById");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'browser'
], function ($router) {
    Route::get("/view", "BrowserController@indexOp");
    Route::post("/add", "BrowserController@addOp");
    Route::post("/edit", "BrowserController@editOp");
    Route::post("/delete", "BrowserController@deleteOp");
    Route::post("/get", "BrowserController@getBrowserById");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'nvlogin'
], function ($router) {
    Route::get("/view", "NvloginController@indexOp");
    Route::post("/add", "NvloginController@addOp");
    Route::post("/edit", "NvloginController@editOp");
    Route::post("/delete", "NvloginController@deleteOp");
    Route::post("/get", "NvloginController@getNvloginById");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'surfing'
], function ($router) {
    Route::get("/view", "SurfingController@indexOp");
    Route::post("/add", "SurfingController@addOp");
    Route::post("/edit", "SurfingController@editOp");
    Route::post("/delete", "SurfingController@deleteOp");
    Route::post("/get", "SurfingController@getSurfingById");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'machine'
], function ($router) {
    Route::get("/view", "MachineController@indexOp");
    Route::post("/add", "MachineController@addOp");
    Route::post("/edit", "MachineController@editOp");
    Route::post("/delete", "MachineController@deleteOp");
    Route::post("/get", "MachineController@getMachineById");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'job'
], function ($router) {
    Route::get("/view", "JobController@indexOp");
    Route::post("/add", "JobController@addOp");
    Route::post("/edit", "JobController@editOp");
    Route::post("/delete", "JobController@deleteOp");
    Route::post("/get", "JobController@getJobById");
    Route::post("/getlogin", "JobController@getNvloginList");
    Route::post("/getmachine", "JobController@getMachineList");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'history'
], function ($router) {
    Route::post("/", "HistoryController@indexOp");
    Route::post("/add", "HistoryController@addOp");
    Route::post("/clear", "HistoryController@clear");
});

Route::group([
    'middleware' => 'cors',
    'prefix' => 'log'
], function ($router) {
    Route::post("/", "LogController@indexOp");
    Route::post("/add", "LogController@addOp");
    Route::post("/clear", "LogController@clear");
});

// Route::get('reset-password', function() {
//     return view('index');
// })->name('password.reset');

// // Catches all other web routes.
// Route::get('{slug}', function () {
//     return view('index');
// })->where('slug', '^(?!api).*$');
