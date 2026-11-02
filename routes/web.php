<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\DonorEventController;
use App\Http\Controllers\BloodStockController;
use App\Http\Controllers\HospitalRequestController;
use App\Http\Controllers\ReportNotificationController;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    // 1. Dashboard: Ringkasan stok dan aktivitas
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Data Donor: Profil, riwayat & Aksi admin: Verifikasi
    Route::get('donors', [DonorController::class, 'index'])->name('donors.index');
    Route::post('donors', [DonorController::class, 'store'])->name('donors.store');
    Route::put('donors/{donor}', [DonorController::class, 'update'])->name('donors.update');
    Route::post('donors/{donor}/verify', [DonorController::class, 'verify'])->name('donors.verify');
    Route::delete('donors/{donor}', [DonorController::class, 'destroy'])->name('donors.destroy');

    // 3. Jadwal Donor: Event, kuota & Aksi admin: Absensi donor
    Route::get('events', [DonorEventController::class, 'index'])->name('events.index');
    Route::post('events', [DonorEventController::class, 'store'])->name('events.store');
    Route::get('events/{event}', [DonorEventController::class, 'show'])->name('events.show');
    Route::put('events/{event}', [DonorEventController::class, 'update'])->name('events.update');
    Route::post('events/{event}/participants', [DonorEventController::class, 'addParticipant'])->name('events.participants.store');
    Route::post('events/attendances/{attendance}', [DonorEventController::class, 'recordAttendance'])->name('events.attendance.record');

    // 4. Stok Darah: Per golongan & Aksi admin: Update stok
    Route::get('stocks', [BloodStockController::class, 'index'])->name('stocks.index');
    Route::post('stocks/update', [BloodStockController::class, 'updateStock'])->name('stocks.update');

    // 5. Permintaan: Rumah sakit & Aksi admin: Setujui / tolak
    Route::get('requests', [HospitalRequestController::class, 'index'])->name('requests.index');
    Route::post('requests', [HospitalRequestController::class, 'store'])->name('requests.store');
    Route::post('requests/{hospitalRequest}/approve', [HospitalRequestController::class, 'approve'])->name('requests.approve');
    Route::post('requests/{hospitalRequest}/reject', [HospitalRequestController::class, 'reject'])->name('requests.reject');
    Route::post('requests/{hospitalRequest}/complete', [HospitalRequestController::class, 'complete'])->name('requests.complete');

    // 6. Laporan dan notifikasi: Ekspor data, kirim pengingat
    Route::get('reports', [ReportNotificationController::class, 'index'])->name('reports.index');
    Route::post('reports/send-reminder', [ReportNotificationController::class, 'sendReminder'])->name('reports.send-reminder');
    Route::post('reports/broadcast', [ReportNotificationController::class, 'broadcastReminder'])->name('reports.broadcast');
    Route::get('reports/export-csv', [ReportNotificationController::class, 'exportCsv'])->name('reports.export-csv');
});

require __DIR__.'/settings.php';
