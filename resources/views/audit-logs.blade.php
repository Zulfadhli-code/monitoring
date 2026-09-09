<x-app-layout>

    @section('page-title', 'Audit Log')
    @section('page-description', 'Riwayat perubahan data fasilitas oleh pengguna')

    <div class="p-5 lg:p-8">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">
                Audit Log
            </h1>

            <p class="text-sm text-slate-400 mt-1">
                Riwayat perubahan data fasilitas
            </p>
        </div>

        {{-- TABLE --}}
        <div class="glass-card rounded-2xl overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th class="px-5 py-4 text-left text-slate-400 font-medium">
                                Waktu
                            </th>

                            <th class="px-5 py-4 text-left text-slate-400 font-medium">
                                User
                            </th>

                            <th class="px-5 py-4 text-left text-slate-400 font-medium">
                                Fasilitas
                            </th>

                            <th class="px-5 py-4 text-left text-slate-400 font-medium">
                                Perubahan
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/5">

                        @forelse ($auditLogs as $log)

                            <tr class="hover:bg-white/5 transition">

                                {{-- WAKTU --}}
                                <td class="px-5 py-4 text-slate-300 whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </td>

                                {{-- USER --}}
                                <td class="px-5 py-4">
                                    <div class="text-white font-medium">
                                        {{ $log->user->name ?? 'User tidak ditemukan' }}
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        {{ $log->user->role ?? '-' }}
                                    </div>
                                </td>

                                {{-- FASILITAS --}}
                                <td class="px-5 py-4">
                                    <div class="text-white font-medium">
                                        {{ $log->fasilitas->nama ?? 'Fasilitas tidak ditemukan' }}
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        {{ $log->fasilitas->lokasi ?? '-' }}
                                    </div>
                                </td>

                                {{-- PERUBAHAN --}}
                                <td class="px-5 py-4">

                                    <div class="text-xs text-slate-400 mb-1">
                                        {{ ucfirst($log->field) }}
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">

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

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="px-5 py-10 text-center text-slate-500">
                                    Belum ada aktivitas audit.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if ($auditLogs->hasPages())
                <div class="px-5 py-4 border-t border-white/10">
                    {{ $auditLogs->links() }}
                </div>
            @endif

        </div>

    </div>

</x-app-layout>