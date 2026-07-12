<?php
$adminCssPath = __DIR__ . '/../../../../public/css/admin.css';
$adminJsPath = __DIR__ . '/../../../../public/js/admin.js';
$adminCssVersion = file_exists($adminCssPath) ? (string) filemtime($adminCssPath) : (string) time();
$adminJsVersion = file_exists($adminJsPath) ? (string) filemtime($adminJsPath) : (string) time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar aclaración</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css?v=<?= rawurlencode($adminCssVersion) ?>">
</head>
<body class="admin-page">
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<div class="admin-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
    <main class="admin-main">
        <header class="admin-clarification-page-header">
            <h1>Registrar aclaración</h1>
            <p>Selecciona al alumno y relaciona, si corresponde, su adeudo o pago.</p>
        </header>

        <div class="admin-extension-form-card admin-clarification-form-card">
        <form class="admin-clarification-form-content" method="post" action="<?= BASE_URL ?>/index.php?ruta=admin-aclaracion-guardar">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="admin-clarification-field">
                <label for="clarification_student_search">Buscar alumno</label>
                <div class="admin-clarification-search">
                <input
                    id="clarification_student_search"
                    class="admin-clarification-search-input"
                    type="search"
                    role="combobox"
                    aria-autocomplete="list"
                    aria-expanded="false"
                    aria-controls="clarification_student_results"
                    autocomplete="off"
                    placeholder="Escribe matrícula, nombre o correo"
                >
                <div id="clarification_student_results" class="admin-clarification-results" role="listbox" hidden></div>
                </div>
                <input id="clarification_student_id" type="hidden" name="id_alumno" value="">
            </div>

            <section id="clarification_student_summary" class="admin-extension-summary admin-clarification-student-summary" aria-live="polite">
                <h2>Información del alumno</h2>
                <p id="clarification_student_empty" class="admin-extension-summary-empty admin-clarification-student-empty">Busca y selecciona un alumno para consultar su información.</p>
                <dl id="clarification_student_data" class="admin-extension-summary-grid admin-clarification-student-grid" hidden>
                    <div><dt>Matrícula</dt><dd id="admin-clarification-summary-matricula"></dd></div>
                    <div><dt>Nombre</dt><dd id="admin-clarification-summary-name"></dd></div>
                    <div><dt>Correo</dt><dd id="admin-clarification-summary-email"></dd></div>
                </dl>
            </section>

            <div class="admin-clarification-field-grid">
                <label class="admin-clarification-field">Tipo de aclaración
                    <select id="admin-clarification-type" name="tipo" required disabled>
                        <option value="">Selecciona</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="admin-clarification-field">Asunto
                    <input id="admin-clarification-subject" name="asunto" maxlength="150" required disabled>
                </label>
            </div>

            <div class="admin-clarification-description">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" minlength="10" maxlength="2000" rows="5" placeholder="Describe claramente la situación que requiere aclaración." required disabled></textarea>
            </div>

            <div class="admin-clarification-related-grid">
                <label class="admin-clarification-field">Adeudo relacionado (opcional)
                    <select id="admin-clarification-debt" name="id_adeudo" disabled><option value="">Selecciona primero un alumno</option></select>
                </label>
                <label class="admin-clarification-field">Pago relacionado (opcional)
                    <select id="admin-clarification-payment" name="id_pago" disabled><option value="">Selecciona primero un alumno</option></select>
                </label>
            </div>

            <p class="admin-clarification-notice">La aclaración será revisada por el personal administrativo. Verifica que los datos sean correctos antes de registrarla.</p>

            <script type="application/json" id="clarification_students_data"><?= json_encode($alumnos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
            <script type="application/json" id="admin-clarification-relations"><?= json_encode($relaciones, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

            <div class="admin-clarification-form-actions">
                <a class="admin-clarification-action-button admin-clarification-cancel-button" href="<?= BASE_URL ?>/index.php?ruta=admin-aclaraciones">Cancelar</a>
                <button id="admin-clarification-submit" type="submit" class="admin-clarification-action-button admin-clarification-submit-button" disabled>Registrar aclaración</button>
            </div>
        </form>
        </div>
    </main>
</div>
<script src="<?= BASE_URL ?>/public/js/admin.js?v=<?= rawurlencode($adminJsVersion) ?>" defer></script>
</body>
</html>
