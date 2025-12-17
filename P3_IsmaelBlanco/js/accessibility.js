document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;
    const body = document.body;
    const toggleBtn = document.getElementById('toggle-accessibility');
    const optionsPanel = document.getElementById('accessibility-options');
    const themeSelect = document.getElementById('theme-select');
    const fontSizeRange = document.getElementById('font-size');
    const fontSelect = document.getElementById('font-select');
    const resetBtn = document.getElementById('reset-accessibility');

    // Mostrar/ocultar panel
    toggleBtn.addEventListener('click', () => {
        optionsPanel.classList.toggle('hidden');
    });

    // Tema
    themeSelect.addEventListener('change', () => {
        if (themeSelect.value === 'light') {
            body.classList.add('light-theme');
            body.classList.remove('dark-theme');
        } else {
            body.classList.add('dark-theme');
            body.classList.remove('light-theme');
        }
        localStorage.setItem('theme', themeSelect.value);
    });

    // Fuente
    fontSelect.addEventListener('change', () => {
        body.classList.remove('serif-font', 'dyslexic-font');
        if (fontSelect.value === 'serif') body.classList.add('serif-font');
        else if (fontSelect.value === 'dyslexic') body.classList.add('dyslexic-font');
        localStorage.setItem('font', fontSelect.value);
    });

    // Tamaño de fuente
    fontSizeRange.addEventListener('input', () => {
        html.style.fontSize = `${fontSizeRange.value}px`;
        localStorage.setItem('fontSize', fontSizeRange.value);
    });

  // Restablecer ajustes
resetBtn.addEventListener('click', () => {
    // Aplica tema oscuro por defecto
    body.classList.add('dark-theme');
    body.classList.remove('light-theme', 'serif-font', 'dyslexic-font');
    html.style.fontSize = '';
    localStorage.removeItem('theme');
    localStorage.removeItem('font');
    localStorage.removeItem('fontSize');

    // Reset inputs visuales
    themeSelect.value = 'dark';
    fontSelect.value = 'default';
    fontSizeRange.value = 16;

    // Disparar eventos de cambio para actualizar la UI correctamente
    themeSelect.dispatchEvent(new Event('change'));
    fontSelect.dispatchEvent(new Event('change'));
    fontSizeRange.dispatchEvent(new Event('input'));
});


    // Cargar preferencias al entrar
    const savedTheme = localStorage.getItem('theme');
    const savedFont = localStorage.getItem('font');
    const savedSize = localStorage.getItem('fontSize');

    if (!savedTheme || savedTheme === 'dark') {
        body.classList.add('dark-theme');
        themeSelect.value = 'dark';
    } else if (savedTheme === 'light') {
        body.classList.remove('dark-theme');
        themeSelect.value = 'light';
    }

    if (savedFont) {
        fontSelect.value = savedFont;
        if (savedFont === 'serif') body.classList.add('serif-font');
        if (savedFont === 'dyslexic') body.classList.add('dyslexic-font');
    }

    if (savedSize) {
        fontSizeRange.value = savedSize;
        html.style.fontSize = `${savedSize}px`;
    }
});
