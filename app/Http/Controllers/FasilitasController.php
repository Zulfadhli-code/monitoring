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
 use App\Models\AuditLog;
 

class FasilitasController extends Controller
{


    // 2
    public function create()
    {
    return view('create');
    }
    public function perangkatPendukung()
{
    $query = Fasilitas::where('kategori', 'Perangkat Pendukung');

    $total = (clone $query)->count();

    $ready = (clone $query)
        ->where('status', 'ready')
        ->count();

    $maintenance = (clone $query)
        ->where('status', 'maintenance')
        ->count();

    $down = (clone $query)
        ->where('status', 'down')
        ->count();

    $fasilitas = (clone $query)
        ->latest()
        ->paginate(10);

    return view('perangkat-pendukung', compact(
        'fasilitas',
        'total',
        'ready',
        'maintenance',
        'down'
    ));
}

public function kesiapanTeknik()
{
    $query = Fasilitas::where('kategori', 'Kesiapan Teknik');

    $total = (clone $query)->count();

    $ready = (clone $query)
        ->where('status', 'ready')
        ->count();

    $maintenance = (clone $query)
        ->where('status', 'maintenance')
        ->count();

    $down = (clone $query)
        ->where('status', 'down')
        ->count();

    $fasilitas = (clone $query)
        ->latest()
        ->paginate(10);

    return view('kesiapan-teknik', compact(
        'fasilitas',
        'total',
        'ready',
        'maintenance',
        'down'
    ));
}

public function kesiapanOperasional()
{
    $query = Fasilitas::where('kategori', 'Kesiapan Operasional');

    $total = (clone $query)->count();

    $ready = (clone $query)
        ->where('status', 'ready')
        ->count();

    $maintenance = (clone $query)
        ->where('status', 'maintenance')
        ->count();

    $down = (clone $query)
        ->where('status', 'down')
        ->count();

    $fasilitas = (clone $query)
        ->latest()
        ->paginate(10);

    return view('kesiapan-operasional', compact(
        'fasilitas',
        'total',
        'ready',
        'maintenance',
        'down'
    ));
}

    public function store(Request $request)
{
    $request->validate([

        'nama' => 'required|string|max:255',
        'lokasi' => 'required|string|max:255',
        'kategori' => 'required|in:Kesiapan Teknik,Kesiapan Operasional,Perangkat Pendukung',
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
    $oldKeterangan = $fasilitas->keterangan;


    // update data
    $fasilitas->status = $request->status;

    $fasilitas->keterangan = $request->keterangan;

    $fasilitas->updated_by = auth()->id();

    $fasilitas->save();

    // audit perubahan status
if ($oldStatus !== $request->status) {
    AuditLog::create([
        'user_id' => auth()->id(),
        'fasilitas_id' => $fasilitas->id,
        'field' => 'status',
        'old_value' => $oldStatus,
        'new_value' => $request->status,
    ]);
}
if ($oldKeterangan !== $request->keterangan) {
    AuditLog::create([
        'user_id' => auth()->id(),
        'fasilitas_id' => $fasilitas->id,
        'field' => 'keterangan',
        'old_value' => $oldKeterangan,
        'new_value' => $request->keterangan,
    ]);
}


    // simpan histori hanya jika status berubah
// simpan histori hanya jika status berubah
$history = null;

if ($oldStatus !== $request->status) {

    $history = FasilitasHistory::create([

        'fasilitas_id' => $fasilitas->id,

        'user_id' => auth()->id(),

        'status_from' => $oldStatus,

        'status_to' => $request->status,

        'keterangan' => $request->keterangan,

    ]);
}
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
        // SIMPAN FOTO
if ($history) {

    // Jika status berubah, foto masuk ke histori
    HistoryPhoto::create([
        'fasilitas_history_id' => $history->id,
        'foto' => $path
    ]);

} else {

    // Jika status tidak berubah, foto tetap disimpan
    // sebagai foto fasilitas
    FasilitasPhoto::create([
        'fasilitas_id' => $fasilitas->id,
        'foto' => $path
    ]);
}
    }
}

    return response()->json([
        'success' => true
    ]);
}

public function edit($id)
{
    $fasilitas = Fasilitas::with('photos')
        ->findOrFail($id);

    return view('fasilitas-edit', compact('fasilitas'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'lokasi' => 'required|string|max:255',
        'kategori' => 'required|string|max:255',
        'subkategori' => 'required|string|max:255',
        'status' => 'required|in:ready,maintenance,down',
        'detail' => 'required|string|max:1000',
        'keterangan' => 'nullable|string|max:1000',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'foto.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    ]);

    $fasilitas = Fasilitas::findOrFail($id);

    // Simpan status lama sebelum dilakukan perubahan
    $oldStatus = $fasilitas->status;

    $fields = [
        'nama',
        'lokasi',
        'kategori',
        'subkategori',
        'status',
        'detail',
        'keterangan',
        'latitude',
        'longitude',
    ];

    foreach ($fields as $field) {

        $oldValue = $fasilitas->{$field};
        $newValue = $request->input($field);

        // Hanya catat jika nilai benar-benar berubah
        if ((string) $oldValue !== (string) $newValue) {

            AuditLog::create([
                'user_id' => auth()->id(),
                'fasilitas_id' => $fasilitas->id,
                'field' => $field,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]);

            $fasilitas->{$field} = $newValue;
        }
    }

    // Buat histori hanya jika STATUS benar-benar berubah
    $newStatus = $fasilitas->status;

    if ($oldStatus !== $newStatus) {

        FasilitasHistory::create([
            'fasilitas_id' => $fasilitas->id,
            'user_id' => auth()->id(),
            'status_from' => $oldStatus,
            'status_to' => $newStatus,
            'keterangan' => $fasilitas->keterangan,
        ]);
    }


    // Upload foto fasilitas
// Upload foto fasilitas
if ($request->hasFile('foto')) {

    foreach ($request->file('foto') as $index => $file) {

        $path = $file->store(
            'fasilitas',
            'public'
        );

        // Simpan ke galeri foto fasilitas
        FasilitasPhoto::create([
            'fasilitas_id' => $fasilitas->id,
            'foto' => $path
        ]);

        // Foto pertama menjadi foto utama/dashboard
        if ($index === 0) {
            $fasilitas->foto = $path;
        }
    }
}
    $fasilitas->updated_by = auth()->id();
    $fasilitas->save();

    return redirect()
        ->route('fasilitas.show', $fasilitas->id)
        ->with('success', 'Data fasilitas berhasil diperbarui.');
}
public function show($id)
{
    $fasilitas = Fasilitas::with([
        'photos',
        'histories.user',
        'histories.photos',
        'updater',
    ])->findOrFail($id);

    $auditLogs = AuditLog::with('user')
        ->where('fasilitas_id', $fasilitas->id)
        ->latest()
        ->get();

    return view('fasilitas-detail', compact(
        'fasilitas',
        'auditLogs'
    ));
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

public function deletePhoto($id)
{
    $photo = FasilitasPhoto::findOrFail($id);

    $fasilitas = Fasilitas::findOrFail($photo->fasilitas_id);

    $deletedPath = $photo->foto;

    // Jika foto yang dihapus adalah foto utama Dashboard
    if ($fasilitas->foto === $deletedPath) {

        $nextPhoto = $fasilitas->photos()
            ->where('id', '!=', $photo->id)
            ->latest()
            ->first();

        $fasilitas->foto = $nextPhoto?->foto;
        $fasilitas->save();
    }

    // Hapus file fisik
    if ($deletedPath && Storage::disk('public')->exists($deletedPath)) {
        Storage::disk('public')->delete($deletedPath);
    }

    // Hapus record foto
    $photo->delete();

    // Catat penghapusan foto ke Audit Log
    AuditLog::create([
        'user_id' => auth()->id(),
        'fasilitas_id' => $fasilitas->id,
        'field' => 'foto',
        'old_value' => $deletedPath,
        'new_value' => null,
    ]);

    return redirect()
        ->route('fasilitas.show', $fasilitas->id)
        ->with('success', 'Foto berhasil dihapus.');
}
}