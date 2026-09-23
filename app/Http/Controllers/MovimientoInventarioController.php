<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\User;
use App\Services\InventarioService;
use App\Http\Requests\StoreMovimientoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class MovimientoInventarioController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', MovimientoInventario::class);

        $user = Auth::user();

        $query = MovimientoInventario::with([
            'item.unidadMedida',
            'areaOrigen.sucursal',
            'areaDestino.sucursal',
            'usuario',
        ]);

        // Filtrar por la empresa del usuario
        if (!$user->hasRole('Super Administrador')) {
            $query->whereHas('item', function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        // Filtro por ítem
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->input('item_id'));
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_inicio'));
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_fin'));
        }

        $movimientos = $query->latest('created_at')->paginate(15);

        $items = $user->hasRole('Super Administrador')
            ? Item::all()
            : Item::where('empresa_id', $user->empresa_id)->get();

        return view('movimientos.index', compact('movimientos', 'items'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', MovimientoInventario::class);

        $user = Auth::user();

        $items = $user->hasRole('Super Administrador')
            ? Item::all()
            : Item::where('empresa_id', $user->empresa_id)->where('estado', true)->get();

        $areas = $user->hasRole('Super Administrador')
            ? Area::with('sucursal')->get()
            : Area::whereHas('sucursal', function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            })->with('sucursal')->get();

        $tipoDefault = $request->query('tipo', 'entrada');

        return view('movimientos.create', compact('items', 'areas', 'tipoDefault'));
    }

    public function store(StoreMovimientoRequest $request, InventarioService $inventarioService)
    {
        Gate::authorize('create', MovimientoInventario::class);

        $user = Auth::user();
        $tipo = $request->input('tipo');
        $itemId = (int) $request->input('item_id');
        $cantidad = (float) $request->input('cantidad');
        $motivo = $request->input('motivo');

        try {
            switch ($tipo) {
                case 'entrada':
                    $inventarioService->registrarEntrada($itemId, (int) $request->input('area_destino_id'), $cantidad, $user->id, $motivo);
                    $msg = "Entrada de {$cantidad} unidades registrada con éxito.";
                    break;

                case 'salida':
                    $inventarioService->registrarSalida($itemId, (int) $request->input('area_origen_id'), $cantidad, $user->id, $motivo);
                    $msg = "Salida de {$cantidad} unidades registrada con éxito.";
                    break;

                case 'traslado':
                    $inventarioService->registrarTraslado($itemId, (int) $request->input('area_origen_id'), (int) $request->input('area_destino_id'), $cantidad, $user->id, $motivo);
                    $msg = "Traslado de {$cantidad} unidades completado. El encargado del área destino es ahora el responsable del inventario.";
                    break;

                case 'ajuste':
                    $inventarioService->registrarAjuste($itemId, (int) $request->input('area_id'), $cantidad, $user->id, $motivo);
                    $msg = "Ajuste de inventario aplicado con éxito.";
                    break;

                default:
                    return back()->withErrors(['error' => 'Tipo de movimiento no válido.']);
            }

            return redirect()->route('movimientos.index')->with('success', $msg);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Devuelve el stock disponible de un ítem en un área vía AJAX
     */
    public function getStockDisponible(Request $request)
    {
        $itemId = $request->query('item_id');
        $areaId = $request->query('area_id');

        if (!$itemId || !$areaId) {
            return response()->json(['cantidad' => 0]);
        }

        $inv = InventarioArea::where('item_id', $itemId)
            ->where('area_id', $areaId)
            ->first();

        return response()->json([
            'cantidad' => $inv ? (float) $inv->cantidad : 0.0,
        ]);
    }
}
