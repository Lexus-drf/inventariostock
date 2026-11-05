<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Error · InventarioStock</title>
<link rel="stylesheet" href="assets/vendor/bootstrap/bootstrap.min.css"></head>
<body class="bg-light d-grid" style="min-height:100vh;place-items:center">
<div class="card shadow-sm" style="max-width:520px"><div class="card-body p-4">
    <h1 class="h4">No se pudo completar la operación</h1>
    <p class="mb-3"><?= htmlspecialchars($mensaje ?? 'Error interno.', ENT_QUOTES, 'UTF-8') ?></p>
    <a class="btn btn-primary" href="index.php">Volver al inicio</a>
</div></div>
</body></html>
