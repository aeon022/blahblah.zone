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
        Route::post(
            'projects/{id}/change-status',
            'ProjectsController@changeProjectStatus'
        );
        Route::get('projects-list', 'ProjectsController@getProjectsForList');
        Route::get(
            'projects/projectmembers',
            'ProjectsController@getProjectMembers'
        );
        Route::post('all-projects', 'ProjectsController@getAllProjects');
        Route::post('projects/import', 'ProjectsController@importProject');
        Route::put('projects/notes/{id}', 'ProjectsController@projectNotesUpdate');
        Route::get(
            'projects/projectsprinttasks',
            'ProjectsController@getProjectSprintTasks'
        );
        Route::get(
            'projects/release-planner',
            'ProjectsController@getProjectTaskDefect'
        );
        Route::post(
            'projects/project-report',
            'ProjectsController@getProjectForReport'
        );
        Route::post(
            'projects/dashboard',
            'ProjectsController@getProjectForDashboard'
        );
        Route::post(
            'projects/task-count-by-status',
            'ProjectsController@getProjectTaskCountByStatus'
        );
        Route::get('projects/count', 'ProjectsController@getAllProjectCount');
        Route::get('projects/{id}/permission', 'ProjectsController@getProjectPermission');
        Route::resource('projects', 'ProjectsController');
    }
);
