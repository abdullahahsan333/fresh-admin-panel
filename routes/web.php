<?php

use Illuminate\Support\Facades\Route;

// All local Controller
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

// All Admin Panel Controller
use App\Http\Controllers\Admin\PanelController as AdminPanelController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\AssetController as AdminAssetController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

// All User Panel Controller
use App\Http\Controllers\User\PanelController as UserPanelController;
use App\Http\Controllers\User\AssetController as UserAssetController;

// All User Panel Routes
Route::get('/', [UserController::class, 'index']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/store', [UserController::class, 'store'])->name('store');

// user panel routes
Route::prefix('user')->group(function () {
    Route::get('/dashboard', [UserPanelController::class, 'dashboard'])->middleware('user')->name('user.dashboard');
    Route::get('/logout', [UserPanelController::class, 'logout'])->middleware('user')->name('user.logout');
    Route::get('/profile', [UserPanelController::class, 'profile'])->middleware('user')->name('user.profile');
    Route::post('/profile', [UserPanelController::class, 'profileUpdate'])->middleware('user')->name('user.profile.update');
    
    Route::get('/assets', [UserAssetController::class, 'index'])->middleware('user')->name('user.assets.index');
    Route::post('/assets', [UserAssetController::class, 'store'])->middleware('user')->name('user.assets.store');
    Route::get('/assets/server-details', [UserAssetController::class, 'server_details'])->middleware('user')->name('user.assets.server.details');
    Route::post('/assets/server', [UserAssetController::class, 'server_store'])->middleware('user')->name('user.assets.server.store');
    Route::post('/server_store', [UserAssetController::class, 'server_store'])->middleware('user')->name('user.server_store');
    Route::get('/assets/overview', [UserAssetController::class, 'overview'])->middleware('user')->name('user.assets.overview');
    
    Route::get('/server/{id}/linux', [UserAssetController::class, 'linux'])->middleware('user')->name('user.server.linux');
    Route::get('/server/{id}/mysql', [UserAssetController::class, 'mysql'])->middleware('user')->name('user.server.mysql');
    Route::get('/server/{id}/mongodb', [UserAssetController::class, 'mongodb'])->middleware('user')->name('user.server.mongodb');
    Route::get('/server/{id}/redis', [UserAssetController::class, 'redis'])->middleware('user')->name('user.server.redis');
    Route::get('/server/{id}/api_log', [UserAssetController::class, 'api_log'])->middleware('user')->name('user.server.api_log');
    Route::get('/server/{id}/scheduler', [UserAssetController::class, 'scheduler'])->middleware('user')->name('user.server.scheduler');
    Route::get('/server/{id}/ssl', [UserAssetController::class, 'ssl'])->middleware('user')->name('user.server.ssl');
    Route::post('/import', [UserAssetController::class, 'import'])->middleware('user')->name('user.import');
});

// all admin routes
Route::redirect('/admin', '/admin/login');
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::get('/register', [AdminController::class, 'registerForm'])->name('admin.register');
    Route::post('/store', [AdminController::class, 'store'])->name('admin.store');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('admin')->name('admin.dashboard');
    Route::get('/logout', [AdminController::class, 'logout'])->middleware('admin')->name('admin.logout');
    
    Route::get('/notifications', [AdminPanelController::class, 'notifications'])->middleware('admin')->name('admin.notifications');
    
    Route::get('/profile', [AdminPanelController::class, 'profile'])->middleware('admin')->name('admin.profile');
    Route::post('/profile', [AdminPanelController::class, 'profileUpdate'])->middleware('admin')->name('admin.profile.update');
    Route::get('/settings', [AdminPanelController::class, 'settings'])->middleware('admin')->name('admin.settings');
    Route::post('/settings', [AdminPanelController::class, 'settingsUpdate'])->middleware('admin')->name('admin.settings.update');

    Route::get('/projects/create', [AdminProjectController::class, 'create'])->middleware('admin')->name('admin.projects.create');
    Route::post('/projects', [AdminProjectController::class, 'store'])->middleware('admin')->name('admin.projects.store');
    
    Route::get('/assets', [AdminAssetController::class, 'index'])->middleware('admin')->name('admin.assets.index');
    Route::post('/assets', [AdminAssetController::class, 'store'])->middleware('admin')->name('admin.assets.store');
    Route::post('/assets/server', [AdminAssetController::class, 'server_store'])->middleware('admin')->name('admin.assets.server.store');
    Route::get('/assets/server-details', [AdminAssetController::class, 'server_details'])->middleware('admin')->name('admin.assets.server.details');
    Route::get('/assets/archive', [AdminAssetController::class, 'archive'])->middleware('admin')->name('admin.assets.archive');
    
    Route::get('/overview', [AdminAssetController::class, 'overview'])->middleware('admin')->name('admin.overview');
    Route::get('/server/{id}/linux', [AdminAssetController::class, 'linux'])->middleware('admin')->name('admin.server.linux');
    Route::get('/server/{id}/linux-data', [AdminAssetController::class, 'linux_data'])->middleware('admin')->name('admin.server.linux.data');
    Route::get('/server/{id}/mysql', [AdminAssetController::class, 'mysql'])->middleware('admin')->name('admin.server.mysql');
    Route::get('/server/{id}/mysql-data', [AdminAssetController::class, 'mysql_data'])->middleware('admin')->name('admin.assets.mysql.data');
    Route::get('/server/{id}/mysql-slow-queries', [AdminAssetController::class, 'mysql_slow_queries'])->middleware('admin')->name('admin.assets.mysql.slow_queries');
    Route::get('/server/{id}/mysql-warnings', [AdminAssetController::class, 'mysql_warnings'])->middleware('admin')->name('admin.assets.mysql_warnings');
    Route::get('/server/{id}/mysql-errors', [AdminAssetController::class, 'mysql_errors'])->middleware('admin')->name('admin.assets.mysql_errors');
    Route::get('/server/{id}/mongodb', [AdminAssetController::class, 'mongodb'])->middleware('admin')->name('admin.server.mongodb');
    Route::get('/server/{id}/mongodb-data', [AdminAssetController::class, 'mongodb_data'])->middleware('admin')->name('admin.assets.mongodb.data');
    Route::get('/server/{id}/redis', [AdminAssetController::class, 'redis'])->middleware('admin')->name('admin.server.redis');
    Route::get('/server/{id}/redis-data', [AdminAssetController::class, 'redis_data'])->middleware('admin')->name('admin.assets.redis.data');
    Route::get('/server/{id}/api-log', [AdminAssetController::class, 'api_log'])->middleware('admin')->name('admin.server.api_log');
    Route::get('/server/{id}/api-log/data', [AdminAssetController::class, 'api_log_data'])->middleware('admin')->name('admin.server.api_log.data');
    Route::get('/server/{id}/scheduler', [AdminAssetController::class, 'scheduler'])->middleware('admin')->name('admin.server.scheduler');
    Route::get('/server/{id}/scheduler/data', [AdminAssetController::class, 'scheduler_data'])->middleware('admin')->name('admin.server.scheduler.data');
    Route::get('/server/{id}/ssl', [AdminAssetController::class, 'ssl'])->middleware('admin')->name('admin.server.ssl');

    Route::get('/users', [AdminUserController::class, 'index'])->middleware('admin')->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->middleware('admin')->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->middleware('admin')->name('admin.users.store');
    Route::post('/users/{id}/status', [AdminUserController::class, 'updateStatus'])->middleware('admin')->name('admin.users.status');
    Route::get('/users/{id}/assets', [AdminUserController::class, 'assets'])->middleware('admin')->name('admin.users.assets');
});
