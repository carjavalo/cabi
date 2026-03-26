<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            'edad',
            'celular',
            'correolec',
            'contacto_emergencia',
            'servicio_unidad',
            'tipo_vinculacion',
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

    public function updateInscrito(Request $request, $id)
    {
        $inscrito = DB::table('inscripgym')->where('id', $id)->first();

        if (!$inscrito) {
            return response()->json(['success' => false, 'message' => 'Inscrito no encontrado.']);
        }

        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:50',
            'correolec' => 'nullable|string|email|max:255',
            'contacto_emergencia' => 'nullable|string|max:255',
            'servicio_unidad' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        DB::table('inscripgym')->where('id', $id)->update([
            'nombres' => $request->input('nombres'),
            'primer_apellido' => $request->input('primer_apellido'),
            'segundo_apellido' => $request->input('segundo_apellido'),
            'celular' => $request->input('celular'),
            'correolec' => $request->input('correolec'),
            'contacto_emergencia' => $request->input('contacto_emergencia'),
            'servicio_unidad' => $request->input('servicio_unidad'),
        ]);

        return response()->json(['success' => true, 'message' => 'Inscrito actualizado correctamente.']);
    }

    public function deleteInscrito($id)
    {
        $inscrito = DB::table('inscripgym')->where('id', $id)->first();

        if (!$inscrito) {
            return response()->json(['success' => false, 'message' => 'Inscrito no encontrado.']);
        }

        DB::table('inscripgym')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Inscrito eliminado correctamente.']);
    }
}
