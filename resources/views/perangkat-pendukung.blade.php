<x-app-layout>
@section('page-title', 'Perangkat Pendukung')
@section('page-description', 'Monitoring status perangkat pendukung Branch Belawan')
    <div class="p-5 lg:p-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">
                Perangkat Pendukung
            </h1>

            <p class="text-sm text-slate-400 mt-1">
                Monitoring status perangkat pendukung Branch Belawan
            </p>
        </div>

        <!-- STATISTIK -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- TOTAL -->
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">
                    Total Perangkat
                </p>

                <p class="text-3xl font-bold text-white mt-2">
                    {{ $total }}
                </p>
            </div>

            <div class="w-11 h-11 rounded-xl bg-blue-500/10
                        border border-blue-500/10
                        flex items-center justify-center text-blue-400">

                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 17v-2a4 4 0 014-4h4m0 0V7m0 4l-3-3m3 3l3-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>

            </div>
        </div>
    </div>


    <!-- READY -->
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">
                    Ready
                </p>

                <p class="text-3xl font-bold text-green-400 mt-2">
                    {{ $ready }}
                </p>
            </div>

            <div class="w-11 h-11 rounded-xl bg-green-500/10
                        border border-green-500/10
                        flex items-center justify-center text-green-400">

                <span class="text-xl">✓</span>

            </div>
        </div>
    </div>


    <!-- MAINTENANCE -->
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">
                    Maintenance
                </p>

                <p class="text-3xl font-bold text-yellow-400 mt-2">
                    {{ $maintenance }}
                </p>
            </div>

            <div class="w-11 h-11 rounded-xl bg-yellow-500/10
                        border border-yellow-500/10
                        flex items-center justify-center text-yellow-400">

                <span class="text-xl">!</span>

            </div>
        </div>
    </div>


    <!-- DOWN -->
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">
                    Down
                </p>

                <p class="text-3xl font-bold text-red-400 mt-2">
                    {{ $down }}
                </p>
            </div>

            <div class="w-11 h-11 rounded-xl bg-red-500/10
                        border border-red-500/10
                        flex items-center justify-center text-red-400">

                <span class="text-xl">!</span>

            </div>
        </div>
    </div>

</div>

        <div class="glass-card rounded-2xl p-6">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-white">
                        Daftar Perangkat
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        {{ $fasilitas->total() }} perangkat terdaftar
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>
                        <tr class="border-b border-white/5 text-slate-500 text-xs">
                            <th class="text-left py-3 px-3">No</th>
                            <th class="text-left py-3 px-3">Nama</th>
                            <th class="text-left py-3 px-3">Subkategori</th>
                            <th class="text-left py-3 px-3">Lokasi</th>
                            <th class="text-left py-3 px-3">Status</th>
                            <th class="text-left py-3 px-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($fasilitas as $item)

                            <tr class="border-b border-white/5 hover:bg-white/[0.02]">

                                <td class="py-3 px-3 text-slate-500">
                                    {{ $loop->iteration + ($fasilitas->currentPage() - 1) * $fasilitas->perPage() }}
                                </td>

                                <td class="py-3 px-3 font-medium text-white">
                                    {{ $item->nama }}
                                </td>

                                <td class="py-3 px-3 text-slate-400">
                                    {{ $item->subkategori }}
                                </td>

                                <td class="py-3 px-3 text-slate-400">
                                    {{ $item->lokasi }}
                                </td>

                                <td class="py-3 px-3">

                                    @if($item->status === 'ready')

                                        <span class="px-2.5 py-1 rounded-lg bg-green-500/10 text-green-400 text-xs font-semibold">
                                            Ready
                                        </span>

                                    @elseif($item->status === 'maintenance')

                                        <span class="px-2.5 py-1 rounded-lg bg-yellow-500/10 text-yellow-400 text-xs font-semibold">
                                            Maintenance
                                        </span>

                                    @else

                                        <span class="px-2.5 py-1 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold">
                                            Down
                                        </span>

                                    @endif

                                                             <!-- AKSI -->
<td class="py-3 px-3">
    <div class="flex flex-wrap gap-1.5">

        <a href="{{ route('fasilitas.show', $item->id) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5
                  rounded-lg bg-blue-500/10
                  border border-blue-500/20
                  text-blue-400 hover:bg-blue-500/20
                  text-xs font-semibold transition">

            <svg class="w-4 h-4"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542-7z"/>
            </svg>

            Detail
        </a>

        <button
            type="button"
            onclick="openUpdateModal({{ $item->id }})"
            class="inline-flex items-center gap-1.5 px-3 py-1.5
                   rounded-lg bg-green-500/10
                   border border-green-500/20
                   text-green-400 hover:bg-green-500/20
                   text-xs font-semibold transition">

            <svg class="w-4 h-4"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 5h2m-1-1v2m-6 7h8m-8 4h8m-8-8h8"/>
            </svg>

            Update
        </button>

    </div>
</td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    Belum ada perangkat pendukung.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-5">
                {{ $fasilitas->links('pagination::tailwind') }}
            </div>

        </div>

    </div>
<!-- UPDATE MODAL -->
<div
    id="updateModal"
    class="fixed inset-0 hidden items-center justify-center bg-black/70 backdrop-blur-sm z-[9999] p-4"
>
    <div class="bg-slate-900 border border-white/10 rounded-2xl shadow-2xl w-full max-w-lg">

        <div class="p-5 border-b border-white/10 flex justify-between items-center">

            <div>
                <h2 class="text-lg font-bold text-white">
                    Update Fasilitas
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Perbarui status dan dokumentasi fasilitas
                </p>
            </div>

            <button
                type="button"
                onclick="closeUpdateModal()"
                class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400"
            >
                ✕
            </button>

        </div>

        <form id="updateForm" enctype="multipart/form-data">

            <input
                type="hidden"
                id="fasilitas_id"
            >

            <div class="p-5 space-y-5">

                <!-- STATUS -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Status
                    </label>

                    <select
                        id="status"
                        class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                    >
                        <option value="ready">Ready</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="down">Down</option>
                    </select>
                </div>

                <!-- KETERANGAN -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Keterangan
                    </label>

                    <textarea
                        id="keterangan"
                        class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        rows="4"
                        placeholder="Masukkan keterangan..."
                    ></textarea>
                </div>

                <!-- FOTO -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Foto Terbaru
                    </label>

                    <div id="foto-container">

                        <input
                            type="file"
                            name="foto[]"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-sm text-slate-400 mb-2"
                        >

                    </div>

                    <button
                        type="button"
                        onclick="tambahFoto()"
                        class="text-xs text-blue-400 hover:text-blue-300"
                    >
                        + Tambah Foto
                    </button>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="px-5 py-4 border-t border-white/10 flex justify-end gap-2">

                <button
                    type="button"
                    onclick="closeUpdateModal()"
                    class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 text-sm"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold"
                >
                    Simpan
                </button>

            </div>

        </form>

    </div>
</div>
<script>
function openUpdateModal(id) {
    document.getElementById('updateModal').classList.remove('hidden');
    document.getElementById('updateModal').classList.add('flex');

    document.getElementById('fasilitas_id').value = id;
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
    document.getElementById('updateModal').classList.remove('flex');
}

document.getElementById('updateForm')
.addEventListener('submit', async function(e) {

    e.preventDefault();

    const id = document.getElementById('fasilitas_id').value;

    let formData = new FormData();

    formData.append(
        'status',
        document.getElementById('status').value
    );

    formData.append(
        'keterangan',
        document.getElementById('keterangan').value
    );

    const fotoInputs = document.querySelectorAll('input[name="foto[]"]');

    fotoInputs.forEach(input => {
        if (input.files[0]) {
            formData.append('foto[]', input.files[0]);
        }
    });

    formData.append('_token', '{{ csrf_token() }}');

    const res = await fetch(`/fasilitas/update/${id}`, {
        method: 'POST',
        body: formData
    });

    const data = await res.json();

    if (data.success) {
        location.reload();
    } else {
        alert('Gagal update');
    }
});

function tambahFoto() {

    let container = document.getElementById('foto-container');

    let total = container.querySelectorAll('input').length;

    if (total >= 5) {
        alert('Maksimal 5 foto');
        return;
    }

    let input = document.createElement('input');

    input.type = 'file';
    input.name = 'foto[]';

    input.className =
        'w-full border rounded p-2 mb-2';

    container.appendChild(input);
}
</script>
</x-app-layout>