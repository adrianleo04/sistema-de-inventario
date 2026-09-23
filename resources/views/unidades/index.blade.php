<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Unidades de Medida') }}
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
                <!-- Formulario Nueva Unidad -->
                @can('catalogos.crear')
                <div class="bg-white p-6 shadow-sm sm:rounded-lg h-fit">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Nueva Unidad</h3>
                    <form action="{{ route('unidades.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="nombre" value="Nombre *" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required placeholder="Ej. Kilogramo, Caja" />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="abreviatura" value="Abreviatura *" />
                            <x-text-input id="abreviatura" name="abreviatura" type="text" class="mt-1 block w-full" required placeholder="Ej. kg, cj, ud" />
                            <x-input-error :messages="$errors->get('abreviatura')" class="mt-2" />
                        </div>
                        <x-primary-button class="w-full justify-center">Guardar Unidad</x-primary-button>
                    </form>
                </div>
                @endcan

                <!-- Tabla de Unidades -->
                <div class="md:col-span-2 bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Abreviatura</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ítems Asignados</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($unidades as $u)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $u->nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-700">
                                            {{ $u->abreviatura }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $u->items_count }} ítems
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @can('catalogos.eliminar')
                                            <form action="{{ route('unidades.destroy', $u) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar esta unidad?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay unidades de medida registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $unidades->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
