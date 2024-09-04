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
        Schema::create('document_audit_control', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_control_id');
            $table->string('attachment')->nullable();
            $table->timestamps();
            $table->foreign('audit_control_id')->references('id')->on('audit_control')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_audit_control');
    }
};
