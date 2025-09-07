
const usuarios = [
  { email: "admin@admin.com", password: "1234" },
  {email: "me@me", password:"zL5e2atYuLA3KyM"}
];

//visto en clases el login, conectado con el modal del bostrap :p

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

        // Cerrar el fakin modal
        const modalElement = document.getElementById("loginModal");
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();

        // pomer iconos en el nav en el div de user area
        const userArea = document.getElementById("userArea");
        userArea.innerHTML = `
          <div class="d-flex gap-3 align-items-center">
            <a class="text-decoration-none  text-white" title="Saldo">
              <i class="bi bi-currency-dollar"></i> 10000
            </a>
            <a href="perfil.html" class="text-decoration-none text-white" title="Perfil">
              <i class="fa-solid fa-user fa-lg"></i>
            </a>
            <a href="#" id="logoutBtn" class="text-decoration-none text-white" title="Cerrar Sesión">
              <i class="fa-solid fa-right-from-bracket fa-lg"></i>
            </a>
          </div>
        `;

        // Ecerrar
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
    alert("Registrado (No hay bases de datos asi q las credenciales nuevas no funcionaran :p)");
    const modalEl = document.getElementById("registerModal");
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
  });
});

