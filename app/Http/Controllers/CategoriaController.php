<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Requests\StoreCategoriaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoriaController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Categoria::class);

        $user = Auth::user();
        $categorias = Categoria::where('empresa_id', $user->empresa_id)
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('categorias.index', compact('categorias'));
    }

    public function store(StoreCategoriaRequest $request)
    {
        Gate::authorize('create', Categoria::class);

        Categoria::create([
            'empresa_id' => Auth::user()->empresa_id,
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function update(StoreCategoriaRequest $request, Categoria $categoria)
    {
        Gate::authorize('update', $categoria);

        $categoria->update($request->validated());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        Gate::authorize('delete', $categoria);

        if ($categoria->items()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar la categoría porque contiene ítems asociados.']);
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada.');
    }
}
