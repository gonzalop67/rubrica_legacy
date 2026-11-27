@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Niveles de Educación
            <small>Crear nuevo nivel de educación</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Nuevo Nivel de Educación</strong></h3>
                <div class="box-tools pull-right">
                    <a href="{{ $assetBase }}/niveles_educacion" class="btn btn-default btn-sm btn-volver">
                        <i class="fa fa-arrow-left"></i> Volver al Listado de Niveles de Educación
                    </a>
                </div>
            </div>

            <form id="formulario" class="form-horizontal" action="" method="post">
                <div class="box-body">
                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nombre" id="nombre" value=""
                                placeholder="Nombre del Nivel de Educación e.g. Bachillerato" required>
                            <span id="error-nombre" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="col-sm-2 control-label">Slug:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="slug" id="slug" value=""
                                placeholder="Slug del Nivel de Educación e.g. bachillerato" required>
                            <span id="error-slug" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="btn-submit" type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <a href="{{ $assetBase }}/niveles_educacion" class="btn btn-default">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ $assetBase }}/js/pages/admin/niveleseducacion/crear.js"></script>
@endsection