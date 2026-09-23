<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use App\Http\Requests\StoreUnidadMedidaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', UnidadMedida::class);

        $unidades = UnidadMedida::withCount('items')->latest()->paginate(10);

        return view('unidades.index', compact('unidades'));
    }

    public function store(StoreUnidadMedidaRequest $request)
    {
        Gate::authorize('create', UnidadMedida::class);

        UnidadMedida::create($request->validated());

        return redirect()->route('unidades.index')->with('success', 'Unidad de Medida registrada.');
    }

    public function update(StoreUnidadMedidaRequest $request, UnidadMedida $unidade)
    {
        Gate::authorize('update', $unidade);

        $unidade->update($request->validated());

        return redirect()->route('unidades.index')->with('success', 'Unidad de Medida actualizada.');
    }

    public function destroy(UnidadMedida $unidade)
    {
        Gate::authorize('delete', $unidade);

        if ($unidade->items()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar esta unidad de medida porque está asignada a ítems en el catálogo.']);
        }

        $unidade->delete();

        return redirect()->route('unidades.index')->with('success', 'Unidad de medida eliminada.');
    }
}
