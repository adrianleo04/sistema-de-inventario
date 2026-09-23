<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadesMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['nombre' => 'Unidad', 'abreviatura' => 'ud'],
            ['nombre' => 'Caja', 'abreviatura' => 'cj'],
            ['nombre' => 'Kilogramo', 'abreviatura' => 'kg'],
            ['nombre' => 'Litro', 'abreviatura' => 'L'],
            ['nombre' => 'Paquete', 'abreviatura' => 'pqt'],
            ['nombre' => 'Metro', 'abreviatura' => 'm'],
        ];

        foreach ($unidades as $u) {
            UnidadMedida::firstOrCreate(['nombre' => $u['nombre']], $u);
        }
    }
}
