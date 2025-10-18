<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DolphinBet - Casino Online</title>
    <link rel="icon" type="image/svg+xml" href="IMG/ico.svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="nav.foot.js"></script>
    <link rel="stylesheet" href="estilo2.css">
    <link rel="stylesheet" href="estilo.css">
  </head>
  <body>
    <?php include 'nav.html'; ?>
    <div class="perfilUsuario">
      <image src="IMG/Wallpaper_ruleta.png"  class="fondo-img" alt="Logo" >
        <h1 style="text-align: center;">Perfil de Usuario</h1>
        <br>
        <div class="contenedorDatos" style="width:100%;">
          <ul class="nav nav-pills nav-justified" style="width:100%;">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#" data-target="DivDatosPersonales">Datos Personales</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-target="DivTransacciones">Transacciones</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-target="DivSeguridad">Seguridad</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" data-target="DivCasino">Casino</a>
            </li>
          </ul>
        </div>
      <div id="DivDatosPersonales" class="DatosPersonales" style="display: block;">
        <div class="contenedorDatos" style="width:100%;">
          <div class="datos">
            <h4>Detalles del Perfil</h4>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1">Username @</span>
                </div>
                <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
              </div>

              <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Email" aria-label="Recipient's email" aria-describedby="basic-addon2">
                <div class="input-group-append">
                  
                  <span class="input-group-text" id="basic-addon2">@DolphinBet.com</span>
                </div>
              </div>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1">Fecha Nacimiento</span>
                </div>
                <input type="date" class="form-control" placeholder="Fecha Nacimiento" aria-label="Fecha Nacimiento" aria-describedby="basic-addon1">
              </div>
              <br>
              <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary ">Actualizar</button>
              </div>
          </div>
          <div class="fotografia">
            <img src="IMG/perfil.jpg" alt="" style="max-width: 60%; height: auto; display: block; margin: 0 auto;">
          </div>
        </div>
      </div>
      <div id="DivTransacciones" class="DatosPersonales" style="display: none;">
        <h4 style="text-align: right !important;">Saldo Actual : 125.000</h4>
        <table class="table table-dark">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col" colspan="2">Fecha</th>
              <th scope="col">Tipo</th>
              <th scope="col">Monto</th>
            </tr>
          </thead>
          <tbody>
            <tr class="table-active" >
              <td scope="row">1</td>
              <td colspan="2" class="table-active">30-07-2025</td>
              <td>Abono</td>
              <td style="text-align: right;">50.000</td>
            </tr>
            <tr class="table-active">
              <td scope="row">2</td>
              <td colspan="2" class="table-active">01-06-2025</td>
              <td>Retiro</td>
              <td style="text-align: right;">20.000</td>
            </tr>
            <tr class="table-active">
              <td scope="row">3</td>
              <td colspan="2" class="table-active">15-05-2025</td>
              <td>Retiro</td>
              <td style="text-align: right;">60.000</td>
            </tr>
            <tr class="table-active">
              <td scope="row">4</td>
              <td colspan="2" class="table-active">20-04-2025</td>
              <td>Abono</td>
              <td style="text-align: right;">10.000</td>
            </tr>
            <tr class="table-active">
              <td scope="row">5</td>
              <td colspan="2" class="table-active">10-02-2025</td>
              <td>Retiro</td>
              <td style="text-align: right;">30.000</td>
            </tr>
            <tr class="table-active">
              <td scope="row">6</td>
              <td colspan="2" class="table-active">01-01-2025</td>
              <td>Abono</td>
              <td style="text-align: right;">100.000</td>
            </tr>
          </tbody>
        </table>



      </div>
      <div id="DivSeguridad" class="DatosPersonales" style="display: none;">
        <h4>Seguridad</h4>
        
            <label for="inputPassword5">Contraseña Anterior</label>
              <input type="password" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock">
              <small id="passwordHelpBlock" class="form-text text-muted">
            </small>
        <br>
          <label for="inputPassword5">Contraseña Nueva</label>
          <input type="password" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock">
          <small id="passwordHelpBlock" class="form-text text-muted">
            Su contraseña debe tener entre 8 y 20 caracteres, contener letras y números, y 
              no debe contener espacios, caracteres especiales ni emojis.
          </small>
        <br>
        <br>
          <label for="inputPassword5">Confirmar Contraseña Nueva</label>
          <input type="password" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock">
          <small id="passwordHelpBlock" class="form-text text-muted">            
          </small>
        <br>
        <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary ">Cambiar Contraseña</button>
              </div>
      </div>
      <div id="DivCasino" class="DatosPersonales" style="display: none;">
        <h4>Volver al Casino</h4>
        <a href="index.html">
          <img src="IMG/iconruleta.png" alt="" style="max-width: 40%; height: auto; display: block; margin: 0 auto;">
        </a>
      </div>


  </body>
  <?php include 'footer.html'; ?>
</html>
<script>
document.querySelectorAll('.nav-link').forEach(tab => {
  tab.addEventListener('click', function(e) {
    e.preventDefault();
    if (this.classList.contains('disabled')) return;

    // Quitar clase activa de todas las tabs
    document.querySelectorAll('.nav-link').forEach(t => t.classList.remove('active'));

    // Ocultar todos los divs de contenido
    document.querySelectorAll('.DatosPersonales').forEach(div => div.style.display = 'none');

    // Activar la tab actual
    this.classList.add('active');

    // Mostrar el div correspondiente
    const target = this.getAttribute('data-target');
    if (target) {
      document.getElementById(target).style.display = 'block';
    }
  });
});
</script>
