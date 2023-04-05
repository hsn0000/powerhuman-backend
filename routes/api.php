<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::namespace('\App\Http\Controllers\API')->group(function () {
    // Authentication API
    Route::prefix('/')->name('authentication')->group(function () {
        Route::post('/login', 'UserController@login')->name('.login');
        Route::post('/register', 'UserController@register')->name('.register');
        Route::post('/logout', 'UserController@logout')->middleware('auth:sanctum')->name('.logout');
    });

    // User API
    Route::prefix('/')->name('user')->group(function () {
        Route::get('/user', 'UserController@fetch')->middleware('auth:sanctum')->name('.fetch');
    });

    // Company Api
    Route::prefix('/')->name('company')->group(function () {
        Route::get('/company', 'CompanyController@fetch')->middleware('auth:sanctum')->name('.fetch');
        Route::post('/company', 'CompanyController@create')->middleware('auth:sanctum')->name('.create');
        Route::post('/company/update/{id}', 'CompanyController@update')->middleware('auth:sanctum')->name('.update');
    });

    // Team Api
    Route::prefix('/')->name('team')->group(function () {
        Route::get('/team', 'TeamController@fetch')->middleware('auth:sanctum')->name('.fetch');
        Route::post('/team', 'TeamController@create')->middleware('auth:sanctum')->name('.create');
        Route::post('/team/update/{id}', 'TeamController@update')->middleware('auth:sanctum')->name('.update');
        Route::delete('/team/{id}', 'TeamController@destroy')->middleware('auth:sanctum')->name('.delete');
    });
    
    // Role Api
    Route::prefix('/')->name('role')->group(function () {
        Route::get('/role', 'RoleController@fetch')->middleware('auth:sanctum')->name('.fetch');
        Route::post('/role', 'RoleController@create')->middleware('auth:sanctum')->name('.create');
        Route::post('/role/update/{id}', 'RoleController@update')->middleware('auth:sanctum')->name('.update');
        Route::delete('/role/{id}', 'RoleController@destroy')->middleware('auth:sanctum')->name('.delete');
    });

    // Responsibility Api
    Route::prefix('/')->name('responsibility')->group(function () {
        Route::get('/responsibility', 'ResponsibilityController@fetch')->middleware('auth:sanctum')->name('.fetch');
        Route::post('/responsibility', 'ResponsibilityController@create')->middleware('auth:sanctum')->name('.create');
        Route::delete('/responsibility/{id}', 'ResponsibilityController@destroy')->middleware('auth:sanctum')->name('.delete');
    });

    // Employee Api
    Route::prefix('/')->name('employee')->group(function () {
        Route::get('/employee', 'EmployeeController@fetch')->middleware('auth:sanctum')->name('.fetch');
        Route::post('/employee', 'EmployeeController@create')->middleware('auth:sanctum')->name('.create');
        Route::post('/employee/update/{id}', 'EmployeeController@update')->middleware('auth:sanctum')->name('.update');
        Route::delete('/employee/{id}', 'EmployeeController@destroy')->middleware('auth:sanctum')->name('.delete');
    });

});


// Route::get('/company', ['API\CompanyController@all']);
