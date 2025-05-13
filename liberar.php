<?php
include 'db.php';

$slot_id = $_GET['id'] ?? null;

// Obtener el log actual
$sql = "SELECT ps.slot_name, pl.id AS log_id, pl.start_time, pl.license_plate, pl.brand,
               vt.name AS tipo, vt.rate_per_hour
        FROM parking_slots ps
        JOIN parking_logs pl ON ps.current_log_id = pl.id
        JOIN vehicle_types vt ON pl.vehicle_type_id = vt.id
        WHERE ps.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $slot_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

if (!$data) {
    echo "No se encontró el registro para este espacio.";
    exit;
}

// Calcular monto actual
$start = new DateTime($data['start_time']);
$now = new DateTime();
$diff = $start->diff($now);
$hours = $diff->h + ($diff->days * 24);
if ($diff->i > 0 || $diff->s > 0) $hours += 1; // redondeo al alza

$monto_actual = $hours * $data['rate_per_hour'];

// Si se confirma el pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE parking_logs SET end_time = NOW(), amount_paid = ? WHERE id = ?");
    $stmt->bind_param("di", $monto_actual, $data['log_id']);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE parking_slots SET is_occupied = 0, current_log_id = NULL WHERE id = ?");
    $stmt->bind_param("i", $slot_id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Liberar espacio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Liberar espacio <?= $data['slot_name'] ?></h2>

    <p><strong>Tipo:</strong> <?= $data['tipo'] ?></p>
    <p><strong>Patente:</strong> <?= htmlspecialchars($data['license_plate']) ?></p>
    <p><strong>Marca:</strong> <?= htmlspecialchars($data['brand']) ?></p>
    <p><strong>Hora de entrada:</strong> <?= $data['start_time'] ?></p>
    <p><strong>Tarifa por hora:</strong> $<?= number_format($data['rate_per_hour'], 2) ?></p>
    <p><strong>Tiempo transcurrido:</strong> <?= $hours ?> hora(s)</p>
    <p><strong>Total a pagar hasta ahora:</strong> <span class="text-success">$<?= number_format($monto_actual, 2) ?></span></p>

    <form method="POST">
        <button class="btn btn-danger">Confirmar pago y liberar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
