<?php
session_start();
$rutaBase     = "";
$paginaActual = "index.php";

$autenticado   = isset($_SESSION["usuario_id"]);
$nombreUsuario = $autenticado ? (string) ($_SESSION["nombre"] ?? "Usuario") : "";
$rolUsuario    = $autenticado ? (string) ($_SESSION["rol"] ?? "") : "";

$nombreFerreteria = "Ferretería Central";

// Helper de permisos: el Admin tiene todos los módulos y cada usuario
// solo los que le fueron asignados.
if (!function_exists("tienePermiso")) {
    require_once __DIR__ . "/model/permisos.php";
}

// El menú lateral y el botón hamburguesa solo se muestran con sesión activa.
$ocultarSidebar  = !$autenticado;

// Módulos reales del sistema: se muestran como accesos rápidos.
// "clave" es el nombre del permiso que exige cada módulo.
$modulos = [
    [
        "icono"  => "fa-cash-register",
        "titulo" => "Ventas",
        "texto"  => "Registra la salida de mercadería y controla la caja del día.",
        "url"    => "view/ventas.php",
        "clave"  => "ventas",
    ],
    [
        "icono"  => "fa-boxes-stacked",
        "titulo" => "Productos",
        "texto"  => "Catálogo con precios, categorías y existencias actualizadas.",
        "url"    => "view/productos.php",
        "clave"  => "productos",
    ],
    [
        "icono"  => "fa-cart-shopping",
        "titulo" => "Compras",
        "texto"  => "Ingresa mercadería y actualiza el stock automáticamente.",
        "url"    => "view/compras.php",
        "clave"  => "compras",
    ],
    [
        "icono"  => "fa-users",
        "titulo" => "Clientes",
        "texto"  => "Información de tus compradores para un mejor servicio.",
        "url"    => "view/clientes.php",
        "clave"  => "clientes",
    ],
    [
        "icono"  => "fa-truck-fast",
        "titulo" => "Proveedores",
        "texto"  => "Contactos y suministros de toda tu cadena de abastecimiento.",
        "url"    => "view/proveedores.php",
        "clave"  => "proveedores",
    ],
    [
        "icono"  => "fa-chart-line",
        "titulo" => "Reportes",
        "texto"  => "Stock, ventas y kardex listos para revisar o exportar.",
        "url"    => "view/dashboard.php",
        "clave"  => "dashboard",
    ],
];

$categorias = [
    ["fa-screwdriver-wrench", "Herramientas"],
    ["fa-bolt", "Tornillería"],
    ["fa-plug", "Eléctrico"],
    ["fa-faucet-drip", "Plomería"],
    ["fa-paint-roller", "Pintura"],
    ["fa-hard-hat", "Seguridad"],
];

$beneficios = [
    "Roles y permisos por tipo de usuario",
    "Alertas visuales de stock bajo",
    "Kardex con el historial de movimientos",
    "Exportación a CSV y reportes en PDF",
];

$enlacePanel = $autenticado ? "view/dashboard.php" : "view/login.php";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Sistema de gestión para ferretería: inventario, ventas, compras, proveedores y reportes." />
        <meta name="author" content="Sistemas de Información Avanzada — UAE" />
        <title><?= $nombreFerreteria ?> — Sistema de Gestión</title>

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

        <style>
            /* ============================================================
               Tema Ferretería — solo para la página de inicio
               Paleta: acero oscuro + amarillo de seguridad + concreto
               ============================================================ */
            .ferro-body {
                --ferro-ink: #101820;
                --ferro-steel: #16222f;
                --ferro-amber: #f5a524;
                --ferro-amber-light: #ffc247;
                --ferro-amber-dark: #b45309;
                --ferro-text: #3b4a59;
                --ferro-muted: #64748b;
                --ferro-line: #e3e9ef;
                --ferro-bg: #eef1f4;
                background-color: var(--ferro-bg);
            }

            .ferro-body #layoutSidenav_content {
                background-color: var(--ferro-bg);
            }

            /* Portada sin menú lateral: el contenido ocupa todo el ancho */
            .ferro-sin-sidebar #layoutSidenav #layoutSidenav_content {
                margin-left: 0;
                padding-left: 0;
            }

            .ferro-page {
                font-family: "Barlow", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
                color: var(--ferro-ink);
                padding-top: 1.5rem;
                padding-bottom: 0;
            }

            .ferro-page a {
                text-decoration: none;
            }

            /* ---------- HERO ---------- */
            .ferro-hero {
                position: relative;
                overflow: hidden;
                border-radius: 22px;
                padding: clamp(2rem, 4vw, 3.5rem);
                background:
                    radial-gradient(900px 420px at 88% 8%, rgba(245, 165, 36, 0.22), transparent 62%),
                    radial-gradient(620px 320px at 6% 96%, rgba(56, 189, 248, 0.10), transparent 62%),
                    linear-gradient(135deg, #111a24 0%, #16222f 55%, #0c141c 100%);
                box-shadow: 0 26px 50px -28px rgba(15, 23, 32, 0.65);
            }

            .ferro-hero::before {
                content: "";
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(255, 255, 255, 0.045) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.045) 1px, transparent 1px);
                background-size: 46px 46px;
                -webkit-mask-image: radial-gradient(85% 85% at 72% 18%, #000 25%, transparent 100%);
                mask-image: radial-gradient(85% 85% at 72% 18%, #000 25%, transparent 100%);
                pointer-events: none;
            }

            /* Franja de peligro en la base del hero */
            .ferro-hero::after {
                content: "";
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                height: 9px;
                background: repeating-linear-gradient(
                    -45deg,
                    var(--ferro-amber) 0 14px,
                    #0c141c 14px 28px
                );
                opacity: 0.9;
            }

            .ferro-hero > .row {
                position: relative;
                z-index: 1;
            }

            .ferro-eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                padding: 0.42rem 0.9rem;
                border-radius: 999px;
                background: rgba(245, 165, 36, 0.12);
                border: 1px solid rgba(245, 165, 36, 0.38);
                color: #ffd27a;
                font-size: 0.75rem;
                font-weight: 600;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }

            .ferro-title {
                margin: 0.9rem 0 1rem;
                font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
                font-weight: 700;
                font-size: clamp(2.4rem, 4.4vw, 4.05rem);
                line-height: 0.96;
                letter-spacing: 0.01em;
                text-transform: uppercase;
                color: #ffffff;
            }

            .ferro-title span {
                color: var(--ferro-amber-light);
            }

            .ferro-lead {
                max-width: 34rem;
                margin-bottom: 1.6rem;
                color: #b9c6d3;
                font-size: clamp(1rem, 1.2vw, 1.12rem);
                line-height: 1.6;
            }

            .ferro-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .ferro-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                padding: 0.8rem 1.35rem;
                border-radius: 0.7rem;
                border: 1px solid transparent;
                font-weight: 700;
                font-size: 0.95rem;
                letter-spacing: 0.01em;
                transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease, border-color 0.22s ease;
            }

            .ferro-btn--primary {
                background: linear-gradient(135deg, var(--ferro-amber-light), var(--ferro-amber));
                color: #1a1206;
                box-shadow: 0 12px 26px -12px rgba(245, 158, 11, 0.95);
            }

            .ferro-btn--primary:hover {
                color: #1a1206;
                transform: translateY(-2px);
                box-shadow: 0 18px 32px -14px rgba(245, 158, 11, 1);
            }

            .ferro-btn--ghost {
                color: #e8eef4;
                background: rgba(255, 255, 255, 0.05);
                border-color: rgba(255, 255, 255, 0.25);
            }

            .ferro-btn--ghost:hover {
                color: #ffffff;
                background: rgba(255, 255, 255, 0.1);
                border-color: rgba(255, 255, 255, 0.45);
                transform: translateY(-2px);
            }

            .ferro-hero__meta {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem 1.4rem;
                margin-top: 1.75rem;
                padding-top: 1.25rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .ferro-hero__meta span {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                color: #cbd5df;
                font-size: 0.85rem;
            }

            .ferro-hero__meta i {
                color: var(--ferro-amber-light);
            }

            .ferro-session {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                margin-top: 1.15rem;
                padding: 0.5rem 0.9rem;
                border-radius: 999px;
                background: rgba(25, 135, 84, 0.14);
                border: 1px solid rgba(52, 211, 153, 0.4);
                color: #b6f2d3;
                font-size: 0.84rem;
            }

            .ferro-session strong {
                color: #ffffff;
                font-weight: 600;
            }

            /* ---------- GRID DE HERRAMIENTAS ---------- */
            .ferro-toolbox {
                position: relative;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 13px;
                width: 100%;
                max-width: 360px;
                margin: 0 auto;
                padding-bottom: 44px;
            }

            .ferro-tool {
                display: grid;
                place-items: center;
                aspect-ratio: 1 / 1;
                border-radius: 18px;
                background: linear-gradient(145deg, rgba(255, 255, 255, 0.10), rgba(255, 255, 255, 0.03));
                border: 1px solid rgba(255, 255, 255, 0.13);
                color: #ffd27a;
                font-size: clamp(1.4rem, 3vw, 1.85rem);
                transition: transform 0.28s ease, border-color 0.28s ease, background 0.28s ease, color 0.28s ease;
            }

            .ferro-tool:hover {
                transform: translateY(-4px) rotate(-2deg);
                border-color: rgba(245, 165, 36, 0.55);
                background: rgba(245, 165, 36, 0.12);
                color: #ffd27a;
            }

            .ferro-tool--feature {
                background: linear-gradient(145deg, var(--ferro-amber-light), var(--ferro-amber));
                border-color: transparent;
                color: #1a1206;
                box-shadow: 0 14px 28px -14px rgba(245, 158, 11, 0.9);
            }

            .ferro-float {
                position: absolute;
                left: -8px;
                bottom: 0;
                display: flex;
                align-items: center;
                gap: 0.65rem;
                padding: 0.7rem 0.95rem;
                border-radius: 14px;
                background: #ffffff;
                box-shadow: 0 20px 42px -18px rgba(0, 0, 0, 0.7);
            }

            .ferro-float i {
                color: #0f9d58;
                font-size: 1.15rem;
            }

            .ferro-float strong {
                display: block;
                color: #0f1720;
                font-size: 0.82rem;
                line-height: 1.15;
            }

            .ferro-float small {
                color: #64748b;
                font-size: 0.72rem;
            }

            /* ---------- FRANJA DE CAPACIDADES ---------- */
            .ferro-strip {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1px;
                margin-top: 1.35rem;
                background: var(--ferro-line);
                border: 1px solid var(--ferro-line);
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 14px 30px -24px rgba(15, 23, 32, 0.55);
            }

            .ferro-strip__item {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                padding: 1.15rem 1.25rem;
                background: #ffffff;
                transition: background 0.2s ease;
            }

            .ferro-strip__item:hover {
                background: #fffdf7;
            }

            .ferro-strip__icon {
                display: grid;
                place-items: center;
                flex: 0 0 auto;
                width: 42px;
                height: 42px;
                border-radius: 12px;
                background: #fff6e3;
                border: 1px solid #ffe6b8;
                color: var(--ferro-amber-dark);
                font-size: 1rem;
            }

            .ferro-strip__item strong {
                display: block;
                color: var(--ferro-ink);
                font-size: 0.92rem;
                line-height: 1.2;
            }

            .ferro-strip__item small {
                color: var(--ferro-muted);
                font-size: 0.78rem;
            }

            /* ---------- SECCIONES ---------- */
            .ferro-section {
                margin-top: 2.75rem;
            }

            .ferro-kicker {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 0.35rem;
                color: var(--ferro-amber-dark);
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.14em;
                text-transform: uppercase;
            }

            .ferro-kicker::before {
                content: "";
                width: 22px;
                height: 3px;
                border-radius: 3px;
                background: var(--ferro-amber);
            }

            .ferro-h2 {
                margin-bottom: 0.4rem;
                font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
                font-weight: 700;
                font-size: clamp(1.7rem, 3vw, 2.35rem);
                line-height: 1.05;
                letter-spacing: 0.01em;
                text-transform: uppercase;
                color: var(--ferro-ink);
            }

            .ferro-sub {
                max-width: 42rem;
                margin-bottom: 1.4rem;
                color: var(--ferro-muted);
                font-size: 0.95rem;
            }

            /* ---------- TARJETAS DE MÓDULOS ---------- */
            .ferro-card {
                position: relative;
                display: flex;
                flex-direction: column;
                gap: 0.7rem;
                height: 100%;
                padding: 1.4rem;
                overflow: hidden;
                border: 1px solid var(--ferro-line);
                border-radius: 16px;
                background: #ffffff;
                color: inherit;
                box-shadow: 0 1px 2px rgba(16, 24, 32, 0.04);
                transition: transform 0.24s ease, border-color 0.24s ease, box-shadow 0.24s ease;
            }

            .ferro-card::before {
                content: "";
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                width: 4px;
                background: linear-gradient(var(--ferro-amber-light), var(--ferro-amber));
                opacity: 0;
                transition: opacity 0.24s ease;
            }

            .ferro-card:hover {
                transform: translateY(-4px);
                border-color: #f3c977;
                box-shadow: 0 20px 36px -24px rgba(15, 23, 32, 0.55);
                color: inherit;
            }

            .ferro-card:hover::before {
                opacity: 1;
            }

            .ferro-card__icon {
                display: grid;
                place-items: center;
                width: 48px;
                height: 48px;
                border-radius: 13px;
                background: #fff6e3;
                border: 1px solid #ffe6b8;
                color: var(--ferro-amber-dark);
                font-size: 1.15rem;
            }

            .ferro-card__title {
                margin: 0;
                color: var(--ferro-ink);
                font-size: 1.05rem;
                font-weight: 700;
            }

            .ferro-card__text {
                margin: 0;
                color: var(--ferro-muted);
                font-size: 0.9rem;
                line-height: 1.5;
            }

            .ferro-card__link {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                margin-top: auto;
                padding-top: 0.35rem;
                color: var(--ferro-amber-dark);
                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.09em;
                text-transform: uppercase;
            }

            .ferro-card__link i {
                transition: transform 0.24s ease;
            }

            .ferro-card:hover .ferro-card__link i {
                transform: translateX(4px);
            }

            /* Tarjeta de módulo sin permiso */
            .ferro-card--locked {
                background: #f7f9fb;
                cursor: not-allowed;
            }

            .ferro-card--locked:hover {
                transform: none;
                border-color: var(--ferro-line);
                box-shadow: 0 1px 2px rgba(16, 24, 32, 0.04);
            }

            .ferro-card--locked:hover::before {
                opacity: 0;
            }

            .ferro-card--locked .ferro-card__icon {
                background: #eef1f5;
                border-color: #e2e8ef;
                color: #94a3b8;
            }

            .ferro-card__link--locked,
            .ferro-card--locked:hover .ferro-card__link--locked {
                color: #94a3b8;
            }

            .ferro-card--locked:hover .ferro-card__link i {
                transform: none;
            }

            /* ---------- CATEGORÍAS ---------- */
            .ferro-cat {
                display: flex;
                align-items: center;
                gap: 0.8rem;
                height: 100%;
                padding: 1rem 1.05rem;
                border: 1px solid var(--ferro-line);
                border-radius: 14px;
                background: #ffffff;
                color: #24313d;
                font-weight: 600;
                font-size: 0.92rem;
                transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
            }

            .ferro-cat:hover {
                transform: translateY(-3px);
                border-color: #f3c977;
                box-shadow: 0 16px 28px -22px rgba(15, 23, 32, 0.5);
            }

            .ferro-cat i {
                display: grid;
                place-items: center;
                flex: 0 0 auto;
                width: 38px;
                height: 38px;
                border-radius: 10px;
                background: #f4f6f8;
                color: var(--ferro-amber-dark);
                font-size: 0.98rem;
            }

            /* ---------- BLOQUE DE BENEFICIOS ---------- */
            .ferro-check {
                display: grid;
                gap: 0.75rem;
                margin: 1.1rem 0 1.6rem;
                padding: 0;
                list-style: none;
            }

            .ferro-check li {
                display: flex;
                align-items: flex-start;
                gap: 0.65rem;
                color: var(--ferro-text);
                font-size: 0.94rem;
            }

            .ferro-check i {
                margin-top: 0.15rem;
                color: var(--ferro-amber-dark);
            }

            .ferro-panel {
                position: relative;
                overflow: hidden;
                padding: 1.75rem;
                border-radius: 20px;
                background:
                    radial-gradient(520px 260px at 100% 0%, rgba(245, 165, 36, 0.18), transparent 65%),
                    linear-gradient(140deg, #16222f, #0e1720);
                box-shadow: 0 24px 44px -28px rgba(15, 23, 32, 0.7);
            }

            .ferro-panel__title {
                margin-bottom: 0.35rem;
                font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
                font-weight: 700;
                font-size: 1.6rem;
                letter-spacing: 0.02em;
                text-transform: uppercase;
                color: #ffffff;
            }

            .ferro-panel__sub {
                margin-bottom: 1.1rem;
                color: #9fb0c0;
                font-size: 0.85rem;
            }

            .ferro-panel__row {
                display: flex;
                align-items: flex-start;
                gap: 0.85rem;
                padding: 0.9rem 0;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
            }

            .ferro-panel__row > i,
            .ferro-panel__row > svg.svg-inline--fa {
                display: grid;
                place-items: center;
                flex: 0 0 auto;
                width: 38px;
                height: 38px;
                padding: 0.65rem;
                box-sizing: border-box;
                border-radius: 11px;
                background: rgba(245, 165, 36, 0.12);
                border: 1px solid rgba(245, 165, 36, 0.25);
                color: var(--ferro-amber-light);
                font-size: 0.95rem;
            }

            .ferro-panel__row strong {
                display: block;
                margin-bottom: 0.1rem;
                color: #ffffff;
                font-size: 0.92rem;
                font-weight: 600;
            }

            .ferro-panel__row span {
                color: #9fb0c0;
                font-size: 0.82rem;
                line-height: 1.4;
            }

            /* ---------- CTA FINAL ---------- */
            .ferro-cta {
                position: relative;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1.25rem;
                margin-top: 2.75rem;
                padding: 2.2rem;
                overflow: hidden;
                border-radius: 20px;
                background:
                    radial-gradient(600px 280px at 92% 20%, rgba(245, 165, 36, 0.22), transparent 65%),
                    linear-gradient(120deg, #16222f, #0d151d);
                box-shadow: 0 24px 44px -30px rgba(15, 23, 32, 0.75);
            }

            .ferro-cta::after {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 7px;
                background: repeating-linear-gradient(
                    -45deg,
                    var(--ferro-amber) 0 12px,
                    #0d151d 12px 24px
                );
                opacity: 0.85;
            }

            .ferro-cta__text {
                position: relative;
                z-index: 1;
            }

            .ferro-cta__text h2 {
                margin-bottom: 0.3rem;
                font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
                font-weight: 700;
                font-size: clamp(1.6rem, 2.6vw, 2.15rem);
                letter-spacing: 0.01em;
                text-transform: uppercase;
                color: #ffffff;
            }

            .ferro-cta__text p {
                margin: 0;
                color: #a9b8c6;
                font-size: 0.92rem;
            }

            .ferro-cta .ferro-btn {
                position: relative;
                z-index: 1;
            }

            /* ---------- FOOTER ---------- */
            .ferro-footer {
                margin-top: 2.75rem;
                padding: 2.5rem 0 1.25rem;
                background: #0f1720;
                color: #93a4b4;
            }

            .ferro-footer__brand {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                margin-bottom: 0.7rem;
                color: #ffffff;
                font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
                font-size: 1.45rem;
                font-weight: 700;
                letter-spacing: 0.03em;
                text-transform: uppercase;
            }

            .ferro-footer__brand i {
                color: var(--ferro-amber);
            }

            .ferro-footer p {
                max-width: 24rem;
                margin: 0;
                color: #8b9cad;
                font-size: 0.86rem;
                line-height: 1.6;
            }

            .ferro-footer h3 {
                margin-bottom: 0.75rem;
                color: #ffffff;
                font-size: 0.8rem;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }

            .ferro-footer ul {
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .ferro-footer li {
                margin-bottom: 0.45rem;
            }

            .ferro-footer a {
                color: #a9b8c6;
                font-size: 0.86rem;
                transition: color 0.2s ease;
            }

            .ferro-footer a:hover {
                color: var(--ferro-amber-light);
            }

            .ferro-footer__bottom {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 0.5rem;
                margin-top: 1.75rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                color: #7d8e9f;
                font-size: 0.78rem;
            }

            .ferro-footer__bottom strong {
                color: #c3cfda;
                font-weight: 600;
            }

            /* ---------- RESPONSIVE ---------- */
            @media (max-width: 991.98px) {
                .ferro-strip {
                    grid-template-columns: repeat(2, 1fr);
                }

                .ferro-toolbox {
                    margin-top: 2.25rem;
                }
            }

            @media (max-width: 575.98px) {
                .ferro-page {
                    padding-top: 1rem;
                }

                .ferro-body .sb-topnav .navbar-brand {
                    font-size: 1rem;
                }

                .ferro-body .sb-topnav .nav-link {
                    font-size: 0.82rem;
                    white-space: nowrap;
                }

                .ferro-strip {
                    grid-template-columns: 1fr;
                }

                .ferro-float {
                    display: none;
                }

                .ferro-cta {
                    padding: 1.6rem;
                }

                .ferro-panel {
                    padding: 1.35rem;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .ferro-page *,
                .ferro-footer * {
                    animation: none !important;
                    transition: none !important;
                }
            }

            .ferro-page a:focus-visible,
            .ferro-footer a:focus-visible {
                outline: 3px solid var(--ferro-amber);
                outline-offset: 3px;
                border-radius: 6px;
            }
        </style>
    </head>
    <body class="sb-nav-fixed ferro-body <?= $ocultarSidebar ? "ferro-sin-sidebar" : "" ?>">
        <?php include "partials/navbar.php"; ?>
        <div id="layoutSidenav">
            <?php if (empty($ocultarSidebar)): ?>
                <?php include "partials/sidebar.php"; ?>
            <?php endif; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="ferro-page container-fluid px-4">

                        <!-- ═════════════ HERO ═════════════ -->
                        <section class="ferro-hero">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-7">
                                    <span class="ferro-eyebrow">
                                        <i class="fas fa-screwdriver-wrench"></i>
                                        Sistema de gestión ferretero
                                    </span>

                                    <h1 class="ferro-title">
                                        Todo para tu obra,<br />
                                        <span>bajo control.</span>
                                    </h1>

                                    <p class="ferro-lead">
                                        Inventario, ventas, compras y proveedores en un solo lugar.
                                        Tu ferretería ordenada desde el mostrador hasta el reporte final.
                                    </p>

                                    <div class="ferro-actions">
                                        <?php if ($autenticado): ?>
                                            <a href="view/dashboard.php" class="ferro-btn ferro-btn--primary">
                                                Ir al panel <i class="fas fa-arrow-right"></i>
                                            </a>
                                            <a href="#modulos" class="ferro-btn ferro-btn--ghost">
                                                Explorar módulos
                                            </a>
                                        <?php else: ?>
                                            <a href="view/login.php" class="ferro-btn ferro-btn--primary">
                                                <i class="fas fa-lock"></i> Iniciar sesión
                                            </a>
                                            <a href="#modulos" class="ferro-btn ferro-btn--ghost">
                                                Conocer el sistema <i class="fas fa-arrow-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($autenticado): ?>
                                        <div class="ferro-session">
                                            <i class="fas fa-circle-check"></i>
                                            <span>
                                                Sesión activa como <strong><?= htmlspecialchars($nombreUsuario) ?></strong>
                                                <?php if ($rolUsuario !== ""): ?>
                                                    (<?= htmlspecialchars($rolUsuario) ?>)
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="ferro-hero__meta">
                                        <span><i class="fas fa-warehouse"></i> Control de stock</span>
                                        <span><i class="fas fa-cash-register"></i> Ventas diarias</span>
                                        <span><i class="fas fa-file-export"></i> Reportes PDF y CSV</span>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="ferro-toolbox" aria-hidden="true">
                                        <div class="ferro-tool"><i class="fas fa-hammer"></i></div>
                                        <div class="ferro-tool ferro-tool--feature"><i class="fas fa-screwdriver-wrench"></i></div>
                                        <div class="ferro-tool"><i class="fas fa-bolt"></i></div>
                                        <div class="ferro-tool"><i class="fas fa-paint-roller"></i></div>
                                        <div class="ferro-tool"><i class="fas fa-plug"></i></div>
                                        <div class="ferro-tool"><i class="fas fa-wrench"></i></div>

                                        <div class="ferro-float">
                                            <i class="fas fa-circle-check"></i>
                                            <div>
                                                <strong>Inventario al día</strong>
                                                <small>Stock sincronizado</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- ═════════════ FRANJA DE CAPACIDADES ═════════════ -->
                        <div class="ferro-strip">
                            <div class="ferro-strip__item">
                                <div class="ferro-strip__icon"><i class="fas fa-warehouse"></i></div>
                                <div>
                                    <strong>Inventario en tiempo real</strong>
                                    <small>Existencias siempre visibles</small>
                                </div>
                            </div>
                            <div class="ferro-strip__item">
                                <div class="ferro-strip__icon"><i class="fas fa-receipt"></i></div>
                                <div>
                                    <strong>Ventas y compras</strong>
                                    <small>Movimientos registrados</small>
                                </div>
                            </div>
                            <div class="ferro-strip__item">
                                <div class="ferro-strip__icon"><i class="fas fa-users-gear"></i></div>
                                <div>
                                    <strong>Roles y permisos</strong>
                                    <small>Cada usuario ve lo suyo</small>
                                </div>
                            </div>
                            <div class="ferro-strip__item">
                                <div class="ferro-strip__icon"><i class="fas fa-file-export"></i></div>
                                <div>
                                    <strong>Reportes listos</strong>
                                    <small>PDF y CSV para decidir</small>
                                </div>
                            </div>
                        </div>

                        <!-- ═════════════ MÓDULOS ═════════════ -->
                        <section class="ferro-section" id="modulos">
                            <span class="ferro-kicker">Accesos rápidos</span>
                            <h2 class="ferro-h2">Los módulos de tu ferretería</h2>
                            <p class="ferro-sub">
                                Todo lo que necesitas para atender el mostrador y mantener el inventario bajo control.
                                <?php if (!$autenticado): ?>
                                    Inicia sesión para entrar a cada módulo.
                                <?php endif; ?>
                            </p>

                            <div class="row g-3 g-lg-4">
                                <?php foreach ($modulos as $modulo): ?>
                                    <?php
                                    // Sin sesión: todas las tarjetas llevan al login.
                                    // Con sesión: solo se puede abrir si tiene el permiso.
                                    $permitido = !$autenticado || tienePermiso($modulo["clave"]);
                                    ?>
                                    <div class="col-sm-6 col-xl-4">
                                        <?php if ($permitido): ?>
                                        <a class="ferro-card" href="<?= $autenticado ? $modulo["url"] : "view/login.php" ?>">
                                        <?php else: ?>
                                        <div class="ferro-card ferro-card--locked" aria-disabled="true">
                                        <?php endif; ?>
                                            <div class="ferro-card__icon">
                                                <i class="fas <?= $modulo["icono"] ?>"></i>
                                            </div>
                                            <h3 class="ferro-card__title"><?= htmlspecialchars($modulo["titulo"]) ?></h3>
                                            <p class="ferro-card__text"><?= htmlspecialchars($modulo["texto"]) ?></p>
                                            <?php if ($permitido): ?>
                                                <span class="ferro-card__link">
                                                    <?= $autenticado ? "Abrir módulo" : "Iniciar sesión" ?>
                                                    <i class="fas fa-arrow-right"></i>
                                                </span>
                                            <?php else: ?>
                                                <span class="ferro-card__link ferro-card__link--locked">
                                                    <i class="fas fa-lock"></i> No tiene permiso
                                                </span>
                                            <?php endif; ?>
                                        <?php if ($permitido): ?>
                                        </a>
                                        <?php else: ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>

                        <!-- ═════════════ CATEGORÍAS ═════════════ -->
                        <section class="ferro-section">
                            <span class="ferro-kicker">Surtido</span>
                            <h2 class="ferro-h2">Todo para tu obra</h2>
                            <p class="ferro-sub">
                                Organiza tus productos por categorías y encuentra cada artículo sin perder tiempo en el mostrador.
                            </p>

                            <div class="row g-3">
                                <?php foreach ($categorias as $categoria): ?>
                                    <div class="col-6 col-md-4 col-xl-2">
                                        <div class="ferro-cat">
                                            <i class="fas <?= $categoria[0] ?>"></i>
                                            <span><?= htmlspecialchars($categoria[1]) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>

                        <!-- ═════════════ BENEFICIOS ═════════════ -->
                        <section class="ferro-section">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-6">
                                    <span class="ferro-kicker">Hecho para el mostrador</span>
                                    <h2 class="ferro-h2">Una ferretería ordenada vende más</h2>
                                    <p class="ferro-sub">
                                        El sistema centraliza la información del negocio para que puedas
                                        decidir con datos y no con suposiciones.
                                    </p>

                                    <ul class="ferro-check">
                                        <?php foreach ($beneficios as $beneficio): ?>
                                            <li>
                                                <i class="fas fa-circle-check"></i>
                                                <span><?= htmlspecialchars($beneficio) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <a href="<?= $enlacePanel ?>" class="ferro-btn ferro-btn--primary">
                                        <?= $autenticado ? "Ver mi panel" : "Empezar ahora" ?>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>

                                <div class="col-lg-6">
                                    <div class="ferro-panel">
                                        <h3 class="ferro-panel__title">Control de punta a punta</h3>
                                        <p class="ferro-panel__sub">Del ingreso de mercadería a la venta final.</p>

                                        <div class="ferro-panel__row">
                                            <i class="fas fa-warehouse"></i>
                                            <div>
                                                <strong>Inventario centralizado</strong>
                                                <span>Productos, categorías y existencias en un solo catálogo.</span>
                                            </div>
                                        </div>
                                        <div class="ferro-panel__row">
                                            <i class="fas fa-right-left"></i>
                                            <div>
                                                <strong>Ventas y compras conectadas</strong>
                                                <span>Cada movimiento actualiza el stock automáticamente.</span>
                                            </div>
                                        </div>
                                        <div class="ferro-panel__row">
                                            <i class="fas fa-user-shield"></i>
                                            <div>
                                                <strong>Usuarios con permisos</strong>
                                                <span>Cada rol accede solo a los módulos que necesita.</span>
                                            </div>
                                        </div>
                                        <div class="ferro-panel__row">
                                            <i class="fas fa-file-lines"></i>
                                            <div>
                                                <strong>Reportes para decidir</strong>
                                                <span>Kardex, stock y ventas listos para exportar.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- ═════════════ CTA ═════════════ -->
                        <section class="ferro-cta">
                            <div class="ferro-cta__text">
                                <h2>Tu ferretería, siempre en orden</h2>
                                <p>
                                    <?= $autenticado
                                        ? "Continúa donde lo dejaste y revisa el estado de tu inventario."
                                        : "Ingresa al sistema y toma el control de tu inventario hoy mismo." ?>
                                </p>
                            </div>
                            <a href="<?= $enlacePanel ?>" class="ferro-btn ferro-btn--primary">
                                <i class="fas <?= $autenticado ? "fa-gauge-high" : "fa-lock" ?>"></i>
                                <?= $autenticado ? "Abrir panel" : "Iniciar sesión" ?>
                            </a>
                        </section>
                    </div>
                </main>

                <!-- ═════════════ FOOTER ═════════════ -->
                <footer class="ferro-footer">
                    <div class="container-fluid px-4">
                        <div class="row g-4">
                            <div class="col-lg-5">
                                <div class="ferro-footer__brand">
                                    <i class="fas fa-screwdriver-wrench"></i>
                                    <?= htmlspecialchars($nombreFerreteria) ?>
                                </div>
                                <p>
                                    Sistema de información para ferreterías: inventario, ventas,
                                    compras, clientes y proveedores en un solo panel.
                                </p>
                            </div>

                            <div class="col-6 col-lg-2">
                                <h3>Sistema</h3>
                                <ul>
                                    <li><a href="index.php">Inicio</a></li>
                                    <li><a href="<?= $enlacePanel ?>">Panel</a></li>
                                    <li><a href="view/login.php">Iniciar sesión</a></li>
                                </ul>
                            </div>

                            <div class="col-6 col-lg-2">
                                <h3>Módulos</h3>
                                <ul>
                                    <li><a href="<?= $autenticado ? "view/productos.php" : "view/login.php" ?>">Productos</a></li>
                                    <li><a href="<?= $autenticado ? "view/ventas.php" : "view/login.php" ?>">Ventas</a></li>
                                    <li><a href="<?= $autenticado ? "view/dashboard.php" : "view/login.php" ?>">Reportes</a></li>
                                </ul>
                            </div>

                            <div class="col-lg-3">
                                <h3>Atención</h3>
                                <ul>
                                    <li><i class="fas fa-location-dot me-2"></i>Guayaquil — Ecuador</li>
                                    <li><i class="fas fa-clock me-2"></i>Lun a Sáb · 08h00 – 18h00</li>
                                    <li><i class="fas fa-graduation-cap me-2"></i>Proyecto académico UAE</li>
                                </ul>
                            </div>
                        </div>

                        <div class="ferro-footer__bottom">
                            <span>&copy; <?= date("Y") ?> <strong><?= htmlspecialchars($nombreFerreteria) ?></strong> — Todos los derechos reservados.</span>
                            <span>Sistemas de Información Avanzada · Universidad Agraria del Ecuador</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
