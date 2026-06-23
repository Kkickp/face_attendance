<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel mahasiswa sudah ada dari database.sql (Python backend)
        // Kita buat jika belum ada
        if (!Schema::hasTable('mahasiswa')) {
            Schema::create('mahasiswa', function (Blueprint $table) {
                $table->string('nim', 20)->primary();
                $table->string('nama_lengkap', 100);
                $table->longText('face_encoding');
                $table->timestamps();
            });
        }

        // Tabel presensi sudah ada, tambah kolom kelas_id dan sesi_id
        if (!Schema::hasTable('presensi')) {
            Schema::create('presensi', function (Blueprint $table) {
                $table->id();
                $table->string('nim', 20);
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
                $table->foreignId('sesi_id')->nullable()->constrained('sesi_presensi')->nullOnDelete();
                $table->timestamp('waktu_presensi')->useCurrent();
                $table->string('status', 50)->default('Hadir');
                $table->longText('foto_bukti')->nullable();
                $table->foreign('nim')->references('nim')->on('mahasiswa')->onDelete('cascade');
            });
        } else {
            // Tabel sudah ada, tambah kolom baru jika belum ada
            Schema::table('presensi', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi', 'kelas_id')) {
                    $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
                }
                if (!Schema::hasColumn('presensi', 'sesi_id')) {
                    $table->foreignId('sesi_id')->nullable()->constrained('sesi_presensi')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropForeign(['sesi_id']);
            $table->dropColumn(['kelas_id', 'sesi_id']);
        });
    }
};
