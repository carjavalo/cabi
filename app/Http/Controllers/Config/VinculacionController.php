<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vinculacion;

class VinculacionController extends Controller
{
    public function index()
    {
        $vinculaciones = Vinculacion::orderBy('id','desc')->paginate(15);
        return view('config.vinculaciones.index', compact('vinculaciones'));
    }

    public function create()
    {
        return view('config.vinculaciones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
        ]);

        Vinculacion::create($data);

        return redirect()->route('config.vinculaciones.index')->with('success','Vinculación creada correctamente.');
    }

    public function edit(Vinculacion $vinculacione)
    {
        $vinculacion = $vinculacione;
        return view('config.vinculaciones.edit', compact('vinculacion'));
    }

    public function update(Request $request, Vinculacion $vinculacione)
    {
        $vinculacion = $vinculacione;

        $data = $request->validate([
            'nombre' => 'required|string|max:150',
        ]);

        $vinculacion->update($data);

        return redirect()->route('config.vinculaciones.index')->with('success','Vinculación actualizada correctamente.');
    }

    public function destroy(Vinculacion $vinculacione)
    {
        $vinculacione->delete();
        return redirect()->route('config.vinculaciones.index')->with('success','Vinculación eliminada.');
    }
}
