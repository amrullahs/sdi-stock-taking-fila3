<?php

use Illuminate\Support\Facades\Route;

// Redirect root URL to admin dashboard
Route::get('/', function () {
    return redirect('/admin/dashboard');
});

// Optional: Add a route for authenticated users to access dashboard directly
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/admin/dashboard');
    });
});
