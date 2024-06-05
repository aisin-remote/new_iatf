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
            $table->unsignedBigInteger('rule_id')->after('dokumen_id')->nullable();

            // Add the foreign key constraint
            $table->foreign('rule_id')->references('id')->on('rule')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('induk_dokumen', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['rule_id']);

            // Drop the rule_id column
            $table->dropColumn('rule_id');
        });
    }
};
