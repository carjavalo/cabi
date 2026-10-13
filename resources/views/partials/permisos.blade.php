{{--
    Gestión de Permisos en la interfaz: oculta los botones de las funciones que el rol no tiene.
    Uso en cualquier vista (también en HTML generado por JavaScript):
        <button data-permiso="config.usuarios.create config.usuarios.store">Nuevo</button>
    Se oculta si falta CUALQUIERA de las claves. En JavaScript: CABI.puede('config.usuarios.destroy').
    El servidor sigue validando cada petición (middleware VerificarPermiso).
--}}
@php($cabiDenegados = \App\Support\GestorPermisos::denegados(auth()->user()))
@if($cabiDenegados)
<style id="cabi-permisos">
{!! collect($cabiDenegados)->map(fn ($c) => '[data-permiso~="' . e($c) . '"]')->implode(",\n") !!} { display: none !important; }
</style>
@endif
<script>
    window.CABI = window.CABI || {};
    window.CABI.denegados = @json(array_values($cabiDenegados));
    window.CABI.puede = function () {
        for (var i = 0; i < arguments.length; i++) {
            if (window.CABI.denegados.indexOf(arguments[i]) !== -1) return false;
        }
        return true;
    };
</script>
