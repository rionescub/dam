<?php

use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
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

Route::middleware(['nova', 'auth'])->group(function () {
    Route::get('/nova-settings/settings', [CustomSettingsController::class, 'getSettings']);
    Route::post('/nova-settings/settings', [CustomSettingsController::class, 'saveSettings']);
});

// Group all admin routes and protect them with 'auth' middleware
Route::prefix('admin')->group(function () {
    // Publicly accessible admin login route
    Route::get('/login', fn() => view('auth.login'))->name('admin.login');

    // All other routes under the 'admin' prefix require authentication
    Route::middleware(['auth'])->group(function () {
        Route::view('/dashboards/main', 'admin.dashboard')->name('admin.dashboard.main');
        // Add more routes here as needed, e.g., settings, users, etc.
    });

    // Redirect any unauthenticated access to /admin/* to the login page
    Route::fallback(function () {
        return redirect()->route('admin.login');
    });
});
