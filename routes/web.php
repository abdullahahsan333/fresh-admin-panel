<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\AssetController as AdminAssetController;

Route::get('/', [UserController::class, 'index']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/store', [UserController::class, 'store'])->name('store');

Route::get('/dashboard', [UserController::class, 'dashboard'])->middleware('user')->name('dashboard');
Route::get('/logout', [UserController::class, 'logout'])->middleware('user')->name('logout');

// all admin routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::get('/register', [AdminController::class, 'registerForm'])->name('admin.register');
    Route::post('/store', [AdminController::class, 'store'])->name('admin.store');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('admin')->name('admin.dashboard');
    Route::get('/logout', [AdminController::class, 'logout'])->middleware('admin')->name('admin.logout');

    Route::get('/projects/create', [AdminProjectController::class, 'create'])->middleware('admin')->name('admin.projects.create');
    Route::post('/projects', [AdminProjectController::class, 'store'])->middleware('admin')->name('admin.projects.store');
    
    Route::get('/assets', [AdminAssetController::class, 'index'])->middleware('admin')->name('admin.assets.index');
    Route::post('/assets', [AdminAssetController::class, 'store'])->middleware('admin')->name('admin.assets.store');
    Route::post('/assets/server', [AdminAssetController::class, 'server_store'])->middleware('admin')->name('admin.assets.server.store');
});
