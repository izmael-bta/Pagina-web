document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('payment-card-form');
    const nombreTitular = document.getElementById('nombre-titular');
    const numeroTarjeta = document.getElementById('numero-tarjeta');
    const mesVencimiento = document.getElementById('mes-vencimiento');
    const anioVencimiento = document.getElementById('anio-vencimiento');
    const cvv = document.getElementById('cvv');
    const botonCvv = document.getElementById('payment-cvv-toggle');
    const botonEnviar = document.getElementById('payment-submit');

    if (!formulario) {
        return;
    }

    function soloDigitos(valor) {
        return valor.replace(/\D/g, '');
    }

    numeroTarjeta.addEventListener('input', function () {
        const digitos = soloDigitos(numeroTarjeta.value).slice(0, 16);
        numeroTarjeta.value = digitos.replace(/(.{4})/g, '$1 ').trim();
        numeroTarjeta.setCustomValidity(
            digitos.length === 0 || digitos.length === 16
                ? ''
                : 'El número de tarjeta debe contener exactamente 16 dígitos.'
        );
    });

    cvv.addEventListener('input', function () {
        cvv.value = soloDigitos(cvv.value).slice(0, 4);
        cvv.setCustomValidity('');
    });

    botonCvv.addEventListener('click', function () {
        const mostrar = cvv.type === 'password';
        cvv.type = mostrar ? 'text' : 'password';
        botonCvv.textContent = mostrar ? 'Ocultar' : 'Mostrar';
        botonCvv.setAttribute('aria-label', mostrar ? 'Ocultar CVV' : 'Mostrar CVV');
        botonCvv.setAttribute('aria-pressed', String(mostrar));
    });

    formulario.addEventListener('submit', function (event) {
        const digitos = soloDigitos(numeroTarjeta.value);
        const mes = Number(mesVencimiento.value);
        const anio = Number(anioVencimiento.value);
        const hoy = new Date();
        let valido = true;

        nombreTitular.setCustomValidity('');
        numeroTarjeta.setCustomValidity('');
        mesVencimiento.setCustomValidity('');
        anioVencimiento.setCustomValidity('');
        cvv.setCustomValidity('');

        if (nombreTitular.value.trim() === '') {
            nombreTitular.setCustomValidity('Ingresa el nombre del titular.');
            valido = false;
        }

        const numeroValido = /^\d{16}$/.test(digitos);

        if (!numeroValido) {
            numeroTarjeta.setCustomValidity('El número de tarjeta debe contener exactamente 16 dígitos.');
            valido = false;
        }

        if (!mes || !anio || anio < hoy.getFullYear()
            || (anio === hoy.getFullYear() && mes < hoy.getMonth() + 1)) {
            mesVencimiento.setCustomValidity('Selecciona una fecha de vencimiento válida.');
            valido = false;
        }

        if (!/^[0-9]{3,4}$/.test(cvv.value)) {
            cvv.setCustomValidity('Ingresa un CVV de 3 o 4 dígitos.');
            valido = false;
        }

        if (!valido || !formulario.checkValidity()) {
            event.preventDefault();
            formulario.reportValidity();
            return;
        }

        botonEnviar.disabled = true;
        botonEnviar.textContent = 'Procesando...';
    });
});
