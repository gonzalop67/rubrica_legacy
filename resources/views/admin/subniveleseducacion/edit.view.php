@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Subniveles de Educación
            <small>Editar Subnivel de educación</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Editar Subnivel de Educación</strong></h3>
                <div class="box-tools pull-right">
                    <a href="{{ $assetBase }}/subniveles_educacion" class="btn btn-default btn-sm btn-volver">
                        <i class="fa fa-arrow-left"></i> Volver al Listado de Subniveles de Educación
                    </a>
                </div>
            </div>

            <form id="formulario" class="form-horizontal" action="" method="post">
                <input type="hidden" name="id_subnivel_educacion" id="id_subnivel_educacion" value="{{ $subnivel_educacion['id'] }}">

                <div class="box-body">
                    <div class="form-group">
                        <label for="nivel_id" class="col-sm-2 control-label">Nivel de Educación:</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="nivel_id" id="nivel_id" required>
                                <option value="">-- Seleccione un Nivel de Educación --</option>
                                @foreach ($niveles as $nivel)
                                    <option value="{{ $nivel['id'] }}" {{ $nivel['id'] == $subnivel_educacion['nivel_id'] ? 'selected' : '' }}>
                                        {{ $nivel['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                            <span id="error-nivel_id" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nombre" id="nombre" value="{{ $subnivel_educacion['nombre'] }}"
                                placeholder="Nombre del subnivel de educación e.g. Básica Superior" required>
                            <span id="error-nombre" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="col-sm-2 control-label">Slug:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="slug" id="slug" value="{{ $subnivel_educacion['slug'] }}"
                                placeholder="Slug del subnivel de educación e.g. basica-superior" required>
                            <span id="error-slug" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="orden" class="col-sm-2 control-label">Orden:</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="orden" id="orden" value="{{ $subnivel_educacion['orden'] }}" min="0" required>
                            <span id="error-orden" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="es_bachillerato" id="es_bachillerato" value="1"
                                        {{ $subnivel_educacion['es_bachillerato'] == 1 ? 'checked' : '' }}>
                                    <strong>¿Es Bachillerato?</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="btn-submit" type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Actualizar
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
