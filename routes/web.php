<?php

use App\Http\Controllers\BoxController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentExportController;
use App\Http\Controllers\DocumentImportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rotas públicas (ex: landing page, se houver)
Route::get('/', function () {
    // Redireciona para login ou dashboard se já logado
    if (Auth::check()) {
        return redirect()->route('documents.index');
    }

    return view('dashboard'); // Ou sua landing page
});

// Rotas autenticadas
Route::middleware('auth')->group(function () {
    // Rota principal do sistema de documentos
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    // Add these routes for document creation
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

    // Rota para buscar detalhes de um documento via AJAX (para o modal)
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');

    // Rotas para Comissões
    Route::resource('commissions', CommissionController::class);

    // Rotas de caixas
    Route::resource('boxes', BoxController::class);

    // Rotas do Breeze (Perfil)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Redireciona a rota padrão /dashboard para /documents
    // Add this line to define the 'dashboard' named route
    Route::get('/dashboard', function () {
        return redirect()->route('documents.index');
    })->name('dashboard');

    // CORRIGIDO: Aponta para o DocumentImportController
    Route::post('/documents/import', [DocumentImportController::class, 'import'])->name('documents.import');
    // Rotas para gerenciamento de documentos
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // --- Rota de Exportação (Apontando para o novo Controller) ---
    Route::get('/documents/export', [DocumentExportController::class, 'exportExcel'])->name('documents.export');

});

require __DIR__.'/auth.php'; // Rotas de autenticação do Breeze
