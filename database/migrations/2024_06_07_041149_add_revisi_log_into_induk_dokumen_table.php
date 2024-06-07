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
            $table->string('revisi_log')->after('tgl_upload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('induk_dokumen', function (Blueprint $table) {
            // Drop the departemen_id column
            $table->dropColumn('revisi_log');
        });
    }
};
