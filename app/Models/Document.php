<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon; // Importar Carbon
use Illuminate\Support\Facades\Log; // Importar Log

class Document extends Model
{
    use HasFactory;

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
     * Accessor para obter a data do documento formatada.
     * Nome: get{CamelCaseAtributo}Attribute -> getFormattedDocumentDateAttribute
     * Acesso na view: $document->formatted_document_date
     */
    /*public function getFormattedDocumentDateAttribute(): string
    {
        $originalDate = $this->document_date; // Pega a string salva no banco

        // Retorna um placeholder se a data original for vazia ou nula
        if (empty($originalDate)) {
            return '--';
        }

        try {
            // Tenta interpretar a string como data (Carbon::parse é flexível)
            // e formata para o padrão brasileiro se conseguir
            return Carbon::parse($originalDate)->format('d/m/Y');
        } catch (\Throwable $e) {
            // Se Carbon::parse falhar (string inválida)
            // Loga um aviso para ajudar a encontrar dados ruins
            Log::warning("Could not format document_date string '{$originalDate}' for Document ID {$this->id}: ".$e->getMessage());

            // Retorna uma indicação de erro para a view
            return 'Data Inválida';
            // Alternativa: retornar a string original: return $originalDate;
            // Alternativa: retornar o placeholder: return '--';
        }
    }*/

    // Você pode adicionar outros accessors aqui se precisar formatar
    // ou calcular outros atributos "virtuais". Ex:
    // public function getFullLocationAttribute(): string {
    //     return "Caixa: " . ($this->box?->number ?? '?') . " / Item: " . ($this->item_number ?? '?');
    // }

}
