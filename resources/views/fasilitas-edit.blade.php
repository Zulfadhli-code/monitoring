<x-app-layout>

    @section('page-title', 'Edit Fasilitas')
    @section('page-description', 'Perubahan data master fasilitas')

    <div class="p-5 lg:p-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Edit Fasilitas
                </h1>

                <p class="text-sm text-slate-400 mt-1">
                    {{ $fasilitas->nama }}
                </p>
            </div>

            <a
                href="{{ route('fasilitas.show', $fasilitas->id) }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-xl
                       bg-white/5 hover:bg-white/10 border border-white/10
                       text-slate-300 text-sm transition"
            >
                ← Kembali
            </a>

        </div>


        {{-- FORM --}}
        <div class="glass-card rounded-2xl p-5 lg:p-6">

           <form
    method="POST"
    action="{{ route('fasilitas.update', $fasilitas->id) }}"
    enctype="multipart/form-data"
>

    @csrf
    @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- NAMA --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Nama Fasilitas
                        </label>

                        <input
                            type="text"
                            name="nama"
                            value="{{ old('nama', $fasilitas->nama) }}"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >
                    </div>


                    {{-- LOKASI --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Lokasi
                        </label>

                        <select
                            name="lokasi"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >
                            @foreach([
                                'Belawan Lama',
                                'Ujung Baru',
                                'IKD',
                                'Dermaga Citra',
                                'Gate 01',
                                'Gate 02',
                                'Gate 03',
                                'Gate 04',
                                'Gate 05',
                            ] as $lokasi)

                                <option
                                    value="{{ $lokasi }}"
                                    @selected($fasilitas->lokasi === $lokasi)
                                >
                                    {{ $lokasi }}
                                </option>

                            @endforeach
                        </select>
                    </div>


                    {{-- KATEGORI --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Kategori
                        </label>

                        <select
                            name="kategori"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >

                            @foreach([
                                'Kesiapan Teknik',
                                'Kesiapan Operasional',
                                'Perangkat Pendukung',
                            ] as $kategori)

                                <option
                                    value="{{ $kategori }}"
                                    @selected($fasilitas->kategori === $kategori)
                                >
                                    {{ $kategori }}
                                </option>

                            @endforeach

                        </select>
                    </div>


                    {{-- SUBKATEGORI --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Subkategori
                        </label>

                        <select
                            name="subkategori"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >

                            @foreach([
                                'CCTV',
                                'Access Control',
                                'Intercom',
                                'Printer',
                                'Network',
                                'Lainnya',
                            ] as $subkategori)

                                <option
                                    value="{{ $subkategori }}"
                                    @selected($fasilitas->subkategori === $subkategori)
                                >
                                    {{ $subkategori }}
                                </option>

                            @endforeach

                        </select>
                    </div>


                    {{-- STATUS --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Status
                        </label>

                        <select
                            name="status"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >

                            <option
                                value="ready"
                                @selected($fasilitas->status === 'ready')
                            >
                                Ready
                            </option>

                            <option
                                value="maintenance"
                                @selected($fasilitas->status === 'maintenance')
                            >
                                Maintenance
                            </option>

                            <option
                                value="down"
                                @selected($fasilitas->status === 'down')
                            >
                                Down
                            </option>

                        </select>
                    </div>


                    {{-- LATITUDE --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Latitude
                        </label>

                        <input
                            type="text"
                            name="latitude"
                            value="{{ old('latitude', $fasilitas->latitude) }}"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >
                    </div>


                    {{-- LONGITUDE --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Longitude
                        </label>

                        <input
                            type="text"
                            name="longitude"
                            value="{{ old('longitude', $fasilitas->longitude) }}"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >
                    </div>


                    {{-- DETAIL --}}
                    <div class="md:col-span-2">

                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Detail
                        </label>

                        <textarea
                            name="detail"
                            rows="4"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >{{ old('detail', $fasilitas->detail) }}</textarea>

                    </div>


                    {{-- KETERANGAN --}}
                    <div class="md:col-span-2">

                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            rows="4"
                            class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-slate-200"
                        >{{ old('keterangan', $fasilitas->keterangan) }}</textarea>

                    </div>

                </div>


                {{-- FOTO --}}
                <div class="mt-6 pt-6 border-t border-white/10">

                    <div class="flex items-center justify-between mb-4">

                        <div>
                            <h2 class="text-lg font-semibold text-white">
                                Foto Fasilitas
                            </h2>

                            <p class="text-xs text-slate-500 mt-1">
                                Tambahkan foto baru jika diperlukan.
                            </p>
                        </div>

                        <span class="text-xs text-slate-500">
                            {{ $fasilitas->photos->count() }} foto
                        </span>

                    </div>


                    @if($fasilitas->photos->count())

                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-5">

                            @foreach($fasilitas->photos as $photo)

                                <div>

                                    <img
                                        src="{{ asset('storage/'.$photo->foto) }}"
                                        alt="{{ $fasilitas->nama }}"
                                        class="w-full h-28 object-cover rounded-xl border border-white/10"
                                    >

                                </div>

                            @endforeach

                        </div>

                    @endif


                    <input
                        type="file"
                        name="foto[]"
                        multiple
                        accept=".jpg,.jpeg,.png"
                        class="w-full bg-slate-950 border border-white/10 rounded-xl p-3 text-sm text-slate-400"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        Maksimal 5 foto. Format JPG, JPEG, atau PNG.
                    </p>

                </div>


                {{-- FOOTER --}}
                <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-white/10">

                    <a
                        href="{{ route('fasilitas.show', $fasilitas->id) }}"
                        class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10
                               border border-white/10 text-slate-300 text-sm"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500
                               text-white text-sm font-semibold"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>