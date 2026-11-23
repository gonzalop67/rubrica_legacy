document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('formulario');
    const tareaInput = document.getElementById('tarea');
    const inputIdTarea = document.getElementById('id_tarea');
    const btnSubmit = document.getElementById('btn-submit');

    // Desactivar validación nativa del navegador para que actúe nuestro JS / Backend
    if (formulario) {
        formulario.setAttribute('novalidate', true);
    }

    // Limpiar el error visual mientras el usuario escribe
    if (tareaInput) {
        tareaInput.addEventListener('input', () => {
            const errorContainer = document.getElementById('error-tarea');
            const formGroup = tareaInput.closest('.form-group');

            if (errorContainer) {
                errorContainer.innerHTML = '';
                errorContainer.style.display = 'none';
            }
            if (formGroup) {
                formGroup.classList.remove('has-error');
            }
            tareaInput.classList.remove('is-invalid');
        });
    }

    // Interceptar el envío del formulario
    if (formulario) {
        formulario.addEventListener('submit', async (e) => {
            e.preventDefault();
            await fntProcesar();
        });
    }

    async function fntProcesar() {
        // Obtener base_url de forma segura (si no existe globalmente, la extrae del botón Volver)
        const btnVolver = document.querySelector('.btn-volver');
        const urlBaseSegura = (typeof base_url !== 'undefined')
            ? base_url
            : (btnVolver ? btnVolver.getAttribute('href').replace(/\/tareas$/, '') : '');

        const url = (inputIdTarea && inputIdTarea.value !== '')
            ? '/tareas/' + inputIdTarea.value + '/update'
            : '/tareas';

        const textoOriginalBtn = btnSubmit ? btnSubmit.innerHTML : '';
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
        }

        try {
            const formData = new FormData(formulario);

            if (tareaInput) {
                formData.set('tarea', tareaInput.value.replace(/\s+/g, ' ').trim());
            }

            const resp = await fetch(urlBaseSegura + url, {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData,
            });

            // Leer primero como texto para detectar si PHP devolvió un Fatal Error o HTML en vez de JSON
            const rawText = await resp.text();
            let json;
            try {
                json = JSON.parse(rawText);
            } catch (parseError) {
                console.error('El servidor no devolvió un JSON válido. Respuesta recibida:', rawText);
                Swal.fire({
                    title: 'Error del Servidor',
                    text: 'La respuesta no es JSON válido. Revisa la consola (F12) para ver el error de PHP.',
                    icon: 'error'
                });
                return;
            }

            if (json.ok) {
                Swal.fire({
                    title: '¡Completado!',
                    text: json.mensaje,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    formulario.reset();
                    window.location.href = urlBaseSegura + '/tareas';
                });
            } else if (json.errors) {
                Object.keys(json.errors).forEach((campo) => {
                    const mensajeError = json.errors[campo];
                    const errorContainer = document.getElementById(`error-${campo}`);

                    // CORREGIDO: índice [0] añadido
                    const elementosByName = document.getElementsByName(campo);
                    let elemento = (elementosByName.length > 0) ? elementosByName[0] : document.getElementById(campo);

                    if (errorContainer) {
                        errorContainer.innerHTML = mensajeError;
                        errorContainer.style.display = 'block';
                    }

                    if (elemento) {
                        elemento.classList.remove('is-valid');
                        elemento.classList.add('is-invalid');

                        const formGroup = elemento.closest('.form-group');
                        if (formGroup) {
                            formGroup.classList.add('has-error');
                        }
                    }
                });

                Swal.fire({
                    title: 'Error de Validación',
                    text: 'Por favor, corrige los campos remarcados en rojo.',
                    icon: 'error'
                });
            } else if (json.mensaje) {
                Swal.fire({
                    title: 'Error de Proceso',
                    text: json.mensaje,
                    icon: 'error'
                });
            }
        } catch (error) {
            console.error('Error crítico en la petición:', error);
            Swal.fire({
                title: 'Error de Conexión',
                text: 'No se pudo establecer comunicación con el servidor.',
                icon: 'error'
            });
        } finally {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = textoOriginalBtn;
            }
        }
    }
});
