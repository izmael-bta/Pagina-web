<?php // Vista parcial: campos compartidos para crear y editar alumnos. ?>
<form class="admin-student-form" method="post" action="<?= BASE_URL ?>/index.php?ruta=<?= $modoFormulario === 'crear' ? 'admin-alumno-guardar' : 'admin-alumno-actualizar' ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($modoFormulario === 'editar'): ?><input type="hidden" name="id_alumno" value="<?= (int) $alumno['id_alumno'] ?>"><?php endif; ?>
    <label class="admin-student-field">Matrícula
        <input id="admin-student-matricula" type="text" name="matricula" value="<?= htmlspecialchars($alumno['matricula'], ENT_QUOTES, 'UTF-8') ?>" inputmode="numeric" pattern="[0-9]{3,20}" maxlength="20" required>
    </label>
    <label class="admin-student-field">Correo institucional
        <input id="admin-student-correo" type="email" value="<?= htmlspecialchars($alumno['correo'], ENT_QUOTES, 'UTF-8') ?>" readonly tabindex="-1">
    </label>
    <label class="admin-student-field">Nombre completo
        <input type="text" name="nombre" value="<?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required>
    </label>
    <label class="admin-student-field">Carrera
        <input type="text" name="carrera" value="<?= htmlspecialchars($alumno['carrera'], ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required>
    </label>
    <label class="admin-student-field">Grupo
        <input type="text" name="grupo" value="<?= htmlspecialchars($alumno['grupo'], ENT_QUOTES, 'UTF-8') ?>" maxlength="20" required>
    </label>
    <?php if ($modoFormulario === 'editar'): ?>
        <p class="admin-student-current-status">Estado actual: <strong><?= htmlspecialchars($alumno['estado'], ENT_QUOTES, 'UTF-8') ?></strong></p>
    <?php endif; ?>
    <div class="admin-student-form-actions">
        <button class="admin-student-submit" type="submit"><?= $modoFormulario === 'crear' ? 'Guardar alumno' : 'Guardar cambios' ?></button>
        <a href="<?= BASE_URL ?>/index.php?ruta=admin-alumnos">Cancelar</a>
    </div>
</form>
