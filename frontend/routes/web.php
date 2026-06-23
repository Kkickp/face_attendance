<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SesiPresensiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Publik (Tanpa Login) ──────────────────────────────────────────
Route::get('/', [PresensiController::class, 'index'])->name('presensi.index');
Route::post('/presensi/proses', [PresensiController::class, 'proses'])->name('presensi.proses');

// ─── Auth Routes (Breeze) ───────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─── Admin Routes (Perlu Login) ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mahasiswa
    Route::resource('mahasiswa', MahasiswaController::class);

    // Mata Kuliah
    Route::resource('mata-kuliah', MataKuliahController::class);

    // Kelas (Laravel pluralizes 'kelas' oddly, so we use explicit binding)
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kela']);

    // Sesi Presensi
    Route::get('/sesi', [SesiPresensiController::class, 'index'])->name('sesi.index');
    Route::post('/sesi/buka', [SesiPresensiController::class, 'buka'])->name('sesi.buka');
    Route::patch('/sesi/{sesi}/tutup', [SesiPresensiController::class, 'tutup'])->name('sesi.tutup');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');

    // Rekap Kehadiran
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export-pdf', [RekapController::class, 'exportPdf'])->name('rekap.pdf');
    Route::get('/rekap/export-excel', [RekapController::class, 'exportExcel'])->name('rekap.excel');

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.markAllRead');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'unreadCount'])->name('notifikasi.unreadCount');

    // Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
});
