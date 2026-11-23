@extends('layout.app')

@section('title')
    Lista de Tareas
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Tareas
            <small>Lista de tareas por hacer</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{ $assetBase }}/tareas/create" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Nueva Tarea
                        </a>
                        <a href="{{ $assetBase }}/tareas/wastebasket" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i> Tareas eliminadas
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form action="{{ $assetBase }}/tareas" method="GET" role="search" class="pull-right"
                            style="margin: 0;">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control"
                                    value="{{ $_GET['search'] ?? '' }}" placeholder="Tarea a buscar...">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-body table-responsive">
                @if (!empty($tareas['data']) && count($tareas['data']) > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px; vertical-align: middle;">#</th>
                                <th style="vertical-align: middle;">Descripción</th>
                                <th style="vertical-align: middle;">Estatus</th>
                                <th class="text-center" style="width: 100px; vertical-align: middle;">Hecho</th>
                                <th class="text-center" style="width: 110px; vertical-align: middle;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = ($tareas['from'] ?? 1) - 1;
                            @endphp
                            @foreach ($tareas['data'] as $tarea)
                                @php
                                    $contador++;
                                    $id = $tarea['id'];
                                    $checked = $tarea['hecho'] == 1 ? 'checked' : '';
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle;">{{ $contador }}</td>
                                    <td style="vertical-align: middle;">{{ $tarea['tarea'] }}</td>
                                    <td>{{ $tarea['hecho'] == 0 ? 'PENDIENTE' : 'REALIZADO' }}</td>
                                    <td style="text-align: center;">
                                        <input type="checkbox" onclick="checkTask(this, {{ $id }})"
                                            {{ $checked }}>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <div class="btn-group">
                                            <a href="{{ $assetBase }}/tareas/{{ $tarea['id'] }}/edit"
                                                class="btn btn-warning btn-sm" title="Editar Tarea">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                data-id="{{ $tarea['id'] }}" data-nombre="{{ $tarea['tarea'] }}"
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
                    <div class="alert alert-info text-center" style="margin-bottom: 0;">
                        Aún no se han registrado Tareas.
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ $assetBase }}/js/pages/admin/tareas/index.js"></script>
@endsection