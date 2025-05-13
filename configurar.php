<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['rate'] as $id => $valor) {
        $stmt = $conn->prepare("UPDATE vehicle_types SET rate_per_hour = ? WHERE id = ?");
        $stmt->bind_param("di", $valor, $id);
        $stmt->execute();
    }
    header("Location: configurar.php?success=1");
    exit;
}

$tipos = $conn->query("SELECT * FROM vehicle_types");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar Tarifas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Configurar Tarifas por Tipo de Vehículo</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Tarifas actualizadas correctamente.</div>
    <?php endif; ?>

    <form method="POST">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Vehículo</th>
                    <th>Tarifa por hora (en pesos)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tipo = $tipos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $tipo['name'] ?></td>
                        <td>
                            <input type="number" step="0.01" name="rate[<?= $tipo['id'] ?>]" value="<?= $tipo['rate_per_hour'] ?>" class="form-control" required>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button class="btn btn-primary">Guardar Cambios</button>
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </form>
</body>
</html>
