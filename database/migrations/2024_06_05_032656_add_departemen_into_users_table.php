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

            // Add the user_id column
            $table->unsignedBigInteger('departemen_id')->after('npk')->nullable();

            // Add the foreign key constraint
            $table->foreign('departemen_id')->references('id')->on('departemen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['departemen_id']);

            // Drop the departemen_id column
            $table->dropColumn('departemen_id');
        });
    }
};
