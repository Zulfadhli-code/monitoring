<aside
    x-data
    :class="sidebarOpen
        ? 'translate-x-0'
        : (sidebarCollapsed
            ? '-translate-x-full'
            : '-translate-x-full lg:translate-x-0')"
    class="fixed left-0 top-0 z-50 h-screen w-64
           sidebar-gradient border-r border-white/5
           transition-transform duration-300 ease-in-out"
>

    <div class="flex h-full flex-col">
<!-- DESKTOP SIDEBAR TOGGLE -->
<button
    @click="sidebarCollapsed = !sidebarCollapsed"
    class="hidden lg:flex absolute -right-4 top-8
           w-8 h-8 rounded-full
           bg-slate-800 border border-white/10
           text-slate-300 hover:text-white
           hover:bg-slate-700
           items-center justify-center
           shadow-lg transition"
    title="Sembunyikan / tampilkan sidebar"
>
    <span
        x-text="sidebarCollapsed ? '›' : '‹'"
        class="text-xl leading-none"
    ></span>
</button>
        <!-- LOGO -->
        <div class="flex h-20 items-center px-6 border-b border-white/5">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-xl bg-white/10
                            flex items-center justify-center overflow-hidden">

                    <img
                        src="{{ asset('logo-web.jpeg') }}"
                        alt="Logo"
                        class="w-8 h-8 object-contain"
                    >

                </div>

                <div>
                    <div class="text-sm font-bold text-white">
                        SPMT
                    </div>

                    <div class="text-[10px] text-slate-400">
                        Branch Belawan
                    </div>
                </div>

            </a>

            <!-- MOBILE CLOSE -->
            <button
                @click="sidebarOpen = false"
                class="ml-auto lg:hidden text-slate-400 hover:text-white"
            >
                ✕
            </button>

        </div>


        <!-- SYSTEM STATUS -->
        <div class="px-4 pt-5">

            <div class="rounded-xl bg-emerald-500/10
                        border border-emerald-500/10
                        px-3 py-2">

                <div class="flex items-center gap-2">

                    <span class="status-dot bg-emerald-400"></span>

                    <span class="text-xs font-medium text-emerald-400">
                        System Online
                    </span>

                </div>

                <p class="mt-1 text-[10px] text-slate-500">
                    Monitoring aktif
                </p>

            </div>

        </div>


        <!-- MENU -->
        <div class="flex-1 overflow-y-auto px-4 py-6">

            <p class="px-3 mb-3 text-[10px] font-bold
                      uppercase tracking-widest text-slate-500">
                Menu Utama
            </p>


            <!-- DASHBOARD -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl
               {{ request()->routeIs('dashboard')
                    ? 'bg-blue-600/20 text-blue-400 border border-blue-500/20'
                    : 'text-slate-400 hover:bg-white/5 hover:text-white' }}
               transition">

                <svg class="w-5 h-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-4H3v4zm10-14h8V3h-8v4z"/>

                </svg>

                <span class="text-sm font-medium">
                    Dashboard
                </span>

            </a>


            <!-- KESIAPAN TEKNIK -->
            <a href="{{ route('kesiapan.teknik') }}"
                class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl
          {{ request()->routeIs('kesiapan.teknik')
              ? 'bg-blue-600/20 text-blue-400 border border-blue-500/20'
              : 'text-slate-400 hover:bg-white/5 hover:text-white' }}
          transition">
                <svg class="w-5 h-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M10.5 6h3m-1.5-3v3m-6 4.5h12M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2z"/>

                </svg>

                <span class="text-sm font-medium">
                    Kesiapan Teknik
                </span>

            </a>


            <!-- KESIAPAN OPERASIONAL -->
<a href="{{ route('kesiapan.operasional') }}"
   class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl
          {{ request()->routeIs('kesiapan.operasional')
              ? 'bg-blue-600/20 text-blue-400 border border-blue-500/20'
              : 'text-slate-400 hover:bg-white/5 hover:text-white' }}
          transition">
                <svg class="w-5 h-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 17v-2a4 4 0 014-4h4m0 0V7m0 4l-3-3m3 3l3-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>

                </svg>

                <span class="text-sm font-medium">
                    Kesiapan Operasional
                </span>

            </a>

<!-- PERANGKAT PENDUKUNG -->
<a href="{{ route('perangkat.pendukung') }}"
   class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl
          {{ request()->routeIs('perangkat.pendukung')
              ? 'bg-blue-600/20 text-blue-400 border border-blue-500/20'
              : 'text-slate-400 hover:bg-white/5 hover:text-white' }}
          transition">
                <svg class="w-5 h-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m18 0h-2M5 15H3m18 0h-2M7 5h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z"/>

                </svg>

                <span class="text-sm font-medium">
                    Perangkat Pendukung
                </span>

            </a>
            {{-- AUDIT LOG - ADMIN ONLY --}}
@if(auth()->user()->role === 'admin')

    <a href="{{ route('audit.logs') }}"
       class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl
              {{ request()->routeIs('audit.logs')
                  ? 'bg-blue-600/20 text-blue-400 border border-blue-500/20'
                  : 'text-slate-400 hover:bg-white/5 hover:text-white' }}
              transition">

        <svg class="w-5 h-5"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                     M9 5a2 2 0 002 2h2a2 2 0 002-2
                     M9 12h6
                     M9 16h6"/>
        </svg>

        <span class="text-sm font-medium">
            Audit Log
        </span>

    </a>

@endif


            <!-- DIVIDER -->
            <div class="my-6 border-t border-white/5"></div>


            <p class="px-3 mb-3 text-[10px] font-bold
                      uppercase tracking-widest text-slate-500">
                Monitoring
            </p>


            <!-- LAPORAN -->
            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                      text-slate-400 hover:bg-white/5 hover:text-white transition">

                <svg class="w-5 h-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h8l4 4v10a2 2 0 01-2 2z"/>

                </svg>

                <span class="text-sm">
                    Laporan
                </span>

            </a>


            <!-- RIWAYAT -->
            <a href="#"
               class="mt-1 flex items-center gap-3 px-3 py-2.5 rounded-xl
                      text-slate-400 hover:bg-white/5 hover:text-white transition">

                <svg class="w-5 h-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>

                </svg>

                <span class="text-sm">
                    Riwayat
                </span>

            </a>

        </div>


        <!-- USER -->
        <div class="border-t border-white/5 p-4">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-xl
                            bg-blue-600/20 border border-blue-500/20
                            flex items-center justify-center
                            text-blue-400 font-bold">

                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                </div>

                <div class="flex-1 min-w-0">

                    <p class="text-sm font-semibold text-white truncate">
                        {{ Auth::user()->name }}
                    </p>

                    <p class="text-xs text-slate-500 capitalize">
                        {{ Auth::user()->role ?? 'User' }}
                    </p>

                </div>

                <!-- LOGOUT -->
                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button
                        type="submit"
                        title="Logout"
                        class="text-slate-500 hover:text-red-400 transition"
                    >

                        <svg class="w-5 h-5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v1"/>

                        </svg>

                    </button>

                </form>

            </div>

        </div>

    </div>

</aside>