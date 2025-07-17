<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importar Document

class UpdateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Delegar para Policy/Controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Pega o objeto Document da rota através do FormRequest
        // Isso funciona se você tiver Route Model Binding no controller:
        // update(UpdateDocumentRequest $request, Document $document)
        /** @var Document $document */
        $document = $this->route('document'); // 'document' é o nome do parâmetro na rota resource

        if (! $document) {
            // Lidar com caso onde o documento não é encontrado (embora Route Model Binding deva fazer isso)
            // Talvez lançar uma exceção ou adicionar um erro geral.
            // Por simplicidade, vamos assumir que $document existe.
            // Em um cenário real, adicione tratamento de erro aqui.
            // Por exemplo: abort(404, 'Documento não encontrado para atualização.');
            // ou adicione um erro $this->validator->errors()->add(...) no after()
            // Mas o ideal é o Route Model Binding tratar isso antes.
            // Vamos pegar o ID de outra forma se necessário:
            $documentId = $this->route('document') ? $this->route('document')->id : null;
        } else {
            $documentId = $document->id;
        }

        // Pega o valor de is_copy que será validado (após prepareForValidation se existir)
        // Usamos $this->input() pois $validated não está disponível aqui ainda.
        $isCopyString = $this->input('is_copy');

        return [
            'box_id' => ['required', 'integer', 'exists:boxes,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'item_number' => [
                'required', 'string', 'max:255',
                // Garante unicidade de item na caixa, IGNORANDO o documento atual
                Rule::unique('documents', 'item_number')
                    ->where('box_id', $this->input('box_id'))
                    ->ignore($documentId), // << IGNORA O DOCUMENTO ATUAL
            ],
            'document_number' => [
                'required', 'string', 'max:255',
                // Garante unicidade composta, IGNORANDO o documento atual
                Rule::unique('documents', 'document_number')
                    ->where(function ($query) use ($isCopyString) {
                        if ($isCopyString === null || $isCopyString === '') {
                            $query->where(function ($q) {
                                $q->whereNull('is_copy')->orWhere('is_copy', '');
                            });
                        } else {
                            $query->where('is_copy', $isCopyString);
                        }
                    })
                    ->ignore($documentId), // << IGNORA O DOCUMENTO ATUAL
            ],
            'title' => ['required', 'string', 'max:65535'],
            'document_date' => [ // Validar a string MES/ANO
                'required', 'string',
                'regex:/^([a-zA-Z]{3})\/?(\d{4})$/i', // Valida formato MES/ANO
            ],
            'confidentiality' => ['nullable', 'string', 'max:255', Rule::in(['OSTENSIVO', 'PÚBLICO', 'RESTRITO', 'CONFIDENCIAL', 'Ostensivo', 'Público', 'Restrito', 'Confidencial', 'ostensivo', 'público', 'restrito', 'confidencial', 'Restricted', 'restricted', 'confidential', 'Confidential', 'Unclassified', 'unclassified', 'Secreto', 'secreto', 'Secret', 'secret'])],
            'code' => [
                'nullable', 'string', 'max:255',
                // Se 'code' precisar ser único (ignorando o atual):
                // Rule::unique('documents', 'code')->ignore($documentId)
            ],
            'descriptor' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:255'],
            'is_copy' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Prepara dados ANTES da validação (ex: normalizar data MES/ANO)
     */
    protected function prepareForValidation(): void
    {
        // Normaliza a data para MES/ANO antes de validar a regex
        if ($this->has('document_date')) {
            $dateString = $this->input('document_date');
            $normalizedDate = null;
            // Tenta normalizar para XXX/YYYY
            if ($dateString && preg_match('/^([a-zA-Z]{3})[\/\s]?(\d{4})$/', $dateString, $matches)) {
                $monthAbbr = strtoupper($matches[1]);
                $year = $matches[2];
                $validMonths = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
                if (in_array($monthAbbr, $validMonths)) {
                    $normalizedDate = $monthAbbr.'/'.$year;
                }
            }
            // Se normalizado ou não, sobrescreve o valor original para validação
            // A regra regex vai pegar se $normalizedDate for null aqui (formato inválido)
            // Ou podemos apenas passar a string original para a regex validar.
            // Vamos passar a original e deixar a regex fazer o trabalho:
            // $this->merge(['document_date' => $normalizedDate]);
            // Se a validação de data não usar regex, faça a normalização aqui.
        }

        // Garante que is_copy vazio seja null (importante para unique rule)
        if ($this->input('is_copy') === '') {
            $this->merge(['is_copy' => null]);
        }
    }

    /**
     * Mensagens customizadas
     */
    public function messages(): array
    {
        return [
            // ... mensagens anteriores ...
            'document_number.unique' => 'Já existe outro documento com este Número e informação de Cópia.',
            'item_number.unique' => 'Este Item já existe nesta Caixa.',
            'document_date.regex' => 'O formato da Data do Documento deve ser Mês/Ano (ex: 01/2024).',
            // ...
        ];
    }
}
