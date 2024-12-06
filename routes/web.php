<?php

use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Controllers\DiplomaController;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\CustomSettingsController;

// Redirect the root URL to the admin login page
Route::get('/', function () {
    return redirect('/admin/login');
});

// Rate limiter for login attempts
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->email . $request->ip());
});

// Custom views for Laravel Fortify
Fortify::loginView(fn() => view('auth.login'));
Fortify::registerView(fn() => view('auth.register'));
Fortify::requestPasswordResetLinkView(fn() => view('auth.forgot-password'));
Fortify::verifyEmailView(fn() => view('auth.verify-email'));
Fortify::resetPasswordView(fn() => view('auth.reset-password'));

Route::prefix('admin')->group(function () {

    Route::get('/login', fn() => view('auth.login'))->name('admin.login');

    Route::middleware(['auth'])->group(function () {
        Route::view('/dashboards/main', 'admin.dashboard')->name('admin.dashboard.main');

        Route::get('/diplomas/{diploma}', [DiplomaController::class, 'download'])
            ->name('diploma.download');
    });

    Route::fallback(function () {
        return redirect()->route('admin.login');
    });
});
