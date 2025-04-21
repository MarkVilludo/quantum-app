<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\YearController;

use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\SubjectModuleController;
use App\Http\Controllers\Api\ModuleLessonController;
use App\Http\Controllers\Api\LessonQuizResultController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/years', [YearController::class, 'index']);
Route::get('/subjects/{year}', [SubjectController::class, 'index']);
Route::get('/module-lessons/{moduleId}', [ModuleLessonController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/subject-modules/{subjectId}', [SubjectModuleController::class, 'show']);

    Route::post('/lesson-quizzes/submit', [LessonQuizResultController::class, 'store']);
    Route::get('/lesson-quizzes/result/{lessonId}', [LessonQuizResultController::class, 'show']);
});
