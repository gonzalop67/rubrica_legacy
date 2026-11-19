@extends('layout.app')

@section('title')
    Nuevo Permiso
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Permisos
            <small>Crear nuevo permiso del sistema</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Nuevo Permiso</strong></h3>
                <div class="box-tools pull-right">
                    <a href="{{ $assetBase }}/permisos" class="btn btn-default btn-sm btn-volver">
                        <i class="fa fa-arrow-left"></i> Volver al Listado de Permisos
                    </a>
                </div>
            </div>

            <form id="formulario" class="form-horizontal" action="" method="post">
                <div class="box-body">
                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nombre" id="nombre" value=""
                                placeholder="Nombre del Permiso e.g. Ver Usuarios" required autofocus>
                            <span id="error-nombre" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="col-sm-2 control-label">Slug:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="slug" id="slug" value=""
                                placeholder="Slug del Permiso e.g. ver-usuarios" required>
                            <span id="error-slug" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="col-sm-2 control-label">Descripción:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="2"
                                placeholder="Descripción del Permiso e.g. Permite ver el listado de usuarios" required></textarea>
                            <span id="error-descripcion" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="btn-submit" type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <a href="{{ $assetBase }}/permisos" class="btn btn-default">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="{{ $assetBase }}/js/pages/admin/permisos/crear.js?v=2"></script>
@endsection
