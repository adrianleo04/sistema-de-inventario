<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Http\Requests\StoreEmpresaRequest;
use App\Http\Requests\UpdateEmpresaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmpresaController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Empresa::class);

        $empresas = Empresa::withCount(['sucursales', 'usuarios'])->latest()->paginate(10);

        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        Gate::authorize('create', Empresa::class);

        return view('empresas.create');
    }

    public function store(StoreEmpresaRequest $request)
    {
        Gate::authorize('create', Empresa::class);

        Empresa::create($request->validated());

        return redirect()->route('empresas.index')->with('success', 'Empresa registrada correctamente.');
    }

    public function show(Empresa $empresa)
    {
        Gate::authorize('view', $empresa);

        $empresa->load(['sucursales.areas', 'usuarios']);

        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        Gate::authorize('update', $empresa);

        return view('empresas.edit', compact('empresa'));
    }

    public function update(UpdateEmpresaRequest $request, Empresa $empresa)
    {
        Gate::authorize('update', $empresa);

        $empresa->update($request->validated());

        return redirect()->route('empresas.show', $empresa)->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Empresa $empresa)
    {
        Gate::authorize('delete', $empresa);

        $empresa->delete();

        return redirect()->route('empresas.index')->with('success', 'Empresa eliminada lógicamente.');
    }
}
