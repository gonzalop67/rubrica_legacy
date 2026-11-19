@extends('layout.app')

@section('title')
    Papelera de Perfiles
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Perfiles Eliminados (Papelera)
            <small>Registros eliminados temporalmente</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-danger">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{ $assetBase }}/roles" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Volver al Listado de Roles
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form action="{{ $assetBase }}/roles/wastebasket" method="GET" role="search"
                            class="pull-right" style="margin: 0;">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control"
                                    value="{{ $_GET['search'] ?? '' }}" placeholder="Buscar en papelera...">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
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
                                <th>Eliminado el</th>
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
                                    <td>{{ $role['deleted_at'] }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-xs"
                                                onclick="confirmarRestauracion({{ $role['id_perfil'] }})"
                                                title="Restaurar Perfil">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs"
                                                onclick="confirmarEliminacionDefinitiva({{ $role['id_perfil'] }})"
                                                title="Eliminar Permanentemente">
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
                        La papelera está vacía.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        function confirmarRestauracion(idPerfil) {
            Swal.fire({
                title: '¿Restaurar perfil?',
                text: "El perfil volverá a estar activo en la lista principal.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00a65a',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/roles/${idPerfil}/restore`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success || data.ok) {
                                Swal.fire('¡Restaurado!', data.message || data.mensaje ||
                                        'El perfil ha sido activado.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message || data.mensaje ||
                                    'No se pudo restaurar el perfil.', 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Ocurrió un fallo en la comunicación con el servidor.', 'error');
                        });
                }
            });
        }

        function confirmarEliminacionDefinitiva(idPerfil) {
            Swal.fire({
                title: '¿Eliminar permanentemente?',
                text: "Esta acción no se puede deshacer de ninguna manera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar definitivamente',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/roles/${idPerfil}/destroy`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success || data.ok) {
                                Swal.fire('¡Eliminado!', data.mensaje || data.message ||
                                        'El perfil ha sido borrado para siempre.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire({
                                    title: data.titulo || 'No se puede eliminar',
                                    text: data.mensaje || data.message,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Ocurrió un fallo en la comunicación con el servidor.', 'error');
                        });
                }
            });
        }
    </script>
@endsection
