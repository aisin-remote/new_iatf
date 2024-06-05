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

            // Add the user_id column
            $table->unsignedBigInteger('dokumen_id')->after('user_id')->nullable();

            // Add the foreign key constraint
            $table->foreign('dokumen_id')->references('id')->on('dokumen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('induk_dokumen', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['dokumen_id']);

            // Drop the dokumen_id column
            $table->dropColumn('dokumen_id');
        });
    }
};
