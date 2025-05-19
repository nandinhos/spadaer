<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Os usuários que pertencem a este papel.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * As permissões atribuídas a este papel.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Verifica se o papel tem uma permissão específica.
     *
     * @param string|Permission $permission
     * @return bool
     */
    public function hasPermission(string|Permission $permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }

        return $this->permissions()->where('id', $permission->id)->exists();
    }
}