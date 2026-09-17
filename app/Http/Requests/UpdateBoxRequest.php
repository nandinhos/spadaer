<?php

namespace App\Http\Requests;

use App\Models\CommissionMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
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

    protected function prepareForValidation(): void
    {
        $commissionMemberId = $this->input('commission_member_id');
        Log::debug('Preparing validation. Submitted commission_member_id: '.$commissionMemberId);

        if ($commissionMemberId === '') {
            // Garante que string vazia se torne null antes da validação 'exists'
            $this->merge(['commission_member_id' => null]);
            Log::debug('Converted empty commission_member_id to null.');
        } elseif ($commissionMemberId) {
            // Log de verificação (a regra 'exists' fará a validação real)
            $exists = CommissionMember::where('id', $commissionMemberId)->exists();
            Log::debug("Pre-validation check: CommissionMember with ID {$commissionMemberId} exists? ".($exists ? 'Yes' : 'No'));
        }
    }

    /**
     * Customiza mensagens de erro em português.
     */
    public function messages(): array
    {
        return [
            'number.required' => 'O número da caixa é obrigatório.',
            'number.unique' => 'Já existe uma caixa com este número.',
            'project_id.exists' => 'O projeto selecionado é inválido.',
            'commission_member_id.exists' => 'O conferente selecionado é inválido.',
            'conference_date.required_with' => 'A data da conferência é obrigatória quando um conferente é selecionado.',
            'conference_date.date' => 'A data da conferência não é válida.',
            'conference_date.before_or_equal' => 'A data da conferência não pode ser no futuro.',
        ];
    }
}
