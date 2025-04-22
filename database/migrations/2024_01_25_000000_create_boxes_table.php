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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();  // Número único da caixa
            $table->string('project');          // Nome do projeto
            $table->string('year_range');       // Período (ex: 2010-2011)
            $table->year('current_archive');    // Ano do arquivo corrente
            $table->year('intermediate_archive'); // Ano do arquivo intermediário
            $table->string('final_destination');  // Destinação final
            $table->timestamps();
        });

        // Adiciona a chave estrangeira na tabela documents
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('box_number')
                  ->references('number')
                  ->on('boxes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove a chave estrangeira primeiro
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['box_number']);
        });

        Schema::dropIfExists('boxes');
    }
};