<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Para autorização opcional
use Illuminate\Validation\Rule;       // Necessário para regras complexas como 'unique' ignore

class UpdateCommissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Geralmente, a autorização para atualizar um recurso específico
     * é feita via Policy no Controller ($this->authorize('update', $commission)).
     * Por isso, aqui podemos simplesmente retornar true.
     * Se você *não* usar Policies, pode adicionar lógica aqui.
     */
    public function authorize(): bool
    {
        // Retorna true para delegar a autorização para a Policy/Controller.
        return true;

        // Exemplo alternativo (se não usar Policy):
        // return Auth::check(); // Garante que o usuário está logado
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtém o ID da comissão da rota (necessário para a regra 'unique' ignore)
        // Isso funciona porque o Laravel injeta o modelo da rota no objeto Request/FormRequest
        // quando você usa Route Model Binding no controller:
        // update(UpdateCommissionRequest $request, Commission $commission)
        $commissionId = $this->commission->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Garante que o nome seja único, ignorando a comissão atual
                Rule::unique('commissions', 'name')->ignore($commissionId),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'ordinance_number' => [ // Usando o nome consistente do formulário/validação
                'required',
                'string',
                'max:100',
                // Adicione regra unique se necessário, ajustando o nome da coluna no DB ('ordinance'?)
                // Rule::unique('commissions', 'ordinance')->ignore($commissionId),
            ],
            'ordinance_date' => [
                'required',
                'date',
            ],
            'ordinance_file' => [
                'nullable', // Arquivo não é obrigatório na atualização
                'file',
                'mimes:pdf',
                'max:2048', // 2MB max (ajuste conforme necessário)
            ],
            'members' => [
                'required', // Ainda requer que o campo 'members' seja enviado, mesmo que vazio
                'array',     // Deve ser um array (mesmo que vazio: members=[])
            ],
            'members.*' => [ // Valida cada item DENTRO do array 'members'
                'required', // Cada ID enviado deve ter um valor
                'integer',  // Deve ser um inteiro
                'exists:users,id', // Deve existir na tabela 'users'
            ],
        ];
    }

    /**
     * Opcional: Customizar as mensagens de erro de validação.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da comissão é obrigatório.',
            'name.unique' => 'Já existe uma comissão com este nome.',
            'ordinance_number.required' => 'O número da portaria é obrigatório.',
            // 'ordinance_number.unique' => 'Já existe uma comissão com este número de portaria.',
            'ordinance_date.required' => 'A data da portaria é obrigatória.',
            'ordinance_date.date' => 'A data da portaria não é uma data válida.',
            'ordinance_file.file' => 'O arquivo enviado não é válido.',
            'ordinance_file.mimes' => 'O arquivo da portaria deve ser um PDF.',
            'ordinance_file.max' => 'O arquivo da portaria não pode ser maior que 2MB.',
            'members.required' => 'A seleção de membros é obrigatória (pode ser vazia se permitido).',
            'members.array' => 'O campo membros deve ser uma lista.',
            'members.*.required' => 'Um ID de membro inválido foi enviado.',
            'members.*.integer' => 'Um ID de membro inválido foi enviado.',
            'members.*.exists' => 'Um dos usuários selecionados como membro é inválido ou não existe.',
        ];
    }

    /**
     * Opcional: Customizar os nomes dos atributos nas mensagens de erro.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nome da Comissão',
            'description' => 'Descrição',
            'ordinance_number' => 'Número da Portaria',
            'ordinance_date' => 'Data da Portaria',
            'ordinance_file' => 'Arquivo da Portaria',
            'members' => 'Membros',
            'members.*' => 'Membro selecionado',
        ];
    }
}
