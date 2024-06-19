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
        Schema::create('document_departement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('induk_dokumen_id')->constrained('induk_dokumen')->onDelete('cascade');
            $table->foreignId('departemen_id')->constrained('departemen')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_document_departement');
    }
};
