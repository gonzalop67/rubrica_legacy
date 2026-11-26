@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $assetBase = preg_replace('#/(public|admin|usuarios|vistas).*$#', '', $scriptDir);
    $assetBase = rtrim($assetBase, '/');

    $avatar = !empty($_SESSION['us_avatar']) ? $_SESSION['us_avatar'] : 'default.png';
    $userImage = $assetBase . '/public/uploads/' . ltrim($avatar, '/');

    /* echo '<pre style="background:#222; color:#0f0; padding:10px; position:relative; z-index:9999;">';
    print_r($_SESSION);
    echo '</pre>'; */
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SIAE Web | @yield('title')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="icon" type="image/x-icon" href="{{ $assetBase }}/public/img/favicon.ico">

    <script src="{{ $assetBase }}/bower_components/jquery/dist/jquery.min.js"></script>
    <script>
        const base_url = "{{ $assetBase }}";
    </script>

    <link rel="stylesheet" href="{{ $assetBase }}/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/dist/css/skins/skin-blue.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/assets/plugins/node_modules/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/assets/datatables/datatables.css">
    <link rel="stylesheet" href="{{ $assetBase }}/estilos.css">

    <style>
        /* Reduce el espacio vacío entre el contenido y el footer en AdminLTE 2 */
        .content-wrapper {
            min-height: calc(100vh - 101px) !important;
            padding-bottom: 15px;
        }

        .content {
            min-height: auto !important;
            padding-bottom: 0 !important;
        }

        .box {
            margin-bottom: 0 !important;
        }
    </style>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans Pro:300,400,600,700,300italic,400italic,600italic">

    @yield('styles')
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- Header -->
        <header class="main-header">
            <a href="{{ $assetBase }}/admin.php?id_usuario={{ $idUsuarioEnc }}&id_perfil={{ $idPerfil }}&enlace=dashboard.php&nivel=0"
                class="logo">
                <span class="logo-mini"><b>SIAE</b></span>
                <span class="logo-lg"><b>SIAE {{ $aniosPeriodoLectivo }}</b></span>
            </a>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ $userImage }}" class="user-image" alt="User Image">
                                <span class="hidden-xs">{{ $nombreUsuario }}</span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ $assetBase }}/logout.php" class="btn-logout"><i
                                            class="fa fa-sign-out" aria-hidden="true"></i> Salir</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Sidebar dinámico (convive con rutas legacy y rutas nuevas MVC) -->
        <aside class="main-sidebar">
            <section class="sidebar">
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="{{ $userImage }}" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>{{ $nombrePerfil }}</p>
                        <small>{{ $nombreOfertaEducativa }}</small>
                    </div>
                </div>

                <ul class="sidebar-menu" data-widget="tree">
                    <li>
                        <a
                            href="{{ $assetBase }}/admin.php?id_usuario={{ $idUsuarioEnc }}&id_perfil={{ $idPerfil }}&enlace=dashboard.php&nivel=0">
                            <i class="fa fa-home"></i> <span>Dashboard</span>
                        </a>
                    </li>

                    @foreach ($menusSidebar as $menu)
                        @if (count($menu['submenus']) > 0)
                            <li class="treeview">
                                <a href="#">
                                    <i class="{{ $menu['mnu_icono'] ?: 'fa fa-link' }}"></i>
                                    <span>{{ $menu['mnu_texto'] }}</span>
                                    <span class="pull-right-container"><i
                                            class="fa fa-angle-left pull-right"></i></span>
                                </a>
                                <ul class="treeview-menu">
                                    @foreach ($menu['submenus'] as $sub)
                                        @php
                                            $enlaceSub = (string) ($sub['mnu_enlace'] ?? '');
                                            $urlSub = str_starts_with($enlaceSub, '/')
                                                ? $assetBase . $enlaceSub
                                                : $assetBase .
                                                    "/admin.php?id_usuario={$idUsuarioEnc}&id_perfil={$idPerfil}&id_menu={$sub['id_menu']}&nivel={$sub['mnu_nivel']}";
                                        @endphp
                                        <li>
                                            <a href="{{ $urlSub }}">
                                                <i class="fa fa-circle-o"></i> {{ $sub['mnu_texto'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            @php
                                $enlaceMenu = (string) ($menu['mnu_enlace'] ?? '');
                                $urlMenu = str_starts_with($enlaceMenu, '/')
                                    ? $assetBase . $enlaceMenu
                                    : $assetBase .
                                        "/admin.php?id_usuario={$idUsuarioEnc}&id_perfil={$idPerfil}&id_menu={$menu['id_menu']}&nivel={$menu['mnu_nivel']}";
                            @endphp
                            <li>
                                <a href="{{ $urlMenu }}">
                                    <i class="{{ $menu['mnu_icono'] ?: 'fa fa-link' }}"></i>
                                    <span>{{ $menu['mnu_texto'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </section>
        </aside>

        <!-- Contenido dinámico de cada módulo MVC -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <footer class="main-footer">
            <strong>{{ date('Y') }} &copy; SIAE Web.</strong> Todos los derechos reservados.
        </footer>

    </div>

    <script src="{{ $assetBase }}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="{{ $assetBase }}/dist/js/adminlte.min.js"></script>
    <script src="{{ $assetBase }}/assets/plugins/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="{{ $assetBase }}/assets/datatables/datatables.js"></script>
    @yield('scripts')
</body>

</html>
