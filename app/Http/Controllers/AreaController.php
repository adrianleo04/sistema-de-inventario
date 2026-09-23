<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Sucursal;
use App\Models\User;
use App\Http\Requests\StoreAreaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AreaController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Area::class);

        $user = Auth::user();

        $query = Area::with(['sucursal.empresa', 'encargado'])->withCount('inventarios');

        if (!$user->hasRole('Super Administrador')) {
            $query->whereHas('sucursal', function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            });
        }

        $areas = $query->latest()->paginate(10);

        return view('areas.index', compact('areas'));
    }

    public function create()
    {
        Gate::authorize('create', Area::class);

        $user = Auth::user();

        $sucursales = $user->hasRole('Super Administrador')
            ? Sucursal::all()
            : Sucursal::where('empresa_id', $user->empresa_id)->get();

        $encargados = $user->hasRole('Super Administrador')
            ? User::all()
            : User::where('empresa_id', $user->empresa_id)->get();

        return view('areas.create', compact('sucursales', 'encargados'));
    }

    public function store(StoreAreaRequest $request)
    {
        Gate::authorize('create', Area::class);

        Area::create($request->validated());

        return redirect()->route('areas.index')->with('success', 'Área creada exitosamente.');
    }

    public function show(Area $area)
    {
        Gate::authorize('view', $area);

        $area->load(['sucursal.empresa', 'encargado', 'inventarios.item.unidadMedida']);

        return view('areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        Gate::authorize('update', $area);

        $user = Auth::user();

        $sucursales = $user->hasRole('Super Administrador')
            ? Sucursal::all()
            : Sucursal::where('empresa_id', $user->empresa_id)->get();

        $encargados = $user->hasRole('Super Administrador')
            ? User::all()
            : User::where('empresa_id', $user->empresa_id)->get();

        return view('areas.edit', compact('area', 'sucursales', 'encargados'));
    }

    public function update(StoreAreaRequest $request, Area $area)
    {
        Gate::authorize('update', $area);

        $area->update($request->validated());

        return redirect()->route('areas.index')->with('success', 'Área actualizada exitosamente.');
    }

    public function destroy(Area $area)
    {
        Gate::authorize('delete', $area);

        // Regla de negocio: No eliminar si tiene stock activo > 0
        $hasActiveStock = $area->inventarios()->where('cantidad', '>', 0)->exists();

        if ($hasActiveStock) {
            return back()->withErrors(['error' => 'No se puede eliminar el área porque contiene inventario activo distinto de cero. Traslade el inventario primero.']);
        }

        $area->delete();

        return redirect()->route('areas.index')->with('success', 'Área eliminada lógicamente.');
    }
}
