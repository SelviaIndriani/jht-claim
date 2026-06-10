<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_bpjs', function (Blueprint $table) {
            $table->id();
            $table->string('no_bpjs', 20)->unique();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('email')->nullable();
            $table->json('layanan_ids')->nullable(); // array of eligible service IDs
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_bpjs');
    }
};
