@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Ofertas Educativas
            <small>Editar oferta educativa</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Nueva Oferta Educativa</strong></h3>
                <div class="box-tools pull-right">
                    <a href="{{ $assetBase }}/ofertas_educativas" class="btn btn-default btn-sm btn-volver">
                        <i class="fa fa-arrow-left"></i> Volver al Listado de Ofertas Educativas
                    </a>
                </div>
            </div>

            <form id="formulario" class="form-horizontal" action="" method="post">
                <input type="hidden" name="id_oferta_educativa" id="id_oferta_educativa" value="{{ $oferta_educativa['id'] }}">

                <div class="box-body">
                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nombre" id="nombre" value="{{ $oferta_educativa['nombre'] }}"
                                placeholder="Nombre de la Oferta Educativa e.g. EPJA TEC" required>
                            <span id="error-nombre" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug" class="col-sm-2 control-label">Slug:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="slug" id="slug" value="{{ $oferta_educativa['slug'] }}"
                                placeholder="Slug de la Oferta Educativa e.g. epja-tec" required>
                            <span id="error-slug" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="btn-submit" type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Actualizar
                            </button>
                            <a href="{{ $assetBase }}/ofertas_educativas" class="btn btn-default">
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
    <script src="{{ $assetBase }}/js/pages/admin/ofertaseducativas/crear.js"></script>
@endsection