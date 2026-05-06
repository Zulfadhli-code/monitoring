<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Illuminate\Http\Request;
use App\Models\FasilitasHistory;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; 
 use Illuminate\Support\Facades\Auth;

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
    $validated = $request->validate([
        'nama' => 'required',
        'lokasi' => 'required',
        'kategori' => 'required',
        'status' => 'required',
        'detail' => 'required',
         'latitude' => 'nullable',
    'longitude' => 'nullable',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('fasilitas', 'public');
        $validated['foto'] = $path;
    }

    Fasilitas::create($validated);

    return redirect('/')->with('success', 'Data berhasil ditambahkan');
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
    $histories = \App\Models\FasilitasHistory::with('user')
        ->where('fasilitas_id', $id)
        ->latest()
        ->limit(20)
        ->get();

    return response()->json($histories);
}
}