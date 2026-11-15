@extends('layout.app')

@section('title')
    Papelera de Usuarios
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Papelera de Usuarios
            <small>Registros eliminados temporalmente</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-danger">
            <div class="box-header with-border">
                <a href="{{ $assetBase }}/usuarios" class="btn btn-default btn-sm">
                    <i class="fa fa-arrow-left"></i> Volver al Listado de Usuarios
                </a>

                <div class="box-tools pull-right">
                    <form action="{{ $assetBase }}/usuarios/wastebasket" method="GET" role="search">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" class="form-control pull-right"
                                value="{{ $_GET['search'] ?? '' }}" placeholder="Buscar en papelera...">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="box-body table-responsive">
                @if (isset($usuarios['data']) && count($usuarios['data']) > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Avatar</th>
                                <th>Nombre de Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Eliminado el</th>
                                <th class="text-center" style="width: 170px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = ($usuarios['from'] ?? 1) - 1;
                            @endphp
                            @foreach ($usuarios['data'] as $usuario)
                                @php
                                    $contador++;
                                    $fotoNombre = !empty($usuario['us_foto']) ? $usuario['us_foto'] : 'no-disponible.png';
                                    $rutaFisica = dirname($_SERVER['SCRIPT_FILENAME']) . '/uploads/' . $fotoNombre;
                                    if (!file_exists($rutaFisica)) {
                                        $fotoNombre = 'no-disponible.png';
                                    }
                                    $avatarUrl = $assetBase . '/uploads/' . $fotoNombre;
                                @endphp
                                <tr>
                                    <td>{{ $contador }}</td>
                                    <td>
                                        <img src="{{ $avatarUrl }}" style="border-radius: 50%; width: 40px; height: 40px; object-fit: cover;" alt="Avatar">
                                    </td>
                                    <td>{{ $usuario['us_login'] }}</td>
                                    <td>{{ !empty($usuario['nombre_completo']) ? $usuario['nombre_completo'] : 'Sin asignar' }}</td>
                                    <td>{{ $usuario['us_email'] }}</td>
                                    <td>{{ $usuario['deleted_at'] }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-xs"
                                            onclick="confirmarRestauracion({{ $usuario['id_usuario'] }})" title="Restaurar Usuario">
                                            <i class="fa fa-undo"></i> Restaurar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-xs"
                                            onclick="confirmarEliminacionDefinitiva({{ $usuario['id_usuario'] }})" title="Eliminar Permanentemente">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @include('includes.pagination')
                @else
                    <div class="alert alert-info text-center">
                        No hay usuarios en la papelera.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        function confirmarRestauracion(idUsuario) {
            Swal.fire({
                title: '¿Restaurar usuario?',
                text: "El usuario volverá a estar activo en el listado principal.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00a65a',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/usuarios/${idUsuario}/restore`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.ok || data.success) {
                            Swal.fire('¡Restaurado!', data.mensaje || data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.mensaje || data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
                    });
                }
            });
        }

        function confirmarEliminacionDefinitiva(idUsuario) {
            Swal.fire({
                title: '¿Eliminar permanentemente?',
                text: "Esta acción borrará al usuario de forma definitiva y no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dd4b39',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar para siempre',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/usuarios/${idUsuario}/destroy`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.ok || data.success) {
                            Swal.fire(data.titulo || '¡Eliminado!', data.mensaje || data.message, data.estado || 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire(data.titulo || 'No se puede eliminar', data.mensaje || data.message, data.estado || 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
                    });
                }
            });
        }
    </script>
@endsection
