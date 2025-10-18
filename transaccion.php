<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DolphinBet - Transacciones</title>
  <link rel="icon" type="image/svg+xml" href="IMG/ico.svg"> 
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
</head>

<body class="d-flex flex-column min-vh-100">
  <?php include 'footer.html'; ?>

  <main class="container my-5 flex-grow-1">

    <!-- saldo -->
    <section class="card text-center mb-5 shadow-sm">
      <div class="card-body">
        <h4>Saldo Actual</h4>
        <h2 class="text-success fw-bold">$0</h2> 
      </div>
    </section>

    <!--- Deposito y retiro -->
    <div class="row justify-content-center">
      
      <!-- Depositar -->
      <section class="col-md-8 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white fw-bold">
            Depositar Dinero
          </div>
          <div class="card-body">
            <form>
              <div class="form-floating mb-3">
                <input type="number" class="form-control" id="montoDeposito" placeholder="100" required>
                <label for="montoDeposito">Monto a Depositar</label>
              </div>
              <button type="submit" class="btn btn-success w-100">Depositar</button>
            </form>
          </div>
        </div>
      </section>

      <!-- Retirar -->
      <section class="col-md-8 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-danger text-white fw-bold">
            Retirar Dinero
          </div>
          <div class="card-body">
            <form>
              <div class="form-floating mb-3">
                <input type="number" class="form-control" id="montoRetiro" placeholder="50" required>
                <label for="montoRetiro">Monto a Retirar</label>
              </div>
              <button type="submit" class="btn btn-danger w-100">Retirar</button>
            </form>
          </div>
        </div>
      </section>

    </div>
  </main>

  <!-- Footer -->
  <?php include 'footer.html'; ?>

  <!-- scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="login.js"></script>

</body>
</html>