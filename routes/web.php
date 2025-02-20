<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScrapingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::get('/create', [HomeController::class, 'create'])->name('create');
    Route::post('/store', [HomeController::class, 'store'])->name('store');
    Route::post('/delete/{id}', [HomeController::class, 'delete'])->name('delete');
    Route::get('/dev', [HomeController::class, 'dev_home'])->name('dev.home');
    Route::get('/dev/create', [HomeController::class, 'dev_show_create'])->name('dev.create');
    Route::post('/dev/create', [HomeController::class, 'dev_save']);
    Route::post('/dev/edit/show', [HomeController::class, 'dev_show_edit'])->name('dev.edit.show');
    Route::post('/dev/edit/save', [HomeController::class, 'dev_save'])->name('dev.edit.save');
    Route::post('/dev/edit/delete', [HomeController::class, 'dev_delete'])->name('dev.edit.delete');
    Route::post('/dev/scraping/show', [ScrapingController::class, 'scrape_to_show'])->name('dev.scraping.show');
    Route::post('/dev/scraping', [ScrapingController::class, 'scrape'])->name('dev.scraping');
});

require __DIR__.'/auth.php';
