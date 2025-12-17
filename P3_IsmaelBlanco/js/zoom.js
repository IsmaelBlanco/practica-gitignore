document.addEventListener('DOMContentLoaded', () => {
  // Elementos
  const zoomContainer = document.getElementById('zoom-container');
  const zoomImage = document.getElementById('zoom-image');
  const zoomClose = document.getElementById('zoom-close');

  // Seleccionar todas las imágenes dentro de .eachfoto
  const fotos = document.querySelectorAll('.eachfoto img');

  fotos.forEach(img => {
    img.style.cursor = 'zoom-in';  // Poner cursor para que el usuario note que es clicable
    img.addEventListener('click', () => {
      zoomImage.src = img.src;
      zoomContainer.style.display = 'flex';
      document.body.style.overflow = 'hidden'; // evitar scroll de fondo
    });
  });

  // Cerrar zoom con botón
  zoomClose.addEventListener('click', () => {
    zoomContainer.style.display = 'none';
    zoomImage.src = '';
    document.body.style.overflow = ''; // restaurar scroll
  });

  // Cerrar zoom haciendo click fuera de la imagen
  zoomContainer.addEventListener('click', (e) => {
    if (e.target === zoomContainer) {
      zoomClose.click();
    }
  });
});
