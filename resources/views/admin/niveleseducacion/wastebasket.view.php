@extends('layout.app')

@section('title')
    Papelera de Niveles de Educación
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Papelera de Niveles de Educación
            <small>Registros eliminados temporalmente</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-danger">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{ $assetBase }}/niveles_educacion" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Volver a Niveles de Educación
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form action="{{ $assetBase }}/niveles_educacion/wastebasket" method="GET" role="search" class="pull-right" style="margin: 0;">
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
                @if (!empty($niveleseducacion['data']) && count($niveleseducacion['data']) > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px; vertical-align: middle;">#</th>
                                <th style="vertical-align: middle;">Nombre del Nivel de Educación</th>
                                <th style="vertical-align: middle;">Slug</th>
                                <th style="vertical-align: middle;">Eliminado el</th>
                                <th class="text-center" style="width: 110px; vertical-align: middle;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = ($niveleseducacion['from'] ?? 1) - 1;
                            @endphp
                            @foreach ($niveleseducacion['data'] as $nivel)
                                @php
                                    $contador++;
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle;">{{ $contador }}</td>
                                    <td style="vertical-align: middle;">{{ $nivel['nombre'] }}</td>
                                    <td style="vertical-align: middle;"><code>{{ $nivel['slug'] }}</code></td>
                                    <td style="vertical-align: middle;">{{ $nivel['deleted_at'] ?? '' }}</td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-xs"
                                                onclick="confirmarRestauracion({{ $nivel['id'] }})"
                                                title="Restaurar nivel Educativa">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs"
                                                onclick="confirmarEliminacionDefinitiva({{ $nivel['id'] }})"
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
                    <div class="alert alert-info text-center" style="margin-bottom: 0;">
                        La papelera está vacía.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        function confirmarRestauracion(idnivel) {
            Swal.fire({
                title: '¿Restaurar nivel de educación?',
                text: "El nivel de educación volverá a estar activo en la lista principal.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00a65a',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/niveles_educacion/${idnivel}/restore`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success || data.ok) {
                            Swal.fire('¡Restaurado!', data.message || data.mensaje || 'El nivel de educación ha sido activado.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || data.mensaje || 'No se pudo restaurar el nivel de educación.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
                    });
                }
            });
        }

        function confirmarEliminacionDefinitiva(idNivel) {
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
                    fetch(`${base_url}/niveles_educacion/${idNivel}/destroy`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success || data.ok) {
                            Swal.fire('¡Eliminado!', data.message || data.mensaje || 'El Nivel de Educación ha sido borrado para siempre.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire({
                                title: 'No se puede eliminar',
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
