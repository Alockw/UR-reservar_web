<?php
session_start();
include 'bd_connector.php';

$conn = conectarDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $placa = $_POST['placa'];
    $color = $_POST['color'];
    $tipo_vehiculo = $_POST['tipo_vehiculo'];
    $id_persona = $_SESSION['id_persona'];

    // Validación de la placa
    $queryValidarPlaca = "SELECT COUNT(*) FROM VEHICULO WHERE PLACA = ?";
    $stmtValidar = $conn->prepare($queryValidarPlaca);
    $stmtValidar->bind_param("s", $placa);
    $stmtValidar->execute();
    $stmtValidar->bind_result($existe);
    $stmtValidar->fetch();
    $stmtValidar->close();

    if ($existe > 0) {
        echo "<script>alert('La placa ya existe. Por favor, ingresa una placa diferente.'); window.history.back();</script>";
        exit();
    }

    // Contar vehículos para el usuario
    $queryContarVehiculos = "SELECT COUNT(*) FROM VEHICULO_PERSONA WHERE ID_PERSONA_FK = ?";
    $stmtContar = $conn->prepare($queryContarVehiculos);
    $stmtContar->bind_param("i", $id_persona);
    $stmtContar->execute();
    $stmtContar->bind_result($contador);
    $stmtContar->fetch();
    $stmtContar->close();

    if ($contador >= 3) {
        echo "<script>alert('Ya tienes 3 vehículos registrados. No puedes añadir más.'); window.history.back();</script>";
        exit();
    }

    // Insertar el nuevo vehículo
    $queryInsertarVehiculo = "INSERT INTO VEHICULO (PLACA, COLOR, ID_TIPO_VEHICULO_FK) VALUES (?, ?, ?)";
    $stmtInsertar = $conn->prepare($queryInsertarVehiculo);
    $stmtInsertar->bind_param("ssi", $placa, $color, $tipo_vehiculo);
    $stmtInsertar->execute();

    // Obtener el ID del vehículo insertado
    $id_vehiculo = $stmtInsertar->insert_id;

    // Asociar el vehículo con la persona
    $queryAsociarVehiculo = "INSERT INTO VEHICULO_PERSONA (ID_VEHICULO_FK, ID_PERSONA_FK) VALUES (?, ?)";
    $stmtAsociar = $conn->prepare($queryAsociarVehiculo);
    $stmtAsociar->bind_param("ii", $id_vehiculo, $id_persona);
    $stmtAsociar->execute();

    // Cerrar las declaraciones
    $stmtInsertar->close();
    $stmtAsociar->close();
    $conn->close();

    echo "<script>alert('Vehículo registrado exitosamente.'); window.location.href='menu_usuario.php';</script>";
    exit();
}
