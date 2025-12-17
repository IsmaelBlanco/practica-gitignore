function goToAdmin() {
  fetch('/php/generate-tokenLgn.php')
      .then(res => res.json())
      .then(data => {
        if(data.token) {
          // Redirige con token en URL
          window.location.href = '/pages/admin-access.php?token=' + data.token;
        }
      });
}

document.getElementById('adminLogo').addEventListener('click', goToAdmin);

document.addEventListener('keydown', function(e) {
  if (e.ctrlKey && e.altKey && e.code === 'KeyA') {
    goToAdmin();
  }
});