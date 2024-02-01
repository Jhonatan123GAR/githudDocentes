<?php
session_start();

if (empty($_SESSION["id"])) {
    header("location: login.php");
}

require 'database.php';

$id = $_SESSION["id"];
$sql = $conexion->prepare("SELECT t.*, a.Nombre as nombre_alumno FROM tutorias t 
                        JOIN alumno a ON t.ID_Alumno = a.ID_Alumno
                        WHERE t.ID_Docente = ?");
$sql->bind_param("s", $id);
$sql->execute();
$result = $sql->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Docente</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 20px;
        }

        h1, h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        input[type="submit"], button {
            margin: 18px;
            width: 98px;
        }

        input[type="text"] {
            padding: 8px;
            margin: 0px;
        }

        input[type=checkbox], input[type=radio] {
            width: 74px;
        }

        #tutorias {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php require 'partials/header.php' ?>

    <div class="container">
        <div class="float-right">
            <form method="post" action="login.php">
                <input type="submit" class="btn btn-danger" value="Salir">
            </form>
        </div>

        <h1>BIENVENIDO <?php echo $_SESSION["nombre"]; ?></h1>

        <form method="post" action="docente.php"> 
            <table class="table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>ID_Alumno</th>
                        <th>Nombre Alumno</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Asistencia</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $result->data_seek(0);
                        $numero = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$numero}</td>";
                            echo "<td>{$row['ID_Alumno']}</td>";
                            echo "<td>{$row['nombre_alumno']}</td>";
                            echo "<td>{$row['Fecha']}</td>";
                            echo "<td>{$row['Hora']}</td>";
                            
                            $checkedValue = ($row['Asistencia'] == 1) ? 'checked' : '';
                            echo "<td><input type='checkbox' name='asistencia[]' value='1' $checkedValue></td>";
                            
                            echo "<td><input type='text' name='observacion[]' value='{$row['Observacion']}'></td>";
                            echo "</tr>";
                            $numero++;
                        }
                    } else {
                        echo "<tr><td colspan='7'>No se encontraron registros.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div>
                <input type="submit" class="btn btn-primary" value="Guardar">
                <button type="button" class="btn btn-primary" onclick="imprimir()">Imprimir</button>
            </div>
        </form>
    </div>

    <div id="tutorias">
        <?php
        if (!empty($_POST["asistencia"]) && !empty($_POST["observacion"])) {
            $asistencias = $_POST["asistencia"];
            $observaciones = $_POST["observacion"];

            $updateSql = $conexion->prepare("UPDATE tutorias SET Asistencia = ?, Observacion = ? WHERE ID_Docente = ? AND ID_Alumno = ?");

            if ($result->num_rows > 0) {
                $result->data_seek(0);
                $numero = 1;
                while ($row = $result->fetch_assoc()) {
                    $idAlumno = $row['ID_Alumno'];

                    $updateSql->bind_param("ssss", $asistencias[$numero - 1], $observaciones[$numero - 1], $id, $idAlumno);
                    $updateSql->execute();
                    $numero++;
                }
            }

            $updateSql->close();
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function imprimir() {
            window.print();
        }
    </script>
</body>
</html>
