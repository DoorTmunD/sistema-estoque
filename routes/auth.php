<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\VerifyEmailController;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    // Formulários de autenticação (GET)
    Volt::route('register', 'pages.auth.register')->name('register');
    Volt::route('login',    'pages.auth.login')->name('login');
    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');
    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');

    // Processar o POST /login
    Route::post('login', function (Request $request) {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors(['email' => 'Estas credenciais não conferem.'])
            ->onlyInput('email');
    });
});

Route::middleware('auth')->group(function () {
    // Confirmações e verificação de e-mail (GET)
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');

   // Processar o POST /logout
    Route::post('logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
    });