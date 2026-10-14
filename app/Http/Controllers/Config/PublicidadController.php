<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publicidad;
use App\Support\Archivos;

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
            try {
                $data['banner'] = $this->guardarImagen($request, $data['titulo'] ?? null);
            } catch (\Throwable $e) {
                report($e);
                return response()->json(['success' => false, 'message' => 'No fue posible guardar la imagen. Verifique la conexión con el almacenamiento de archivos.'], 500);
            }
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

        // Referencia guardada en la base (el accessor "banner" devuelve un enlace, no la referencia)
        $bannerAnterior = $item->getRawOriginal('banner');
        if ($request->hasFile('imagen')) {
            try {
                $data['banner'] = $this->guardarImagen($request, $data['titulo'] ?? $item->titulo);
            } catch (\Throwable $e) {
                report($e);
                return response()->json(['success' => false, 'message' => 'No fue posible guardar la imagen. Verifique la conexión con el almacenamiento de archivos.'], 500);
            }
        }

        $payload = array_filter([
            'titulo' => $data['titulo'] ?? $item->titulo,
            'tag' => $data['tag'] ?? $item->tag,
            'descripcion' => $data['descripcion'] ?? $item->descripcion,
            'link' => $data['link'] ?? $item->link,
            'seccion_titulo' => $data['seccion_titulo'] ?? $item->seccion_titulo,
            'seccion_subtitulo' => $data['seccion_subtitulo'] ?? $item->seccion_subtitulo,
            'banner' => $data['banner'] ?? $bannerAnterior,
            'prioridad' => $data['prioridad'] ?? $item->prioridad,
            'fecha_inicio' => $data['fecha_inicio'] ?? $item->fecha_inicio,
            'fecha_fin' => $data['fecha_fin'] ?? $item->fecha_fin,
            'activo' => $data['activo'] ?? $item->activo,
        ]);

        $item->update($payload);
        // La imagen reemplazada ya no se usa
        if (!empty($data['banner']) && $bannerAnterior && $bannerAnterior !== $data['banner']) {
            Archivos::eliminar($bannerAnterior);
        }
        return response()->json(['success' => true, 'item' => $item]);
    }

    public function destroy($id)
    {
        $item = Publicidad::findOrFail($id);
        $banner = $item->getRawOriginal('banner');
        $item->delete();
        Archivos::eliminar($banner);
        return response()->json(['success' => true]);
    }

    /** Guarda la imagen en el almacenamiento (S3): talentoHumanoHUV/publicidad/publicidad_{título}_{fecha}.{ext} */
    private function guardarImagen(Request $request, ?string $titulo): string
    {
        $archivo = $request->file('imagen');
        $nombre = 'publicidad_' . ($titulo ?: pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . now()->format('Y-m-d_His');
        return Archivos::guardar($archivo, 'publicidad', $nombre);
    }
}
