<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReportController;
use App\Models\Course;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)
->middleware('guest')
    ->group(function () {
        Route::get('/login', 'login')
        ->name('login');
        Route::post('/login', 'authenticate')
        ->name('authenticate');
        Route::get('/register', 'register')
        ->name('register');
        Route::post('/register', 'store')
        ->name('store.user');
        Route::get('/logout', 'logout')
        ->name('logout')
        ->withoutMiddleware('guest')
        ->middleware(['auth', 'EnsureKeepExam']);
    });
    
Route::middleware(['auth', 'EnsureKeepExam'])
    ->group(function() {
        
        Route::get('/', function () {
            return view('pages/dashboard', ['courses' => Course::all()]);
        })->name('home');

        Route::singleton('profile', ProfileController::class);

        // Route::resource('courses', CourseController::class);
        Route::prefix('courses')
            ->name('courses.')
            ->controller(CourseController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index'); // Page
                Route::get('/all', 'all')->name('all');
                Route::post('/store', 'store')->name('store');
                Route::get('/create', 'create')->name('create');
                Route::get('/{course}/show', 'show')->name('show'); // Page
                Route::get('/{course}/detail', 'detail')->name('detail');
                Route::put('/{course}/update', 'update')->name('update');
                Route::delete('/{course}/delete', 'delete')->name('delete');
            });
        
        Route::resource('courses.materials', MaterialController::class)
            ->except(['index', 'show']);

        Route::post('/courses/{course}/exams/{exam}',[ExamController::class, 'submit'])
            ->name('courses.exams.submit')
            ->middleware('EnsurePreExam')
            ->withoutMiddleware('EnsureKeepExam');
        Route::get('/courses/{course}/exams/{exam}/finish',[ExamController::class, 'finish'])
            ->name('courses.exams.finish')
            ->middleware('EnsurePreExam')
            ->withoutMiddleware('EnsureKeepExam');
        Route::resource('courses.exams', ExamController::class)
            ->except(['index'])
            ->middlewareFor('show', 'EnsurePreExam')
            ->withoutMiddlewareFor('show', 'EnsureKeepExam');

        Route::resource('courses.exams.questions', QuestionController::class)
            ->except(['index', 'create', 'show', 'edit']);

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');
        Route::delete('/reports', [ReportController::class, 'reset'])
            ->name('reports.reset');
    });

// user resource untuk admin
