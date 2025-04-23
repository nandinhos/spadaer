<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_members', function (Blueprint $table) {
            $table->id();
            // Chave estrangeira para o usuário principal
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Se o usuário for deletado, remove o membro da comissão
            // Campos específicos da comissão (opcional, mas útil)
            $table->string('role')->nullable(); // Ex: 'Presidente', 'Membro', 'Secretário'
            $table->date('start_date')->nullable(); // Data que entrou na comissão
            $table->boolean('is_active')->default(true); // Para poder inativar membros
            $table->timestamps();

            // Garante que um usuário só possa ser membro uma vez (se fizer sentido)
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_members');
    }
};
