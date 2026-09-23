<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UnidadesMedidaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationalModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(UnidadesMedidaSeeder::class);
    }

    public function test_super_admin_can_view_empresas(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Administrador');

        $response = $this->actingAs($superAdmin)->get('/empresas');
        $response->assertOk();
    }

    public function test_empresa_admin_can_create_sucursal_and_area(): void
    {
        $empresa = Empresa::create(['nombre' => 'Test Empresa', 'estado' => true]);
        $admin = User::factory()->create(['empresa_id' => $empresa->id]);
        $admin->assignRole('Administrador de Empresa');

        // Crear Sucursal
        $response = $this->actingAs($admin)->post('/sucursales', [
            'nombre' => 'Sucursal Test',
            'direccion' => 'Calle 123',
            'telefono' => '2222-3333',
            'estado' => 1,
        ]);
        $response->assertRedirect('/sucursales');
        $this->assertDatabaseHas('sucursales', ['nombre' => 'Sucursal Test']);

        $sucursal = Sucursal::where('nombre', 'Sucursal Test')->first();

        // Crear Área
        $responseArea = $this->actingAs($admin)->post('/areas', [
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Bodega Alpha',
            'descripcion' => 'Área de prueba',
            'estado' => 1,
        ]);
        $responseArea->assertRedirect('/areas');
        $this->assertDatabaseHas('areas', ['nombre' => 'Bodega Alpha']);
    }

    public function test_cannot_delete_area_with_active_inventory(): void
    {
        $empresa = Empresa::create(['nombre' => 'Test Empresa', 'estado' => true]);
        $admin = User::factory()->create(['empresa_id' => $empresa->id]);
        $admin->assignRole('Administrador de Empresa');

        $sucursal = Sucursal::create(['empresa_id' => $empresa->id, 'nombre' => 'Sucursal Principal', 'estado' => true]);
        $area = Area::create(['sucursal_id' => $sucursal->id, 'nombre' => 'Bodega Central', 'estado' => true]);
        $categoria = $empresa->categorias()->create(['nombre' => 'General']);
        $item = Item::create([
            'empresa_id' => $empresa->id,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => 1,
            'nombre' => 'Producto X',
            'sku' => 'PROD-X',
            'estado' => true,
        ]);

        InventarioArea::create(['item_id' => $item->id, 'area_id' => $area->id, 'cantidad' => 10]);

        $response = $this->actingAs($admin)->delete("/areas/{$area->id}");
        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseHas('areas', ['id' => $area->id, 'deleted_at' => null]);
    }
}
