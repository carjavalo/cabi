<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servicio;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $servicios = Servicio::when($request->buscar, function ($query, $buscar) {
                $query->where('nombre', 'like', '%' . $buscar . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->appends($request->only('buscar'));

        return view('config.servicios.index', compact('servicios'));
    }

    public function buscar(Request $request)
    {
        $term = $request->query('q', '');
        $servicios = Servicio::where('nombre', 'like', '%' . $term . '%')
            ->orderBy('nombre')
            ->limit(10)
            ->pluck('nombre');

        return response()->json($servicios);
    }

    public function create()
    {
        return view('config.servicios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
        ]);

        Servicio::create($data);

        return redirect()->route('config.servicios.index')->with('success','Servicio creado correctamente.');
    }

    public function edit(Servicio $servicio)
    {
        return view('config.servicios.edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
        ]);

        $servicio->update($data);

        return redirect()->route('config.servicios.index')->with('success','Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->delete();
        return redirect()->route('config.servicios.index')->with('success','Servicio eliminado.');
    }
}
