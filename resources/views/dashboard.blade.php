<x-app-layout>
<!-- SUCCESS -->
@if(session('success'))
    <div class="mb-5 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-green-400">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
@endif

<!-- DIAGRAM + SUMMARY + ALERT -->

<div id="diagram" class="grid grid-cols-1 xl:grid-cols-12 gap-5">
    <!-- MAP -->
    <div class="xl:col-span-7 glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-4 flex items-center justify-between border-b border-white/5">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-white font-bold">
                        Diagram Kesiapan
                    </h2>
                    <span class="px-2 py-0.5 rounded-full bg-green-500/10 border border-green-500/20 text-green-400 text-[10px] font-bold">
                        LIVE
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Monitoring lokasi fasilitas Branch Belawan </p>
            </div>
            <button onclick="document.getElementById('map').scrollIntoView({behavior:'smooth'})" class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 text-xs border border-blue-500/20">
                Lihat Detail
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!-- MAP -->
        <div class="p-3">
            <div id="map" class="w-full h-[390px] lg:h-[450px] rounded-xl overflow-hidden"></div>
        </div>

        <!-- LEGEND -->
        <div class="px-5 pb-4">
            <div class="flex flex-wrap gap-5 text-xs text-slate-400">
                <div class="flex items-center gap-2"><span class="status-dot bg-green-500"></span> Ready / Siap</div>
                <div class="flex items-center gap-2"><span class="status-dot bg-yellow-400"></span>Maintenance / Perhatian</div>
                <div class="flex items-center gap-2"><span class="status-dot bg-red-500"></span>Down / Tidak Siap</div>
            </div>
        </div>
    </div>

    <!-- SUMMARY -->
<div class="xl:col-span-2 glass-card rounded-2xl p-5 h-full">
    {{-- WAKTU SISTEM --}}
    <div class="text-center">
        <p class="text-[14px] uppercase tracking-widest text-slate-500">Waktu Indonesia Barat</p>
        <div id="system-date" class="text-xl font-semibold text-white mt-4">2026-09-08</div>
        <div id="system-time" class="text-5xl  font-bold text-white  tracking-wide">17:27:00</div>
        <p class="text-[20px] text-slate-500 mt-2">(UTC+07:00)</p>
        <p class="text-[20px] text-slate-500">Asia/Jakarta</p>
    </div>

    {{-- PEMBATAS --}}
    <div class="border-t border-white/5 my-5"></div>

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-base font-semibold text-white">Ringkasan</h3>
            <p class="text-[11px] text-slate-500 mt-1">Status seluruh fasilitas</p>
        </div>
        <div class="px-2.5 py-1 rounded-lg bg-blue-500/10border border-blue-500/20 text-[10px] text-blue-400"> Semua Area</div>
    </div>

    {{-- TOTAL FASILITAS --}}
    <div class="text-center py-5">
        <p class="text-[10px] uppercase tracking-widest text-slate-500">Total Fasilitas Dipantau</p>
        <div class="text-6xl font-bold text-white mt-3"> {{ $totalFasilitas }} </div>
        <p class="text-[11px] text-slate-500 mt-2"> fasilitas terdaftar </p>
    </div>
</div>

{{-- REALTIME CLOCK --}}
<script>
    function updateSystemClock() {
        const now = new Date();

        const parts = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Jakarta',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).formatToParts(now);

        const year = parts.find(p => p.type === 'year').value;
        const month = parts.find(p => p.type === 'month').value;
        const day = parts.find(p => p.type === 'day').value;

        const time = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).format(now);

        document.getElementById('system-date').textContent =
            `${year}-${month}-${day}`;

        document.getElementById('system-time').textContent =
            time;
    }

    updateSystemClock();
    setInterval(updateSystemClock, 1000);
</script>
    <!-- ALERT -->
<div class="xl:col-span-3 glass-card rounded-2xl overflow-hidden">
    <!-- HEADER -->
    <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-white">Alert Terbaru</h2>
            <p class="text-[10px] text-slate-500 mt-1"> Fasilitas yang membutuhkan perhatian </p>
        </div>
        <span class="px-2 py-1 rounded-lg {{ ($alertCount ?? 0) > 0 ? 'bg-red-500/10 border border-red-500/10 text-red-400' : 'bg-green-500/10 border border-green-500/10 text-green-400'}} text-[10px] font-semibold">{{ $alertCount ?? 0 }} Alert</span>
    </div>

    <!-- ALERT LIST -->
    <div class="p-3 space-y-2 max-h-[390px] overflow-y-auto"> @forelse($alerts as $alert) @php $isDown = $alert->status === 'down'; $statusLabel = $isDown? 'DOWN' : 'MAINTENANCE'; @endphp
            <div class="group rounded-xl p-3 {{ $isDown ? 'bg-red-500/5 border border-red-500/10 hover:border-red-500/20' : 'bg-yellow-500/5 border border-yellow-500/10 hover:border-yellow-500/20' }} transition-colors duration-200">
                <div class="flex items-start gap-3">
                    <!-- ICON -->
                    <div class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center {{ $isDown ? 'bg-red-500/10 text-red-400': 'bg-yellow-500/10 text-yellow-400'}}">@if($isDown)
                            <!-- DOWN ICON -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor"viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                            </svg>
                        @else
                            <!-- MAINTENANCE ICON -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                                <path stroke-linecap="round"  stroke-linejoin="round" stroke-width="2" d="M11 4a7 7 0 0 0 0 14h1a7 7 0 0 0 0-14h-1zm1 3v4l3 2"/>
                            </svg>
                        @endif
                    </div>

                    <!-- CONTENT -->
                    <div class="min-w-0 flex-1">
    <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-white truncate">
                {{ ucwords($alert->nama ?? $alert->detail ?? 'Fasilitas') }}
            </p>
            <p class="text-[10px] text-slate-500 mt-1">
                📍 {{ $alert->lokasi ?? 'Lokasi tidak tersedia' }}
            </p>
        </div>
        <span class="shrink-0 text-[9px] font-bold tracking-wide {{ $isDown ? 'text-red-400' : 'text-yellow-400' }}">
            {{ $statusLabel }}
        </span>
    </div>

                        @if(!empty($alert->keterangan))
                            <p class="text-[11px] text-slate-400 mt-1 truncate">
                                {{ $alert->keterangan }}
                            </p>
                        @else
                            <p class="text-[11px] text-slate-600 mt-1">
                                Tidak ada keterangan
                            </p>
                        @endif
                        <div class="flex items-center gap-1 mt-2 text-[9px] text-slate-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>

                            Diperbarui
                            {{ optional($alert->updated_at)->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        @empty

            <!-- EMPTY STATE -->
            <div class="flex flex-col items-center justify-center text-center py-12 px-4">
                <div class="w-14 h-14 rounded-2xl bg-green-500/10 border border-green-500/10 flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"  stroke-linejoin="round" stroke-width="2"  d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm text-green-400 font-semibold mt-4">
                    Semua fasilitas siap
                </p>
                <p class="text-[11px] text-slate-600 mt-1">
                    Tidak ada alert aktif saat ini
                </p>
            </div>
        @endforelse
    </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- KPI KESIAPAN -->
<!-- ========================================================= -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5">
    <!-- ===================================================== -->
    <!-- TEKNIK -->
    <!-- ===================================================== -->
    <div id="kesiapan-teknik" class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.3-3.3a6 6 0 01-7.5 7.5l-6 6a2.1 2.1 0 11-3-3l6-6a6 6 0 017.5-7.5l-3.3 3.3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">
                        Kesiapan
                    </p>
                    <h3 class="font-bold text-white">
                        Teknik
                    </h3>
                </div>
            </div>
            <span class="text-xs text-slate-500">
                {{ $teknikTotal }} fasilitas
            </span>
        </div>

        <!-- PERCENTAGE -->
        <div class="mt-5">
            <div class="text-4xl font-bold text-white">
                {{ $teknikReadiness }}%
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Tingkat kesiapan
            </p>
        </div>

        <!-- PROGRESS -->
        <div class="w-full h-2 bg-slate-800 rounded-full mt-4 overflow-hidden">
            <div
                class="h-full bg-blue-500 rounded-full transition-all"
                style="width: {{ $teknikReadiness }}%"
            ></div>
        </div>

        <!-- STATUS -->
        <div class="grid grid-cols-3 gap-2 mt-4">
            <div class="rounded-lg bg-green-500/10 p-2 text-center">
                <div class="text-sm font-bold text-green-400">
                    {{ $teknikReady }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Ready
                </div>
            </div>

            <div class="rounded-lg bg-yellow-500/10 p-2 text-center">
                <div class="text-sm font-bold text-yellow-400">
                    {{ $teknikMaintenance }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Maintenance
                </div>
            </div>

            <div class="rounded-lg bg-red-500/10 p-2 text-center">
                <div class="text-sm font-bold text-red-400">
                    {{ $teknikDown }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Down
                </div>
            </div>
        </div>
    </div>



    <!-- ===================================================== -->
    <!-- OPERASIONAL -->
    <!-- ===================================================== -->

    <div id="kesiapan-operasional" class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7h-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">
                        Kesiapan
                    </p>
                    <h3 class="font-bold text-white">
                        Operasional
                    </h3>
                </div>
            </div>
            <span class="text-xs text-slate-500">
                {{ $operasionalTotal }} fasilitas
            </span>
        </div>

        <div class="mt-5">
            <div class="text-4xl font-bold text-white">
                {{ $operasionalReadiness }}%
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Tingkat kesiapan
            </p>
        </div>

        <div class="w-full h-2 bg-slate-800 rounded-full mt-4 overflow-hidden">
            <div
                class="h-full bg-purple-500 rounded-full transition-all"
                style="width: {{ $operasionalReadiness }}%"
            ></div>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-4">
            <div class="rounded-lg bg-green-500/10 p-2 text-center">
                <div class="text-sm font-bold text-green-400">
                    {{ $operasionalReady }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Ready
                </div>
            </div>

            <div class="rounded-lg bg-yellow-500/10 p-2 text-center">
                <div class="text-sm font-bold text-yellow-400">
                    {{ $operasionalMaintenance }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Maintenance
                </div>
            </div>

            <div class="rounded-lg bg-red-500/10 p-2 text-center">
                <div class="text-sm font-bold text-red-400">
                    {{ $operasionalDown }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Down
                </div>
            </div>
        </div>
    </div>



    <!-- ===================================================== -->
    <!-- PERANGKAT PENDUKUNG -->
    <!-- ===================================================== -->

    <div id="perangkat-pendukung" class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v9a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm-2 15h20M9 19h6"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">
                        Perangkat
                    </p>

                    <h3 class="font-bold text-white">
                        Pendukung
                    </h3>
                </div>
            </div>

            <span class="text-xs text-slate-500">
                {{ $pendukungTotal }} perangkat
            </span>
        </div>

        <div class="mt-5">
            <div class="text-4xl font-bold text-white">
                {{ $pendukungReadiness }}%
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Tingkat kesiapan
            </p>
        </div>

        <div class="w-full h-2 bg-slate-800 rounded-full mt-4 overflow-hidden">
            <div
                class="h-full bg-cyan-500 rounded-full transition-all"
                style="width: {{ $pendukungReadiness }}%"
            ></div>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-4">
            <div class="rounded-lg bg-green-500/10 p-2 text-center">
                <div class="text-sm font-bold text-green-400">
                    {{ $pendukungReady }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Ready
                </div>
            </div>

            <div class="rounded-lg bg-yellow-500/10 p-2 text-center">
                <div class="text-sm font-bold text-yellow-400">
                    {{ $pendukungMaintenance }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Maintenance
                </div>
            </div>

            <div class="rounded-lg bg-red-500/10 p-2 text-center">
                <div class="text-sm font-bold text-red-400">
                    {{ $pendukungDown }}
                </div>
                <div class="text-[10px] text-slate-500">
                    Down
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- DATA FASILITAS -->
<!-- ========================================================= -->

<div id="laporan" class="glass-card rounded-2xl mt-5 overflow-hidden">
    <!-- HEADER -->
    <div class="px-5 lg:px-6 py-5 border-b border-white/5">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-white">
                    Data Fasilitas
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Daftar fasilitas dan status kesiapan terkini
                </p>
            </div>

            <!-- ACTION -->
            <div class="flex flex-wrap items-center gap-2">
    {{-- TAMBAH FASILITAS --}}
    <a href="/fasilitas/create" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white px-4 py-2.5 rounded-lg text-xs font-semibold transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Fasilitas
    </a>

    {{-- EXPORT --}}
    <div class="flex flex-wrap items-center gap-2">
        <input type="month" id="export_bulan" value="{{ date('Y-m') }}" class="bg-slate-900 border border-white/10 text-slate-300 px-3 py-2.5 rounded-lg text-xs">
        <select id="export_kategori" class="bg-slate-900 border border-white/10 text-slate-300 px-3 py-2.5 rounded-lg text-xs" >
            <option value="">Semua Kategori</option>
            <option value="Kesiapan Teknik">Kesiapan Teknik</option>
            <option value="Kesiapan Operasional">Kesiapan Operasional</option>
            <option value="Perangkat Pendukung">Perangkat Pendukung</option>
        </select>
        <input type="text" id="export_nama" placeholder="Nama fasilitas..." class="bg-slate-900 border border-white/10 text-slate-300 placeholder-slate-600 px-3 py-2.5 rounded-lg text-xs w-44">
       <a href="{{ route('export.monthly.excel') }}" id="btnExportExcel" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-lg text-xs font-semibold transition">
    Excel Bulanan
</a>
        <button type="button"  onclick="exportPdfBulanan()" class="inline-flex items-center gap-2 bg-red-600/90 hover:bg-red-500 text-white px-4 py-2.5 rounded-lg text-xs font-semibold transition">
            PDF 
        </button>
    </div>
</div>
        </div>

        <!-- FILTER -->
<form method="GET" class="flex flex-col md:flex-row gap-2 mt-5">
    <div class="relative flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama fasilitas..." class="w-full bg-slate-950/70 border border-white/10 text-slate-200 placeholder-slate-600 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    </div>
    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white rounded-lg px-5 py-2.5 text-sm font-semibold transition">
        Filter
    </button>

    @if(request('search'))
        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg px-5 py-2.5 text-sm font-semibold transition">
            Reset
        </a>
    @endif
</form>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-white/[0.025]">
                <tr class="text-left text-[11px] uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-4">No</th>
                    <th class="px-4 py-4 min-w-[180px]">
                        Nama Fasilitas
                    </th>
                    <th class="px-4 py-4">
                        Lokasi
                    </th>
                    <th class="px-4 py-4">
                        Kategori
                    </th>
                    <th class="px-4 py-4 min-w-[200px]">
                        Detail
                    </th>
                    <th class="px-4 py-4">
                        Status
                    </th>
                    <th class="px-4 py-4 min-w-[150px]">
                        Keterangan
                    </th>
                    <th class="px-4 py-4">
                        Update
                    </th>
                    <th class="px-4 py-4">
                        Gambar
                    </th>
                    <th class="px-4 py-4">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-white/5">
                @forelse($fasilitas as $item)
                    <tr class="hover:bg-white/[0.025] transition  @if($item->status === 'down') bg-red-500/[0.025] @elseif($item->status === 'maintenance') bg-yellow-500/[0.02] @endif">
                        <td class="px-5 py-4 text-slate-500">
                            {{ $loop->iteration + ($fasilitas->currentPage() - 1) * $fasilitas->perPage() }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-semibold text-white">
                                {{ ucwords($item->nama) }}
                            </div>
                        </td>

                        <td class="px-4 py-4 text-slate-400">
                            {{ ucwords($item->lokasi) }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 rounded-md bg-blue-500/10 text-blue-400 text-xs">
                                {{ ucwords($item->kategori) }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-slate-400">
                            {{ ucwords($item->detail) }}
                        </td>

                        <!-- STATUS -->
                        <td class="px-4 py-4">
                            @if($item->status === 'ready')
                                <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-green-500/10 border border-green-500/10 text-green-400 text-xs font-semibold">
                                    <span class="status-dot bg-green-500"></span>
                                    READY
                                </span>
                            @elseif($item->status === 'maintenance')
                                <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-yellow-500/10 border border-yellow-500/10 text-yellow-400 text-xs font-semibold">
                                    <span class="status-dot bg-yellow-400"></span>
                                    MAINTENANCE
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-red-500/10 border border-red-500/10 text-red-400 text-xs font-semibold">
                                    <span class="status-dot bg-red-500"></span>
                                    DOWN
                                </span>
                            @endif
                        </td>

                        <!-- KETERANGAN -->
                        <td class="px-4 py-4 text-slate-400">
                            {{ $item->keterangan ?? '-' }}
                        </td>

                        <!-- UPDATE -->
                        <td class="px-4 py-4">
                            <div class="text-xs text-slate-300">
                                {{ $item->updated_at->timezone('Asia/Jakarta')->format('d M Y') }}
                            </div>
                            <div class="text-[11px] text-slate-600">
                                {{ $item->updated_at->timezone('Asia/Jakarta')->format('H:i') }}
                            </div>
                        </td>

                        <!-- FOTO -->
                        <td class="px-4 py-4">
                            @if($item->foto)
                                <a href="{{ asset('storage/'.$item->foto) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$item->foto) }}" class="w-14 h-10 object-cover rounded-lg border border-white/10 hover:scale-110 transition">
                                </a>
                            @else
                                <span class="text-slate-600">
                                    -
                                </span>
                            @endif
                        </td>

                        <!-- AKSI -->
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1.5">
<a href="{{ route('fasilitas.show', $item->id) }}" class="bg-slate-600/80 hover:bg-slate-500 text-white px-2.5 py-1.5 rounded-lg text-[11px]">
    Detail
</a>
                                <button onclick="openUpdateModal({{ $item->id }})" class="bg-green-600/80 hover:bg-green-500 text-white px-2.5 py-1.5 rounded-lg text-[11px]">
                                    Update
                                </button>

                                <button type="button" onclick="showHistory({{ $item->id }})" class="bg-blue-600/80 hover:bg-blue-500 text-white px-2.5 py-1.5 rounded-lg text-[11px]">
                                    Histori
                                </button>

                                @if(auth()->user()->role === 'admin')
                                    <form action="/fasilitas/{{ $item->id }}" method="POST" onsubmit="return confirm('Yakin mau hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-600/80 hover:bg-red-500 text-white px-2.5 py-1.5 rounded-lg text-[11px]">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-5 py-16 text-center text-slate-500">
                            Tidak ada data fasilitas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div id="pagination-wrapper" class="px-5 py-5 border-t border-white/5">
        {{ $fasilitas->appends(request()->query())->links('pagination::tailwind') }}

    </div>
</div>

<!-- ========================================================= -->
<!-- HISTORY MODAL -->
<!-- ========================================================= -->

<div id="historyModal" class="fixed inset-0 hidden items-center justify-center bg-black/70 backdrop-blur-sm z-[9999] p-4">
    <div class="bg-slate-900 border border-white/10 rounded-2xl shadow-2xl w-full max-w-5xl max-h-[85vh] overflow-auto">
        <div class="sticky top-0 bg-slate-900 border-b border-white/10 p-5 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-white text-lg">
                    Riwayat Perubahan
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Histori perubahan status fasilitas
                </p>
            </div>

            <button onclick="closeModal()" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400">
                ✕
            </button>
        </div>

        <div class="p-5">
            <a id="downloadHistoryPdf" href="#" target="_blank" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-xs mb-4">
                Download PDF
            </a>

            <div id="historyContent" class="text-slate-300">
                Loading...
            </div>

        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- UPDATE MODAL -->
<!-- ========================================================= -->

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

                        <option value="ready">
                            Ready
                        </option>

                        <option value="maintenance">
                            Maintenance
                        </option>

                        <option value="down">
                            Down
                        </option>

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


<!-- ========================================================= -->
<!-- JAVASCRIPT -->
<!-- ========================================================= -->

<script>

/*
|--------------------------------------------------------------------------
| MAP
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| MAP
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', function () {

    const mapElement = document.getElementById('map');

    if (!mapElement) {
        return;
    }

    const map = L.map('map', {
        zoomControl: true
    }).setView([3.7915, 98.6730], 12);


    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);


    const data = @json($fasilitasMap ?? []);


    /*
    |--------------------------------------------------------------------------
    | Group fasilitas berdasarkan koordinat
    |--------------------------------------------------------------------------
    */

    const groupedLocations = {};


    data.forEach(item => {

        if (!item.latitude || !item.longitude) {
            return;
        }


        const lat = parseFloat(item.latitude);
        const lng = parseFloat(item.longitude);


        if (isNaN(lat) || isNaN(lng)) {
            return;
        }


        /*
        | Gunakan koordinat sebagai identitas lokasi
        */

        const key = `${lat.toFixed(6)},${lng.toFixed(6)}`;


        if (!groupedLocations[key]) {

            groupedLocations[key] = {
                latitude: lat,
                longitude: lng,
                items: []
            };

        }


        groupedLocations[key].items.push(item);

    });


    /*
    |--------------------------------------------------------------------------
    | Helper status
    |--------------------------------------------------------------------------
    */

    function getStatusColor(status) {

        if (status === 'down') {
            return '#ef4444';
        }

        if (status === 'maintenance') {
            return '#facc15';
        }

        return '#22c55e';

    }


    function getStatusText(status) {

        if (status === 'down') {
            return 'DOWN';
        }

        if (status === 'maintenance') {
            return 'MAINTENANCE';
        }

        return 'READY';

    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        if (value === null || value === undefined) {
            return '-';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }


    /*
    |--------------------------------------------------------------------------
    | Tampilkan marker berdasarkan lokasi
    |--------------------------------------------------------------------------
    */

    Object.values(groupedLocations).forEach(location => {

        const items = location.items;


        /*
        |--------------------------------------------------------------------------
        | Tentukan warna marker
        |--------------------------------------------------------------------------
        |
        | Jika ada DOWN → merah
        | Jika tidak ada DOWN tapi ada MAINTENANCE → kuning
        | Selain itu → hijau
        |
        */

        let markerColor = '#22c55e';


        if (
            items.some(item => item.status === 'down')
        ) {

            markerColor = '#ef4444';

        } else if (
            items.some(item => item.status === 'maintenance')
        ) {

            markerColor = '#facc15';

        }


        /*
        |--------------------------------------------------------------------------
        | SATU PERANGKAT
        |--------------------------------------------------------------------------
        */

        if (items.length === 1) {

            const item = items[0];


            const marker = L.circleMarker(
                [
                    location.latitude,
                    location.longitude
                ],
                {
                    radius: 8,
                    color: markerColor,
                    fillColor: markerColor,
                    fillOpacity: 0.9,
                    weight: 2
                }
            ).addTo(map);


            marker.bindPopup(`

                <div style="min-width:220px">

                    <div style="
                        font-weight:700;
                        font-size:15px;
                        margin-bottom:8px;
                    ">
                        ${escapeHtml(
                            item.nama ??
                            item.detail ??
                            'Fasilitas'
                        )}
                    </div>


                    <div style="
                        font-size:12px;
                        margin-bottom:4px;
                    ">
                        <b>Lokasi:</b>
                        ${escapeHtml(item.lokasi)}
                    </div>


                    <div style="
                        font-size:12px;
                        margin-bottom:4px;
                    ">
                        <b>Kategori:</b>
                        ${escapeHtml(item.kategori)}
                    </div>


                    <div style="font-size:12px">

                        <b>Status:</b>

                        <span style="
                            color:${markerColor};
                            font-weight:700;
                        ">
                            ${getStatusText(item.status)}
                        </span>

                    </div>


                    <a
                        href="/fasilitas/${item.id}"
                        style="
                            display:block;
                            margin-top:12px;
                            color:#60a5fa;
                            font-size:12px;
                            font-weight:600;
                            text-decoration:none;
                        "
                    >
                        Lihat Detail →
                    </a>

                </div>

            `);

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | BANYAK PERANGKAT DI LOKASI YANG SAMA
        |--------------------------------------------------------------------------
        */

        const markerIcon = L.divIcon({

            className: '',

            html: `

                <div style="
                    width:34px;
                    height:34px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                ">

                    <div style="
                        width:28px;
                        height:28px;
                        border-radius:50%;
                        background:${markerColor};
                        border:3px solid white;
                        box-shadow:0 2px 8px rgba(0,0,0,0.35);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:white;
                        font-size:12px;
                        font-weight:800;
                    ">
                        ${items.length}
                    </div>

                </div>

            `,

            iconSize: [34, 34],

            iconAnchor: [17, 17],

            popupAnchor: [0, -17]

        });


        /*
        |--------------------------------------------------------------------------
        | Marker tetap di koordinat ASLI
        |--------------------------------------------------------------------------
        */

        const marker = L.marker(
            [
                location.latitude,
                location.longitude
            ],
            {
                icon: markerIcon
            }
        ).addTo(map);


        const locationName =
            items[0].lokasi ??
            'Lokasi Fasilitas';


        /*
        |--------------------------------------------------------------------------
        | Buat daftar perangkat
        |--------------------------------------------------------------------------
        */

        let deviceList = '';


        items.forEach(item => {

            const color = getStatusColor(item.status);

            const statusText = getStatusText(item.status);


            deviceList += `

                <a
                    href="/fasilitas/${item.id}"
                    style="
                        display:block;
                        text-decoration:none;
                        color:inherit;
                        padding:10px;
                        margin-bottom:7px;
                        border:1px solid rgba(148,163,184,0.15);
                        border-radius:10px;
                        background:rgba(15,23,42,0.65);
                    "
                >

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        align-items:flex-start;
                        gap:10px;
                    ">


                        <div style="
                            min-width:0;
                            flex:1;
                        ">

                            <div style="
                                font-weight:700;
                                font-size:13px;
                                margin-bottom:3px;
                            ">
                                ${escapeHtml(
                                    item.nama ??
                                    item.detail ??
                                    'Fasilitas'
                                )}
                            </div>


                            <div style="
                                font-size:11px;
                                color:#94a3b8;
                            ">
                                ${escapeHtml(
                                    item.kategori ?? '-'
                                )}

                                ${
                                    item.subkategori
                                        ? ' • ' +
                                          escapeHtml(
                                              item.subkategori
                                          )
                                        : ''
                                }
                            </div>

                        </div>


                        <span style="
                            flex-shrink:0;
                            color:${color};
                            font-size:10px;
                            font-weight:800;
                            white-space:nowrap;
                        ">
                            ${statusText}
                        </span>


                    </div>

                </a>

            `;

        });


        /*
        |--------------------------------------------------------------------------
        | Popup lokasi
        |--------------------------------------------------------------------------
        */

        marker.bindPopup(`

            <div style="
                width:300px;
                max-width:80vw;
            ">


                <div style="
                    margin-bottom:10px;
                ">


                    <div style="
                        font-size:15px;
                        font-weight:800;
                        margin-bottom:3px;
                    ">
                        ${escapeHtml(locationName)}
                    </div>


                    <div style="
                        font-size:11px;
                        color:#64748b;
                    ">
                        ${items.length} perangkat
                    </div>


                </div>


                <div style="
                    border-top:1px solid rgba(148,163,184,0.2);
                    padding-top:10px;
                    max-height:280px;
                    overflow-y:auto;
                    padding-right:4px;
                ">

                    ${deviceList}

                </div>


            </div>

        `);

    });


    /*
    |--------------------------------------------------------------------------
    | Force Leaflet resize
    |--------------------------------------------------------------------------
    */

    setTimeout(() => {

        map.invalidateSize();

    }, 500);

});

/*
|--------------------------------------------------------------------------
| AUTO REFRESH TABLE
|--------------------------------------------------------------------------
*/

function loadTable() {

    fetch(window.location.href)

        .then(res => res.text())

        .then(html => {

            const newDoc =
                new DOMParser()
                .parseFromString(html, "text/html");


            const oldBody =
                document.querySelector("tbody");

            const newBody =
                newDoc.querySelector("tbody");


            if (oldBody && newBody) {
                oldBody.innerHTML = newBody.innerHTML;
            }


            const oldPagination =
                document.querySelector("#pagination-wrapper");

            const newPagination =
                newDoc.querySelector("#pagination-wrapper");


            if (oldPagination && newPagination) {
                oldPagination.innerHTML =
                    newPagination.innerHTML;
            }

        })

        .catch(error => {
            console.error("Gagal refresh data:", error);
        });

}


/*
|--------------------------------------------------------------------------
| Refresh 5 menit
|--------------------------------------------------------------------------
*/

setInterval(loadTable, 300000);


/*
|--------------------------------------------------------------------------
| HISTORY
|--------------------------------------------------------------------------
*/

async function showHistory(id) {

    document.getElementById('downloadHistoryPdf')
        .href = `/fasilitas/${id}/history-pdf`;


    const modal =
        document.getElementById('historyModal');

    const content =
        document.getElementById('historyContent');


    modal.classList.remove('hidden');
    modal.classList.add('flex');


    content.innerHTML = `
        <div class="text-center py-10 text-slate-500">
            Loading histori...
        </div>
    `;


    try {

        const res =
            await fetch(`/fasilitas/${id}/histories`);


        const data =
            await res.json();


        if (data.length === 0) {

            content.innerHTML = `
                <div class="text-center py-10 text-slate-500">
                    Belum ada histori.
                </div>
            `;

            return;
        }


        let html = `

            <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>

                    <tr class="border-b border-white/10 text-left text-xs text-slate-500">

                        <th class="p-3">Sebelum</th>
                        <th class="p-3">Diupdate</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Keterangan</th>
                        <th class="p-3">Foto</th>

                    </tr>

                </thead>

                <tbody>
        `;


        data.forEach(item => {

            html += `

                <tr class="border-b border-white/5">

                    <td class="p-3 text-xs text-slate-400">

                        ${
                            item.previous_update
                                ? new Date(item.previous_update).toLocaleString()
                                : '-'
                        }

                    </td>


                    <td class="p-3 text-xs text-slate-400">

                        ${new Date(item.created_at).toLocaleString()}

                    </td>


                    <td class="p-3 text-slate-300">

                        ${item.user?.name ?? 'System'}

                    </td>


                    <td class="p-3">

                        ${item.status_from ?? '-'}

                        →

                        <b class="text-white">
                            ${item.status_to}
                        </b>

                    </td>


                    <td class="p-3 text-slate-400">

                        ${item.keterangan ?? '-'}

                    </td>


                    <td class="p-3">

                        ${
                            item.photos.length
                                ? item.photos.map(photo => `

                                    <a
                                        href="/storage/${photo.foto}"
                                        target="_blank"
                                    >

                                        <img
                                            src="/storage/${photo.foto}"
                                            width="60"
                                            class="rounded-lg mb-1"
                                        >

                                    </a>

                                `).join('')
                                : '-'
                        }

                    </td>

                </tr>

            `;

        });


        html += `

                </tbody>

            </table>

            </div>

        `;


        content.innerHTML = html;


    } catch (e) {

        console.error(e);

        content.innerHTML = `
            <div class="text-center py-10 text-red-400">
                Gagal memuat histori.
            </div>
        `;

    }

}


function closeModal() {

    const modal =
        document.getElementById('historyModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');

}


/*
|--------------------------------------------------------------------------
| UPDATE MODAL
|--------------------------------------------------------------------------
*/

function openUpdateModal(id) {

    const modal =
        document.getElementById('updateModal');


    modal.classList.remove('hidden');
    modal.classList.add('flex');


    document.getElementById('fasilitas_id')
        .value = id;

}


function closeUpdateModal() {

    const modal =
        document.getElementById('updateModal');


    modal.classList.add('hidden');
    modal.classList.remove('flex');

}


/*
|--------------------------------------------------------------------------
| UPDATE DATA
|--------------------------------------------------------------------------
*/

document
    .getElementById('updateForm')
    .addEventListener('submit', async function(e) {

        e.preventDefault();


        const id =
            document.getElementById('fasilitas_id').value;


        let formData =
            new FormData();


        formData.append(
            'status',
            document.getElementById('status').value
        );


        formData.append(
            'keterangan',
            document.getElementById('keterangan').value
        );


        const fotoInputs =
            document.querySelectorAll(
                'input[name="foto[]"]'
            );


        fotoInputs.forEach(input => {

            if (input.files[0]) {

                formData.append(
                    'foto[]',
                    input.files[0]
                );

            }

        });


        formData.append(
            '_token',
            '{{ csrf_token() }}'
        );


        try {

            const res =
                await fetch(
                    `/fasilitas/update/${id}`,
                    {
                        method: 'POST',
                        body: formData
                    }
                );


            const data =
                await res.json();


            if (data.success) {

                location.reload();

            } else {

                alert('Gagal update fasilitas.');

            }

        } catch (error) {

            console.error(error);

            alert('Terjadi kesalahan saat update.');

        }

    });


/*
|--------------------------------------------------------------------------
| TAMBAH FOTO
|--------------------------------------------------------------------------
*/
function tambahFoto() {

    const container =
        document.getElementById('foto-container');

    const total =
        container.querySelectorAll('input').length;

    if (total >= 5) {

        alert('Maksimal 5 foto');

        return;

    }

    const input =
        document.createElement('input');

    input.type = 'file';

    input.name = 'foto[]';

    input.className =
        'w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-sm text-slate-400 mb-2';

    container.appendChild(input);

}


/*
|--------------------------------------------------------------------------
| EXPORT EXCEL BULANAN
|--------------------------------------------------------------------------
*/

document.getElementById('btnExportExcel').addEventListener('click', function (e) {

    e.preventDefault();

    const bulan =
        document.getElementById('export_bulan').value;

    const kategori =
        document.getElementById('export_kategori').value;

    const nama =
        document.getElementById('export_nama').value;

    if (!bulan) {

        alert('Silakan pilih periode laporan terlebih dahulu.');

        return;

    }

    const params = new URLSearchParams();

    params.append('bulan', bulan);

    if (kategori) {
        params.append('kategori', kategori);
    }

    if (nama) {
        params.append('nama', nama);
    }

    window.location.href =
        "{{ route('export.monthly.excel') }}?" + params.toString();

});


</script>

</x-app-layout>