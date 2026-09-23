<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Historial de Movimientos de Inventario') }}
            </h2>
            @can('movimientos.registrar')
            <div class="flex space-x-2">
                <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    + Entrada
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'salida']) }}" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    - Salida
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'traslado']) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    ⇄ Traslado
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'ajuste']) }}" class="inline-flex items-center px-3 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                    ⚙ Ajuste
                </a>
            </div>
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

            <!-- Filtros de Bitácora -->
            <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('movimientos.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <x-input-label for="tipo" value="Tipo Movimiento" />
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
                                <option value="{{ $itm->id }}" {{ request('item_id') == $itm->id ? 'selected' : '' }}>{{ $itm->nombre }} ({{ $itm->sku }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="fecha_inicio" value="Fecha Desde" />
                        <x-text-input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full text-sm" />
                    </div>

                    <div>
                        <x-input-label for="fecha_fin" value="Fecha Hasta" />
                        <x-text-input type="date" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full text-sm" />
                    </div>

                    <div class="flex space-x-2">
                        <x-primary-button class="w-full justify-center">Filtrar</x-primary-button>
                        @if(request()->anyFilled(['tipo', 'item_id', 'fecha_inicio', 'fecha_fin']))
                            <a href="{{ route('movimientos.index') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Limpiar</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabla Bitácora Inmutable -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha / Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ítem / SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen → Destino</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo / Observación</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($movimientos as $mov)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500">
                                            {{ $mov->created_at ? $mov->created_at->format('d/m/Y H:i:s') : '' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($mov->tipo === 'entrada')
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-300">
                                                    ↓ Entrada
                                                </span>
                                            @elseif($mov->tipo === 'salida')
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-300">
                                                    ↑ Salida
                                                </span>
                                            @elseif($mov->tipo === 'traslado')
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                                                    ⇄ Traslado
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-300">
                                                    ⚙ Ajuste
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-gray-900 text-sm">{{ $mov->item->nombre ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $mov->item->sku ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            {{ number_format($mov->cantidad, 2) }} {{ $mov->item->unidadMedida->abreviatura ?? '' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                            @if($mov->tipo === 'traslado')
                                                <span class="font-semibold text-gray-900">{{ $mov->areaOrigen->nombre ?? '-' }}</span>
                                                <span class="text-indigo-600 font-bold mx-1">➔</span>
                                                <span class="font-semibold text-gray-900">{{ $mov->areaDestino->nombre ?? '-' }}</span>
                                            @elseif($mov->tipo === 'entrada')
                                                <span>Destino: <strong class="text-green-700">{{ $mov->areaDestino->nombre ?? '-' }}</strong></span>
                                            @elseif($mov->tipo === 'salida')
                                                <span>Origen: <strong class="text-red-700">{{ $mov->areaOrigen->nombre ?? '-' }}</strong></span>
                                            @else
                                                <span>{{ $mov->areaOrigen->nombre ?? ($mov->areaDestino->nombre ?? '-') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900 font-medium">
                                            {{ $mov->usuario->name ?? 'Sistema' }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-600 max-w-xs truncate">
                                            {{ $mov->motivo ?? 'Sin observación' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay movimientos registrados en la bitácora.</td>
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
    </div>
</x-app-layout>
