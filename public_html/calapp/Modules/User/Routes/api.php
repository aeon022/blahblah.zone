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

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::get('verify/user/{code}/{email?}', 'AuthController@getVerifyUser');
Route::get('logout', 'AuthController@logout');
Route::post('forgot-password', 'PasswordResetController@forgotPassword');
Route::post('verify/token', 'PasswordResetController@verifyToken');
Route::post('reset-password', 'PasswordResetController@resetPassword');


/*
 * Users routes
 */
Route::group(
    [
    'middleware' => 'auth:api'
    ],
    function () {
        Route::get('get-users', 'UserController@getUserIdName');
        Route::post('all-users', 'UserController@getAllUsers');
        Route::get('users/permissions', 'UserController@getUserPermissions');
        Route::get('users/last-id', 'UserController@getUserLastId');
        Route::get('mail-users', 'UserController@getMailUsers');
        Route::put(
            'users/status-change/{id}',
            'UserController@setActiveDeactiveUser'
        );
        Route::post('users/change-password/{id}', 'UserController@changePassword');
        Route::post('users/change-email/{id}', 'UserController@changeEmail');
        Route::get(
            'users/invite/{id}',
            'UserController@sendInviteUserMail'
        );
        Route::post(
            'users/client/dashboard',
            'UserController@getClientForDashboard'
        );
        Route::post('users/import', 'UserController@import');
        Route::resource('users', 'UserController');
    }
);

/*
 * Roles routes
 */
Route::group(
    [
    'middleware' => 'auth:api'
    ],
    function () {
        Route::post('all-roles', 'RoleController@getAllRoles');
        Route::resource('roles', 'RoleController');
    }
);

/*
 * Departments routes
 */
Route::group(
    [
    'middleware' => 'auth:api'
    ],
    function () {
        Route::get(
            'departments/{departmentId}/{roleId}',
            'DepartmentController@getDepartmentDetail'
        );
        Route::put(
            'departments/{departmentId}/{roleId}',
            'DepartmentController@putDepartmentDetail'
        );
        Route::delete(
            'departments/{departmentId}/{roleId}',
            'DepartmentController@deleteDepartmentRole'
        );
        Route::get(
            'departments/clients-roles',
            'DepartmentController@getDepartmentsClientsRoles'
        );
        Route::resource('departments', 'DepartmentController');
    }
);
