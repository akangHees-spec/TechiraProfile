<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/admin/login', '/login');
Route::redirect('/admin', '/dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Panel Routes
    Route::get('/admin/categories', function () { return view('admin.categories'); })->name('admin.categories');
    Route::get('/admin/products', function () { return view('admin.products'); })->name('admin.products');
    Route::get('/admin/services', function () { return view('admin.services'); })->name('admin.services');
    Route::get('/admin/sliders', function () { return view('admin.sliders'); })->name('admin.sliders');
    Route::get('/admin/sections', function () { return view('admin.sections'); })->name('admin.sections');
    Route::get('/admin/testimonials', function () { return view('admin.testimonials'); })->name('admin.testimonials');
    Route::get('/admin/team', function () { return view('admin.team'); })->name('admin.team');
    Route::get('/admin/partners', function () { return view('admin.partners'); })->name('admin.partners');
    Route::get('/admin/faqs', function () { return view('admin.faqs'); })->name('admin.faqs');
    Route::get('/admin/messages', function () { return view('admin.messages'); })->name('admin.messages');
    Route::get('/admin/settings', function () { return view('admin.settings'); })->name('admin.settings');
});

require __DIR__.'/auth.php';
