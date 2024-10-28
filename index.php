<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>

  <?php

  require_once "funciones.php";

  // Iniciar sesión o reanudar sesión existente
  session_start();

  $mensaje = "";
  $intentosPermitidos = 3;
  $bloqueoTiempo = 60; // segundos

  // Variables para temporizador
  if (!isset($_SESSION['intentos'])) {
    $_SESSION['intentos'] = 0;
  }

  if (!isset($_SESSION['ultimo_intento'])) {
    $_SESSION['ultimo_intento'] = time();
  }

  // Temporizador de bloqueo por intentos fallidos
  if ($_SESSION['intentos'] >= $intentosPermitidos) {
    $tiempoRestante = $bloqueoTiempo - (time() - $_SESSION['ultimo_intento']);

    if ($tiempoRestante > 0) {
      $mensaje = "Demasiados intentos fallidos. Intente de nuevo en $tiempoRestante segundos.";
    } else {
      $_SESSION['intentos'] = 0;
      $_SESSION['ultimo_intento'] = time();
    }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['intentos'] < $intentosPermitidos) {
    $nombre = $_POST['name'] ?? '';
    $contraseña = $_POST['pass'] ?? '';

    // Cargar usuarios desde el archivo JSON
    $usuarios = cargarUsuarios();

    // Verificar si el usuario existe y la contraseña es correcta
    if (array_key_exists($nombre, $usuarios) && $usuarios[$nombre] === $contraseña) {
      // Reiniciar los intentos fallidos en caso de inicio de sesión exitoso
      $_SESSION['intentos'] = 0;

      $_SESSION['usuario'] = $nombre;
      $_SESSION['rol'] = $usuarios[$nombre]['rol'];

      // Redirigir según el tipo de usuario
      if ($_SESSION['rol'] == "admin") {
        header("Location: hola_admin.php");
      } else {
        header("Location: hola.php");
      }

      exit();
    } else {
      // Incrementar el contador de intentos fallidos
      $_SESSION['intentos']++;
      $_SESSION['ultimo_intento'] = time();

      $mensaje = "Nombre de usuario o contraseña incorrectos.";

      if ($_SESSION['intentos'] >= $intentosPermitidos) {
        $mensaje .= " Has alcanzado el límite de intentos. Bloqueado durante 60 segundos.";
      }
    }
  }
  ?>

  <form method="POST">
    <label for="name">Nombre: </label>
    <input name="name" id="name" type="text" required>
    <label for="pass">Contraseña: </label>
    <input name="pass" id="pass" type="password" required>
    <button type="submit">Iniciar Sesión</button>
  </form>

  <p><?php echo htmlspecialchars($mensaje); ?></p>

</body>

</html>
