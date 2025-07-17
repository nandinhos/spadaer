<?php

namespace App\Imports;

use App\Models\Document;
use App\Models\Project; // Garanta que o model Project está sendo importado
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DocumentsBoxImport implements SkipsOnFailure, ToCollection, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    private ?int $userId;

    private int $targetBoxId;

    // Mapeamento para CSV de caixa (sem box_id)
    private array $columnMap = [
        'item_number' => 'item_number',
        'code' => 'code',
        'descriptor' => 'descriptor',
        'document_number' => 'document_number',
        'title' => 'title',
        'document_date' => 'document_date_csv',
        'project_id' => 'project_id',
        'confidentiality' => 'confidentiality',
        'version' => 'version',
        'is_copy' => 'is_copy',
    ];

    private array $validatedData = [];

    private array $collectedErrors = [];

    public function __construct(?int $userId, int $targetBoxId)
    {
        $this->userId = $userId;
        $this->targetBoxId = $targetBoxId;
    }

    /**
     * Processa a coleção de linhas do CSV.
     */
    public function collection(Collection $rows)
    {
        $rowNumber = 1; // Linha do cabeçalho

        foreach ($rows as $row) {
            $rowNumber++;
            $originalRowData = $row->toArray();
            Log::debug("----- Processing Box CSV Row #{$rowNumber} for Box ID {$this->targetBoxId} -----", $originalRowData);

            if (collect($originalRowData)->filter()->isEmpty()) {
                Log::debug("[Row {$rowNumber}] Skipping empty row.");

                continue;
            }

            $mappedRow = $this->mapRowKeys($originalRowData);
            Log::debug("[Row {$rowNumber}] Mapped data:", $mappedRow);

            // --- Processamento Prévio ---
            $documentDateMonthYear = $this->validateAndNormalizeMonthYear($mappedRow['document_date_csv'] ?? null);
            $itemNumberString = isset($mappedRow['item_number']) ? (string) $mappedRow['item_number'] : null;
            $codeString = isset($mappedRow['code']) ? (string) $mappedRow['code'] : null;
            $versionString = isset($mappedRow['version']) ? (string) $mappedRow['version'] : null;
            $isCopyString = $mappedRow['is_copy'] ?? null;

            // Array para passar ao Validator
            $dataToValidate = $mappedRow;
            $dataToValidate['box_id'] = $this->targetBoxId; // Adiciona o ID da caixa
            $dataToValidate['item_number'] = $itemNumberString;
            $dataToValidate['code'] = $codeString;
            $dataToValidate['version'] = $versionString;
            $dataToValidate['processed_date'] = $documentDateMonthYear;

            Log::debug("[Row {$rowNumber}] Data prepared for validation:", $dataToValidate);

            // --- Validação Manual ---
            $validator = Validator::make($dataToValidate, $this->getValidationRules($isCopyString), $this->customValidationMessages());

            $validator->after(function ($validator) use ($itemNumberString, $mappedRow) {
                // Valida item único na caixa alvo
                $currentBoxId = $this->targetBoxId;
                if ($itemNumberString !== null) {
                    $exists = Document::where('box_id', $currentBoxId)
                        ->where('item_number', $itemNumberString)
                        ->exists();
                    if ($exists) {
                        $validator->errors()->add('item', 'O item "'.$itemNumberString.'" já existe nesta caixa.');
                    }
                }

                // Valida unicidade de document_number + is_copy
                if (! $validator->errors()->has('document_number')) {
                    $docNumber = $mappedRow['document_number'];
                    $isCopy = $mappedRow['is_copy'] ?? null;
                    $query = Document::where('document_number', $docNumber);

                    if ($isCopy === null || $isCopy === '') {
                        $query->where(function ($q) {
                            $q->whereNull('is_copy')->orWhere('is_copy', '');
                        });
                    } else {
                        $query->where('is_copy', $isCopy);
                    }

                    if ($query->exists()) {
                        $validator->errors()->add('document_number', 'Já existe um documento com este Número e informação de Cópia.');
                    }
                }
            });

            if ($validator->fails()) {
                $rowIdentifier = $mappedRow['document_number'] ?? ($mappedRow['item_number'] ?? "Linha {$rowNumber}");
                $this->collectError($rowNumber, $validator->errors()->messages(), $originalRowData, $rowIdentifier);
                Log::warning("[Row {$rowNumber}] Validation failed.", ['errors' => $validator->errors()->messages()]);
            } else {
                // Prepara dados para inserção
                $validated = $validator->validated();
                $this->validatedData[] = [
                    'box_id' => $this->targetBoxId,
                    'project_id' => $validated['project_id'] ?? null,
                    'item_number' => $itemNumberString,
                    'code' => $codeString,
                    'descriptor' => $validated['descriptor'] ?? null,
                    'document_number' => $validated['document_number'],
                    'title' => $validated['title'],
                    'document_date' => $documentDateMonthYear,
                    'confidentiality' => $validated['confidentiality'] ?? null,
                    'version' => $versionString,
                    'is_copy' => $validated['is_copy'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    // 'created_by' => $this->userId,
                ];
                Log::info("[Row {$rowNumber}] Row validated successfully.");
            }
        }
    }

    /**
     * Regras de validação básicas para o CSV sem box_id.
     */
    public function rules(): array
    {
        return [
            '*.item_number' => ['required'],
            '*.document_number' => ['required', 'distinct'],
            '*.title' => ['required'],
            '*.document_date' => ['required'],
            '*.project_id' => ['nullable', 'numeric'],
        ];
    }

    /**
     * Regras de validação detalhadas para o Validator manual.
     */
    private function getValidationRules($isCopyString): array
    {
        return [
            'item_number' => ['required', 'string', 'max:255'],
            'document_number' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:65535'],
            'document_date_csv' => ['required'],
            'processed_date' => ['required'], // Valida se o parse MÊS/ANO funcionou
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'confidentiality' => ['nullable', 'string', 'max:255', Rule::in(['Ostensivo', 'Público', 'Restrito', 'Confidencial', 'ostensivo', 'público', 'restrito', 'confidencial', 'Restricted', 'restricted', 'confidential', 'Confidential', 'Unclassified', 'unclassified', 'Secreto', 'secreto', 'Secret', 'secret', 'RESERVADO', 'CONFIDENCIAL', 'SECRETO', 'OSTENSIVO'])],
            'code' => ['nullable', 'string', 'max:255'],
            'descriptor' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:255'],
            'is_copy' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Mensagens de validação customizadas.
     */
    public function customValidationMessages(): array
    {
        return [
            '*.item_number.required' => 'A coluna "item_number" é obrigatória.',
            '*.document_number.required' => 'A coluna "document_number" é obrigatória.',
            '*.document_number.distinct' => 'O "document_number" está duplicado neste arquivo CSV.',
            '*.title.required' => 'A coluna "title" é obrigatória.',
            '*.document_date.required' => 'A coluna "document_date" é obrigatória.',

            'item_number.required' => 'O Item é obrigatório.',
            'document_number.required' => 'O Número do Documento é obrigatório.',
            'document_number.unique' => 'Já existe um documento com este Número e informação de Cópia.',
            'title.required' => 'O Título é obrigatório.',
            'processed_date.required' => 'O formato da Data do Documento é inválido. Use MÊS/ANO (ex: 01/2025).', // <-- MENSAGEM AJUSTADA
            'confidentiality.in' => 'O Nível de Sigilo fornecido é inválido.',
            'item.unique_in_box' => 'Este Item já existe nesta Caixa.',
        ];
    }

    /**
     * Valida e normaliza a string de data para "MM/YYYY". Retorna null se for inválida.
     */
    private function validateAndNormalizeMonthYear(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }
        $dateString = trim($dateString);

        // Regex para validar o formato MM/YYYY (ex: 01/2025)
        if (preg_match('/^(\d{2})\/(\d{4})$/', $dateString, $matches)) {
            $month = (int) $matches[1];
            $year = (int) $matches[2];

            // Valida se o mês está entre 1 e 12 e o ano é um valor razoável
            if ($month >= 1 && $month <= 12 && $year >= 1900 && $year <= 2100) {
                // Retorna a data no formato MM/YYYY, garantindo que o mês tenha dois dígitos.
                return sprintf('%02d/%d', $month, $year);
            }
        }

        Log::warning('Formato de data inválido durante a importação. Esperado "MM/YYYY", recebido: '.$dateString);

        return null;
    }

    /**
     * Mapeia as chaves da linha do CSV.
     */
    private function mapRowKeys(array $row): array
    {
        $mappedRow = [];
        $lowerCaseRowKeys = array_change_key_case($row, CASE_LOWER);
        foreach ($this->columnMap as $csvHeader => $internalKey) {
            $lowerCsvHeader = strtolower(trim($csvHeader));
            $value = $lowerCaseRowKeys[$lowerCsvHeader] ?? null;
            $mappedRow[$internalKey] = is_string($value) ? trim($value) : $value;
        }

        return $mappedRow;
    }

    /**
     * Coleta erros de validação.
     */
    private function collectError(int $rowNumber, array $errors, array $originalValues = [], string $identifier = 'Dados Inválidos')
    {
        if (! isset($this->collectedErrors[$rowNumber])) {
            $this->collectedErrors[$rowNumber] = [
                'row' => $rowNumber,
                'identifier' => $identifier,
                'errors' => $errors,
                'values' => $originalValues,
            ];
        }
    }

    /**
     * Retorna os dados validados.
     */
    public function getValidatedData(): array
    {
        return $this->validatedData;
    }

    /**
     * Retorna todos os erros coletados.
     */
    public function getErrors(): array
    {
        foreach ($this->failures() as $failure) {
            $rowNumber = $failure->row();
            $errors = [];
            foreach ($failure->errors() as $message) {
                $errors[$failure->attribute() ?? 'geral'][] = $message;
            }
            if (! isset($this->collectedErrors[$rowNumber])) {
                $this->collectedErrors[$rowNumber] = [
                    'row' => $rowNumber,
                    'errors' => $errors,
                    'values' => $failure->values() ?? [],
                ];
            } else {
                $this->collectedErrors[$rowNumber]['errors'] = array_merge_recursive(
                    $this->collectedErrors[$rowNumber]['errors'],
                    $errors
                );
            }
        }
        ksort($this->collectedErrors);

        return array_values($this->collectedErrors);
    }
}
