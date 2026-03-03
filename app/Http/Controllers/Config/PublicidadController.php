<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publicidad;
use Illuminate\Support\Facades\Storage;

class PublicidadController extends Controller
{
    public function index()
    {
        return view('config.publicidad.index');
    }

    public function data()
    {
        $items = Publicidad::orderBy('id','desc')->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'link' => 'nullable|url|max:1000',
            'seccion_titulo' => 'nullable|string|max:255',
            'seccion_subtitulo' => 'nullable|string|max:255',
            'prioridad' => 'nullable|integer',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'activo' => 'nullable|boolean',
            'imagen' => 'nullable|file|image|max:5120',
        ]);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $folder = public_path('img/publicidad');
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            $name = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\\.]/', '', $file->getClientOriginalName());
            $file->move($folder, $name);
            $data['banner'] = url('img/publicidad/'.$name);
        }

        // map possible fields
        $payload = array_merge([
            'prioridad' => $data['prioridad'] ?? 3,
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'activo' => $data['activo'] ?? 1,
        ], array_filter([
            'titulo' => $data['titulo'] ?? null,
            'tag' => $data['tag'] ?? null,
            'descripcion' => $data['descripcion'] ?? null,
            'link' => $data['link'] ?? null,
            'seccion_titulo' => $data['seccion_titulo'] ?? null,
            'seccion_subtitulo' => $data['seccion_subtitulo'] ?? null,
            'banner' => $data['banner'] ?? null,
        ]));

        $record = Publicidad::create($payload);
        return response()->json(['success' => true, 'item' => $record]);
    }

    public function show($id)
    {
        $item = Publicidad::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Publicidad::findOrFail($id);
        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'link' => 'nullable|url|max:1000',
            'seccion_titulo' => 'nullable|string|max:255',
            'seccion_subtitulo' => 'nullable|string|max:255',
            'prioridad' => 'nullable|integer',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'activo' => 'nullable|boolean',
            'imagen' => 'nullable|file|image|max:5120',
        ]);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $folder = public_path('img/publicidad');
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            $name = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $file->move($folder, $name);
                $data['banner'] = url('img/publicidad/'.$name);
        }

        $payload = array_filter([
            'titulo' => $data['titulo'] ?? $item->titulo,
            'tag' => $data['tag'] ?? $item->tag,
            'descripcion' => $data['descripcion'] ?? $item->descripcion,
            'link' => $data['link'] ?? $item->link,
            'seccion_titulo' => $data['seccion_titulo'] ?? $item->seccion_titulo,
            'seccion_subtitulo' => $data['seccion_subtitulo'] ?? $item->seccion_subtitulo,
            'banner' => $data['banner'] ?? $item->banner,
            'prioridad' => $data['prioridad'] ?? $item->prioridad,
            'fecha_inicio' => $data['fecha_inicio'] ?? $item->fecha_inicio,
            'fecha_fin' => $data['fecha_fin'] ?? $item->fecha_fin,
            'activo' => $data['activo'] ?? $item->activo,
        ]);

        $item->update($payload);
        return response()->json(['success' => true, 'item' => $item]);
    }

    public function destroy($id)
    {
        $item = Publicidad::findOrFail($id);
        // opcional: borrar archivo físico si está en public/img/publicidad
        try {
            $path = $item->banner;
            if ($path && strpos($path, url('/')) !== false) {
                // intentamos borrar si corresponde a public/img/publicidad
                $relative = str_replace(url('/') . '/', '', $path);
                $full = public_path($relative);
                if (file_exists($full)) @unlink($full);
            }
        } catch (\Throwable $e) { /* ignore */ }
        $item->delete();
        return response()->json(['success' => true]);
    }
}
