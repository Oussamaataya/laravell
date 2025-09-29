<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\CollecteController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\AvisController;

//gestion _collecte
Route::resource('campagnes', CampagneController::class);

// Routes publiques
Route::get('/', function () {
    return view('layouts.layout');
})->name('home');

// Routes des événements publics
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/search', [EventController::class, 'search'])->name('events.search');
Route::get('/events/category/{category}', [EventController::class, 'category'])->name('events.category');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Routes publiques pour collectes
Route::get('/collectes', [App\Http\Controllers\CampagneController::class, 'publicIndex'])->name('collectes.index');
Route::get('/collectes/{campagne}', [App\Http\Controllers\CampagneController::class, 'publicShow'])->name('collectes.show');
Route::get('/collectes/{campagne}/donate', [App\Http\Controllers\CollecteController::class, 'donateForm'])->name('collectes.donate.form')->middleware('auth');
Route::post('/collectes/{campagne}/donate', [App\Http\Controllers\CollecteController::class, 'donate'])->name('collectes.donate')->middleware('auth');

// Routes publiques pour reclamations
Route::get('/reclamations', [ReclamationController::class, 'publicIndex'])->name('reclamations.index');
Route::get('/reclamations/{reclamation}', [ReclamationController::class, 'publicShow'])->name('reclamations.show');
Route::post('/reclamations/{reclamation}/avis', [AvisController::class, 'publicStore'])->name('reclamations.avis.store')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::post('/reclamations', [ReclamationController::class, 'publicStore'])->name('reclamations.store');
});

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

    // Gestion des campagnes
    Route::resource('campagnes', CampagneController::class);

    // Gestion des collectes
    Route::get('/collectes', [CollecteController::class, 'index'])->name('collectes.index');
    Route::get('/collectes/create', [CollecteController::class, 'create'])->name('collectes.create');
    Route::post('/collectes', [CollecteController::class, 'store'])->name('collectes.store');
    Route::get('/collectes/{collecte}', [CollecteController::class, 'show'])->name('collectes.show');
    Route::get('/collectes/{collecte}/edit', [CollecteController::class, 'edit'])->name('collectes.edit');
    Route::put('/collectes/{collecte}', [CollecteController::class, 'update'])->name('collectes.update');
    Route::delete('/collectes/{collecte}', [CollecteController::class, 'destroy'])->name('collectes.destroy');


    //gestion avis et reclamation
    Route::resource('reclamations', ReclamationController::class);
    Route::get('avis', [AvisController::class, 'index'])->name('avis.index');
    Route::get('avis/create', [AvisController::class, 'create'])->name('avis.create');
    Route::post('avis', [AvisController::class, 'store'])->name('avis.store');
    Route::get('avis/{avis}', [AvisController::class, 'showTopLevel'])->name('avis.show');
    Route::get('avis/{avis}/edit', [AvisController::class, 'editTopLevel'])->name('avis.edit');
    Route::put('avis/{avis}', [AvisController::class, 'updateTopLevel'])->name('avis.update');
    Route::delete('avis/{avis}', [AvisController::class, 'destroyTopLevel'])->name('avis.destroy');
    Route::resource('reclamations.avis', AvisController::class)->parameters(['avis' => 'avis']);

});

require __DIR__.'/auth.php';
