<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBoxRequest extends FormRequest
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
        $boxId = $this->box->id; // Pega o ID da caixa da rota

        return [
            'number' => [
                'required',
                'string',
                'max:50',
                // Garante unicidade ignorando a caixa atual
                Rule::unique('boxes', 'number')->ignore($boxId),
            ],
            'physical_location' => [
                'nullable',
                'string',
                'max:255',
            ],
            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],
            'checker_member_id' => [
                'nullable',
                'integer',
                'exists:commission_members,id',
            ],
            'conference_date' => [
                'nullable',
                'required_with:checker_member_id',
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    /**
     * Customiza mensagens de erro (opcional).
     * Pode herdar do StoreBoxRequest ou definir aqui.
     */
    // public function messages(): array
    // {
    //     return [ /* ... */ ];
    // }
}
