<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBoxRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Delegar
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number' => [
                'required',
                'string',
                'max:50', // Ajuste o tamanho
                Rule::unique('boxes', 'number'), // Número da caixa deve ser único
            ],
            'physical_location' => [
                'nullable', // Localização é opcional?
                'string',
                'max:255',
            ],
            'project_id' => [
                'nullable', // Pode não ter projeto
                'integer',
                'exists:projects,id', // Verifica se o ID do projeto existe
            ],
            'commission_member_id' => [ // <-- ATUALIZADO
                'nullable',
                'integer',
                'exists:commission_members,id',
            ],
            'conference_date' => [
                'nullable',
                'required_with:commission_member_id', // <-- ATUALIZADO
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    /**
     * Customiza mensagens de erro (opcional).
     */
    public function messages(): array
    {
        return [
            'number.required' => 'O número da caixa é obrigatório.',
            'number.unique' => 'Já existe uma caixa com este número.',
            'project_id.exists' => 'O projeto selecionado é inválido.',
            'checker_member_id.exists' => 'O conferente selecionado é inválido.',
            'conference_date.required_with' => 'A data da conferência é obrigatória quando um conferente é selecionado.',
            'conference_date.date' => 'A data da conferência não é válida.',
            'conference_date.before_or_equal' => 'A data da conferência não pode ser no futuro.',
        ];
    }
}
