<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required' => 'Nenhum arquivo CSV foi selecionado.',
            'csv_file.file' => 'O item enviado não é um arquivo válido.',
            'csv_file.mimes' => 'O arquivo deve ser do tipo CSV ou TXT.',
            'csv_file.max' => 'O arquivo CSV não pode ser maior que 5MB.',
        ];
    }
}
