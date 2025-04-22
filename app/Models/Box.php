<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Box extends Model
{
    protected $fillable = [
        'number',           // Número da caixa (441)
        'project',          // Projeto (ex: PROJETO DE PESQUISA E DESENVOLVIMENTO)
        'year_range',       // Período (ex: 2010-2011)
        'current_archive',  // Arquivo Corrente (ex: 2016)
        'intermediate_archive', // Arquivo Intermediário (ex: 2026)
        'final_destination',    // Destinação Final (ex: Guarda permanente)
    ];

    /**
     * Os documentos contidos nesta caixa.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'box_number', 'number');
    }
}