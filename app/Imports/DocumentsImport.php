<?php

namespace App\Imports;

use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DocumentsImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $importedCount = 0;
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $validator = Validator::make($row->toArray(), $this->rules(), $this->customValidationMessages());

            if ($validator->fails()) {
                $this->errors[] = ['
Linha ' . ($this->getRowNumber($row) + 1) . ': ' . implode(', ', $validator->errors()->all())];
                continue;
            }

            try {
                Document::create([
                    'box_number' => $row['caixa'],
                    'item_number' => $row['item'],
                    'code' => $row['codigo'] ?? null,
                    'descriptor' => $row['descritor'] ?? null,
                    'document_number' => $row['numero'],
                    'title' => $row['titulo'],
                    'document_date' => $row['data'] ?? null,
                    'project' => $row['projeto'] ?? null,
                    'confidentiality' => $row['sigilo'] ?? null,
                    'version' => $row['versao'] ?? null,
                    'is_copy' => $row['copia'] ?? null,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errors[] = ['Linha ' . ($this->getRowNumber($row) + 1) . ': ' . $e->getMessage()];
            }
        }
    }

    public function rules(): array
    {
        return [
            'caixa' => 'required',
            'item' => 'required',
            'codigo' => 'nullable',
            'descritor' => 'nullable',
            'numero' => ['required', function($attribute, $value, $fail) {
                $existingDocument = Document::where([
                    'box_number' => request('caixa'),
                    'item_number' => request('item'),
                    'code' => request('codigo'),
                    'descriptor' => request('descritor'),
                    'document_number' => $value,
                    'title' => request('titulo'),
                    'document_date' => request('data'),
                    'project' => request('projeto'),
                    'confidentiality' => request('sigilo'),
                    'version' => request('versao'),
                    'is_copy' => request('copia'),
                ])->first();
                
                if ($existingDocument) {
                    $fail('Um documento idêntico já existe no sistema.');
                }
            }],
            'titulo' => 'required',
            'data' => 'nullable',
            'projeto' => 'nullable',
            'sigilo' => 'nullable',
            'versao' => 'nullable',
            'copia' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'caixa.required' => 'O campo Caixa é obrigatório',
            'item.required' => 'O campo Item é obrigatório',
            'numero.required' => 'O campo Número é obrigatório',
            'numero.unique' => 'O Número do documento já existe no sistema',
            'titulo.required' => 'O campo Título é obrigatório',
        ];
    }

    protected function getRowNumber($row)
    {
        return (int) collect($row)->keys()->first() + 2; // +2 because index starts at 0 and we have a header row
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}