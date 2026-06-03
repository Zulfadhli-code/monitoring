<x-guest-layout>

<div class="relative min-h-screen w-full overflow-hidden flex items-center justify-center">
<link rel="icon" type="image/png" href="{{ asset('logo-web.jpeg') }}">
    <!-- BACKGROUND FULL SCREEN -->
    <div class="fixed inset-0">
        <img src="{{ asset('logo-bg.jpg') }}"
             class="w-full h-full object-cover"
             alt="background">
    </div>

    <!-- OVERLAY GELAP -->
    <div class="fixed inset-0 bg-black/60"></div>

  
    <!-- LOGIN BOX -->
    <div class="relative w-full max-w-md">

        <div class="backdrop-blur-xl bg-white/10 border border-white/20 shadow-2xl rounded-2xl p-8 text-white">

            <!-- LOGO -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('logo-pelindo.jpg') }}"
                     class="w-20 h-20 object-contain drop-shadow-lg"
                     alt="Logo">
            </div>

            <!-- TITLE -->
            <h2 class="text-2xl font-bold text-center">
                Monitoring Kesiapan Teknik
            </h2>

            <!-- FORM -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="email" value="Email" class="text-white" />
                    <x-text-input id="email"
                        class="block mt-1 w-full bg-white/10 border-white/20 text-white"
                        type="email"
                        name="email"
                        required autofocus />
                </div>

                <div>
                    <x-input-label for="password" value="Password" class="text-white" />
                    <x-text-input id="password"
                        class="block mt-1 w-full bg-white/10 border-white/20 text-white"
                        type="password"
                        name="password"
                        required />
                </div>

                <button type="submit"
                    class="w-full py-2 rounded-lg bg-blue-500 hover:bg-blue-600 font-bold">
                    LOGIN
                </button>

            </form>

        </div>

    </div>

</div>

</x-guest-layout>