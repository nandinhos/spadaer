<?php

use App\Http\Controllers\BoxController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentExportController;
use App\Http\Controllers\DocumentImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('auth.login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard redireciona para documentos
    Route::get('/dashboard', function () {
        return redirect()->route('documents.index');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Rotas de Documentos
    |--------------------------------------------------------------------------
    */
    Route::prefix('documents')->name('documents.')->group(function () {
        // Rotas acessíveis para todos os usuários autenticados
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');

        // NOVA ROTA PARA RETORNAR DADOS DO DOCUMENTO EM JSON PARA O MODAL
        Route::get('/{document}/details', [DocumentController::class, 'getJsonDetails'])->name('getJsonDetails');
        

        // Rotas que requerem papel de administrador ou presidente de comissão
        Route::middleware(['role:admin,presidente_comissao'])->group(function () {
            Route::get('/create', [DocumentController::class, 'create'])->name('create');
            Route::post('/', [DocumentController::class, 'store'])->name('store');
            Route::post('/import', [DocumentImportController::class, 'import'])->name('import');
            Route::get('/export', [DocumentExportController::class, 'exportExcel'])->name('export');
            Route::get('/export/pdf', [DocumentExportController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
            Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
            Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Rotas de Caixas (Boxes)
    |--------------------------------------------------------------------------
    */
    Route::prefix('boxes')->name('boxes.')->group(function () {
        Route::delete('/batch-destroy', [BoxController::class, 'batchDestroy'])->name('batch-destroy');
        Route::post('/batch-assign-checker', [BoxController::class, 'batchAssignChecker'])->name('batchAssignChecker');
        Route::post('/{box}/documents/import', [DocumentImportController::class, 'importForBox'])->name('documents.import');
        Route::delete('/{box}/documents/batch-destroy', [BoxController::class, 'batchDestroyDocuments'])->name('documents.batchDestroy');
    });

    // Rotas padrão de recurso (deixadas por último para não interferirem com rotas específicas acima)
    Route::resource('boxes', BoxController::class);
    Route::resource('commissions', CommissionController::class);
    Route::resource('projects', ProjectController::class);

    /*
    |--------------------------------------------------------------------------
    | Rotas de Perfil (Laravel Breeze)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Rotas de Administração (Somente admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions');
        Route::put('/users/{user}/roles', [\App\Http\Controllers\Admin\PermissionController::class, 'updateUserRoles'])->name('users.roles.update');
    });

});

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
