<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::get('/', function () {
    return view('layouts.layout');
})->name('home');

// Routes des événements publics
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/search', [EventController::class, 'search'])->name('events.search');
Route::get('/events/category/{category}', [EventController::class, 'category'])->name('events.category');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Routes utilisateurs authentifiés
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Gestion du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Inscriptions aux événements
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/unregister', [EventRegistrationController::class, 'unregister'])->name('events.unregister');
    Route::get('/my-registrations', [EventRegistrationController::class, 'myRegistrations'])->name('events.my-registrations');
});

// Routes d'administration (admin seulement)
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Gestion des utilisateurs
    Route::resource('users', UserManagementController::class);
    Route::patch('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Gestion des événements
    Route::resource('events', AdminEventController::class);
    Route::post('events/{event}/duplicate', [AdminEventController::class, 'duplicate'])->name('events.duplicate');
    Route::patch('events/{event}/toggle-featured', [AdminEventController::class, 'toggleFeatured'])->name('events.toggle-featured');
});

require __DIR__.'/auth.php';
