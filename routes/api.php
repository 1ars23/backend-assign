<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->except(['destroy']);
    Route::post('users/delete', [UserController::class, 'destroy']); // Custom delete route
    Route::apiResource('projects', ProjectController::class)->except(['destroy']);
    Route::post('projects/delete', [ProjectController::class, 'destroy']); // Custom delete route
    Route::post('/projects/assign-user', [ProjectController::class, 'assignUser']);
    Route::apiResource('timesheets', TimesheetController::class)->except(['destroy']);
    Route::post('timesheets/delete', [TimesheetController::class, 'destroy']); // Custom delete route

});
