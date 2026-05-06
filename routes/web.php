<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FasilitasController;
use App\Models\Fasilitas;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/dashboard', [FasilitasController::class, 'index'])
//     ->middleware('auth');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';


// redirect root ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// dashboard
Route::get('/dashboard', [FasilitasController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

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