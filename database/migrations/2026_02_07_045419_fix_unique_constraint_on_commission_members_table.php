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
            // Drop the foreign key first because the unique index is used by it
            $table->dropForeign(['user_id']);

            // Remove the unique constraint on user_id
            $table->dropUnique(['user_id']);

            // Add the new unique constraint on (user_id, commission_id)
            $table->unique(['user_id', 'commission_id']);

            // Re-add the foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_members', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'commission_id']);
            $table->unique(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
