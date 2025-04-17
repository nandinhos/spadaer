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
            $table->string('document_number')->unique(); // numero (considerar unique?)
            $table->text('title'); // titulo
            $table->date('document_date'); // data
            $table->string('project')->nullable(); // projeto
            $table->enum('confidentiality', ['Público', 'Restrito', 'Confidencial'])->default('Público'); // sigilo
            $table->string('version')->nullable(); // versao
            $table->boolean('is_copy')->default(false); // copia
            // Adicione outras colunas se necessário (e.g., user_id, file_path)
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};