<?php // Vista: pago simulado con tarjeta. ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago de Adeudo</title>
    <link rel="stylesheet" href="/Portal_Web_Pagos_UTSC/public/css/estilos.css">
    <link rel="stylesheet" href="/Portal_Web_Pagos_UTSC/public/css/pago.css">
</head>
<body class="payment-page">
    <?php require_once __DIR__ . '/../partials/navbar_alumno.php'; ?>
    <main id="payment-screen" class="payment-screen">
        <header class="payment-header">
            <img src="/Portal_Web_Pagos_UTSC/public/img/logo.png" alt="Universidad Tecnológica Santa Catarina" class="payment-logo">
            <div>
                <h1>Pago de Adeudo</h1>
                <p>Ingresa los datos de tu tarjeta para confirmar el pago</p>
            </div>
        </header>

        <div class="payment-layout">
            <section class="payment-summary-card" aria-labelledby="payment-summary-title">
                <h2 id="payment-summary-title">Resumen del pago</h2>
                <dl class="payment-details">
                    <div class="payment-detail-row">
                        <dt>Alumno</dt>
                        <dd><?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="payment-detail-row">
                        <dt>Matrícula</dt>
                        <dd><?= htmlspecialchars($alumno['matricula'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="payment-detail-row">
                        <dt>Periodo</dt>
                        <dd><?= htmlspecialchars($periodoFormateado, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="payment-detail-row">
                        <dt>Fecha límite actual</dt>
                        <dd><?= htmlspecialchars($fechaLimiteTexto, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <?php if ($prorrogaVigente): ?>
                        <div class="payment-detail-row payment-extension-note"><dt>Prórroga</dt><dd>Autorizada hasta el <?= htmlspecialchars($fechaLimiteTexto, ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <?php endif; ?>
                    <div class="payment-detail-row">
                        <dt>Mensualidad</dt>
                        <dd>$<?= number_format((float) $adeudo['mensualidad'], 2) ?></dd>
                    </div>
                    <div class="payment-detail-row">
                        <dt>Aportación TSU</dt>
                        <dd>$<?= number_format((float) $adeudo['aportacion_tsu'], 2) ?></dd>
                    </div>
                    <div class="payment-detail-row">
                        <dt>Atraso</dt>
                        <dd>$<?= number_format((float) ($adeudo['atraso'] ?? 0), 2) ?></dd>
                    </div>
                    <div class="payment-detail-row">
                        <dt>Recargo</dt>
                        <dd>$<?= number_format((float) $adeudo['recargo'], 2) ?></dd>
                    </div>
                    <div class="payment-detail-row payment-total">
                        <dt>Total a pagar</dt>
                        <dd>$<?= number_format((float) $adeudo['total'], 2) ?></dd>
                    </div>
                </dl>
            </section>

            <section class="payment-method-card" aria-labelledby="payment-method-title">
                <h2 id="payment-method-title">Método de pago</h2>
                <p class="payment-method-name">Tarjeta de débito o crédito</p>
                <p class="payment-warning">Pago simulado con fines académicos. Ingresa cualquier número de 16 dígitos. No utilices datos de una tarjeta real.</p>

                <?php if ($message !== ''): ?>
                    <p class="payment-error" role="alert"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <form class="payment-card-form" id="payment-card-form" method="post" action="<?= BASE_URL ?>/index.php?ruta=pago" novalidate>
                    <input type="hidden" name="metodo_pago" value="Tarjeta">

                    <label for="nombre-titular">Nombre del titular</label>
                    <input type="text" id="nombre-titular" name="nombre_titular" value="<?= htmlspecialchars($nombreTitular, ENT_QUOTES, 'UTF-8') ?>" maxlength="100" autocomplete="cc-name" required>

                    <label for="numero-tarjeta">Número de tarjeta</label>
                    <input type="text" id="numero-tarjeta" name="numero_tarjeta" inputmode="numeric" autocomplete="cc-number" maxlength="19" placeholder="0000 0000 0000 0000" required>

                    <div class="payment-card-row">
                        <div>
                            <label for="mes-vencimiento">Mes</label>
                            <select id="mes-vencimiento" name="mes_vencimiento" autocomplete="cc-exp-month" required>
                                <option value="">MM</option>
                                <?php for ($mes = 1; $mes <= 12; $mes++): ?>
                                    <option value="<?= $mes ?>"><?= str_pad((string) $mes, 2, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label for="anio-vencimiento">Año</label>
                            <select id="anio-vencimiento" name="anio_vencimiento" autocomplete="cc-exp-year" required>
                                <option value="">AAAA</option>
                                <?php foreach ($aniosVencimiento as $anio): ?>
                                    <option value="<?= $anio ?>"><?= $anio ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="cvv">CVV</label>
                            <div class="payment-cvv-field">
                                <input type="password" id="cvv" name="cvv" inputmode="numeric" autocomplete="cc-csc" minlength="3" maxlength="4" pattern="[0-9]{3,4}" required>
                                <button type="button" id="payment-cvv-toggle" aria-label="Mostrar CVV" aria-pressed="false">Mostrar</button>
                            </div>
                        </div>
                    </div>

                    <button class="payment-submit" id="payment-submit" type="submit">Confirmar pago</button>
                    <a class="payment-return" href="<?= BASE_URL ?>/index.php?ruta=alumno">Regresar</a>
                </form>
            </section>
        </div>
    </main>
    <script src="/Portal_Web_Pagos_UTSC/public/js/pago.js"></script>
</body>
</html>
