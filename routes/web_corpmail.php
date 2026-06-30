<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    if (Auth::guest()) {
        return redirect('/login');
    }
    return view('dashboard', [
        'user' => Auth::user()
    ]);
})->name('dashboard');

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/admin', function () {
        if (Auth::guest()) {
            return redirect('/login');
        }
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized.');
        }
        return view('admin.dashboard', [
            'user' => Auth::user()
        ]);
    });

    // Admin Settings Panel
    Route::middleware('can:admin-actions')->prefix('admin/settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings.index');
        Route::post('/logo', [\App\Http\Controllers\AdminSettingsController::class, 'uploadLogo']);
        Route::post('/test/smtp', [\App\Http\Controllers\AdminSettingsController::class, 'testSmtp']);
        Route::post('/test/imap', [\App\Http\Controllers\AdminSettingsController::class, 'testImap']);
        Route::post('/test/pop', [\App\Http\Controllers\AdminSettingsController::class, 'testPop']);
        Route::get('/{group}', [\App\Http\Controllers\AdminSettingsController::class, 'show']);
        Route::post('/{group}', [\App\Http\Controllers\AdminSettingsController::class, 'update']);
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    
    // Stubs for messaging modules
    Route::get('/inbox', function() { return view('messages.inbox'); })->name('inbox');
    Route::get('/sent', function() { return view('messages.inbox'); })->name('sent');
    Route::get('/drafts', function() { return view('messages.inbox'); })->name('drafts');
    Route::get('/settings', function() { return view('settings.index'); })->name('settings.index');
});
