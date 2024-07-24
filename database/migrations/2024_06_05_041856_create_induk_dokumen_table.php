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
            $table->text('file');
            $table->text('file_pdf');
            $table->string('revisi_log');
            $table->string('status');
            $table->string('statusdoc');
            $table->string('comment');
            $table->string('tgl_efektif');
            $table->string('tgl_obsolete');
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
