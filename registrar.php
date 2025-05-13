<?php
include 'db.php';

$slot_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehiculo_id = $_POST['vehiculo_id'];
    $patente = $_POST['license_plate'];
    $marca = $_POST['brand'];

    $stmt = $conn->prepare("INSERT INTO parking_logs (slot_id, vehicle_type_id, start_time, license_plate, brand)
                            VALUES (?, ?, NOW(), ?, ?)");
    $stmt->bind_param("iiss", $slot_id, $vehiculo_id, $patente, $marca);
    $stmt->execute();
    $log_id = $stmt->insert_id;

    $stmt2 = $conn->prepare("UPDATE parking_slots SET is_occupied = 1, current_log_id = ? WHERE id = ?");
    $stmt2->bind_param("ii", $log_id, $slot_id);
    $stmt2->execute();

    header("Location: index.php");
    exit;
}

$tipos = $conn->query("SELECT * FROM vehicle_types");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Vehículo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Registrar vehículo en espacio <?= htmlspecialchars($slot_id) ?></h2>
    <form method="POST">
        <div class="mb-3">
            <label for="vehiculo_id" class="form-label">Tipo de vehículo</label>
            <select name="vehiculo_id" id="vehiculo_id" class="form-select" required>
                <?php while($tipo = $tipos->fetch_assoc()): ?>
                    <option value="<?= $tipo['id'] ?>"><?= $tipo['name'] ?> - $<?= $tipo['rate_per_hour'] ?>/hora</option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="license_plate" class="form-label">Patente</label>
            <input type="text" name="license_plate" id="license_plate" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="brand" class="form-label">Marca del vehículo</label>
            <input type="text" name="brand" id="brand" class="form-control" required>
        </div>

        <button class="btn btn-success">Registrar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
