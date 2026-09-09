<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FasilitasController;
use App\Models\Fasilitas;
use App\Exports\HistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditLogController;

// redirect root ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Audit Log - Admin only
Route::get('/audit-logs', [AuditLogController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('audit.logs');

// group auth
Route::middleware('auth')->group(function () {

    // =========================================================
    // PROFILE
    // =========================================================

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    // =========================================================
    // FASILITAS - ADMIN + TEKNISI
    // =========================================================

    // Tambah fasilitas
    Route::get('/fasilitas/create', [FasilitasController::class, 'create']);

    Route::post('/fasilitas/store', [FasilitasController::class, 'store']);


    // =========================================================
    // FASILITAS - ADMIN ONLY
    // =========================================================

    Route::middleware('admin')->group(function () {

        // Hapus fasilitas
        Route::delete('/fasilitas/{id}', [FasilitasController::class, 'destroy']);

        // Hapus foto
        Route::delete('/fasilitas-photo/{id}', [FasilitasController::class, 'deletePhoto'])
            ->name('fasilitas.photo.delete');
    });


    // =========================================================
    // PERANGKAT PENDUKUNG
    // =========================================================

    Route::get('/perangkat-pendukung', [FasilitasController::class, 'perangkatPendukung'])
        ->name('perangkat.pendukung');
});

// KESIAPAN TEKNIK
Route::get('/kesiapan-teknik', [FasilitasController::class, 'kesiapanTeknik'])
    ->name('kesiapan.teknik');

    // KESIAPAN OPERASIONAL
Route::get('/kesiapan-operasional', [FasilitasController::class, 'kesiapanOperasional'])
    ->name('kesiapan.operasional');

// =============================================================
// AUTH LOGIN / REGISTER BREEZE
// =============================================================

require __DIR__.'/auth.php';


// =============================================================
// CHART DATA
// =============================================================

Route::get('/chart-data', function () {

    return response()->json([
        'ready' => Fasilitas::where('status', 'ready')->count(),
        'maintenance' => Fasilitas::where('status', 'maintenance')->count(),
        'down' => Fasilitas::where('status', 'down')->count(),
    ]);

})->middleware('auth');


// =============================================================
// EXPORT PDF FASILITAS
// =============================================================

Route::get('/fasilitas/export-pdf', [FasilitasController::class, 'exportPdf'])
    ->middleware('auth');


// =============================================================
// DETAIL FASILITAS
// =============================================================

Route::get('/fasilitas/{id}', [FasilitasController::class, 'show'])
    ->middleware('auth')
    ->name('fasilitas.show');


// =============================================================
// EDIT DATA MASTER - ADMIN ONLY
// =============================================================

Route::get('/fasilitas/{id}/edit', [FasilitasController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('fasilitas.edit');

Route::put('/fasilitas/{id}', [FasilitasController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('fasilitas.update');


// =============================================================
// HISTORI FASILITAS
// =============================================================

Route::middleware('auth')->get(
    '/fasilitas/{id}/histories',
    [FasilitasController::class, 'history']
);


// =============================================================
// UPDATE KONDISI - ADMIN + TEKNISI
// =============================================================

Route::post(
    '/fasilitas/update/{id}',
    [FasilitasController::class, 'updateData']
)->middleware('auth');


// =============================================================
// PDF HISTORI
// =============================================================

Route::get(
    '/fasilitas/{id}/history-pdf',
    [FasilitasController::class, 'historyPdf']
)->middleware('auth');


// =============================================================
// EXPORT EXCEL HISTORI
// =============================================================

Route::get('/export-history', function () {

    $date = explode('-', request('bulan_export'));

    $tahun = $date[0];
    $bulan = $date[1];

    return Excel::download(
        new HistoryExport(
            $bulan,
            $tahun,
            request('search')
        ),
        'history-'.$bulan.'-'.$tahun.'.xlsx'
    );

})->middleware('auth');


// =============================================================
// DASHBOARD - ADMIN + TEKNISI
// =============================================================

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');