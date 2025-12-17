let archivos = []; // almacena archivos sin duplicados
const TIPOS_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp'];

function actualizarBordePrimeraImagen() {
  const imgs = document.querySelectorAll("#vistaFotos img");
  imgs.forEach(img => img.classList.remove("primera"));
  if (imgs.length > 0) imgs[0].classList.add("primera");
}

function mostrarImagenes() {
  const previewContainer = document.getElementById('vistaFotos');
  previewContainer.innerHTML = '';

  archivos.forEach((file, i) => {
    const img = document.createElement('img');
    img.alt = "Vista previa";
    img.dataset.index = i;
    if (i === 0) img.classList.add("primera");
    if (i === archivos.length-1) img.classList.add("ultima");

    const reader = new FileReader();
    reader.onload = e => img.src = e.target.result;
    reader.readAsDataURL(file);

    img.addEventListener('click', () => {
      const modal = document.getElementById('modalZoom');
      const modalImg = document.getElementById('modalImg');
      modalImg.src = img.src;
      modal.style.display = 'flex';
    });

    previewContainer.appendChild(img);
  });

  actualizarBordePrimeraImagen();
}

function handleFiles(newFiles) {
  const incoming = Array.from(newFiles);
  if (incoming.length === 0) return;

  const nuevosValidos = incoming.filter(f => {
    const esValido = TIPOS_PERMITIDOS.includes(f.type);
    if (!esValido) alert(`El archivo "${f.name}" no es una imagen válida (jpg, png o webp).`);
    return esValido;
  });

  if (nuevosValidos.length === 0) return;

  nuevosValidos.forEach(nuevo => {
    const existe = archivos.some(archivo =>
      archivo.name === nuevo.name && archivo.lastModified === nuevo.lastModified
    );
    if (!existe) archivos.push(nuevo);
  });

  if (archivos.length > 7) {
    alert("Solo puedes subir un máximo de 7 imágenes.");
    archivos = archivos.slice(0, 7);
  }

  mostrarImagenes();
}

// Evento drag & drop y click para input file
const dropArea = document.getElementById("dropArea");
const fileInput = document.getElementById("fotoDrop");

dropArea.addEventListener("click", () => fileInput.click());

dropArea.addEventListener("dragover", e => {
  e.preventDefault();
  dropArea.style.background = "#e0f7ff";
});

dropArea.addEventListener("dragleave", () => {
  dropArea.style.background = "";
});

dropArea.addEventListener("drop", e => {
  e.preventDefault();
  dropArea.style.background = "";
  handleFiles(e.dataTransfer.files);
});

fileInput.addEventListener("change", e => {
  handleFiles(e.target.files);
});

// Vaciar fotos
document.getElementById("vaciarFotos").addEventListener("click", () => {
  archivos = [];
  fileInput.value = "";
  document.getElementById("vistaFotos").innerHTML = "";
});

// Sortable para reordenar imágenes
Sortable.create(document.getElementById('vistaFotos'), {
  animation: 150,
  ghostClass: 'sortable-ghost',
  dragClass: 'sortable-drag',
  chosenClass: 'sortable-chosen',
  onEnd: () => {
    // Actualizar el array archivos según el nuevo orden visual
    const imgs = document.querySelectorAll("#vistaFotos img");
    const nuevosArchivos = [];
    imgs.forEach(img => {
      const idx = parseInt(img.dataset.index);
      nuevosArchivos.push(archivos[idx]);
    });
    archivos = nuevosArchivos;
    mostrarImagenes();
  }
});

// Calcular ganancia
function calcularGanancia(event) {
  const precioCompra = parseFloat(document.getElementById('precio_compra')?.value) || 0;
  const precioInversion = parseFloat(document.getElementById('precio_inversion')?.value) || 0;
  const precioVenta = parseFloat(document.getElementById('precio_venta')?.value) || 0;
  const ganancia = precioVenta - (precioCompra + precioInversion);
  const gananciaInput = document.getElementById('ganancia');
  gananciaInput.value = (Math.abs(ganancia) < 0.005 ? 0 : ganancia).toFixed(2);
  gananciaInput.style.color = ganancia >= 0 ? 'green' : 'red';
}

document.addEventListener("DOMContentLoaded", () => {
  ['precio_compra', 'precio_inversion', 'precio_venta'].forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener('input', calcularGanancia);
      input.addEventListener('change', calcularGanancia);
    }
  });
});

// Enviar formulario con imágenes en orden
document.querySelector("form").addEventListener("submit", e => {
  e.preventDefault();

  const formData = new FormData(e.target);

  // Eliminar fotos previas del FormData, si existen
  formData.delete("foto[]");

  archivos.forEach(file => {
    formData.append("foto[]", file);
  });

  fetch("addcch.php", {
    method: "POST",
    body: formData
  })
  .then(response => {
    if (response.redirected) {
      window.location.href = response.url;
    } else {
      return response.text().then(text => console.error("Error:", text));
    }
  })
  .catch(err => console.error("Error al subir:", err));
});
