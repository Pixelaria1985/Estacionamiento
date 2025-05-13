<?php
include 'db.php';

// Obtener los registros del día actual (que ya salieron y pagaron)
$sql = "SELECT pl.*, ps.slot_name, vt.name AS tipo
        FROM parking_logs pl
        JOIN parking_slots ps ON pl.slot_id = ps.id
        JOIN vehicle_types vt ON pl.vehicle_type_id = vt.id
        WHERE DATE(pl.start_time) = CURDATE() AND pl.end_time IS NOT NULL
        ORDER BY pl.start_time DESC";

$res = $conn->query($sql);
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial del Día</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Historial de Estacionamiento - Hoy</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Espacio</th>
                <th>Tipo de Vehículo</th>
                <th>Patente</th>
                <th>Marca</th>
                <th>Hora de Entrada</th>
                <th>Hora de Salida</th>
                <th>Monto Pagado</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $res->fetch_assoc()): 
                $total += $row['amount_paid'];
            ?>
                <tr>
                    <td><?= $row['slot_name'] ?></td>
                    <td><?= $row['tipo'] ?></td>
                    <td><?= htmlspecialchars($row['license_plate']) ?></td>
                    <td><?= htmlspecialchars($row['brand']) ?></td>
                    <td><?= $row['start_time'] ?></td>
                    <td><?= $row['end_time'] ?></td>
                    <td>$<?= number_format($row['amount_paid'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h4>Total Recaudado Hoy: <strong>$<?= number_format($total, 2) ?></strong></h4>

    <a href="index.php" class="btn btn-secondary mt-3">Volver al Mapa</a>
    <a href="historial_por_dia.php" class="btn btn-outline-dark mt-3">Consultar otro día</a>

</body>
</html>
