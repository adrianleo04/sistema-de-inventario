<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('items.editar');
    }

    public function rules(): array
    {
        $item = $this->route('item');
        $empresaId = $item->empresa_id;

        return [
            'categoria_id' => ['required', 'exists:categorias,id'],
            'unidad_medida_id' => ['required', 'exists:unidades_medida,id'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('items', 'sku')->where('empresa_id', $empresaId)->ignore($item->id),
            ],
            'descripcion' => ['nullable', 'string'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'costo_unitario' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'estado' => ['required', 'boolean'],
        ];
    }
}
