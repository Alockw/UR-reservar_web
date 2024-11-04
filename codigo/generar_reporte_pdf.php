<?php
session_start();
require('fpdf/fpdf.php');
include 'bd_connector.php';

class PDF extends FPDF
{
    function Header()
    {
        global $nombreUsuario, $apellidoUsuario, $numeroReporte;

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('Reporte de uso de ParkEase UR'), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, utf8_decode("Usuario: $nombreUsuario $apellidoUsuario | Reporte N°: $numeroReporte"), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function UserReservationsTable($header, $data)
    {
        $this->SetFont('Arial', 'B', 12);
        foreach ($header as $col) {
            $this->Cell(40, 10, utf8_decode($col), 1);
        }
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            foreach ($row as $col) {
                $this->Cell(40, 10, utf8_decode($col), 1);
            }
            $this->Ln();
        }
    }
}

try {
    $conn = conectarDB();
    $mesActual = date("m");
    $anioActual = date("Y");

    $id_persona = $_SESSION['id_persona'];
    $sqlUsuario = "SELECT nombre, apellido FROM PERSONA WHERE ID_PERSONA = ?";
    $stmtUsuario = $conn->prepare($sqlUsuario);
    $stmtUsuario->bind_param("i", $id_persona);
    $stmtUsuario->execute();
    $stmtUsuario->bind_result($nombreUsuario, $apellidoUsuario);
    $stmtUsuario->fetch();
    $stmtUsuario->close();

    $numeroReporte = rand(1000, 9999);

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(0, 10, utf8_decode("Estadísticas del mes: $mesActual/$anioActual"), 0, 1);
    $pdf->Ln(5);

    // Total de reservas del usuario en el mes actual
    $sqlReservas = "SELECT COUNT(*) AS total_reservas 
                    FROM RESERVA 
                    WHERE MONTH(HORA_RESERVA) = ? AND YEAR(HORA_RESERVA) = ? AND ID_PERSONA_FK_FK = ?";
    $stmt = $conn->prepare($sqlReservas);
    $stmt->bind_param("iii", $mesActual, $anioActual, $id_persona);
    $stmt->execute();
    $stmt->bind_result($totalReservas);
    $stmt->fetch();
    $stmt->close();

    $pdf->Cell(0, 10, utf8_decode("Total de reservas en el mes: $totalReservas"), 0, 1);
    $pdf->Ln(5);

    // Vehículos del usuario más usados en el mes actual
    $sqlVehiculos = "SELECT V.PLACA, COALESCE(COUNT(R.ID_RESERVA), 0) AS uso
FROM VEHICULO_PERSONA VP
INNER JOIN VEHICULO V ON VP.ID_VEHICULO_FK = V.ID_VEHICULO
LEFT JOIN RESERVA R ON R.PLACA = V.PLACA 
                    AND R.ID_PERSONA_FK_FK = VP.ID_PERSONA_FK
                    AND MONTH(R.HORA_RESERVA) = ? 
                    AND YEAR(R.HORA_RESERVA) = ?
WHERE VP.ID_PERSONA_FK = ?
GROUP BY V.PLACA
ORDER BY uso DESC
LIMIT 5;
";
    $stmtVehiculos = $conn->prepare($sqlVehiculos);
    $stmtVehiculos->bind_param("iii", $mesActual, $anioActual, $id_persona);
    $stmtVehiculos->execute();
    $resultVehiculos = $stmtVehiculos->get_result();

    $pdf->Cell(0, 10, utf8_decode("Vehículos más usados:"), 0, 1);
    while ($row = $resultVehiculos->fetch_assoc()) {
        $pdf->Cell(0, 10, utf8_decode("Placa: " . $row['PLACA'] . " - Uso: " . $row['uso'] . " reservas"), 0, 1);
    }
    $pdf->Ln(5);
    $stmtVehiculos->close();

    // Distribución de uso por tipo de vehículo para el usuario en el mes actual
    $sqlTipos = "SELECT TV.DESCRIPCION_TIPO_VEHICULO, COALESCE(COUNT(R.ID_RESERVA), 0) AS total
FROM VEHICULO_PERSONA VP
INNER JOIN VEHICULO V ON VP.ID_VEHICULO_FK = V.ID_VEHICULO
INNER JOIN TIPO_VEHICULO TV ON V.ID_TIPO_VEHICULO_FK = TV.ID_TIPO_VEHICULO
LEFT JOIN RESERVA R ON R.PLACA = V.PLACA 
                    AND R.ID_PERSONA_FK_FK = VP.ID_PERSONA_FK
                    AND MONTH(R.HORA_RESERVA) = ? 
                    AND YEAR(R.HORA_RESERVA) = ?
WHERE VP.ID_PERSONA_FK = ?
GROUP BY TV.DESCRIPCION_TIPO_VEHICULO;
";
    $stmtTipos = $conn->prepare($sqlTipos);
    $stmtTipos->bind_param("iii", $mesActual, $anioActual, $id_persona);
    $stmtTipos->execute();
    $resultTipos = $stmtTipos->get_result();

    $pdf->Cell(0, 10, utf8_decode("Distribución de uso por tipo de vehículo:"), 0, 1);
    while ($row = $resultTipos->fetch_assoc()) {
        $pdf->Cell(0, 10, utf8_decode($row['DESCRIPCION_TIPO_VEHICULO'] . ": " . $row['total'] . " reservas"), 0, 1);
    }
    $pdf->Ln(10);
    $stmtTipos->close();

    // Reservas detalladas del usuario
    $reservasUsuario = [];
    $sqlReservasUsuario = "SELECT R.HORA_RESERVA, 
       R.HORA_EXPIRACION, 
       P.LOCACION, 
       V.PLACA
FROM VEHICULO_PERSONA VP
INNER JOIN VEHICULO V ON VP.ID_VEHICULO_FK = V.ID_VEHICULO
INNER JOIN RESERVA R ON R.PLACA = V.PLACA 
                    AND R.ID_PERSONA_FK_FK = VP.ID_PERSONA_FK
                    AND MONTH(R.HORA_RESERVA) = ? 
                    AND YEAR(R.HORA_RESERVA) = ?
INNER JOIN PARQUEADERO P ON R.ID_PARQUEADERO_FK = P.ID_PARQUEADERO
WHERE VP.ID_PERSONA_FK = ?
ORDER BY R.HORA_RESERVA;
";
    $stmtReservasUsuario = $conn->prepare($sqlReservasUsuario);
    $stmtReservasUsuario->bind_param("iii", $mesActual, $anioActual, $id_persona);
    $stmtReservasUsuario->execute();
    $resultReservasUsuario = $stmtReservasUsuario->get_result();

    while ($row = $resultReservasUsuario->fetch_assoc()) {
        $reservasUsuario[] = [$row['HORA_RESERVA'], $row['HORA_EXPIRACION'], $row['LOCACION'], $row['PLACA']];
    }
    $stmtReservasUsuario->close();

    if (empty($reservasUsuario)) {
        $reservasUsuario[] = ['Sin datos', 'Sin datos', 'Sin datos', 'Sin datos'];
    }

    $header = ["Fecha y Hora", "Expiración", "Ubicación", "Placa"];
    $pdf->UserReservationsTable($header, $reservasUsuario);

    $conn->close();
    $pdf->Output("D", "Reporte_Parqueadero_{$mesActual}_{$anioActual}_N{$numeroReporte}.pdf");
} catch (Exception $e) {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo generar el reporte: {$e->getMessage()}',
                confirmButtonText: 'Aceptar'
            });
          </script>";
}
