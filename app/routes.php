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

/**
 * Service routes
 */

// GET  /services
Route::get('services', array(
    'as'     => 'services.index',
    'before' => 'auth',
    'uses'   => 'ServiceController@index'
));

// GET  /services
Route::get('services/register', array(
    'as'     => 'services.register',
    'before' => 'auth',
    'uses'   => 'ServiceController@register'
));

Route::post('services/save', array(
    'as'   => 'services.save',
    'uses' => 'ServiceController@save'
));

Route::get('services/list', array(
    'as'    => 'services.list',
    'uses'  => 'ServiceController@getList'
    ));
# GET   /services/1
Route::get('services/{id}', array(
    'before' => 'auth',
    'uses'   => 'ServiceController@entries'
));

Route::group(['prefix' => 'services'], function() {
    Route::get('search', array('uses' => 'ServiceController@searchVisit'));
    Route::post('screening/save', array('uses' => 'ServiceController@saveScreening'));

    Route::post('diagnosis/save', array('uses' => 'ServiceController@saveDiagnosis'));
    Route::post('diagnosis/remove', array('uses' => 'ServiceController@removeDiagnosis'));

    Route::post('procedure/save', array('uses' => 'ServiceController@saveProcedure'));
    Route::post('procedure/list', array('uses' => 'ServiceController@getProcedureList'));
    Route::post('procedure/remove', array('uses' => 'ServiceController@removeProcedure'));
    Route::post('procedure/dental/save', array('uses' => 'ServiceController@saveProcedureDental'));
    Route::post('procedure/dental/list', array('uses' => 'ServiceController@getProcedureDentalList'));
    Route::post('procedure/dental/remove', array('uses' => 'ServiceController@removeProcedureDental'));

    Route::post('income/save', array('uses'             => 'ServiceController@saveIncome'));
    Route::post('income/list', array('uses'             => 'ServiceController@getIncomeList'));
    Route::post('income/remove', array('uses'           => 'ServiceController@removeIncome'));
    Route::post('drug/save', array('uses'               => 'ServiceController@saveDrug'));
    Route::post('drug/list', array('uses'               => 'ServiceController@getDrugList'));
    Route::post('drug/remove', array('uses'             => 'ServiceController@removeDrug'));
    Route::post('drug/clear', array('uses'              => 'ServiceController@clearDrug'));
    Route::post('appoint/save', array('uses'            => 'ServiceController@saveAppoint'));
    Route::post('appoint/list', array('uses'            => 'ServiceController@getAppointList'));
    Route::post('appoint/remove', array('uses'          => 'ServiceController@removeAppoint'));
    Route::post('referout/save', array('uses'           => 'ServiceController@saveReferOut'));
    Route::post('referout/remove', array('uses'         => 'ServiceController@removeReferOut'));
    Route::post('accident/save', array('uses'           => 'ServiceController@saveAccident'));
    Route::post('accident/remove', array('uses'         => 'ServiceController@removeAccident'));
});

# GET, POST   /api/*
Route::group(array('prefix' => 'api'), function()
{
    Route::get('search/hospital', array('uses' => 'ApiController@searchHospital'));
    Route::get('search/person', array('uses' => 'PersonController@searchPerson'));
    Route::get('search/doctorroom', array('uses' => 'ApiController@searchDoctorRoom'));
    Route::get('search/diagnosis', array('uses' => 'ApiController@searchDiagnosis'));
    Route::get('search/income', array('uses' => 'ApiController@searchIncome'));
    Route::get('search/procedure', array('uses' => 'ApiController@searchProcedure'));
    Route::get('search/procedure/position', array('uses' => 'ApiController@getProcedurePosition'));
    Route::get('search/drug', array('uses' => 'ApiController@searchDrug'));
    Route::get('search/drug/usage', array('uses' => 'ApiController@searchDrugUsage'));
});

# GET   /pages/*
Route::group(array('prefix' => 'pages'), function () {
    Route::post('services/screening', array('uses' => 'PageController@serviceScreening'));
    Route::post('services/diagnosis', array('uses' => 'PageController@serviceDiagnosis'));
    Route::post('services/procedure', array('uses' => 'PageController@serviceProcedure'));
    Route::post('services/income', array('uses' => 'PageController@serviceIncome'));
    Route::post('services/drug', array('uses' => 'PageController@serviceDrug'));
    Route::post('services/appointment', array('uses' => 'PageController@serviceAppointment'));
    Route::post('services/referout', array('uses' => 'PageController@serviceReferOut'));
    Route::post('services/accident', array('uses' => 'PageController@serviceAccident'));
    Route::post('preg/register', array('uses' => 'PageController@pregnancyRegister'));
    Route::post('preg/list', array('uses' => 'PageController@pregnancyList'));

    Route::post('preg/detail', array('uses' => 'PageController@pregnancyDetail'));
});


// Pregnancies
Route::group(['prefix' => 'pregnancies'], function() {
    Route::post('register', ['uses' => 'PregnancyController@doRegister']);
    Route::post('list', ['uses' => 'PregnancyController@getList']);
    Route::get('detail/{id}', ['uses' => 'PregnancyController@getDetail']);
    Route::post('save', ['uses' => 'PregnancyController@saveDetail']);
});

/*
|---------------------------------------------------------------------
| Services sheet
|---------------------------------------------------------------------
*/

Route::get('accounts/1', [
	'before' => 'auth',
	'as'     => 'accounts.one',
	'uses'   => 'VillageController@index'
]);

Route::get('accounts/2', [
	'before' => 'auth',
	'as'     => 'accounts.two',
	'uses'   => 'PregnancyController@index'
]);

Route::get('villages/house',    ['uses' => 'VillageController@getHome']);
Route::get('house/person',      ['uses' => 'VillageController@getPerson']);
Route::get('people/edit/{pid}', ['uses' => 'PersonController@showEdit']);


App::missing(function () {
    return Response::view('errors.404', [], 404);
});
