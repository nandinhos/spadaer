<?php

namespace App\Imports;

use App\Models\Box; // Necessário para validação 'exists' de projeto (se ainda vier no CSV)
use App\Models\Document;
// Necessário para validação 'exists' de projeto (se ainda vier no CSV)
use Illuminate\Support\Collection; // Usado em collection()
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures; // Lê tudo para uma coleção
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Usa a primeira linha como cabeçalho
use Maatwebsite\Excel\Concerns\ToCollection; // Permite usar o método rules()
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Pula linhas que falham em rules()
use Maatwebsite\Excel\Concerns\WithValidation; // Permite coletar falhas de rules()

// Para capturar exceções no parse de data

class DocumentsBoxImport implements
    SkipsOnFailure, // Nome da Classe Alterado
    ToCollection,
    WithHeadingRow,
    WithValidation
{
    // SkipsFailures coleta falhas das rules(), Importable permite $this->failures()
    use Importable, SkipsFailures;

    private ?int $userId;

    private int $targetBoxId; // << ESSENCIAL: ID da caixa de destino (não nullable nesta classe)

    // !! MAPEAMENTO PARA CSV DE CAIXA (sem box_id) !!
    // !! Ajuste as chaves à ESQUERDA para corresponderem 100% ao CSV sem box_id !!
    private array $columnMap = [
        // 'box_id' NÃO ESTÁ NO CSV
        'item_number' => 'item_number',
        'code' => 'code',
        'descriptor' => 'descriptor',
        'document_number' => 'document_number',
        'title' => 'title',
        'document_date' => 'document_date_csv', // Cabeçalho da data no CSV
        'project_id' => 'project_id', // Assume que project_id AINDA vem no CSV
        // OU use busca por nome se o CSV tiver nome do projeto:
        // 'projeto'        => 'project_name_csv', // Cabeçalho para nome/código do projeto
        'confidentiality' => 'confidentiality',
        'version' => 'version',
        'is_copy' => 'is_copy',
    ];

    // Armazena os dados que passaram em TODAS as validações
    private array $validatedData = [];

    // Armazena TODOS os erros encontrados [linha => ['errors' => [campo => [mensagem]], 'values' => [...]]]
    private array $collectedErrors = [];

    // *** Construtor recebe o ID da caixa alvo (OBRIGATÓRIO) ***
    public function __construct(?int $userId, int $targetBoxId) // targetBoxId NÃO é nullable aqui
    {
        $this->userId = $userId;
        $this->targetBoxId = $targetBoxId; // Armazena o ID da caixa
    }

    /**
     * Processa a coleção de linhas lidas do CSV (sem box_id).
     * Valida cada linha e armazena dados válidos ou erros.
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

            // Buscar project_id por nome se o CSV tiver nome e não ID (exemplo)
            // $projectId = null;
            // if ($projectName = $mappedRow['project_name_csv'] ?? null) {
            //     $projectId = $this->findProjectId($projectName); // Precisaria do método findProjectId
            // }

            // Array para passar ao Validator manual
            $dataToValidate = $mappedRow;
            $dataToValidate['box_id'] = $this->targetBoxId; // << ADICIONA O ID DA CAIXA ALVO para validação
            $dataToValidate['item_number'] = $itemNumberString;
            $dataToValidate['code'] = $codeString;
            $dataToValidate['version'] = $versionString;
            $dataToValidate['processed_date'] = $documentDateMonthYear; // Data validada/normalizada
            // 'is_copy' já está como string ou null em $mappedRow
            // 'project_id' já está em $mappedRow se vier no CSV

            Log::debug("[Row {$rowNumber}] Data prepared for validation:", $dataToValidate);

            // --- Validação Manual Detalhada ---
            // Regras usam os nomes das chaves em $dataToValidate
            $validator = Validator::make($dataToValidate, $this->getValidationRules($isCopyString), $this->customValidationMessages());

            $validator->after(function ($validator) use ($documentDateMonthYear, $mappedRow, $itemNumberString) {
                // Valida formato da data
                if (empty($documentDateMonthYear) && ! empty($mappedRow['document_date_csv'])) { /* ... erro data ... */
                }

                // Valida item único DENTRO da caixa alvo ($this->targetBoxId)
                // NÃO precisa verificar errors->has('box_id') aqui, pois box_id vem do controller e é obrigatório
                $currentBoxId = $this->targetBoxId; // Pega o ID da caixa alvo
                if ($itemNumberString !== null && ! empty($currentBoxId)) { // Verifica se item não é nulo e caixa é válida
                    $exists = Document::where('box_id', $currentBoxId)
                        ->where('item_number', $itemNumberString)
                        ->exists();
                    if ($exists) {
                        $validator->errors()->add('item', 'O item "' . $itemNumberString . '" já existe nesta caixa.');
                    }
                }

                // Validar unicidade composta document_number + is_copy (se document_number é válido)
                if (! $validator->errors()->has('document_number')) {
                    $docNumber = $mappedRow['document_number'];
                    $isCopyString = $mappedRow['is_copy'] ?? null; // Pega o valor de is_copy para a regra
                    $query = Document::where('document_number', $docNumber);
                    if ($isCopyString === null || $isCopyString === '') {
                        $query->where(function ($q) {
                            $q->whereNull('is_copy')->orWhere('is_copy', '');
                        });
                    } else {
                        $query->where('is_copy', $isCopyString);
                    }
                    if ($query->exists()) {
                        $validator->errors()->add('document_number', 'Já existe um documento com este Número e informação de Cópia.');
                    }
                }
            });

            // Se a validação desta linha falhar
            if ($validator->fails()) {
                $rowIdentifier = $mappedRow['document_number'] ?? ($mappedRow['item_number'] ?? "Linha {$rowNumber}");
                $this->collectError($rowNumber, $validator->errors()->messages(), $originalRowData, $rowIdentifier);
                Log::warning("[Row {$rowNumber}] Validation failed.", ['errors' => $validator->errors()->messages()]);
            } else {
                // Se passou, prepara os dados para inserção posterior
                $validated = $validator->validated(); // Pega os dados validados
                $this->validatedData[] = [
                    'box_id' => $this->targetBoxId, // << USA O ID DA CAIXA ALVO
                    'project_id' => $validated['project_id'] ?? null, // ID validado (vindo do CSV)
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
            }
        } // Fim foreach $rows
    }

    /**
     * Define as regras de validação BÁSICAS (rules()) para o CSV SEM box_id.
     * Erros aqui são coletados via SkipsFailures.
     */
    public function rules(): array
    {
        // Chaves são os CABEÇALHOS DO CSV (case-insensitive)
        // Valida a presença e tipo básico dos dados nas colunas do CSV (sem box_id)
        return [
            // '*.box_id' => ['required', 'numeric'], // REMOVIDO
            '*.item_number' => ['required'],
            '*.document_number' => ['required', 'distinct'], // Único no CSV (já é bom)
            '*.title' => ['required'],
            '*.document_date' => ['required'], // Verifica se a coluna existe
            '*.project_id' => ['nullable', 'numeric'], // Se project_id vem no CSV
            // Se o CSV tem nome do projeto em vez de ID, remova essa regra e ajuste mapRowKeys/model
            // '*.projeto' => ['nullable'], // Se CSV tiver coluna 'Projeto' com nome
            '*.confidentiality' => ['nullable', Rule::in(['Ostensivo', 'Público', 'Restrito', 'Confidencial', 'ostensivo', 'público', 'restrito', 'confidencial', 'Restricted', 'restricted', 'confidential', 'Confidential', 'Unclassified', 'unclassified', 'Secreto', 'secreto', 'Secret', 'secret'])],
            '*.is_copy' => ['nullable', 'string', 'max:50'],
            '*.code' => ['nullable'],
            '*.descriptor' => ['nullable'],
            '*.version' => ['nullable'],
        ];
    }

    /**
     * Retorna as regras de validação DETALHADAS para o Validator manual.
     */
    private function getValidationRules($isCopyString): array
    {
        // Regras sem 'box_id' aqui
        return [
            'item_number' => ['required', 'string', 'max:255'], // Valida a string
            'document_number' => [
                'required',
                'string',
                'max:255',
                // Unicidade composta validada no after()
            ],
            'title' => ['required', 'string', 'max:65535'],
            'document_date_csv' => ['required'], // Presença da data original
            'processed_date' => ['required'], // Valida se o parse MES/ANO funcionou
            'project_id' => ['nullable', 'integer', 'exists:projects,id'], // Valida o ID lido do CSV
            'confidentiality' => ['nullable', 'string', 'max:255', Rule::in(['Ostensivo', 'Público', 'Restrito', 'Confidencial', 'ostensivo', 'público', 'restrito', 'confidencial', 'Restricted', 'restricted', 'confidential', 'Confidential', 'Unclassified', 'unclassified', 'Secreto', 'secreto', 'Secret', 'secret', 'RESERVADO', 'CONFIDENCIAL', 'SECRETO', 'OSTENSIVO'])],
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
            '*.item_number.required' => 'A coluna/cabeçalho "item_number" é obrigatória.',
            '*.document_number.required' => 'A coluna/cabeçalho "document_number" é obrigatória.',
            '*.document_number.distinct' => 'O "document_number" está duplicado neste arquivo CSV.', // Único no CSV
            '*.title.required' => 'A coluna/cabeçalho "title" (ou titulo) é obrigatória.',
            '*.document_date.required' => 'A coluna/cabeçalho "document_date" (ou data_documento) é obrigatória.',
            '*.confidentiality.in' => 'O valor em "confidentiality" (ou sigilo) é inválido.',
            '*.is_copy.max' => 'O campo cópia não pode ter mais que 50 caracteres.',

            // Mensagens para validação manual (sem *. prefixo)
            'item_number.required' => 'O Item é obrigatório.',
            'document_number.required' => 'O Número do Documento é obrigatório.',
            'document_number.unique' => 'Já existe um documento com este Número e informação de Cópia.',
            'title.required' => 'O Título é obrigatório.',
            'processed_date.required' => 'O formato da Data do Documento é inválido. Use MES/ANO (ex: JAN/2024).',
            'confidentiality.in' => 'O Nível de Sigilo fornecido é inválido.', // Esta mensagem é para o Validator manual
            'item.unique_in_box' => 'Este Item já existe nesta Caixa.', // Mensagem para erro manual de item
            // ... outras mensagens conforme necessário
        ];
    }

    // --- Métodos auxiliares e Getters ---
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
                return $monthAbbr . '/' . $year; // Retorna normalizado
            }
        }
        Log::warning('Invalid month/year format during import: ' . $dateString);

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
} // Fim da classe DocumentsBoxImport
