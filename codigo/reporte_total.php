<?php
require('fpdf/fpdf.php');
include 'bd_connector.php';

$conn = conectarDB();

// Obtener reservas activas
$sqlReservasActivas = "SELECT V.PLACA, TV.DESCRIPCION_TIPO_VEHICULO, CONCAT(PS.DESCRIPCION_PISO, P.LOCACION) AS espacio, E.DESCRIPCION_ESTADO, R.HORA_RESERVA
                       FROM RESERVA R
                       INNER JOIN VEHICULO V ON R.PLACA = V.PLACA
                       INNER JOIN TIPO_VEHICULO TV ON V.ID_TIPO_VEHICULO_FK = TV.ID_TIPO_VEHICULO
                       INNER JOIN PARQUEADERO P ON R.ID_PARQUEADERO_FK = P.ID_PARQUEADERO
                       INNER JOIN PISO PS ON P.ID_PISO_FK = PS.ID_PISO
                       INNER JOIN ESTADO E ON R.ID_ESTADO_FK = E.ID_ESTADO
                       WHERE R.ID_ESTADO_FK IN (2, 3)
                       ORDER BY R.HORA_RESERVA";
$resultReservas = $conn->query($sqlReservasActivas);

// Obtener estado del parqueadero
$sqlParqueaderos = "SELECT P.ID_PARQUEADERO, P.LOCACION, P.ID_PISO_FK, TV.DESCRIPCION_TIPO_VEHICULO, P.estado_actual
                    FROM PARQUEADERO P
                    INNER JOIN TIPO_VEHICULO TV ON P.ID_TIPO_VEHICULO_FK = TV.ID_TIPO_VEHICULO
                    ORDER BY P.ID_PISO_FK, TV.DESCRIPCION_TIPO_VEHICULO, P.LOCACION";
$resultParqueaderos = $conn->query($sqlParqueaderos);

$parqueaderos = [];
$totalEspacios = ['moto' => 0, 'carro' => 0];
$ocupados = ['moto' => 0, 'carro' => 0];

if ($resultParqueaderos && $resultParqueaderos->num_rows > 0) {
    while ($row = $resultParqueaderos->fetch_assoc()) {
        $tipoVehiculo = strtolower($row['DESCRIPCION_TIPO_VEHICULO']);
        $totalEspacios[$tipoVehiculo]++;
        if (in_array(strtolower($row['estado_actual']), ['ocupado', 'reservado'])) {
            $ocupados[$tipoVehiculo]++;
        }
        $parqueaderos[$tipoVehiculo][] = [
            'locacion' => $row['LOCACION'],
            'estado' => ucfirst($row['estado_actual'])
        ];
    }
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título
$pdf->Cell(0, 10, 'Reporte Total', 0, 1, 'C');

// Reservas Activas
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Reservas Activas', 0, 1);
$pdf->SetFont('Arial', '', 10);

if ($resultReservas && $resultReservas->num_rows > 0) {
    while ($row = $resultReservas->fetch_assoc()) {
        $pdf->Cell(0, 10, "Placa: {$row['PLACA']} - Tipo: {$row['DESCRIPCION_TIPO_VEHICULO']} - Espacio: {$row['espacio']} - Estado: {$row['DESCRIPCION_ESTADO']}", 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No hay reservas activas.', 0, 1);
}

// Estado del Parqueadero
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Estado del Parqueadero', 0, 1);
$pdf->SetFont('Arial', 'B', 10);

foreach (['moto' => 'Motos', 'carro' => 'Carros'] as $tipo => $descripcion) {
    $pdf->Cell(0, 10, $descripcion, 0, 1);
    $pdf->SetFont('Arial', '', 10);

    if (isset($parqueaderos[$tipo])) {
        foreach ($parqueaderos[$tipo] as $espacio) {
            $pdf->Cell(0, 10, "Espacio: {$espacio['locacion']} - Estado: {$espacio['estado']}", 0, 1);
        }
    }

    $total = $totalEspacios[$tipo];
    $ocupado = $ocupados[$tipo];
    $disponible = $total - $ocupado;
    $porcentajeOcupacion = $total > 0 ? ($ocupado / $total) * 100 : 0;

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, "Total: $total - Ocupados: $ocupado - Disponibles: $disponible - Ocupacion: " . number_format($porcentajeOcupacion, 2) . "%", 0, 1);
    $pdf->SetFont('Arial', '', 10);
}

// Generar el PDF en una nueva pestaña y descargar automáticamente
$pdf->Output('D', 'reporte_total.pdf');
