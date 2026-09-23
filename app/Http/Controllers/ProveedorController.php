<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Http\Requests\StoreProveedorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProveedorController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Proveedor::class);

        $user = Auth::user();
        $proveedores = Proveedor::where('empresa_id', $user->empresa_id)
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('proveedores.index', compact('proveedores'));
    }

    public function store(StoreProveedorRequest $request)
    {
        Gate::authorize('create', Proveedor::class);

        Proveedor::create(array_merge($request->validated(), [
            'empresa_id' => Auth::user()->empresa_id,
        ]));

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    public function update(StoreProveedorRequest $request, Proveedor $proveedore)
    {
        Gate::authorize('update', $proveedore);

        $proveedore->update($request->validated());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedore)
    {
        Gate::authorize('delete', $proveedore);

        if ($proveedore->items()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar este proveedor porque tiene ítems asociados.']);
        }

        $proveedore->delete();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }
}
