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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nome único da permissão (ex: 'create-user')
            $table->string('display_name')->nullable(); // Nome de exibição (ex: 'Criar Usuário')
            $table->text('description')->nullable(); // Descrição da permissão
            $table->string('group')->nullable(); // Grupo da permissão (ex: 'users', 'posts')
            $table->boolean('is_active')->default(true); // Status da permissão
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};