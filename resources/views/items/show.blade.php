<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Ítem: ') . $item->nombre }}
            </h2>
            <div class="space-x-2">
                @can('items.editar')
                <a href="{{ route('items.edit', $item) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                    Editar Ítem
                </a>
                @endcan
                <a href="{{ route('items.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Volver al Catálogo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Información General -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    @if($item->imagen)
                        <img src="{{ asset('storage/' . $item->imagen) }}" alt="{{ $item->nombre }}" class="h-32 w-32 rounded-lg object-cover border shadow-sm">
                    @else
                        <div class="h-32 w-32 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-2xl border">
                            {{ strtoupper(substr($item->nombre, 0, 2)) }}
                        </div>
                    @endif

                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold">SKU</span>
                            <p class="text-lg font-bold font-mono text-gray-900">{{ $item->sku }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold">Categoría</span>
                            <p class="text-lg font-medium text-gray-900">{{ $item->categoria->nombre ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold">Unidad de Medida</span>
                            <p class="text-lg font-medium text-gray-900">{{ $item->unidadMedida->nombre ?? '' }} ({{ $item->unidadMedida->abreviatura ?? '' }})</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold">Costo Unitario</span>
                            <p class="text-lg font-bold text-gray-900">${{ number_format($item->costo_unitario, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold">Stock Mínimo (Alerta)</span>
                            <p class="text-lg font-medium text-gray-900">{{ $item->stock_minimo }} {{ $item->unidadMedida->abreviatura ?? '' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold">Proveedor Habitual</span>
                            <p class="text-lg font-medium text-gray-900">{{ $item->proveedor->nombre ?? 'Sin asignar' }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <span class="text-xs text-gray-500 uppercase font-semibold">Descripción</span>
                            <p class="text-gray-900 mt-1">{{ $item->descripcion ?? 'Sin descripción registrada' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribución de Stock por Área -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center justify-between">
                    <span>Distribución de Stock Real por Áreas</span>
                    <span class="text-sm font-normal text-gray-500">Stock Total Consolidado: <strong class="text-indigo-700 text-base font-bold">{{ number_format($item->stock_total, 2) }} {{ $item->unidadMedida->abreviatura ?? '' }}</strong></span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encargado Responsable</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad Disponible</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($item->inventarios as $inv)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $inv->area->sucursal->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                        {{ $inv->area->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $inv->area->encargado->name ?? 'Sin asignar' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-indigo-800">
                                        {{ number_format($inv->cantidad, 2) }} {{ $item->unidadMedida->abreviatura ?? '' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Este ítem no posee existencias registradas en ninguna área.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bitácora de Movimientos del Ítem -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Historial de Movimientos de este Ítem</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha / Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen → Destino</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($item->movimientos as $mov)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($mov->tipo === 'entrada')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">Entrada</span>
                                        @elseif($mov->tipo === 'salida')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Salida</span>
                                        @elseif($mov->tipo === 'traslado')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Traslado</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Ajuste</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        {{ number_format($mov->cantidad, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                        {{ $mov->areaOrigen->nombre ?? '-' }} → {{ $mov->areaDestino->nombre ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                        {{ $mov->usuario->name ?? 'Sistema' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $mov->motivo ?? 'Sin observación' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay movimientos registrados para este ítem.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
