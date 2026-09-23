<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Categorías de Inventario') }}
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
                <!-- Formulario Nueva Categoría -->
                @can('catalogos.crear')
                <div class="bg-white p-6 shadow-sm sm:rounded-lg h-fit">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Nueva Categoría</h3>
                    <form action="{{ route('categorias.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="nombre" value="Nombre de Categoría *" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" required placeholder="Ej. Electrónica, Insumos" />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>
                        <x-primary-button class="w-full justify-center">Guardar Categoría</x-primary-button>
                    </form>
                </div>
                @endcan

                <!-- Tabla de Categorías -->
                <div class="md:col-span-2 bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ítems Asociados</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($categorias as $cat)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $cat->nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                {{ $cat->items_count }} ítems
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            @can('catalogos.eliminar')
                                            <form action="{{ route('categorias.destroy', $cat) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay categorías registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $categorias->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
