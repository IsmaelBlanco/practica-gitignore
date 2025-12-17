document.addEventListener("DOMContentLoaded", function () {
    const botones = document.querySelectorAll(".btnfiltrado");

    botones.forEach(boton => {
        const flecha = boton.querySelector(".flecha");
        const targetId = boton.getAttribute("data-bs-target");
        const target = document.querySelector(targetId);

        if (flecha && target) {
            target.addEventListener('shown.bs.collapse', () => {
                flecha.classList.add("rotada");
            });

            target.addEventListener('hidden.bs.collapse', () => {
                flecha.classList.remove("rotada");
            });
        }
    });
});