<?php
session_start();
include 'bd_connector.php';

function existeReservaActiva($conn, $id_persona)
{
    $sql = "SELECT COUNT(*) FROM RESERVA WHERE ID_PERSONA_FK_FK = ? AND ID_ESTADO_FK = 3";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_persona);
    $stmt->execute();
    $stmt->bind_result($reservaActiva);
    $stmt->fetch();
    $stmt->close();
    return $reservaActiva > 0;
}

function espacioDisponible($conn, $id_parqueadero)
{
    $sql = "SELECT estado_actual FROM PARQUEADERO WHERE ID_PARQUEADERO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_parqueadero);
    $stmt->execute();
    $stmt->bind_result($estado_actual);
    $stmt->fetch();
    $stmt->close();
    return $estado_actual === 'DISPONIBLE';
}

function crearReserva($conn, $id_parqueadero, $id_persona, $placa)
{

    $sql2 = "SELECT ID_TIPO_VEHICULO_FK FROM VEHICULO WHERE PLACA = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $placa);
    $stmt2->execute();
    $stmt2->bind_result($id_tipo_vehiculo);
    $stmt2->fetch();
    $stmt2->close();

    $sql3 = "SELECT ID_TIPO_VEHICULO_FK FROM PARQUEADERO WHERE ID_PARQUEADERO = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $id_parqueadero);
    $stmt3->execute();
    $stmt3->bind_result($id_tipo_parqueadero);
    $stmt3->fetch();
    $stmt3->close();
    

    if ($id_tipo_vehiculo != $id_tipo_parqueadero) {

        echo "<script>alert('El tipo de vehiculo no coincide con el tipo de parqueadero'); window.location.href = 'menu_usuario.php';</script>";
        exit();
    } else {


        $sql = "INSERT INTO RESERVA (ID_PARQUEADERO_FK, ID_PERSONA_FK_FK, PLACA, ID_ESTADO_FK, HORA_RESERVA, HORA_EXPIRACION)
    VALUES (?, ?, ?, (SELECT ID_ESTADO FROM ESTADO WHERE DESCRIPCION_ESTADO = 'Reservado'), NOW(), ADDTIME(NOW(), '00:15:00'))";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iis", $id_parqueadero, $id_persona, $placa);
            $resultado = $stmt->execute();
            $stmt->close();
            return $resultado;
        }
    }


    return false;
}

function actualizarEstadoParqueadero($conn, $id_parqueadero)
{
    $sql = "UPDATE PARQUEADERO SET estado_actual = 'RESERVADO' WHERE ID_PARQUEADERO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_parqueadero);
    $resultado = $stmt->execute();
    $stmt->close();
    return $resultado;
}

if (isset($_SESSION['placa_seleccionada'], $_SESSION['id_persona'], $_POST['id_parqueadero'])) {
    $placa = $_SESSION['placa_seleccionada'];
    $id_persona = $_SESSION['id_persona'];
    $id_parqueadero = $_POST['id_parqueadero'];

    $conn = conectarDB();

    // Verificar si ya existe una reserva activa
    if (existeReservaActiva($conn, $id_persona)) {
        echo "<script>alert('Ya tienes una reserva activa en estado \"Reservado\". No puedes hacer otra reserva hasta que se libere'); window.location.href = 'menu_usuario.php';</script>";
        $conn->close();
        exit();
    }

    // Verificar si el espacio está disponible
    if (!espacioDisponible($conn, $id_parqueadero)) {
        echo "<script>alert('El espacio ya está reservado u ocupado.'); window.location.href = 'menu_usuario.php';</script>";
        $conn->close();
        exit();
    }

    // Crear la nueva reserva
    if (!crearReserva($conn, $id_parqueadero, $id_persona, $placa)) {
        echo "<script>alert('Error al crear la reserva.'); window.location.href = 'menu_usuario.php';</script>";
        $conn->close();
        exit();
    }

    // Actualizar el estado del parqueadero
    if (!actualizarEstadoParqueadero($conn, $id_parqueadero)) {
        echo "<script>alert('Error al actualizar estado del parqueadero.'); window.location.href = 'menu_usuario.php';</script>";
        $conn->close();
        exit();
    }

    // Reserva creada con éxito
    echo "<script>alert('Reserva creada con éxito.'); window.location.href = 'menu_usuario.php';</script>";
    $conn->close();
    exit();
} else {
    echo "<script>alert('Error: falta información para la reserva.'); window.location.href = 'menu_usuario.php';</script>";
    exit();
}
