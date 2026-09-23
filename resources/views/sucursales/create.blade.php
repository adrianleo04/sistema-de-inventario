<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nueva Sucursal') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('sucursales.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @role('Super Administrador')
                        <div class="md:col-span-2">
                            <x-input-label for="empresa_id" value="Empresa *" />
                            <select id="empresa_id" name="empresa_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Seleccione una empresa...</option>
                                @foreach($empresas as $emp)
                                    <option value="{{ $emp->id }}" {{ old('empresa_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('empresa_id')" class="mt-2" />
                        </div>
                        @endrole

                        <div>
                            <x-input-label for="nombre" value="Nombre de la Sucursal *" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required autofocus />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono')" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="direccion" value="Dirección" />
                            <textarea id="direccion" name="direccion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('direccion') }}</textarea>
                            <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Estado *" />
                            <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="1" {{ old('estado', '1') == '1' ? 'selected' : '' }}>Activa</option>
                                <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('sucursales.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Guardar Sucursal</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
