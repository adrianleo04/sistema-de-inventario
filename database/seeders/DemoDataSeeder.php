<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Administrador (sin empresa asignada)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@inventario.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'),
                'estado' => true,
            ]
        );
        $superAdmin->assignRole('Super Administrador');

        // 2. Empresa de ejemplo
        $empresa = Empresa::firstOrCreate(
            ['identificacion_fiscal' => '08011990123450'],
            [
                'nombre' => 'Comercializadora Mipyme S.A.',
                'direccion' => 'Colonia Palmira, Tegucigalpa',
                'telefono' => '+504 2234-5678',
                'correo' => 'contacto@mipyme.com',
                'estado' => true,
            ]
        );

        // 3. Administrador de Empresa
        $adminEmpresa = User::firstOrCreate(
            ['email' => 'admin@empresa.com'],
            [
                'empresa_id' => $empresa->id,
                'name' => 'Carlos Gerente',
                'password' => Hash::make('password'),
                'estado' => true,
            ]
        );
        $adminEmpresa->assignRole('Administrador de Empresa');

        // 4. Encargados de Área
        $encargado1 = User::firstOrCreate(
            ['email' => 'encargado.bodega@empresa.com'],
            [
                'empresa_id' => $empresa->id,
                'name' => 'Juan Bodeguero',
                'password' => Hash::make('password'),
                'estado' => true,
            ]
        );
        $encargado1->assignRole('Encargado de Área');

        $encargado2 = User::firstOrCreate(
            ['email' => 'encargado.recepcion@empresa.com'],
            [
                'empresa_id' => $empresa->id,
                'name' => 'Ana Recepción',
                'password' => Hash::make('password'),
                'estado' => true,
            ]
        );
        $encargado2->assignRole('Encargado de Área');

        $encargado3 = User::firstOrCreate(
            ['email' => 'encargado.norte@empresa.com'],
            [
                'empresa_id' => $empresa->id,
                'name' => 'Roberto Sucursal Norte',
                'password' => Hash::make('password'),
                'estado' => true,
            ]
        );
        $encargado3->assignRole('Encargado de Área');

        // Usuario Solo Lectura
        $auditor = User::firstOrCreate(
            ['email' => 'consulta@empresa.com'],
            [
                'empresa_id' => $empresa->id,
                'name' => 'Laura Auditora',
                'password' => Hash::make('password'),
                'estado' => true,
            ]
        );
        $auditor->assignRole('Consulta / Solo Lectura');

        // 5. Sucursales
        $sucursalCentro = Sucursal::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Sucursal Centro',
            'direccion' => 'Av. Cervantes, Centro Histórico',
            'telefono' => '+504 2222-1111',
            'estado' => true,
        ]);

        $sucursalNorte = Sucursal::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Sucursal Norte',
            'direccion' => 'Blvd. Morazán, Plaza comercial',
            'telefono' => '+504 2239-9999',
            'estado' => true,
        ]);

        // 6. Áreas
        $areaBodega = Area::create([
            'sucursal_id' => $sucursalCentro->id,
            'nombre' => 'Bodega Principal',
            'descripcion' => 'Almacenamiento general de mercancía pesada y cajas',
            'encargado_id' => $encargado1->id,
            'estado' => true,
        ]);

        $areaRecepcion = Area::create([
            'sucursal_id' => $sucursalCentro->id,
            'nombre' => 'Recepción y Mostrador',
            'descripcion' => 'Área de atención directa al público y exhibición',
            'encargado_id' => $encargado2->id,
            'estado' => true,
        ]);

        $areaBodegaNorte = Area::create([
            'sucursal_id' => $sucursalNorte->id,
            'nombre' => 'Bodega Sucursal Norte',
            'descripcion' => 'Inventario regional sucursal norte',
            'encargado_id' => $encargado3->id,
            'estado' => true,
        ]);

        // 7. Categorías
        $catElectronica = Categoria::create(['empresa_id' => $empresa->id, 'nombre' => 'Electrónica y Cómputo']);
        $catOficina = Categoria::create(['empresa_id' => $empresa->id, 'nombre' => 'Insumos de Oficina']);
        $catHerramientas = Categoria::create(['empresa_id' => $empresa->id, 'nombre' => 'Herramientas y Equipos']);

        // 8. Unidades de Medida
        $ud = UnidadMedida::where('nombre', 'Unidad')->first();
        $caja = UnidadMedida::where('nombre', 'Caja')->first();
        $kg = UnidadMedida::where('nombre', 'Kilogramo')->first();

        // 9. Proveedores
        $provTech = Proveedor::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Tech Supplies S.A.',
            'contacto' => 'Ing. Mario López',
            'telefono' => '+504 9988-7766',
            'correo' => 'ventas@techsupplies.com',
        ]);

        $provPapelera = Proveedor::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Papelera Hondureña',
            'contacto' => 'Lic. Sofia Ramos',
            'telefono' => '+504 9876-5432',
            'correo' => 'pedidos@papelera.com',
        ]);

        // 10. Ítems de ejemplo (15 ítems)
        $itemsData = [
            ['nombre' => 'Laptop HP ProBook 450 G9', 'sku' => 'ITM-001', 'categoria' => $catElectronica, 'ud' => $ud, 'prov' => $provTech, 'costo' => 850.00, 'minimo' => 5],
            ['nombre' => 'Mouse Inalámbrico Logitech M185', 'sku' => 'ITM-002', 'categoria' => $catElectronica, 'ud' => $ud, 'prov' => $provTech, 'costo' => 15.50, 'minimo' => 10],
            ['nombre' => 'Teclado Mecánico Redragon', 'sku' => 'ITM-003', 'categoria' => $catElectronica, 'ud' => $ud, 'prov' => $provTech, 'costo' => 45.00, 'minimo' => 8],
            ['nombre' => 'Monitor Dell 24 pulgadas FHD', 'sku' => 'ITM-004', 'categoria' => $catElectronica, 'ud' => $ud, 'prov' => $provTech, 'costo' => 175.00, 'minimo' => 4],
            ['nombre' => 'Impresora Multifuncional Epson EcoTank', 'sku' => 'ITM-005', 'categoria' => $catElectronica, 'ud' => $ud, 'prov' => $provTech, 'costo' => 280.00, 'minimo' => 2],
            ['nombre' => 'Caja de Papel Bond Carta 75g (5 resmas)', 'sku' => 'ITM-006', 'categoria' => $catOficina, 'ud' => $caja, 'prov' => $provPapelera, 'costo' => 35.00, 'minimo' => 15],
            ['nombre' => 'Bolígrafos Azules Cello (Caja 50 ud)', 'sku' => 'ITM-007', 'categoria' => $catOficina, 'ud' => $caja, 'prov' => $provPapelera, 'costo' => 8.50, 'minimo' => 5],
            ['nombre' => 'Engrapadora Pesada Metalica', 'sku' => 'ITM-008', 'categoria' => $catOficina, 'ud' => $ud, 'prov' => $provPapelera, 'costo' => 12.00, 'minimo' => 3],
            ['nombre' => 'Archivador A-Z Tamaño Carta', 'sku' => 'ITM-009', 'categoria' => $catOficina, 'ud' => $ud, 'prov' => $provPapelera, 'costo' => 4.20, 'minimo' => 20],
            ['nombre' => 'Tinta Negra Epson T504', 'sku' => 'ITM-010', 'categoria' => $catOficina, 'ud' => $ud, 'prov' => $provTech, 'costo' => 14.00, 'minimo' => 8],
            ['nombre' => 'Taladro Percutor DeWalt 20V', 'sku' => 'ITM-011', 'categoria' => $catHerramientas, 'ud' => $ud, 'prov' => null, 'costo' => 160.00, 'minimo' => 3],
            ['nombre' => 'Juego de Destornilladores Stanley (10 piezas)', 'sku' => 'ITM-012', 'categoria' => $catHerramientas, 'ud' => $ud, 'prov' => null, 'costo' => 25.00, 'minimo' => 5],
            ['nombre' => 'Multímetro Digital Fluke 117', 'sku' => 'ITM-013', 'categoria' => $catHerramientas, 'ud' => $ud, 'prov' => $provTech, 'costo' => 210.00, 'minimo' => 2],
            ['nombre' => 'Cinta de Enmascarar 3M 1 pulgada', 'sku' => 'ITM-014', 'categoria' => $catHerramientas, 'ud' => $ud, 'prov' => $provPapelera, 'costo' => 2.50, 'minimo' => 25],
            ['nombre' => 'Cable de Red UTP Cat6 (Rollo 305m)', 'sku' => 'ITM-015', 'categoria' => $catElectronica, 'ud' => $caja, 'prov' => $provTech, 'costo' => 95.00, 'minimo' => 3],
        ];

        $createdItems = [];
        foreach ($itemsData as $data) {
            $item = Item::create([
                'empresa_id' => $empresa->id,
                'categoria_id' => $data['categoria']->id,
                'unidad_medida_id' => $data['ud']->id,
                'proveedor_id' => $data['prov'] ? $data['prov']->id : null,
                'nombre' => $data['nombre'],
                'sku' => $data['sku'],
                'descripcion' => 'Descripción detallada para ' . $data['nombre'],
                'costo_unitario' => $data['costo'],
                'stock_minimo' => $data['minimo'],
                'estado' => true,
            ]);
            $createdItems[] = $item;
        }

        // 11. Cargar stock inicial en áreas e historial de movimientos
        // Laptops: 10 en Bodega Principal, 2 en Recepción
        InventarioArea::create(['item_id' => $createdItems[0]->id, 'area_id' => $areaBodega->id, 'cantidad' => 10]);
        InventarioArea::create(['item_id' => $createdItems[0]->id, 'area_id' => $areaRecepcion->id, 'cantidad' => 2]);
        MovimientoInventario::create([
            'item_id' => $createdItems[0]->id,
            'tipo' => 'entrada',
            'cantidad' => 12,
            'area_destino_id' => $areaBodega->id,
            'usuario_id' => $adminEmpresa->id,
            'motivo' => 'Compra inicial de lote de Laptops L-2026',
        ]);
        MovimientoInventario::create([
            'item_id' => $createdItems[0]->id,
            'tipo' => 'traslado',
            'cantidad' => 2,
            'area_origen_id' => $areaBodega->id,
            'area_destino_id' => $areaRecepcion->id,
            'usuario_id' => $encargado1->id,
            'motivo' => 'Traslado de 2 Laptops para mostrador de recepción',
        ]);

        // Mouse: 25 en Bodega Principal, 3 en Bodega Norte
        InventarioArea::create(['item_id' => $createdItems[1]->id, 'area_id' => $areaBodega->id, 'cantidad' => 25]);
        InventarioArea::create(['item_id' => $createdItems[1]->id, 'area_id' => $areaBodegaNorte->id, 'cantidad' => 3]);
        MovimientoInventario::create([
            'item_id' => $createdItems[1]->id,
            'tipo' => 'entrada',
            'cantidad' => 30,
            'area_destino_id' => $areaBodega->id,
            'usuario_id' => $adminEmpresa->id,
            'motivo' => 'Ingreso de stock Mouse Logitech',
        ]);
        MovimientoInventario::create([
            'item_id' => $createdItems[1]->id,
            'tipo' => 'salida',
            'cantidad' => 2,
            'area_origen_id' => $areaBodega->id,
            'usuario_id' => $encargado1->id,
            'motivo' => 'Consumo interno de oficina',
        ]);

        // Ítem en Alerta Bajo Stock (Caja Papel Bond: stock minimo 15, stock actual 4)
        InventarioArea::create(['item_id' => $createdItems[5]->id, 'area_id' => $areaBodega->id, 'cantidad' => 4]);
        MovimientoInventario::create([
            'item_id' => $createdItems[5]->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
            'area_destino_id' => $areaBodega->id,
            'usuario_id' => $adminEmpresa->id,
            'motivo' => 'Entrada parcial de papel bond',
        ]);
        MovimientoInventario::create([
            'item_id' => $createdItems[5]->id,
            'tipo' => 'salida',
            'cantidad' => 1,
            'area_origen_id' => $areaBodega->id,
            'usuario_id' => $encargado1->id,
            'motivo' => 'Uso en departamento de contabilidad',
        ]);

        // Cargar stock al resto de los ítems
        for ($i = 2; $i < count($createdItems); $i++) {
            if ($i == 5) continue;
            $qty = rand(8, 30);
            InventarioArea::create([
                'item_id' => $createdItems[$i]->id,
                'area_id' => $areaBodega->id,
                'cantidad' => $qty,
            ]);
            MovimientoInventario::create([
                'item_id' => $createdItems[$i]->id,
                'tipo' => 'entrada',
                'cantidad' => $qty,
                'area_destino_id' => $areaBodega->id,
                'usuario_id' => $adminEmpresa->id,
                'motivo' => 'Carga inicial de inventario demo',
            ]);
        }
    }
}
