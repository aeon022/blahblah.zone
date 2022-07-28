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
        Route::post('taskboard', 'TaskController@getTaskForTaskBoard');
        Route::post('tasks/status-list', 'TaskController@updateStatusList');
        Route::get('tasks/last-id', 'TaskController@getTaskLastId');
        Route::post('all-tasks', 'TaskController@getAllTask');
        Route::post('tasks/{taskId}/change-status', 'TaskController@changeStatus');
        Route::get(
            'tasks/{parent_task_id}/parent-task',
            'TaskController@getParentTask'
        );
        Route::post('tasks/import', 'TaskController@importTask');
        Route::put('tasks/notes/{id}', 'TaskController@taskNotesUpdate');
        Route::get(
            'tasks/{parent_task_id}/subtask-count-by-parent',
            'TaskController@getSubTaskCountByParent'
        );
        Route::post('tasks/task-report', 'TaskController@getTaskForReport');
        Route::post('tasks/dashboard', 'TaskController@getTaskForDashboard');
        Route::get('tasks/count-by-status', 'TaskController@getTaskCountByStatus');
        Route::get('tasks/{id}/permission/{type}', 'TaskController@getTaskPermission');
        Route::resource('tasks', 'TaskController');
    }
);
