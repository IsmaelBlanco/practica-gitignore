document.addEventListener("DOMContentLoaded", () => {
    const minSlider = document.querySelector(".min");
    const maxSlider = document.querySelector(".max");
    const minInput = document.getElementById("precioMinInput");
    const maxInput = document.getElementById("precioMaxInput");
    const minValueLabel = document.querySelector(".minvalue");
    const maxValueLabel = document.querySelector(".maxvalue");
    const rangeTrack = document.getElementById("range_track");

    const realMin = parseInt(minSlider.dataset.min);
    const realMax = parseInt(minSlider.dataset.max);

    function percentToPrecio(percent) {
        return Math.round(realMin + ((realMax - realMin) * (percent / 100)));
    }

    function updateSliderPositions() {
        let minVal = parseInt(minSlider.value);
        let maxVal = parseInt(maxSlider.value);

        // Corregir si sliders se cruzan
        if (minVal > maxVal - 1) {
            minVal = maxVal - 1;
            minSlider.value = minVal;
        }
        if (maxVal < minVal + 1) {
            maxVal = minVal + 1;
            maxSlider.value = maxVal;
        }

        // Convertir a precios reales
        const minPrecio = percentToPrecio(minVal);
        const maxPrecio = percentToPrecio(maxVal);

        // Actualizar inputs ocultos
        minInput.value = minPrecio;
        maxInput.value = maxPrecio;

        // Mostrar valores
        minValueLabel.textContent = "Mínimo: " + minPrecio.toLocaleString() + "€";
        maxValueLabel.textContent = "Máximo: " + maxPrecio.toLocaleString() + "€";

        // Posicionar etiquetas
        minValueLabel.style.left = `calc(${minVal}% - 20px)`;
        maxValueLabel.style.left = `calc(${maxVal}% - 20px)`;

        // ACTUALIZAR RANGE TRACK (¡esto era lo que faltaba!)
        rangeTrack.style.left = `${minVal}%`;
        rangeTrack.style.width = `${maxVal - minVal}%`;
    }

    minSlider.addEventListener("input", updateSliderPositions);
    maxSlider.addEventListener("input", updateSliderPositions);

    // Inicial
    updateSliderPositions();
});
