<?php

define('USUARIOS_JSON', 'usuarios.json');

function cargarUsuarios() {
  if (file_exists(USUARIOS_JSON)) {
      $jsonData = file_get_contents(USUARIOS_JSON);
      $usuarios = json_decode($jsonData, true);
      if (json_last_error() === JSON_ERROR_NONE) {
          return $usuarios;
      }
  }
  return [];
}

function guardarUsuarios($usuarios) {
  $jsonData = json_encode($usuarios, JSON_PRETTY_PRINT);

  if (json_last_error() !== JSON_ERROR_NONE) {
      echo "Error en JSON: " . json_last_error_msg();
      return;
  }

  // Confirmar permisos de escritura
  if (!is_writable(USUARIOS_JSON)) {
      echo "Error: No se puede escribir en " . USUARIOS_JSON;
      return;
  }

  // Intentar guardar en el archivo JSON
  if (file_put_contents(USUARIOS_JSON, $jsonData) === false) {
      error_log("Error: No se pudo escribir en " . USUARIOS_JSON);
      echo "Error: No se pudo guardar en usuarios.json.";
  } else {
      echo "Usuarios guardados correctamente en usuarios.json.";
  }
}



function insertarUsuario(&$usuarios, $nombre, $pass)
{
  if (!array_key_exists($nombre, $usuarios)) {
    $usuarios[$nombre] = $pass;
    guardarUsuarios($usuarios);
    return "Usuario '$nombre' añadido correctamente.";
  } else {
    return "Error: El usuario '$nombre' ya existe.";
  }
}

function eliminarUsuario(&$usuarios, $nombre)
{
  if (array_key_exists($nombre, $usuarios)) {
    unset($usuarios[$nombre]);
    guardarUsuarios($usuarios);
    return "Usuario '$nombre' eliminado correctamente.";
  } else {
    return "Error: El usuario '$nombre' no existe.";
  }
}
