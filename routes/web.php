<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PriorityController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('api/tasks', TaskController::class);
    Route::resource('api/tags', TagController::class);
    Route::get('api/priorities', [PriorityController::class, 'index']);
    Route::get('api/getArchives', [TaskController::class, 'getArchives']);
    Route::put('api/tasks/{task}/complete', [TaskController::class, 'markComplete']);
    Route::put('api/tasks/{task}/incomplete', [TaskController::class, 'markInComplete']);
    Route::put('api/tasks/{task}/archive', [TaskController::class, 'archive']);
    Route::put('api/tasks/{task}/restore', [TaskController::class, 'restore']);


});

// Route::get('/{any}', function () {
//     return view('app');
//     })->where('any', '.*');
