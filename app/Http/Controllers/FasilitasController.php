<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Illuminate\Http\Request;
use App\Models\FasilitasHistory;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; 
 use Illuminate\Support\Facades\Auth;
 use App\Models\HistoryPhoto;
 use App\Models\FasilitasPhoto;

class FasilitasController extends Controller
{
    // 1
    public function index(Request $request)
{
    if (Auth::user()->role !== 'admin') {
    abort(403, 'Unauthorized');
}
    /*
    |--------------------------------------------------------------------------
    | QUERY DATA FASILITAS
    |--------------------------------------------------------------------------
    */

    $query = Fasilitas::query();


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('nama', 'like', "%{$search}%")
                ->orWhere('lokasi', 'like', "%{$search}%")
                ->orWhere('kategori', 'like', "%{$search}%")
                ->orWhere('subkategori', 'like', "%{$search}%")
                ->orWhere('detail', 'like', "%{$search}%");

        });
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER STATUS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {

        $query->where('status', $request->status);

    }


    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('tanggal')) {

        $query->whereDate(
            'updated_at',
            $request->tanggal
        );

    }


    /*
    |--------------------------------------------------------------------------
    | DATA MAP
    |--------------------------------------------------------------------------
    */

    $fasilitasMap = Fasilitas::whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | DATA TABLE
    |--------------------------------------------------------------------------
    */

    $fasilitas = $query
        ->with([
            'photos',

            'histories' => function ($q) {
                $q->latest()->limit(5);
            },

            'histories.user'

        ])
        ->latest()
        ->paginate(10);


    /*
    |--------------------------------------------------------------------------
    | KATEGORI
    |--------------------------------------------------------------------------
    */

    $teknik = Fasilitas::where(
        'kategori',
        'Kesiapan Teknik'
    );

    $operasional = Fasilitas::where(
        'kategori',
        'Kesiapan Operasional'
    );

    $pendukung = Fasilitas::where(
        'kategori',
        'Perangkat Pendukung'
    );


    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

    $teknikTotal = (clone $teknik)->count();

    $operasionalTotal = (clone $operasional)->count();

    $pendukungTotal = (clone $pendukung)->count();


    /*
    |--------------------------------------------------------------------------
    | TEKNIK
    |--------------------------------------------------------------------------
    */

    $teknikReady = (clone $teknik)
        ->where('status', 'ready')
        ->count();

    $teknikMaintenance = (clone $teknik)
        ->where('status', 'maintenance')
        ->count();

    $teknikDown = (clone $teknik)
        ->where('status', 'down')
        ->count();


    /*
    |--------------------------------------------------------------------------
    | OPERASIONAL
    |--------------------------------------------------------------------------
    */

    $operasionalReady = (clone $operasional)
        ->where('status', 'ready')
        ->count();

    $operasionalMaintenance = (clone $operasional)
        ->where('status', 'maintenance')
        ->count();

    $operasionalDown = (clone $operasional)
        ->where('status', 'down')
        ->count();


    /*
    |--------------------------------------------------------------------------
    | PERANGKAT PENDUKUNG
    |--------------------------------------------------------------------------
    */

    $pendukungReady = (clone $pendukung)
        ->where('status', 'ready')
        ->count();

    $pendukungMaintenance = (clone $pendukung)
        ->where('status', 'maintenance')
        ->count();

    $pendukungDown = (clone $pendukung)
        ->where('status', 'down')
        ->count();


    /*
    |--------------------------------------------------------------------------
    | READINESS PERCENTAGE
    |--------------------------------------------------------------------------
    */

    $teknikReadiness = $teknikTotal > 0
        ? round(($teknikReady / $teknikTotal) * 100)
        : 0;


    $operasionalReadiness = $operasionalTotal > 0
        ? round(($operasionalReady / $operasionalTotal) * 100)
        : 0;


    $pendukungReadiness = $pendukungTotal > 0
        ? round(($pendukungReady / $pendukungTotal) * 100)
        : 0;


    /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA FASILITAS
    |--------------------------------------------------------------------------
    */

    $totalFasilitas = Fasilitas::count();

    $ready = Fasilitas::where(
        'status',
        'ready'
    )->count();

    $maintenance = Fasilitas::where(
        'status',
        'maintenance'
    )->count();

    $down = Fasilitas::where(
        'status',
        'down'
    )->count();


    /*
    |--------------------------------------------------------------------------
    | ALERT
    |--------------------------------------------------------------------------
    */

    $alerts = Fasilitas::whereIn(
        'status',
        ['down', 'maintenance']
    )
        ->latest('updated_at')
        ->take(5)
        ->get();


    $alertCount = Fasilitas::whereIn(
        'status',
        ['down', 'maintenance']
    )->count();


    /*
    |--------------------------------------------------------------------------
    | RETURN DASHBOARD
    |--------------------------------------------------------------------------
    */

    return view('dashboard', [

        // Data utama
        'fasilitas' => $fasilitas,
        'fasilitasMap' => $fasilitasMap,

        // Semua fasilitas
        'totalFasilitas' => $totalFasilitas,
        'ready' => $ready,
        'maintenance' => $maintenance,
        'down' => $down,

        // Teknik
        'teknikTotal' => $teknikTotal,
        'teknikReady' => $teknikReady,
        'teknikMaintenance' => $teknikMaintenance,
        'teknikDown' => $teknikDown,
        'teknikReadiness' => $teknikReadiness,

        // Operasional
        'operasionalTotal' => $operasionalTotal,
        'operasionalReady' => $operasionalReady,
        'operasionalMaintenance' => $operasionalMaintenance,
        'operasionalDown' => $operasionalDown,
        'operasionalReadiness' => $operasionalReadiness,

        // Perangkat Pendukung
        'pendukungTotal' => $pendukungTotal,
        'pendukungReady' => $pendukungReady,
        'pendukungMaintenance' => $pendukungMaintenance,
        'pendukungDown' => $pendukungDown,
        'pendukungReadiness' => $pendukungReadiness,

        // Alert
        'alerts' => $alerts,
        'alertCount' => $alertCount,

    ]);
}

    // 2
    public function create()
    {
    return view('create');
    }

    public function store(Request $request)
{
    $request->validate([

        'nama' => 'required|string|max:255',
        'lokasi' => 'required|string|max:255',
        'kategori' => 'required|string|max:255',
        'subkategori' => 'required|string|max:255',

        'status' => 'required|in:ready,maintenance,down',

       'detail' => 'required|string|max:1000',

        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',

        'foto.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'

    ]);

    // =========================
    // SIMPAN FASILITAS
    // =========================

    $fasilitas = Fasilitas::create([

        'nama' => $request->nama,

        'lokasi' => $request->lokasi,

        'kategori' => $request->kategori,
        'subkategori' => $request->subkategori,
        'status' => $request->status,

        'detail' => $request->detail,
        'keterangan' => $request->keterangan,

        'latitude' => $request->latitude,

        'longitude' => $request->longitude,

    ]);

    // =========================
    // BUAT HISTORY AWAL
    // =========================

    $history = FasilitasHistory::create([

        'fasilitas_id' => $fasilitas->id,

        'user_id' => auth()->id(),

        'status_from' => null,

        'status_to' => $request->status,

          'keterangan' => $request->keterangan,

    ]);

    // =========================
    // UPLOAD FOTO
    // =========================

    if($request->hasFile('foto')){

        foreach($request->file('foto') as $index => $file){

            $path = $file->store(
                'fasilitas',
                'public'
            );

            // foto utama dashboard
            if($index == 0){

                $fasilitas->foto = $path;

                $fasilitas->save();
            }

            // simpan foto fasilitas
            FasilitasPhoto::create([

                'fasilitas_id' => $fasilitas->id,

                'foto' => $path

            ]);

            // simpan foto histori
            HistoryPhoto::create([

                'fasilitas_history_id' => $history->id,

                'foto' => $path

            ]);
        }
    }

    return redirect('/')
        ->with('success',
            'Data berhasil ditambahkan');
}
    // 3

public function updateStatus(Request $request, $id)
{
    // 🔐 VALIDASI
    $request->validate([
        'status' => 'required|in:ready,maintenance,down'
    ]);

    // 🔐 CEK ROLE
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    $fasilitas = Fasilitas::findOrFail($id);

    $oldStatus = $fasilitas->status;
    $newStatus = $request->status;

    // ❌ kalau sama, tidak usah simpan
    if ($oldStatus === $newStatus) {
        return response()->json(['success' => true]);
    }

    // update
    $fasilitas->update([
        'status' => $newStatus
    ]);

    // simpan histori
    FasilitasHistory::create([
        'fasilitas_id' => $fasilitas->id,
        'user_id' => Auth::id(),
        'status_from' => $oldStatus,
        'status_to' => $newStatus
    ]);

    return response()->json(['success' => true]);
}

    // 4
    // public function dashboard()
    // {
    // return view('dashboard', [
    //     'ready' => Fasilitas::where('status','ready')->count(),
    //     'maintenance' => Fasilitas::where('status','maintenance')->count(),
    //     'down' => Fasilitas::where('status','down')->count(),
    // ]);
    // }

    // Delete
public function destroy($id)
{
    $data = Fasilitas::with([
        'photos',
        'histories.photos'
    ])->findOrFail($id);

    $deletedFiles = [];

    // Hapus foto utama
    if (
        $data->foto &&
        Storage::disk('public')->exists($data->foto)
    ) {
        Storage::disk('public')->delete($data->foto);

        $deletedFiles[] = $data->foto;
    }

    // Hapus foto fasilitas
    foreach ($data->photos as $photo) {

        if (
            $photo->foto &&
            Storage::disk('public')->exists($photo->foto) &&
            !in_array($photo->foto, $deletedFiles)
        ) {
            Storage::disk('public')->delete($photo->foto);

            $deletedFiles[] = $photo->foto;
        }

        $photo->delete();
    }

    // Hapus foto histori
    foreach ($data->histories as $history) {

        foreach ($history->photos as $photo) {

            if (
                $photo->foto &&
                Storage::disk('public')->exists($photo->foto) &&
                !in_array($photo->foto, $deletedFiles)
            ) {
                Storage::disk('public')->delete($photo->foto);

                $deletedFiles[] = $photo->foto;
            }

            $photo->delete();
        }

        $history->delete();
    }

    $data->delete();

    return redirect()
        ->back()
        ->with('success', 'Data berhasil dihapus');
}
// PDF EXPORT YEE
public function exportPdf()
{
    $query = Fasilitas::query();

    if(request('search')){
        $query->where('nama','like','%'.request('search').'%');
    }

    if(request('status')){
        $query->where('status', request('status'));
    }

    if(request('tanggal')){
        $query->whereDate('updated_at', request('tanggal'));
    }

    $data = $query->get();

    $pdf = Pdf::loadView('pdf.fasilitas', compact('data'));

    return $pdf->download('laporan-fasilitas.pdf');
}

public function history($id)
{
    $histories = FasilitasHistory::with([
        'user',
        'photos'
    ])
    ->where('fasilitas_id', $id)
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

    foreach ($histories as $index => $history) {

        $next = $histories[$index + 1] ?? null;

        $history->previous_update =
            $next ? $next->created_at : null;
    }

    return response()->json($histories);
}

public function updateData(Request $request, $id)
{
    $request->validate([

    'status' => 'required|in:ready,maintenance,down',

    'keterangan' => 'nullable|string|max:1000',

    'foto.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'

]);

  // BATASI JUMLAH FOTO
    if(
        $request->hasFile('foto') &&
        count($request->file('foto')) > 5
    ){
        return response()->json([
            'success' => false,
            'message' => 'Maksimal 5 foto'
        ], 422);
    }

    $fasilitas = Fasilitas::findOrFail($id);

    $oldStatus = $fasilitas->status;


    // update data
    $fasilitas->status = $request->status;

    $fasilitas->keterangan = $request->keterangan;

    $fasilitas->updated_by = auth()->id();

    $fasilitas->save();

    // simpan histori
    $history = FasilitasHistory::create([

    'fasilitas_id' => $fasilitas->id,

    'user_id' => auth()->id(),

    'status_from' => $oldStatus,

    'status_to' => $request->status,

    'keterangan' => $request->keterangan,
    
]);
// upload foto update
if($request->hasFile('foto')){

    foreach($request->file('foto') as $index => $file){

        $path = $file->store(
            'history',
            'public'
        );

        // FOTO TERBARU DASHBOARD
        if($index == 0){

            $fasilitas->foto = $path;

            $fasilitas->save();
        }

        // FOTO HISTORI
        HistoryPhoto::create([

            'fasilitas_history_id' => $history->id,

            'foto' => $path

        ]);
    }
}

    return response()->json([
        'success' => true
    ]);
}
public function historyPdf($id)
{
    $fasilitas = Fasilitas::findOrFail($id);

    $histories = FasilitasHistory::with('user')
    ->where('fasilitas_id', $id)
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($histories as $index => $history) {

    $next = $histories[$index + 1] ?? null;

    $history->previous_update =
        $next ? $next->created_at : null;
}

    $pdf = Pdf::loadView(
        'pdf.history',
        compact('fasilitas', 'histories')
    );

    return $pdf->download(
        'history-'.$fasilitas->nama.'.pdf'
    );
}
}