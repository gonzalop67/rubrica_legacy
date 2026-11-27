@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Subniveles de Educación
            <small>Panel Administrativo</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{ $assetBase }}/subniveles_educacion/create" class="btn btn-primary btn-sm">
                            <i class="fa fa-graduation-cap"></i> Nuevo Subnivel de Educación
                        </a>
                        <a href="{{ $assetBase }}/subniveles_educacion/wastebasket" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i> Papelera
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form action="{{ $assetBase }}/subniveles_educacion" method="GET" role="search"
                            class="pull-right" style="margin: 0;">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control"
                                    value="{{ $_GET['search'] ?? '' }}" placeholder="Subnivel o nivel a buscar...">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-body table-responsive">
                @if (!empty($subniveleseducacion['data']) && count($subniveleseducacion['data']) > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px; vertical-align: middle;">#</th>
                                <th style="vertical-align: middle;">Nivel de Educación</th>
                                <th style="vertical-align: middle;">Nombre</th>
                                <th style="vertical-align: middle;">Slug</th>
                                <th class="text-center" style="width: 120px; vertical-align: middle;">¿Bachillerato?</th>
                                <th class="text-center" style="width: 110px; vertical-align: middle;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = ($subniveleseducacion['from'] ?? 1) - 1;
                            @endphp
                            @foreach ($subniveleseducacion['data'] as $subnivel)
                                @php
                                    $contador++;
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle;">{{ $contador }}</td>
                                    <td style="vertical-align: middle;">{{ $subnivel['nivel_nombre'] }}</td>
                                    <td style="vertical-align: middle;">{{ $subnivel['nombre'] }}</td>
                                    <td style="vertical-align: middle;"><code>{{ $subnivel['slug'] }}</code></td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        @if ($subnivel['es_bachillerato'] == 1)
                                            <span class="label label-success">Sí</span>
                                        @else
                                            <span class="label label-default">No</span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <div class="btn-group">
                                            <a href="{{ $assetBase }}/subniveles_educacion/{{ $subnivel['id'] }}/edit"
                                                class="btn btn-warning btn-sm" title="Editar subnivel de educación">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmarEliminacion({{ $subnivel['id'] }})"
                                                title="Eliminar Subnivel de Educación">
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
                        Aún no se han registrado Subniveles de Educación.
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function confirmarEliminacion(idSubnivel) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El Subnivel de educación será enviado a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/subniveles_educacion/${idSubnivel}/delete`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success || data.ok) {
                                Swal.fire('¡Eliminado!', data.message || data.mensaje, 'success')
                                    .then(() => location.reload());
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
