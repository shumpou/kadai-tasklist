<?php
use App\Http\Controllers\UsersController; // 追記
use App\Http\Controllers\TasksController; //追記

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', [TasksController::class, 'index'])->name('home');

Route::get('/dashboard', [TasksController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('users', UsersController::class, ['only' => ['index', 'show']]);

    // Route::redirect('settings', 'settings/profile');

    // Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    // Volt::route('settings/password', 'settings.password')->name('settings.password');
    // Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::resource('tasks', TasksController::class, ['only' => ['index', 'store', 'destroy', 'show', 'create', 'edit', 'update']]);


});

require __DIR__.'/auth.php';
