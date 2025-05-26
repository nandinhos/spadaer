<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
// Adicione esta linha se não estiver presente, caso use o tipo Permission nos Gates
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates para Documentos
        Gate::define('documents.view', function (User $user) {
            $result = $user->hasPermission('documents.view');
            Log::info('Gate documents.view', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('documents.create', function (User $user) {
            $result = $user->hasPermission('documents.create');
            Log::info('Gate documents.create', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('documents.edit', function (User $user) {
            $result = $user->hasPermission('documents.edit');
            Log::info('Gate documents.edit', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('documents.delete', function (User $user) {
            $result = $user->hasPermission('documents.delete');
            Log::info('Gate documents.delete', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        // Gates para Usuários
        Gate::define('users.view', function (User $user) {
            $result = $user->hasPermission('users.view');
            Log::info('Gate users.view', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('users.create', function (User $user) {
            $result = $user->hasPermission('users.create');
            Log::info('Gate users.create', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('users.edit', function (User $user) {
            $result = $user->hasPermission('users.edit');
            Log::info('Gate users.edit', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('users.delete', function (User $user) {
            $result = $user->hasPermission('users.delete');
            Log::info('Gate users.delete', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        // Gates para Comissões
        Gate::define('commissions.view', function (User $user) {
            $result = $user->hasPermission('commissions.view');
            Log::info('Gate commissions.view', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('commissions.create', function (User $user) {
            $result = $user->hasPermission('commissions.create');
            Log::info('Gate commissions.create', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('commissions.edit', function (User $user) {
            $result = $user->hasPermission('commissions.edit');
            Log::info('Gate commissions.edit', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('commissions.delete', function (User $user) {
            $result = $user->hasPermission('commissions.delete');
            Log::info('Gate commissions.delete', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        // Definição de Gates específicos para cada operação do sistema
        // Cada Gate verifica a permissão específica do usuário
        // Não há mais bypass para administradores - todas as permissões são verificadas

        // Se você quiser definir gates específicos para cada permissão (opcional,
        // já que 'has-permission' é mais genérico):
        // Gate::define('documents.view', function (User $user) {
        //     return $user->hasPermission('documents.view');
        // });
        Gate::define('documents.import', function (User $user) {
            return $user->hasPermission('documents.import');
        });

        Gate::define('documents.export.excel', function (User $user) {
            $result = $user->hasPermission('documents.export.excel');
            Log::info('Gate documents.export.excel', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });

        Gate::define('documents.export.pdf', function (User $user) {
            $result = $user->hasPermission('documents.export.pdf');
            Log::info('Gate documents.export.pdf', ['user_id' => $user->id, 'result' => $result]);
            return $result;
        });
        // ... e assim por diante para outras permissões
    }
}