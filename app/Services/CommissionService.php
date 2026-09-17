<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\CommissionMember;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CommissionService
{
    /**
     * Cria uma nova comissão e vincula os membros informados.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $file = null): Commission
    {
        return DB::transaction(function () use ($data, $file) {
            $path = null;
            if ($file && $file->isValid()) {
                $path = $file->store('ordinances', 'public');
            }

            $commission = Commission::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'ordinance_number' => $data['ordinance_number'],
                'ordinance_date' => $data['ordinance_date'],
                'ordinance_file' => $path,
            ]);

            if (! empty($data['members'])) {
                $this->addMembers($commission, (array) $data['members']);
            }

            return $commission;
        });
    }

    /**
     * Atualiza dados da comissão, trata arquivo de portaria e sincroniza membros.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Commission $commission, array $data, ?UploadedFile $file = null): Commission
    {
        return DB::transaction(function () use ($commission, $data, $file) {
            $path = $commission->ordinance_file;

            if ($file && $file->isValid()) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    Log::info('Arquivo antigo da portaria deletado: '.$path);
                }

                $path = $file->store('ordinances', 'public');
                Log::info('Novo arquivo da portaria salvo: '.$path);
            }

            $commission->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'ordinance_number' => $data['ordinance_number'],
                'ordinance_date' => $data['ordinance_date'],
                'ordinance_file' => $path,
            ]);

            if (isset($data['members'])) {
                $this->syncMembers($commission, (array) $data['members']);
            }

            return $commission;
        });
    }

    /**
     * Remove a comissão e seu arquivo anexado.
     */
    public function delete(Commission $commission): bool
    {
        return DB::transaction(function () use ($commission) {
            if ($commission->ordinance_file && Storage::disk('public')->exists($commission->ordinance_file)) {
                Storage::disk('public')->delete($commission->ordinance_file);
            }

            return (bool) $commission->delete();
        });
    }

    /**
     * Sincroniza membros de uma comissão removendo ausentes e adicionando novos.
     *
     * @param  array<int|string>  $userIds
     */
    public function syncMembers(Commission $commission, array $userIds): void
    {
        $targetUserIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        $currentUserIds = $commission->members()->pluck('user_id')->toArray();

        $userIdsToRemove = array_diff($currentUserIds, $targetUserIds);
        if (! empty($userIdsToRemove)) {
            $commission->members()->whereIn('user_id', $userIdsToRemove)->delete();
            Log::info("Membros removidos da Comissão ID {$commission->id}: ".implode(', ', $userIdsToRemove));
        }

        $userIdsToAdd = array_diff($targetUserIds, $currentUserIds);
        foreach ($userIdsToAdd as $userId) {
            CommissionMember::create([
                'commission_id' => $commission->id,
                'user_id' => $userId,
                'role' => 'member',
                'is_active' => true,
            ]);
            Log::info("Membro adicionado à Comissão ID {$commission->id}: User ID {$userId}");
        }
    }

    /**
     * Adiciona membros à comissão.
     *
     * @param  array<int|string>  $userIds
     */
    protected function addMembers(Commission $commission, array $userIds): void
    {
        $uniqueIds = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        foreach ($uniqueIds as $userId) {
            CommissionMember::create([
                'commission_id' => $commission->id,
                'user_id' => $userId,
                'role' => 'member',
                'is_active' => true,
            ]);
        }
    }
}
