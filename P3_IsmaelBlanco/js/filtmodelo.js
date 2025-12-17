const btnModelos = document.getElementById('btnModelos');
const modeloUl = document.getElementById('listaModelosUl');
const mensaje = document.getElementById('modeloMensaje');

function actualizarEstadoModelo() {
    const seleccionadas = Array.from(document.querySelectorAll('input[name="filtro_marcas[]"]:checked'));
    if (seleccionadas.length === 0) {

        btnModelos.setAttribute('disabled', 'disabled');
        mensaje.style.display = 'block';

        const collapseElement = document.getElementById('listaModelos');
        if (collapseElement.classList.contains('show')) {

            const collapseInstance = bootstrap.Collapse.getInstance(collapseElement);
            if (collapseInstance) {
                collapseInstance.hide();
            }
        }

    } else {
        btnModelos.removeAttribute('disabled');
        mensaje.style.display = 'none';
    }
}

document.querySelectorAll('input[name="filtro_marcas[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        actualizarEstadoModelo();

        const seleccionadas = Array.from(document.querySelectorAll('input[name="filtro_marcas[]"]:checked')).map(cb => cb.value);

        fetch('../php/filtmodelo.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ marcas: seleccionadas })
        })
        .then(res => res.json())
        .then(modelos => {
            modeloUl.innerHTML = '';
            modelos.forEach(modelo => {
                const li = document.createElement('li');
                li.innerHTML = `<label><input type="checkbox" name="filtro_modelos[]" value="${modelo}"> ${modelo}</label>`;
                modeloUl.appendChild(li);
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    actualizarEstadoModelo();
});

//Aprovecho este JS para meter la redirección de Detalles
document.querySelectorAll('.custom-button').forEach(div => {
    div.addEventListener('click', () => {
        const id = div.getAttribute('data-id');
        window.location.href = `detalles.php?id=${encodeURIComponent(id)}`; 
    });
});
