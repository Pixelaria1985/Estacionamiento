<?php
include 'db.php';

$fecha = $_GET['fecha'] ?? date('Y-m-d');

// Obtener registros de esa fecha
$sql = "SELECT pl.*, ps.slot_name, vt.name AS tipo
        FROM parking_logs pl
        JOIN parking_slots ps ON pl.slot_id = ps.id
        JOIN vehicle_types vt ON pl.vehicle_type_id = vt.id
        WHERE DATE(pl.start_time) = ? AND pl.end_time IS NOT NULL
        ORDER BY pl.start_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $fecha);
$stmt->execute();
$res = $stmt->get_result();

$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial por Día</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Consultar Historial por Fecha</h2>

    <form method="GET" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="fecha" class="form-label">Seleccionar fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?= $fecha ?>" required>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Buscar</button>
                <a href="index.php" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </form>

    <?php if ($res->num_rows > 0): ?>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Espacio</th>
                <th>Tipo</th>
                <th>Patente</th>
                <th>Marca</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Pagado</th>
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
    <h4>Total recaudado el <?= $fecha ?>: <strong>$<?= number_format($total, 2) ?></strong></h4>
    <?php else: ?>
        <div class="alert alert-warning">No se encontraron registros para el día <strong><?= $fecha ?></strong>.</div>
    <?php endif; ?>
</body>
</html>
