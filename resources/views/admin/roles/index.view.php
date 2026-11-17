@extends('layout.app')

@section('title')
    Lista de Perfiles
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Perfiles
            <small>Administración de roles y permisos del sistema</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <a href="{{ $assetBase }}/roles/create" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Nuevo Rol
                </a>
                <a href="{{ $assetBase }}/roles/wastebasket" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> Papelera
                </a>

                <div class="box-tools pull-right">
                    <form action="{{ $assetBase }}/roles" method="GET" role="search">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" class="form-control pull-right"
                                value="{{ $_GET['search'] ?? '' }}" placeholder="Rol a buscar...">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="box-body table-responsive">
                @if (!empty($roles['data']) && count($roles['data']) > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Nombre del Rol</th>
                                <th>Slug</th>
                                <th class="text-center" style="width: 100px;">Permisos</th>
                                <th class="text-center" style="width: 110px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = ($roles['from'] ?? 1) - 1;
                            @endphp
                            @foreach ($roles['data'] as $role)
                                @php
                                    $contador++;
                                @endphp
                                <tr>
                                    <td>{{ $contador }}</td>
                                    <td>{{ $role['pe_nombre'] }}</td>
                                    <td><code>{{ $role['pe_slug'] }}</code></td>
                                    <td class="text-center">
                                        <a href="{{ $assetBase }}/roles/{{ $role['id_perfil'] }}/permissions"
                                            class="btn btn-xs btn-primary" title="Permisos">
                                            <i class="fa fa-shield"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ $assetBase }}/roles/{{ $role['id_perfil'] }}/edit"
                                                class="btn btn-success btn-xs" title="Editar Perfil">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-xs"
                                                onclick="confirmarEliminacion({{ $role['id_perfil'] }})"
                                                title="Eliminar Perfil">
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
                        Aún no se han registrado Perfiles.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        function confirmarEliminacion(idPerfil) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El perfil será enviado a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/roles/${idPerfil}/delete`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success || data.ok) {
                            Swal.fire(
                                '¡Eliminado!',
                                data.message || data.mensaje,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message || data.mensaje, 'error');
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
