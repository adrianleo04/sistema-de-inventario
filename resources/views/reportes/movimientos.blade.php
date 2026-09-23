<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reporte de Movimientos de Inventario') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reportes.movimientos.excel', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-green-700 hover:bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow transition">
                    📊 Exportar Excel
                </a>
                <a href="{{ route('reportes.movimientos.pdf', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-red-700 hover:bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow transition">
                    📄 Exportar PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filtros -->
            <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('reportes.movimientos') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <x-input-label for="tipo" value="Tipo" />
                        <select id="tipo" name="tipo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">Todos los tipos</option>
                            <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="salida" {{ request('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                            <option value="traslado" {{ request('tipo') == 'traslado' ? 'selected' : '' }}>Traslado</option>
                            <option value="ajuste" {{ request('tipo') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="item_id" value="Ítem" />
                        <select id="item_id" name="item_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">Todos los ítems</option>
                            @foreach($items as $itm)
                                <option value="{{ $itm->id }}" {{ request('item_id') == $itm->id ? 'selected' : '' }}>{{ $itm->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="fecha_inicio" value="Desde" />
                        <x-text-input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full text-sm" />
                    </div>

                    <div>
                        <x-input-label for="fecha_fin" value="Hasta" />
                        <x-text-input type="date" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full text-sm" />
                    </div>

                    <div class="flex space-x-2">
                        <x-primary-button class="w-full justify-center">Filtrar</x-primary-button>
                        @if(request()->anyFilled(['tipo', 'item_id', 'fecha_inicio', 'fecha_fin']))
                            <a href="{{ route('reportes.movimientos') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Limpiar</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabla de Movimientos -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 uppercase text-gray-500 font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Fecha / Hora</th>
                                <th class="px-6 py-3 text-left">Tipo</th>
                                <th class="px-6 py-3 text-left">Ítem / SKU</th>
                                <th class="px-6 py-3 text-left">Cantidad</th>
                                <th class="px-6 py-3 text-left">Origen → Destino</th>
                                <th class="px-6 py-3 text-left">Usuario</th>
                                <th class="px-6 py-3 text-left">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($movimientos as $mov)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-500">{{ $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold">
                                        @if($mov->tipo === 'entrada')
                                            <span class="text-green-700">Entrada</span>
                                        @elseif($mov->tipo === 'salida')
                                            <span class="text-red-700">Salida</span>
                                        @elseif($mov->tipo === 'traslado')
                                            <span class="text-blue-700">Traslado</span>
                                        @else
                                            <span class="text-purple-700">Ajuste</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $mov->item->nombre ?? '' }}</div>
                                        <div class="text-gray-400 font-mono">{{ $mov->item->sku ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">{{ number_format($mov->cantidad, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                        {{ $mov->areaOrigen->nombre ?? '-' }} → {{ $mov->areaDestino->nombre ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $mov->usuario->name ?? '' }}</td>
                                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $mov->motivo ?? '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay movimientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $movimientos->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
