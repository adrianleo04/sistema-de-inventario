<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Item;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Item::class);

        $user = Auth::user();
        $query = Item::where('empresa_id', $user->empresa_id)
            ->with(['categoria', 'unidadMedida', 'proveedor', 'inventarios']);

        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->input('categoria_id'));
        }

        $items = $query->latest()->paginate(10);
        $categorias = Categoria::where('empresa_id', $user->empresa_id)->get();

        return view('items.index', compact('items', 'categorias'));
    }

    public function create()
    {
        Gate::authorize('create', Item::class);

        $user = Auth::user();
        $categorias = Categoria::where('empresa_id', $user->empresa_id)->get();
        $unidades = UnidadMedida::all();
        $proveedores = Proveedor::where('empresa_id', $user->empresa_id)->get();

        return view('items.create', compact('categorias', 'unidades', 'proveedores'));
    }

    public function store(StoreItemRequest $request)
    {
        Gate::authorize('create', Item::class);

        $user = Auth::user();
        $data = $request->validated();
        $data['empresa_id'] = $user->empresa_id;

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('items', 'public');
        }

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'Ítem registrado correctamente en el catálogo.');
    }

    public function show(Item $item)
    {
        Gate::authorize('view', $item);

        $item->load([
            'categoria',
            'unidadMedida',
            'proveedor',
            'inventarios.area.sucursal',
            'movimientos.usuario',
            'movimientos.areaOrigen',
            'movimientos.areaDestino',
        ]);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        Gate::authorize('update', $item);

        $user = Auth::user();
        $categorias = Categoria::where('empresa_id', $user->empresa_id)->get();
        $unidades = UnidadMedida::all();
        $proveedores = Proveedor::where('empresa_id', $user->empresa_id)->get();

        return view('items.edit', compact('item', 'categorias', 'unidades', 'proveedores'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        Gate::authorize('update', $item);

        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($item->imagen && Storage::disk('public')->exists($item->imagen)) {
                Storage::disk('public')->delete($item->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('items', 'public');
        }

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'Ítem actualizado exitosamente.');
    }

    public function destroy(Item $item)
    {
        Gate::authorize('delete', $item);

        // Regla de negocio: No eliminar ítem con stock activo > 0 en cualquier área
        $stockTotal = $item->inventarios()->sum('cantidad');
        if ($stockTotal > 0) {
            return back()->withErrors(['error' => "No se puede eliminar el ítem '{$item->nombre}' porque cuenta con un stock activo de {$stockTotal} unidades."]);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Ítem eliminado del catálogo.');
    }
}
