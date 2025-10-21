<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\PublicationController as AdminPublicationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\CollecteController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\TypeRecyclageController;
use App\Http\Controllers\RecyclageController;
use App\Http\Controllers\AIAssistantController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\ChatController;
// AI Assistant Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/assistant', [AIAssistantController::class, 'index'])->name('assistant.chat');
    Route::post('/assistant/send', [AIAssistantController::class, 'sendMessage'])->name('assistant.send-message');
    Route::get('/assistant/history', [AIAssistantController::class, 'getHistory'])->name('assistant.history');
});

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
Route::post('/collectes/{campagne}/donate', [App\Http\Controllers\StripePaymentController::class, 'createCheckoutSession'])->name('collectes.donate')->middleware('auth');

// Route de test Stripe
Route::get('/test-stripe-payment', function () {
    return view('test-stripe-payment');
})->name('test.stripe.payment')->middleware('auth');

// Routes de test Email
Route::get('/test-email', function () {
    return view('test-email');
})->name('test.email')->middleware('auth');

Route::post('/test-send-email', function (Illuminate\Http\Request $request) {
    try {
        $email = $request->input('email');
        
        \Illuminate\Support\Facades\Mail::raw('Ceci est un email de test envoyé depuis votre application Laravel. Si vous recevez cet email, la configuration SMTP fonctionne correctement ! 🎉', function($message) use ($email) {
            $message->to($email)
                    ->subject('Test Email - ' . config('app.name'));
        });
        
        return redirect()->back()->with('success', 'Email de test envoyé à ' . $email . ' ! Vérifiez votre boîte de réception (et les spams).');
    } catch (\Exception $e) {
        \Log::error('Erreur envoi email de test: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Erreur lors de l\'envoi : ' . $e->getMessage());
    }
})->name('test.send.email')->middleware('auth');

// Routes Stripe
Route::get('/stripe/success/{collecte}', [App\Http\Controllers\StripePaymentController::class, 'success'])->name('stripe.success')->middleware('auth');
Route::get('/stripe/cancel/{collecte}', [App\Http\Controllers\StripePaymentController::class, 'cancel'])->name('stripe.cancel')->middleware('auth');
Route::post('/stripe/webhook', [App\Http\Controllers\StripePaymentController::class, 'webhook'])->name('stripe.webhook');

// Routes publiques pour reclamations
Route::get('/reclamations', [ReclamationController::class, 'publicIndex'])->name('reclamations.index');
Route::get('/reclamations/{reclamation}', [ReclamationController::class, 'publicShow'])->name('reclamations.show');
Route::post('/reclamations/{reclamation}/avis', [AvisController::class, 'publicStore'])->name('reclamations.avis.store')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::post('/reclamations', [ReclamationController::class, 'publicStore'])->name('reclamations.store');


Route::post('/chat', [ChatController::class, 'handleRequest']);

Route::view('/chatbot', 'reclamations.chat'); // Pour afficher la page Blade
});

// Routes pour les publications
Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');

// Routes pour l'administration des publications
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('publications', AdminPublicationController::class);
    // Bulk actions for publications
    Route::post('publications/bulk', [AdminPublicationController::class, 'bulkAction'])->name('publications.bulk');
        Route::patch('publications/{publication}/approve', [AdminPublicationController::class, 'approvePublication'])->name('publications.approve');

    // AJAX user search for Select2
    Route::get('users/search', [\App\Http\Controllers\Admin\UserManagementController::class, 'search'])->name('users.search');

        // Gestion des commentaires
        Route::get('commentaires', [\App\Http\Controllers\Admin\CommentaireController::class, 'index'])->name('commentaires.index');
    Route::post('commentaires/bulk', [\App\Http\Controllers\Admin\CommentaireController::class, 'bulkAction'])->name('commentaires.bulk');
    Route::delete('commentaires/{commentaire}', [\App\Http\Controllers\Admin\CommentaireController::class, 'destroy'])->name('commentaires.destroy');
    });
});

// Routes pour les publications (authentifiées)
Route::middleware('auth')->group(function () {
    Route::get('/publications/create', [PublicationController::class, 'create'])->name('publications.create');
    Route::post('/publications/analyze-image', [PublicationController::class, 'analyzeImage'])->name('publications.analyze-image');
    Route::post('/publications/analyze-content', [PublicationController::class, 'analyzeContent'])->name('publications.analyze-content');
    
    // Nouvelles routes IA
    Route::post('/publications/analyze-ai', [PublicationController::class, 'analyzeWithAI'])->name('publications.analyze-ai');
    Route::post('/publications/generate-suggestions', [PublicationController::class, 'generateContentSuggestions'])->name('publications.generate-suggestions');
    Route::post('/publications/improve-content', [PublicationController::class, 'improveContent'])->name('publications.improve-content');
    Route::post('/publications/generate-hashtags', [PublicationController::class, 'generateHashtags'])->name('publications.generate-hashtags');
    Route::get('/publications/content-calendar', [PublicationController::class, 'getContentCalendar'])->name('publications.content-calendar');
    
    Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');
    Route::get('/publications/{publication}/edit', [PublicationController::class, 'edit'])->name('publications.edit');
    Route::put('/publications/{publication}', [PublicationController::class, 'update'])->name('publications.update');
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');
});

// Routes pour le système de chat (authentifiées)
Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
    // Pages principales
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/room/{roomId}', [ChatController::class, 'showRoom'])->name('room');
    
    // Gestion des rooms
    Route::post('/rooms', [ChatController::class, 'createRoom'])->name('rooms.create');
    Route::post('/rooms/{roomId}/join', [ChatController::class, 'joinRoom'])->name('rooms.join');
    Route::post('/rooms/join-by-code', [ChatController::class, 'joinByCode'])->name('rooms.join-by-code');
    Route::delete('/rooms/{roomId}/leave', [ChatController::class, 'leaveRoom'])->name('rooms.leave');
    Route::get('/rooms/search', [ChatController::class, 'searchRooms'])->name('rooms.search');
    Route::get('/rooms/{roomId}/stats', [ChatController::class, 'getRoomStats'])->name('rooms.stats');
    
    // Gestion des messages
    Route::get('/rooms/{roomId}/messages', [ChatController::class, 'getMessages'])->name('messages.get');
    Route::post('/rooms/{roomId}/messages', [ChatController::class, 'sendMessage'])->name('messages.send');
    Route::put('/messages/{messageId}', [ChatController::class, 'editMessage'])->name('messages.edit');
    Route::delete('/messages/{messageId}', [ChatController::class, 'deleteMessage'])->name('messages.delete');
    
    // Gestion des participants
    Route::post('/rooms/{roomId}/participants/{userId}/manage', [ChatController::class, 'manageParticipant'])->name('participants.manage');
});

// Route pour afficher une publication spécifique (doit être après les routes spécifiques)
Route::get('/publications/{publication}', [PublicationController::class, 'show'])->name('publications.show');
// Route to serve publication images when storage link is not accessible
Route::get('/publications/image/{filename}', [PublicationController::class, 'image'])->name('publications.image');

// Routes pour le recyclage
Route::get('/recyclages', [RecyclageController::class, 'index'])->name('recyclages.index');

// Routes pour le recyclage (authentifiées) - AVANT la route show pour éviter les conflits
Route::middleware('auth')->group(function () {
    Route::get('/recyclages/create', [RecyclageController::class, 'create'])->name('recyclages.create');
    Route::post('/recyclages', [RecyclageController::class, 'store'])->name('recyclages.store');
    Route::get('/recyclages/{recyclage}/edit', [RecyclageController::class, 'edit'])->name('recyclages.edit');
    Route::put('/recyclages/{recyclage}', [RecyclageController::class, 'update'])->name('recyclages.update');
    Route::delete('/recyclages/{recyclage}', [RecyclageController::class, 'destroy'])->name('recyclages.destroy');
});



// Routes pour le recyclage (authentifiées) - AVANT la route show pour éviter les conflits
Route::middleware('auth')->group(function () {
    Route::get('/recyclages/create', [RecyclageController::class, 'create'])->name('recyclages.create');
    Route::post('/recyclages', [RecyclageController::class, 'store'])->name('recyclages.store');
    Route::get('/recyclages/{recyclage}/edit', [RecyclageController::class, 'edit'])->name('recyclages.edit');
    Route::put('/recyclages/{recyclage}', [RecyclageController::class, 'update'])->name('recyclages.update');
    Route::delete('/recyclages/{recyclage}', [RecyclageController::class, 'destroy'])->name('recyclages.destroy');
});


// Route show APRÈS les routes spécifiques pour éviter les conflits
Route::get('/recyclages/{recyclage}', [RecyclageController::class, 'show'])->name('recyclages.show');


// Routes pour les commentaires et likes (authentifiées)
Route::middleware('auth')->group(function () {
    // Routes pour les commentaires
    Route::post('/publications/{publication}/commentaires', [CommentaireController::class, 'store'])->name('commentaires.store');
    Route::put('/commentaires/{commentaire}', [CommentaireController::class, 'update'])->name('commentaires.update');
    Route::delete('/commentaires/{commentaire}', [CommentaireController::class, 'destroy'])->name('commentaires.destroy');
    
    // Routes pour les likes
    Route::post('/publications/{publication}/like', [LikeController::class, 'toggleLike'])->name('publications.like');
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
    Route::get('/my-tickets/{registration}', [EventRegistrationController::class, 'showTicket'])->name('events.ticket');
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

    // Gestion des Chat Rooms
    Route::resource('chat-rooms', \App\Http\Controllers\Admin\ChatRoomController::class);
    Route::post('chat-rooms/bulk', [\App\Http\Controllers\Admin\ChatRoomController::class, 'bulk'])->name('chat-rooms.bulk');
    Route::patch('chat-rooms/{chatRoom}/toggle-status', [\App\Http\Controllers\Admin\ChatRoomController::class, 'toggleStatus'])->name('chat-rooms.toggle-status');
    Route::patch('chat-rooms/{chatRoom}/regenerate-code', [\App\Http\Controllers\Admin\ChatRoomController::class, 'regenerateInviteCode'])->name('chat-rooms.regenerate-code');
    
    // Gestion des participants
    Route::get('chat-rooms/{chatRoom}/participants', [\App\Http\Controllers\Admin\ChatRoomController::class, 'participants'])->name('chat-rooms.participants');
    Route::post('chat-rooms/{chatRoom}/participants', [\App\Http\Controllers\Admin\ChatRoomController::class, 'addParticipant'])->name('chat-rooms.add-participant');
    Route::delete('chat-rooms/{chatRoom}/participants/{user}', [\App\Http\Controllers\Admin\ChatRoomController::class, 'removeParticipant'])->name('chat-rooms.remove-participant');
    Route::patch('chat-rooms/{chatRoom}/participants/{user}/ban', [\App\Http\Controllers\Admin\ChatRoomController::class, 'toggleBan'])->name('chat-rooms.toggle-ban');
    
    // Export des données
    Route::get('chat-rooms/{chatRoom}/export-messages', [\App\Http\Controllers\Admin\ChatRoomController::class, 'exportMessages'])->name('chat-rooms.export-messages');
    Route::get('chat-rooms/{chatRoom}/export-participants', [\App\Http\Controllers\Admin\ChatRoomController::class, 'exportParticipants'])->name('chat-rooms.export-participants');


    //gestion avis et reclamation
    Route::resource('reclamations', ReclamationController::class);
    
    // Routes pour les réponses aux réclamations
    Route::post('reclamations/{reclamation}/responses', [App\Http\Controllers\ResponseController::class, 'store'])->name('reclamations.responses.store');
    Route::put('reclamations/{reclamation}/responses/{response}', [App\Http\Controllers\ResponseController::class, 'update'])->name('reclamations.responses.update');
    Route::delete('reclamations/{reclamation}/responses/{response}', [App\Http\Controllers\ResponseController::class, 'destroy'])->name('reclamations.responses.destroy');
    
    Route::get('avis', [AvisController::class, 'index'])->name('avis.index');
    Route::get('avis/create', [AvisController::class, 'create'])->name('avis.create');
    Route::post('avis', [AvisController::class, 'store'])->name('avis.store');
    Route::get('avis/{avis}', [AvisController::class, 'showTopLevel'])->name('avis.show');
    Route::get('avis/{avis}/edit', [AvisController::class, 'editTopLevel'])->name('avis.edit');
    Route::put('avis/{avis}', [AvisController::class, 'updateTopLevel'])->name('avis.update');
    Route::delete('avis/{avis}', [AvisController::class, 'destroyTopLevel'])->name('avis.destroy');
    Route::resource('reclamations.avis', AvisController::class)->parameters(['avis' => 'avis']);

    // Gestion des types de recyclage (admin seulement)
    Route::resource('type-recyclages', TypeRecyclageController::class);

    // Gestion des billets électroniques (QR Codes)
    Route::get('tickets/scan/{event?}', [TicketController::class, 'scanInterface'])->name('tickets.scan');
    Route::post('tickets/validate', [TicketController::class, 'validateTicket'])->name('tickets.validate');
    Route::get('tickets/event/{event}', [TicketController::class, 'eventTickets'])->name('tickets.event');
    Route::post('tickets/{registration}/regenerate', [TicketController::class, 'regenerateQRCode'])->name('tickets.regenerate');
    Route::get('tickets/{registration}/download', [TicketController::class, 'downloadQRCode'])->name('tickets.download');
    Route::post('tickets/{registration}/cancel', [TicketController::class, 'cancelTicket'])->name('tickets.cancel');

});

require __DIR__.'/auth.php';
