<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $teknik = Fasilitas::where('kategori', 'Kesiapan Teknik');
        $operasional = Fasilitas::where('kategori', 'Kesiapan Operasional');
        $pendukung = Fasilitas::where('kategori', 'Perangkat Pendukung');

        $teknikTotal = (clone $teknik)->count();
        $operasionalTotal = (clone $operasional)->count();
        $pendukungTotal = (clone $pendukung)->count();

        $teknikReady = (clone $teknik)->where('status', 'ready')->count();
        $teknikMaintenance = (clone $teknik)->where('status', 'maintenance')->count();
        $teknikDown = (clone $teknik)->where('status', 'down')->count();

        $operasionalReady = (clone $operasional)->where('status', 'ready')->count();
        $operasionalMaintenance = (clone $operasional)->where('status', 'maintenance')->count();
        $operasionalDown = (clone $operasional)->where('status', 'down')->count();

        $pendukungReady = (clone $pendukung)->where('status', 'ready')->count();
        $pendukungMaintenance = (clone $pendukung)->where('status', 'maintenance')->count();
        $pendukungDown = (clone $pendukung)->where('status', 'down')->count();

        $teknikReadiness = $teknikTotal > 0
            ? round(($teknikReady / $teknikTotal) * 100)
            : 0;

        $operasionalReadiness = $operasionalTotal > 0
            ? round(($operasionalReady / $operasionalTotal) * 100)
            : 0;

        $pendukungReadiness = $pendukungTotal > 0
            ? round(($pendukungReady / $pendukungTotal) * 100)
            : 0;

        $totalFasilitas = Fasilitas::count();

        $ready = Fasilitas::where('status', 'ready')->count();
        $maintenance = Fasilitas::where('status', 'maintenance')->count();
        $down = Fasilitas::where('status', 'down')->count();

        $alerts = Fasilitas::whereIn('status', ['down', 'maintenance'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $alertCount = Fasilitas::whereIn(
            'status',
            ['down', 'maintenance']
        )->count();

        $fasilitasMap = Fasilitas::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
        $fasilitas = Fasilitas::latest()->paginate(10);

        return view('dashboard', [
            'fasilitas' => $fasilitas,
            'fasilitasMap' => $fasilitasMap,

            'totalFasilitas' => $totalFasilitas,
            'ready' => $ready,
            'maintenance' => $maintenance,
            'down' => $down,

            'teknikTotal' => $teknikTotal,
            'teknikReady' => $teknikReady,
            'teknikMaintenance' => $teknikMaintenance,
            'teknikDown' => $teknikDown,
            'teknikReadiness' => $teknikReadiness,

            'operasionalTotal' => $operasionalTotal,
            'operasionalReady' => $operasionalReady,
            'operasionalMaintenance' => $operasionalMaintenance,
            'operasionalDown' => $operasionalDown,
            'operasionalReadiness' => $operasionalReadiness,

            'pendukungTotal' => $pendukungTotal,
            'pendukungReady' => $pendukungReady,
            'pendukungMaintenance' => $pendukungMaintenance,
            'pendukungDown' => $pendukungDown,
            'pendukungReadiness' => $pendukungReadiness,

            'alerts' => $alerts,
            'alertCount' => $alertCount,
        ]);
    }
}