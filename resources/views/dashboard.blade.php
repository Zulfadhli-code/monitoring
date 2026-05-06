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
                        <th class="p-3">Nama</th>
                        <th class="p-3">Lokasi</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Detail</th>
                        <th class="p-3">Status</th>
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
                      <td class="p-3 space-y-1">

    <!-- BADGE -->
    <span class="px-2 py-1 rounded text-white block text-center
        @if($item->status=='ready') bg-green-500
        @elseif($item->status=='maintenance') bg-yellow-500
        @else bg-red-500
        @endif">
        {{ strtoupper($item->status) }}
    </span>

    <!-- UPDATE READY MAINTENANCE DOWN -->
    @if(auth()->user()->role === 'admin')
    <select onchange="updateStatus({{ $item->id }}, this.value)"
        class="border rounded px-2 py-1 w-full text-sm">
        <option value="ready" {{ $item->status=='ready'?'selected':'' }}>Ready</option>
        <option value="maintenance" {{ $item->status=='maintenance'?'selected':'' }}>Maintenance</option>
        <option value="down" {{ $item->status=='down'?'selected':'' }}>Down</option>
    </select>
@else
    <div class="text-gray-500 text-sm text-center">
        (Tidak bisa ubah)
    </div>
@endif

</td>

                        <td class="p-3"><div>
   {{ $item->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
</div></td>

                        <!-- FOTO -->
                        <td class="p-3">
                            @if($item->foto)
                                <a href="{{ asset('storage/'.$item->foto) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$item->foto) }}" width="60"
                                        class="rounded hover:scale-110 transition">
                                </a>
                            @else
                                -
                            @endif
                        </td>

                        <!-- AKSI -->
                        <td class="p-3">
                            <button type="button"
    onclick="showHistory({{ $item->id }})"
    class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
    Histori
</button>
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
<div id="historyModal"
    class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50">

    <div class="bg-white rounded-lg shadow-lg w-[500px] max-h-[400px] overflow-auto p-4">

        <h3 class="font-bold text-lg mb-3">Riwayat Perubahan</h3>

        <div id="historyContent"></div>

        <button onclick="closeModal()"
            class="mt-4 bg-gray-500 text-white px-3 py-1 rounded">
            Tutup
        </button>

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

        let html = '';

        data.forEach(item => {
    const user = item.user ? item.user.name : 'System';
            html += `
                <div class="border-b py-2 text-sm">
                    <div class="font-semibold">${item.user?.name ?? 'System'}</div>
                    <div>
                        ${item.status_from ?? '-'} → 
                        <b>${item.status_to}</b>
                    </div>
                    <div class="text-gray-500 text-xs">
                        ${new Date(item.created_at).toLocaleString()}
                    </div>
                </div>
            `;
        });

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


</x-app-layout>