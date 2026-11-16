<?php
// ===========================
// partials/navbar.php
// Sistemas de Información Avanzada — UAE
// ===========================
?>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ferro-brand ps-3 d-flex align-items-center gap-2" href="<?= $rutaBase ?>index.php">
        <i class="fas fa-screwdriver-wrench"></i> Ferretería Central
    </a>
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
        <?php if (isset($_SESSION["usuario_id"])): ?>
            <!-- Usuario CON sesión activa: mostramos su nombre y la opción de salir -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i> <?= htmlspecialchars($_SESSION["nombre"]) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= $rutaBase ?>view/password.php"><i class="fas fa-key fa-fw me-1"></i>Cambiar contraseña</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="<?= $rutaBase ?>controller/logout.php">Cerrar sesión</a></li>
                </ul>
            </li>
        <?php else: ?>
            <!-- Usuario SIN sesión: mostramos un botón directo para entrar -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $rutaBase ?>view/login.php">
                    <i class="fas fa-sign-in-alt me-1"></i> Iniciar sesión
                </a>
            </li>
        <?php endif; ?>
        <?php if (empty($ocultarSidebar)): ?>
            <li class="nav-item">
                <a class="nav-link ms-2" id="sidebarToggle" href="#" role="button" aria-label="Mostrar u ocultar menú">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
