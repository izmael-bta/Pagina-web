document.addEventListener('DOMContentLoaded', function () {
    const botonAccesos = document.getElementById('accesos-boton');
    const menuAccesos = document.getElementById('accesos-menu');

    function cerrarMenu() {
        if (!botonAccesos || !menuAccesos) {
            return;
        }

        botonAccesos.setAttribute('aria-expanded', 'false');
        menuAccesos.hidden = true;
    }

    if (botonAccesos && menuAccesos) {
        botonAccesos.addEventListener('click', function () {
            const estaAbierto = botonAccesos.getAttribute('aria-expanded') === 'true';
            botonAccesos.setAttribute('aria-expanded', String(!estaAbierto));
            menuAccesos.hidden = estaAbierto;

            if (!estaAbierto) {
                menuAccesos.querySelector('a').focus();
            }
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.accesos')) {
                cerrarMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !menuAccesos.hidden) {
                cerrarMenu();
                botonAccesos.focus();
            }
        });
    }

    document.querySelectorAll('[data-password-target]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const campo = document.getElementById(boton.dataset.passwordTarget);

            if (!campo) {
                return;
            }

            const mostrar = campo.type === 'password';
            campo.type = mostrar ? 'text' : 'password';
            boton.textContent = mostrar ? 'Ocultar' : 'Mostrar';
            boton.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
            boton.setAttribute('aria-pressed', String(mostrar));
        });
    });

    const formularioLogin = document.getElementById('form-login');

    if (formularioLogin) {
        formularioLogin.addEventListener('submit', function (event) {
            const matricula = document.getElementById('matricula');
            const password = document.getElementById('password-alumno');

            if (matricula.value.trim() === '' || password.value === '') {
                event.preventDefault();
                (matricula.value.trim() === '' ? matricula : password).focus();
            }
        });
    }

});
