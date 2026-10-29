<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\YearController;

use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\SubjectModuleController;
use App\Http\Controllers\Api\ModuleLessonController;
use App\Http\Controllers\Api\LessonQuizController;
use App\Http\Controllers\Api\LessonQuizResultController;
use App\Http\Controllers\Api\ModuleProgressController;
use App\Http\Controllers\Api\LessonActivityController;
use App\Http\Controllers\Api\SubjectProgressController;
use App\Http\Controllers\Api\SubjectFinalQuizController;
use App\Http\Controllers\Api\ModuleQuizController;
use App\Http\Controllers\Api\StudentProfileController;
use App\Http\Controllers\Api\GraduationController;

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
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/years', [YearController::class, 'index']);
Route::get('/module-lessons/{moduleId}', [ModuleLessonController::class, 'show']);
Route::get('/lesson-quizzes/{lessonId}', [LessonQuizController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/years/galaxy', [YearController::class, 'galaxy']);
    Route::get('/subjects/{year}', [SubjectController::class, 'index']);
    Route::get('/subject-modules/{subjectId}', [SubjectModuleController::class, 'show']);
    Route::get('/subject-progress/{subjectId}', [SubjectProgressController::class, 'show']);
    Route::get('/module-progress/{moduleId}', [ModuleProgressController::class, 'show']);
    Route::get('/module-quiz/{moduleId}', [ModuleQuizController::class, 'show']);
    Route::post('/module-quiz/{moduleId}/submit', [ModuleQuizController::class, 'submit']);
    Route::post('/lesson-activities/micro-check', [LessonActivityController::class, 'submitMicroCheck']);
    Route::post('/lesson-activities/novice', [LessonActivityController::class, 'submitNovice']);
    Route::post('/lesson-activities/quiz', [LessonActivityController::class, 'submitQuizMode']);
    Route::get('/subject-final-quiz/{subjectId}', [SubjectFinalQuizController::class, 'show']);
    Route::post('/subject-final-quiz/{subjectId}/submit', [SubjectFinalQuizController::class, 'submit']);

    Route::post('/lesson-quizzes/submit', [LessonQuizResultController::class, 'store']);
    Route::get('/lesson-quizzes/result/{lessonId}', [LessonQuizResultController::class, 'show']);
    Route::get('/student-profile', [StudentProfileController::class, 'show']);
    Route::get('/graduation/status', [GraduationController::class, 'status']);
    Route::get('/graduation/certificate', [GraduationController::class, 'certificate']);
});
