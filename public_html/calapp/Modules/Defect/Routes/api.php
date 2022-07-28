<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    ['middleware' => 'auth:api'],
    function () {
        Route::post('all-defects', 'DefectController@getAllDefects');
        Route::post(
            'defect/{id}/change-status',
            'DefectController@changeDefectStatus'
        );
        Route::post(
            'defect/{id}/change-severity',
            'DefectController@changeDefectSeverity'
        );
        Route::get('defect/last-id', 'DefectController@getLastId');
        Route::put('defect/notes/{id}', 'DefectController@defectNotesUpdate');
        Route::post('defect/defect-report', 'DefectController@getDefectForReport');
        Route::post('defect/dashboard', 'DefectController@getDefectForDashboard');
        Route::get('defect/{id}/permission/{type}', 'DefectController@getDefectPermission');
        Route::resource('defect', 'DefectController');
    }
);
