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
        // 1. Rotas Estáticas / Específicas (Devem vir ANTES dos wildcards)
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create')->middleware('role:admin,presidente_comissao');
        Route::post('/', [DocumentController::class, 'store'])->name('store')->middleware('role:admin,presidente_comissao');
        Route::post('/import', [DocumentImportController::class, 'import'])->name('import')->middleware('role:admin,presidente_comissao');
        Route::get('/export', [DocumentExportController::class, 'exportExcel'])->name('export')->middleware('role:admin,presidente_comissao');
        Route::get('/export/pdf', [DocumentExportController::class, 'exportPdf'])->name('export.pdf')->middleware('role:admin,presidente_comissao');

        // 2. Rotas com Wildcards / Parâmetros
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/details', [DocumentController::class, 'getJsonDetails'])->name('getJsonDetails');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit')->middleware('role:admin,presidente_comissao');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('update')->middleware('role:admin,presidente_comissao');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy')->middleware('role:admin,presidente_comissao');
    });

    /*
    |--------------------------------------------------------------------------
    | Rotas de Caixas (Boxes)
    |--------------------------------------------------------------------------
    */
    Route::prefix('boxes')->name('boxes.')->group(function () {
        // Rotas estáticas primeiro
        Route::delete('/batch-destroy', [BoxController::class, 'batchDestroy'])->name('batch-destroy');
        Route::post('/batch-assign-checker', [BoxController::class, 'batchAssignChecker'])->name('batchAssignChecker');

        // Rotas com wildcard de caixa
        Route::post('/{box}/documents/import', [DocumentImportController::class, 'importForBox'])->name('documents.import');
        Route::delete('/{box}/documents/batch-destroy', [BoxController::class, 'batchDestroyDocuments'])->name('documents.batchDestroy');
    });
    Route::resource('boxes', BoxController::class);
    Route::resource('commissions', CommissionController::class);
    Route::resource('projects', ProjectController::class)->middleware('role:admin');

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
        Route::get('/users', \App\Livewire\Admin\UserList::class)->name('users.index');
        Route::get('/roles', \App\Livewire\Admin\RoleManager::class)->name('roles.index');
        Route::get('/roles/{role}/edit', \App\Livewire\Admin\RoleEdit::class)->name('roles.edit');
        Route::get('/audit', function () {
            return view('admin.audit');
        })->name('audit');
    });

});

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
