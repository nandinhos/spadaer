<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('box_number'); // caixa
            $table->string('item_number'); // item
            $table->string('code')->nullable(); // codigo
            $table->string('descriptor')->nullable(); // descritor
            $table->string('document_number'); // numero
            $table->text('title'); // titulo
            $table->string('document_date')->nullable(); // data (alterado para string)
            $table->string('project')->nullable(); // projeto
            $table->string('confidentiality')->nullable(); // sigilo (alterado para string)
            $table->string('version')->nullable(); // versao
            $table->string('is_copy')->nullable(); // copia (alterado para string)
            // Adicione outras colunas se necessário (e.g., user_id, file_path)
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};