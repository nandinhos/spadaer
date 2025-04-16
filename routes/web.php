<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController; // Importe o controller
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Rotas públicas (ex: landing page, se houver)
Route::get('/', function () {
     // Redireciona para login ou dashboard se já logado
     if (Auth::check()) {
        return redirect()->route('documents.index');
     }
     return view('welcome'); // Ou sua landing page
});

// Rotas autenticadas
Route::middleware('auth')->group(function () {
    // Rota principal do sistema de documentos
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    // Rota para buscar detalhes de um documento via AJAX (para o modal)
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');

    // Rotas do Breeze (Perfil)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

     // Redireciona a rota padrão /dashboard para /documents
     Route::redirect('/dashboard', '/documents');

     // Adicione outras rotas aqui (ex: criar, editar, deletar documentos)
     // Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
     // Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
     // Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
     // Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
     // Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});


require __DIR__.'/auth.php'; // Rotas de autenticação do Breeze