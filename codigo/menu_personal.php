<?php
session_start();
include 'bd_connector.php'; // Archivo de conexión a la base de datos

$conn = conectarDB(); // Conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkEase UR - Personal</title>
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
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" onclick="toggleMenu()">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="menu_personal.php">ParkEase UR - Personal</a>
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
            <a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Cambiar Contraseña</a>
            <a href="index.html">Salir</a>
        </nav>

        <footer class="text-center mt-auto">
            <p>&copy; 2024 ParkEase. Todos los derechos reservados.</p>
        </footer>
    </div>

    <!-- Dashboard content -->
    <div class="content" id="content">
        <div class="container mt-4">
            <h3>Consultar Reserva</h3>
            <form id="consultaReservaForm" action="menu_personal.php" method="POST">
                <div class="mb-3">
                    <label for="placa" class="form-label">Placa del Vehículo</label>
                    <input type="text" class="form-control" id="placa" name="placa" required>
                </div>
                <button type="submit" class="btn btn-primary">Consultar</button>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['placa'])) {
                $placa = strtolower($_POST['placa']); // Convert to lowercase for case-insensitive comparison
                $sql = "SELECT ID_RESERVA, ID_ESTADO_FK FROM RESERVA WHERE LOWER(PLACA) = ? AND (ID_ESTADO_FK = 2 OR ID_ESTADO_FK = 3)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $placa);
                $stmt->execute();
                $stmt->bind_result($id_reserva, $id_estado_fk);
                if ($stmt->fetch()) {
                    $nuevo_estado = ($id_estado_fk == 3) ? 2 : (($id_estado_fk == 2) ? 1 : $id_estado_fk);
                    $stmt->close(); // Close the statement before executing the next query

                    $sql_update = "UPDATE RESERVA SET ID_ESTADO_FK = ? WHERE ID_RESERVA = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("ii", $nuevo_estado, $id_reserva);
                    if ($stmt_update->execute()) {
                        echo "<div class='alert alert-success mt-3'>Estado de la reserva actualizado correctamente.</div>";
                    } else {
                        echo "<div class='alert alert-danger mt-3'>Error al actualizar el estado de la reserva.</div>";
                    }
                    $stmt_update->close();
                } else {

                    $stmt->close(); // Ensure the statement is closed in the else block as well
                }
            }
            ?>
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>