<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $empresaId = $user->empresa_id;

        // Base query para ítems
        $itemsQuery = Item::query();
        $movimientosQuery = MovimientoInventario::with(['item.unidadMedida', 'usuario', 'areaOrigen', 'areaDestino']);
        $sucursalesQuery = Sucursal::query();
        $areasQuery = Area::query();

        if (!$user->hasRole('Super Administrador')) {
            $itemsQuery->where('empresa_id', $empresaId);
            $movimientosQuery->whereHas('item', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            });
            $sucursalesQuery->where('empresa_id', $empresaId);
            $areasQuery->whereHas('sucursal', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            });
        }

        // 1. KPIs principales
        $totalItems = (clone $itemsQuery)->count();
        $totalSucursales = (clone $sucursalesQuery)->where('estado', true)->count();
        $totalAreas = (clone $areasQuery)->where('estado', true)->count();

        $items = (clone $itemsQuery)->with(['unidadMedida', 'inventarios.area.sucursal'])->get();

        // Calcular unidades totales acumuladas y filtrar ítems bajo stock mínimo
        $totalUnidades = 0;
        $itemsBajoMinimo = collect();

        foreach ($items as $item) {
            $stockConsolidado = $item->stock_total;
            $totalUnidades += $stockConsolidado;

            if ($stockConsolidado < $item->stock_minimo) {
                $item->stock_actual = $stockConsolidado;
                $itemsBajoMinimo->push($item);
            }
        }

        // 2. Últimos 10 movimientos
        $ultimosMovimientos = (clone $movimientosQuery)
            ->latest('created_at')
            ->take(10)
            ->get();

        // 3. Datos para el gráfico Chart.js (Stock por Sucursal)
        $sucursalesData = (clone $sucursalesQuery)->with('areas.inventarios')->get();
        $chartLabels = [];
        $chartData = [];

        foreach ($sucursalesData as $suc) {
            $chartLabels[] = $suc->nombre;
            $stockSucursal = 0;
            foreach ($suc->areas as $area) {
                $stockSucursal += $area->inventarios->sum('cantidad');
            }
            $chartData[] = (float) $stockSucursal;
        }

        return view('dashboard', compact(
            'totalItems',
            'totalUnidades',
            'totalSucursales',
            'totalAreas',
            'itemsBajoMinimo',
            'ultimosMovimientos',
            'chartLabels',
            'chartData'
        ));
    }
}
