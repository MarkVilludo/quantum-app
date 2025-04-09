<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\{ActivityController, ActivityQuestionController};
use App\Http\Controllers\{QuizController};
use App\Http\Controllers\CMS\{
    SubjectController,
    SubjectModuleController,
    LessonQuizController,
    LessonQuizResultController,
    AdminController,
    StudentController
};

use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

    Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::get('/lessons/{id}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::put('/lessons/{id}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])->name('lessons.destroy');


    // subject modules
    Route::resource('subject-modules', SubjectModuleController::class)->except(['show']);

    Route::get('/lessons/{lesson}/quizzes', [LessonQuizController::class, 'index'])->name('lesson-quizzes.index');
    Route::get('/modules/{subject_id}', [LessonController::class, 'getModules'])->name('modules.get');
    Route::get('/subjects/{yearId}', [LessonController::class, 'getSubjects'])->name('subjects.get');


    // Lesson Quizzes Routes
    Route::prefix('lessons/{lesson}')->group(function () {
        Route::get('/quizzes', [LessonQuizController::class, 'index'])->name('lesson-quizzes.index');
        Route::get('/quizzes/create', [LessonQuizController::class, 'create'])->name('lesson-quizzes.create');
    });

    Route::post('lessons/quizzes', [LessonQuizController::class, 'store'])->name('lesson-quizzes.store');
    Route::get('lessons/quizzes/{id}', [LessonQuizController::class, 'edit'])->name('lesson-quizzes.edit');
    Route::post('lessons/quizzes/{id}', [LessonQuizController::class, 'update'])->name('lesson-quizzes.update');
    Route::delete('lessons/quizzes/{id}', [LessonQuizController::class, 'destroy'])->name('lesson-quizzes.destroy');
    Route::get('lesson/{lesson}/quizzes', [LessonQuizController::class, 'index'])->name('lesson-quizzes.index');
});


// // Activity routes
Route::prefix('lessons/{lesson}')->group(function () {
    Route::get('quizzes', [QuizController::class, 'index'])->name('lesson.quizzes.index');
    Route::get('activities', [ActivityController::class, 'index'])->name('lesson.activities.index');
});

require __DIR__ . '/auth.php';


// Activity Questions Routes
Route::post('/activities/{activity}/questions', [ActivityQuestionController::class, 'store'])
    ->name('activities.questions.store');

Route::get('/quiz-results', [LessonQuizResultController::class, 'index'])->name('quiz-results.index');


Route::middleware(['auth'])->group(function () {
    //admin user management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');


    //student quiz results
    Route::get('/student-quiz-results', [StudentController::class, 'index'])->name('student-quiz-results.index');
});
