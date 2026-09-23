<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Ítem: ') . $item->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nombre" value="Nombre del Ítem *" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $item->nombre)" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="sku" value="Código / SKU *" />
                            <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full uppercase" :value="old('sku', $item->sku)" required />
                            <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="categoria_id" value="Categoría *" />
                            <select id="categoria_id" name="categoria_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id', $item->categoria_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="unidad_medida_id" value="Unidad de Medida *" />
                            <select id="unidad_medida_id" name="unidad_medida_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" {{ old('unidad_medida_id', $item->unidad_medida_id) == $u->id ? 'selected' : '' }}>{{ $u->nombre }} ({{ $u->abreviatura }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('unidad_medida_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="proveedor_id" value="Proveedor habitual" />
                            <select id="proveedor_id" name="proveedor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Sin proveedor asignado</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}" {{ old('proveedor_id', $item->proveedor_id) == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('proveedor_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="costo_unitario" value="Costo Unitario ($) *" />
                            <x-text-input id="costo_unitario" name="costo_unitario" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('costo_unitario', $item->costo_unitario)" required />
                            <x-input-error :messages="$errors->get('costo_unitario')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="stock_minimo" value="Stock Mínimo (para alertas) *" />
                            <x-text-input id="stock_minimo" name="stock_minimo" type="number" min="0" class="mt-1 block w-full" :value="old('stock_minimo', $item->stock_minimo)" required />
                            <x-input-error :messages="$errors->get('stock_minimo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="imagen" value="Cambiar Imagen (opcional)" />
                            <input id="imagen" name="imagen" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="descripcion" value="Descripción" />
                            <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion', $item->descripcion) }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Estado *" />
                            <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="1" {{ old('estado', $item->estado) == '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('estado', $item->estado) == '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('items.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Actualizar Ítem</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
