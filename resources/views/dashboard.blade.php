<x-app-layout>

    <x-slot name="header">
    <h2 class="font-bold text-xl text-black text-center">
        
    <!-- SELAMAT DATANG -->
    </h2>
</x-slot>


    <!-- Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <!-- CHART -->
         <div class="w-60 mx-auto mb-6">
    <canvas id="chart"></canvas>
</div>
    <div class="max-w-7xl mx-auto px-4">
        <div id="map" class="w-full h-[400px] rounded-lg mb-6"></div>

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))
            <div class="bg-green-500 text-white p-2 mb-3 rounded">
                {{ session('success') }}
            </div>
        @endif


        <!-- FILTER -->
        <form method="GET" class="flex gap-2 mb-4">
           <input type="text" name="search" value="{{ request('search') }}"
    placeholder="Cari fasilitas..."
    class="border p-2 rounded w-1/3">
                <input type="date" name="tanggal" class="border p-2 rounded">

            <select name="status" class="border p-2 rounded">
                <option value="">Semua Status</option>
                <option value="ready">Ready</option>
                <option value="maintenance">Maintenance</option>
                <option value="down">Down</option>
            </select>

            <button class="bg-blue-500 text-white px-4 rounded">Filter</button>
        </form>

        <!-- ADD -->
        <a href="/fasilitas/create" class="bg-green-500 text-white px-4 py-2 rounded mb-3 inline-block">
            + Tambah Fasilitas
        </a>

        <a href="{{ url('/fasilitas/export-pdf?'.http_build_query(request()->all())) }}"
   class="bg-red-500 text-white px-4 py-2 rounded mb-3 inline-block">
   Download PDF
</a>

   <!-- <div id="map" class="w-full  rounded-lg mb-6"></div> -->
        <!-- TABLE -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Nama Fasilitas</th>
                        <th class="p-3">Lokasi</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Detail</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Keterangan</th>
                        <th class="p-3">Diupdate Oleh</th>
                        <th class="p-3">Update</th>
                        <th class="p-3">Gambar</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fasilitas as $item)
                    <tr class="border-t hover:bg-gray-50
    @if($item->status == 'down') bg-red-100
    @elseif($item->status == 'maintenance') bg-yellow-100
    @endif">
                       <td class="p-3">
    {{ $loop->iteration + ($fasilitas->currentPage() - 1) * $fasilitas->perPage() }}
</td>
                        <td class="p-3 font-semibold">{{ ucwords($item->nama) }}</td>
                        <td class="p-3">{{  ucwords($item->lokasi) }}</td>
                        <td class="p-3">{{  ucwords($item->kategori) }}</td>
                        <td class="p-3">{{  ucwords($item->detail) }}</td>

                        <!-- STATUS -->
                         <td class="p-3">

<span class="px-2 py-1 rounded block text-center text-white

@if($item->status=='ready')
    bg-green-500
@elseif($item->status=='maintenance')
    bg-yellow-500
@else
    bg-red-500
@endif
">

{{ strtoupper($item->status) }}

</span>

</td><!-- Keterangan -->
                        <td class="p-3">{{ $item->keterangan ?? '-' }}</td>
                        <td class="p-3">{{ $item->updater->name ?? '-' }}</td>

                        <!-- Waktu -->
                        <td class="p-3"><div>
   {{ $item->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
</div></td>

                        <!-- FOTO -->
                       <td class="p-3">

@if($item->foto)

    <a href="{{ asset('storage/'.$item->foto) }}"
        target="_blank">

        <img
            src="{{ asset('storage/'.$item->foto) }}"
            width="80"
            class="rounded hover:scale-110 transition">

    </a>

@else

    -

@endif

</td>

                        <!-- AKSI -->
                        <td class="p-3">
                            <button onclick="openUpdateModal({{ $item->id }})"class="bg-green-500 text-white px-3 py-1 rounded text-sm">Update</button>
                            <button type="button"onclick="showHistory({{ $item->id }})"class="bg-blue-500 text-white px-3 py-1 rounded text-sm"> Histori</button>
                            @if(auth()->user()->role === 'admin')
                                <form action="/fasilitas/{{ $item->id }}" method="POST"
                                    onsubmit="return confirm('Yakin mau hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- TABLE -->

<!-- 🔥 TARUH MODAL DI SINI -->
<!-- MODAL HISTORI -->
<div id="historyModal"
    class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-[9999]">

    <div class="bg-white rounded-lg shadow-lg w-[500px] max-h-[400px] overflow-auto p-4">

        <h3 class="font-bold text-lg mb-3">Riwayat Perubahan</h3>
        <a id="downloadHistoryPdf"
    href="#"
    target="_blank"
    class="bg-red-500 text-white px-3 py-1 rounded text-sm inline-block mb-3">

    Download PDF
</a>

        <div id="historyContent"></div>

        <button onclick="closeModal()"
            class="mt-4 bg-gray-500 text-white px-3 py-1 rounded">
            Tutup
        </button>

    </div>
</div>


<!-- MODAL UPDATE -->
<div id="updateModal"
    class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">

    <div class="bg-white rounded-lg shadow-lg w-[500px] p-5">

        <h2 class="text-lg font-bold mb-4">
            Update Fasilitas
        </h2>

        <form id="updateForm" enctype="multipart/form-data">

            <input type="hidden" id="fasilitas_id">

            <!-- STATUS -->
            <div class="mb-3">
                <label>Status</label>

                <select id="status"
                    class="w-full border rounded p-2">

                    <option value="ready">Ready</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="down">Down</option>

                </select>
            </div>

            <!-- KETERANGAN -->
            <div class="mb-3">
                <label>Keterangan</label>

                <textarea id="keterangan"
                    class="w-full border rounded p-2"
                    rows="3"></textarea>
            </div>

            <!-- FOTO -->
            <div class="mb-3">
                <label>Foto Terbaru</label>
<div id="foto-container">

    <input
        type="file"
        name="foto[]"
        class="w-full border rounded p-2 mb-2">

</div>

            <div class="flex gap-2 justify-end">

            <button
    type="button"
    onclick="tambahFoto()"
    class="bg-green-500 text-white px-3 py-1 rounded">

    + Tambah Foto
</button>
                <button type="button"
                    onclick="closeUpdateModal()"
                    class="bg-gray-500 text-white px-4 py-2 rounded">

                    Batal
                </button>

                <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded">

                    Simpan
                </button>

            </div>

        </form>

    </div>
</div>
        <!-- PAGINATION -->
        <div class="mt-4">
            {{ $fasilitas->links() }}
        </div>
    </div>


    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        fetch('/chart-data')
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('chart');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Ready', 'Maintenance', 'Down'],
                    datasets: [{
                        data: [data.ready, data.maintenance, data.down],
                        backgroundColor: ['green', 'orange', 'red']
                    }]
                }
            });
        });

        function updateStatus(id, status) {
            fetch(`/fasilitas/update-status/${id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    location.reload();
                }
            });
        }
    </script>

<script>
function loadTable() {
    fetch('/dashboard')
    .then(res => res.text())
    .then(html => {
        const newDoc = new DOMParser().parseFromString(html, "text/html");

        // update tabel
        document.querySelector("tbody").innerHTML =
            newDoc.querySelector("tbody").innerHTML;
    });
}

// refresh tiap 5 detik
setInterval(loadTable, 5000);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Histori -->
 <script>
async function showHistory(id){
    document.getElementById('downloadHistoryPdf')
    .href = `/fasilitas/${id}/history-pdf`;
    const modal = document.getElementById('historyModal');
    const content = document.getElementById('historyContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    content.innerHTML = 'Loading...';

    try {
        const res = await fetch(`/fasilitas/${id}/histories`);
        const data = await res.json();

        if(data.length === 0){
            content.innerHTML = '<p class="text-gray-500">Belum ada histori</p>';
            return;
        }

        let html = `

<table class="w-full text-sm border">

    <thead class="bg-gray-100">

        <tr>

<th class="border p-2">Sebelum</th>

<th class="border p-2">Diupdate</th>
            <th class="border p-2">User</th>

            <th class="border p-2">Status</th>

            <th class="border p-2">Keterangan</th>

            <th class="border p-2">Foto</th>

        </tr>

    </thead>

    <tbody>

`;

data.forEach(item => {

    html += `

        <tr class="border-t">

           <td class="border p-2 text-xs">

    ${
        item.previous_update
        ?
        new Date(item.previous_update).toLocaleString()
        :
        '-'
    }

</td>

<td class="border p-2 text-xs">
    ${new Date(item.created_at).toLocaleString()}
</td>

            <td class="border p-2">
                ${item.user?.name ?? 'System'}
            </td>

            <td class="border p-2">

                ${item.status_from ?? '-'}
                →
                <b>${item.status_to}</b>

            </td>

            <td class="border p-2">
                ${item.keterangan ?? '-'}
            </td>

          <td class="border p-2">

    ${
        item.photos.length

        ?

        item.photos.map(photo => `

            <a href="/storage/${photo.foto}"
                target="_blank">

                <img
                    src="/storage/${photo.foto}"
                    width="60"
                    class="rounded mb-1">

            </a>

        `).join('')

        :

        '-'
    }

</td>

        </tr>

    `;
});

html += `
    </tbody>
</table>
`;

content.innerHTML = html;
    } catch(e){
        content.innerHTML = 'Error load data';
    }
}

function closeModal(){
    const modal = document.getElementById('historyModal');
    modal.classList.add('hidden');
}
</script>
<!-- Maps -->
 <script>
   console.log("FASILITAS MAP:", @json($fasilitasMap));
</script>
 <script>
    const map = L.map('map').setView([3.7915, 98.6730], 12); // Belawan

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const data = @json($fasilitasMap);

    data.forEach(item => {
        if (!item.latitude || !item.longitude) return;

        let color = "green";

        if (item.status === "maintenance") color = "orange";
        if (item.status === "down") color = "red";

        const marker = L.circleMarker([item.latitude, item.longitude], {
            radius: 8,
            color: color,
            fillColor: color,
            fillOpacity: 0.8
        }).addTo(map);

        marker.bindPopup(`
            <b>${item.detail}</b><br>
            ${item.lokasi}<br>
            Status: <b>${item.status}</b>
        `);
    });
</script>
<!-- Javascript Modal Update -->
 <script>

function openUpdateModal(id)
{
    document.getElementById('updateModal')
        .classList.remove('hidden');

    document.getElementById('updateModal')
        .classList.add('flex');

    document.getElementById('fasilitas_id').value = id;
}

function closeUpdateModal()
{
    document.getElementById('updateModal')
        .classList.add('hidden');
}

document.getElementById('updateForm')
.addEventListener('submit', async function(e){

    e.preventDefault();

    const id = document.getElementById('fasilitas_id').value;

    let formData = new FormData();

    formData.append('status',
        document.getElementById('status').value);

    formData.append('keterangan',
        document.getElementById('keterangan').value);

   const fotoInputs =
    document.querySelectorAll(
        'input[name="foto[]"]'
    );

fotoInputs.forEach(input => {

    if(input.files[0]){

        formData.append(
            'foto[]',
            input.files[0]
        );
    }

});

    formData.append('_token',
        '{{ csrf_token() }}');

    const res = await fetch(`/fasilitas/update/${id}`, {
        method: 'POST',
        body: formData
    });
    const data = await res.json();
    if(data.success){
        location.reload();} else {alert('Gagal update');}});</script>

<!-- Upload FOTO UPDATE -->
 <script>

function tambahFoto(){

    let container =
        document.getElementById('foto-container');

    let input =
        document.createElement('input');

    input.type = 'file';

    input.name = 'foto[]';

    input.className =
        'w-full border rounded p-2 mb-2';

    container.appendChild(input);
}

</script>
</x-app-layout>