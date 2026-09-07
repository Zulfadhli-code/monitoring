<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FasilitasController;
use App\Models\Fasilitas;
 use App\Exports\HistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\DashboardController;

// redirect root ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// dashboard
// Route::get('/dashboard', [FasilitasController::class, 'index'])
//     ->middleware(['auth', 'admin'])
//     ->name('dashboard');

// group auth
Route::middleware('auth')->group(function () {

    // profile (WAJIB untuk Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // fasilitas
    Route::get('/fasilitas/create', [FasilitasController::class, 'create']);
    Route::post('/fasilitas/store', [FasilitasController::class, 'store']);
    Route::post('/fasilitas/update-status/{id}', [FasilitasController::class, 'updateStatus']);
    Route::delete('/fasilitas/{id}', [FasilitasController::class, 'destroy']);
});

// auth login/register
require __DIR__.'/auth.php';

Route::get('/chart-data', function () {
    return response()->json([
        'ready' => Fasilitas::where('status', 'ready')->count(),
        'maintenance' => Fasilitas::where('status', 'maintenance')->count(),
        'down' => Fasilitas::where('status', 'down')->count(),
    ]);
})->middleware('auth');

Route::get('/fasilitas/export-pdf', [FasilitasController::class, 'exportPdf'])
    ->middleware('auth');

    // Histori alat
Route::middleware('auth')->get(
    '/fasilitas/{id}/histories',
    [FasilitasController::class, 'history']
);

Route::post('/fasilitas/update/{id}',
    [FasilitasController::class, 'updateData']);

    // PDF histori
    Route::get('/fasilitas/{id}/history-pdf',
    [FasilitasController::class, 'historyPdf']);

    // Export Excel histori
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
});


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');