<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cargo;

class CargoController extends Controller
{
    public function index(Request $request)
    {
        $query = Cargo::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $cargos = $query->orderBy('id', 'desc')->paginate(12);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('config.cargos._table', compact('cargos'))->render(),
                'pagination' => $cargos->appends(['search' => $search])->links()->toHtml(),
            ]);
        }

        return view('config.cargos.index', compact('cargos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:80',
            'descripcion' => 'nullable|string|max:200',
        ]);

        Cargo::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cargo creado correctamente.']);
        }

        return redirect()->route('config.cargos.index')->with('success', 'Cargo creado correctamente.');
    }

    public function show(Cargo $cargo)
    {
        return response()->json($cargo);
    }

    public function update(Request $request, Cargo $cargo)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:80',
            'descripcion' => 'nullable|string|max:200',
        ]);

        $cargo->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cargo actualizado correctamente.']);
        }

        return redirect()->route('config.cargos.index')->with('success', 'Cargo actualizado correctamente.');
    }

    public function destroy(Cargo $cargo)
    {
        $cargo->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cargo eliminado correctamente.']);
        }

        return redirect()->route('config.cargos.index')->with('success', 'Cargo eliminado.');
    }

    public function exportExcel(Request $request)
    {
        $query = Cargo::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $cargos = $query->orderBy('id', 'desc')->get();

        $filename = 'cargos_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($cargos) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID', 'Nombre', 'Descripción', 'Fecha de Creación'], ';');

            foreach ($cargos as $cargo) {
                fputcsv($file, [
                    $cargo->id,
                    $cargo->nombre,
                    $cargo->descripcion ?? '',
                    $cargo->created_at ? $cargo->created_at->format('d/m/Y H:i') : '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
