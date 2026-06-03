<nav x-data="{ open: false }"
     class="backdrop-blur-xl bg-white/10 border-b border-white/20 text-white">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT SIDE -->
            <div class="flex">

                <!-- Logo -->
            

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    <x-nav-link :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')"
                        class="text-black hover:text-blue-30">
                        <!-- {{ __('BRANCH BELAWAN') }} -->
                    </x-nav-link>

                </div>
            </div>

            <!-- RIGHT SIDE (USER MENU) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-white/20 text-sm rounded-md text-white bg-white/10 hover:bg-white/20 transition">

                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                        </button>
                    </x-slot>

                    <x-slot name="content"
                        class="bg-slate-900 text-white border border-white/20">

                        <x-dropdown-link :href="route('profile.edit')"
                            class="text-black hover:bg-white/10">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-black hover:bg-white/10">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>

                    </x-slot>
                </x-dropdown>

            </div>

            <!-- MOBILE BUTTON -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="p-2 rounded-md text-white hover:bg-white/10">

                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>

                </button>
            </div>

        </div>
    </div>

    <!-- MOBILE MENU -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <div class="pt-2 pb-3 space-y-1 text-white">

            <x-responsive-nav-link :href="route('dashboard')"
                :active="request()->routeIs('dashboard')"
                class="text-white">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

        </div>

        <div class="pt-4 pb-1 border-t border-white/20">

            <div class="px-4">
                <div class="font-medium text-base text-white">
                    {{ Auth::user()->name }}
                </div>
                <div class="font-medium text-sm text-white/70">
                    {{ Auth::user()->email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">

                <x-responsive-nav-link :href="route('profile.edit')" class="text-white">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="text-white">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>

                </form>

            </div>
        </div>

    </div>

</nav>