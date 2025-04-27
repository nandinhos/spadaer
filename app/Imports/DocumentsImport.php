<?php

namespace App\Imports;

use App\Models\Document;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class DocumentsImport implements SkipsOnFailure, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    private int $importedCount = 0;

    private int $skippedCount = 0;

    private array $errors = [];

    private ?int $userId;

    // !! VERIFIQUE SE OS CABEÇALHOS À ESQUERDA CORRESPONDEM EXATAMENTE AO SEU CSV !!
    private array $columnMap = [
        'box_id' => 'box_id',            // Cabeçalho CSV: box_id
        'project_id' => 'project_id',        // Cabeçalho CSV: project_id
        'item_number' => 'item_number',       // Cabeçalho CSV: item_number
        'code' => 'code',              // Cabeçalho CSV: code
        'descriptor' => 'descriptor',        // Cabeçalho CSV: descriptor
        'document_number' => 'document_number',   // Cabeçalho CSV: document_number
        'title' => 'title',             // Cabeçalho CSV: title
        'document_date' => 'document_date_csv', // Cabeçalho CSV: document_date
        'confidentiality' => 'confidentiality',   // Cabeçalho CSV: confidentiality
        'version' => 'version',           // Cabeçalho CSV: version
        'is_copy' => 'is_copy',           // Cabeçalho CSV: is_copy
    ];

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
    }

    /**
     * Processa cada linha do CSV.
     */
    public function model(array $row): ?Document
    {
        Log::debug('------------------ Processing Row ------------------', $row);
        $mappedRow = $this->mapRowKeys($row);
        Log::debug('Mapped Row Data:', $mappedRow);

        // --- Processamento Prévio e Conversão para String (Opção 2) ---
        $documentDateString = $this->formatDateString($mappedRow['document_date_csv'] ?? null);
        // Garante que os campos sejam tratados como strings antes da validação
        $itemNumberString = isset($mappedRow['item_number']) ? (string) $mappedRow['item_number'] : null;
        $codeString = isset($mappedRow['code']) ? (string) $mappedRow['code'] : null;
        $versionString = isset($mappedRow['version']) ? (string) $mappedRow['version'] : null;
        // is_copy já é tratado como string pelo mapRowKeys e validação
        $isCopyString = $mappedRow['is_copy'] ?? null;

        // Cria um array temporário com os dados processados para validação
        $dataToValidate = $mappedRow;
        $dataToValidate['item_number'] = $itemNumberString;
        $dataToValidate['code'] = $codeString;
        $dataToValidate['version'] = $versionString;
        // is_copy já está correto em $mappedRow, então usamos $dataToValidate['is_copy']

        Log::debug('Data prepared for validation:', $dataToValidate);

        // --- Validação Manual Detalhada ---
        /**
         * Regras de Validação para cada linha do CSV:
         * box_id:           Obrigatório, inteiro, existe em 'boxes'.
         * project_id:       Opcional, inteiro, existe em 'projects' (se fornecido).
         * item_number:      Obrigatório, string. Unicidade DENTRO da 'box_id' validada no after().
         * document_number:  Obrigatório, string. Combinação 'document_number' + 'is_copy' deve ser única.
         * title:            Obrigatório, string.
         * document_date_csv:Obrigatório (presença no CSV), formato validado no after().
         * confidentiality:  Opcional, string, um dos valores permitidos (case-insensitive na validação inicial).
         * code:             Opcional, string (NÃO único).
         * descriptor:       Opcional, string.
         * version:          Opcional, string.
         * is_copy:          Opcional, string (informação da cópia).
         */
        $validator = Validator::make($dataToValidate, [ // Valida os dados processados
            'box_id' => ['required', 'integer', 'exists:boxes,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'item_number' => ['required', 'string', 'max:255'], // Agora valida a string
            'document_number' => [
                'required',
                'string',
                'max:255',
                // Nova tentativa para unicidade composta document_number + is_copy
                Rule::unique('documents', 'document_number')
                    ->where(function ($query) use ($isCopyString) {
                        // Se is_copy for nulo ou vazio no CSV, procura onde é nulo ou vazio no DB
                        if ($isCopyString === null || $isCopyString === '') {
                            $query->where(function ($q) {
                                $q->whereNull('is_copy')->orWhere('is_copy', '');
                            });
                        } else {
                            // Se is_copy tiver valor, procura onde é igual no DB
                            $query->where('is_copy', $isCopyString);
                        }
                    }),
                // ->whereNull('deleted_at') // Mantenha comentado/removido
            ],
            'title' => ['required', 'string', 'max:65535'],
            'document_date_csv' => ['required'],
            'confidentiality' => ['nullable', 'string', 'max:255', Rule::in(['Ostensivo', 'Público', 'Restrito', 'Confidencial', 'ostensivo', 'público', 'restrito', 'confidencial', 'Restricted', 'restricted', 'confidential', 'Confidential'])], // Sua lista expandida
            'code' => ['nullable', 'string', 'max:255'], // Agora valida a string
            'descriptor' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:255'], // Agora valida a string
            'is_copy' => ['nullable', 'string', 'max:50'],
        ], $this->customValidationMessages());

        // Validação adicional após validação básica
        $validator->after(function ($validator) use ($documentDateString, $mappedRow, $itemNumberString) { // Usa $itemNumberString
            // Valida formato da data
            if (empty($documentDateString) && ! empty($mappedRow['document_date_csv'])) {
                $validator->errors()->add('data_documento', 'Formato de data inválido: "'.$mappedRow['document_date_csv'].'".');
            }
            // Valida item único na caixa (só se box_id for válido)
            if (! $validator->errors()->has('box_id')) {
                $boxId = $mappedRow['box_id']; // Pega ID da caixa
                if ($itemNumberString !== null) { // Usa a string convertida
                    $exists = Document::where('box_id', $boxId)
                        ->where('item_number', $itemNumberString) // Compara string com string/varchar
                        ->exists();
                    if ($exists) {
                        $validator->errors()->add('item', 'O item "'.$itemNumberString.'" já existe na caixa ID "'.$boxId.'".');
                    }
                }
            }
        });

        // Se a validação falhar, coleta erros e pula
        if ($validator->fails()) {
            $this->collectManualError(null, $validator->errors()->messages());
            $this->skippedCount++;
            Log::warning('Skipping row due to validation errors.', ['errors' => $validator->errors()->messages(), 'row_data' => $mappedRow]);

            return null;
        }
        // --- Fim Validação ---

        $validatedData = $validator->validated(); // Pega dados que passaram

        $this->importedCount++;

        // Monta o array de dados para o Model usando strings processadas onde necessário
        $documentData = [
            'box_id' => $validatedData['box_id'],
            'project_id' => $validatedData['project_id'] ?? null,
            'item_number' => $itemNumberString, // << USA A STRING
            'code' => $codeString, // << USA A STRING
            'descriptor' => $validatedData['descriptor'] ?? null,
            'document_number' => $validatedData['document_number'],
            'title' => $validatedData['title'],
            'document_date' => $documentDateString, // String YYYY-MM-DD
            'confidentiality' => $validatedData['confidentiality'] ?? null,
            'version' => $versionString, // << USA A STRING
            'is_copy' => $validatedData['is_copy'] ?? null, // String direta validada
        ];

        Log::info('Attempting to create Document instance with data:', $documentData);

        try {
            return new Document($documentData);
        } catch (\Throwable $e) {
            Log::error('Error creating Document model instance:', ['error' => $e->getMessage(), 'data' => $documentData]);
            $this->collectManualError(null, ['database' => ['Erro ao preparar dados para o banco: '.$e->getMessage()]]);
            $this->skippedCount++;
            $this->importedCount--;

            return null;
        }
    }

    /**
     * Regras de validação básicas do pacote Excel (aplicadas ANTES do model()).
     * Validam a presença e tipo básico das colunas/cabeçalhos no CSV.
     */
    public function rules(): array
    {
        // As chaves aqui devem corresponder aos CABEÇALHOS do CSV (case-insensitive)
        // conforme mapeado em $columnMap
        return [
            '*.box_id' => ['required', 'numeric'],
            '*.project_id' => ['nullable', 'numeric'],
            '*.item_number' => ['required'], // Apenas 'required', tipo será validado no model()
            '*.document_number' => ['required', 'distinct'], // Único no CSV
            '*.title' => ['required'],
            '*.document_date' => ['required'], // Cabeçalho no CSV
            '*.confidentiality' => ['nullable', Rule::in(['Ostensivo', 'Público', 'Restrito', 'Confidencial', 'ostensivo', 'público', 'restrito', 'confidencial', 'Restricted', 'restricted', 'confidential', 'Confidential'])],
            '*.is_copy' => ['nullable', 'string', 'max:50'], // Valida se a coluna veio e é string
            '*.code' => ['nullable'], // Apenas verifica se veio (opcional)
            '*.version' => ['nullable'], // Apenas verifica se veio (opcional)
        ];
    }

    /**
     * Mensagens customizadas para as regras de validação BÁSICAS (rules()).
     */
    public function customValidationMessages()
    {
        return [
            '*.box_id.required' => 'A coluna/cabeçalho "box_id" é obrigatória.',
            '*.box_id.numeric' => 'O valor em "box_id" deve ser um número.',
            '*.project_id.numeric' => 'O valor em "project_id" deve ser um número.',
            '*.item_number.required' => 'A coluna/cabeçalho "item_number" é obrigatória.',
            '*.document_number.required' => 'A coluna/cabeçalho "document_number" é obrigatória.',
            '*.document_number.distinct' => 'O "document_number" está duplicado neste arquivo CSV.',
            '*.title.required' => 'A coluna/cabeçalho "title" (ou titulo) é obrigatória.',
            '*.document_date.required' => 'A coluna/cabeçalho "document_date" (ou data_documento) é obrigatória.',
            '*.confidentiality.in' => 'O valor em "confidentiality" (ou sigilo) deve ser um dos valores permitidos.',
            '*.is_copy.max' => 'O campo cópia não pode ter mais que 50 caracteres.',
        ];
    }

    // --- Métodos auxiliares e Getters ---
    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    // Mapeia as chaves do $row usando $columnMap (case-insensitive)
    private function mapRowKeys(array $row): array
    {
        $mappedRow = [];
        $lowerCaseRowKeys = array_change_key_case($row, CASE_LOWER);
        foreach ($this->columnMap as $csvHeader => $internalKey) {
            $lowerCsvHeader = strtolower(trim($csvHeader));
            $value = $lowerCaseRowKeys[$lowerCsvHeader] ?? null;
            // Garante que valores numéricos que devam ser string sejam tratados como tal
            if (in_array($internalKey, ['item_number', 'code', 'version', 'is_copy', 'document_number', 'confidentiality']) && ! is_null($value)) {
                $mappedRow[$internalKey] = (string) trim($value);
            } else {
                $mappedRow[$internalKey] = is_string($value) ? trim($value) : $value;
            }
        }

        return $mappedRow;
    }

    // Formata string de data para YYYY-MM-DD
    private function formatDateString(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }
        try {
            // Tenta converter formatos comuns (DD/MM/YYYY, DD-MM-YYYY) para YYYY-MM-DD
            return Carbon::parse(str_replace('/', '-', $dateString))->toDateString();
        } catch (Throwable $e) {
            Log::warning('Failed to parse date string during import: '.$dateString, ['exception' => $e->getMessage()]);

            return null; // Retorna null se não conseguir parsear
        }
    }

    // Coleta erros manuais
    private function collectManualError(?int $rowNumber, array $errors)
    {
        // Simplifica a estrutura do erro para a view
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages; // Mantém como array de mensagens
        }
        $this->errors[] = [
            'row' => $rowNumber ?? 'Desconhecida',
            'errors' => $formattedErrors,
        ];
    }
} // Fim da classe DocumentsImport
