<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'physical_location',
        'project_id',
        'commission_member_id',
        'conference_date',
    ];

    // Cast para garantir que a data seja um objeto Carbon
    protected $casts = [
        'conference_date' => 'date',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Renomeia o relacionamento para clareza (opcional, mas recomendado)
    public function commissionMember(): BelongsTo
    {
        // O Laravel infere a chave estrangeira como commission_member_id
        return $this->belongsTo(CommissionMember::class);
    }

    // Accessor para pegar o nome do usuário conferente facilmente (exemplo)
    public function getCheckerNameAttribute(): ?string
    {
        // Acessa o relacionamento pelo novo nome
        return $this->commissionMember?->user?->name;
    }
}
