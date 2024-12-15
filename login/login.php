<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-form">
        <form action="" method="POST">
            <h2>Iniciar Sesión</h2>
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
</body>
</html>
<?php
session_start(); // Inicia sesión

// Activa el reporte de errores para depurar
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Si ya está logueado, redirige al index.php
if (isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost"; 
    $username = "root"; 
    $password = "MyG4b0QL2023**@##"; 
    $database = "db_mymsa"; 

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        echo "<script>alertify.error('Error al conectar con la base de datos');</script>";
        exit;
    }

    $user = $_POST['username'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM adm_usuario 
            WHERE usuario = ? AND AES_DECRYPT(clave, '$0fTM1M') = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "<script>alertify.error('Error en la consulta SQL');</script>";
        exit;
    }

    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['nombre'] = $row['nombres'];

        echo "<script>
            alertify.success('Inicio de sesión exitoso. Bienvenido " . $row['nombres'] . "');
            setTimeout(() => { window.location.href = '../index.php'; }, 2000);
        </script>";
    } else {
        echo "<script>alertify.error('Usuario o contraseña incorrectos');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
