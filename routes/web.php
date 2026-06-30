<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    
    // Stubs for messaging modules
    Route::get('/inbox', function() { return view('messages.inbox'); })->name('inbox');
    Route::get('/sent', function() { return view('messages.inbox'); })->name('sent');
    Route::get('/drafts', function() { return view('messages.inbox'); })->name('drafts');
    Route::get('/settings', function() { return view('settings.index'); })->name('settings.index');
});
