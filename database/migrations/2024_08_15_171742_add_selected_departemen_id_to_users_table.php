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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('selected_departemen_id')->nullable()->after('password');
    
            // Tambahkan foreign key constraint jika perlu
            $table->foreign('selected_departemen_id')
                  ->references('id')
                  ->on('departemen')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['selected_departemen_id']);
            $table->dropColumn('selected_departemen_id');
        });
    }
};
