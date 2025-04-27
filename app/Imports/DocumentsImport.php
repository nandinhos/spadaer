<?php

namespace App\Imports;

use App\Models\Box;
use App\Models\Document;    // Necessário para validação 'exists'
// Necessário para validação 'exists'
use Illuminate\Support\Collection; // Usado em collection()
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnFailure; // Lê tudo para uma coleção
use Maatwebsite\Excel\Concerns\SkipsFailures; // Usa a primeira linha como cabeçalho
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Permite usar o método rules()
use Maatwebsite\Excel\Concerns\ToCollection; // Permite coletar falhas de rules()
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Pula linhas que falham em rules()
// Para obter número da linha
use Maatwebsite\Excel\Concerns\WithValidation; // Para capturar falhas de rules() mais detalhadamente (opcional)

class DocumentsImport implements SkipsOnFailure, ToCollection, WithHeadingRow, WithValidation // Pula linhas que falham nas rules() abaixo
    // OnFailure // Implementar se quiser tratar falhas de rules() mais finamente
{
    // SkipsFailures coleta falhas das rules(), Importable permite $this->failures()
    use Importable, SkipsFailures;

    private ?int $userId;

    // !! MAPEAMENTO: CABEÇALHO CSV (Esquerda, Case-Insensitive) -> CHAVE INTERNA (Direita) !!
    // !! Ajuste as chaves à ESQUERDA para corresponderem 100% ao seu CSV !!
    private array $columnMap = [
        'box_id' => 'box_id',
        'project_id' => 'project_id',
        'item_number' => 'item_number',
        'code' => 'code',
        'descriptor' => 'descriptor',
        'document_number' => 'document_number',
        'title' => 'title',
        'document_date' => 'document_date_csv', // Cabeçalho no CSV
        'confidentiality' => 'confidentiality',
        'version' => 'version',
        'is_copy' => 'is_copy',
    ];

    // Armazena os dados que passaram em TODAS as validações
    private array $validatedData = [];

    // Armazena TODOS os erros encontrados [linha => ['errors' => [campo => [mensagem]], 'values' => [...]]]
    private array $collectedErrors = [];

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
    }

    /**
     * Processa a coleção de linhas lidas do CSV.
     * Valida cada linha e armazena dados válidos ou erros.
     */
    public function collection(Collection $rows)
    {
        $rowNumber = 1; // Linha do cabeçalho

        foreach ($rows as $row) {
            $rowNumber++;
            $originalRowData = $row->toArray(); // Dados originais da linha
            Log::debug("----- Processing CSV Row #{$rowNumber} -----", $originalRowData);

            // Pular linhas completamente vazias
            if (collect($originalRowData)->filter()->isEmpty()) {
                Log::debug("[Row {$rowNumber}] Skipping empty row.");

                continue;
            }

            $mappedRow = $this->mapRowKeys($originalRowData);
            Log::debug("[Row {$rowNumber}] Mapped data:", $mappedRow);
            Log::debug("[Row {$rowNumber}] Mapped confidentiality value:", ['value' => $mappedRow['confidentiality'] ?? 'NOT MAPPED']);

            // --- Processamento Prévio ---
            $documentDateMonthYear = $this->validateAndNormalizeMonthYear($mappedRow['document_date_csv'] ?? null);
            $itemNumberString = isset($mappedRow['item_number']) ? (string) $mappedRow['item_number'] : null;
            $codeString = isset($mappedRow['code']) ? (string) $mappedRow['code'] : null;
            $versionString = isset($mappedRow['version']) ? (string) $mappedRow['version'] : null;
            $isCopyString = $mappedRow['is_copy'] ?? null;

            // Array para passar ao Validator manual
            $dataToValidate = $mappedRow;
            $dataToValidate['item_number'] = $itemNumberString; // Usa string
            $dataToValidate['code'] = $codeString;             // Usa string
            $dataToValidate['version'] = $versionString;       // Usa string
            $dataToValidate['processed_date'] = $documentDateMonthYear; // Data validada/normalizada
            // 'is_copy' já está como string ou null em $mappedRow

            Log::debug("[Row {$rowNumber}] Data prepared for validation:", $dataToValidate);

            // --- Validação Manual Detalhada ---
            $validator = Validator::make($dataToValidate, $this->getValidationRules($isCopyString), $this->customValidationMessages());

            $validator->after(function ($validator) use ($mappedRow, $itemNumberString) {
                // Valida item único na caixa (só se box_id for válido)
                // Usamos $mappedRow['box_id'] pois é o ID que veio do CSV
                if (! $validator->errors()->has('box_id') && ($boxId = $mappedRow['box_id'] ?? null)) {
                    if ($itemNumberString !== null) {
                        $exists = Document::where('box_id', $boxId)
                            ->where('item_number', $itemNumberString)
                            ->exists();
                        if ($exists) {
                            $validator->errors()->add('item', 'O item "'.$itemNumberString.'" já existe na caixa ID "'.$boxId.'".');
                        }
                    }
                }
            });

            // Se a validação desta linha falhar
            if ($validator->fails()) {
                $rowIdentifier = $mappedRow['document_number'] ?? ($mappedRow['item_number'] ?? 'Linha inválida');
                $this->collectError($rowNumber, $validator->errors()->messages(), $originalRowData, $rowIdentifier);
                Log::warning("[Row {$rowNumber}] Validation failed.", ['errors' => $validator->errors()->messages()]);
            } else {
                // Se passou, prepara os dados para inserção posterior
                $validated = $validator->validated(); // Pega os dados validados
                $this->validatedData[] = [
                    'box_id' => $validated['box_id'],
                    'project_id' => $validated['project_id'] ?? null,
                    'item_number' => $itemNumberString, // String
                    'code' => $codeString,       // String
                    'descriptor' => $validated['descriptor'] ?? null,
                    'document_number' => $validated['document_number'],
                    'title' => $validated['title'],
                    'document_date' => $documentDateMonthYear, // String MES/ANO
                    'confidentiality' => $validated['confidentiality'] ?? null,
                    'version' => $versionString,      // String
                    'is_copy' => $validated['is_copy'] ?? null,   // String
                    'created_at' => now(),               // Adiciona timestamp
                    'updated_at' => now(),               // Adiciona timestamp
                    // 'created_by'     => $this->userId,      // Opcional
                ];
                Log::info("[Row {$rowNumber}] Row validated successfully.");

                // $validatedData = $validator->validated();
                // Log::debug("[Row {$rowNumber}] Validated Data Array:", $validatedData); // Log para ver dados validados
            }
        } // Fim foreach $rows
    }

    /**
     * Define as regras de validação BÁSICAS aplicadas pelo pacote ANTES do collection().
     * Erros aqui são coletados via SkipsFailures e podem ser acessados com $this->failures().
     */
    public function rules(): array
    {
        // Chaves são os CABEÇALHOS DO CSV (case-insensitive)
        // Mantenha regras simples aqui, principalmente 'required' e 'numeric' para IDs
        return [
            '*.box_id' => ['required', 'numeric'],
            '*.project_id' => ['nullable', 'numeric'], // Permite vazio, mas valida se for número
            '*.item_number' => ['required'],
            '*.document_number' => ['required'], // Único no CSV
            '*.title' => ['required'],
            '*.document_date' => ['required'], // Verifica se a coluna existe
            // Não validar 'exists' ou 'unique' complexo aqui, será feito no model() / validação manual
        ];
    }

    /**
     * Retorna as regras de validação detalhadas para o Validator manual.
     */
    private function getValidationRules($isCopyString): array
    {
        return [
            'box_id' => ['required', 'integer', 'exists:boxes,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'item_number' => ['required', 'string', 'max:255'], // Valida a string
            'document_number' => [
                'required', 'string', 'max:255',
                Rule::unique('documents', 'document_number')->where(function ($query) use ($isCopyString) {
                    if ($isCopyString === null || $isCopyString === '') {
                        $query->where(function ($q) {
                            $q->whereNull('is_copy')->orWhere('is_copy', '');
                        });
                    } else {
                        $query->where('is_copy', $isCopyString);
                    }
                }),
            ],
            'title' => ['required', 'string', 'max:65535'],
            'document_date_csv' => ['required'], // Valida presença da data original
            'processed_date' => ['required'], // Valida se o parse MES/ANO funcionou
            'confidentiality' => ['nullable', 'string', 'max:255', Rule::in(['Ostensivo', 'Público', 'Restrito', 'Confidencial', 'ostensivo', 'público', 'restrito', 'confidencial', 'Restricted', 'restricted', 'confidential', 'Confidential', 'Unclassified', 'unclassified'])],
            'code' => ['nullable', 'string', 'max:255'], // Valida string
            'descriptor' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:255'], // Valida string
            'is_copy' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Mensagens customizadas (usadas por rules() e pelo Validator manual).
     */
    public function customValidationMessages(): array
    {
        return [
            // Mensagens para rules() básicas (com *. prefixo)
            '*.box_id.required' => 'A coluna/cabeçalho "box_id" é obrigatória.',
            '*.box_id.numeric' => 'O valor em "box_id" deve ser um número.',
            '*.project_id.numeric' => 'O valor em "project_id" deve ser um número.',
            '*.item_number.required' => 'A coluna/cabeçalho "item_number" é obrigatória.',
            '*.document_number.required' => 'A coluna/cabeçalho "document_number" é obrigatória.',
            // '*.document_number.distinct' => 'O "document_number" está duplicado neste arquivo CSV.',
            '*.title.required' => 'A coluna/cabeçalho "title" (ou titulo) é obrigatória.',
            '*.document_date.required' => 'A coluna/cabeçalho "document_date" (ou data_documento) é obrigatória.',

            // Mensagens para validação manual (sem *. prefixo)
            'box_id.required' => 'O ID da Caixa é obrigatório.',
            'box_id.integer' => 'O ID da Caixa deve ser um número inteiro.',
            'box_id.exists' => 'A Caixa com o ID fornecido não existe no sistema.',
            'project_id.integer' => 'O ID do Projeto deve ser um número inteiro.',
            'project_id.exists' => 'O Projeto com o ID fornecido não existe no sistema.',
            'item_number.required' => 'O Item é obrigatório.',
            'document_number.required' => 'O Número do Documento é obrigatório.',
            'document_number.unique' => 'Já existe um documento com este Número e informação de Cópia.',
            'title.required' => 'O Título é obrigatório.',
            'document_date_csv.required' => 'A Data do Documento é obrigatória no CSV.', // Erro se coluna faltar
            'processed_date.required' => 'O formato da Data do Documento é inválido. Use MES/ANO (ex: JAN/2024).', // Mensagem se o parse falhar
            'confidentiality.in' => 'O Nível de Sigilo fornecido é inválido.',
            'item.unique_in_box' => 'Este Item já existe nesta Caixa.', // Mensagem para erro manual de item
            // Adicione outras mensagens conforme necessário
        ];
    }

    // --- Métodos auxiliares e Getters ---
    public function batchSize(): int
    {
        return 200;
    } // Mantido para referência, mas não usado por ToCollection

    public function chunkSize(): int
    {
        return 500;
    } // Mantido, ToCollection pode usar chunks implicitamente

    public function getValidatedData(): array
    {
        return $this->validatedData;
    }

    public function getErrors(): array
    { /* ... implementação anterior com $this->collectedErrors e $this->failures() ... */
        // Processa falhas da validação básica (rules()) e adiciona aos erros coletados
        foreach ($this->failures() as $failure) {
            $rowNumber = $failure->row();
            $errors = [];
            // Pega a primeira mensagem de erro para cada atributo que falhou
            foreach ($failure->errors() as $message) {
                $errors[$failure->attribute() ?? 'geral'][] = $message;
            }
            // Evita sobrescrever erros já coletados manualmente para a mesma linha
            if (! isset($this->collectedErrors[$rowNumber])) {
                $this->collectedErrors[$rowNumber] = [
                    'row' => $rowNumber,
                    'errors' => $errors,
                    'values' => $failure->values() ?? [], // Adiciona os valores da linha que falhou
                ];
            } else {
                // Mescla os erros se já houver erros manuais para essa linha
                $this->collectedErrors[$rowNumber]['errors'] = array_merge_recursive(
                    $this->collectedErrors[$rowNumber]['errors'],
                    $errors
                );
                // Adiciona valores se não existirem ainda
                if (empty($this->collectedErrors[$rowNumber]['values'])) {
                    $this->collectedErrors[$rowNumber]['values'] = $failure->values() ?? [];
                }
            }
        }
        // Retorna os erros ordenados pela linha
        ksort($this->collectedErrors);

        return array_values($this->collectedErrors); // Retorna como array indexado
    }

    // Mapeia as chaves do $row usando $columnMap (case-insensitive)
    private function mapRowKeys(array $row): array
    {
        $mappedRow = [];
        $lowerCaseRowKeys = array_change_key_case($row, CASE_LOWER);
        foreach ($this->columnMap as $csvHeader => $internalKey) {
            $lowerCsvHeader = strtolower(trim($csvHeader));
            $value = $lowerCaseRowKeys[$lowerCsvHeader] ?? null;
            // Trim strings, mantenha outros tipos como estão
            $mappedRow[$internalKey] = is_string($value) ? trim($value) : $value;
        }

        return $mappedRow;
    }

    // Valida/Normaliza string de data para "MES/ANO", retorna null se inválido
    private function validateAndNormalizeMonthYear(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }
        $dateString = trim($dateString);
        // Aceita 3 letras, barra ou espaço opcional, 4 dígitos
        if (preg_match('/^([a-zA-Z]{3})[\/\s]?(\d{4})$/', $dateString, $matches)) {
            $monthAbbr = strtoupper($matches[1]);
            $year = $matches[2];
            // Valida mês (Português)
            $validMonths = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];
            if (in_array($monthAbbr, $validMonths)) {
                return $monthAbbr.'/'.$year; // Retorna normalizado
            }
        }
        Log::warning('Invalid month/year format during import: '.$dateString);

        return null;
    }

    // Coleta erros manuais (gerados pela validação dentro do model())
    private function collectError(int $rowNumber, array $errors, array $originalValues = [], string $identifier = 'Dados Inválidos')
    {
        $this->collectedErrors[$rowNumber] = [ // Usa número da linha como chave para evitar duplicação
            'row' => $rowNumber,
            'errors' => $errors, // Formato [campo => [mensagem]]
            'values' => $originalValues, // Guarda os dados originais
        ];
    }
} // Fim da classe DocumentsImport
