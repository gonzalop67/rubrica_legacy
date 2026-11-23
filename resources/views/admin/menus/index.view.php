@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ $assetBase }}/public/css/jquery.nestable.css">
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        #nestable {
            max-width: 100%;
            width: 100%;
            float: none;
        }

        /* AISLAMIENTO EXCLUSIVO PARA EL FORMULARIO IZQUIERDO */
        #form_insert .box-visor {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            max-width: 38px !important;
            padding: 0 !important;
            overflow: hidden !important;
        }

        #form_insert #mi-previsualizador-icono,
        #form_insert #mi-previsualizador-icono::before {
            font-size: 16px !important;
            width: 16px !important;
            height: 16px !important;
            line-height: 16px !important;
            display: inline-block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Menús
            <small>Administración y organización jerárquica</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">

            <!-- COLUMNA IZQUIERDA (col-md-4): FORMULARIO -->
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" id="titulo-formulario">
                            <i class="fa fa-plus-circle"></i> Crear Nuevo Menú
                        </h3>
                        <div class="box-tools pull-right">
                            <button type="button" id="btn-limpiar" class="btn btn-default btn-xs" title="Nuevo / Limpiar">
                                <i class="fa fa-eraser"></i> Nuevo
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <form id="form_insert" action="{{ $assetBase }}/menus" method="POST" novalidate>
                            <input type="hidden" name="id_menu" id="hidden_id_menu" value="">
                            <input type="hidden" name="id_perfil" id="hidden_id_perfil" value="">

                            <div class="form-group" id="group-nombre">
                                <label for="nombre">Texto / Nombre:</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="32"
                                    placeholder="Ej: Registrar Venta">
                                <span id="error-nombre" class="help-block text-danger" style="display:none;"></span>
                            </div>

                            <div class="form-group" id="group-url">
                                <label for="url">Enlace / URL:</label>
                                <input type="text" class="form-control" name="url" id="url" maxlength="64"
                                    placeholder="Ej: /ventas/nuevo o #">
                                <span id="error-url" class="help-block text-danger" style="display:none;"></span>
                            </div>

                            <div class="form-group" id="group-icono">
                                <label for="icono">Ícono:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="icono" id="icono" maxlength="25"
                                        placeholder="Ej: fa fa-dashboard">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default disabled" style="opacity: 1;">
                                            <i id="mi-previsualizador-icono" class="fa fa-question text-muted"></i>
                                        </span>
                                    </span>
                                </div>
                                <span id="error-icono" class="help-block text-danger" style="display:none;"></span>
                            </div>

                            <div class="form-group" id="group-padre_id">
                                <label for="padre_id">Menú Padre (Ubicación):</label>
                                <select class="form-control" name="padre_id" id="padre_id">
                                    <option value="0">-- Ninguno (Es un menú principal) --</option>
                                    @foreach ($menus_principales as $menu)
                                        <option value="{{ $menu['id_menu'] }}">
                                            ({{ $menu['pe_nombre'] }})
                                            {{ $menu['mnu_texto'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="error-padre_id" class="help-block text-danger" style="display:none;"></span>
                            </div>

                            <button type="submit" id="button-save" class="btn btn-primary btn-block">
                                <i class="fa fa-save"></i> Registrar Menú
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA (col-md-8): SELECTOR DE PERFIL Y ÁRBOL NESTABLE -->
            <div class="col-md-8">
                <div class="box box-default">
                    <div class="box-body">
                        <div class="pull-right" style="margin-bottom: 15px;">
                            <a href="{{ $assetBase }}/menus/wastebasket" class="btn btn-default btn-sm">
                                <i class="fa fa-trash text-danger"></i> Ver Papelera de Menús
                            </a>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="select-perfil">Selecciona un Perfil / Rol:</label>
                            <select id="select-perfil" class="form-control">
                                <option value="">-- Seleccione un Perfil --</option>
                                @foreach ($roles as $perfil)
                                    <option value="{{ $perfil['id_perfil'] }}">
                                        {{ $perfil['pe_nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="cf nestable-lists">
                            <div id="nestable" class="dd">
                                <div id="nestable-placeholder">
                                    <div class="text-muted text-center" style="padding: 20px 0;">
                                        Selecciona un perfil para cargar y organizar sus menús.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ $assetBase }}/js/jquery.nestable.js"></script>
    <script src="{{ $assetBase }}/js/pages/admin/menus/index.js"></script>
@endsection
