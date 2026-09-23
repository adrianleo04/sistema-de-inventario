<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('movimientos.registrar');
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'in:entrada,salida,traslado,ajuste'],
            'item_id' => ['required', 'exists:items,id'],
            'cantidad' => ['required', 'numeric'],
            'area_origen_id' => ['nullable', 'required_if:tipo,salida,traslado', 'exists:areas,id'],
            'area_destino_id' => ['nullable', 'required_if:tipo,entrada,traslado', 'exists:areas,id'],
            'area_id' => ['nullable', 'required_if:tipo,ajuste', 'exists:areas,id'],
            'motivo' => ['nullable', 'required_if:tipo,ajuste', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'area_origen_id.required_if' => 'Debe seleccionar un área de origen.',
            'area_destino_id.required_if' => 'Debe seleccionar un área de destino.',
            'area_id.required_if' => 'Debe seleccionar el área del ajuste.',
            'motivo.required_if' => 'El motivo es obligatorio para registrar un ajuste de inventario.',
        ];
    }
}
