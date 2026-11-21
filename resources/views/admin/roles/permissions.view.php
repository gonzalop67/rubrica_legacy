@extends('layout.app')

@section('title')
    Asignar Permisos
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Permisos por Perfil
            <small>{{ $role['pe_nombre'] }}</small>
        </h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="box-title" style="margin-top: 5px;">
                            <i class="fa fa-key"></i> Perfil: <strong>{{ $role['pe_nombre'] }}</strong>
                            (<code>{{ $role['pe_slug'] }}</code>)
                        </h3>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="button" id="btn-marcar-todos" class="btn btn-default btn-sm">
                            <i class="fa fa-check-square-o"></i> Marcar todos
                        </button>
                        <button type="button" id="btn-desmarcar-todos" class="btn btn-default btn-sm">
                            <i class="fa fa-square-o"></i> Desmarcar todos
                        </button>
                        <a href="{{ $assetBase }}/roles" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            <form id="form-permisos" method="post">
                <div class="box-body">
                    <!-- Pestañas de navegación para organizar la pantalla -->
                    <div class="nav-tabs-custom" style="box-shadow: none; margin-bottom: 0;">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab-menus" data-toggle="tab">
                                    <i class="fa fa-bars"></i> Acceso a Menús
                                </a>
                            </li>
                            <li>
                                <a href="#tab-granulares" data-toggle="tab">
                                    <i class="fa fa-shield"></i> Permisos Granulares (Acciones)
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" style="padding-top: 15px;">
                            <!-- PESTAÑA 1: ÁRBOL DE MENÚS -->
                            <div class="tab-pane active" id="tab-menus">
                                <div class="row">
                                    @foreach ($menusPadre as $menu)
                                        @php
                                            $padreChecked = in_array((int)$menu['id_menu'], $permisosAsignados, true);
                                            $icono = !empty($menu['mnu_icono']) ? $menu['mnu_icono'] : 'fa fa-folder';
                                        @endphp
                                        <div class="col-md-4 col-sm-6">
                                            <div class="panel panel-default" style="min-height: 220px;">
                                                <div class="panel-heading" style="background-color: #f4f6f9;">
                                                    <div class="checkbox" style="margin: 0;">
                                                        <label style="font-weight: bold;">
                                                            <input type="checkbox"
                                                                class="chk-global chk-menu chk-padre"
                                                                name="menus[]"
                                                                value="{{ $menu['id_menu'] }}"
                                                                data-padre-id="{{ $menu['id_menu'] }}"
                                                                {{ $padreChecked ? 'checked' : '' }}>
                                                            <i class="{{ $icono }}"></i> {{ $menu['mnu_texto'] }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="panel-body" style="padding: 10px 15px;">
                                                    @if (!empty($menu['submenus']) && count($menu['submenus']) > 0)
                                                        @foreach ($menu['submenus'] as $sub)
                                                            @php
                                                                $subChecked = in_array((int)$sub['id_menu'], $permisosAsignados, true);
                                                            @endphp
                                                            <div class="checkbox" style="margin-top: 5px; margin-bottom: 5px; padding-left: 10px;">
                                                                <label>
                                                                    <input type="checkbox"
                                                                        class="chk-global chk-menu chk-hijo"
                                                                        name="menus[]"
                                                                        value="{{ $sub['id_menu'] }}"
                                                                        data-parent="{{ $menu['id_menu'] }}"
                                                                        {{ $subChecked ? 'checked' : '' }}>
                                                                    <i class="fa fa-angle-right text-muted"></i> {{ $sub['mnu_texto'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-muted small" style="margin-bottom: 0;">
                                                            <i class="fa fa-info-circle"></i> Menú directo sin submenús.
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- PESTAÑA 2: PERMISOS GRANULARES (sw_perfil_permiso) -->
                            <div class="tab-pane" id="tab-granulares">
                                @if (!empty($permisosAgrupados) && count($permisosAgrupados) > 0)
                                    <div class="row">
                                        @foreach ($permisosAgrupados as $modulo => $listaPermisos)
                                            @php
                                                $idsModulo = array_map('intval', array_column($listaPermisos, 'id_permiso'));
                                                $todosMarcadosModulo = count(array_diff($idsModulo, $granularesAsignados)) === 0;
                                            @endphp
                                            <div class="col-md-4 col-sm-6">
                                                <div class="panel panel-default" style="min-height: 220px;">
                                                    <div class="panel-heading" style="background-color: #f4f6f9;">
                                                        <div class="checkbox" style="margin: 0;">
                                                            <label style="font-weight: bold;">
                                                                <input type="checkbox"
                                                                    class="chk-global chk-grupo-permiso"
                                                                    {{ $todosMarcadosModulo ? 'checked' : '' }}>
                                                                <i class="fa fa-folder-open text-primary"></i> Módulo: {{ $modulo }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body" style="padding: 10px 15px; max-height: 220px; overflow-y: auto;">
                                                        @foreach ($listaPermisos as $permiso)
                                                            @php
                                                                $permChecked = in_array((int)$permiso['id_permiso'], $granularesAsignados, true);
                                                            @endphp
                                                            <div class="checkbox" style="margin-top: 5px; margin-bottom: 8px; padding-left: 10px;">
                                                                <label title="{{ $permiso['descripcion'] }}">
                                                                    <input type="checkbox"
                                                                        class="chk-global chk-permiso-item"
                                                                        name="permisos[]"
                                                                        value="{{ $permiso['id_permiso'] }}"
                                                                        {{ $permChecked ? 'checked' : '' }}>
                                                                    <span>{{ $permiso['nombre'] }}</span>
                                                                    <br>
                                                                    <small class="text-muted"><code>{{ $permiso['slug'] }}</code></small>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info text-center" style="margin-bottom: 0;">
                                        <i class="fa fa-info-circle"></i> Aún no existen permisos registrados en <code>sw_permiso</code>.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" id="btn-guardar-permisos" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar Permisos
                    </button>
                    <a href="{{ $assetBase }}/roles" class="btn btn-default">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('form-permisos');
            const idPerfil = {{ (int)$role['id_perfil'] }};

            // 1. Menús: Al marcar/desmarcar un padre, sincronizar sus hijos
            document.querySelectorAll('.chk-padre').forEach(padre => {
                padre.addEventListener('change', function () {
                    const padreId = this.getAttribute('data-padre-id');
                    document.querySelectorAll(`.chk-hijo[data-parent="${padreId}"]`).forEach(hijo => {
                        hijo.checked = this.checked;
                    });
                });
            });

            // 2. Menús: Al marcar/desmarcar un hijo, sincronizar su padre
            document.querySelectorAll('.chk-hijo').forEach(hijo => {
                hijo.addEventListener('change', function () {
                    const padreId = this.getAttribute('data-parent');
                    const padre = document.querySelector(`.chk-padre[data-padre-id="${padreId}"]`);
                    if (padre) {
                        if (this.checked) {
                            padre.checked = true;
                        } else {
                            const hijosMarcados = document.querySelectorAll(`.chk-hijo[data-parent="${padreId}"]:checked`).length;
                            if (hijosMarcados === 0) {
                                padre.checked = false;
                            }
                        }
                    }
                });
            });

            // 3. Permisos Granulares: Al marcar/desmarcar cabecera del módulo, sincronizar sus ítems
            document.querySelectorAll('.chk-grupo-permiso').forEach(grupo => {
                grupo.addEventListener('change', function () {
                    const panel = this.closest('.panel');
                    panel.querySelectorAll('.chk-permiso-item').forEach(item => {
                        item.checked = this.checked;
                    });
                });
            });

            // 4. Permisos Granulares: Sincronizar cabecera del módulo cuando cambian sus ítems
            document.querySelectorAll('.chk-permiso-item').forEach(item => {
                item.addEventListener('change', function () {
                    const panel = this.closest('.panel');
                    const grupo = panel.querySelector('.chk-grupo-permiso');
                    const total = panel.querySelectorAll('.chk-permiso-item').length;
                    const marcados = panel.querySelectorAll('.chk-permiso-item:checked').length;
                    if (grupo) {
                        grupo.checked = (total > 0 && total === marcados);
                    }
                });
            });

            // 5. Botones globales Marcar / Desmarcar todos (aplica a menús y permisos)
            document.getElementById('btn-marcar-todos').addEventListener('click', () => {
                document.querySelectorAll('.chk-global').forEach(chk => chk.checked = true);
            });

            document.getElementById('btn-desmarcar-todos').addEventListener('click', () => {
                document.querySelectorAll('.chk-global').forEach(chk => chk.checked = false);
            });

            // 6. Envío asíncrono con Fetch + SweetAlert2
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const btn = document.getElementById('btn-guardar-permisos');
                btn.disabled = true;

                try {
                    const formData = new FormData(form);
                    const resp = await fetch(`${base_url}/roles/${idPerfil}/permisos`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    const json = await resp.json();

                    if (json.ok) {
                        Swal.fire({
                            title: '¡Permisos Guardados!',
                            text: json.mensaje,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = `${base_url}/roles`;
                        });
                    } else {
                        Swal.fire('Error', json.mensaje || 'No se pudieron guardar los permisos.', 'error');
                    }
                } catch (err) {
                    Swal.fire('Error de Conexión', 'No se pudo comunicar con el servidor.', 'error');
                } finally {
                    btn.disabled = false;
                }
            });
        });
    </script>
@endsection
