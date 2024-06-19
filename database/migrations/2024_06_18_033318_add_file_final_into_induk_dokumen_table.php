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
        Schema::table('induk_dokumen', function (Blueprint $table) {
            $table->string('file_final')->after('file_draft')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('induk_dokumen', function (Blueprint $table) {
            // Drop the departemen_id column
            $table->dropColumn('file_final');
        });
    }
};
