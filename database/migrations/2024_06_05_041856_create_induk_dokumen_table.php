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
        Schema::create('induk_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dokumen');
            $table->string('nama_dokumen');
            $table->date('tgl_upload');
            $table->text('file')->nullable();
            $table->text('file_pdf')->nullable();
            $table->text('active_doc')->nullable();
            $table->text('obsolete_doc')->nullable();
            $table->string('revisi_log')->nullable();
            $table->string('status')->nullable();
            $table->string('statusdoc')->nullable();
            $table->string('comment')->nullable();
            $table->string('tgl_efektif')->nullable();
            $table->string('tgl_obsolete')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('induk_dokumen');
    }
};
