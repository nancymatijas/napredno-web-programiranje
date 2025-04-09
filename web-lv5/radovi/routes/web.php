<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\NastavnikController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [LoginController::class, 'redirectTo']);

Route::middleware(['auth'])->group(function () {
    Route::get('/student', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/student/apply/{task}', [StudentController::class, 'apply'])->name('student.apply');
    Route::delete('/student/unapply/{task}', [StudentController::class, 'unapply'])->name('student.unapply');
});

Route::prefix('{locale}')->middleware(['auth'])->group(function () {
    Route::prefix('nastavnik')->group(function () {
        Route::get('/', [NastavnikController::class, 'index'])->name('nastavnik.dashboard');
        Route::post('/store-task', [NastavnikController::class, 'store'])->name('nastavnik.store-task');
        Route::post('/tasks/{task}/accept/{student}', [NastavnikController::class, 'acceptStudent'])
         ->name('nastavnik.accept');
    });
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::patch('/users/{user}/update-role', [AdminController::class, 'updateRole'])
        ->name('admin.users.update-role');
});
