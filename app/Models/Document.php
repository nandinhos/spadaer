<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_number',
        'item_number',
        'code',
        'descriptor',
        'document_number',
        'title',
        'document_date',
        'project',
        'confidentiality',
        'version',
        'is_copy',
        // 'conference_military',
        // 'conference_date',
        // 'user_id',
        // 'box_id',
        // 'project_id',
    ];

    protected $casts = [
        'document_date' => 'date:Y-m-d', // Removido pois agora é string
        // 'is_copy' => 'boolean', // Removido pois agora é string
    ];

    // O mutator setSecrecyAttribute foi removido pois o campo confidentiality agora é string
    // A lógica de mapeamento pode ser feita na importação ou em outra camada, se necessário.

    // Relacionamentos (opcional, se criar models Box, Project, User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamentos
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
