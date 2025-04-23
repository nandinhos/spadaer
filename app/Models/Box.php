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
        'checker_member_id',
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

    // Atualiza o relacionamento para o conferente
    public function checkerMember(): BelongsTo
    {
        // O nome da chave estrangeira é inferido como checker_member_id
        // Mas podemos ser explícitos: return $this->belongsTo(CommissionMember::class, 'checker_member_id');
        return $this->belongsTo(CommissionMember::class, 'checker_member_id');
    }

    // Opcional: Acesso fácil ao usuário conferente através do membro
    public function checkerUser(): BelongsTo
    {
        // Define um relacionamento "HasOneThrough" reverso ou acessa via checkerMember
        // return $this->hasOneThrough(User::class, CommissionMember::class, 'id', 'id', 'checker_member_id', 'user_id'); // Complexo
        // Mais simples: acessar via o relacionamento existente
        return $this->checkerMember()->with('user')->get()->pluck('user')->first(); // Não ideal para queries
        // Ou usar um Accessor:
    }

    // Accessor para pegar o nome do usuário conferente facilmente (exemplo)
    public function getCheckerNameAttribute(): ?string
    {
        // Acessa o relacionamento carregado (use with('checkerMember.user') na query)
        return $this->checkerMember?->user?->name;
    }
}
