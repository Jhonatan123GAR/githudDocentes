<?php
  // Verifica si se proporciona el parámetro "id" en la URL
  if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirige a una página de error si no se proporciona el parámetro
    header("Location: error.php");
    exit();
  }

  // Obtiene el ID_Docente de la URL
  $idDocente = $_GET['id'];

  // Realiza la conexión a la base de datos (asegúrate de tener un archivo database.php válido)
  require 'database.php';

  // Prepara la consulta SQL para obtener datos de la tabla tutorias
  $sql = $conexion->prepare("SELECT ID_Estudiante, Nombre FROM tutorias WHERE ID_Docente = ?");
  $sql->bind_param("s", $idDocente);
  $sql->execute();
  $result = $sql->get_result();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Lista de Alumnos</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <?php require 'partials/header.php' ?>
    <div style="position: absolute; top: 10px; right: 10px;">
      <a href="javascript:history.go(-1)">Volver</a>
    </div>
    <h1>Otra Página</h1>
    <h2>Estudiantes Asociados:</h2>
    <table border="1">
      <tr>
        <th>Número</th>
        <th>ID_Estudiante</th>
        <th>Nombre</th>
      </tr>
      <?php
        $numero = 1; // Inicializar el contador
        foreach ($result as $row) {
          echo "<tr>";
          echo "<td>{$numero}</td>";
          echo "<td>{$row['ID_Estudiante']}</td>";
          echo "<td>{$row['Nombre']}</td>";
          echo "</tr>";
          $numero++; // Incrementar el contador
        }
      ?>
    </table>
  </body>
</html>
