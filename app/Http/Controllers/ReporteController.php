<?php

namespace App\Http\Controllers;

use App\Exports\InventarioExport;
use App\Exports\MovimientosExport;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /**
     * Reporte de Inventario Actual
     */
    public function inventario(Request $request)
    {
        Gate::authorize('viewAny', MovimientoInventario::class);

        $user = Auth::user();
        $query = $this->buildInventarioQuery($request, $user);

        $inventarios = $query->paginate(15);
        $sucursales = $user->hasRole('Super Administrador')
            ? Sucursal::all()
            : Sucursal::where('empresa_id', $user->empresa_id)->get();

        $areas = $user->hasRole('Super Administrador')
            ? Area::all()
            : Area::whereHas('sucursal', fn($q) => $q->where('empresa_id', $user->empresa_id))->get();

        $categorias = $user->hasRole('Super Administrador')
            ? Categoria::all()
            : Categoria::where('empresa_id', $user->empresa_id)->get();

        return view('reportes.inventario', compact('inventarios', 'sucursales', 'areas', 'categorias'));
    }

    /**
     * Exportar Reporte de Inventario a Excel
     */
    public function exportarInventarioExcel(Request $request)
    {
        $user = Auth::user();
        $inventarios = $this->buildInventarioQuery($request, $user)->get();

        return Excel::download(new InventarioExport($inventarios), 'reporte_inventario_' . date('Ymd_His') . '.xlsx');
    }

    /**
     * Exportar Reporte de Inventario a PDF
     */
    public function exportarInventarioPdf(Request $request)
    {
        $user = Auth::user();
        $inventarios = $this->buildInventarioQuery($request, $user)->get();
        $empresaNombre = $user->empresa ? $user->empresa->nombre : 'Plataforma Mipyme';

        $pdf = Pdf::loadView('reportes.pdf_inventario', compact('inventarios', 'empresaNombre'));

        return $pdf->download('reporte_inventario_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Reporte de Movimientos de Inventario
     */
    public function movimientos(Request $request)
    {
        Gate::authorize('viewAny', MovimientoInventario::class);

        $user = Auth::user();
        $query = $this->buildMovimientosQuery($request, $user);

        $movimientos = $query->paginate(15);
        $items = $user->hasRole('Super Administrador')
            ? Item::all()
            : Item::where('empresa_id', $user->empresa_id)->get();

        return view('reportes.movimientos', compact('movimientos', 'items'));
    }

    /**
     * Exportar Reporte de Movimientos a Excel
     */
    public function exportarMovimientosExcel(Request $request)
    {
        $user = Auth::user();
        $movimientos = $this->buildMovimientosQuery($request, $user)->get();

        return Excel::download(new MovimientosExport($movimientos), 'reporte_movimientos_' . date('Ymd_His') . '.xlsx');
    }

    /**
     * Exportar Reporte de Movimientos a PDF
     */
    public function exportarMovimientosPdf(Request $request)
    {
        $user = Auth::user();
        $movimientos = $this->buildMovimientosQuery($request, $user)->get();
        $empresaNombre = $user->empresa ? $user->empresa->nombre : 'Plataforma Mipyme';

        $pdf = Pdf::loadView('reportes.pdf_movimientos', compact('movimientos', 'empresaNombre'));

        return $pdf->download('reporte_movimientos_' . date('Ymd_His') . '.pdf');
    }

    private function buildInventarioQuery(Request $request, User $user)
    {
        $query = InventarioArea::with(['item.categoria', 'item.unidadMedida', 'area.sucursal', 'area.encargado']);

        if (!$user->hasRole('Super Administrador')) {
            $query->whereHas('area.sucursal', fn($q) => $q->where('empresa_id', $user->empresa_id));
        }

        if ($request->filled('sucursal_id')) {
            $query->whereHas('area', fn($q) => $q->where('sucursal_id', $request->input('sucursal_id')));
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->input('area_id'));
        }

        if ($request->filled('categoria_id')) {
            $query->whereHas('item', fn($q) => $q->where('categoria_id', $request->input('categoria_id')));
        }

        return $query;
    }

    private function buildMovimientosQuery(Request $request, User $user)
    {
        $query = MovimientoInventario::with(['item.unidadMedida', 'areaOrigen', 'areaDestino', 'usuario']);

        if (!$user->hasRole('Super Administrador')) {
            $query->whereHas('item', fn($q) => $q->where('empresa_id', $user->empresa_id));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->input('item_id'));
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_inicio'));
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_fin'));
        }

        return $query->latest('created_at');
    }
}
