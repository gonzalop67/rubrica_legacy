@extends('layout.app')

@section('title')
    Nueva Tarea
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Tareas
            <small>Crear nueva tarea del sistema</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Nueva Tarea</strong></h3>
                <div class="box-tools pull-right">
                    <a href="{{ $assetBase }}/tareas" class="btn btn-default btn-sm btn-volver">
                        <i class="fa fa-arrow-left"></i> Volver al Listado de Tareas
                    </a>
                </div>
            </div>

            <form id="formulario" class="form-horizontal" action="{{ $assetBase }}/tareas" method="post">
                <div class="box-body">
                    <div class="form-group">
                        <label for="tarea" class="col-sm-2 control-label">Tarea:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="tarea" id="tarea"
                                cols="40" rows="5" placeholder="Inserte una nueva tarea..." required autofocus></textarea>
                            <span id="error-tarea" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="btn-submit" type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <a href="{{ $assetBase }}/tareas" class="btn btn-default">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="{{ $assetBase }}/js/pages/admin/tareas/crear.js"></script>
@endsection