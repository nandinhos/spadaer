<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Defina aqui suas políticas, se houver
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir Gates para permissões específicas
        $this->defineGates([
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',
            'documents.import',
            'documents.export.excel',
            'documents.export.pdf',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'commissions.view',
            'commissions.create',
            'commissions.edit',
            'commissions.delete',
        ]);
    }

    /**
     * Define Gates para uma lista de permissões.
     *
     * @param array $permissions
     * @return void
     */
    protected function defineGates(array $permissions): void
    {
        foreach ($permissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                $result = $user->hasPermissionTo($permission);

                // Log apenas em ambiente de debug, se necessário
                if (config('app.debug')) {
                    Log::info("Gate {$permission}", [
                        'user_id' => $user->id,
                        'result' => $result,
                    ]);
                }

                return $result;
            });
        }
    }
}