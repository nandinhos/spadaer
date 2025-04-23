<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Necessário para regras mais complexas

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Delegar para Policy/Controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'box_id' => [
                'required', // Todo documento deve pertencer a uma caixa? Se não, use 'nullable'
                'integer',
                'exists:boxes,id', // Garante que o ID da caixa existe na tabela boxes
            ],
            'item_number' => [
                'required',
                'string',
                'max:50', // Ajuste o tamanho máximo se necessário
                // Garante que a combinação box_id e item_number seja única
                Rule::unique('documents', 'item_number')->where('box_id', $this->input('box_id')),
            ],
            'code' => [
                'nullable', // Ou 'required' se for obrigatório
                'string',
                'max:100',
                Rule::unique('documents', 'code'), // Código do documento deve ser único?
            ],
            'descriptor' => [
                'nullable', // Ou 'required'
                'string',
                'max:255',
            ],
            'document_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('documents', 'document_number'), // Número do documento único
            ],
            'title' => [
                'required',
                'string',
                'max:500', // Aumente se precisar de títulos maiores
            ],
            'document_date' => [
                'required',
                'date',
            ],
            'project_id' => [
                'nullable', // Documento pode não ter projeto
                'integer',
                'exists:projects,id', // Garante que o ID do projeto existe
            ],
            'confidentiality' => [
                'required',
                Rule::in(['Público', 'Restrito', 'Confidencial']), // Garante que é um dos valores permitidos
            ],
            'version' => [
                'nullable',
                'string',
                'max:50',
            ],
            'is_copy' => [
                'nullable', // Checkboxes não enviados têm valor null
                'boolean', // Garante que seja true/false, 1/0, 'on'/'off' (o Laravel converte)
            ],
            // Adicione regras para upload de arquivo de documento se houver
            // 'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'] // Ex: PDF/Word até 10MB
        ];
    }

    /**
     * Customiza mensagens de erro (opcional).
     */
    public function messages(): array
    {
        return [
            'box_id.required' => 'A caixa é obrigatória.',
            'box_id.exists' => 'A caixa selecionada é inválida.',
            'item_number.required' => 'O número do item na caixa é obrigatório.',
            'item_number.unique' => 'Já existe um item com este número nesta caixa.',
            'document_number.required' => 'O número do documento é obrigatório.',
            'document_number.unique' => 'Este número de documento já está cadastrado.',
            'code.unique' => 'Este código de documento já está cadastrado.',
            'title.required' => 'O título do documento é obrigatório.',
            'document_date.required' => 'A data do documento é obrigatória.',
            'project_id.exists' => 'O projeto selecionado é inválido.',
            'confidentiality.required' => 'O nível de sigilo é obrigatório.',
            'confidentiality.in' => 'O nível de sigilo selecionado é inválido.',
        ];
    }

    /**
     * Prepara os dados para validação (ex: converter checkbox).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Converte o valor do checkbox 'is_copy' para booleano (1/0)
            // Se não for enviado (desmarcado), será null, que se tornará false no banco
            'is_copy' => $this->boolean('is_copy'),
        ]);
    }
}
