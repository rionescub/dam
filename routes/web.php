<?php

use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

Route::get('/', function () {
    return view('welcome');
});

RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->email . $request->ip());
});

Fortify::loginView(fn() => view('auth.login'));
Fortify::registerView(fn() => view('auth.register'));
Fortify::requestPasswordResetLinkView(fn() => view('auth.forgot-password'));
Fortify::verifyEmailView(fn() => view('auth.verify-email'));
Fortify::resetPasswordView(fn() => view('auth.reset-password'));
