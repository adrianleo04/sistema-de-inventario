<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de Área: ') . $area->nombre }}
            </h2>
            <div class="space-x-2">
                @can('areas.editar')
                <a href="{{ route('areas.edit', $area) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                    Editar
                </a>
                @endcan
                <a href="{{ route('areas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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
                        <span class="text-xs text-gray-500 uppercase font-semibold">Sucursal</span>
                        <p class="text-lg font-medium text-gray-900">{{ $area->sucursal->nombre ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $area->sucursal->empresa->nombre ?? '' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Encargado Responsable</span>
                        <p class="text-lg font-medium text-gray-900">{{ $area->encargado->name ?? 'Sin asignar' }}</p>
                        <p class="text-xs text-gray-500">{{ $area->encargado->email ?? '' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Estado</span>
                        <p>
                            @if($area->estado)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactiva</span>
                            @endif
                        </p>
                    </div>
                    <div class="md:col-span-3">
                        <span class="text-xs text-gray-500 uppercase font-semibold">Descripción</span>
                        <p class="text-gray-900">{{ $area->descripcion ?? 'Sin descripción registrada' }}</p>
                    </div>
                </div>

                <!-- Stock en este Área -->
                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Stock de Ítems en este Área ({{ $area->inventarios->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ítem</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad Disponible</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($area->inventarios as $inv)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $inv->item->nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $inv->item->sku }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-700">
                                            {{ number_format($inv->cantidad, 2) }} {{ $inv->item->unidadMedida->abreviatura ?? '' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay existencias registradas en este área.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
