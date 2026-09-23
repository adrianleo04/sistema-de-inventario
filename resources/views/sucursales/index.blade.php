<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Sucursales') }}
            </h2>
            @can('sucursales.crear')
            <a href="{{ route('sucursales.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + Nueva Sucursal
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->has('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                                    @role('Super Administrador')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                                    @endrole
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono / Dirección</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Áreas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sucursales as $sucursal)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                            {{ $sucursal->nombre }}
                                        </td>
                                        @role('Super Administrador')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sucursal->empresa->nombre ?? 'N/A' }}
                                        </td>
                                        @endrole
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $sucursal->telefono ?? 'Sin teléfono' }}</div>
                                            <div class="text-xs text-gray-400">{{ $sucursal->direccion ?? 'Sin dirección' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                                {{ $sucursal->areas_count }} área(s)
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($sucursal->estado)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactiva</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('sucursales.show', $sucursal) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                            @can('sucursales.editar')
                                            <a href="{{ route('sucursales.edit', $sucursal) }}" class="text-yellow-600 hover:text-yellow-900">Editar</a>
                                            @endcan
                                            @can('sucursales.eliminar')
                                            <form action="{{ route('sucursales.destroy', $sucursal) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Está seguro de eliminar esta sucursal?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron sucursales registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sucursales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
