<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon; // Importar Carbon
use Illuminate\Support\Facades\Log; // Importar Log
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

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

    /**
     * Níveis de confidencialidade considerados públicos (fonte única usada
     * por isSecret() e pelo escopo de visibilidade da listagem).
     *
     * @return array<int,string>
     */
    public static function publicConfidentialityLevels(): array
    {
        return [
            'OSTENSIVO',
            'PÚBLICO',
            'UNCLASSIFIED',
            'SEM CLASSIFICAÇÃO',
            'EXPOSIÇÃO PÚBLICA',
        ];
    }

    /**
     * Verifica se o documento é sigiloso.
     */
    public function isSecret(): bool
    {
        $level = mb_strtoupper($this->confidentiality ?? '');

        return ! empty($level) && ! in_array($level, static::publicConfidentialityLevels());
    }

    /**
     * Restringe a query a documentos visíveis ao usuário: quem tem
     * documents.view.secret vê tudo; os demais, só níveis públicos
     * (espelha exatamente a semântica de isSecret()).
     */
    public function scopeWhereVisibleTo(Builder $query, mixed $user): Builder
    {
        // Spatie lança PermissionDoesNotExist se a permissão não foi semeada:
        // banco sem seed = visão restrita (fail-closed), nunca exceção.
        try {
            $canSeeSecret = $user
                && method_exists($user, 'hasPermissionTo')
                && $user->hasPermissionTo('documents.view.secret');
        } catch (PermissionDoesNotExist) {
            $canSeeSecret = false;
        }

        if ($canSeeSecret) {
            return $query;
        }

        $levels = implode(',', array_map(
            fn (string $level): string => "'".str_replace("'", "''", $level)."'",
            static::publicConfidentialityLevels()
        ));

        return $query->where(function (Builder $q) use ($levels): void {
            $q->whereNull('confidentiality')
                ->orWhere('confidentiality', '')
                ->orWhereRaw("UPPER(confidentiality) IN ({$levels})");
        });
    }

    /**
     * Registra log de visualização para o documento.
     */
    public function logView(): void
    {
        $this->audit('viewed');
    }
}
