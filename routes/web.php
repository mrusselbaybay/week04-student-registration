<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(StudentController::class)->prefix('students')->name('students.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/register', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{student}', 'show')->name('show');
});
