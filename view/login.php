<?php
// VIEW — login.php
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Si el usuario YA inició sesión, no tiene sentido ver el login de nuevo
session_start();
if (isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}

$msg     = $_GET["msg"]     ?? "";
$detalle = $_GET["detalle"] ?? "";

// Mensajes del sistema agrupados por tipo, icono y texto.
$alertas = [
    "error" => [
        "danger",
        "fa-circle-exclamation",
        $detalle,
    ],
    "registrado" => [
        "success",
        "fa-circle-check",
        "¡Cuenta creada! Ya puede iniciar sesión.",
    ],
    "logout" => [
        "info",
        "fa-right-from-bracket",
        "Sesión cerrada correctamente.",
    ],
    "password_actualizada" => [
        "success",
        "fa-key",
        "Contraseña actualizada. Ya puede iniciar sesión.",
    ],
    "inactividad" => [
        "warning",
        "fa-clock",
        "Su sesión se cerró por inactividad. Vuelva a iniciar sesión.",
    ],
    "no_autenticado" => [
        "warning",
        "fa-lock",
        "Debe iniciar sesión para continuar.",
    ],
];

$alerta = $alertas[$msg] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Acceso al sistema de gestión de Ferretería Central." />
        <title>Acceso — Ferretería Central</title>

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <link href="../css/styles.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" rel="stylesheet" />

        <style>
            /* ============================================================
               Login con identidad de ferretería
               ============================================================ */
            .ferro-auth {
                --ferro-ink: #101820;
                --ferro-steel: #16222f;
                --ferro-amber: #f5a524;
                --ferro-amber-light: #ffc247;
                --ferro-amber-dark: #b45309;
                --ferro-text: #3b4a59;
                --ferro-muted: #64748b;
                --ferro-line: #e3e9ef;
                --ferro-bg: #eef1f4;

                min-height: 100vh;
                margin: 0;
                background-color: var(--ferro-bg);
                color: var(--ferro-ink);
                font-family: "Barlow", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            }

            .ferro-auth a {
                text-decoration: none;
            }

            .ferro-auth__shell {
                display: grid;
                grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
                min-height: 100vh;
            }

            /* ---------- PANEL IZQUIERDO ---------- */
            .ferro-auth__brand {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 2rem;
                overflow: hidden;
                padding: clamp(1.75rem, 4vw, 3.25rem);
                background:
                    radial-gradient(820px 420px at 88% 10%, rgba(245, 165, 36, 0.22), transparent 62%),
                    radial-gradient(560px 320px at 4% 94%, rgba(56, 189, 248, 0.10), transparent 62%),
                    linear-gradient(140deg, #111a24 0%, #16222f 55%, #0c141c 100%);
            }

            .ferro-auth__brand::before {
                content: "";
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(255, 255, 255, 0.045) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.045) 1px, transparent 1px);
                background-size: 46px 46px;
                -webkit-mask-image: radial-gradient(90% 90% at 70% 20%, #000 25%, transparent 100%);
                mask-image: radial-gradient(90% 90% at 70% 20%, #000 25%, transparent 100%);
                pointer-events: none;
            }

            .ferro-auth__brand::after {
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

            .ferro-auth__brand > * {
                position: relative;
                z-index: 1;
            }

            .ferro-auth__logo {
                display: inline-flex;
                align-items: center;
                gap: 0.6rem;
                width: fit-content;
                color: #ffffff;
                font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: 0.03em;
                text-transform: uppercase;
            }

            .ferro-auth__logo i {
                color: var(--ferro-amber);
                font-size: 1.25rem;
            }

            .ferro-auth__eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.4rem 0.85rem;
                border-radius: 999px;
                background: rgba(245, 165, 36, 0.12);
                border: 1px solid rgba(245, 165, 36, 0.38);
                color: #ffd27a;
                font-size: 0.72rem;
                font-weight: 600;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }

            .ferro-auth__title {
                margin: 0.9rem 0 1rem;
                color: #ffffff;
                font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
                font-size: clamp(2.4rem, 4.4vw, 3.9rem);
                font-weight: 700;
                line-height: 0.96;
                letter-spacing: 0.01em;
                text-transform: uppercase;
            }

            .ferro-auth__title span {
                color: var(--ferro-amber-light);
            }

            .ferro-auth__text {
                max-width: 30rem;
                margin: 0;
                color: #b9c6d3;
                font-size: 1rem;
                line-height: 1.6;
            }

            .ferro-auth__points {
                display: grid;
                gap: 0.6rem;
                margin: 1.4rem 0 0;
                padding: 0;
                list-style: none;
            }

            .ferro-auth__points li {
                display: flex;
                align-items: center;
                gap: 0.55rem;
                color: #cbd5df;
                font-size: 0.9rem;
            }

            .ferro-auth__points i {
                color: var(--ferro-amber-light);
            }

            .ferro-auth__tools {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 1.9rem;
            }

            .ferro-auth__tool {
                display: grid;
                place-items: center;
                width: 54px;
                height: 54px;
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.07);
                border: 1px solid rgba(255, 255, 255, 0.13);
                color: #ffd27a;
                font-size: 1.15rem;
            }

            .ferro-auth__tool--feature {
                background: linear-gradient(145deg, var(--ferro-amber-light), var(--ferro-amber));
                border-color: transparent;
                color: #1a1206;
                box-shadow: 0 12px 26px -14px rgba(245, 158, 11, 0.9);
            }

            .ferro-auth__brand-foot {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 0.75rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                color: #7f93a5;
                font-size: 0.76rem;
            }

            /* ---------- PANEL DERECHO ---------- */
            .ferro-auth__panel {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2.5rem 1.5rem;
            }

            .ferro-auth__card {
                width: 100%;
                max-width: 430px;
                padding: clamp(1.5rem, 3vw, 2.3rem);
                border: 1px solid var(--ferro-line);
                border-radius: 20px;
                background: #ffffff;
                box-shadow: 0 32px 64px -42px rgba(15, 23, 32, 0.55);
            }

            .ferro-auth__back {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                margin-bottom: 1.1rem;
                color: var(--ferro-muted);
                font-size: 0.8rem;
                font-weight: 600;
                transition: color 0.2s ease;
            }

            .ferro-auth__back:hover {
                color: var(--ferro-amber-dark);
            }

            .ferro-auth__card h2 {
                margin: 0 0 0.3rem;
                color: var(--ferro-ink);
                font-family: "Barlow Condensed", "Arial Narrow", sans-serif;
                font-size: 2rem;
                font-weight: 700;
                line-height: 1.05;
                letter-spacing: 0.01em;
                text-transform: uppercase;
            }

            .ferro-auth__hint {
                margin: 0 0 1.35rem;
                color: var(--ferro-muted);
                font-size: 0.9rem;
                line-height: 1.5;
            }

            /* ---------- ALERTAS ---------- */
            .ferro-alert {
                display: flex;
                align-items: flex-start;
                gap: 0.6rem;
                margin-bottom: 1rem;
                padding: 0.8rem 0.95rem;
                border: 1px solid transparent;
                border-radius: 11px;
                font-size: 0.86rem;
                line-height: 1.45;
            }

            .ferro-alert i {
                margin-top: 0.12rem;
            }

            .ferro-alert--danger {
                background: #fef2f2;
                border-color: #fecaca;
                color: #991b1b;
            }

            .ferro-alert--success {
                background: #ecfdf5;
                border-color: #a7f3d0;
                color: #065f46;
            }

            .ferro-alert--info {
                background: #eff6ff;
                border-color: #bfdbfe;
                color: #1e40af;
            }

            .ferro-alert--warning {
                background: #fffbeb;
                border-color: #fde68a;
                color: #92400e;
            }

            /* ---------- FORMULARIO ---------- */
            .ferro-auth__field {
                margin-bottom: 1.05rem;
            }

            .ferro-auth__label {
                display: block;
                margin-bottom: 0.4rem;
                color: var(--ferro-text);
                font-size: 0.82rem;
                font-weight: 600;
            }

            .ferro-auth__control {
                position: relative;
            }

            .ferro-auth__control > i {
                position: absolute;
                top: 50%;
                left: 0.95rem;
                transform: translateY(-50%);
                color: #94a3b8;
                font-size: 0.9rem;
                pointer-events: none;
            }

            .ferro-auth__input {
                width: 100%;
                height: 50px;
                padding: 0 2.8rem 0 2.55rem;
                border: 1px solid #d8e0e8;
                border-radius: 11px;
                background: #fbfcfd;
                color: var(--ferro-ink);
                font-family: inherit;
                font-size: 0.95rem;
                transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            }

            .ferro-auth__input::placeholder {
                color: #9aa8b6;
            }

            .ferro-auth__input:focus {
                outline: none;
                border-color: var(--ferro-amber);
                background: #ffffff;
                box-shadow: 0 0 0 4px rgba(245, 165, 36, 0.18);
            }

            .ferro-auth__eye {
                position: absolute;
                top: 50%;
                right: 0.5rem;
                display: grid;
                place-items: center;
                width: 38px;
                height: 38px;
                transform: translateY(-50%);
                border: 0;
                border-radius: 8px;
                background: transparent;
                color: #7b8b9b;
                font-size: 0.9rem;
                cursor: pointer;
                transition: background 0.2s ease, color 0.2s ease;
            }

            .ferro-auth__eye:hover {
                background: #f1f5f9;
                color: #334155;
            }

            .ferro-auth__submit {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.55rem;
                width: 100%;
                height: 50px;
                margin-top: 1.35rem;
                border: 0;
                border-radius: 11px;
                background: linear-gradient(135deg, var(--ferro-amber-light), var(--ferro-amber));
                color: #1a1206;
                font-family: inherit;
                font-size: 0.98rem;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 14px 26px -14px rgba(245, 158, 11, 0.95);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .ferro-auth__submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 18px 32px -14px rgba(245, 158, 11, 1);
            }

            .ferro-auth__help {
                margin: 1.1rem 0 0;
                color: #7b8b9b;
                font-size: 0.8rem;
                line-height: 1.5;
                text-align: center;
            }

            .ferro-auth__legal {
                margin-top: 1.25rem;
                color: #8b9cad;
                font-size: 0.75rem;
                text-align: center;
            }

            .ferro-auth__legal strong {
                color: #64748b;
                font-weight: 600;
            }

            .ferro-auth__security {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.4rem;
                margin-top: 0.9rem;
                color: #94a3b8;
                font-size: 0.74rem;
            }

            .ferro-auth__security i {
                color: var(--ferro-amber-dark);
            }

            /* ---------- RESPONSIVE ---------- */
            @media (max-width: 991.98px) {
                .ferro-auth__shell {
                    grid-template-columns: 1fr;
                }

                .ferro-auth__brand {
                    justify-content: flex-start;
                    gap: 1rem;
                    padding: 1.5rem;
                }

                .ferro-auth__title {
                    font-size: clamp(2rem, 7vw, 2.6rem);
                    margin-bottom: 0.6rem;
                }

                .ferro-auth__text {
                    font-size: 0.92rem;
                }

                .ferro-auth__points,
                .ferro-auth__tools,
                .ferro-auth__brand-foot {
                    display: none;
                }

                .ferro-auth__panel {
                    justify-content: flex-start;
                    padding: 1.5rem 1rem 2rem;
                }

                .ferro-auth__card {
                    max-width: 520px;
                }
            }

            @media (max-width: 575.98px) {
                .ferro-auth__text {
                    display: none;
                }

                .ferro-auth__pitch {
                    margin-top: 0.4rem;
                }

                .ferro-auth__card {
                    padding: 1.35rem;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .ferro-auth * {
                    transition: none !important;
                }
            }

            .ferro-auth a:focus-visible,
            .ferro-auth button:focus-visible,
            .ferro-auth input:focus-visible {
                outline: 3px solid var(--ferro-amber);
                outline-offset: 2px;
            }
        </style>
    </head>
    <body class="ferro-auth">
        <main class="ferro-auth__shell">

            <!-- ═════════════ PANEL DE MARCA ═════════════ -->
            <section class="ferro-auth__brand">
                <a class="ferro-auth__logo" href="../index.php">
                    <i class="fas fa-screwdriver-wrench"></i> Ferretería Central
                </a>

                <div class="ferro-auth__pitch">
                    <span class="ferro-auth__eyebrow">
                        <i class="fas fa-screwdriver-wrench"></i>
                        Sistema de gestión ferretero
                    </span>

                    <h1 class="ferro-auth__title">
                        Tu ferretería,<br />
                        <span>bajo control.</span>
                    </h1>

                    <p class="ferro-auth__text">
                        Inventario, ventas, compras y reportes en un solo lugar.
                        Ingresa para continuar donde lo dejaste.
                    </p>

                    <ul class="ferro-auth__points">
                        <li><i class="fas fa-circle-check"></i> Control de stock en tiempo real</li>
                        <li><i class="fas fa-circle-check"></i> Ventas y compras conectadas</li>
                        <li><i class="fas fa-circle-check"></i> Reportes en PDF y CSV</li>
                    </ul>

                    <div class="ferro-auth__tools" aria-hidden="true">
                        <div class="ferro-auth__tool"><i class="fas fa-hammer"></i></div>
                        <div class="ferro-auth__tool ferro-auth__tool--feature"><i class="fas fa-wrench"></i></div>
                        <div class="ferro-auth__tool"><i class="fas fa-bolt"></i></div>
                        <div class="ferro-auth__tool"><i class="fas fa-paint-roller"></i></div>
                    </div>
                </div>

                <div class="ferro-auth__brand-foot">
                    <span>Acceso exclusivo para personal autorizado</span>
                    <span>Sistemas de Información Avanzada · UAE</span>
                </div>
            </section>

            <!-- ═════════════ FORMULARIO ═════════════ -->
            <section class="ferro-auth__panel">
                <div class="ferro-auth__card">
                    <a class="ferro-auth__back" href="../index.php">
                        <i class="fas fa-arrow-left"></i> Volver al inicio
                    </a>

                    <h2>Acceso al sistema</h2>
                    <p class="ferro-auth__hint">
                        Ingresa con tu correo y contraseña para administrar tu ferretería.
                    </p>

                    <?php if ($alerta): ?>
                        <div class="ferro-alert ferro-alert--<?= $alerta[0] ?>" role="alert" aria-live="polite">
                            <i class="fas <?= $alerta[1] ?>"></i>
                            <span><?= htmlspecialchars($alerta[2]) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- VIEW → envía datos al CONTROLLER -->
                    <form action="../controller/procesar_login.php" method="POST">
                        <div class="ferro-auth__field">
                            <label class="ferro-auth__label" for="email">Correo electrónico</label>
                            <div class="ferro-auth__control">
                                <i class="fas fa-envelope"></i>
                                <input
                                    class="ferro-auth__input"
                                    id="email"
                                    name="email"
                                    type="email"
                                    placeholder="correo@ejemplo.com"
                                    autocomplete="username"
                                    required
                                />
                            </div>
                        </div>

                        <div class="ferro-auth__field">
                            <label class="ferro-auth__label" for="password">Contraseña</label>
                            <div class="ferro-auth__control">
                                <i class="fas fa-lock"></i>
                                <input
                                    class="ferro-auth__input"
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="Ingrese su contraseña"
                                    autocomplete="current-password"
                                    required
                                />
                                <button
                                    type="button"
                                    class="ferro-auth__eye"
                                    data-toggle-password
                                    aria-label="Mostrar contraseña"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="ferro-auth__submit">
                            Iniciar sesión <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>

                    <p class="ferro-auth__help">
                        ¿Olvidó su contraseña? Solicite ayuda al administrador.
                    </p>

                    <div class="ferro-auth__security">
                        <i class="fas fa-shield-halved"></i>
                        Conexión para personal autorizado
                    </div>
                </div>

                <p class="ferro-auth__legal">
                    &copy; <?= date("Y") ?> <strong>Ferretería Central</strong> · Universidad Agraria del Ecuador
                </p>
            </section>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script>
            // Mostrar / ocultar la contraseña
            const passwordInput = document.getElementById("password");
            const passwordToggle = document.querySelector("[data-toggle-password]");

            if (passwordInput && passwordToggle) {
                passwordToggle.addEventListener("click", () => {
                    const mostrar = passwordInput.type === "password";
                    passwordInput.type = mostrar ? "text" : "password";
                    passwordToggle.setAttribute(
                        "aria-label",
                        mostrar ? "Ocultar contraseña" : "Mostrar contraseña"
                    );
                    passwordToggle.querySelector("i").className = mostrar
                        ? "fas fa-eye-slash"
                        : "fas fa-eye";
                });
            }
        </script>
    </body>
</html>
