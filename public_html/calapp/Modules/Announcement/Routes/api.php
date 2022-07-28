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
        Route::post(
            'all-announcements',
            'AnnouncementController@getAllAnnouncements'
        );
        Route::post(
            'announcements/dashboard',
            'AnnouncementController@getAnnouncementsForDashboard'
        );
        Route::resource('announcements', 'AnnouncementController');
    }
);