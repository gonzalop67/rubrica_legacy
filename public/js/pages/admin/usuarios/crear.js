// Validaciones y envío AJAX para Usuarios (AdminLTE 2 / Bootstrap 3.3.7)
const formulario = document.getElementById('formulario');
const inputs = document.querySelectorAll('#formulario input, #formulario select, #formulario textarea');
const inputIdUsuario = document.getElementById('id_usuario');
const buttonSubmit = document.getElementById('btn-save') || document.getElementById('btn-submit');

const esEdicion = Boolean(inputIdUsuario && inputIdUsuario.value.trim() !== '');

// Expresiones regulares sincronizadas con los atributos 'name' del formulario
const expresiones = {
    dni: /^[a-zA-Z0-9-]{5,20}$/,
    titulo: /^[a-zA-ZÀ-ÿ.\s]{2,32}$/,
    descripcion_titulo: /^[a-zA-ZÀ-ÿ0-9.,()\s-]{4,128}$/,
    primer_apellido: /^[a-zA-ZÀ-ÿ\s]{2,32}$/,
    segundo_apellido: /^[a-zA-ZÀ-ÿ\s]{2,32}$/,
    primer_nombre: /^[a-zA-ZÀ-ÿ\s]{2,32}$/,
    segundo_nombre: /^[a-zA-ZÀ-ÿ\s]{2,32}$/,
    nombre_corto: /^[a-zA-ZÀ-ÿ.\s]{4,64}$/,
    username: /^[a-zA-Z0-9_.-]{4,32}$/,
    email: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/,
    password: /^.{4,64}$/
};

const camposObligatorios = {
    tipo_documento: true,
    dni: true,
    nacionalidad: true,
    titulo: true,
    descripcion_titulo: false,
    primer_apellido: true,
    segundo_apellido: true,
    primer_nombre: true,
    segundo_nombre: true,
    nombre_corto: true,
    username: true,
    email: true,
    password: !esEdicion,
    genero: true,
    activo: true
};

// Autocompletado de Nombre Completo (Apellidos + Nombres)
const actualizarNombreCompleto = () => {
    const pApellido = (document.getElementById('primer_apellido')?.value || '').trim();
    const sApellido = (document.getElementById('segundo_apellido')?.value || '').trim();
    const pNombre = (document.getElementById('primer_nombre')?.value || '').trim();
    const sNombre = (document.getElementById('segundo_nombre')?.value || '').trim();

    const nombreCompleto = [pApellido, sApellido, pNombre, sNombre]
        .filter(Boolean)
        .join(' ');

    const inputNombreCompleto = document.getElementById('nombre_completo');
    if (inputNombreCompleto) {
        inputNombreCompleto.value = nombreCompleto;
    }
};

['primer_apellido', 'segundo_apellido', 'primer_nombre', 'segundo_nombre'].forEach((id) => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', actualizarNombreCompleto);
    }
});

// Validación individual de campo compatible con Bootstrap 3 (.has-error)
const validarCampo = (input) => {
    if (!input || input.disabled || input.type === 'hidden' || input.type === 'file' || input.type === 'checkbox') {
        return true;
    }

    const nombreCampo = input.name || input.id;
    if (!nombreCampo) return true;

    const valor = input.value.trim();
    const errorEl = document.getElementById(`error-${nombreCampo}`);
    const formGroup = input.closest('.form-group');
    const obligatorio = Boolean(camposObligatorios[nombreCampo]);
    const expresion = expresiones[nombreCampo];

    // 1. Verificar obligatoriedad
    if (obligatorio && valor === '') {
        input.classList.add('is-invalid');
        if (formGroup) formGroup.classList.add('has-error');
        if (errorEl) {
            errorEl.innerHTML = 'Este campo es obligatorio.';
            errorEl.style.display = 'block';
        }
        return false;
    }

    // 2. Verificar formato si tiene valor y expresión regular definida
    if (valor !== '' && expresion && !expresion.test(valor)) {
        input.classList.add('is-invalid');
        if (formGroup) formGroup.classList.add('has-error');
        if (errorEl) {
            errorEl.innerHTML = 'El formato o longitud ingresada no es válido.';
            errorEl.style.display = 'block';
        }
        return false;
    }

    // Campo válido
    input.classList.remove('is-invalid');
    if (formGroup) formGroup.classList.remove('has-error');
    if (errorEl) {
        errorEl.innerHTML = '';
        errorEl.style.display = 'none';
    }
    return true;
};

// Validación de checkboxes de Roles
const validarCheckboxesPerfiles = () => {
    const checkboxes = document.querySelectorAll('input[name="roles[]"]');
    if (checkboxes.length === 0) return true;

    const algunoMarcado = Array.from(checkboxes).some((cb) => cb.checked);
    const rolesContainer = document.getElementById('roles-container');
    const errorRoles = document.getElementById('error-roles');

    if (!algunoMarcado) {
        if (rolesContainer) rolesContainer.classList.add('has-error');
        if (errorRoles) {
            errorRoles.innerHTML = 'Debe seleccionar al menos un rol para el usuario.';
            errorRoles.style.display = 'block';
        }
        return false;
    }

    if (rolesContainer) rolesContainer.classList.remove('has-error');
    if (errorRoles) {
        errorRoles.innerHTML = '';
        errorRoles.style.display = 'none';
    }
    return true;
};

// Eventos en tiempo real
inputs.forEach((input) => {
    if (input.type === 'checkbox') {
        input.addEventListener('change', validarCheckboxesPerfiles);
    } else {
        input.addEventListener('keyup', () => validarCampo(input));
        input.addEventListener('blur', () => validarCampo(input));
        input.addEventListener('change', () => validarCampo(input));
    }
});

// Envío de formulario vía Fetch
async function fntProcesar() {
    const url = esEdicion
        ? '/usuarios/' + inputIdUsuario.value.trim() + '/update'
        : '/usuarios';

    // Limpieza previa de errores
    document.querySelectorAll('#formulario .form-group').forEach((fg) => {
        fg.classList.remove('has-error');
    });

    inputs.forEach((input) => {
        input.classList.remove('is-invalid');
        const nombreCampo = input.name || input.id;
        const errorContainer = document.getElementById(`error-${nombreCampo}`);
        if (errorContainer) {
            errorContainer.innerHTML = '';
            errorContainer.style.display = 'none';
        }
    });

    const rolesContainer = document.getElementById('roles-container');
    const errorRoles = document.getElementById('error-roles');
    if (rolesContainer) rolesContainer.classList.remove('has-error', 'is-invalid');
    if (errorRoles) {
        errorRoles.innerHTML = '';
        errorRoles.style.display = 'none';
    }

    try {
        const formData = new FormData(formulario);
        const resp = await fetch(base_url + url, {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        const json = await resp.json();

        if (json.ok) {
            Swal.fire({
                title: '¡Completado!',
                text: json.mensaje,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                formulario.reset();
                window.location.href = base_url + '/usuarios';
            });
        } else if (json.errors) {
            Object.keys(json.errors).forEach((campo) => {
                const mensajeError = json.errors[campo];
                const errorContainer = document.getElementById(`error-${campo}`);

                let elemento = document.getElementsByName(campo)[0] || document.getElementById(campo);
                if (campo === 'roles') {
                    elemento = document.getElementById('roles-container');
                }

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
        console.error('Error crítico en el servidor:', error);
        Swal.fire({
            title: 'Error de Conexión',
            text: 'No se pudo establecer comunicación con el servidor de la plataforma.',
            icon: 'error'
        });
    }
}

if (formulario) {
    formulario.addEventListener('submit', (e) => {
        e.preventDefault();
        let formularioValido = true;

        inputs.forEach((input) => {
            if (!validarCampo(input)) {
                formularioValido = false;
            }
        });

        if (!validarCheckboxesPerfiles()) {
            formularioValido = false;
        }

        if (formularioValido) {
            fntProcesar();
        } else {
            Swal.fire({
                title: 'Formulario incompleto',
                text: 'Por favor, revisa las casillas marcadas en rojo antes de continuar.',
                icon: 'error'
            });
        }
    });
}
