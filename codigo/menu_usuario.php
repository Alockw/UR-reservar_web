<?php

session_start();
include 'bd_connector.php'; // Archivo de conexión a la base de datos

$conn = conectarDB(); // Conexión a la base de datos


// Verificar si se ha seleccionado una placa y tipo de vehículo
if (isset($_POST['placa'])) {
    $_SESSION['placa_seleccionada'] = $_POST['placa'];
}
if (isset($_POST['tipo_vehiculo'])) {
    $_SESSION['tipo_vehiculo'] = $_POST['tipo_vehiculo'];
}
$placaSeleccionada = $_SESSION['placa_seleccionada'] ?? null;
$tipoVehiculo = $_SESSION['tipo_vehiculo'] ?? null;



// Obtener lista de parqueaderos y su estado basado en estado_actual en la tabla PARQUEADERO
$sqlParqueaderos = "SELECT P.ID_PARQUEADERO, P.LOCACION, P.ID_PISO_FK, TV.DESCRIPCION_TIPO_VEHICULO,
P.estado_actual FROM PARQUEADERO P INNER JOIN TIPO_VEHICULO TV ON P.ID_TIPO_VEHICULO_FK = TV.ID_TIPO_VEHICULO
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
    <title>ParkEase UR - Usuario</title>
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
            <a class="navbar-brand" href="menu_usuario.php">ParkEase UR - Usuario</a>
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

        <div class="dropdown mt-3">
            <a class="dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Seleccionar vehículo</a>
            <ul class="dropdown-menu">
                <?php
                if (isset($id_persona)) {
                    $queryVehiculos = "SELECT PLACA FROM VEHICULO_PERSONA INNER JOIN VEHICULO ON VEHICULO_PERSONA.ID_VEHICULO_FK = VEHICULO.ID_VEHICULO WHERE ID_PERSONA_FK= ?";
                    $stmtVehiculos = $conn->prepare($queryVehiculos);
                    $stmtVehiculos->bind_param("i", $id_persona);
                    $stmtVehiculos->execute();
                    $stmtVehiculos->store_result();
                    $stmtVehiculos->bind_result($placa);
                    if ($stmtVehiculos->num_rows > 0) {
                        while ($stmtVehiculos->fetch()) {
                            $selectedClass = ($placa === $placaSeleccionada) ? 'dropdown-item selected' : 'dropdown-item text-dark';
                            echo "<li><a class='$selectedClass' href='#' onclick='selectPlaca(\"$placa\")'>$placa</a></li>";
                        }
                    } else {
                        echo "<li><a class='dropdown-item text-dark' href='#'>No hay vehículos. Añade uno nuevo.</a></li>";
                    }
                    $stmtVehiculos->close();
                } else {
                    echo "<li><a class='dropdown-item text-dark' href='#'>No hay vehículos asignados</a></li>";
                }
                ?>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-dark" href="#" data-bs-toggle="modal" data-bs-target="#vehiculoModal">Añadir nuevo vehículo</a></li>
            </ul>
        </div>

        <div class="mt-4 text-center">
            <button type="button" onclick="generarReporte()" class="btn btn-primary">Generar Reporte</button>
        </div>

        <div class="mt-4 text-center">
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Cambiar Contraseña</a>
        </div>

        <nav class="mt-4">
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
                        if (isset($_SESSION['id_persona'])) {
                            $id_persona = $_SESSION['id_persona'];
                            $sqlReservasActivas = "SELECT V.PLACA, 
                                              TV.DESCRIPCION_TIPO_VEHICULO AS tipo_vehiculo,
                                              CONCAT(PS.DESCRIPCION_PISO, P.LOCACION) AS espacio, 
                                              E.DESCRIPCION_ESTADO, 
                                              R.HORA_RESERVA
                                       FROM RESERVA R
                                       INNER JOIN VEHICULO V ON R.PLACA = V.PLACA
                                       INNER JOIN TIPO_VEHICULO TV ON V.ID_TIPO_VEHICULO_FK = TV.ID_TIPO_VEHICULO
                                       INNER JOIN PARQUEADERO P ON R.ID_PARQUEADERO_FK = P.ID_PARQUEADERO
                                       INNER JOIN PISO PS ON P.ID_PISO_FK = PS.ID_PISO
                                       INNER JOIN ESTADO E ON R.ID_ESTADO_FK = E.ID_ESTADO
                                       WHERE R.ID_PERSONA_FK_FK = ?
                                         AND (E.DESCRIPCION_ESTADO = 'Reservado' OR E.DESCRIPCION_ESTADO = 'Ocupado')
                                       ORDER BY R.HORA_RESERVA";

                            $stmt = $conn->prepare($sqlReservasActivas);
                            $stmt->bind_param("i", $id_persona);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Calcular la hora de expiración (15 minutos después de HORA_RESERVA)
                                    $horaReserva = strtotime($row['HORA_RESERVA']);
                                    $horaExpiracion = $horaReserva + 15 * 60; // 15 minutos en segundos
                                    $horaExpiracionFormatted = date("Y-m-d H:i:s", $horaExpiracion); // Formato legible

                                    echo "<tr data-hora-expiracion='{$horaExpiracionFormatted}'>";
                                    echo "<td>{$row['PLACA']}</td>";
                                    echo "<td>{$row['tipo_vehiculo']}</td>";
                                    echo "<td>{$row['espacio']}</td>";
                                    echo "<td>{$row['DESCRIPCION_ESTADO']}</td>";
                                    echo "<td class='temporizador'>Calculando...</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No hay reservas activas.</td></tr>";
                            }

                            $stmt->close();
                        } else {
                            echo "<tr><td colspan='5'>Error: No se encontró el ID de persona en sesión.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>

        <!-- Sección Estado del Parqueadero por Piso -->

        <div class="content" id="content">
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
                                        <?php if ($espacio['estado'] === 'Disponible'): ?>
                                            <form action="reservar_espacio.php" method="POST">
                                                <input type="hidden" name="id_parqueadero" value="<?= $espacio['ID_PARQUEADERO'] ?>">
                                                <button type="submit" class="btn btn-primary btn-sm mt-2">Reservar</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="vehiculoModal" tabindex="-1" aria-labelledby="vehiculoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vehiculoModalLabel">Registrar Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="vehiculoForm" action="insertar_vehiculo.php" method="POST">
                        <div class="mb-3">
                            <label for="placa" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="placa" name="placa" required pattern="[A-Z]{3}[0-9]{3}" placeholder="Ej: ABC123">
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="color" name="color" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_vehiculo" class="form-label">Tipo de Vehículo</label>
                            <select class="form-select" id="tipo_vehiculo" name="tipo_vehiculo" required>
                                <option value="2">Carro</option>
                                <option value="1">Moto</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </form>
                </div>
            </div>
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

        function selectPlaca(placa) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'placa';
            input.value = placa;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        function generarReporte() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'generar_reporte_pdf.php';
            form.target = '_blank';
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function startCountdown() {
            document.querySelectorAll('tr[data-hora-expiracion]').forEach(function(row) {
                const horaExpiracion = new Date(row.getAttribute('data-hora-expiracion')).getTime();
                const temporizador = row.querySelector('.temporizador');

                const interval = setInterval(function() {
                    const tiempoRestante = horaExpiracion - new Date().getTime(); // Diferencia en milisegundos

                    if (tiempoRestante <= 0) {
                        clearInterval(interval);
                        temporizador.innerText = "0:00";
                    } else {
                        const minutos = Math.floor((tiempoRestante / 1000) / 60) % 60;
                        const segundos = Math.floor((tiempoRestante / 1000) % 60);
                        temporizador.innerText = `${minutos < 10 ? '0' : ''}${minutos}:${segundos < 10 ? '0' : ''}${segundos}`;
                    }
                }, 1000);
            });
        }

        window.onload = startCountdown;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>