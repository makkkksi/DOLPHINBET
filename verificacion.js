// Simulación de usuarios registrados
const usuarios = [
    { email: "admin@admin.com", password: "1" }
  ];
  
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
  
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
  
        const email = document.getElementById("loginEmail").value.trim();
        const password = document.getElementById("loginPassword").value;
  
        const usuarioValido = usuarios.find(
          (u) => u.email === email && u.password === password
        );
  
        const errorDiv = document.getElementById("loginError");
  
        if (usuarioValido) {
          errorDiv.style.display = "none";
          alert("¡Inicio de sesión exitoso!");
          const modalElement = document.getElementById("loginModal");
          const modal = bootstrap.Modal.getInstance(modalElement);
          modal.hide();
        } else {
          errorDiv.textContent = "Correo o contraseña incorrectos.";
          errorDiv.style.display = "block";
        }
      });
    }
  });
