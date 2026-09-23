<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('empresas.crear') || $this->user()->hasRole('Super Administrador');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion_fiscal' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'correo' => ['nullable', 'email', 'max:255'],
            'estado' => ['required', 'boolean'],
        ];
    }
}
