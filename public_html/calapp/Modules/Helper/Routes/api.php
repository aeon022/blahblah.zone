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
    [
    'middleware' => 'auth:api'
    ],
    function () {
        Route::get('helper/countries', 'HelperController@getCountries');
        Route::get('helper/locals', 'HelperController@getLocales');
        Route::get('helper/languages', 'HelperController@getLanguages');
        Route::get('helper/currencies', 'HelperController@getCurrencies');
        Route::get('helper/days', 'HelperController@getDays');
        Route::get('helper/get_user_by_email', 'HelperController@getUserByEmail');
        // --
        // For PM
        Route::get(
            'helper/pm/dashboard/count',
            'PmHelperController@getCountForDashboard'
        );
        Route::get(
            'helper/pm/timecard/{user_id}/{date}',
            'PmHelperController@getTimeCardReports'
        );
        Route::get(
            'helper/pm/userwisecount/{user_id}',
            'PmHelperController@getUserWiseCountForUserDetail'
        );
    }
);

Route::get('helper/timezone', 'HelperController@getTimezones');

// --
// Cronjob
Route::get('cronjob', 'HelperController@cronjob');
