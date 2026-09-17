<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyBatchBoxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selected_boxes' => ['required', 'array'],
            'selected_boxes.*' => ['required', 'integer', 'exists:boxes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'selected_boxes.required' => 'Nenhuma caixa foi selecionada para exclusão.',
            'selected_boxes.array' => 'A lista de caixas selecionadas é inválida.',
            'selected_boxes.*.exists' => 'Uma ou mais caixas selecionadas não existem no sistema.',
        ];
    }
}
