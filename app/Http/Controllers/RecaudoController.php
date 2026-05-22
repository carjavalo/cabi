<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recaudo;
use App\Models\User;

class RecaudoController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        $recaudos = Recaudo::with('user')->orderBy('fecha', 'desc')->orderBy('id', 'desc')->take(10)->get();
        
        // Generar un número de recibo sugerido (por ejemplo, el máximo + 1)
        $lastRecibo = \DB::table('recaudos')->max('id') ?? 0;
        $nextRecibo = str_pad($lastRecibo + 1, 4, '0', STR_PAD_LEFT);
        
        return view('recaudo.index', compact('users', 'recaudos', 'nextRecibo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'valor' => 'required|numeric|min:0',
            'cantidad' => 'required|numeric|min:1',
            'concepto' => 'nullable|string|max:255',
            'numero_recibo' => 'required|string|unique:recaudos,numero_recibo'
        ]);

        $valor_parcial = $request->valor * $request->cantidad;

        Recaudo::create([
            'numero_recibo' => $request->numero_recibo,
            'fecha' => $request->fecha,
            'user_id' => $request->user_id,
            'valor' => $request->valor,
            'cantidad' => $request->cantidad,
            'valor_parcial' => $valor_parcial,
            'concepto' => $request->concepto,
        ]);

        return redirect()->route('recaudo.index')->with('success', 'Recibo guardado correctamente.');
    }

    public function destroy(Recaudo $recaudo)
    {
        $recaudo->delete();
        return redirect()->route('recaudo.index')->with('success', 'Recibo eliminado correctamente.');
    }
}
