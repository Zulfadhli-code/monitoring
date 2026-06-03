<script src="https://cdn.tailwindcss.com"></script>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
    <div class="w-full max-w-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white rounded-2xl p-8 shadow-2xl">
        <h1 class="text-2xl font-bold text-center mb-6">
            Tambah Fasilitas
        </h1>
        <form method="POST" action="{{ url('/fasilitas/store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <select name="nama" id="nama"
    class="w-full bg-white/10 border border-white/20 p-2 rounded text-white focus:text-black focus:bg-white"
    onchange="toggleNamaManual()">

    <option value="">-- Pilih Nama Fasilitas --</option>
    <option >Bolder</option>
    <option >Fender</option>
    <option >CCTV FIX</option>
    <option >CCTV PTZ</option>
    <option >+ Lainnya</option>
</select>

<input type="text" name="nama_manual" id="nama_manual"
    placeholder="Isi nama fasilitas"
    class="w-full bg-white/10 border border-white/20 p-2 rounded text-white hidden mt-2">
            <select name="lokasi"
                class="w-full bg-white/10 border border-white/20 p-2 rounded text-white placeholder-black/50 focus:text-black focus:bg-white">
                <option>Belawan Lama</option>
                <option>Ujung Baru</option>
                <option>IKD</option>
                <option>Dermaga Citra</option>
            </select>
            <select name="kategori"
               class="w-full bg-white/10 border border-white/20 p-2 rounded text-white placeholder-black/50 focus:text-black focus:bg-white">
                <option>Dermaga</option>
                <option>Alat</option>
                <option>Kantor</option>
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
function toggleNamaManual() {
    let select = document.getElementById('nama');
    let input = document.getElementById('nama_manual');

    if (select.value === 'other') {
        input.classList.remove('hidden');
        input.value = '';
    } else {
        input.classList.add('hidden');
        input.value = select.value;
    }
}
</script>
</div>