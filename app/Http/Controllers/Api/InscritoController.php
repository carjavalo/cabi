<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscritoController extends Controller
{
    public function getInscritos()
    {
        $inscritos = DB::table('inscripgym')->select(
            'id', 
            'identificacion', 
            'nombres', 
            'primer_apellido', 
            'segundo_apellido', 
            'servicio_unidad',
            'autorizado'
        )->get();
        return response()->json(['data' => $inscritos]);
    }

    public function toggleAutorizacion(Request $request, $id)
    {
        $inscrito = DB::table('inscripgym')->where('id', $id)->first();
        
        if (!$inscrito) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.']);
        }

        $nuevoEstado = !$inscrito->autorizado;

        DB::table('inscripgym')->where('id', $id)->update(['autorizado' => $nuevoEstado]);

        return response()->json([
            'success' => true, 
            'message' => 'Autorización actualizada.',
            'autorizado' => $nuevoEstado
        ]);
    }
}
