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
   $query = Fasilitas::query();

if(request('search')){
    $search = request('search');

    $query->where(function($q) use ($search){
        $q->where('nama','like',"%$search%")
          ->orWhere('lokasi','like',"%$search%")
          ->orWhere('kategori','like',"%$search%")
          ->orWhere('detail','like',"%$search%");
    });
}

if(request('status')){
    $query->where('status', request('status'));
}

if(request('tanggal')){
    $query->whereDate('updated_at', request('tanggal'));
}
   
$fasilitasMap = Fasilitas::whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->get();
$fasilitas = $query->with([

    'photos',

    'histories' => function ($q) {
        $q->latest()->limit(5);
    },

    'histories.user'

])->latest()->paginate(10);

    // 🔥 TAMBAHAN UNTUK CHART
    $ready = Fasilitas::where('status','ready')->count();
    $maintenance = Fasilitas::where('status','maintenance')->count();
    $down = Fasilitas::where('status','down')->count();

       return view('dashboard', [
        'fasilitas' => $fasilitas,
        'fasilitasMap' => $fasilitasMap,
        'ready' => $ready,
        'maintenance' => $maintenance,
        'down' => $down
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

        'nama' => 'required',

        'lokasi' => 'required',

        'kategori' => 'required',

        'status' => 'required',

        'detail' => 'required',

        'latitude' => 'nullable',

        'longitude' => 'nullable',

        'foto.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'

    ]);

    // =========================
    // SIMPAN FASILITAS
    // =========================

    $fasilitas = Fasilitas::create([

        'nama' => $request->nama,

        'lokasi' => $request->lokasi,

        'kategori' => $request->kategori,

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
    public function dashboard()
    {
    return view('dashboard', [
        'ready' => Fasilitas::where('status','ready')->count(),
        'maintenance' => Fasilitas::where('status','maintenance')->count(),
        'down' => Fasilitas::where('status','down')->count(),
    ]);
    }

    // Delete
public function destroy($id)
{
    $data = Fasilitas::findOrFail($id);

    if ($data->foto) {
        Storage::disk('public')->delete($data->foto);
    }

    $data->delete();

    return redirect()->back()->with('success', 'Data berhasil dihapus');
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
    $fasilitas = Fasilitas::findOrFail($id);

    $oldStatus = $fasilitas->status;

    // upload foto baru
  // upload foto baru
$latestPhoto = null;

if($request->hasFile('foto')){

    foreach($request->file('foto') as $file){

        $path = $file->store(
            'history',
            'public'
        );

        $latestPhoto = $path;
    }

    // foto utama fasilitas
    $fasilitas->foto = $latestPhoto;
}

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