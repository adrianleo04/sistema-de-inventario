<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\UnidadMedida;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UnidadesMedidaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(UnidadesMedidaSeeder::class);
    }

    public function test_user_can_view_items_catalog(): void
    {
        $empresa = Empresa::create(['nombre' => 'Test Corp', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $user->assignRole('Administrador de Empresa');

        $response = $this->actingAs($user)->get('/items');
        $response->assertOk();
    }

    public function test_can_create_item_in_catalog(): void
    {
        $empresa = Empresa::create(['nombre' => 'Test Corp', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $user->assignRole('Administrador de Empresa');

        $categoria = Categoria::create(['empresa_id' => $empresa->id, 'nombre' => 'Electrónica']);
        $unidad = UnidadMedida::first();

        $response = $this->actingAs($user)->post('/items', [
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'nombre' => 'Monitor Gaming 27"',
            'sku' => 'MON-27',
            'costo_unitario' => 300.00,
            'stock_minimo' => 3,
            'estado' => 1,
        ]);

        $response->assertRedirect('/items');
        $this->assertDatabaseHas('items', ['sku' => 'MON-27', 'empresa_id' => $empresa->id]);
    }

    public function test_sku_must_be_unique_per_empresa(): void
    {
        $empresa = Empresa::create(['nombre' => 'Test Corp', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $user->assignRole('Administrador de Empresa');

        $categoria = Categoria::create(['empresa_id' => $empresa->id, 'nombre' => 'General']);
        $unidad = UnidadMedida::first();

        Item::create([
            'empresa_id' => $empresa->id,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'nombre' => 'Laptop A',
            'sku' => 'SKU-DUPLICATE',
            'costo_unitario' => 500,
            'stock_minimo' => 2,
            'estado' => true,
        ]);

        // Intentar crear otro item con la misma SKU en la misma empresa
        $response = $this->actingAs($user)->post('/items', [
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => $unidad->id,
            'nombre' => 'Laptop B',
            'sku' => 'SKU-DUPLICATE',
            'costo_unitario' => 600,
            'stock_minimo' => 2,
            'estado' => 1,
        ]);

        $response->assertSessionHasErrors(['sku']);
    }
}
