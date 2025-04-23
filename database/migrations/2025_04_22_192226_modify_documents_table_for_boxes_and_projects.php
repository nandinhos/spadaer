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
        Schema::table('documents', function (Blueprint $table) {
            // Adiciona novas colunas de chave estrangeira (depois de 'id' ou onde preferir)
            $table->foreignId('box_id')->nullable()->after('id')->constrained('boxes')->onDelete('cascade'); // Se deletar a caixa, deleta os docs? Ou set null? Cascade é comum.
            $table->foreignId('project_id')->nullable()->after('box_id')->constrained('projects')->onDelete('set null');

            // Remove as colunas antigas se elas existirem
            if (Schema::hasColumn('documents', 'box_number')) {
                $table->dropColumn('box_number');
            }
            if (Schema::hasColumn('documents', 'project')) {
                $table->dropColumn('project');
            }
            // Poderia remover item_number aqui também se ele for para a tabela boxes, mas mantemos por enquanto.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
