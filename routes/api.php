<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ToDoController;

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

// Profiles
Route::get('get-profiles', [ProfileController::class, 'getProfiles']);
Route::post('store-profile', [ProfileController::class, 'storeProfile']);
Route::post('update-profile', [ProfileController::class, 'updateProfile']);
Route::get('delete-profile', [ProfileController::class, 'deleteProfile']);
Route::get('make-default-profile', [ProfileController::class, 'makeDefault']);

// ToDos
Route::get('get-to-do-list', [ToDoController::class, 'getToDoList']);
Route::post('store-to-do', [ToDoController::class, 'storeToDo']);
Route::post('update-to-do', [ToDoController::class, 'updateToDo']);
Route::get('delete-to-do', [ToDoController::class, 'deleteToDo']);
Route::get('done-to-do', [ToDoController::class, 'finishToDo']);
