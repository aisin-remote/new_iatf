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
        Schema::create('audit_control', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumenaudit_id')->constrained('document_audit')->onDelete('cascade');
            $table->foreignId('audit_id')->constrained('audit')->onDelete('cascade');
            $table->text('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_control');
    }
};
