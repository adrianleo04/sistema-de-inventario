<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('catalogos.crear');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
        ];
    }
}
