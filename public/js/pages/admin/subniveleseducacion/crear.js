document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('formulario');
    const inputNombre = document.getElementById('nombre');
    const inputSlug = document.getElementById('slug');
    const inputId = document.getElementById('id_subnivel_educacion');
    const btnSubmit = document.getElementById('btn-submit');

    // Autogenerar slug al escribir en nombre
    if (inputNombre && inputSlug) {
        inputNombre.addEventListener('input', function () {
            const slug = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            inputSlug.value = slug;
        });
    }

    if (formulario) {
        formulario.addEventListener('submit', function (e) {
            e.preventDefault();

            // Limpiar errores previos
            document.querySelectorAll('.help-block.text-danger').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });

            const idSubnivel = inputId ? inputId.value : '';
            const url = idSubnivel
                ? `${base_url}/subniveles_educacion/${idSubnivel}/update`
                : `${base_url}/subniveles_educacion`;

            const formData = new FormData(formulario);
            btnSubmit.disabled = true;

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                btnSubmit.disabled = false;

                if (data.success || data.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message || data.mensaje
                    }).then(() => {
                        window.location.href = `${base_url}/subniveles_educacion`;
                    });
                } else if (data.errors) {
                    Object.keys(data.errors).forEach(campo => {
                        const spanError = document.getElementById(`error-${campo}`);
                        if (spanError) {
                            spanError.textContent = data.errors[campo];
                            spanError.style.display = 'block';
                        }
                    });
                } else {
                    Swal.fire('Error', data.message || data.mensaje || 'Ocurrió un error inesperado.', 'error');
                }
            })
            .catch(() => {
                btnSubmit.disabled = false;
                Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
            });
        });
    }
});
