<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commission_members', function (Blueprint $table) {
            // Adiciona a chave estrangeira para a tabela 'commissions'
            // Coloque 'after' onde fizer sentido (ex: depois de user_id)
            $table->foreignId('commission_id')
                ->after('user_id') // Ou outra coluna
                ->nullable() // Torne nullable inicialmente se já houver dados
                ->constrained('commissions') // Aponta para a tabela 'commissions'
                ->onDelete('cascade'); // Ou 'set null' se preferir manter o membro se a comissão for deletada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_members', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna
            $table->dropForeign(['commission_id']); // Nome da restrição pode variar, usar array é mais seguro
            $table->dropColumn('commission_id');
        });
    }
};
