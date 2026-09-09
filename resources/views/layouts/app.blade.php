<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Monitoring') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('logo-web.jpeg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Leaflet -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            background: #020617;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: #020617;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-track {
            background: #020617;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        .glass-card {
        background: rgba(15, 23, 42, 0.78);
        border: 1px solid rgba(148, 163, 184, 0.12);
        box-shadow:
        0 10px 30px rgba(0, 0, 0, 0.18),
        inset 0 1px 0 rgba(255,255,255,0.025);

        transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease,
        transform 0.2s ease;
        }

.glass-card:hover {
        border-color: rgba(96, 165, 250, 0.20);
        box-shadow:
        0 14px 35px rgba(0, 0, 0, 0.22),
        inset 0 1px 0 rgba(255,255,255,0.035);
    }

        .sidebar-gradient {
            background:
                radial-gradient(
                    circle at 0% 0%,
                    rgba(30, 64, 175, 0.20),
                    transparent 35%
                ),
                linear-gradient(
                    180deg,
                    #071426 0%,
                    #020b17 100%
                );
        }

        .main-gradient {
            background:
                radial-gradient(
                    circle at 75% 0%,
                    rgba(30, 64, 175, 0.10),
                    transparent 30%
                ),
                #020817;
        }

        .status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        .leaflet-container {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>

<body class="antialiased text-slate-200">

<div
    x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
    class="min-h-screen main-gradient"
>
    <!-- Mobile overlay -->
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
        style="display:none;"
    ></div>

    <!-- SIDEBAR -->
    @include('layouts.navigation')

    <!-- MAIN CONTENT -->
<div
    class="min-h-screen transition-all duration-300 ease-in-out"
    :class="sidebarCollapsed ? 'lg:ml-0' : 'lg:ml-64'"
>

        <!-- TOP HEADER -->
        <header class="sticky top-0 z-30 h-20 bg-slate-950/85 backdrop-blur-xl border-b border-white/5">

            <div class="h-full px-5 lg:px-8 flex items-center justify-between">

                <!-- LEFT -->
                <div class="flex items-center gap-4">

                    <!-- Mobile menu -->
                    <button
                        @click="sidebarOpen = true"
                        class="lg:hidden w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div>
                        <h1 class="text-xl lg:text-2xl font-bold text-white">
    @yield('page-title', 'Dashboard')
</h1>

<p class="text-xs lg:text-sm text-slate-400">
    @yield('page-description', 'Monitoring Kesiapan Teknik, Operasional & Perangkat Pendukung')
</p>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="flex items-center gap-3 lg:gap-5">

                    <!-- Date -->
                    <div class="hidden xl:flex items-center gap-2 text-sm text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>

                        {{ now()->timezone('Asia/Jakarta')->format('d M Y') }}
                    </div>

                    <!-- Time -->
                    <div
                        class="hidden md:flex items-center gap-2 text-sm text-slate-400"
                        x-data="{
                            time: new Date().toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            })
                        }"
                        x-init="
                            setInterval(() => {
                                time = new Date().toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                })
                            }, 1000)
                        "
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>

                        <span x-text="time"></span> WIB
                    </div>

                    <!-- Notification -->
                    <button class="relative w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>

                        @php
                            $downCount = collect($fasilitasMap ?? [])
                                ->where('status', 'down')
                                ->count();

                            $maintenanceCount = collect($fasilitasMap ?? [])
                                ->where('status', 'maintenance')
                                ->count();

                            $alertCount = $downCount + $maintenanceCount;
                        @endphp

                        @if($alertCount > 0)
                            <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">
                                {{ $alertCount }}
                            </span>
                        @endif
                    </button>

                    <!-- User -->
                    <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-white/10">

                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden lg:block">
                            <p class="text-sm font-semibold text-white">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ Auth::user()->role ?? 'User' }}
                            </p>
                        </div>

                    </div>

                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="p-4 lg:p-7">
            {{ $slot }}
        </main>

    </div>
</div>

<!-- Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

</body>
</html>