<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Proveedores') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Formulario Nuevo Proveedor -->
                @can('catalogos.crear')
                <div class="bg-white p-6 shadow-sm sm:rounded-lg h-fit">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Nuevo Proveedor</h3>
                    <form action="{{ route('proveedores.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="nombre" value="Nombre o Razon Social *" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="contacto" value="Contacto Persona" />
                            <x-text-input id="contacto" name="contacto" type="text" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="correo" value="Correo Electrónico" />
                            <x-text-input id="correo" name="correo" type="email" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
                        </div>
                        <x-primary-button class="w-full justify-center">Guardar Proveedor</x-primary-button>
                    </form>
                </div>
                @endcan

                <!-- Tabla de Proveedores -->
                <div class="md:col-span-2 bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto / Datos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ítems</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($proveedores as $prov)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $prov->nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $prov->contacto ?? 'Sin contacto' }}</div>
                                            <div class="text-xs text-gray-400">Tel: {{ $prov->telefono ?? 'N/A' }} | {{ $prov->correo ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                {{ $prov->items_count }} ítems
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @can('catalogos.eliminar')
                                            <form action="{{ route('proveedores.destroy', $prov) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este proveedor?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay proveedores registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $proveedores->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
