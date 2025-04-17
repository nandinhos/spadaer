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
        Schema::table('commissions', function (Blueprint $table) {
            $table->string('ordinance_number')->nullable()->after('status'); // Número da Portaria
            $table->string('ordinance_file')->nullable()->after('ordinance_number'); // Link para o arquivo PDF da Portaria
            $table->date('ordinance_date')->nullable()->after('ordinance_file'); // Data da Portaria
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropColumn(['ordinance_number', 'ordinance_file', 'ordinance_date']);
        });
    }
};