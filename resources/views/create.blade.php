<script src="https://cdn.tailwindcss.com"></script>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
    <div class="w-full max-w-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white rounded-2xl p-8 shadow-2xl">
        <h1 class="text-2xl font-bold text-center mb-6">
            Tambah Fasilitas
        </h1>
        @if ($errors->any())
    <div class="bg-red-500 text-white p-3 rounded mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <form method="POST" action="{{ url('/fasilitas/store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
           <select name="nama" id="nama" class="select2 w-full bg-white/10 border border-white/20 p-2 rounded text-white">
    <option value="Bolder">Bolder</option>
    <option value="Fender">Fender</option>
    <option value="CCTV FIX">CCTV FIX</option>
    <option value="CCTV PTZ">CCTV PTZ</option>
</select>


<input type="text" name="nama_manual" id="nama_manual"
    placeholder="Isi nama fasilitas"
    class="w-full bg-white/10 border border-white/20 p-2 rounded text-white hidden mt-2">

           <select name="lokasi" id="lokasi" class="select2 w-full">
    <option value="Belawan Lama">Belawan Lama</option>
    <option value="Ujung Baru">Ujung Baru</option>
    <option value="IKD">IKD</option>
    <option value="Dermaga Citra">Dermaga Citra</option>
</select>

            <select name="kategori" id="kategori" class="select2 w-full">
    <option value="Dermaga">Dermaga</option>
    <option value="Alat">Alat</option>
    <option value="Kantor">Kantor</option>
</select>
            <select name="status"
                class="w-full bg-white/10 border border-white/20 p-2 rounded text-white placeholder-black/50 focus:text-black focus:bg-white">
                <option >Ready</option>
                <option>Maintenance</option>
                <option>Down</option>
            </select>
            <input type="text" name="detail" placeholder="Detail"
                class="w-full bg-white/10 border border-white/20 p-2 rounded text-white placeholder-black/50 focus:text-black focus:bg-white">
           <input type="file" name="foto[]" multiple class="w-full bg-white/10 border border-white/20 p-2 rounded text-white">
            <input type="text" name="keterangan" placeholder="Keterangan"
                class="w-full bg-white/10 border border-white/20 p-2 rounded text-white placeholder-black/50 focus:text-black focus:bg-white">

<!-- Maps -->
            <input type="text" name="latitude" placeholder="Latitude" class="w-full bg-white/10 border border-white/20 p-2 rounded text-white placeholder-black/50 focus:text-black focus:bg-white">
            <input type="text" name="longitude" placeholder="Longitude" class="w-full bg-white/10 border border-white/20 p-2 rounded text-white placeholder-black/50 focus:text-black focus:bg-white">
            <div class="flex gap-2">
                <a href="{{ url('/dashboard') }}"
                    class="w-1/2 text-center bg-gray-600 hover:bg-gray-700 py-2 rounded">
                    Kembali
                </a> 
                <button class="w-1/2 bg-blue-500 hover:bg-blue-600 py-2 rounded font-bold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
    <script>
$(document).ready(function() {
    $('.select2').select2({
        tags: true,   // bisa ketik manual
        width: '100%',
        placeholder: "Pilih atau ketik..."
    });
});
</script>
<style>
.select2-container .select2-selection--single {
    height: 42px !important;
    background-color: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 6px !important;
    display: flex !important;
    align-items: center !important;
    color: white !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: white !important;
    line-height: 42px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
}

.select2-dropdown {
    background-color: #ffffff !important;
    color: black !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
}

.select2-results__option {
    color: black !important;
}
</style>
</div>