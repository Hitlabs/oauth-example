<?php

use Illuminate\Support\Facades\Route;

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
//ELB Healthcheck
Route::get('/healthcheck', function () { return 'ok'; });
//Redirect home
Route::get('/', function () { return redirect('/home'); });
//Registration
Auth::routes(['verify' => true]);
//OAuth Handshake
Route::any('/oauth/error', 'OAuthController@error')->name('oauth.error')->middleware(['auth']);
Route::any('/oauth/success', 'OAuthController@success')->name('oauth.success')->middleware(['auth']);
Route::get('/oauth/refresh', 'OAuthController@refresh')->name('oauth.refresh')->middleware(['auth']);
Route::any('/oauth/auth', 'OAuthController@callback');
//Users
Route::get('/users/list', 'User\ListUsersController@list')->name('oauth.scim.users');
Route::get('/users/{userId}/{mode}', 'User\ShowUserController@show')->name('oauth.scim.user.show');
Route::get('/users/create', function() { return view('oauth/user/create', ['roles' => ['user', 'admin', 'manager']]); })->name('user.create')->middleware(['auth']);
Route::post('/users/create', 'User\CreateUserController@create')->name('oauth.scim.user.create');
Route::post('/users/update', 'User\UpdateUserController@update')->name('oauth.scim.user.update');
//Groups
Route::get('/groups/list', 'Group\ListGroupsController@list')->name('oauth.scim.groups');
//Landing Page
Route::get('/home', 'HomeController@index')->name('home');
