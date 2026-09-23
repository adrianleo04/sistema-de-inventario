<nav x-data="{ open: false }" class="bg-indigo-900 border-b border-indigo-800 text-white shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 font-bold text-xl tracking-wider text-white">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>InvMipyme</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-indigo-200">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @role('Super Administrador')
                    <x-nav-link :href="route('empresas.index')" :active="request()->routeIs('empresas.*')" class="text-white hover:text-indigo-200">
                        {{ __('Empresas') }}
                    </x-nav-link>
                    @endrole

                    @can('sucursales.ver')
                    <x-nav-link :href="route('sucursales.index')" :active="request()->routeIs('sucursales.*')" class="text-white hover:text-indigo-200">
                        {{ __('Sucursales') }}
                    </x-nav-link>
                    @endcan

                    @can('areas.ver')
                    <x-nav-link :href="route('areas.index')" :active="request()->routeIs('areas.*')" class="text-white hover:text-indigo-200">
                        {{ __('Áreas') }}
                    </x-nav-link>
                    @endcan

                    @can('items.ver')
                    <x-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')" class="text-white hover:text-indigo-200">
                        {{ __('Catálogo (Ítems)') }}
                    </x-nav-link>
                    @endcan

                    @can('catalogos.ver')
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-800 hover:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                                    <span>{{ __('Tablas Catálogo') }}</span>
                                    <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('categorias.index')">Categorías</x-dropdown-link>
                                <x-dropdown-link :href="route('unidades.index')">Unidades de Medida</x-dropdown-link>
                                <x-dropdown-link :href="route('proveedores.index')">Proveedores</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-800 hover:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex flex-col text-left">
                                <span class="font-bold">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-indigo-300">
                                    {{ Auth::user()->roles->pluck('name')->first() ?? 'Usuario' }}
                                    @if(Auth::user()->empresa)
                                        - {{ Auth::user()->empresa->nombre }}
                                    @endif
                                </span>
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-indigo-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-indigo-950">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @role('Super Administrador')
            <x-responsive-nav-link :href="route('empresas.index')" :active="request()->routeIs('empresas.*')" class="text-white">
                {{ __('Empresas') }}
            </x-responsive-nav-link>
            @endrole

            @can('sucursales.ver')
            <x-responsive-nav-link :href="route('sucursales.index')" :active="request()->routeIs('sucursales.*')" class="text-white">
                {{ __('Sucursales') }}
            </x-responsive-nav-link>
            @endcan

            @can('areas.ver')
            <x-responsive-nav-link :href="route('areas.index')" :active="request()->routeIs('areas.*')" class="text-white">
                {{ __('Áreas') }}
            </x-responsive-nav-link>
            @endcan

            @can('items.ver')
            <x-responsive-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')" class="text-white">
                {{ __('Catálogo (Ítems)') }}
            </x-responsive-nav-link>
            @endcan

            @can('catalogos.ver')
            <x-responsive-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')" class="text-white">
                {{ __('Categorías') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('unidades.index')" :active="request()->routeIs('unidades.*')" class="text-white">
                {{ __('Unidades de Medida') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('proveedores.index')" :active="request()->routeIs('proveedores.*')" class="text-white">
                {{ __('Proveedores') }}
            </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-indigo-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-indigo-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-white">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
