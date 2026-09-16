<?php

namespace App\Support;

/**
 * Sanitiza parâmetros de ordenação vindos de request ou props Livewire.
 *
 * Nomes de coluna e direção são interpolados crus no SQL pelo query builder,
 * então NUNCA podem trafegar sem allowlist (fail-closed: valor inválido volta
 * para o padrão em vez de quebrar ou injetar SQL).
 */
class SortHelper
{
    /**
     * @param  array<int,string>  $allowedColumns
     * @return array{0: string, 1: string} [coluna, direção]
     */
    public static function sanitize(
        ?string $column,
        ?string $direction,
        array $allowedColumns,
        string $defaultColumn,
        string $defaultDirection = 'asc',
    ): array {
        $direction = strtolower(trim((string) $direction));
        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = $defaultDirection;
        }

        if (! in_array($column, $allowedColumns, true)) {
            $column = $defaultColumn;
        }

        return [$column, $direction];
    }
}
