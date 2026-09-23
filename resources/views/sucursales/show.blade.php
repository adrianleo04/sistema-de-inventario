<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de Sucursal: ') . $sucursal->nombre }}
            </h2>
            <div class="space-x-2">
                @can('sucursales.editar')
                <a href="{{ route('sucursales.edit', $sucursal) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                    Editar
                </a>
                @endcan
                <a href="{{ route('sucursales.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-b pb-6">
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Empresa</span>
                        <p class="text-lg font-medium text-gray-900">{{ $sucursal->empresa->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Teléfono</span>
                        <p class="text-lg font-medium text-gray-900">{{ $sucursal->telefono ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Estado</span>
                        <p>
                            @if($sucursal->estado)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactiva</span>
                            @endif
                        </p>
                    </div>
                    <div class="md:col-span-3">
                        <span class="text-xs text-gray-500 uppercase font-semibold">Dirección</span>
                        <p class="text-gray-900">{{ $sucursal->direccion ?? 'No registrada' }}</p>
                    </div>
                </div>

                <!-- Áreas -->
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Áreas de esta Sucursal ({{ $sucursal->areas->count() }})</h3>
                        @can('areas.crear')
                        <a href="{{ route('areas.create') }}" class="text-xs text-indigo-600 font-bold hover:underline">+ Agregar Área</a>
                        @endcan
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($sucursal->areas as $area)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="font-bold text-gray-900 text-base mb-1">{{ $area->nombre }}</div>
                                <div class="text-xs text-gray-500 mb-2">Encargado: <span class="font-semibold text-gray-700">{{ $area->encargado->name ?? 'Sin asignar' }}</span></div>
                                <p class="text-xs text-gray-600 line-clamp-2 mb-2">{{ $area->descripcion ?? 'Sin descripción' }}</p>
                                <div class="text-right">
                                    <a href="{{ route('areas.show', $area) }}" class="text-xs text-indigo-600 font-semibold hover:underline">Ver detalle →</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No hay áreas registradas para esta sucursal.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
