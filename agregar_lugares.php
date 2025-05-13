<?php
include 'db.php';

$tipos = $conn->query("SELECT * FROM vehicle_types");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['slot_name'];
    $tipo = $_POST['vehicle_type_id'];

    $stmt = $conn->prepare("INSERT INTO parking_slots (slot_name, vehicle_type_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $tipo);
    $stmt->execute();

    $mensaje = "Espacio agregado correctamente.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Espacio de Estacionamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Agregar nuevo lugar de estacionamiento</h2>

    <?php if (isset($mensaje)): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="slot_name" class="form-label">Nombre del espacio</label>
            <input type="text" name="slot_name" id="slot_name" class="form-control" required placeholder="Ej: Auto-5, Moto-2">
        </div>

        <div class="mb-3">
            <label for="vehicle_type_id" class="form-label">Tipo de vehículo</label>
            <select name="vehicle_type_id" id="vehicle_type_id" class="form-select" required>
                <?php while ($tipo = $tipos->fetch_assoc()): ?>
                    <option value="<?= $tipo['id'] ?>"><?= $tipo['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button class="btn btn-primary">Agregar espacio</button>
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </form>
</body>
</html>
