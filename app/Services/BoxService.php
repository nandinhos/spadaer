<?php

namespace App\Services;

use App\Models\Box;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BoxService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Cria caixas em lote gerando numeração sequencial.
     *
     * @param  array<string, mixed>  $data  Dados validados da caixa base
     * @param  int  $quantity  Quantidade de caixas a serem criadas
     * @return int Quantidade de caixas criadas
     */
    public function createBatch(array $data, int $quantity = 1): int
    {
        $quantity = max(1, min($quantity, 200));
        $baseNumber = $data['number'];

        return DB::transaction(function () use ($data, $quantity, $baseNumber): int {
            $createdBoxes = 0;

            for ($i = 0; $i < $quantity; $i++) {
                $boxData = $data;

                if ($i > 0) {
                    $boxData['number'] = $this->generateSequentialNumber($baseNumber, $i);
                }

                Box::create($boxData);
                $createdBoxes++;
            }

            return $createdBoxes;
        });
    }

    /**
     * Exclui múltiplas caixas em lote, desassociando documentos das caixas ocupadas.
     *
     * @param  array<int>  $boxIds  IDs das caixas
     * @param  User|null  $actor  Usuário autor da ação para notificação
     * @return array{deleted: int, orphaned: int, skipped: int, status: string, message: string}
     */
    public function destroyBatch(array $boxIds, ?User $actor = null): array
    {
        return DB::transaction(function () use ($boxIds, $actor): array {
            $deletedCount = 0;
            $orphanedCount = 0;
            $skippedCount = 0;

            foreach ($boxIds as $boxId) {
                $box = Box::find($boxId);

                if (! $box) {
                    $skippedCount++;

                    continue;
                }

                if ($box->documents()->count() > 0) {
                    $disassociatedIds = $box->documents()->pluck('documents.id')->all();
                    $box->documents()->update(['box_id' => null]);
                    $box->auditManual(
                        'documents_disassociated',
                        ['box_id' => $box->id],
                        ['document_ids' => $disassociatedIds, 'count' => count($disassociatedIds)]
                    );
                    $orphanedCount++;
                } else {
                    $box->delete();
                    $deletedCount++;
                }
            }

            $messages = [];
            if ($deletedCount > 0) {
                $messages[] = "{$deletedCount} caixa(s) vazia(s) excluída(s) com sucesso.";
            }
            if ($orphanedCount > 0) {
                $messages[] = "Documentos de {$orphanedCount} caixa(s) foram desassociados. As caixas com documentos não foram excluídas.";
            }
            if ($skippedCount > 0) {
                $messages[] = "{$skippedCount} caixa(s) selecionada(s) não foram encontradas.";
            }
            if ($deletedCount === 0 && $orphanedCount === 0 && $skippedCount === 0 && count($boxIds) > 0) {
                $messages[] = 'Nenhuma caixa selecionada pôde ser processada (verifique se todas continham documentos e foram desassociadas).';
            } elseif (count($boxIds) === 0) {
                $messages[] = 'Nenhuma caixa foi selecionada para processar.';
            }

            $status = 'success';
            if ($deletedCount === 0 && $orphanedCount === 0) {
                $status = $skippedCount > 0 ? 'warning' : 'info';
            }

            $fullMessage = implode(' ', $messages);

            if ($actor) {
                $this->notificationService->send(
                    $actor,
                    'Caixas processadas em lote',
                    $fullMessage,
                    'fa-boxes-stacked'
                );
            }

            return [
                'deleted' => $deletedCount,
                'orphaned' => $orphanedCount,
                'skipped' => $skippedCount,
                'status' => $status,
                'message' => $fullMessage,
            ];
        });
    }

    /**
     * Remove documentos específicos de uma caixa com auditoria por item.
     *
     * @param  array<int>  $documentIds
     * @return int Quantidade de documentos excluídos
     *
     * @throws \DomainException Se algum documento não pertencer à caixa
     */
    public function destroyDocumentsFromBox(Box $box, array $documentIds): int
    {
        $documents = Document::where('box_id', $box->id)
            ->whereIn('id', $documentIds)
            ->get();

        if ($documents->count() !== count($documentIds)) {
            throw new \DomainException('Um ou mais documentos selecionados não pertencem a esta caixa.');
        }

        return DB::transaction(function () use ($documents): int {
            $deletedCount = 0;
            foreach ($documents as $document) {
                $document->delete();
                $deletedCount++;
            }

            return $deletedCount;
        });
    }

    /**
     * Gera um número sequencial para caixas baseado no número base.
     *
     * @param  string  $baseNumber  Número base da caixa (ex: 'CX001' ou 'AD-2024-01')
     * @param  int  $sequence  Número da sequência (começando em 1)
     * @return string Novo número sequencial
     */
    public function generateSequentialNumber(string $baseNumber, int $sequence): string
    {
        if (preg_match('/(\d+)$/', $baseNumber, $matches)) {
            $baseDigits = $matches[1];
            $prefix = substr($baseNumber, 0, -strlen($baseDigits));
            $newNumber = str_pad((int) $baseDigits + $sequence, strlen($baseDigits), '0', STR_PAD_LEFT);

            return $prefix.$newNumber;
        }

        return $baseNumber.'-'.($sequence + 1);
    }
}
