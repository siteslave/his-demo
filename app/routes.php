<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', ['before' => 'auth', 'uses' => 'HomeController@showWelcome']);

//Route::controller('users', 'UserController');
Route::get('login',  ['as'  => 'users.login', 'uses'   => 'UserController@login']);
Route::post('login', ['as'  => 'users.dologin', 'uses' => 'UserController@checkLogin']);
Route::get('logout', ['as'  => 'users.logout', 'uses'  => 'UserController@logout']);

Route::controller('services', 'ServicesController');

/**
 * url    /apis
 */

Route::controller('apis', 'ApisController');

/**
 * URL    /pages/*
 */

Route::controller('pages', 'PagesController');

/**
 * URL    /pregnancies/*
 */

Route::controller('pregnancies', 'PregnanciesController');

Route::controller('person', 'PersonController');
/*
|---------------------------------------------------------------------
| Services sheet
|---------------------------------------------------------------------
*/

// Route::get('accounts/1', [
// 	'before' => 'auth',
// 	'as'     => 'accounts.one',
// 	'uses'   => 'VillageController@index'
// ]);
//
// Route::get('accounts/2', [
// 	'before' => 'auth',
// 	'as'     => 'accounts.two',
// 	'uses'   => 'PregnanciesController@getIndex'
// ]);
Route::controller('villages', 'VillagesController');
//Route::get('villages/house',    ['uses' => 'VillageController@getHome']);
Route::get('house/person',      ['uses' => 'VillageController@getPerson']);
#Route::get('people/edit/{pid}', ['uses' => 'PersonController@showEdit']);

App::missing(function () {
    if (Request::ajax()) {
        return Response::json(['ok' => 0, 'error' => 'Page not found']);
    } else {
        return Response::view('errors.404', [], 404);
    }
});
