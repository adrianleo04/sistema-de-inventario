<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Services\InventarioService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UnidadesMedidaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryMovementTest extends TestCase
{
    use RefreshDatabase;

    protected InventarioService $inventarioService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(UnidadesMedidaSeeder::class);
        $this->inventarioService = new InventarioService();
    }

    public function test_can_register_entrada_stock(): void
    {
        $empresa = Empresa::create(['nombre' => 'Company A', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $sucursal = Sucursal::create(['empresa_id' => $empresa->id, 'nombre' => 'Sucursal 1', 'estado' => true]);
        $area = Area::create(['sucursal_id' => $sucursal->id, 'nombre' => 'Bodega Principal', 'estado' => true]);
        $categoria = Categoria::create(['empresa_id' => $empresa->id, 'nombre' => 'General']);

        $item = Item::create([
            'empresa_id' => $empresa->id,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => 1,
            'nombre' => 'Item Test Entrada',
            'sku' => 'SKU-ENTRADA',
            'costo_unitario' => 10,
            'stock_minimo' => 2,
            'estado' => true,
        ]);

        $mov = $this->inventarioService->registrarEntrada($item->id, $area->id, 50, $user->id, 'Compra lote nuevo');

        $this->assertEquals('entrada', $mov->tipo);
        $this->assertEquals(50, $item->stock_total);
        $this->assertDatabaseHas('inventario_area', ['item_id' => $item->id, 'area_id' => $area->id, 'cantidad' => 50]);
    }

    public function test_salida_fails_when_insufficient_stock(): void
    {
        $empresa = Empresa::create(['nombre' => 'Company B', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $sucursal = Sucursal::create(['empresa_id' => $empresa->id, 'nombre' => 'Sucursal 1', 'estado' => true]);
        $area = Area::create(['sucursal_id' => $sucursal->id, 'nombre' => 'Bodega', 'estado' => true]);
        $categoria = Categoria::create(['empresa_id' => $empresa->id, 'nombre' => 'General']);

        $item = Item::create([
            'empresa_id' => $empresa->id,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => 1,
            'nombre' => 'Item Sin Stock',
            'sku' => 'SKU-NO-STOCK',
            'costo_unitario' => 10,
            'stock_minimo' => 2,
            'estado' => true,
        ]);

        InventarioArea::create(['item_id' => $item->id, 'area_id' => $area->id, 'cantidad' => 5]);

        $this->expectException(ValidationException::class);

        // Intentar retirar 20 cuando solo hay 5
        $this->inventarioService->registrarSalida($item->id, $area->id, 20, $user->id, 'Salida excesiva');
    }

    public function test_traslado_moves_stock_between_areas(): void
    {
        $empresa = Empresa::create(['nombre' => 'Company C', 'estado' => true]);
        $user = User::factory()->create(['empresa_id' => $empresa->id]);
        $sucursal = Sucursal::create(['empresa_id' => $empresa->id, 'nombre' => 'Sucursal 1', 'estado' => true]);
        $areaOrigen = Area::create(['sucursal_id' => $sucursal->id, 'nombre' => 'Bodega Origen', 'estado' => true]);
        $areaDestino = Area::create(['sucursal_id' => $sucursal->id, 'nombre' => 'Recepción Destino', 'estado' => true]);
        $categoria = Categoria::create(['empresa_id' => $empresa->id, 'nombre' => 'General']);

        $item = Item::create([
            'empresa_id' => $empresa->id,
            'categoria_id' => $categoria->id,
            'unidad_medida_id' => 1,
            'nombre' => 'Item Traslado',
            'sku' => 'SKU-TRASLADO',
            'costo_unitario' => 10,
            'stock_minimo' => 2,
            'estado' => true,
        ]);

        InventarioArea::create(['item_id' => $item->id, 'area_id' => $areaOrigen->id, 'cantidad' => 30]);

        $this->inventarioService->registrarTraslado($item->id, $areaOrigen->id, $areaDestino->id, 10, $user->id, 'Traslado a mostrador');

        $this->assertDatabaseHas('inventario_area', ['item_id' => $item->id, 'area_id' => $areaOrigen->id, 'cantidad' => 20]);
        $this->assertDatabaseHas('inventario_area', ['item_id' => $item->id, 'area_id' => $areaDestino->id, 'cantidad' => 10]);
    }
}
