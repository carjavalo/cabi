<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EncuestaController extends Controller
{
    public function index()
    {
        return view('config.encuestas.index');
    }

    public function data()
    {
        try {
            $items = DB::table('encuestas')->orderBy('id', 'desc')->get();
        } catch (\Throwable $e) {
            $items = collect();
        }
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'codigo' => 'nullable|string|max:255',
            'estructura' => 'nullable|string',
            'activo' => 'nullable|boolean',
            'permitir_repetir' => 'nullable|boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $estructura = $data['estructura'] ?? null;
        $preguntas_count = 0;
        if ($estructura) {
            try {
                $decoded = json_decode($estructura, true);
                if (is_array($decoded) && isset($decoded['questions']) && is_array($decoded['questions'])) {
                    $preguntas_count = count($decoded['questions']);
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        $titulo = trim($data['titulo'] ?? '');
        if ($titulo === '') $titulo = 'Sin título';

        $codigo = trim($data['codigo'] ?? '');
        if ($codigo === null) $codigo = '';
        $payload = [
            'titulo' => $titulo,
            'codigo' => $codigo,
            'estructura' => $estructura,
            'preguntas_count' => $preguntas_count,
            'activo' => isset($data['activo']) ? (int)$data['activo'] : 1,
            'permitir_repetir' => isset($data['permitir_repetir']) ? (int)$data['permitir_repetir'] : 0,
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'created_by' => auth()->id() ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        try {
            $id = DB::table('encuestas')->insertGetId($payload);
            $item = DB::table('encuestas')->where('id', $id)->first();
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function show($id)
    {
        $item = DB::table('encuestas')->where('id', $id)->first();
        if (!$item) return response()->json(['message' => 'Not found'], 404);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'codigo' => 'nullable|string|max:255',
            'estructura' => 'nullable|string',
            'activo' => 'nullable|boolean',
            'permitir_repetir' => 'nullable|boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $estructura = $data['estructura'] ?? null;
        $preguntas_count = null;
        if ($estructura) {
            try {
                $decoded = json_decode($estructura, true);
                if (is_array($decoded) && isset($decoded['questions']) && is_array($decoded['questions'])) {
                    $preguntas_count = count($decoded['questions']);
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        $titulo = trim($data['titulo'] ?? '');
        if ($titulo === '') $titulo = 'Sin título';

        $codigo = trim($data['codigo'] ?? '');
        if ($codigo === null) $codigo = '';
        $payload = array_filter([
            'titulo' => $titulo,
            'codigo' => $codigo,
            'estructura' => $estructura ?? null,
            'preguntas_count' => $preguntas_count,
            'activo' => isset($data['activo']) ? (int)$data['activo'] : null,
            'permitir_repetir' => isset($data['permitir_repetir']) ? (int)$data['permitir_repetir'] : null,
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'updated_at' => now(),
        ], function ($v) { return $v !== null; });

        try {
            DB::table('encuestas')->where('id', $id)->update($payload);
            $item = DB::table('encuestas')->where('id', $id)->first();
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
        return response()->json(['success' => true, 'item' => $item]);
    }

    public function destroy($id)
    {
        try {
            DB::table('encuestas')->where('id', $id)->delete();
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
        return response()->json(['success' => true]);
    }

    // Mostrar encuesta pública para responder
    public function mostrarEncuesta($id)
    {
        try {
            $now = now();
            $encuesta = DB::table('encuestas')
                ->where('id', $id)
                ->where('activo', 1)
                ->where(function($query) use ($now) {
                    $query->where(function($q) use ($now) {
                        $q->whereNull('fecha_inicio')
                          ->orWhere('fecha_inicio', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>', $now);
                    });
                })
                ->first();
            
            if (!$encuesta) {
                // Verificar si la encuesta existe pero está inactiva o fuera de fechas
                $encuestaInactiva = DB::table('encuestas')->where('id', $id)->first();
                
                if ($encuestaInactiva) {
                    // La encuesta existe pero no está disponible
                    return view('encuestas.no-disponible', compact('encuestaInactiva'));
                } else {
                    // La encuesta no existe
                    abort(404, 'Encuesta no encontrada');
                }
            }
            
            return view('encuestas.responder', compact('encuesta'));
        } catch (\Throwable $e) {
            abort(404, 'Encuesta no encontrada');
        }
    }

    // Guardar respuesta de encuesta
    public function guardarRespuesta(Request $request)
    {
        $data = $request->validate([
            'encuesta_id' => 'required|integer',
            'respuestas' => 'required|array',
        ]);

        try {
            // Verificar si la encuesta permite repetir
            $encuesta = DB::table('encuestas')->where('id', $data['encuesta_id'])->first();
            
            if (!$encuesta) {
                return response()->json(['success' => false, 'message' => 'Encuesta no encontrada'], 404);
            }
            
            // Si no permite repetir, verificar si el usuario ya respondió
            if (!$encuesta->permitir_repetir) {
                $ipAddress = $request->ip();
                $userAgent = $request->userAgent();
                $usuarioId = auth()->id();
                
                // Verificar por usuario autenticado o por IP + User Agent
                $yaRespondio = DB::table('encuestas_respuestas')
                    ->where('encuesta_id', $data['encuesta_id'])
                    ->where(function($query) use ($usuarioId, $ipAddress, $userAgent) {
                        if ($usuarioId) {
                            $query->where('usuario_id', $usuarioId);
                        } else {
                            $query->where('ip_address', $ipAddress)
                                  ->where('user_agent', $userAgent);
                        }
                    })
                    ->exists();
                
                if ($yaRespondio) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Ya has respondido esta encuesta anteriormente. No se permiten respuestas múltiples.'
                    ], 400);
                }
            }
            
            $payload = [
                'encuesta_id' => $data['encuesta_id'],
                'respuestas' => json_encode($data['respuestas']),
                'usuario_id' => auth()->id() ?? null,
                'usuario_nombre' => auth()->user()->name ?? 'Anónimo',
                'usuario_email' => auth()->user()->email ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('encuestas_respuestas')->insert($payload);

            return response()->json(['success' => true, 'message' => 'Respuesta guardada correctamente']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar la respuesta: ' . $e->getMessage()], 500);
        }
    }

    // Obtener respuestas de una encuesta
    public function getRespuestas($id)
    {
        try {
            $respuestas = DB::table('encuestas_respuestas')
                ->where('encuesta_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'usuario_nombre' => $item->usuario_nombre ?? 'Anónimo',
                        'usuario_email' => $item->usuario_email ?? 'N/A',
                        'fecha' => $item->created_at,
                        'respuestas' => json_decode($item->respuestas, true),
                    ];
                });

            return response()->json(['success' => true, 'respuestas' => $respuestas]);
        } catch (\Throwable $e) {
            return response()->json(['success' => true, 'respuestas' => []]);
        }
    }

    // Permitir que un usuario específico repita la encuesta
    public function permitirRepetir($encuestaId, $respuestaId)
    {
        try {
            // Verificar que la respuesta existe y pertenece a la encuesta
            $respuesta = DB::table('encuestas_respuestas')
                ->where('id', $respuestaId)
                ->where('encuesta_id', $encuestaId)
                ->first();

            if (!$respuesta) {
                return response()->json(['success' => false, 'message' => 'Respuesta no encontrada'], 404);
            }

            // Eliminar la respuesta para permitir que el usuario responda nuevamente
            DB::table('encuestas_respuestas')->where('id', $respuestaId)->delete();

            return response()->json([
                'success' => true, 
                'message' => 'La respuesta ha sido eliminada. El usuario ahora puede responder nuevamente.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
}
