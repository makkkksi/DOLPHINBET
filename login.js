document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const userArea = document.getElementById("userArea");

  // --- VERIFICAR SESIÓN AL CARGAR LA PÁGINA ---
  async function checkLoginStatus() {
    try {
      const response = await fetch("check_session.php", {
        method: "GET",
      });
      const result = await response.json();

      if (result.success) {
        // Si la sesión ya existe, actualiza la UI
        updateUserUI(result.nombre, result.saldo);
      }
    } catch (err) {
      console.error("Error al verificar la sesión:", err);
    }
  }

  // Llama a la función al cargar el DOM en CUALQUIER página
  if (userArea) {
    checkLoginStatus();
  }

  // --- 1. MANEJO DEL REGISTRO ---
  if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {
      e.preventDefault(); // Evita que el formulario se envíe

      // --- MODIFICADO: Obtenemos y limpiamos (trim) los valores ---
      const name = document.getElementById("registerName").value.trim();
      const email = document.getElementById("registerEmail").value.trim();
      const pass = document.getElementById("registerPassword").value;
      const confirmPass = document.getElementById(
        "registerConfirmPassword"
      ).value;
      const errorDiv = document.getElementById("registerError");

      // Limpiar errores previos
      errorDiv.textContent = "";
      errorDiv.style.display = "none";

      // --- NUEVA VALIDACIÓN 1: NOMBRE COMPLETO (Debe tener un espacio) ---
      if (name.length === 0) {
        errorDiv.textContent = "Por favor, ingresa tu nombre completo.";
        errorDiv.style.display = "block";
        return; // Detiene el envío
      }
      if (name.indexOf(" ") === -1) {
        errorDiv.textContent =
          "Por favor, ingresa tu nombre y al menos un apellido.";
        errorDiv.style.display = "block";
        return; // Detiene el envío
      }

      // --- NUEVA VALIDACIÓN 2: EMAIL VÁLIDO (con .com o similar) ---
      // Esta es una expresión regular (RegEx) que busca el formato: texto@texto.texto
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (email.length === 0) {
        errorDiv.textContent = "Por favor, ingresa tu correo electrónico.";
        errorDiv.style.display = "block";
        return; // Detiene el envío
      }
      if (!emailRegex.test(email)) {
        errorDiv.textContent =
          "Por favor, ingresa un correo electrónico válido (ej: usuario@dominio.com).";
        errorDiv.style.display = "block";
        return; // Detiene el envío
      }
      // --- FIN DE NUEVAS VALIDACIONES ---

      // Validación de contraseñas (ya existente)
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
        const response = await fetch("register.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("registerModal")
          );
          modal.hide();
          const loginModal = new bootstrap.Modal(
            document.getElementById("loginModal")
          );
          loginModal.show();
          registerForm.reset();
          errorDiv.style.display = "none";
        } else {
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
        const response = await fetch("login.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("loginModal")
          );
          modal.hide();
          loginForm.reset();
          errorDiv.style.display = "none";

          updateUserUI(result.nombre, result.saldo);
        } else {
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
    let saldoFormateado = "N/A";
    const saldoNum = parseFloat(saldo);
    if (!isNaN(saldoNum)) {
      saldoFormateado = new Intl.NumberFormat("es-CL", {
        style: "currency",
        currency: "CLP",
        minimumFractionDigits: 0,
      }).format(saldoNum);
    }

    userArea.className = "d-flex align-items-center";

    userArea.innerHTML = `
      <span class="navbar-text me-2 text-white" style="white-space: nowrap;">
        ¡Hola, <strong>${nombre}</strong>!
      </span>
      <span class="navbar-text me-2 text-white" style="white-space: nowrap;">
        Saldo: <strong>${saldoFormateado}</strong>
      </span>
      
      <a href="perfil.php" class="btn btn-outline-light btn-sm me-2" title="Mi Perfil">
        <i class="fas fa-user"></i>
      </a>

      <button id="logoutButton" class="btn btn-outline-warning btn-sm" style="white-space: nowrap;">Cerrar Sesión</button>
    `;

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
        window.location.reload();
      }
    } catch (err) {
      console.error("Error al cerrar sesión:", err);
    }
  }
});