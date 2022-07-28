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
        Route::post('todos/dashboard', 'ToDoController@getTodoForDashboard');
        Route::post('all-todos', 'ToDoController@getAllTodos');
        Route::resource('todos', 'ToDoController');
    }
);
