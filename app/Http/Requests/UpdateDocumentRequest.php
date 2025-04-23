<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $documentId = $this->document->id; // Pega o ID do documento da rota

        return [
            'box_id' => [
                'required', // Manter obrigatório na atualização?
                'integer',
                'exists:boxes,id',
            ],
            'item_number' => [
                'required',
                'string',
                'max:50',
                // Garante que a combinação box_id e item_number seja única, IGNORANDO o documento atual
                Rule::unique('documents', 'item_number')
                    ->where('box_id', $this->input('box_id'))
                    ->ignore($documentId),
            ],
            'code' => [
                'nullable',
                'string',
                'max:100',
                // Ignora o documento atual ao verificar a unicidade
                Rule::unique('documents', 'code')->ignore($documentId),
            ],
            'descriptor' => [
                'nullable',
                'string',
                'max:255',
            ],
            'document_number' => [
                'required',
                'string',
                'max:100',
                // Ignora o documento atual ao verificar a unicidade
                Rule::unique('documents', 'document_number')->ignore($documentId),
            ],
            'title' => [
                'required',
                'string',
                'max:500',
            ],
            'document_date' => [
                'required',
                'date',
            ],
            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],
            'confidentiality' => [
                'required',
                Rule::in(['Público', 'Restrito', 'Confidencial']),
            ],
            'version' => [
                'nullable',
                'string',
                'max:50',
            ],
            'is_copy' => [
                'nullable',
                'boolean',
            ],
            // 'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'] // Não obrigatório no update
        ];
    }

    /**
     * Customiza mensagens de erro (opcional).
     * Pode herdar do StoreDocumentRequest ou definir aqui.
     */
    // public function messages(): array
    // {
    //     return [ /* ... */ ];
    // }

    /**
     * Prepara os dados para validação (ex: converter checkbox).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_copy' => $this->boolean('is_copy'),
        ]);
    }
}
