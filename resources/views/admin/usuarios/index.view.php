@extends('layout.app')

@section('title')
    Lista de Usuarios
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Lista de Usuarios
            <small>Administración del sistema</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <a href="{{ $assetBase }}/usuarios/create" class="btn btn-primary btn-sm">
                    <i class="fa fa-user-plus"></i> Nuevo Usuario
                </a>
                <a href="{{ $assetBase }}/usuarios/wastebasket" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> Papelera
                </a>

                <div class="box-tools pull-right">
                    <form action="{{ $assetBase }}/usuarios" method="GET" role="search">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" class="form-control pull-right"
                                value="{{ $_GET['search'] ?? '' }}" placeholder="Usuario a buscar..." aria-label="Search">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="box-body table-responsive">
                @if (count($usuarios['data']) > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Avatar</th>
                                <th>Nombre de Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th class="text-center">Roles</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = $usuarios['from'] - 1;
                            @endphp
                            @foreach ($usuarios['data'] as $usuario)
                                @php
                                    $contador++;
                                    $fotoNombre = !empty($usuario['us_foto'])
                                        ? $usuario['us_foto']
                                        : 'no-disponible.png';
                                    $rutaFisica = dirname($_SERVER['SCRIPT_FILENAME']) . '/uploads/' . $fotoNombre;

                                    if (!file_exists($rutaFisica)) {
                                        $fotoNombre = 'no-disponible.png';
                                    }

                                    $avatarUrl = $assetBase . '/uploads/' . $fotoNombre;
                                @endphp
                                <tr>
                                    <td>{{ $contador }}</td>
                                    <td>
                                        <img src="{{ $avatarUrl }}"
                                            style="border-radius: 50%; width: 40px; height: 40px; object-fit: cover;"
                                            alt="Avatar">
                                    </td>
                                    <td>{{ $usuario['us_login'] }}</td>
                                    <td>{{ !empty($usuario['nombre_completo']) ? $usuario['nombre_completo'] : 'Sin asignar' }}
                                    </td>
                                    <td>{{ $usuario['us_email'] }}</td>
                                    <td class="text-center">
                                        <a href="{{ $assetBase }}/usuarios/{{ $usuario['id_usuario'] }}/roles"
                                            class="btn btn-xs btn-primary" title="Roles">
                                            <i class="fa fa-users"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ $assetBase }}/usuarios/{{ $usuario['id_usuario'] }}/edit""
                                                class="btn btn-warning btn-xs" title="Editar">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-xs"
                                                onclick="confirmarEliminacion({{ $usuario['id_usuario'] }})"
                                                title="Eliminar">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @include('includes.pagination')
                @else
                    <div class="alert alert-info text-center">
                        Aún no se han registrado Usuarios o no coinciden con la búsqueda.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        function confirmarEliminacion(idUsuario) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El usuario será enviado a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/usuarios/${idUsuario}/delete`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.ok || data.success) {
                                Swal.fire(
                                    '¡Eliminado!',
                                    data.mensaje || data.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', data.mensaje || data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
                        });
                }
            });
        }
    </script>
@endsection
