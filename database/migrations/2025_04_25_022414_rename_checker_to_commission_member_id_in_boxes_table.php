<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            // 1. Remover a FK antiga (nome pode variar, por isso verificamos a coluna)
            //    Importante: Faça backup antes se tiver dados!
            $table->dropForeign(['checker_member_id']); // Tenta remover usando a coluna como referência

            // 2. Renomear a coluna
            $table->renameColumn('checker_member_id', 'commission_member_id');

            // 3. Recriar a FK com a nova coluna
            $table->foreign('commission_member_id')
                ->references('id')
                ->on('commission_members')
                ->onDelete('set null'); // Mantenha o onDelete desejado
        });
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            // Processo Inverso
            $table->dropForeign(['commission_member_id']);
            $table->renameColumn('commission_member_id', 'checker_member_id');
            $table->foreign('checker_member_id')
                ->references('id')
                ->on('commission_members')
                ->onDelete('set null');
        });
    }
};
