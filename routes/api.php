<?php

use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TasksController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 * projects routes
 */

Route::group(["prefix" => "projects"], function () {
    Route::get("/", [ProjectsController::class, "index"]);
    Route::post("/", [ProjectsController::class, "store"]);
    Route::delete("/{project_id}", [ProjectsController::class, "delete"]);
    Route::post("/{project_id}", [ProjectsController::class, "update"]);

});


/**
 * tasks routes
 */

Route::group(["prefix" => "tasks"], function () {
    Route::get("/{project_id}", [TasksController::class, "index"]);
    Route::get("/{task_id}/done", [TasksController::class, "done"]);
    Route::get("/{task_id}/undone", [TasksController::class, "undone"]);
    Route::post("/{task_id}/change-priority", [TasksController::class, "changerPriority"]);
    Route::post("/", [TasksController::class, "store"]);
    Route::delete("/{task_id}", [TasksController::class, "delete"]);
    Route::post("/{task_id}", [TasksController::class, "update"]);

});




