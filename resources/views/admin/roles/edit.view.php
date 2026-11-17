@extends('layout.app')

@section('title')
    Editar Perfil
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Perfiles
            <small>Actualizar datos del rol</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Editar Perfil: {{ $role['pe_nombre'] }}</strong></h3>
                <div class="box-tools pull-right">
                    <a href="{{ $assetBase }}/roles" class="btn btn-default btn-sm btn-volver">
                        <i class="fa fa-arrow-left"></i> Volver al Listado de Perfiles
                    </a>
                </div>
            </div>

            <form id="formulario" class="form-horizontal" action="" method="post">
                <input type="hidden" id="id_perfil" value="{{ $role['id_perfil'] }}">

                <div class="box-body">
                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nombre" id="nombre"
                                value="{{ $role['pe_nombre'] }}" placeholder="Nombre del Perfil e.g. Estudiante"
                                required autofocus>
                            <span id="error-nombre" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="col-sm-2 control-label">Slug:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="slug" id="slug"
                                value="{{ $role['pe_slug'] }}" placeholder="Slug del Perfil e.g. estudiante"
                                required>
                            <span id="error-slug" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="btn-submit" type="submit" class="btn btn-success">
                                <i class="fa fa-pencil"></i> Actualizar
                            </button>
                            <a href="{{ $assetBase }}/roles" class="btn btn-default">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="{{ $assetBase }}/js/pages/admin/perfiles/crear.js"></script>
@endsection
