<?php
session_start();
include 'bd_connector.php'; // Archivo de conexión a la base de datos

$conn = conectarDB(); // Conexión a la base de datos

// Obtener lista de parqueaderos y su estado basado en estado_actual en la tabla PARQUEADERO
$sqlParqueaderos = "SELECT P.ID_PARQUEADERO, P.LOCACION, P.ID_PISO_FK, TV.DESCRIPCION_TIPO_VEHICULO, P.estado_actual
                    FROM PARQUEADERO P
                    INNER JOIN TIPO_VEHICULO TV ON P.ID_TIPO_VEHICULO_FK = TV.ID_TIPO_VEHICULO
                    ORDER BY P.ID_PISO_FK, TV.DESCRIPCION_TIPO_VEHICULO, P.LOCACION";

$resultParqueaderos = $conn->query($sqlParqueaderos);
$parqueaderos = [];

// Conversión de espacios y asignación de colores
if ($resultParqueaderos && $resultParqueaderos->num_rows > 0) {
    while ($row = $resultParqueaderos->fetch_assoc()) {
        $piso = $row['ID_PISO_FK'];
        $tipoVehiculo = $row['DESCRIPCION_TIPO_VEHICULO'];

        // Convertir el número de locación en el formato A1, B1, etc.
        $letraPiso = chr(65 + $piso - 1); // Convierte 1 en 'A', 2 en 'B', etc.
        $locacionFormateada = $letraPiso . $row['LOCACION'];

        // Asignar el color basado en el estado_actual en PARQUEADERO
        $estado = strtolower($row['estado_actual']); // Convertir a minúsculas para CSS
        $colorClase = ($estado === 'reservado') ? 'reservado' : (($estado === 'ocupado') ? 'ocupado' : 'disponible');

        // Organizar el array con los datos actualizados
        $parqueaderos[$piso][$tipoVehiculo][] = [
            'ID_PARQUEADERO' => $row['ID_PARQUEADERO'],
            'locacion' => $locacionFormateada,
            'estado' => ucfirst($estado),
            'colorClase' => $colorClase
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkEase UR - Administrador</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        .sidebar {
            background-color: #d91616;
            position: fixed;
            top: 56px;
            bottom: 0;
            left: -250px;
            width: 250px;
            transition: all 0.3s ease;
            color: white;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
        }

        .profile-img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin: 15px auto;
        }

        .content {
            margin-top: 56px;
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s ease;
        }

        .btn-primary {
            background-color: #d91616;
            border-color: #d91616;
        }

        .btn-primary:hover {
            background-color: #ad0a0a;
            border-color: #ad0a0a;
        }

        .dropdown-item.selected {
            background-color: #d91616;
            color: white;
        }

        .container .border {
            width: 100px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .container .disponible {
            background-color: #d4edda;
            color: #155724;
        }

        .container .reservado {
            background-color: #cce5ff;
            color: #004085;
        }

        .container .ocupado {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" onclick="toggleMenu()">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="menu_admin.php">ParkEase UR - Administrador</a>
        </div>
    </nav>

    <div class="sidebar" id="sidebar">
        <div class="text-center">
            <img src="img/ico_man.png" alt="Foto de perfil" class="profile-img">
            <?php
            if (isset($_SESSION['id_persona'])) {
                $id_persona = $_SESSION['id_persona'];
                $sql = "SELECT nombre, apellido FROM PERSONA WHERE ID_PERSONA = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_persona);
                $stmt->execute();
                $stmt->bind_result($nombre, $apellido);
                $stmt->fetch();
                echo $nombre && $apellido ? "<p>$nombre</p><p>$apellido</p>" : "<p>Nombre</p><p>Apellido</p>";
                $stmt->close();
            } else {
                echo "No has iniciado sesión. Por favor, inicia sesión.";
                header("Location: login.php");
                exit();
            }
            ?>
        </div>

        <nav class="mt-4">
            <a href="reporte_total.php">Generar Reporte</a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Cambiar Contraseña</a>
            <a href="index.html">Salir</a>
        </nav>

        <footer class="text-center mt-auto">
            <p>&copy; 2024 ParkEase. Todos los derechos reservados.</p>
        </footer>
    </div>

    <!-- Dashboard content -->
    <div class="content" id="content">
        <!-- Sección Reservas Activas -->
        <div class="container mt-4">
            <h3>Reservas Activas</h3>
            <div class="table-responsive"> <!-- Hace que la tabla sea desplazable en pantallas pequeñas -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Tipo de Vehículo</th>
                            <th>Espacio de Parqueadero</th>
                            <th>Estado</th>
                            <th>Tiempo Restante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consulta SQL para obtener todas las reservas activas con ID_ESTADO_FK 2 o 3
                        $sqlReservasActivas = "SELECT V.PLACA, 
                                  TV.DESCRIPCION_TIPO_VEHICULO, 
                                  CONCAT(PS.DESCRIPCION_PISO, P.LOCACION) AS espacio, 
                                  E.DESCRIPCION_ESTADO, 
                                  R.HORA_RESERVA
                           FROM RESERVA R
                           INNER JOIN VEHICULO V ON R.PLACA = V.PLACA
                           INNER JOIN TIPO_VEHICULO TV ON V.ID_TIPO_VEHICULO_FK = TV.ID_TIPO_VEHICULO
                           INNER JOIN PARQUEADERO P ON R.ID_PARQUEADERO_FK = P.ID_PARQUEADERO
                           INNER JOIN PISO PS ON P.ID_PISO_FK = PS.ID_PISO
                           INNER JOIN ESTADO E ON R.ID_ESTADO_FK = E.ID_ESTADO
                           WHERE R.ID_ESTADO_FK IN (2, 3)
                           ORDER BY R.HORA_RESERVA";

                        $result = $conn->query($sqlReservasActivas);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $horaReserva = strtotime($row['HORA_RESERVA']);
                                $tiempoRestante = max(0, (900 - (time() - $horaReserva))); // 900 segundos = 15 minutos
                                $minutos = floor($tiempoRestante / 60);
                                $segundos = $tiempoRestante % 60;

                                echo "<tr data-estado='{$row['DESCRIPCION_ESTADO']}' data-tiempo-restante='{$tiempoRestante}'>";
                                echo "<td>{$row['PLACA']}</td>";
                                echo "<td>{$row['DESCRIPCION_TIPO_VEHICULO']}</td>";
                                echo "<td>{$row['espacio']}</td>";
                                echo "<td class='estado'>{$row['DESCRIPCION_ESTADO']}</td>";
                                echo "<td class='temporizador'>" . ($row['DESCRIPCION_ESTADO'] === 'Reservado' ? "$minutos:$segundos" : "-") . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No hay reservas activas.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección Estado del Parqueadero por Piso -->
        <div class="container mt-4">
            <h3>Estado del Parqueadero por Piso</h3>
            <?php foreach ($parqueaderos as $piso => $tiposVehiculo): ?>
                <h4>Piso <?= chr(65 + $piso - 1) ?></h4>
                <?php foreach ($tiposVehiculo as $tipo => $espacios): ?>
                    <h5>Tipo de Vehículo: <?= htmlspecialchars($tipo) ?></h5>
                    <div class="row d-flex flex-wrap">
                        <?php foreach ($espacios as $espacio): ?>
                            <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                                <div class="border text-center <?= htmlspecialchars($espacio['colorClase']) ?>">
                                    <strong><?= htmlspecialchars($espacio['locacion']) ?></strong><br>
                                    <span><?= htmlspecialchars($espacio['estado']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Cambiar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm" action="cambiar_contrasena.php" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function startCountdown() {
            const rows = document.querySelectorAll('tr[data-tiempo-restante]');
            rows.forEach(row => {
                const estado = row.querySelector('.estado').textContent.trim();
                if (estado === 'Reservado') {
                    const tiempoRestante = parseInt(row.getAttribute('data-tiempo-restante'), 10);
                    const temporizadorCell = row.querySelector('.temporizador');
                    if (tiempoRestante > 0) {
                        let remainingTime = tiempoRestante;
                        const intervalId = setInterval(() => {
                            if (remainingTime <= 0) {
                                clearInterval(intervalId);
                                temporizadorCell.textContent = '0:00';
                            } else {
                                remainingTime--;
                                const minutes = Math.floor(remainingTime / 60);
                                const seconds = remainingTime % 60;
                                temporizadorCell.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                            }
                        }, 1000);
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', startCountdown);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>