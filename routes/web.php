<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\AdminAuthController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin Dashboard Login Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'login'])->middleware(['guest:admin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'store'])->middleware(['guest:admin'])->name('login.store');
    Route::get('dashboard', [AdminAuthController::class, 'dashboard'])->middleware(['auth:admin'])->name('dashboard');
    Route::post('logout', [AdminAuthController::class, 'logout'])->middleware(['auth:admin'])->name('logout');

    Route::middleware(['auth:admin'])->group(function () {
        Volt::route('settings/profile', 'admin.settings.profile')->name('profile.edit');
        Volt::route('settings/password', 'admin.settings.password-update')->name('user-password.edit');
        Volt::route('settings/appearance', 'admin.settings.appearance')->name('appearance.edit');
    });
});

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
