<?php
session_start();

// 1. BLOQUE DE PROTECCIÓN
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=not_logged_in');
    exit;
}

// 2. INCLUIR CONEXIÓN A LA BD
require 'api/v1/conexion.php';
$connObj = new Conexion();
$conn = $connObj->getConnection();

// 3. RECUPERAR DATOS DEL USUARIO
$usuario_id = $_SESSION['user_id'];
$saldo_actual = $_SESSION['user_saldo'] ?? 0;
$saldo_formateado = number_format($saldo_actual, 0, ',', '.');

// 4. CONSULTAR HISTORIAL DE MOVIMIENTOS
$historial = [];
$stmt = $conn->prepare(
    "SELECT h.fecha, h.monto, t.nombre AS tipo, t.suma
     FROM billetera_historial h
     JOIN billetera b ON h.billetera_id = b.id
     JOIN bill_hist_reg_tipo t ON h.tipo_id = t.id
     WHERE b.usuario_id = ?
     ORDER BY h.fecha DESC
     LIMIT 10"
);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
while ($fila = $resultado->fetch_assoc()) {
    $historial[] = $fila;
}
$stmt->close();
$connObj->closeConnection();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DolphinBet - Transacciones</title>
  <link rel="icon" type="image/svg+xml" href="IMG/ico.svg">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
</head>
<body class="perfil-page"> <?php include 'nav.html'; ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <div class="card shadow-lg mb-4 text-center card-profile">
          <div class="card-body">
            <h5 class="card-title text-muted">SALDO ACTUAL</h5>
            <h2 class="display-4 fw-bold" style="color: #073763ff;">$<?php echo $saldo_formateado; ?></h2>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="card shadow-lg h-100 card-profile">
              <div class="card-body">
                <h4 class="mb-3"><i class="fas fa-plus-circle text-success me-2"></i>Abonar Dinero</h4>
                <form id="abonarForm">
                  <div class="mb-3">
                    <label for="montoAbonar" class="form-label">Monto a abonar (CLP)</label>
                    
                    <input type-="number" class="form-control" id="montoAbonar" placeholder="Mín: $1.000 / Máx: $100.000" min="1000" max="100000" required>
                    </div>
                  <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-credit-card me-2"></i>Pagar con Webpay
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-6 mb-4">
            <div class="card shadow-lg h-100 card-profile">
              <div class="card-body">
                <h4 class="mb-3"><i class="fas fa-minus-circle text-danger me-2"></i>Retirar Dinero</h4>
                <form id="retirarForm">
                  <div class="mb-3">
                    <label for="montoRetirar" class="form-label">Monto a retirar (CLP)</label>
                    <input type="number" class="form-control" id="montoRetirar" placeholder="Máx: $<?php echo $saldo_formateado; ?>" min="1" max="<?php echo $saldo_actual; ?>" required>
                  </div>
                  <button type="submit" class="btn btn-danger w-100">
                    <i class="fas fa-university me-2"></i>Solicitar Retiro
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="card shadow-lg card-profile">
          <div class="card-body">
            <h4 class="mb-3"><i class="fas fa-history me-2"></i>Últimos Movimientos</h4>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th scope="col">Fecha</th>
                    <th scope="col">Tipo</th>
                    <th scope="col" class="text-end">Monto</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($historial)): ?>
                    <tr>
                      <td colspan="3" class="text-center text-muted">No hay movimientos registrados.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($historial as $mov): ?>
                      <tr>
                        <td><?php echo date("d/m/Y H:i", strtotime($mov['fecha'])); ?></td>
                        <td><?php echo htmlspecialchars($mov['tipo']); ?></td>
                        
                        <?php if ($mov['suma'] == 1): ?>
                          <td class="text-end text-success fw-bold">
                            + $<?php echo number_format($mov['monto'], 0, ',', '.'); ?>
                          </td>
                        <?php else: ?>
                          <td class="text-end text-danger fw-bold">
                            - $<?php echo number_format($mov['monto'], 0, ',', '.'); ?>
                          </td>
                        <?php endif; ?>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="modal fade" id="webpayModal" tabindex="-1" aria-labelledby="webpayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="webpayModalLabel">Simulación de Pago</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="webpayLoading">
            <p class="text-center">Serás redirigido a Webpay...</p>
            <div class="d-flex justify-content-center">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
          <div id="webpayForm" style="display: none;">
            <div class="mb-3">
              <label class="form-label">Número de Tarjeta</label>
              <input type="text" class="form-control" value="4555 4444 3333 2222">
            </div>
            <div class="row">
              <div class="col-7"><label class="form-label">Fecha Exp.</label><input type="text" class="form-control" value="12/28"></div>
              <div class="col-5"><label class="form-label">CVV</label><input type="text" class="form-control" value="123"></div>
            </div>
            <button id="fakePayBtn" class="btn btn-primary w-100 mt-4">Pagar</button>
          </div>
          <div id="webpaySuccess" class="text-center" style="display: none;">
            <h3 class="text-success"><i class="fas fa-check-circle fa-3x"></i></h3>
            <h4 class="mt-3">¡Pago Aprobado!</h4>
            <p>Tu saldo se actualizará en breve.</p>
          </div>
        </div>
      </div>
    </div>
  </div>


  <?php include 'footer.html'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="login.js"></script> 
  
  <script>
    const saldoActual = <?php echo $saldo_actual; ?>;
    const webpayModal = new bootstrap.Modal(document.getElementById('webpayModal'));
    let montoAAbonar = 0; 

    // --- MANEJO DE ABONO (WEBPAY) ---
    document.getElementById('abonarForm').addEventListener('submit', function(e) {
      e.preventDefault();
      montoAAbonar = parseInt(document.getElementById('montoAbonar').value);

      // ===== INICIO DE CAMBIO (JAVASCRIPT) =====
      if (montoAAbonar < 1000) {
        alert('El monto mínimo para abonar es $1.000');
        return;
      }
      if (montoAAbonar > 100000) {
        alert('El monto máximo para abonar es $100.000');
        return;
      }
      // ===== FIN DE CAMBIO (JAVASCRIPT) =====

      // 1. Mostrar modal y estado de carga
      document.getElementById('webpayLoading').style.display = 'block';
      document.getElementById('webpayForm').style.display = 'none';
      document.getElementById('webpaySuccess').style.display = 'none';
      webpayModal.show();

      // 2. Simular redirección (2 segundos)
      setTimeout(() => {
        document.getElementById('webpayLoading').style.display = 'none';
        document.getElementById('webpayForm').style.display = 'block';
      }, 2000);
    });

    // 3. Manejar clic en el botón de pago FALSO
    document.getElementById('fakePayBtn').addEventListener('click', function() {
      document.getElementById('webpayForm').style.display = 'none';
      document.getElementById('webpaySuccess').style.display = 'block';
      
      handleTransaction('abonar', montoAAbonar);
      
      setTimeout(() => {
        webpayModal.hide();
        window.location.reload();
      }, 2500);
    });


    // --- MANEJO DE RETIRO ---
    document.getElementById('retirarForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const montoRetirar = parseInt(document.getElementById('montoRetirar').value);

      if (montoRetirar <= 0) {
        alert('El monto debe ser positivo.');
        return;
      }
      if (montoRetirar > saldoActual) {
        alert('Error: Saldo insuficiente. No puedes retirar más de lo que tienes.');
        return;
      }

      if (confirm(`¿Estás seguro que deseas retirar $${montoRetirar.toLocaleString('es-CL')}?`)) {
        handleTransaction('retirar', montoRetirar);
      }
    });


    // --- FUNCIÓN FETCH (Común para ambas) ---
    async function handleTransaction(tipo, monto) {
      const formData = new FormData();
      formData.append('tipo', tipo);
      formData.append('monto', monto);

      try {
        const response = await fetch('update_saldo.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();

        if (result.success) {
          if (tipo === 'retirar') {
            alert('¡Retiro exitoso! La página se recargará.');
            window.location.reload();
          }
        } else {
          alert('Error: ' + result.msg);
        }
      } catch (err) {
        alert('Error de conexión con el servidor. ' + err);
      }
    }
  </script>

</body>
</html>