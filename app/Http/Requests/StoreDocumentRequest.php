<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; // Importar Carbon se usar para normalizar/validar
use Illuminate\Validation\Rule; // Para log na normalização

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Delegar
    }

    /**
     * Prepara os dados ANTES da validação.
     * Normaliza a data para MES/ANO e trata is_copy.
     */
    protected function prepareForValidation(): void
    {
        $normalizedDate = null;
        if ($this->has('document_date')) {
            $dateString = $this->input('document_date');
            // Tenta validar/normalizar para MES/ANO
            if ($dateString && preg_match('/^([a-zA-Z]{3})[\/\s]?(\d{4})$/', $dateString, $matches)) {
                $monthAbbr = strtoupper($matches[1]);
                $year = $matches[2];
                $validMonths = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];
                if (in_array($monthAbbr, $validMonths)) {
                    $normalizedDate = $monthAbbr.'/'.$year; // Salva normalizado
                } else {
                    Log::warning('Mês inválido detectado em prepareForValidation: '.$dateString);
                }
            } else {
                Log::warning('Formato de data inválido detectado em prepareForValidation: '.$dateString);
            }
        }

        $this->merge([
            // Sobrescreve 'document_date' com o valor normalizado (ou null se inválido)
            // A regra 'required|string|size:8' abaixo vai validar isso.
            'document_date' => $normalizedDate,
            // Converte is_copy para string ou null (se for checkbox, usa boolean())
            // 'is_copy' => $this->boolean('is_copy'), // Se fosse checkbox
            // Se is_copy é text input, apenas garanta que vazio seja null
            'is_copy' => $this->input('is_copy') === '' ? null : $this->input('is_copy'),
        ]);

        Log::debug('Data prepared for validation in StoreDocumentRequest:', $this->all());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Pega o valor de is_copy DEPOIS do prepareForValidation
        $isCopyString = $this->input('is_copy');

        return [
            'box_id' => [
                'required',
                'integer',
                'exists:boxes,id',
            ],
            'item_number' => [
                'required',
                'string',
                'max:255',
                // Garante unicidade DENTRO da caixa selecionada
                Rule::unique('documents', 'item_number')->where('box_id', $this->input('box_id')),
            ],
            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],
            'code' => [ // REMOVIDA A REGRA UNIQUE
                'nullable',
                'string',
                'max:255',
            ],
            'descriptor' => [
                'nullable',
                'string',
                'max:255',
            ],
            'document_number' => [
                'required',
                'string',
                'max:255',
                // Validação de unicidade composta: document_number + is_copy
                Rule::unique('documents', 'document_number')->where(function ($query) use ($isCopyString) {
                    if ($isCopyString === null) { // Checa estritamente por NULL após prepareForValidation
                        $query->where(function ($q) {
                            $q->whereNull('is_copy')->orWhere('is_copy', '');
                        });
                    } else {
                        $query->where('is_copy', $isCopyString);
                    }
                }),
            ],
            'title' => [
                'required',
                'string',
                'max:65535',
            ],
            'document_date' => [ // Valida o valor JÁ PROCESSADO pelo prepareForValidation
                'required',      // Garante que não ficou nulo (ou seja, formato era válido)
                'string',        // Garante que é uma string
                // 'size:8'      // Garante que tem o formato XXX/YYYY (3 + 1 + 4 = 8)
                'regex:/^[A-Z]{3}\/\d{4}$/', // Garante formato MES/ANO (ex: JAN/2024) após normalização
            ],
            'confidentiality' => [
                'required', // Sigilo é obrigatório na criação? Se sim, required. Senão, nullable.
                'string',
                'max:255',
                Rule::in(['Público', 'Restrito', 'Confidencial'/* Adicione outras variações se necessário */]),
            ],
            'version' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_copy' => [ // Valida o valor que foi preparado
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    /**
     * Customiza mensagens de erro (opcional).
     */
    public function messages(): array
    {
        return [
            'box_id.required' => 'A seleção da Caixa é obrigatória.',
            'box_id.exists' => 'A Caixa selecionada é inválida.',
            'item_number.required' => 'O número do Item na caixa é obrigatório.',
            'item_number.unique' => 'Já existe um item com este número nesta caixa.',
            'document_number.required' => 'O número do Documento é obrigatório.',
            'document_number.unique' => 'Já existe um documento com este Número e informação de Cópia.',
            'title.required' => 'O Título do documento é obrigatório.',
            'document_date.required' => 'A Data do Documento é obrigatória ou está em formato inválido: (ex: FEV/2020)',
            'document_date.size' => 'O formato da Data do Documento deve ser Mês/Ano (ex: JAN/2024).',
            'document_date.regex' => 'O formato da Data do Documento deve ser Mês/Ano (ex: JAN/2024).',
            'confidentiality.required' => 'O Nível de Sigilo é obrigatório.',
            'confidentiality.in' => 'O Nível de Sigilo selecionado é inválido.',
            // ... outras mensagens ...
        ];
    }
}
