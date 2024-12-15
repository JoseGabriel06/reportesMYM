<?php
session_start(); // Inicia sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login/login.php'); // Redirige al login si no está autenticado
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes MYM</title>
</head>
<body>
    <h1>Holi</h1>
    <a href="login/logout.php">Cerrar Sesión</a>
</body>
</html>