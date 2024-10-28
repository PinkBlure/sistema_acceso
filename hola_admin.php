<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenida</title>
</head>

<body>

  <?php

  require_once "funciones.php";

  // Iniciar sesión o reanudar sesión existente
  session_start();

  if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
  }

  if ($_SESSION['role'] !== 'admin') {
    echo "Acceso denegado. Esta página es solo para administradores.";
    exit();
  }

  $nombre = htmlspecialchars($_SESSION['usuario']);
  ?>

  <h1>Bienvenido, administrador: <?php echo $nombre; ?>!</h1>
  <p>Has iniciado sesión correctamente.</p>
  <a href="logout.php">Cerrar sesión</a>

  <?php

  $mensaje = "";

  // Cargar usuarios desde el archivo JSON
  $usu = cargarUsuarios();

  // Manejar formulario de creación/eliminación de usuario
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioNombre = $_POST['name'];
    $usuarioPass = $_POST['password'];
    $accion = $_POST['funcion'];

    if ($accion === "crear") {
      $mensaje = insertarUsuario($usu, $usuarioNombre, $usuarioPass);
    } elseif ($accion === "eliminar") {
      $mensaje = eliminarUsuario($usu, $usuarioNombre);
    }
  }

  ?>

  <br><br>
  <form method="post">
    <label for="name">Nombre del usuario:</label>
    <input id="name" name="name" type="text" required>
    <br>
    <label for="password">Contraseña del usuario (Solo necesaria para crear):</label>
    <input id="password" name="password" type="password">
    <br>
    <label for="funcion">Elige la función</label>
    <select id="funcion" name="funcion">
      <option value="crear">Crear usuario</option>
      <option value="eliminar">Eliminar usuario</option>
    </select>
    <br><br>
    <input type="submit" value="Realizar función">
  </form>

  <?php if ($mensaje): ?>
    <p><?php echo $mensaje; ?></p>
  <?php endif; ?>

</body>

</html>
