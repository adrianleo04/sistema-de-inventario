<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Movimiento de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="{
        tipo: '{{ old('tipo', $tipoDefault) }}',
        itemId: '{{ old('item_id', '') }}',
        areaOrigenId: '{{ old('area_origen_id', '') }}',
        stockDisponible: null,
        loadingStock: false,

        checkStock() {
            if (!this.itemId || !this.areaOrigenId) {
                this.stockDisponible = null;
                return;
            }
            this.loadingStock = true;
            fetch(`/movimientos/stock-disponible?item_id=${this.itemId}&area_id=${this.areaOrigenId}`)
                .then(res => res.json())
                .then(data => {
                    this.stockDisponible = data.cantidad;
                    this.loadingStock = false;
                })
                .catch(() => {
                    this.stockDisponible = null;
                    this.loadingStock = false;
                });
        }
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">¡Atención! Error en la validación:</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Pestañas de Selección de Tipo -->
                <div class="flex border-b border-gray-200 mb-6 space-x-2">
                    <button type="button" @click="tipo = 'entrada'" :class="tipo === 'entrada' ? 'border-green-600 text-green-700 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700'" class="py-2 px-4 text-sm focus:outline-none">
                        ↓ Entrada (Compra / Ingreso)
                    </button>
                    <button type="button" @click="tipo = 'salida'" :class="tipo === 'salida' ? 'border-red-600 text-red-700 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700'" class="py-2 px-4 text-sm focus:outline-none">
                        ↑ Salida (Venta / Consumo)
                    </button>
                    <button type="button" @click="tipo = 'traslado'" :class="tipo === 'traslado' ? 'border-indigo-600 text-indigo-700 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700'" class="py-2 px-4 text-sm focus:outline-none">
                        ⇄ Traslado entre Áreas
                    </button>
                    <button type="button" @click="tipo = 'ajuste'" :class="tipo === 'ajuste' ? 'border-purple-600 text-purple-700 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700'" class="py-2 px-4 text-sm focus:outline-none">
                        ⚙ Ajuste de Inventario
                    </button>
                </div>

                <form action="{{ route('movimientos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tipo" x-model="tipo">

                    <div class="space-y-6">
                        <!-- Ítem -->
                        <div>
                            <x-input-label for="item_id" value="Seleccione el Ítem *" />
                            <select id="item_id" name="item_id" x-model="itemId" @change="checkStock()" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Seleccionar ítem del catálogo --</option>
                                @foreach($items as $itm)
                                    <option value="{{ $itm->id }}" {{ old('item_id') == $itm->id ? 'selected' : '' }}>
                                        {{ $itm->nombre }} (SKU: {{ $itm->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campos para Entrada -->
                        <template x-if="tipo === 'entrada'">
                            <div>
                                <x-input-label for="area_destino_id" value="Área de Destino (donde ingresa el stock) *" />
                                <select id="area_destino_id" name="area_destino_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- Seleccionar área destino --</option>
                                    @foreach($areas as $ar)
                                        <option value="{{ $ar->id }}">{{ $ar->nombre }} (Sucursal: {{ $ar->sucursal->nombre }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>

                        <!-- Campos para Salida -->
                        <template x-if="tipo === 'salida'">
                            <div>
                                <x-input-label for="area_origen_id" value="Área de Origen (donde se descuenta el stock) *" />
                                <select id="area_origen_id" name="area_origen_id" x-model="areaOrigenId" @change="checkStock()" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- Seleccionar área origen --</option>
                                    @foreach($areas as $ar)
                                        <option value="{{ $ar->id }}">{{ $ar->nombre }} (Sucursal: {{ $ar->sucursal->nombre }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>

                        <!-- Campos para Traslado -->
                        <template x-if="tipo === 'traslado'">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="area_origen_id_traslado" value="Área de Origen *" />
                                    <select id="area_origen_id_traslado" name="area_origen_id" x-model="areaOrigenId" @change="checkStock()" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">-- Seleccionar área origen --</option>
                                        @foreach($areas as $ar)
                                            <option value="{{ $ar->id }}">{{ $ar->nombre }} (Sucursal: {{ $ar->sucursal->nombre }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="area_destino_id_traslado" value="Área de Destino *" />
                                    <select id="area_destino_id_traslado" name="area_destino_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">-- Seleccionar área destino --</option>
                                        @foreach($areas as $ar)
                                            <option value="{{ $ar->id }}">{{ $ar->nombre }} (Sucursal: {{ $ar->sucursal->nombre }})</option>
                                        @endforeach
                                    </select>
                                    <span class="text-xs text-indigo-600 font-semibold block mt-1">El encargado del área destino será el nuevo responsable de las unidades trasladadas.</span>
                                </div>
                            </div>
                        </template>

                        <!-- Campos para Ajuste -->
                        <template x-if="tipo === 'ajuste'">
                            <div>
                                <x-input-label for="area_id_ajuste" value="Área de Ajuste *" />
                                <select id="area_id_ajuste" name="area_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- Seleccionar área --</option>
                                    @foreach($areas as $ar)
                                        <option value="{{ $ar->id }}">{{ $ar->nombre }} (Sucursal: {{ $ar->sucursal->nombre }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>

                        <!-- Alerta de Stock Disponible en Tiempo Real -->
                        <template x-if="(tipo === 'salida' || tipo === 'traslado') && stockDisponible !== null">
                            <div class="p-4 rounded-md border" :class="stockDisponible > 0 ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-red-50 border-red-200 text-red-900'">
                                <div class="flex items-center space-x-2 font-bold text-sm">
                                    <span>Stock Real Disponible en Origen:</span>
                                    <span class="text-lg" x-text="stockDisponible"></span>
                                </div>
                                <div x-show="stockDisponible === 0" class="text-xs text-red-700 mt-1 font-semibold">
                                    ⚠️ No se puede realizar salida o traslado porque esta área no tiene stock disponible para este ítem.
                                </div>
                            </div>
                        </template>

                        <!-- Cantidad -->
                        <div>
                            <x-input-label for="cantidad" value="Cantidad *" />
                            <x-text-input id="cantidad" name="cantidad" type="number" step="0.01" class="mt-1 block w-full" :value="old('cantidad')" required placeholder="Ej. 10.00 (positivo para entrada/salida/traslado, +/- para ajuste)" />
                            <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                        </div>

                        <!-- Motivo / Observación -->
                        <div>
                            <x-input-label for="motivo" value="Motivo / Observación" />
                            <textarea id="motivo" name="motivo" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Ej. Factura N° 1042 / Merma por daño / Traslado rutinario">{{ old('motivo') }}</textarea>
                            <span x-show="tipo === 'ajuste'" class="text-xs text-red-600 font-bold block mt-1">* El motivo es obligatorio para el tipo de movimiento 'Ajuste'.</span>
                            <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('movimientos.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Confirmar Movimiento</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
