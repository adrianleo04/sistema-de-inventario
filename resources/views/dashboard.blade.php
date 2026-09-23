<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard General de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Acciones Rápidas -->
            <div class="bg-indigo-900 text-white p-6 rounded-xl shadow-md flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-xl font-bold">¡Bienvenido, {{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-indigo-200">Panel de control de inventario para Mipymes. Monitoree existencias y registre movimientos fácilmente.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('movimientos.registrar')
                    <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase rounded-md shadow transition">
                        + Entrada
                    </a>
                    <a href="{{ route('movimientos.create', ['tipo' => 'traslado']) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase rounded-md shadow transition">
                        ⇄ Traslado
                    </a>
                    @endcan
                    @can('reportes.ver')
                    <a href="{{ route('reportes.inventario') }}" class="px-4 py-2 bg-white text-indigo-900 hover:bg-indigo-50 text-xs font-bold uppercase rounded-md shadow transition">
                        Ver Reportes
                    </a>
                    @endcan
                </div>
            </div>

            <!-- KPIs Principales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Ítems -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Ítems en Catálogo</span>
                        <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalItems) }}</div>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>

                <!-- Unidades Totales -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Unidades en Inventario</span>
                        <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalUnidades, 2) }}</div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>

                <!-- Alertas Bajo Mínimo -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Alertas Stock Mínimo</span>
                        <div class="text-3xl font-extrabold {{ $itemsBajoMinimo->count() > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">
                            {{ $itemsBajoMinimo->count() }}
                        </div>
                    </div>
                    <div class="p-3 {{ $itemsBajoMinimo->count() > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-400' }} rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>

                <!-- Sucursales / Áreas -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Estructura Activa</span>
                        <div class="text-xl font-bold text-gray-900 mt-1">{{ $totalSucursales }} Sucursales</div>
                        <div class="text-xs text-gray-500">{{ $totalAreas }} Áreas operativas</div>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
            </div>

            <!-- Sección Gráfico y Alertas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Gráfico Chart.js: Distribución de Stock por Sucursal -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 mb-4">Distribución de Stock por Sucursal</h3>
                    <div class="h-64">
                        <canvas id="stockChart"></canvas>
                    </div>
                </div>

                <!-- Tabla de Alertas de Stock Mínimo -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-base font-bold text-red-600 mb-4 flex items-center space-x-2">
                        <span>⚠️ Alertas de Ítems Bajo Stock Mínimo</span>
                        <span class="text-xs font-normal text-gray-500">({{ $itemsBajoMinimo->count() }} ítems críticos)</span>
                    </h3>

                    <div class="overflow-y-auto max-h-64">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-red-50 text-red-700 font-semibold uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Ítem</th>
                                    <th class="px-4 py-2 text-left">SKU</th>
                                    <th class="px-4 py-2 text-right">Actual</th>
                                    <th class="px-4 py-2 text-right">Mínimo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($itemsBajoMinimo as $itm)
                                    <tr class="hover:bg-red-50/50">
                                        <td class="px-4 py-2.5 font-bold text-gray-900">{{ $itm->nombre }}</td>
                                        <td class="px-4 py-2.5 font-mono text-gray-500">{{ $itm->sku }}</td>
                                        <td class="px-4 py-2.5 text-right font-bold text-red-600">{{ number_format($itm->stock_actual, 2) }}</td>
                                        <td class="px-4 py-2.5 text-right text-gray-500">{{ $itm->stock_minimo }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400 font-medium">🎉 ¡Excelente! No hay ítems por debajo del stock mínimo.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Últimos 10 Movimientos -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-gray-900">Últimos 10 Movimientos Registrados</h3>
                    @can('movimientos.ver')
                    <a href="{{ route('movimientos.index') }}" class="text-xs text-indigo-600 font-bold hover:underline">Ver todos los movimientos →</a>
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 uppercase text-gray-500 font-semibold">
                            <tr>
                                <th class="px-4 py-3 text-left">Fecha / Hora</th>
                                <th class="px-4 py-3 text-left">Tipo</th>
                                <th class="px-4 py-3 text-left">Ítem</th>
                                <th class="px-4 py-3 text-left">Cantidad</th>
                                <th class="px-4 py-3 text-left">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($ultimosMovimientos as $mov)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-gray-500">{{ $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '' }}</td>
                                    <td class="px-4 py-3 font-bold">
                                        @if($mov->tipo === 'entrada')
                                            <span class="text-green-600">Entrada</span>
                                        @elseif($mov->tipo === 'salida')
                                            <span class="text-red-600">Salida</span>
                                        @elseif($mov->tipo === 'traslado')
                                            <span class="text-blue-600">Traslado</span>
                                        @else
                                            <span class="text-purple-600">Ajuste</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $mov->item->nombre ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 font-bold text-gray-900">{{ number_format($mov->cantidad, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $mov->usuario->name ?? 'Sistema' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">No hay movimientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('stockChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Unidades de Stock',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: '#4F46E5',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</x-app-layout>
