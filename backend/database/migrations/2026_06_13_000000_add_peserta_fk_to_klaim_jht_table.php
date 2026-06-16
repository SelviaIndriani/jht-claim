<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('klaim_jht', function (Blueprint $table) {
            // Add peserta_id column if it doesn't exist
            if (!Schema::hasColumn('klaim_jht', 'peserta_id')) {
                $table->unsignedBigInteger('peserta_id')->nullable()->after('id');
            }

            // Add foreign key constraint
            $table->foreign('peserta_id')
                ->references('id')
                ->on('peserta_bpjs')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Add indexes if they don't exist
            if (!Schema::hasIndex('klaim_jht', 'klaim_jht_peserta_id_foreign')) {
                $table->index('peserta_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('klaim_jht', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['peserta_id']);

            // Drop column
            $table->dropColumn(['peserta_id']);
        });
    }
};
