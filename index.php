<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estacionamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .slot {
            width: 100px;
            height: 100px;
            margin: 10px;
            font-weight: bold;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            color: white;
            border-radius: 8px;
        }
        .green { background-color: green; }
        .red { background-color: red; }
    </style>
</head>
<body class="container mt-4">
    <h2>Mapa del Estacionamiento</h2>

    <?php
    // Consultar los espacios ordenados por tipo de vehículo
    $sql = "SELECT ps.id, ps.slot_name, ps.is_occupied, vt.name AS tipo
            FROM parking_slots ps
            JOIN vehicle_types vt ON ps.vehicle_type_id = vt.id
            ORDER BY FIELD(vt.name, 'Auto', 'Camioneta', 'Moto', 'Bicicleta'), ps.slot_name";

    $res = $conn->query($sql);
    $vehiculos = [
        'Auto' => [],
        'Camioneta' => [],
        'Moto' => [],
        'Bicicleta' => []
    ];

    // Agrupar los lugares por tipo de vehículo
    while ($row = $res->fetch_assoc()) {
        $vehiculos[$row['tipo']][] = $row;
    }

    // Mostrar los lugares por tipo
    foreach ($vehiculos as $tipo => $lugares):
        if (count($lugares) > 0):
    ?>
        <h4><?= $tipo ?>s</h4>
        <div class="d-flex flex-wrap">
            <?php foreach ($lugares as $lugar): ?>
                <a href="<?= $lugar['is_occupied'] ? "liberar.php?id={$lugar['id']}" : "registrar.php?id={$lugar['id']}" ?>" style="text-decoration: none;">
                    <div class="slot <?= $lugar['is_occupied'] ? 'red' : 'green' ?>">
                        <?= $lugar['slot_name'] ?><br><small><?= $lugar['tipo'] ?></small>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; endforeach; ?>

    <a href="configurar.php" class="btn btn-primary mt-3">Configurar Tarifas</a>
    <a href="historial.php" class="btn btn-secondary mt-3">Ver Historial</a>
    <a href="agregar_lugares.php" class="btn btn-outline-primary mt-3">Agregar Lugar</a>
</body>
</html>
