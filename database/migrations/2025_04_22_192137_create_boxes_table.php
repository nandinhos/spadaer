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
        // A ÚNICA COISA AQUI DENTRO DEVE SER Schema::create('boxes', ...)
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('physical_location')->nullable();

            // Chave estrangeira para projects (se já existir a tabela projects)
            // Verifique se a migration de projects vem ANTES desta
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');

            // Chave estrangeira para commission_members (se já existir a tabela)
            // Verifique se a migration de commission_members vem ANTES desta
            // E se o nome da coluna está correto (checker_member_id)
            $table->foreignId('checker_member_id')->nullable()->constrained('commission_members')->onDelete('set null');

            $table->date('conference_date')->nullable();
            $table->timestamps();

            // !!!!! NÃO DEVE HAVER NENHUMA LINHA AQUI TENTANDO ALTERAR A TABELA 'documents' !!!!!
            // !!!!! Exemplo do que REMOVER: $table->foreign('box_number')... !!!!!
            // !!!!! Exemplo do que REMOVER: Schema::table('documents', ... !!!!!
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
