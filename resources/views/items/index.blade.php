<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Catálogo de Inventario (Ítems)') }}
            </h2>
            @can('items.crear')
            <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + Nuevo Ítem
            </a>
            @endcan
        </div>
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

            <!-- Filtros y Búsqueda -->
            <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('items.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 w-full">
                        <x-text-input name="search" type="text" class="w-full" placeholder="Buscar por nombre de ítem o código SKU..." value="{{ request('search') }}" />
                    </div>
                    <div class="w-full md:w-64">
                        <select name="categoria_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" onchange="this.form.submit()">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>Filtrar</x-primary-button>
                    @if(request()->anyFilled(['search', 'categoria_id']))
                        <a href="{{ route('items.index') }}" class="text-sm text-gray-500 hover:underline">Limpiar</a>
                    @endif
                </form>
            </div>

            <!-- Tabla del Catálogo -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ítem / SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Unit.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Stock</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($items as $item)
                                    @php
                                        $totalStock = $item->stock_total;
                                        $isBelowMin = $totalStock < $item->stock_minimo;
                                        $isNearMin = !$isBelowMin && $totalStock <= ($item->stock_minimo * 1.2);
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                @if($item->imagen)
                                                    <img src="{{ asset('storage/' . $item->imagen) }}" alt="{{ $item->nombre }}" class="h-10 w-10 rounded-lg object-cover border">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                                        {{ strtoupper(substr($item->nombre, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ $item->nombre }}</div>
                                                    <div class="text-xs text-gray-500 font-mono">SKU: {{ $item->sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->categoria->nombre ?? 'Sin categoría' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${{ number_format($item->costo_unitario, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            {{ number_format($totalStock, 2) }} {{ $item->unidadMedida->abreviatura ?? '' }}
                                            <div class="text-xs text-gray-400 font-normal">Mínimo: {{ $item->stock_minimo }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($totalStock == 0)
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                                    Agotado
                                                </span>
                                            @elseif($isBelowMin)
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-300">
                                                    ⚠️ Bajo Mínimo
                                                </span>
                                            @elseif($isNearMin)
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800">
                                                    ⚡ Cerca del Mínimo
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Normal
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900">Ver / Stock</a>
                                            @can('items.editar')
                                            <a href="{{ route('items.edit', $item) }}" class="text-yellow-600 hover:text-yellow-900">Editar</a>
                                            @endcan
                                            @can('items.eliminar')
                                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este ítem del catálogo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron ítems en el catálogo.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $items->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
