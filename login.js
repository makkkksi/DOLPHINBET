
const usuarios = [
  { email: "admin@admin.com", password: "123456" }
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

        // Cerrar modal
        const modalElement = document.getElementById("loginModal");
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();

        // Reemplazar el botón por íconos
        const userArea = document.getElementById("userArea");
        userArea.innerHTML = `
          <div class="d-flex gap-3 align-items-center">
            <a href="panel.html" class="text-decoration-none" title="Panel de Control">
              <i class="fa-solid fa-gauge-high fa-lg"></i>
            </a>
            <a href="perfil.html" class="text-decoration-none" title="Perfil">
              <i class="fa-solid fa-user fa-lg"></i>
            </a>
            <a href="#" id="logoutBtn" class="text-decoration-none" title="Cerrar Sesión">
              <i class="fa-solid fa-right-from-bracket fa-lg"></i>
            </a>
          </div>
        `;

        // Evento de cerrar sesión
        document.getElementById("logoutBtn").addEventListener("click", function (e) {
          e.preventDefault();
          userArea.innerHTML = `
            <a href="#" class="btn btn-registrar" data-bs-toggle="modal" data-bs-target="#loginModal" id="btnIngresar">
              <i class="fa-solid fa-circle-user icon"></i> INGRESAR
            </a>
          `;
        });
      } else {
        errorDiv.textContent = "Correo o contraseña incorrectos.";
        errorDiv.style.display = "block";
      }
    });
  }
});


document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("registerForm");

  registerForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("registerName").value.trim();
    const email = document.getElementById("registerEmail").value.trim();
    const password = document.getElementById("registerPassword").value;
    const confirmPassword = document.getElementById("registerConfirmPassword").value;

    const errorDiv = document.getElementById("registerError");

    if (password !== confirmPassword) {
      errorDiv.textContent = "Las contraseñas no coinciden.";
      errorDiv.style.display = "block";
      return;
    }

    //conectar con base de datos pero esta wea no tiene base de datos asi q no

    errorDiv.style.display = "none";
    alert("Registro exitoso!");
    const modalEl = document.getElementById("registerModal");
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
  });
});

