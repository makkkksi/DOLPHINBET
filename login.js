document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const userArea = document.getElementById("userArea");

  // --- 1. MANEJO DEL REGISTRO ---
  if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {
      e.preventDefault(); // Evita que el formulario se envíe de la forma tradicional

      const name = document.getElementById("registerName").value;
      const email = document.getElementById("registerEmail").value;
      const pass = document.getElementById("registerPassword").value;
      const confirmPass = document.getElementById("registerConfirmPassword").value;
      const errorDiv = document.getElementById("registerError");

      // Validación de contraseñas
      if (pass !== confirmPass) {
        errorDiv.textContent = "Las contraseñas no coinciden.";
        errorDiv.style.display = "block";
        return;
      }

      // Prepara los datos para enviar
      const formData = new FormData();
      formData.append("nombre", name);
      formData.append("email", email);
      formData.append("password", pass);

      try {
        // Esta ruta (register.php) es correcta porque está en la misma carpeta que index.php
        const response = await fetch("register.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          // Si el registro es exitoso, cerramos el modal de registro
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("registerModal")
          );
          modal.hide();

          // Abrimos el modal de login automáticamente
          const loginModal = new bootstrap.Modal(
            document.getElementById("loginModal")
          );
          loginModal.show();
          
          registerForm.reset();
          errorDiv.style.display = "none";
        } else {
          // Muestra el error (ej: "Email ya registrado")
          errorDiv.textContent = result.msg;
          errorDiv.style.display = "block";
        }
      } catch (err) {
        errorDiv.textContent = "Error de conexión. Inténtalo de nuevo.";
        errorDiv.style.display = "block";
      }
    });
  }

  // --- 2. MANEJO DEL LOGIN ---
  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const email = document.getElementById("loginEmail").value;
      const pass = document.getElementById("loginPassword").value;
      const errorDiv = document.getElementById("loginError");

      const formData = new FormData();
      formData.append("email", email);
      formData.append("password", pass);

      try {
        // Esta ruta (login.php) es correcta
        const response = await fetch("login.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          // Si el login es exitoso, cerramos el modal
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("loginModal")
          );
          modal.hide();
          loginForm.reset();
          errorDiv.style.display = "none";

          // Actualizamos la UI para mostrar al usuario
          updateUserUI(result.nombre, result.saldo);
        } else {
          // Muestra el error (ej: "Contraseña incorrecta")
          errorDiv.textContent = result.msg;
          errorDiv.style.display = "block";
        }
      } catch (err) {
        errorDiv.textContent = "Error de conexión. Inténtalo de nuevo.";
        errorDiv.style.display = "block";
      }
    });
  }

  // --- 3. FUNCIÓN PARA ACTUALIZAR LA UI (NAVBAR) ---
  function updateUserUI(nombre, saldo) {
    // Formatea el saldo si es un número
    let saldoFormateado = "N/A";
    const saldoNum = parseFloat(saldo);
    if (!isNaN(saldoNum)) {
        saldoFormateado = new Intl.NumberFormat("es-CL", {
          style: "currency",
          currency: "CLP",
          minimumFractionDigits: 0,
        }).format(saldoNum);
    }

    userArea.innerHTML = `
      <span class="navbar-text me-3 text-white">
        ¡Hola, <strong>${nombre}</strong>!
      </span>
      <span class="navbar-text me-3 text-white">
        Saldo: <strong>${saldoFormateado}</strong>
      </span>
      <button id="logoutButton" class="btn btn-outline-warning">Cerrar Sesión</button>
    `;

    // Añadimos el listener al nuevo botón de logout
    document
      .getElementById("logoutButton")
      .addEventListener("click", handleLogout);
  }

  // --- 4. MANEJO DEL LOGOUT ---
  async function handleLogout() {
    try {
      const response = await fetch("logout.php", {
        method: "POST",
      });
      const result = await response.json();

      if (result.success) {
        // Recargamos la página para resetear el estado
        window.location.reload();
      }
    } catch (err) {
      console.error("Error al cerrar sesión:", err);
    }
  }
});