<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\HorarioGim;

class AgendaHorarioController extends Controller
{
    public function index()
    {
        return view('bienestar.gym.agenda');
    }

    public function store(Request $request)
    {
        // Only require identification, day and horario. Personal fields are autofilled from inscripgym.
        $data = $request->validate([
            'identificacion' => 'required|string|max:100',
            'dia_entrenamiento' => 'required|string|max:50',
            'horario' => 'required|string|max:50',
            'nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'nullable|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'servicio' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'correolec' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:50',
        ]);

        // derive fecha (today) and hora (start time) from horario when possible
        $fecha = now()->toDateString();
        $hora = now()->toTimeString();

        if (!empty($data['horario']) && str_contains($data['horario'], '-')) {
            $parts = array_map('trim', explode('-', $data['horario']));
            if (!empty($parts[0])) {
                // ensure format HH:MM[:SS]
                $hora = strlen($parts[0]) <= 5 ? $parts[0] . ':00' : $parts[0];
            }
        }

        // Try to fetch the personal data from inscripgym using identificacion
        $inscrip = DB::table('inscripgym')->where('identificacion', $data['identificacion'])->first();

        // Check if they exist in inscripgym
        if (!$inscrip) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['identificacion' => 'No se encuentra registrado en el sistema. Por favor inscríbase primero.']);
        }

        // Check authorization (Must be 1 to allow scheduling)
        if (!$inscrip->autorizado) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['identificacion' => 'No está autorizado para agendar. Por favor complete su proceso de autorización en Bienestar.']);
        }

        // Prevent duplicate horariosgim for the same identificacion (business validation)
        if (DB::table('horariosgim')->where('identificacion', $data['identificacion'])->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['identificacion' => 'Ya existe una cita registrada con esa identificación. Si necesita otra cita, contacte al administrador.']);
        }

        $createData = [
            'identificacion' => $data['identificacion'],
            'dia_entrenamiento' => $data['dia_entrenamiento'],
            'horario' => $data['horario'],
            'fecha' => $fecha,
            'hora' => $hora,
        ];

        // populate personal fields from inscripgym when available, otherwise use submitted values or empty strings
        $createData['nombre'] = $inscrip->nombres ?? ($data['nombre'] ?? '');
        $createData['primer_apellido'] = $inscrip->primer_apellido ?? ($data['primer_apellido'] ?? '');
        $createData['segundo_apellido'] = $inscrip->segundo_apellido ?? ($data['segundo_apellido'] ?? null);
        $createData['email'] = $inscrip->email ?? ($data['email'] ?? null);
        $createData['telefono'] = $inscrip->celular ?? ($data['telefono'] ?? null);
        $createData['servicio'] = $inscrip->servicio ?? ($inscrip->servicio_unidad ?? ($data['servicio'] ?? ''));

        // Create horario entry
        HorarioGim::create($createData);

        // If inscription record exists or correolec was provided, update inscripgym
        if (!empty($data['identificacion'])) {
            $insData = [
                'nombres' => $createData['nombre'],
                'primer_apellido' => $createData['primer_apellido'],
                'segundo_apellido' => $createData['segundo_apellido'],
                'identificacion' => $createData['identificacion'],
                'celular' => $createData['telefono'] ?? null,
                'correolec' => $data['correolec'] ?? ($inscrip->correolec ?? null),
            ];
            // Ensure the identificacion column exists to avoid SQL errors
            if (!Schema::hasColumn('inscripgym', 'identificacion')) {
                try {
                    Schema::table('inscripgym', function ($table) {
                        $table->string('identificacion')->nullable();
                    });
                } catch (\Exception $e) {
                    // ignore alter errors
                }
            }
            DB::table('inscripgym')->updateOrInsert(
                ['identificacion' => $data['identificacion']],
                $insData
            );
        }

        return redirect()->route('agenda.horario')->with('success', 'Cita agendada correctamente.');
    }

    // Fetch inscripgym record by identificacion (AJAX)
    public function fetchInscripById($identificacion)
    {
        $record = \Illuminate\Support\Facades\DB::table('inscripgym')
            ->where('identificacion', $identificacion)
            ->first();

        if (!$record) {
            return response()->json(['found' => false]);
        }

        if (!$record->autorizado) {
            return response()->json([
                'found' => true,
                'authorized' => false,
                'message' => 'No está autorizado para agendar.'
            ]);
        }

        // Map DB columns to form field names (inscripgym uses 'nombres' and 'celular')
        $mapped = [
            'nombre' => $record->nombres ?? ($record->nombre ?? null),
            'primer_apellido' => $record->primer_apellido ?? null,
            'segundo_apellido' => $record->segundo_apellido ?? null,
            'email' => $record->email ?? null,
            'correolec' => $record->correolec ?? null,
            'telefono' => $record->celular ?? ($record->telefono ?? null),
            'servicio' => $record->servicio_unidad ?? ($record->servicio ?? null),
        ];

        return response()->json(['found' => true, 'data' => $mapped]);
    }

    // Store inscription from inscripcion form into inscripgym
    public function storeInscription(Request $request)
    {
        $data = $request->validate([
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'identificacion' => 'required|string|max:100|unique:inscripgym,identificacion',
            'edad' => 'nullable|integer',
            'celular' => 'nullable|string|max:50',
            'tipo_vinculacion_id' => 'nullable|integer|exists:vinculaciones,id',
            'servicio_id' => 'nullable|integer|exists:servicios,id',
            'contacto_emergencia' => 'nullable|string|max:255',
            'correolec' => 'nullable|email|max:150|unique:inscripgym,correolec',
        ], [
            'identificacion.unique' => 'Esta identificación ya se encuentra registrada en el sistema.',
            'correolec.unique' => 'Este correo institucional ya se encuentra registrado en el sistema.'
        ]);

        // Ensure identificacion column exists before attempting updateOrInsert
        if (!Schema::hasColumn('inscripgym', 'identificacion')) {
            try {
                Schema::table('inscripgym', function ($table) {
                    $table->string('identificacion')->nullable();
                });
            } catch (\Exception $e) {
                // ignore
            }
        }

        // Resolve names for vinculacion and servicio if IDs provided
        $tipoNombre = null;
        $servicioNombre = null;
        if (!empty($data['tipo_vinculacion_id'])) {
            $v = \App\Models\Vinculacion::find($data['tipo_vinculacion_id']);
            $tipoNombre = $v->nombre ?? null;
        }
        if (!empty($data['servicio_id'])) {
            $s = \App\Models\Servicio::find($data['servicio_id']);
            $servicioNombre = $s->nombre ?? null;
        }

        \Illuminate\Support\Facades\DB::table('inscripgym')->insert([
            'nombres' => $data['nombres'],
            'primer_apellido' => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'] ?? '',
            'identificacion' => $data['identificacion'],
            'edad' => $data['edad'] ?? null,
            'celular' => $data['celular'] ?? null,
            'tipo_vinculacion' => $tipoNombre ?? '',
            'servicio_unidad' => $servicioNombre ?? '',
            'contacto_emergencia' => $data['contacto_emergencia'] ?? '',
            'correolec' => $data['correolec'] ?? null,
        ]);

        // Volver al formulario de inscripción en lugar de ir a la agenda
        return redirect('/bienestar/gym/inscripcion')->with('success', 'Inscripción guardada correctamente.');
    }
}
