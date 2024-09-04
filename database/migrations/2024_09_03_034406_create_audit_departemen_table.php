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
            $table->unsignedBigInteger('departemen_id');
            $table->unsignedBigInteger('item_audit_id');
            $table->string('status')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('departemen_id')->references('id')->on('departemen')->onDelete('cascade');
            $table->foreign('item_audit_id')->references('id')->on('item_audit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_departemen');
    }
};
