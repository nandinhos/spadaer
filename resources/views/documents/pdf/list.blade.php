<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Documentos</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        .org-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
        }

        .generated-date {
            font-size: 8pt;
            margin-top: 5px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        thead th {
            background-color: #f8f8f8;
            font-weight: bold;
            font-size: 9pt;
        }

        tbody td {
            font-size: 8.5pt;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            color: #fff;
            text-align: center;
        }

        .badge-blue {
            background-color: #3182ce;
        }

        .badge-green {
            background-color: #38a169;
        }

        .badge-yellow {
            background-color: #d69e2e;
        }

        .badge-red {
            background-color: #e53e3e;
        }

        .badge-gray {
            background-color: #718096;
        }
    </style>
</head>

<body>

    <header>
        <div class="org-title">SPADAER GAC-PAC</div>
        <h1 class="report-title">Relatório de Documentos</h1>
        <p class="generated-date">Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>Caixa</th>
                <th>Item</th>
                <th>Projeto</th>
                <th>Código</th>
                <th>Número Doc.</th>
                <th>Título</th>
                <th>Data Doc.</th>
                <th>Sigilo</th>
                <th>Versão</th>
                <th>Cópia</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($documents as $document)
                <tr>
                    <td>{{ $document->box?->number ?? '--' }}</td>
                    <td>{{ $document->item_number }}</td>
                    <td>{{ $document->project?->name ?? '--' }}</td>
                    <td>{{ $document->code ?? '--' }}</td>
                    <td>{{ $document->document_number }}</td>
                    <td>{{ $document->title }}</td>
                    <td>{{ $document->document_date }}</td>
                    <td>
                        @php
                            $level = strtolower($document->confidentiality ?? '');
                            $badgeClass = match ($level) {
                                'unclassified' => 'badge-blue',
                                'ostensivo', 'público', 'public' => 'badge-green',
                                'confidencial', 'confidential' => 'badge-yellow',
                                'restrito', 'secreto', 'restricted' => 'badge-red',
                                default => 'badge-gray',
                            };
                            $label = $document->confidentiality ?: '--';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                    </td>
                    <td>{{ $document->version ?? '--' }}</td>
                    <td>{{ $document->is_copy ?? '--' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">Nenhum documento encontrado para os
                        filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
