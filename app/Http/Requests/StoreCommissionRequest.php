<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Para autorização opcional

class StoreCommissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Aqui você pode colocar lógica de autorização básica se não usar Policies.
     * Se usar Policies, pode simplesmente retornar true aqui.
     */
    public function authorize(): bool
    {
        // Exemplo: Só usuários autenticados podem criar
        // return Auth::check();
        // Ou, se usar Policies:
        return true; // A Policy cuidará da autorização no controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Mova as regras de validação do controller para cá
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordinance_number' => 'required|string|max:100', // Nome consistente
            'ordinance_date' => 'required|date',
            'ordinance_file' => 'nullable|file|mimes:pdf|max:2048', // Ajuste max se necessário
            'members' => 'required|array',
            'members.*' => 'required|exists:users,id', // Valida cada ID de membro
        ];
    }

    /**
     * Opcional: Customizar mensagens de erro.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da comissão é obrigatório.',
            'members.required' => 'Selecione pelo menos um membro.',
            'members.*.exists' => 'Um dos usuários selecionados como membro é inválido.',
            // ... outras mensagens
        ];
    }
}
