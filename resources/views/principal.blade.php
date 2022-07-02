<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Prueba ACME">
    <meta name="author" content="Martin Cordoba">
    <meta name="keyword" content="Prueba ACME">
    <link rel="shortcut icon" href="img/acme.png">
    <meta name="userId" content="{{ Auth::check() ? Auth::user()->id : ''}}">  

    <title>ACME</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js">
    <!-- Icons -->
    <link href="css/plantilla.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/w/dt/dt-1.10.18/r-2.2.2/rg-1.1.0/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css"/>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.7.2/css/all.min.css"/>

</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
    <div id="app">
        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
 
            <ul class="nav navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                        <span class="d-md-down-none">{{Auth::user()->usuario}} </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header text-center">
                            <strong>Cuenta</strong>
                        </div>

                        <a class="dropdown-item" @click="menu=9" href="#">  <!-- Opción Add by MAELCO -->
                            <i class="fa fa-key"></i> Cambiar Contraseña
                        </a>

                        <a class="dropdown-item" href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-lock"></i> Cerrar sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
            </ul>
        </header>

        <div class="app-body">
            
            @if(Auth::check())
                @if (Auth::user()->idrol == 1)
                    @include('plantilla.sidebaradministrador')
                @elseif (Auth::user()->idrol == 2)
                    @include('plantilla.sidebaroperador')
                @else

                @endif

            @endif
            <!-- Contenido Principal -->
            @yield('contenido')
            <!-- /Fin del contenido principal -->
        </div>   
    </div>
    <footer class="app-footer">
        <span>Desarrollado por <a >Martín Córdoba &copy; 2022</a></span>
    </footer>
    <script src="js/app.js"></script>
    <script src="js/plantilla.js"></script>

    <script src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/datatable/moment.min.js') }}"></script>


    <script src="{{ asset('js/datatable/vfs_fonts.js') }}"></script>


    <script src="{{ asset('js/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/datatable/responsive.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/datatable/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('js/datatable/datetime-moment.js') }}"></script>

</body>

</html>