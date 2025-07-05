<?php
require_once __DIR__ . '/../models/VentaDAO.php';

$formato = $_GET['formato'] ?? 'csv';
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;

$dao = new VentaDAO();

if ($fecha_inicio && $fecha_fin) {
    $ventas = $dao->obtenerVentasPorRango($fecha_inicio, $fecha_fin);
} else {
    $ventas = $dao->obtenerVentas();
}

// Exportar a CSV
if ($formato === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reporte_ventas.csv"');

    $salida = fopen('php://output', 'w');
    fputcsv($salida, ['ID', 'Fecha', 'Total']);

    foreach ($ventas as $v) {
        fputcsv($salida, [$v->id, $v->fecha, $v->total]);
    }

    fclose($salida);
    exit;
}

// Exportar a PDF
if ($formato === 'pdf') {
    require_once __DIR__ . '/../libs/fpdf/fpdf.php';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Reporte de Ventas', 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 10, 'ID', 1);
    $pdf->Cell(60, 10, 'Fecha', 1);
    $pdf->Cell(30, 10, 'Total', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    foreach ($ventas as $v) {
        $pdf->Cell(20, 10, $v['id'], 1);
        $pdf->Cell(60, 10, $v['fecha'], 1);
        $pdf->Cell(30, 10, '$' . number_format($v['total'], 2), 1);
        $pdf->Ln();
    }

    $pdf->Output('I', 'reporte_ventas.pdf');
    exit;
}
