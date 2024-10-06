<?php
session_start(); // Iniciar la sesión

include 'bd_connector.php'; // Archivo de conexión a la base de datos

$conn = conectarDB();

// Verificar si se ha seleccionado una placa
if (isset($_POST['placa'])) {
    $_SESSION['placa_seleccionada'] = $_POST['placa']; // Guardar la placa seleccionada en la sesión
}

$placaSeleccionada = isset($_SESSION['placa_seleccionada']) ? $_SESSION['placa_seleccionada'] : null;
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
        /* Altura de la barra superior */
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
        display: block;
    }

    .content {
        margin-top: 56px;
        padding: 20px;
        width: 100%;
        transition: margin-left 0.3s ease;
    }

    @media (min-width: 768px) {
        .sidebar {
            left: 0;
        }

        .sidebar.active~.content {
            margin-left: 0;
        }
    }

    .btn-primary {
        background-color: #d91616;
        border-color: #d91616;
    }

    .btn-primary:hover {
        background-color: #ad0a0a;
        border-color: #ad0a0a;
    }

    .dropdown-item.text-selected {
        background-color: #d91616;
        color: white;
    }

    .dropdown-item.selected {
        color: #d91616;
    }


    /* Control del espacio que deja el sidebar */
    .sidebar.active~.content {
        margin-left: 250px;
    }

    input[type="text"]:focus, select:focus {
        border-color: #d91616;
        /* Color rojo claro */
        box-shadow: 0 0 5px rgba(217, 22, 22, 0.5);
        /* Sombra para resaltar el borde */
    }

</style>
</head>

<body>
    <!-- Top bar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" onclick="toggleMenu()">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="menu_usuario.php">ParkEase UR - Usuario</a>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Perfil del usuario -->
        <div class="text-center">
            <img src="img/ico_man.png" alt="Foto de perfil" class="profile-img">
            <?php
            // Verificar si id_persona está definida en la sesión
            if (isset($_SESSION['id_persona'])) {
                $id_persona = $_SESSION['id_persona']; // Recuperar id_persona de la sesión

                // Consulta SQL para obtener nombre y apellido usando id_persona
                $sql = "SELECT nombre, apellido FROM PERSONA WHERE ID_PERSONA = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_persona); // Enlazar el parámetro id_persona
                $stmt->execute();
                $stmt->bind_result($nombre, $apellido); // Solo enlazar las columnas que estás seleccionando
                $stmt->fetch();

                // Mostrar el nombre y apellido si están disponibles
                if ($nombre && $apellido) {
                    echo "<p>$nombre</p>";
                    echo "<p>$apellido</p>";
                } else {
                    echo "<p>Nombre</p>";
                    echo "<p>Apellido</p>";
                }

                // Cerrar la declaración
                $stmt->close();
            } else {
                echo "No has iniciado sesión. Por favor, inicia sesión.";
                header("Location: login.php");
                exit();
            }
            ?>
        </div>

        <!-- Dropdown para los vehículos -->
        <div class="dropdown mt-3">
            <a class="dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Seleccionar vehículo
            </a>

            <ul class="dropdown-menu">
                <?php
                // Consulta para obtener los vehículos del usuario
                $queryVehiculos = "SELECT PLACA FROM VEHICULO_PERSONA INNER JOIN VEHICULO ON VEHICULO_PERSONA.ID_VEHICULO_FK = VEHICULO.ID_VEHICULO WHERE ID_PERSONA_FK= ?";
                $stmtVehiculos = $conn->prepare($queryVehiculos);
                $stmtVehiculos->bind_param("i", $id_persona);
                $stmtVehiculos->execute();
                $stmtVehiculos->store_result();
                $stmtVehiculos->bind_result($placa);

                if ($stmtVehiculos->num_rows > 0) {
                    while ($stmtVehiculos->fetch()) {
                        // Verificar si la placa está seleccionada
                        $selectedClass = ($placa === $placaSeleccionada) ? 'dropdown-item selected' : 'dropdown-item text-dark';
                        echo "<li><a class='$selectedClass' href='#' onclick='selectPlaca(\"$placa\")'>$placa</a></li>";
                    }
                } else {
                    echo "<li><a class='dropdown-item text-dark' href='#'>No hay vehículos. Añade uno nuevo.</a></li>";
                }

                $stmtVehiculos->close();
                ?>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-dark" href="#" data-bs-toggle="modal" data-bs-target="#vehiculoModal">Añadir nuevo vehículo</a></li>
            </ul>


        </div>

        <!-- Otras opciones del menú -->
        <nav>
            <a href="#">Generar Reporte</a>
            <a href="index.html">Salir</a>
        </nav>

        <!-- Copyright -->
        <footer class="text-center mt-auto">
            <p>&copy; 2024 ParkEase. Todos los derechos reservados.</p>
        </footer>
    </div>

    <!-- Dashboard content -->
    <div class="content" id="content">
        <div class="container mt-4">
            <p>aqui metemos la informacion del Dashboard</p>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        }

        function selectPlaca(placa) {
            // Enviar la placa seleccionada a través de POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; // Enviar a la misma página
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'placa';
            input.value = placa;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit(); // Enviar el formulario
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Modal para añadir vehículo -->
    <div class="modal fade" id="vehiculoModal" tabindex="-1" aria-labelledby="vehiculoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vehiculoModalLabel">Registrar Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='menu_usuario.php'"></button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownItems = document.querySelectorAll('.dropdown-item[data-bs-toggle="modal"]');
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(event) {
                    event.preventDefault();
                    const myModal = new bootstrap.Modal(document.getElementById('vehiculoModal'));
                    myModal.show();
                });
            });
        });
    </script>
</body>

</html>