<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PageSectionController as AdminPageSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/comingsoon', function () {
    return view('coming-soon');
})->name('coming-soon');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.pages.index'));

    Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::patch('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');

    Route::get('/pages/{page}/sections/create', [AdminPageSectionController::class, 'create'])->name('sections.create');
    Route::post('/pages/{page}/sections', [AdminPageSectionController::class, 'store'])->name('sections.store');
    Route::get('/pages/{page}/sections/{section}/edit', [AdminPageSectionController::class, 'edit'])->name('sections.edit');
    Route::patch('/pages/{page}/sections/{section}', [AdminPageSectionController::class, 'update'])->name('sections.update');
    Route::delete('/pages/{page}/sections/{section}', [AdminPageSectionController::class, 'destroy'])->name('sections.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
