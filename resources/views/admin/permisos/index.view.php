@extends('layout.app')

@section('title')
    Lista de Permisos
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Permisos
            <small>Administración de permisos del sistema</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{ $assetBase }}/permisos/create" class="btn btn-primary btn-sm">
                            <i class="fa fa-key"></i> Nuevo Permiso
                        </a>
                        <a href="{{ $assetBase }}/permisos/wastebasket" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i> Papelera
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form action="{{ $assetBase }}/permisos" method="GET" role="search" class="pull-right" style="margin: 0;">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control"
                                    value="{{ $_GET['search'] ?? '' }}" placeholder="Permiso a buscar...">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-body table-responsive">
                @if (!empty($permisos['data']) && count($permisos['data']) > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px; vertical-align: middle;">#</th>
                                <th style="vertical-align: middle;">Nombre</th>
                                <th style="vertical-align: middle;">Slug</th>
                                <th style="vertical-align: middle;">Descripción</th>
                                <th class="text-center" style="width: 110px; vertical-align: middle;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = ($permisos['from'] ?? 1) - 1;
                            @endphp
                            @foreach ($permisos['data'] as $permiso)
                                @php
                                    $contador++;
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle;">{{ $contador }}</td>
                                    <td style="vertical-align: middle;">{{ $permiso['nombre'] }}</td>
                                    <td style="vertical-align: middle;"><code>{{ $permiso['slug'] }}</code></td>
                                    <td style="vertical-align: middle;">{{ $permiso['descripcion'] }}</td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <div class="btn-group">
                                            <a href="{{ $assetBase }}/permisos/{{ $permiso['id_permiso'] }}/edit"
                                                class="btn btn-warning btn-xs" title="Editar Permiso">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-xs"
                                                onclick="confirmarEliminacion({{ $permiso['id_permiso'] }})"
                                                title="Eliminar Permiso">
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
                    <div class="alert alert-info text-center" style="margin-bottom: 0;">
                        Aún no se han registrado Permisos.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        function confirmarEliminacion(idPermiso) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El permiso será enviado a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/permisos/${idPermiso}/delete`, {
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
