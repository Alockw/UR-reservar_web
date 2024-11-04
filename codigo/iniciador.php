<?php

include 'bd_connector.php';

$conn = conectarDB();

$username = $_POST['login']; // datos del login
$password = $_POST['password'];


$sql = "SELECT ID_PERSONA, CONTRASEÑA, ID_TIPO_FK FROM PERSONA INNER JOIN USUARIO ON ID_USUARIO_FK = USUARIO.ID_USUARIO WHERE PASAPORTE_VIRTUAL = ?"; //datos bd
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();


if ($stmt->num_rows > 0) { // si user existe

    $stmt->bind_result($id_persona, $hashed_password, $id_tipo_fk); //guarda los datos
    $stmt->fetch();


    if (hash("sha256", $password) === $hashed_password) {    // Verificar la contraseña

        session_start();
        $_SESSION['id_persona'] = $id_persona; //guarda datos en sesion
        $_SESSION['id_tipo_fk'] = $id_tipo_fk;


        if ($id_tipo_fk == 1) {
            header("Location: menu_usuario.php");
        } elseif ($id_tipo_fk == 2) {
            header("Location: menu_admin.php");
        } else {
            header("Location: menu_personal.php");
        }
        exit();
    } else {

        echo "<script>alert('Pasaporte virtual o contraseña incorrectos'); window.location.href = 'index.html';</script>";
    }
} else {

    echo "<script>alert('Usuario no disponible, contacta con el administrador'); window.location.href = 'index.html';</script>";
}


$stmt->close();
$conn->close();
