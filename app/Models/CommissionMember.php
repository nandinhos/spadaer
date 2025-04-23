<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commission_id',
        'role',
        'start_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relacionamento com o usuário principal
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com as caixas conferidas POR ESTE MEMBRO
    public function checkedBoxes(): HasMany
    {
        // O nome da chave estrangeira em 'boxes' precisa corresponder!
        return $this->hasMany(Box::class, 'checker_member_id');
    }

    // Scope para pegar apenas membros ativos (útil para selects)
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    // Método para verificar se o membro é ativo
    public function isActive(): bool
    {
        return $this->is_active;
    }

    // Método para verificar se o membro é o presidente
    public function isPresident(): bool
    {
        return $this->role === 'president';
    }
}
