<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar Carbon
use Illuminate\Support\Carbon; // Importar Log
use Illuminate\Support\Facades\Log;

class Document extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'box_id',
        'project_id',
        'item_number',
        'code',
        'descriptor',
        'document_number',
        'title',
        'document_date',    // Coluna VARCHAR
        'confidentiality',
        'version',
        'is_copy',          // Coluna VARCHAR
    ];

    // Removido $casts para document_date e is_copy
    protected $casts = [
        // Nenhum cast necessário para as colunas VARCHAR aqui
    ];

    // Relacionamentos
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Accessor para obter a data do documento formatada (apenas data).
     * Acesso na view: $document->formatted_document_date
     */
    public function getFormattedDocumentDateAttribute(): string
    {
        $originalDate = $this->document_date;

        if (empty($originalDate)) {
            return '--';
        }

        try {
            // Garante pegar apenas a parte da data caso venha com H:i:s
            $dateOnly = explode(' ', $originalDate)[0];

            return Carbon::parse($dateOnly)->format('d/m/Y');
        } catch (\Throwable $e) {
            Log::warning("Could not format document_date string '{$originalDate}' for Document ID {$this->id}: ".$e->getMessage());

            return $originalDate;
        }
    }

    // Você pode adicionar outros accessors aqui se precisar formatar
    // ou calcular outros atributos "virtuais". Ex:
    // public function getFullLocationAttribute(): string {
    //     return "Caixa: " . ($this->box?->number ?? '?') . " / Item: " . ($this->item_number ?? '?');
    // }

}
