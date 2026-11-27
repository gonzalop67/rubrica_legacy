@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Ofertas Educativas
            <small>Panel Administrativo</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{ $assetBase }}/ofertas_educativas/create" class="btn btn-primary btn-sm">
                            <i class="fa fa-graduation-cap"></i> Nueva Oferta Educativa
                        </a>
                        <a href="{{ $assetBase }}/ofertas_educativas/wastebasket" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i> Papelera
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form action="{{ $assetBase }}/ofertas_educativas" method="GET" role="search"
                            class="pull-right" style="margin: 0;">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control"
                                    value="{{ $_GET['search'] ?? '' }}" placeholder="Oferta Educativa a buscar...">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-body table-responsive">
                @if (!empty($ofertaseducativas['data']) && count($ofertaseducativas['data']) > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px; vertical-align: middle;">#</th>
                                <th style="vertical-align: middle;">Nombre</th>
                                <th style="vertical-align: middle;">Slug</th>
                                <th class="text-center" style="width: 110px; vertical-align: middle;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = ($ofertaseducativas['from'] ?? 1) - 1;
                            @endphp
                            @foreach ($ofertaseducativas['data'] as $oferta)
                                @php
                                    $contador++;
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle;">{{ $contador }}</td>
                                    <td style="vertical-align: middle;">{{ $oferta['nombre'] }}</td>
                                    <td style="vertical-align: middle;"><code>{{ $oferta['slug'] }}</code></td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <div class="btn-group">
                                            <a href="{{ $assetBase }}/ofertas_educativas/{{ $oferta['id'] }}/edit"
                                                class="btn btn-warning btn-sm" title="Editar oferta educativa">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmarEliminacion({{ $oferta['id'] }})"
                                                title="Eliminar Oferta Educativa">
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
                        Aún no se han registrado Ofertas Eucativas.
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function confirmarEliminacion(idOferta) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "La Oferta Educativa será enviada a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/ofertas_educativas/${idOferta}/delete`, {
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
