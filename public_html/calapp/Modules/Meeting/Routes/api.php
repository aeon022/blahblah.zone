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
    ], function () {
        Route::post('meeting/dashboard', 'MeetingController@getDashboardMeeting');
        Route::post('all-meetings', 'MeetingController@getAllMeetings');
        Route::resource('meetings', 'MeetingController');
    }
);
