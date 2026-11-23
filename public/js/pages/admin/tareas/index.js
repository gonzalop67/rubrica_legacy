document.addEventListener('DOMContentLoaded', () => {
    const urlBaseSegura = (typeof base_url !== 'undefined') ? base_url : '';

    // =========================================================================
    // 1. FUNCIÓN PARA EL CHECKBOX (onclick="checkTask(this, id)")
    // =========================================================================
    window.checkTask = async function (checkbox, id) {
        const hecho = checkbox.checked ? 1 : 0;
        const fila = checkbox.closest('tr');
        // La celda "Estatus" es la 3ra columna (índice 2)
        const celdaEstatus = fila ? fila.cells[2] : null;

        // Deshabilitar temporalmente mientras guarda
        checkbox.disabled = true;

        try {
            const formData = new FormData();
            formData.append('hecho', hecho);

            const resp = await fetch(`${urlBaseSegura}/tareas/${id}/check`, {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });

            const json = await resp.json();

            if (json.success) {
                // Actualizar el texto de la columna Estatus sin recargar la página
                if (celdaEstatus) {
                    celdaEstatus.textContent = hecho === 1 ? 'REALIZADO' : 'PENDIENTE';
                }

                // Notificación tipo Toast discreta en la esquina superior derecha
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: json.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                // Revertir checkbox si falló en el servidor
                checkbox.checked = !checkbox.checked;
                Swal.fire('Error', json.message || 'No se pudo cambiar el estado.', 'error');
            }
        } catch (error) {
            console.error('Error al actualizar estado:', error);
            checkbox.checked = !checkbox.checked;
            Swal.fire('Error de Conexión', 'No se pudo comunicar con el servidor.', 'error');
        } finally {
            checkbox.disabled = false;
        }
    };

    // =========================================================================
    // 2. DELEGACIÓN DE EVENTOS: ELIMINAR, RESTAURAR Y DESTRUIR
    // =========================================================================
    document.addEventListener('click', async (e) => {
        // A. BOTÓN ELIMINAR (Enviar a Papelera -> POST /tareas/{id}/delete)
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            e.preventDefault();
            const id = btnDelete.dataset.id;
            const nombre = btnDelete.dataset.nombre || 'esta tarea';

            const confirmacion = await Swal.fire({
                title: '¿Enviar a la papelera?',
                text: `Se enviará a la papelera: "${nombre}"`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (confirmacion.isConfirmed) {
                await ejecutarAccion(`${urlBaseSegura}/tareas/${id}/delete`, btnDelete);
            }
            return;
        }

        // B. BOTÓN RESTAURAR (Desde Papelera -> POST /tareas/{id}/restore)
        const btnRestore = e.target.closest('.btn-restore');
        if (btnRestore) {
            e.preventDefault();
            const id = btnRestore.dataset.id;

            const confirmacion = await Swal.fire({
                title: '¿Restaurar tarea?',
                text: 'La tarea volverá a estar activa en el listado principal.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00a65a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-undo"></i> Sí, restaurar',
                cancelButtonText: 'Cancelar'
            });

            if (confirmacion.isConfirmed) {
                await ejecutarAccion(`${urlBaseSegura}/tareas/${id}/restore`, btnRestore);
            }
            return;
        }

        // C. BOTÓN ELIMINAR DEFINITIVO (Desde Papelera -> POST /tareas/{id}/destroy)
        const btnDestroy = e.target.closest('.btn-destroy');
        if (btnDestroy) {
            e.preventDefault();
            const id = btnDestroy.dataset.id;

            const confirmacion = await Swal.fire({
                title: '¿Eliminar permanentemente?',
                text: '¡Esta acción no se puede deshacer!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-times"></i> Sí, borrar definitivamente',
                cancelButtonText: 'Cancelar'
            });

            if (confirmacion.isConfirmed) {
                await ejecutarAccion(`${urlBaseSegura}/tareas/${id}/destroy`, btnDestroy);
            }
        }
    });

    async function ejecutarAccion(url, boton) {
        try {
            const resp = await fetch(url, {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache'
            });

            const json = await resp.json();

            if (json.success) {
                Swal.fire({
                    title: json.titulo || '¡Completado!',
                    text: json.mensaje || json.message,
                    icon: json.estado || 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    const fila = boton.closest('tr');
                    const tbody = fila ? fila.parentElement : null;

                    if (fila) {
                        fila.remove();
                    }

                    // Si ya no quedan filas en la página actual, recarga para actualizar paginación/mensaje vacío
                    if (!tbody || tbody.children.length === 0) {
                        window.location.reload();
                    }
                });
            } else {
                Swal.fire({
                    title: json.titulo || 'Error',
                    text: json.mensaje || json.message || 'No se pudo completar la operación.',
                    icon: json.estado || 'error'
                });
            }
        } catch (error) {
            console.error('Error en la petición:', error);
            Swal.fire({
                title: 'Error de Conexión',
                text: 'Ocurrió un fallo al comunicarse con el servidor.',
                icon: 'error'
            });
        }
    }
});
