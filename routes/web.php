<?php

use App\Http\Controllers\BoxController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentExportController;
use App\Http\Controllers\DocumentImportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
|
| Rotas acessíveis sem autenticação.
|
*/

Route::get('/', function () {
    // Redireciona para o dashboard se logado, senão mostra a view 'dashboard' (ou 'welcome'/'login')
    if (Auth::check()) {
        return redirect()->route('dashboard'); // Redireciona para a rota nomeada 'dashboard'
    }

    // Se não estiver logado, mostre a view 'auth.login' ou uma landing page
    // return view('welcome');
    return view('auth.login'); // Exemplo: envia direto para login
})->name('home'); // Nomear a rota raiz é uma boa prática

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas
|--------------------------------------------------------------------------
|
| Rotas que exigem que o usuário esteja logado.
| O middleware 'auth' cuida disso.
|
*/
Route::middleware(['auth'])->group(function () { // Adicionar 'verified' se usar verificação de email

    // --- Rota Dashboard ---
    // Aponta para a listagem de documentos como página inicial após login
    Route::get('/dashboard', function () {
        return redirect()->route('documents.index');
    })->name('dashboard'); // Rota nomeada 'dashboard'

    // --- Rotas de Documentos ---
    // Rotas específicas (sem parâmetros de ID) devem vir primeiro

    // Rota para gerar o PDF dos documentos (usando o mesmo controller da exportação Excel)
    Route::get('/documents/export/pdf', [DocumentExportController::class, 'exportPdf'])->name('documents.export.pdf');

    // Rota para exibir o formulário de criação
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');

    // Rota para processar a criação de um novo documento
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

    // Rota para processar a importação de CSV
    Route::post('/documents/import', [DocumentImportController::class, 'import'])->name('documents.import');

    // Rota para importar documentos para uma caixa específica
    // Usa o ID da caixa na URL
    Route::post('/boxes/{box}/documents/import', [DocumentImportController::class, 'importForBox'])->name('boxes.documents.import');

    // Rota para processar a exportação para Excel/CSV
    Route::get('/documents/export', [DocumentExportController::class, 'exportExcel'])->name('documents.export');

    // Rotas padrão de recurso (com parâmetro {document})

    // Rota para exibir a lista principal de documentos (com filtros, etc.)
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    // Rota para obter detalhes de um documento (usada pelo Modal AJAX)
    // Deve vir depois das outras rotas GET /documents/* para não capturá-las
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');

    // Rota para exibir o formulário de edição
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');

    // Rota para processar a atualização de um documento
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    // Route::patch('/documents/{document}', [DocumentController::class, 'update']); // Alias comum para PUT

    // Rota para excluir um documento
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // --- Rotas de Caixas ---
    // Usando Route::resource para gerar automaticamente as rotas CRUD padrão:
    // GET /boxes (index)
    // GET /boxes/create (create)
    // POST /boxes (store)
    // GET /boxes/{box} (show)
    // GET /boxes/{box}/edit (edit)
    // PUT/PATCH /boxes/{box} (update)
    // DELETE /boxes/{box} (destroy)
    Route::resource('boxes', BoxController::class);
    // Rota adicional para ação em lote (se implementada)
    Route::post('/boxes/batch-assign-checker', [BoxController::class, 'batchAssignChecker'])->name('boxes.batchAssignChecker');

    // --- Rotas para Comissões ---
    // Usando Route::resource para as rotas CRUD padrão
    Route::resource('commissions', CommissionController::class);

    // --- Rotas de Perfil (Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

}); // Fim do grupo middleware('auth')

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Breeze)
|--------------------------------------------------------------------------
|
| Inclui as rotas para login, registro, esqueceu senha, etc.
| definidas pelo Laravel Breeze.
|
*/
require __DIR__.'/auth.php';
