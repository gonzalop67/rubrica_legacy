@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Usuarios
            <small>Crear nuevo registro</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Nuevo Usuario</strong></h3>
                <div class="box-tools pull-right">
                    <a href="{{ $assetBase }}/usuarios" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Volver al Listado de Usuarios
                    </a>
                </div>
            </div>

            <form id="formulario" class="form-horizontal" action="" enctype="multipart/form-data" method="post">
                <div class="box-body">
                    <!-- Tipo de Documento y DNI -->
                    <div class="form-group">
                        <label for="tipo_documento" class="col-sm-2 control-label">DNI:</label>
                        <div class="col-sm-4">
                            <select name="tipo_documento" id="tipo_documento" class="form-control">
                                <option value="" disabled selected>Seleccionar...</option>
                                @foreach ($tipos_documentos as $tipo_documento)
                                    <option value="{{ $tipo_documento['id_tipo_documento'] }}">{{ $tipo_documento['td_nombre'] }}</option>
                                @endforeach
                            </select>
                            <span id="error-tipo_documento" class="help-block text-danger" style="display:none;"></span>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="dni" id="dni" value="" placeholder="DNI e.g. 1712345678" required autofocus>
                            <span id="error-dni" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Nacionalidad -->
                    <div class="form-group">
                        <label for="nacionalidad" class="col-sm-2 control-label">Nacionalidad:</label>
                        <div class="col-sm-10">
                            <select name="nacionalidad" id="nacionalidad" class="form-control">
                                <option value="" disabled selected>Seleccionar...</option>
                                @foreach ($nacionalidades as $nacionalidad)
                                    <option value="{{ $nacionalidad['id_def_nacionalidad'] }}">{{ $nacionalidad['dn_nombre'] }}</option>
                                @endforeach
                            </select>
                            <span id="error-nacionalidad" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Título -->
                    <div class="form-group">
                        <label for="titulo" class="col-sm-2 control-label">Título (Abrev.):</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="titulo" id="titulo" value="" placeholder="Abreviatura del Título e.g. Lic." required>
                            <span id="error-titulo" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Descripción del Título -->
                    <div class="form-group">
                        <label for="descripcion_titulo" class="col-sm-2 control-label">Descripción del Título:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="descripcion_titulo" id="descripcion_titulo" rows="2" placeholder="Descripción del Título e.g. Licenciado en Ciencias de la Educación"></textarea>
                            <span id="error-descripcion_titulo" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Apellidos -->
                    <div class="form-group">
                        <label for="primer_apellido" class="col-sm-2 control-label">Primer Apellido:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="primer_apellido" id="primer_apellido" value="" required>
                            <span id="error-primer_apellido" class="help-block text-danger" style="display:none;"></span>
                        </div>
                        <label for="segundo_apellido" class="col-sm-2 control-label">Segundo Apellido:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="segundo_apellido" id="segundo_apellido" value="" required>
                            <span id="error-segundo_apellido" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Nombres -->
                    <div class="form-group">
                        <label for="primer_nombre" class="col-sm-2 control-label">Primer Nombre:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="primer_nombre" id="primer_nombre" value="" required>
                            <span id="error-primer_nombre" class="help-block text-danger" style="display:none;"></span>
                        </div>
                        <label for="segundo_nombre" class="col-sm-2 control-label">Segundo Nombre:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="segundo_nombre" id="segundo_nombre" value="" required>
                            <span id="error-segundo_nombre" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Nombre Corto -->
                    <div class="form-group">
                        <label for="nombre_corto" class="col-sm-2 control-label">Nombre Corto:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nombre_corto" id="nombre_corto" value="" required>
                            <span id="error-nombre_corto" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Nombre Completo -->
                    <div class="form-group">
                        <label for="nombre_completo" class="col-sm-2 control-label">Nombre Completo:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nombre_completo" id="nombre_completo" value="" disabled>
                            <span id="error-nombre_completo" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="form-group">
                        <label for="username" class="col-sm-2 control-label">Nombre de Usuario:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="username" id="username" value="" required>
                            <span id="error-username" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">Email:</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" name="email" id="email" value="" required>
                            <span id="error-email" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">Contraseña:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="password" id="password" value="" required>
                            <span id="error-password" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Género -->
                    <div class="form-group">
                        <label for="genero" class="col-sm-2 control-label">Género:</label>
                        <div class="col-sm-10">
                            <select name="genero" id="genero" class="form-control">
                                <option value="Femenino">Femenino</option>
                                <option value="Masculino">Masculino</option>
                            </select>
                            <span id="error-genero" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Activo -->
                    <div class="form-group">
                        <label for="activo" class="col-sm-2 control-label">Activo:</label>
                        <div class="col-sm-10">
                            <select name="activo" id="activo" class="form-control">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                            <span id="error-activo" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div id="roles-container" class="form-group">
                        <label class="col-sm-2 control-label">Rol:</label>
                        <div class="col-sm-10">
                            @foreach ($roles as $role)
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="roles[]" value="{{ $role['id_perfil'] }}">
                                        {{ $role['pe_nombre'] }}
                                    </label>
                                </div>
                            @endforeach
                            <span id="error-roles" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>

                    <!-- Vista Previa del Avatar -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Vista Previa:</label>
                        <div id="img_div" class="col-sm-10">
                            <img id="us_avatar"
                                 src="{{ $assetBase }}/img/vecteezy_blue-profile-icon_36885313.png"
                                 name="us_avatar"
                                 class="img-thumbnail"
                                 style="width: 90px; height: 90px; object-fit: cover;"
                                 alt="Avatar del usuario">
                        </div>
                    </div>

                    <!-- Subir Imagen -->
                    <div class="form-group">
                        <label for="avatar" class="col-sm-2 control-label">Imagen:</label>
                        <div class="col-sm-10">
                            <input type="file" name="avatar" id="avatar" accept="image/*">
                            <p class="help-block">Formatos permitidos: JPG, PNG, GIF.</p>
                            <span id="error-avatar" class="help-block text-danger" style="display:none;"></span>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="btn-save" type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <a href="{{ $assetBase }}/usuarios" class="btn btn-default">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.getElementById('avatar').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    document.getElementById('us_avatar').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    <script src="{{ $assetBase }}/js/pages/admin/usuarios/crear.js"></script>
@endsection
