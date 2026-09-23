<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de Empresa: ') . $empresa->nombre }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('empresas.edit', $empresa) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                    Editar
                </a>
                <a href="{{ route('empresas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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
                        <span class="text-xs text-gray-500 uppercase font-semibold">Identificación Fiscal / RTN</span>
                        <p class="text-lg font-medium text-gray-900">{{ $empresa->identificacion_fiscal ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Teléfono</span>
                        <p class="text-lg font-medium text-gray-900">{{ $empresa->telefono ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Correo Electrónico</span>
                        <p class="text-lg font-medium text-gray-900">{{ $empresa->correo ?? 'No registrado' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-xs text-gray-500 uppercase font-semibold">Dirección</span>
                        <p class="text-gray-900">{{ $empresa->direccion ?? 'No registrada' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Estado</span>
                        <p>
                            @if($empresa->estado)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactiva</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Sucursales -->
                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Sucursales Asociadas ({{ $empresa->sucursales->count() }})</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($empresa->sucursales as $sucursal)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="font-bold text-gray-900 text-base mb-1">{{ $sucursal->nombre }}</div>
                                <div class="text-xs text-gray-500 mb-2">Tel: {{ $sucursal->telefono ?? 'N/A' }}</div>
                                <div class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-1 rounded w-max">
                                    {{ $sucursal->areas->count() }} áreas registradas
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No hay sucursales registradas para esta empresa.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
