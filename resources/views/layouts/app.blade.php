<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard') - CABI</title>
    <link rel="icon" href="{{ asset('Cabi.ico') }}" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">

    <style>
        :root{--brand:#2e3a75}
        /* Override AdminLTE primary color to match brand */
        .brand-link, .main-sidebar .nav-link:hover, .main-sidebar .nav-link.active {
              background: var(--brand) !important;
              color: #fff !important;
          }
        /* Sidebar background */
        .main-sidebar {
          background: var(--brand) !important;
          color: #fff;
        }
        .main-sidebar .nav-link {
          color: #fff !important;
        }
        /* Navbar uses corporate color */
        .main-header .navbar, .navbar.navbar-dark {
          background: var(--brand) !important;
          border-bottom: 1px solid rgba(255,255,255,0.06);
          color: #fff !important;
        }
        /* Responsive navbar layout helpers */
        .nav-welcome { display:flex; flex-direction:column; text-align:right; }
        @media(min-width:768px){ .nav-welcome { flex-direction:row; align-items:center; } }
        .nav-welcome .navbar-text { margin-right:0.8rem; }
        .main-header .navbar .nav-link,
        .main-header .navbar .navbar-brand,
        .main-header .navbar .navbar-text,
        .navbar.navbar-dark .nav-link,
        .navbar.navbar-dark .navbar-nav .nav-link {
          color: #fff !important;
        }
      /* Global background image (site-wide) */
      body {
        background-image: url('{{ asset('img/nuevologo.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
      }
      /* Safety: limitar tamaño de SVGs e iconos para evitar que crezcan desproporcionadamente */
      svg { max-width: 48px; max-height: 48px; }
      /* limitar icon font fallback y forzar tamaño razonable */
      .material-symbols-outlined { font-size: 1.25rem; line-height:1; }
      /* controles del carrusel e indicadores */
      #bannerCarousel button svg { width: 18px; height: 18px; }
      /* Generic responsive helpers */
      img, video, iframe { max-width: 100%; height: auto; display: block; }
      input, textarea, select, button { max-width: 100%; box-sizing: border-box; }
      /* Layout: make header and sidebar fixed; content scrolls independently */
      html, body { height: 100%; }
      .wrapper { min-height: 100vh; height: 100vh; overflow: hidden; }
      .main-header { position: fixed; top: 0; left: 0; right: 0; z-index: 1040; }
      .main-sidebar {
        position: fixed;
        top: 56px; /* height of header */
        bottom: 0;
        width: 250px;
        overflow: auto;
        z-index: 1030;
      }
      /* Content wrapper must leave space for fixed header + sidebar and be scrollable */
      .content-wrapper {
        margin-top: 56px;
        margin-left: 250px;
        height: calc(100vh - 56px);
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        background: rgba(245,247,251,0.85);
        padding: 1.25rem;
      }
      .main-footer { position: fixed; bottom: 0; left: 250px; right: 0; z-index: 1020; }
      /* Responsive: on small screens make sidebar off-canvas and content use full width */
      @media (max-width: 991px) {
        .main-sidebar {
          position: fixed;
          top: 56px;
          left: 0;
          bottom: 0;
          width: 240px;
          transform: translateX(-110%);
          transition: transform .25s ease-in-out;
          z-index: 2000;
          display: block;
        }
        /* When AdminLTE toggles the sidebar open it adds 'sidebar-open' on the body */
        body.sidebar-open .main-sidebar, body.sidebar-show .main-sidebar { transform: translateX(0); }
        .content-wrapper { margin-left: 0; padding: 0.75rem; }
        .main-footer { left: 0; }
        body { background-attachment: scroll; }
      }
    </style>

    @stack('head')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <div class="ml-auto nav-welcome">
      @guest
        <div class="navbar-text text-white" style="line-height:1">Bienvenido a CABI</div>
        <div style="line-height:1">
          <a href="{{ url('/login') }}" class="text-white mr-2">Iniciar sesión</a>
          <a href="{{ url('/register') }}" class="text-white">Registrarse</a>
        </div>
      @else
        @php
          $firstName = Auth::user()->nombre ?? Auth::user()->name ?? '';
          $apellido1 = Auth::user()->apellido1 ?? '';
          $apellido2 = Auth::user()->apellido2 ?? '';
          $fullName = trim($firstName . ' ' . $apellido1 . ' ' . $apellido2);
        @endphp
        <div class="navbar-text text-white" style="line-height:1">Bienvenido a CABI</div>
        <div class="text-white d-flex align-items-center" style="line-height:1">
          <span class="d-none d-sm-inline">{{ $fullName }}</span>
          <button type="button" class="btn btn-link text-white p-0 ml-2" data-toggle="modal" data-target="#logoutModal">Cerrar sesión</button>
          <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">
            @csrf
          </form>
        </div>
      @endguest
    </div>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link" style="display:flex;align-items:center;">
      <div style="width:40px;height:40px;background:#fff;color:var(--brand);font-weight:700;border-radius:6px;display:flex;align-items:center;justify-content:center;margin:6px">C</div>
      <span class="brand-text font-weight-light" style="margin-left:6px">CABI</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <!-- Bienestar submenu -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-folder"></i>
              <p>Bienestar <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-dumbbell"></i>
                  <p>GYM <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item"><a href="{{ url('/bienestar/gym/inscripcion') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Inscripción</p></a></li>
                  @if(Auth::check() && Auth::user()->role === 'Super Admin')
                  <li class="nav-item"><a href="{{ url('/bienestar/gym/agenda') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Agenda tu Horario</p></a></li>
                  @endif
                </ul>
              </li>
                <!-- 
              <li class="nav-item"><a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Item 2</p></a></li> -->
            </ul>
          </li>
          <!-- Configuración submenu (visible para Super Admin, Administrador y Operador) -->
          @if(Auth::check() && in_array(Auth::user()->role, ['Super Admin','Administrador','Operador']))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>Configuración <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="{{ url('/config/usuarios') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Gestión de Usuarios</p></a></li>
              <li class="nav-item"><a href="{{ url('/config/servicios') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Gestión Servicios</p></a></li>
              <li class="nav-item"><a href="{{ url('/config/vinculaciones') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Gestión Vinculaciones</p></a></li>
              	<li class="nav-item"><a href="{{ url('/config/publicidad') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Gestión de Publicidad</p></a></li>
              <li class="nav-item"><a href="{{ url('/config/cursos') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Gestión Cursos</p></a></li>
              <li class="nav-item"><a href="{{ url('/config/encuestas') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Gestión Encuestas</p></a></li>
              <li class="nav-item"><a href="{{ url('/config/eventos') }}" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Gestión de Eventos</p></a></li>
            </ul>
          </li>
          @endif
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        @if(session('success'))
        <div class="row">
          <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
          </div>
        </div>
        @endif
        @if(session('error'))
        <div class="row">
          <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
          </div>
        </div>
        @endif
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>@yield('header','Panel')</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        @yield('content')
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer text-sm" style="background:#fff">
    <div class="float-right d-none d-sm-inline">CABI</div>
    <strong>&copy; {{ date('Y') }} CABI.</strong>
  </footer>
</div>

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--brand); color:#fff; border-bottom:none;">
        <h5 class="modal-title" id="logoutModalLabel">Confirmar cierre de sesión</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mb-0">¿Está seguro de que desea cerrar su sesión?</p>
        <p class="text-muted small mt-2">Se le redirigirá a la página principal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" id="confirmLogoutBtn" class="btn" style="background:var(--brand); color:#fff;">Cerrar sesión</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var btn = document.getElementById('confirmLogoutBtn');
  if(btn){
    btn.addEventListener('click', function(){
      var form = document.getElementById('logoutForm');
      if(form) form.submit();
    });
  }
});
</script>

@stack('scripts')
</body>
</html>

<script>
// Función global para paginar tablas en el cliente (12 por página por defecto)
window.applyPagination = function(tableSelector, perPage = 12) {
  try {
    const table = document.querySelector(tableSelector);
    if(!table) return;
    const tbody = table.tBodies[0] || table.querySelector('tbody');
    if(!tbody) return;

    // eliminar controles anteriores si existen
    const existing = table.parentElement.querySelector('.table-pagination-controls');
    if(existing) existing.remove();

    const rows = Array.from(tbody.querySelectorAll('tr'));
    const total = rows.length;
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    let current = 1;

    const controls = document.createElement('div');
    controls.className = 'table-pagination-controls d-flex align-items-center justify-content-between mt-3';
    controls.innerHTML = `
      <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary" data-action="prev">&laquo; Anterior</button>
        <button class="btn btn-sm btn-outline-secondary" data-action="next">Siguiente &raquo;</button>
      </div>
      <div class="text-muted small">Página <span class="current">${current}</span> de <span class="total">${totalPages}</span></div>
    `;

    table.parentElement.appendChild(controls);

    function renderPage(page){
      if(page < 1) page = 1;
      if(page > totalPages) page = totalPages;
      const start = (page-1)*perPage;
      const end = start + perPage;
      rows.forEach((r,i)=>{
        r.style.display = (i>=start && i<end) ? '' : 'none';
      });
      current = page;
      controls.querySelector('.current').textContent = current;
      controls.querySelectorAll('button[data-action]').forEach(b=>{
        const action = b.getAttribute('data-action');
        if(action === 'prev') b.disabled = current === 1;
        if(action === 'next') b.disabled = current === totalPages;
      });
    }

    controls.addEventListener('click', function(e){
      const btn = e.target.closest('button[data-action]');
      if(!btn) return;
      const action = btn.getAttribute('data-action');
      if(action === 'prev') renderPage(current-1);
      if(action === 'next') renderPage(current+1);
    });

    // inicial
    renderPage(1);
  } catch (e) {
    console.error('applyPagination error', e);
  }
}
</script>
