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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/menuPrincipal.css">
    <title>Reportes MYM</title>
</head>
<body>
<nav id="sidebar">
    <ul>
      <li>
        <span class="logo">Distruidora MYM</span>
        <button onclick=toggleSidebar() id="toggle-btn">
        <i class='bx bx-chevrons-left' ></i>
        </button>
      </li>
      <li class="active">
        <a href="index.php">
        <i class='bx bx-home'></i>
          <span>Inicio</span>
        </a>
      </li>
        <button onclick=toggleSubMenu(this) class="dropdown-btn">
        <i class='bx bxs-report'></i>
          <span>Reporte</span>
          <i class='bx bx-chevron-down'></i>
        </button>
        <ul class="sub-menu">
          <div>
            <li><a href="#">Ventas</a></li>
          </div>
        </ul>
      </li>
    
      <li class="log_out">
        <a href="login/logout.php">
        <i class='bx bx-log-out'></i>
          <span>Cerrar Sesión</span>
        </a>
      </li>
    </ul>
  </nav>
  <main>
  </main>
    <script src="js/sidebar.js"></script>
</body>
</html>