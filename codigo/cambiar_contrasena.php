<?php
session_start();
include 'bd_connector.php'; // Archivo de conexión a la base de datos

$conn = conectarDB(); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_persona = $_SESSION['id_persona'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "Las nuevas contraseñas no coinciden.";
        exit();
    }

    // Verificar la contraseña actual
    $sql = "SELECT U.contraseña FROM USUARIO U INNER JOIN PERSONA P ON P.ID_USUARIO_FK = U.ID_USUARIO WHERE P.ID_PERSONA = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_persona);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!hash_equals($hashed_password, hash('sha256', $current_password))) {
        echo "<script>alert('La contraseña actual es incorrecta.'); window.location.href = 'index.html';</script>";
        exit();
    }

    // Actualizar la contraseña
    $new_hashed_password = hash('sha256', $new_password);
    $sql = "UPDATE USUARIO U INNER JOIN PERSONA P ON P.ID_USUARIO_FK = U.ID_USUARIO SET U.contraseña = ? WHERE P.ID_PERSONA = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_hashed_password, $id_persona);
    if ($stmt->execute()) {
        echo "<script>alert('Contraseña cambiada exitosamente.'); window.location.href = ' index.html';</script>";
        header("Location: index.html");
        exit();
    } else {
        echo "<script>alert('Error al cambiar la contraseña.'); window.location.href = 'index.html';</script>";
    }
    $stmt->close();
}

$conn->close();
