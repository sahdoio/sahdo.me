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
     *  Content routes
     */
    Route::get('/admin/pages/home', ['as' => 'admin.pages.home', 'uses' => 'Admin\PagesController@home']);
    Route::post('/admin/pages/home/update', ['as' => 'admin.pages.home.update', 'uses' => 'Admin\PagesController@update_home']);
    Route::get('/admin/pages/about', ['as' => 'admin.pages.about', 'uses' => 'Admin\PagesController@about']);
    Route::post('/admin/pages/about/update', ['as' => 'admin.pages.about.update', 'uses' => 'Admin\PagesController@update_about']);
    Route::get('/admin/pages/contact', ['as' => 'admin.pages.contact', 'uses' => 'Admin\PagesController@contact']);
    Route::post('/admin/pages/contact/update', ['as' => 'admin.pages.contact.update', 'uses' => 'Admin\PagesController@update_contact']);

    /**
     * Banners
     */
    Route::get('/admin/banners', ['as' => 'admin.banners', 'uses' => 'Admin\BannerController@banners']);
    Route::get('/admin/banners/new', ['as' => 'admin.banners.new', 'uses' => 'Admin\BannerController@new']);
    Route::get('/admin/banners/edit/{id}', ['as' => 'admin.banners.edit', 'uses' => 'Admin\BannerController@edit']);
    Route::post('/admin/banners/create', ['as' => 'admin.banners.create', 'uses' => 'Admin\BannerController@create']);
    Route::post('/admin/banners/update/{id}', ['as' => 'admin.banners.update', 'uses' => 'Admin\BannerController@update']);
    Route::post('/admin/banners/delete/{id}', ['as' => 'admin.banners.delete', 'uses' => 'Admin\BannerController@delete']);

    /**
     * Services
     */

    // views
    Route::get('/admin/services', ['as' => 'admin.services', 'uses' => 'Admin\ServiceController@services']);
    Route::get('/admin/services/new', ['as' => 'admin.services.new', 'uses' => 'Admin\ServiceController@new']);
    Route::get('/admin/services/edit/{id}', ['as' => 'admin.services.edit', 'uses' => 'Admin\ServiceController@edit']);
    Route::post('/admin/services/create', ['as' => 'admin.services.create', 'uses' => 'Admin\ServiceController@create']);

    // ajax and post and post
    Route::get('/admin/services/table', ['as' => 'admin.services.table', 'uses' => 'Admin\ServiceController@table']);
    Route::post('/admin/services/update/{id}', ['as' => 'admin.services.update', 'uses' => 'Admin\ServiceController@update']);
    Route::post('/admin/services/delete/{id}', ['as' => 'admin.services.delete', 'uses' => 'Admin\ServiceController@delete']);
    Route::post('/admin/services/update_info', ['as' => 'admin.services.update_info', 'uses' => 'Admin\ServiceController@update_info']);

    /**
     * Albums
     */

    // views
    Route::get('/admin/albums', ['as' => 'admin.albums', 'uses' => 'Admin\AlbumController@albums']);
    Route::get('/admin/albums/new', ['as' => 'admin.albums.new', 'uses' => 'Admin\AlbumController@new']);
    Route::get('/admin/albums/edit/{id}', ['as' => 'admin.albums.edit', 'uses' => 'Admin\AlbumController@edit']);

    // ajax and post and post
    Route::post('/admin/albums/create', ['as' => 'admin.albums.create', 'uses' => 'Admin\AlbumController@create']);
    Route::post('/admin/albums/update', ['as' => 'admin.albums.update', 'uses' => 'Admin\AlbumController@update']);
    Route::post('/admin/albums/delete', ['as' => 'admin.albums.delete', 'uses' => 'Admin\AlbumController@delete']);

    /**
     * Videos
     */

    // views
    Route::get('/admin/videos', ['as' => 'admin.videos', 'uses' => 'Admin\VideoController@videos']);
    Route::get('/admin/videos/new', ['as' => 'admin.videos.new', 'uses' => 'Admin\VideoController@new']);
    Route::get('/admin/videos/edit/{id}', ['as' => 'admin.videos.edit', 'uses' => 'Admin\VideoController@edit']);

    // ajax and post and post
    Route::post('/admin/videos/create', ['as' => 'admin.videos.create', 'uses' => 'Admin\VideoController@create']);
    Route::post('/admin/videos/update', ['as' => 'admin.videos.update', 'uses' => 'Admin\VideoController@update']);
    Route::post('/admin/videos/delete', ['as' => 'admin.videos.delete', 'uses' => 'Admin\VideoController@delete']);

    /**
     * Jobs
     */

    // views
    Route::get('/admin/jobs', ['as' => 'admin.jobs', 'uses' => 'Admin\JobsController@jobs']);
    Route::get('/admin/jobs/new', ['as' => 'admin.jobs.new', 'uses' => 'Admin\JobsController@new']);
    Route::get('/admin/jobs/edit/{id}', ['as' => 'admin.jobs.edit', 'uses' => 'Admin\JobsController@edit']);

    // ajax and post and post
    Route::post('/admin/jobs/create', ['as' => 'admin.jobs.create', 'uses' => 'Admin\JobsController@create']);
    Route::post('/admin/jobs/update/{id}', ['as' => 'admin.jobs.update', 'uses' => 'Admin\JobsController@update']);
    Route::post('/admin/jobs/delete/{id}', ['as' => 'admin.jobs.delete', 'uses' => 'Admin\JobsController@delete']);

    /**
     * Members
     */

    // views
    Route::get('/admin/members', ['as' => 'admin.members', 'uses' => 'Admin\MemberController@members']);
    Route::get('/admin/members/new', ['as' => 'admin.members.new', 'uses' => 'Admin\MemberController@new']);
    Route::get('/admin/members/edit/{id}', ['as' => 'admin.members.edit', 'uses' => 'Admin\MemberController@edit']);
    Route::post('/admin/members/create', ['as' => 'admin.members.create', 'uses' => 'Admin\MemberController@create']);

    // ajax
    Route::get('/admin/members/table', ['as' => 'admin.members.table', 'uses' => 'Admin\MemberController@table']);
    Route::post('/admin/members/update/{id}', ['as' => 'admin.members.update', 'uses' => 'Admin\MemberController@update']);
    Route::post('/admin/members/delete/{id}', ['as' => 'admin.members.delete', 'uses' => 'Admin\MemberController@delete']);


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
     * Clients
     */

    // views
    Route::get('/admin/clients', ['as' => 'admin.clients', 'uses' => 'Admin\ClientController@clients']);
    Route::get('/admin/clients/new', ['as' => 'admin.clients.new', 'uses' => 'Admin\ClientController@new']);
    Route::get('/admin/clients/edit/{id}', ['as' => 'admin.clients.edit', 'uses' => 'Admin\ClientController@edit']);

    // ajax and post and post
    Route::post('/admin/clients/create', ['as' => 'admin.clients.create', 'uses' => 'Admin\ClientController@create']);
    Route::get('/admin/clients/table', ['as' => 'admin.clients.table', 'uses' => 'Admin\ClientController@table']);
    Route::post('/admin/clients/update/{id}', ['as' => 'admin.clients.update', 'uses' => 'Admin\ClientController@update']);
    Route::post('/admin/clients/delete/{id}', ['as' => 'admin.clients.delete', 'uses' => 'Admin\ClientController@delete']);

    /**
     * Team
     */

    // views
    Route::get('/admin/team', ['as' => 'admin.team', 'uses' => 'Admin\TeamController@team']);
    Route::get('/admin/team/new', ['as' => 'admin.team.new', 'uses' => 'Admin\TeamController@new']);
    Route::get('/admin/team/edit/{id}', ['as' => 'admin.team.edit', 'uses' => 'Admin\TeamController@edit']);

    // ajax and post and post
    Route::post('/admin/team/create', ['as' => 'admin.team.create', 'uses' => 'Admin\TeamController@create']);
    Route::get('/admin/team/table', ['as' => 'admin.team.table', 'uses' => 'Admin\TeamController@table']);
    Route::post('/admin/team/update/{id}', ['as' => 'admin.team.update', 'uses' => 'Admin\TeamController@update']);
    Route::post('/admin/team/delete/{id}', ['as' => 'admin.team.delete', 'uses' => 'Admin\TeamController@delete']);

    /**
     * Schedule
     */

    // views
    Route::get('/admin/schedule', ['as' => 'admin.schedule', 'uses' => 'Admin\ScheduleController@schedule']);
    Route::get('/admin/schedule/new', ['as' => 'admin.schedule.new', 'uses' => 'Admin\ScheduleController@new']);
    Route::get('/admin/schedule/edit/{id}', ['as' => 'admin.schedule.edit', 'uses' => 'Admin\ScheduleController@edit']);

    // ajax and post and post
    Route::post('/admin/schedule/create', ['as' => 'admin.schedule.create', 'uses' => 'Admin\ScheduleController@create']);
    Route::get('/admin/schedule/table', ['as' => 'admin.schedule.table', 'uses' => 'Admin\ScheduleController@table']);
    Route::post('/admin/schedule/update/{id}', ['as' => 'admin.schedule.update', 'uses' => 'Admin\ScheduleController@update']);
    Route::post('/admin/schedule/delete/{id}', ['as' => 'admin.schedule.delete', 'uses' => 'Admin\ScheduleController@delete']);

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
     * License
     */

    // views
    Route::get('/admin/license/{id}', ['as' => 'admin.license', 'uses' => 'Admin\LicenseController@license']);
    Route::get('/admin/license/export/{id}', ['as' => 'admin.license.export', 'uses' => 'Admin\LicenseController@export']);
    Route::get('/admin/license/template/{id}', ['as' => 'admin.license.template', 'uses' => 'Admin\LicenseController@template']);

    /**
     * Letter
     */

    // views
    Route::get('/admin/letter/{id}', ['as' => 'admin.letter', 'uses' => 'Admin\LetterController@letter']);
    Route::get('/admin/letter/export/{id}', ['as' => 'admin.letter.export', 'uses' => 'Admin\LetterController@export']);
    Route::get('/admin/letter/template/{id}', ['as' => 'admin.letter.template', 'uses' => 'Admin\LetterController@template']);

    /**
     * Documents
     */
    Route::get('/admin/docs', ['as' => 'admin.docs', 'uses' => 'Admin\DocsController@docs']);

    /**
     * Settings routes
     */
    Route::get('/admin/settings/user/{user_id}', ['as' => 'admin.settings.user', 'uses' => 'Admin\SettingsController@user']);
    Route::get('/admin/settings/users', ['as' => 'admin.settings.users', 'uses' => 'Admin\SettingsController@users']);
    Route::get('/admin/settings/preferences', ['as' => 'admin.settings.preferences', 'uses' => 'Admin\SettingsController@preferences']);
});

