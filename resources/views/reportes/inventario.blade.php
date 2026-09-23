<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reporte de Inventario Actual') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reportes.inventario.excel', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-green-700 hover:bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow transition">
                    📊 Exportar Excel
                </a>
                <a href="{{ route('reportes.inventario.pdf', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-red-700 hover:bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow transition">
                    📄 Exportar PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filtros -->
            <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('reportes.inventario') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <x-input-label for="sucursal_id" value="Sucursal" />
                        <select id="sucursal_id" name="sucursal_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">Todas las sucursales</option>
                            @foreach($sucursales as $suc)
                                <option value="{{ $suc->id }}" {{ request('sucursal_id') == $suc->id ? 'selected' : '' }}>{{ $suc->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="area_id" value="Área" />
                        <select id="area_id" name="area_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">Todas las áreas</option>
                            @foreach($areas as $ar)
                                <option value="{{ $ar->id }}" {{ request('area_id') == $ar->id ? 'selected' : '' }}>{{ $ar->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="categoria_id" value="Categoría" />
                        <select id="categoria_id" name="categoria_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <x-primary-button class="w-full justify-center">Filtrar Reporte</x-primary-button>
                        @if(request()->anyFilled(['sucursal_id', 'area_id', 'categoria_id']))
                            <a href="{{ route('reportes.inventario') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Limpiar</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabla de Resultados -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ítem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal / Área</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encargado Área</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Existencias</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($inventarios as $inv)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">{{ $inv->item->sku ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $inv->item->nombre ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inv->item->categoria->nombre ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-semibold">{{ $inv->area->sucursal->nombre ?? '' }}</div>
                                        <div class="text-xs text-gray-500">{{ $inv->area->nombre ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">{{ $inv->area->encargado->name ?? 'Sin asignar' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-indigo-700">
                                        {{ number_format($inv->cantidad, 2) }} {{ $inv->item->unidadMedida->abreviatura ?? '' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay existencias registradas con los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $inventarios->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
