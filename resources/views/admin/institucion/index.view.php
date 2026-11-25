@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="row" style="padding: 15px 10px;">
        <div class="col-md-12">
            <!-- Caja con borde superior info (AdminLTE 2) -->
            <div class="box box-info">

                <div class="box-header with-border" style="padding: 15px 20px;">
                    <h3 class="box-title">Datos de la Institución Educativa</h3>
                </div>

                <div class="box-body" style="padding: 25px 30px;">
                    <form id="form_institucion" action="" method="POST" class="form-horizontal"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id"
                            value="{{ $institucion['id_institucion'] ?? '' }}">

                        <div class="form-group">
                            <label for="nombre" class="col-sm-2 control-label">Nombre:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nombre" id="nombre"
                                    onfocus="sel_texto(this)" value="{{ $institucion['in_nombre'] ?? '' }}" required>
                                <span class="error help-block text-red" id="error-nombre"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="direccion" class="col-sm-2 control-label">Dirección:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="direccion" id="direccion"
                                    value="{{ $institucion['in_direccion'] ?? '' }}" onfocus="sel_texto(this)" required>
                                <span class="error help-block text-red" id="error-direccion"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Email:</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="email"
                                    value="{{ $institucion['in_email'] ?? '' }}" onfocus="sel_texto(this)" required>
                                <span class="error help-block text-red" id="error-email"></span>
                            </div>
                        </div>

                        <div id="administrador" class="form-group">
                            <label for="admin_id" class="col-sm-2 control-label requerido">Administrador:</label>
                            <div class="col-sm-10">
                                <input type="hidden" name="id_usuario_admin" value="">

                                <select class="form-control" id="admin_id" name="admin_id">
                                    <option value="">Seleccione Administrador de la Institución Educativa...</option>
                                    @foreach ($admin_list as $row)
                                        <option value="{{ $row['id_usuario'] }}" data-foto="{{ $row['us_foto'] ?? '' }}"
                                            {{ $institucion['admin_id'] == $row['id_usuario'] ? 'selected' : '' }}>
                                            {{ $row['nombre_corto'] }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Bloque del Avatar -->
                                <div id="img_admin" class="hidden" style="margin-top: 15px;">
                                    <img id="avatar_admin" name="avatar_admin" class="img-thumbnail"
                                        style="width: 75px; height: auto;" alt="Avatar del administrador">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="telefono" class="col-sm-2 control-label">Teléfono:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="telefono" id="telefono"
                                    value="{{ $institucion['in_telefono'] ?? '' }}" onfocus="sel_texto(this)" required>
                                <span class="error help-block text-red" id="error-telefono"></span>
                            </div>

                            <label for="regimen" class="col-sm-2 control-label">Régimen:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" style="text-transform: uppercase;" name="regimen" id="regimen"
                                    value="{{ $institucion['in_regimen'] ?? '' }}" onfocus="sel_texto(this)">
                                <span class="error help-block text-red" id="error-regimen"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nom_rector" class="col-sm-2 control-label">Rector (a):</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="nom_rector" id="nom_rector"
                                    value="{{ $institucion['in_nom_rector'] ?? '' }}" onfocus="sel_texto(this)" required>
                                <span class="error help-block text-red" id="error-nom_rector"></span>
                            </div>

                            <label for="genero_rector" class="col-sm-2 control-label">Género:</label>
                            <div class="col-sm-4">
                                <select name="genero_rector" id="genero_rector" class="form-control">
                                    <option value="F"
                                        {{ $institucion['in_genero_rector'] ? ($institucion['in_genero_rector'] == 'F' ? 'selected' : '') : '' }}>
                                        Femenino</option>
                                    <option value="M"
                                        {{ $institucion['in_genero_rector'] ? ($institucion['in_genero_rector'] == 'M' ? 'selected' : '') : '' }}>
                                        Masculino</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nom_vicerrector" class="col-sm-2 control-label">Vicerrector (a):</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="nom_vicerrector" id="nom_vicerrector"
                                    value="{{ $institucion['in_nom_vicerrector'] ?? '' }}" onfocus="sel_texto(this)"
                                    required>
                                <span class="error help-block text-red" id="error-nom_vicerrector"></span>
                            </div>

                            <label for="genero_vicerrector" class="col-sm-2 control-label">Género:</label>
                            <div class="col-sm-4">
                                <select name="genero_vicerrector" id="genero_vicerrector" class="form-control fuente10">
                                    <option value="F"
                                        {{ $institucion['in_genero_vicerrector'] ? ($institucion['in_genero_vicerrector'] == 'F' ? 'selected' : '') : '' }}>
                                        Femenino</option>
                                    <option value="M"
                                        {{ $institucion['in_genero_vicerrector'] ? ($institucion['in_genero_vicerrector'] == 'M' ? 'selected' : '') : '' }}>
                                        Masculino</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nom_secretario" class="col-sm-2 control-label">Secretario (a):</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="nom_secretario" id="nom_secretario"
                                    value="{{ $institucion['in_nom_secretario'] ?? '' }}" onfocus="sel_texto(this)"
                                    required>
                                <span class="error help-block text-red" id="error-nom_secretario"></span>
                            </div>

                            <label for="genero_secretario" class="col-sm-2 control-label">Género:</label>
                            <div class="col-sm-4">
                                <select name="genero_secretario" id="genero_secretario" class="form-control fuente10">
                                    <option value="F"
                                        {{ $institucion['in_genero_secretario'] ? ($institucion['in_genero_secretario'] == 'F' ? 'selected' : '') : '' }}>
                                        Femenino</option>
                                    <option value="M"
                                        {{ $institucion['in_genero_secretario'] ? ($institucion['in_genero_secretario'] == 'M' ? 'selected' : '') : '' }}>
                                        Masculino</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="url" class="col-sm-2 control-label">URL:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="url" id="url"
                                    value="{{ $institucion['in_url'] ?? '' }}" onfocus="sel_texto(this)" required>
                                <span class="error help-block text-red" id="error-url"></span>
                            </div>

                            <label for="amie" class="col-sm-2 control-label">AMIE:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" style="text-transform: uppercase;" name="amie" id="amie"
                                    value="{{ $institucion['in_amie'] ?? '' }}" onfocus="sel_texto(this)">
                                <span class="error help-block text-red" id="error-amie"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ciudad" class="col-sm-2 control-label">Ciudad:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="ciudad" id="ciudad"
                                    value="{{ $institucion['in_ciudad'] ?? '' }}" onfocus="sel_texto(this)" required>
                                <span class="error help-block text-red" id="error-ciudad"></span>
                            </div>

                            <label for="copiar_y_pegar" class="col-sm-2 control-label">Copy & Paste:</label>
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="copiar_y_pegar" name="copiar_y_pegar"
                                            {{ $institucion['in_copiar_y_pegar'] == 1 ? 'checked' : '' }}>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="img_upload">
                            <!-- Fila 1: Previsualización de la Imagen -->
                            <div class="form-group">
                                <label for="img_logo" class="col-sm-2 control-label">Imagen:</label>
                                <div id="img_div" class="col-sm-10 <?php echo empty($institucion['in_logo']) ? 'hidden' : ''; ?>">
                                    <img id="img_logo" name="img_logo" src="<?php echo !empty($institucion['in_logo']) ? RUTA_URL . '/public/uploads/' . $institucion['in_logo'] : ''; ?>"
                                        class="img-thumbnail" style="width: 75px; height: auto;"
                                        alt="Avatar de la institucion">
                                </div>
                            </div>

                            <!-- Fila 2: Selector de Archivo (Bootstrap 3 / AdminLTE 2) -->
                            <div class="form-group">
                                <label for="in_logo" class="col-sm-2 control-label">Archivo:</label>
                                <input type="hidden" name="in_logo_file" id="in_logo_file" value="<?php echo $institucion['in_logo'] ?? ''; ?>">

                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <label class="input-group-btn">
                                            <span class="btn btn-default btn-flat">
                                                Buscar&hellip; <input type="file" name="in_logo" id="in_logo" style="display: none;"
                                                    accept="image/png, image/jpeg, image/jpg, image/svg+xml">
                                            </span>
                                        </label>
                                        <input type="text" id="in_logo_text" class="form-control" readonly placeholder="Seleccionar imagen...">
                                    </div>
                                    <span class="error help-block text-red" id="error-in_logo"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12" style="margin-top: 15px;">
                                <button id="btn-add-item" type="submit"
                                    class="btn btn-primary btn-block btn-flat">
                                    <i class="fa fa-save" style="margin-right: 5px;"></i> Actualizar datos de la Institución
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Loader con Font Awesome (AdminLTE 2) -->
                    <div id="img_loader" class="text-center hidden" style="margin-top: 15px; margin-bottom: 15px;">
                        <i class="fa fa-spinner fa-spin fa-2x text-light-blue"></i>
                        <span class="sr-only">Procesando...</span>
                    </div>

                    <!-- Contenedor para Mensajes o Alertas -->
                    <div id="mensaje" class="text-center hidden" style="margin-top: 15px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ RUTA_URL }}/public/assets/js/funciones.js"></script>
    <script>
        $(document).ready(function() {
            // 1. Mostrar dinámicamente el nombre de la imagen seleccionada en el input de Bootstrap 3
            $('#in_logo').on('change', function(e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : "";
                $('#in_logo_text').val(fileName);

                // Previsualización local inmediata antes de enviar al servidor
                if (e.target.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $('#img_logo').attr('src', event.target.result);
                        $('#img_div').removeClass('hidden');
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });

            // 2. Procesamiento AJAX del Formulario
            $('#form_institucion').on('submit', function(e) {
                e.preventDefault();

                // Limpieza de estados e interfaces previas (Bootstrap 3)
                $('.error').text('');
                $('.form-group').removeClass('has-error');
                $('#mensaje').removeClass('alert alert-success alert-danger').text('').addClass('hidden');
                $('#btn-add-item').prop('disabled', true);
                $('#img_loader').removeClass('hidden');

                var formData = new FormData(this);
                var checkboxValor = $('#copiar_y_pegar').is(':checked') ? 1 : 0;
                formData.set('copiar_y_pegar', checkboxValor);

                $.ajax({
                    type: "POST",
                    url: "{{ RUTA_URL }}/institucion/update",
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#img_loader').addClass('hidden');
                        $('#btn-add-item').prop('disabled', false);

                        if (response.success) {
                            $('#mensaje')
                                .removeClass('hidden')
                                .addClass('alert alert-success')
                                .text(response.message);

                            $('#in_logo').val('');
                            $('#in_logo_text').val('');
                        } else {
                            $('#mensaje')
                                .removeClass('hidden')
                                .addClass('alert alert-danger')
                                .text(response.message);

                            if (response.errors) {
                                $.each(response.errors, function(campo, mensajeError) {
                                    $('#error-' + campo).text(mensajeError);
                                    $('[name="' + campo + '"]').closest('.form-group').addClass('has-error');
                                });
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#img_loader').addClass('hidden');
                        $('#btn-add-item').prop('disabled', false);

                        $('#mensaje')
                            .removeClass('hidden')
                            .addClass('alert alert-danger')
                            .text('Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo.');

                        console.error("Detalles del error AJAX:", error, xhr.responseText);
                    }
                });
            });

            // 3. Cambio de administrador y carga de avatar
            $('#admin_id').on('change', function() {
                var optionSeleccionada = $(this).find('option:selected');
                var idUsuario = $(this).val();
                var nombreAvatar = optionSeleccionada.data('foto');

                if (idUsuario !== "" && nombreAvatar !== undefined && nombreAvatar !== "") {
                    var rutaCompletaAvatar = base_url + '/public/uploads/' + nombreAvatar;
                    $('#avatar_admin').attr('src', rutaCompletaAvatar);
                    $('#img_admin').removeClass('hidden');
                } else {
                    $('#img_admin').addClass('hidden');
                    $('#avatar_admin').attr('src', '');
                }
            });

            if ($('#admin_id').val() !== "") {
                $('#admin_id').trigger('change');
            }
        });
    </script>
@endsection
