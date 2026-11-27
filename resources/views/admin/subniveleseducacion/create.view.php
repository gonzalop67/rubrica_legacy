@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Subniveles de Educación
            <small>Crear nuevo subnivel de educación</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Nuevo Subnivel de Educación</strong></h3>
                <div class="box-tools pull-right">
                    <a href="{{ $assetBase }}/subniveles_educacion" class="btn btn-default btn-sm btn-volver">
                        <i class="fa fa-arrow-left"></i> Volver al Listado de Subniveles de Educación
                    </a>
                </div>
            </div>

            <form id="formulario" class="form-horizontal" action="" method="post">
                <div class="box-body">
                    <div class="form-group">
                        <label for="nivel_id" class="col-sm-2 control-label">Nivel de Educación:</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="nivel_id" id="nivel_id" required>
                                <option value="">-- Seleccione un Nivel de Educación --</option>
                                @foreach ($niveles as $nivel)
                                    <option value="{{ $nivel['id'] }}">{{ $nivel['nombre'] }}</option>
                                @endforeach
                            </select>
                            <span id="error-nivel_id" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nombre" id="nombre" value=""
                                placeholder="Nombre del Subnivel de Educación e.g. Básica Superior" required>
                            <span id="error-nombre" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="col-sm-2 control-label">Slug:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="slug" id="slug" value=""
                                placeholder="Slug del Subnivel de Educación e.g. basica-superior" required>
                            <span id="error-slug" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="es_bachillerato" id="es_bachillerato" value="1">
                                    <strong>¿Es Bachillerato?</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="btn-submit" type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <a href="{{ $assetBase }}/subniveles_educacion" class="btn btn-default">
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
    <script src="{{ $assetBase }}/js/pages/admin/subniveleseducacion/crear.js"></script>
@endsection