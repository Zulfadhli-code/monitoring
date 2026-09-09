<x-app-layout>

    @section('page-title', 'Detail Fasilitas')
    @section('page-description', 'Informasi lengkap fasilitas dan kondisi terkini')

    <div class="p-5 lg:p-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    {{ $fasilitas->nama }}
                </h1>

                <p class="text-sm text-slate-400 mt-1">
                    Detail informasi fasilitas
                </p>
            </div>

            @if(auth()->user()->role === 'admin')
                <a
                    href="{{ route('fasilitas.edit', $fasilitas->id) }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl
                    bg-blue-600 hover:bg-blue-500
                    text-white text-sm font-semibold transition"
                >
                    ✏️ Edit
                </a>
            @endif

    

        </div>


        {{-- INFORMASI + STATUS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- INFORMASI --}}
            <div class="lg:col-span-2 glass-card rounded-2xl p-5">

                <h2 class="text-lg font-semibold text-white mb-5">
                    Informasi Fasilitas
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Nama
                        </p>

                        <p class="text-sm text-white">
                            {{ $fasilitas->nama ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Lokasi
                        </p>

                        <p class="text-sm text-white">
                            {{ $fasilitas->lokasi ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Kategori
                        </p>

                        <p class="text-sm text-white">
                            {{ $fasilitas->kategori ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Subkategori
                        </p>

                        <p class="text-sm text-white">
                            {{ $fasilitas->subkategori ?: '-' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-500 mb-1">
                            Detail
                        </p>

                        <p class="text-sm text-slate-300 whitespace-pre-line">
                            {{ $fasilitas->detail ?: '-' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-500 mb-1">
                            Keterangan Terakhir
                        </p>

                        <p class="text-sm text-slate-300 whitespace-pre-line">
                            {{ $fasilitas->keterangan ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Koordinat
                        </p>

                        <p class="text-sm text-white">
                            {{ $fasilitas->latitude ?: '-' }},
                            {{ $fasilitas->longitude ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Terakhir diperbarui
                        </p>

                        <p class="text-sm text-white">
                            {{ $fasilitas->updated_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}
                        </p>
                    </div>

                </div>

            </div>


            {{-- STATUS --}}
            <div class="glass-card rounded-2xl p-5">

                <h2 class="text-lg font-semibold text-white mb-5">
                    Status Saat Ini
                </h2>

                @if($fasilitas->status === 'ready')

                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                                bg-green-500/10 border border-green-500/20
                                text-green-400 font-semibold">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                        READY
                    </div>

                @elseif($fasilitas->status === 'maintenance')

                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                                bg-yellow-500/10 border border-yellow-500/20
                                text-yellow-400 font-semibold">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                        MAINTENANCE
                    </div>

                @else

                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                                bg-red-500/10 border border-red-500/20
                                text-red-400 font-semibold">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                        DOWN
                    </div>

                @endif

                <div class="mt-6 pt-5 border-t border-white/10">

                    <p class="text-xs text-slate-500 mb-1">
                        Diperbarui oleh
                    </p>

                    <p class="text-sm text-white">
                        {{ $fasilitas->updater->name ?? '-' }}
                    </p>

                </div>

            </div>

        </div>




        {{-- FOTO --}}
        <div class="glass-card rounded-2xl p-5 mt-5">

            <div class="flex items-center justify-between mb-5">

                <h2 class="text-lg font-semibold text-white">
                    Foto Fasilitas
                </h2>

                <span class="text-xs text-slate-500">
                    {{ $fasilitas->photos->count() }} foto
                </span>

            </div>

            @if($fasilitas->photos->count())

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">

                   @foreach($fasilitas->photos as $photo)

    <div class="relative group">

        <a
            href="{{ asset('storage/'.$photo->foto) }}"
            target="_blank"
            class="block"
        >
            <img
                src="{{ asset('storage/'.$photo->foto) }}"
                alt="{{ $fasilitas->nama }}"
                class="w-full h-32 object-cover rounded-xl
                       border border-white/10
                       group-hover:scale-[1.02] transition"
            >
        </a>

        @if(auth()->user()->role === 'admin')

    <form
        action="{{ route('fasilitas.photo.delete', $photo->id) }}"
        method="POST"
        class="absolute top-2 right-2"
        onsubmit="return confirm('Yakin ingin menghapus foto ini?')"
    >
        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="px-2.5 py-1.5 rounded-lg
                   bg-red-600/90 hover:bg-red-500
                   text-white text-xs font-semibold
                   shadow-lg transition"
        >
            🗑️ Hapus
        </button>
    </form>

@endif

    </div>

@endforeach

                </div>

            @elseif($fasilitas->foto)

                <a
                    href="{{ asset('storage/'.$fasilitas->foto) }}"
                    target="_blank"
                >
                    <img
                        src="{{ asset('storage/'.$fasilitas->foto) }}"
                        alt="{{ $fasilitas->nama }}"
                        class="w-40 h-28 object-cover rounded-xl border border-white/10"
                    >
                </a>

            @else

                <div class="py-10 text-center text-slate-500">
                    Belum ada foto fasilitas.
                </div>

            @endif

        </div>

{{-- RIWAYAT KONDISI --}}
<div class="glass-card rounded-2xl p-5 mt-5">

    <div class="flex items-center justify-between mb-5">

        <div>
            <h2 class="text-lg font-semibold text-white">
                Riwayat Kondisi
            </h2>

            <p class="text-xs text-slate-500 mt-1">
                Riwayat perubahan status fasilitas
            </p>
        </div>

        <span class="text-xs text-slate-500">
            {{ $fasilitas->histories->count() }} riwayat
        </span>

    </div>

    @forelse($fasilitas->histories as $history)

        <div class="relative pl-6 pb-6 last:pb-0">

            {{-- GARIS TIMELINE --}}
            @if(!$loop->last)
                <div class="absolute left-[7px] top-3 bottom-0 w-px bg-white/10"></div>
            @endif

            {{-- DOT --}}
            <div class="absolute left-0 top-1.5 w-3.5 h-3.5 rounded-full
                        bg-blue-500 border-4 border-slate-900">
            </div>

            <div class="bg-white/[0.03] border border-white/5 rounded-xl p-4">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                    <div class="flex items-center gap-2 flex-wrap">

                        @if($history->status_from)

                            <span class="px-2.5 py-1 rounded-lg bg-white/10 text-slate-300 text-xs">
                                {{ strtoupper($history->status_from) }}
                            </span>

                            <span class="text-slate-500">
                                →
                            </span>

                        @endif

                        <span class="px-2.5 py-1 rounded-lg bg-white/10 text-white text-xs">
                            {{ strtoupper($history->status_to) }}
                        </span>

                    </div>

                    <span class="text-xs text-slate-500">
                        {{ $history->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                    </span>

                </div>

                @if($history->keterangan)

                    <p class="text-sm text-slate-300 mt-3 whitespace-pre-line">
                        {{ $history->keterangan }}
                    </p>

                @endif

                <p class="text-xs text-slate-500 mt-3">
                    Oleh:
                    <span class="text-slate-300">
                        {{ $history->user->name ?? '-' }}
                    </span>
                </p>

            </div>

        </div>

    @empty

        <div class="py-10 text-center text-slate-500">
            Belum ada riwayat kondisi.
        </div>

    @endforelse

</div>


{{-- AUDIT PERUBAHAN --}}
<div class="glass-card rounded-2xl p-5 mt-5">

    <div class="flex items-center justify-between mb-5">

        <div>
            <h2 class="text-lg font-semibold text-white">
                Audit Perubahan
            </h2>

            <p class="text-xs text-slate-500 mt-1">
                Catatan perubahan data fasilitas
            </p>
        </div>

        <span class="text-xs text-slate-500">
            {{ $auditLogs->count() }} perubahan
        </span>

    </div>

    @forelse($auditLogs as $log)

        <div class="border-b border-white/5 last:border-0 py-4 first:pt-0 last:pb-0">

            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">

                <div>

                    <div class="flex items-center gap-2 flex-wrap">

                        <span class="px-2.5 py-1 rounded-lg bg-blue-500/10
                                     border border-blue-500/20 text-blue-400 text-xs font-medium">
                            {{ ucfirst($log->field) }}
                        </span>

                        <span class="text-xs text-slate-500">
                            diubah oleh
                        </span>

                        <span class="text-xs text-white font-medium">
                            {{ $log->user->name ?? 'User tidak ditemukan' }}
                        </span>

                    </div>

                    <div class="flex items-center gap-2 flex-wrap mt-3">

                        <span class="px-2.5 py-1 rounded-lg bg-white/10 text-slate-300 text-xs max-w-xs break-words">
                            {{ $log->old_value ?? '(kosong)' }}
                        </span>

                        <span class="text-slate-500">
                            →
                        </span>

                        <span class="px-2.5 py-1 rounded-lg bg-white/10 text-white text-xs max-w-xs break-words">
                            {{ $log->new_value ?? '(kosong)' }}
                        </span>

                    </div>

                </div>

                <span class="text-xs text-slate-500 whitespace-nowrap">
                    {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                </span>

            </div>

        </div>

    @empty

        <div class="py-10 text-center text-slate-500">
            Belum ada audit perubahan.
        </div>

    @endforelse

</div>

    </div>

</x-app-layout>