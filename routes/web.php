<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Website
 */
Route::group(['middleware' => 'session_verify'], function () {
    Route::get('/', ['as' => 'home', 'uses' => 'WebsiteController@home']);
    Route::get('/home', ['as' => 'home', 'uses' => 'WebsiteController@home']);
    Route::get('/blog', ['as' => 'blog', 'uses' => 'WebsiteController@blog']);
    Route::get('/blog/{id}', ['as' => 'blog.post', 'uses' => 'WebsiteController@post']);
    Route::get('/notfound', ['as' => 'notfound', 'uses' => 'WebsiteController@notfound']);
});

/**
 * Mail
 */
Route::post('sendMail', ['as' => 'sendMail', 'uses' => 'MailController@sendMail']);

/**
 * Access
 */
//Route::get('/notfound', ['as' => 'notfound', 'uses' => 'WebsiteController@notfound']);
Route::get('/building', ['as' => 'building', 'uses' => 'WebsiteController@building']);

/**
 *  Login routes
 */
Route::get('/login', ['as' => 'login', 'uses' => 'LoginController@index']);
Route::post('/login/in', ['as' => 'login.in', 'uses' => 'LoginController@in']);
Route::get('/login/out', ['as' => 'login.out', 'uses' => 'LoginController@out']);

/**
 * Protect routes from unauthenticated access
 */
Route::group(['middleware' => 'check_auth'], function() {
    /**
     *  Analytics routes
     */
    Route::get('/adm', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@dashboard']);
    Route::get('/panel', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@dashboard']);
    Route::get('/admin/dashboard', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@dashboard']);

    /**
     * Users
     */

    // views
    Route::get('/admin/users', ['as' => 'admin.users', 'uses' => 'Admin\UsersController@users'])->middleware('only_admin');
    Route::get('/admin/users/new', ['as' => 'admin.users.new', 'uses' => 'Admin\UsersController@new'])->middleware('only_admin');
    Route::get('/admin/users/edit/{id}', ['as' => 'admin.users.edit', 'uses' => 'Admin\UsersController@edit'])->middleware('user_and_admin');

    // ajax and post and post and post
    Route::post('/admin/users/create', ['as' => 'admin.users.create', 'uses' => 'Admin\UsersController@create'])->middleware('only_admin');
    Route::get('/admin/users/table', ['as' => 'admin.users.table', 'uses' => 'Admin\UsersController@table'])->middleware('only_admin');
    Route::post('/admin/users/update/{id}', ['as' => 'admin.users.update', 'uses' => 'Admin\UsersController@update'])->middleware('user_and_admin');
    Route::post('/admin/users/delete/{id}', ['as' => 'admin.users.delete', 'uses' => 'Admin\UsersController@delete'])->middleware('only_admin');


    /**
     * Blog
     */

    // views
    Route::get('/admin/blog', ['as' => 'admin.blog', 'uses' => 'Admin\BlogController@blog']);
    Route::get('/admin/blog/blog', ['as' => 'admin.blog.new', 'uses' => 'Admin\BlogController@new']);
    Route::get('/admin/blog/edit/{id}', ['as' => 'admin.blog.edit', 'uses' => 'Admin\BlogController@edit']);

    // ajax and post and post
    Route::post('/admin/blog/create', ['as' => 'admin.blog.create', 'uses' => 'Admin\BlogController@create']);
    Route::get('/admin/blog/table', ['as' => 'admin.blog.table', 'uses' => 'Admin\BlogController@table']);
    Route::post('/admin/blog/update/{id}', ['as' => 'admin.blog.update', 'uses' => 'Admin\BlogController@update']);
    Route::post('/admin/blog/delete/{id}', ['as' => 'admin.blog.delete', 'uses' => 'Admin\BlogController@delete']);

    /**
     * Settings routes
     */
    Route::get('/admin/settings/user/{user_id}', ['as' => 'admin.settings.user', 'uses' => 'Admin\SettingsController@user']);
    Route::get('/admin/settings/users', ['as' => 'admin.settings.users', 'uses' => 'Admin\SettingsController@users']);
    Route::get('/admin/settings/preferences', ['as' => 'admin.settings.preferences', 'uses' => 'Admin\SettingsController@preferences']);
});
