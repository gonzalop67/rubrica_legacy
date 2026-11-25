document.addEventListener('DOMContentLoaded', () => {
    const urlBaseSegura = (typeof base_url !== 'undefined') ? base_url : '';

    // Elementos exactos de tu nueva vista Blade
    const selectPerfil = document.getElementById('select-perfil');
    const hiddenIdPerfil = document.getElementById('hidden_id_perfil');
    const hiddenIdMenu = document.getElementById('hidden_id_menu');
    const formInsert = document.getElementById('form_insert');
    const tituloFormulario = document.getElementById('titulo-formulario');
    const btnLimpiar = document.getElementById('btn-limpiar');
    const inputIcono = document.getElementById('icono');
    const previewIcono = document.getElementById('mi-previsualizador-icono');
    const nestableContainer = document.getElementById('nestable');

    // Vista previa en tiempo real del ícono FontAwesome
    if (inputIcono && previewIcono) {
        inputIcono.addEventListener('input', () => {
            const val = inputIcono.value.trim();
            previewIcono.className = val !== '' ? val : 'fa fa-question text-muted';
        });
    }

    const limpiarErrores = () => {
        document.querySelectorAll('.help-block.text-danger').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        document.querySelectorAll('.form-group').forEach(el => el.classList.remove('has-error'));
    };

    const resetearFormulario = () => {
        if (formInsert) formInsert.reset();
        if (hiddenIdMenu) hiddenIdMenu.value = '';
        if (hiddenIdPerfil && selectPerfil) hiddenIdPerfil.value = selectPerfil.value;
        if (previewIcono) previewIcono.className = 'fa fa-question text-muted';
        if (tituloFormulario) {
            tituloFormulario.innerHTML = '<i class="fa fa-plus-circle"></i> Crear Nuevo Menú';
        }
        const btnSave = document.getElementById('button-save');
        if (btnSave) {
            btnSave.innerHTML = '<i class="fa fa-save"></i> Registrar Menú';
        }
        limpiarErrores();
    };

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', resetearFormulario);
    }

    // 1. AL CAMBIAR EL PERFIL -> CARGAR ÁRBOL DE MENÚS
    if (selectPerfil) {
        selectPerfil.addEventListener('change', async () => {
            const perfilId = selectPerfil.value;
            if (hiddenIdPerfil) hiddenIdPerfil.value = perfilId;
            resetearFormulario();

            if (!perfilId) {
                nestableContainer.innerHTML = `
                    <div id="nestable-placeholder">
                        <div class="text-muted text-center" style="padding: 20px 0;">
                            Selecciona un perfil para cargar y organizar sus menús.
                        </div>
                    </div>`;
                return;
            }

            await cargarArbolMenus(perfilId);
        });
    }

    async function cargarArbolMenus(perfilId) {
        nestableContainer.innerHTML = '<div class="text-center text-muted" style="padding:20px 0;"><i class="fa fa-spinner fa-spin"></i> Cargando menús...</div>';

        try {
            const formData = new FormData();
            formData.append('perfil_id', perfilId);

            const resp = await fetch(`${urlBaseSegura}/menus/get_menu_ajax`, {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });

            if (!resp.ok) {
                throw new Error(`Error HTTP: ${resp.status}`);
            }

            const html = await resp.text();

            // 1. Limpiar por completo la instancia anterior de Nestable en el contenedor padre
            const $nestable = $('#nestable');
            $nestable.off();
            $nestable.removeData('nestable');

            // 2. Inyectar el nuevo HTML del árbol
            nestableContainer.innerHTML = html;

            // 3. Inicializar Nestable desde cero sobre el nuevo DOM
            if (typeof $ !== 'undefined' && $.fn.nestable && nestableContainer.querySelector('.dd-list')) {
                $nestable.nestable({
                    maxDepth: 3
                }).on('change', async function () {
                    const ordenSerializado = JSON.stringify($nestable.nestable('serialize'));
                    await guardarOrdenNestable(ordenSerializado);
                });
            }
        } catch (error) {
            console.error('Error al cargar menús:', error);
            nestableContainer.innerHTML = '<div class="alert alert-danger">Error al cargar los menús. Revisa la consola (F12).</div>';
        }
    }

    async function guardarOrdenNestable(jsonOrden) {
        try {
            const formData = new FormData();
            formData.append('menu', jsonOrden);

            const resp = await fetch(`${urlBaseSegura}/menus/guardar-orden`, {
                method: 'POST',
                body: formData
            });

            const json = await resp.json();
            if (json.ok) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Orden actualizado',
                    showConfirmButton: false,
                    timer: 1200
                });
            }
        } catch (error) {
            console.error('Error al guardar orden:', error);
        }
    }

    // 2. REGISTRAR O ACTUALIZAR MENÚ
    if (formInsert) {
        formInsert.addEventListener('submit', async (e) => {
            e.preventDefault();
            limpiarErrores();

            if (!selectPerfil || !selectPerfil.value) {
                Swal.fire('Atención', 'Primero debes seleccionar un Perfil / Rol a la derecha.', 'warning');
                return;
            }

            if (hiddenIdPerfil) hiddenIdPerfil.value = selectPerfil.value;

            const idMenu = hiddenIdMenu ? hiddenIdMenu.value : '';
            const url = idMenu !== '' ? `/menus/${idMenu}/update` : '/menus';

            try {
                const formData = new FormData(formInsert);
                const resp = await fetch(urlBaseSegura + url, {
                    method: 'POST',
                    body: formData
                });

                const json = await resp.json();

                if (json.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Completado!',
                        text: json.mensaje,
                        timer: 1400,
                        showConfirmButton: false
                    });
                    resetearFormulario();
                    await cargarArbolMenus(selectPerfil.value);
                } else if (json.errors) {
                    Object.keys(json.errors).forEach((campo) => {
                        const errEl = document.getElementById(`error-${campo}`);
                        const groupEl = document.getElementById(`group-${campo}`);
                        if (errEl) {
                            errEl.textContent = json.errors[campo];
                            errEl.style.display = 'block';
                        }
                        if (groupEl) {
                            groupEl.classList.add('has-error');
                        }
                    });
                } else if (json.mensaje) {
                    Swal.fire('Error', json.mensaje, 'error');
                }
            } catch (error) {
                console.error('Error al guardar menú:', error);
            }
        });
    }

    // 3. CARGAR DATOS AL HACER CLIC EN UN NODO DEL ÁRBOL
    window.obtenerDatos = async function (id) {
        limpiarErrores();
        try {
            const resp = await fetch(`${urlBaseSegura}/menus/${id}/edit`);
            const json = await resp.json();

            if (json.ok && json.menu) {
                const m = json.menu;
                if (hiddenIdMenu) hiddenIdMenu.value = m.id_menu;
                document.getElementById('nombre').value = m.mnu_texto || '';
                document.getElementById('url').value = m.mnu_enlace || m.mnu_link || '';
                document.getElementById('icono').value = m.mnu_icono || '';
                document.getElementById('padre_id').value = m.mnu_padre ?? 0;

                if (previewIcono) {
                    previewIcono.className = m.mnu_icono || 'fa fa-question text-muted';
                }
                if (tituloFormulario) {
                    tituloFormulario.innerHTML = `<i class="fa fa-pencil"></i> Editar Menú: <strong>${m.mnu_texto}</strong>`;
                }
                const btnSave = document.getElementById('button-save');
                if (btnSave) {
                    btnSave.innerHTML = '<i class="fa fa-save"></i> Actualizar Menú';
                }
            }
        } catch (error) {
            console.error('Error al obtener datos del menú:', error);
        }
    };

    // 4. ELIMINAR MENÚ DEL ÁRBOL
    window.eliminarMenu = async function (id) {
        const confirmacion = await Swal.fire({
            title: '¿Eliminar este menú?',
            text: 'Se quitará del perfil y se eliminará del sistema.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmacion.isConfirmed) return;

        try {
            const resp = await fetch(`${urlBaseSegura}/menus/${id}/delete`, {
                method: 'POST'
            });
            const json = await resp.json();

            if (json.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: json.mensaje,
                    timer: 1300,
                    showConfirmButton: false
                });
                if (hiddenIdMenu && hiddenIdMenu.value == id) {
                    resetearFormulario();
                }
                await cargarArbolMenus(selectPerfil.value);
            } else {
                Swal.fire('Error', json.mensaje || 'No se pudo eliminar.', 'error');
            }
        } catch (error) {
            console.error('Error al eliminar menú:', error);
        }
    };
});