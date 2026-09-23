<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nueva Empresa') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('empresas.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nombre" value="Nombre de la Empresa *" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required autofocus />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="identificacion_fiscal" value="Identificación Fiscal / RTN" />
                            <x-text-input id="identificacion_fiscal" name="identificacion_fiscal" type="text" class="mt-1 block w-full" :value="old('identificacion_fiscal')" />
                            <x-input-error :messages="$errors->get('identificacion_fiscal')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono')" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="correo" value="Correo Electrónico" />
                            <x-text-input id="correo" name="correo" type="email" class="mt-1 block w-full" :value="old('correo')" />
                            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
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
                        <a href="{{ route('empresas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Guardar Empresa</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
