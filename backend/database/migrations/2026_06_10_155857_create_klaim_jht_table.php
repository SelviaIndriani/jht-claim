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
        Schema::create('klaim_jht', function (Blueprint $table) {
            $table->string('no_klaim', 20)->unique()->nullable()->index();
            $table->string('no_bpjs', 20)->nullable()->index();
            $table->string('nik', 16)->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('email')->nullable();
            $table->enum('sebab_klaim', ['mengundurkan_diri', 'berakhir_kontrak'])->nullable();
            $table->json('layanan_dipilih')->nullable();    // array of selected service IDs
            $table->enum('cara_konfirmasi', ['video_call', 'datang_kantor'])->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('pas_foto')->nullable();
            $table->enum('status', ['pending', 'diproses', 'disetujui', 'ditolak'])->default('pending');
            $table->unsignedBigInteger('kantor_cabang_id')->nullable()->index();
            $table->timestamp('submitted_at')->nullable();
            $table->softDeletes()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klaim_jht');
    }
};
