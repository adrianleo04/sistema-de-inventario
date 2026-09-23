<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\Empresa;
use App\Http\Requests\StoreSucursalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SucursalController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Sucursal::class);

        $user = Auth::user();

        $query = Sucursal::with(['empresa'])->withCount('areas');

        if (!$user->hasRole('Super Administrador')) {
            $query->where('empresa_id', $user->empresa_id);
        }

        $sucursales = $query->latest()->paginate(10);

        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        Gate::authorize('create', Sucursal::class);

        $user = Auth::user();
        $empresas = $user->hasRole('Super Administrador') ? Empresa::all() : collect();

        return view('sucursales.create', compact('empresas'));
    }

    public function store(StoreSucursalRequest $request)
    {
        Gate::authorize('create', Sucursal::class);

        $user = Auth::user();
        $empresaId = $user->hasRole('Super Administrador')
            ? $request->input('empresa_id')
            : $user->empresa_id;

        Sucursal::create(array_merge($request->validated(), [
            'empresa_id' => $empresaId,
        ]));

        return redirect()->route('sucursales.index')->with('success', 'Sucursal registrada exitosamente.');
    }

    public function show(Sucursal $sucursal)
    {
        Gate::authorize('view', $sucursal);

        $sucursal->load(['areas.encargado', 'empresa']);

        return view('sucursales.show', compact('sucursal'));
    }

    public function edit(Sucursal $sucursal)
    {
        Gate::authorize('update', $sucursal);

        return view('sucursales.edit', compact('sucursal'));
    }

    public function update(StoreSucursalRequest $request, Sucursal $sucursal)
    {
        Gate::authorize('update', $sucursal);

        $sucursal->update($request->validated());

        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        Gate::authorize('delete', $sucursal);

        // Regla de negocio: no eliminar si tiene stock o áreas asociadas con stock distinto de 0
        $hasActiveStock = $sucursal->areas()->whereHas('inventarios', function ($query) {
            $query->where('cantidad', '>', 0);
        })->exists();

        if ($hasActiveStock) {
            return back()->withErrors(['error' => 'No se puede eliminar la sucursal porque tiene áreas con inventario activo. Traslade el inventario primero.']);
        }

        $sucursal->delete();

        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada lógicamente.');
    }
}
