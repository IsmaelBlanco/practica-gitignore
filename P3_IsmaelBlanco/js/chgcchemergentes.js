let cocheIdSeleccionado = null;

document.querySelectorAll('.eliminar-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        cocheIdSeleccionado = btn.dataset.id;
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    });
});

document.getElementById('motivoVendido').addEventListener('click', () => {
    actualizarEstadoCoche('Vendido');
});

document.getElementById('motivoEliminado').addEventListener('click', () => {
    actualizarEstadoCoche('Eliminado');
});

function actualizarEstadoCoche(motivo) {
    fetch('../php/delcch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: cocheIdSeleccionado, motivo })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Eliminar el div visualmente sin recargar
                document.querySelector(`[data-id="${cocheIdSeleccionado}"]`).closest('.coche').remove();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            alert("Error de red o del servidor.");
            console.error(err);
        });

    bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
}

function actualizarEstadoCoche(motivo) {
    fetch('../php/delcch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: cocheIdSeleccionado, motivo })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-id="${cocheIdSeleccionado}"]`).closest('.coche').remove();

                // Mostrar toast con mensaje personalizado
                const toastEl = document.getElementById('estadoToast');
                const toastMsg = document.getElementById('estadoToastMsg');
                toastMsg.textContent = `Coche marcado como ${motivo.toLowerCase()}.`;
                const toast = new bootstrap.Toast(toastEl);
                if (motivo === 'Eliminado') {
                    toastEl.classList.remove("bg-success");
                    toastEl.classList.add("bg-danger");
                } else {
                    toastEl.classList.remove("bg-danger");
                    toastEl.classList.add("bg-success");
                }

                toast.show();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            alert("Error de red o del servidor.");
            console.error(err);
        });

    bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
}
