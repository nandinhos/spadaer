<?php

namespace App\Exports;

use App\Models\Document;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings; // Opcional: para exportações em background
use Maatwebsite\Excel\Concerns\WithMapping; // Opcional: para usar $this->download() ou store()

class DocumentsExport implements FromQuery,        // Mapeia cada modelo para um array de linha
    ShouldAutoSize,          // Exporta a partir de uma Query Builder
    WithHeadings,       // Define a linha de cabeçalho
    WithMapping      // Ajusta automaticamente a largura das colunas
    // ShouldQueue      // Descomente se quiser que a exportação rode em fila (background)
{
    // Opcional: Permite usar $export->download() ou $export->store() se chamado fora do Excel::download()
    // use Exportable;

    protected Request $request; // Armazena os parâmetros da request original (filtros, sort, etc.)

    // Recebe a Request com os parâmetros no construtor
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Define a query Eloquent para buscar os dados a serem exportados.
     * Esta query DEVE refletir os filtros e ordenação aplicados na tela.
     */
    public function query()
    {
        // 1. Obter Parâmetros da Requisição (igual ao DocumentController@index)
        $searchTerm = $this->request->input('search');
        $filterBoxNumber = $this->request->input('filter_box_number');
        $filterProjectId = $this->request->input('filter_project_id');
        $filterYear = $this->request->input('filter_year');
        $sortBy = $this->request->input('sort_by', 'documents.id'); // Default sort
        $sortDir = $this->request->input('sort_dir', 'desc');

        // 2. Validar Parâmetros de Ordenação (igual ao DocumentController@index)
        // IMPORTANTE: Manter esta lista sincronizada com a do controller
        $validSortColumns = [
            'documents.id', 'documents.item_number', 'documents.code', 'documents.descriptor',
            'documents.document_number', 'documents.title', 'documents.document_date',
            'documents.confidentiality', 'documents.version', 'documents.is_copy',
            'boxes.number', // Ordenar pelo número da caixa
            'projects.name', // Ordenar pelo nome do projeto
        ];
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = 'documents.id'; // Default seguro
        }
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        // 3. Construir a Query Base com Joins e Eager Loading
        $query = Document::query()
            // Carrega os relacionamentos para usar no map(), selecionando só o necessário
            ->with(['box:id,number', 'project:id,name'])
            // Joins são necessários para FILTRAR e ORDENAR por colunas relacionadas
            ->leftJoin('boxes', 'documents.box_id', '=', 'boxes.id')
            ->leftJoin('projects', 'documents.project_id', '=', 'projects.id');

        // 4. Aplicar Busca Textual (igual ao DocumentController@index)
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $searchTermWild = "%{$searchTerm}%";
                $q->where('documents.item_number', 'like', $searchTermWild)
                    ->orWhere('documents.code', 'like', $searchTermWild)
                    ->orWhere('documents.descriptor', 'like', $searchTermWild)
                    ->orWhere('documents.document_number', 'like', $searchTermWild)
                    ->orWhere('documents.title', 'like', $searchTermWild)
                    ->orWhere('documents.document_date', 'like', $searchTermWild)
                    ->orWhere('boxes.number', 'like', $searchTermWild) // Usa join
                    ->orWhere('projects.name', 'like', $searchTermWild); // Usa join
            });
        }

        // 5. Aplicar Filtros Específicos (igual ao DocumentController@index)
        if ($filterBoxNumber) {
            $query->where('boxes.number', 'like', "%{$filterBoxNumber}%");
        }
        if ($filterProjectId) {
            $query->where('documents.project_id', $filterProjectId);
        }
        if ($filterYear) {
            $query->where('documents.document_date', 'like', '%/'.$filterYear);
        }

        // 6. Aplicar Ordenação (igual ao DocumentController@index)
        // Cuidado ao ordenar por colunas de tabelas juntadas - garanta que o nome esteja correto
        if (str_contains($sortBy, '.')) { // Se já tem nome da tabela (ex: boxes.number)
            $query->orderBy($sortBy, $sortDir);
        } else { // Senão, assume que é da tabela documents e qualifica
            $query->orderBy('documents.'.$sortBy, $sortDir);
        }

        // 7. Selecionar colunas da tabela documents explicitamente
        // Isso é importante quando se usa joins para evitar ambiguidade
        // e para garantir que o Eager Loading funcione corretamente (o `with` precisa do ID)
        $query->select('documents.*');

        // Retorna o Query Builder configurado. O FromQuery cuidará de executá-lo.
        return $query;
    }

    /**
     * Define os cabeçalhos do arquivo Excel/CSV.
     */
    public function headings(): array
    {
        // Ordem deve corresponder à ordem dos dados retornados por map()
        return [
            // 'ID Doc', // Opcional incluir o ID interno
            'Caixa',
            'Item',
            'Projeto',
            'Código',
            'Descritor',
            'Número Doc.',
            'Título',
            'Data Doc.',
            'Sigilo',
            'Versão',
            'Cópia',
        ];
    }

    /**
     * Mapeia/Transforma cada linha (modelo Document) para um array de dados para o Excel.
     * A ordem DEVE corresponder à ordem definida em headings().
     *
     * @param  Document  $document  O modelo Document hidratado com os relacionamentos de `with()`
     */
    public function map($document): array
    {
        // Acessa os dados do documento e seus relacionamentos carregados via `with()`
        return [
            // $document->id, // Descomente se incluiu 'ID Doc' nos headings
            $document->box?->number ?? '',       // Usa relacionamento carregado
            $document->item_number,
            $document->project?->name ?? '',     // Usa relacionamento carregado
            $document->code,
            $document->descriptor,
            $document->document_number,
            $document->title,
            $document->document_date,             // String "MES/ANO"
            $document->confidentiality,
            $document->version,
            $document->is_copy,
        ];
    }
}
