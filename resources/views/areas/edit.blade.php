<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Área: ') . $area->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('areas.update', $area) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="sucursal_id" value="Sucursal *" />
                            <select id="sucursal_id" name="sucursal_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Seleccione una sucursal...</option>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}" {{ old('sucursal_id', $area->sucursal_id) == $suc->id ? 'selected' : '' }}>
                                        {{ $suc->nombre }} @role('Super Administrador') ({{ $suc->empresa->nombre }}) @endrole
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('sucursal_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nombre" value="Nombre del Área *" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $area->nombre)" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="encargado_id" value="Encargado Responsable (Usuario del sistema)" />
                            <select id="encargado_id" name="encargado_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Sin encargado asignado</option>
                                @foreach($encargados as $usr)
                                    <option value="{{ $usr->id }}" {{ old('encargado_id', $area->encargado_id) == $usr->id ? 'selected' : '' }}>
                                        {{ $usr->name }} ({{ $usr->email }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-xs text-gray-500">Nota: Al reasignar el encargado, el nuevo responsable asumirá la supervisión del inventario de este área.</span>
                            <x-input-error :messages="$errors->get('encargado_id')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="descripcion" value="Descripción" />
                            <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion', $area->descripcion) }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Estado *" />
                            <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="1" {{ old('estado', $area->estado) == '1' ? 'selected' : '' }}>Activa</option>
                                <option value="0" {{ old('estado', $area->estado) == '0' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('areas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Actualizar Área</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
