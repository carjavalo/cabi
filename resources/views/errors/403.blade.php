<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso restringido · CABI</title>
    <link rel="icon" href="/Cabi.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root { --brand: #2e3a75; --brand-2: #1f4fa3; --texto: #3b4453; --suave: #6b7280; }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: "Source Sans Pro", "Segoe UI", Roboto, Arial, sans-serif; color: var(--texto);
            background: linear-gradient(135deg, #eef2fa 0%, #f7f9fc 55%, #e7ecf7 100%); padding: 24px 16px;
        }
        .tarjeta {
            width: 100%; max-width: 560px; background: #fff; border-radius: 16px; overflow: hidden;
            box-shadow: 0 18px 45px rgba(46, 58, 117, .14); text-align: center;
        }
        .franja { height: 6px; background: linear-gradient(90deg, var(--brand), var(--brand-2)); }
        .contenido { padding: 34px 36px 30px; }
        .logo { max-width: 260px; width: 70%; height: auto; margin-bottom: 26px; }
        .icono {
            width: 84px; height: 84px; border-radius: 50%; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center;
            background: rgba(46, 58, 117, .08); color: var(--brand); font-size: 36px;
        }
        h1 { margin: 0 0 10px; font-size: 24px; color: var(--brand); font-weight: 700; }
        p { margin: 0 0 8px; line-height: 1.55; color: var(--suave); font-size: 15px; }
        .detalle {
            margin: 18px 0 24px; padding: 12px 14px; border-radius: 10px; background: #f5f7fb; border: 1px solid #e3e8f3;
            font-size: 13.5px; color: var(--texto); text-align: left;
        }
        .detalle div + div { margin-top: 4px; }
        .detalle i { color: var(--brand); width: 18px; }
        .acciones { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 14.5px;
            font-weight: 600; text-decoration: none; cursor: pointer; border: 1px solid var(--brand); transition: all .15s ease;
        }
        .btn-primario { background: var(--brand); color: #fff; }
        .btn-primario:hover { background: #24305f; }
        .btn-secundario { background: #fff; color: var(--brand); }
        .btn-secundario:hover { background: rgba(46, 58, 117, .06); }
        .pie { padding: 14px; font-size: 12px; color: #9aa3b2; border-top: 1px solid #eef1f6; }
        @media (max-width: 480px) { .contenido { padding: 28px 20px 24px; } h1 { font-size: 21px; } .btn { width: 100%; justify-content: center; } }
    </style>
</head>
<body>
    <div class="tarjeta">
        <div class="franja"></div>
        <div class="contenido">
            <img src="{{ asset('img/huv-h.png') }}" alt="Hospital Universitario del Valle" class="logo">
            <div class="icono"><i class="fas fa-user-lock"></i></div>
            <h1>Acceso restringido</h1>
            <p>Lo sentimos, su perfil no cuenta con autorización para ingresar a esta sección.</p>
            <p>Si considera que necesita este acceso para sus labores, solicítelo al administrador del sistema.</p>

            @auth
            <div class="detalle">
                <div><i class="fas fa-user"></i> {{ trim(auth()->user()->name . ' ' . (auth()->user()->apellido1 ?? '')) }}</div>
                <div><i class="fas fa-id-badge"></i> Rol: <strong>{{ auth()->user()->role ?: 'Sin rol' }}</strong></div>
            </div>
            @endauth

            <div class="acciones">
                <a href="javascript:history.length > 1 ? history.back() : location.assign('{{ url('/') }}')" class="btn btn-secundario"><i class="fas fa-arrow-left"></i> Regresar</a>
                <a href="{{ url('/') }}" class="btn btn-primario"><i class="fas fa-home"></i> Ir al inicio</a>
            </div>
        </div>
        <div class="pie">CABI · Hospital Universitario del Valle "Evaristo García" E.S.E.</div>
    </div>
</body>
</html>
