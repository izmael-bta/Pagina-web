document.addEventListener('DOMContentLoaded', () => {
    const boton = document.getElementById('admin-menu-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');

    if (boton && sidebar && overlay) {
    const cerrarMenu = () => {
        sidebar.classList.remove('admin-sidebar-open');
        overlay.hidden = true;
        boton.setAttribute('aria-expanded', 'false');
        boton.setAttribute('aria-label', 'Abrir menú administrativo');
    };

    const abrirMenu = () => {
        sidebar.classList.add('admin-sidebar-open');
        overlay.hidden = false;
        boton.setAttribute('aria-expanded', 'true');
        boton.setAttribute('aria-label', 'Cerrar menú administrativo');
    };

    boton.addEventListener('click', () => {
        if (sidebar.classList.contains('admin-sidebar-open')) {
            cerrarMenu();
        } else {
            abrirMenu();
        }
    });

    overlay.addEventListener('click', cerrarMenu);
    sidebar.addEventListener('click', (evento) => {
        if (evento.target.closest('a') && window.innerWidth <= 800) {
            cerrarMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 800) {
            cerrarMenu();
        }
    });
    }

    document.querySelectorAll('form[data-confirm]').forEach((formulario) => {
        formulario.addEventListener('submit', (evento) => {
            if (!window.confirm(formulario.dataset.confirm || '¿Deseas continuar?')) {
                evento.preventDefault();
            }
        });
    });

    const matricula = document.getElementById('admin-student-matricula');
    const correo = document.getElementById('admin-student-correo');
    if (matricula && correo) {
        const actualizarCorreo = () => {
            const valor = matricula.value.trim().replace(/\D/g, '');
            correo.value = valor ? `${valor}@virtual.utsc.edu.mx` : '';
        };
        matricula.addEventListener('input', actualizarCorreo);
        actualizarCorreo();
    }

    const importesAdeudo = document.querySelectorAll('.admin-debt-amount');
    const totalAdeudo = document.getElementById('admin-debt-total');
    if (importesAdeudo.length && totalAdeudo) {
        const calcularTotal = () => {
            const total = Array.from(importesAdeudo).reduce((suma, campo) => {
                const importe = Number.parseFloat(campo.value);
                return suma + (Number.isFinite(importe) && importe >= 0 ? importe : 0);
            }, 0);
            totalAdeudo.value = total.toFixed(2);
        };
        importesAdeudo.forEach((campo) => campo.addEventListener('input', calcularTotal));
        calcularTotal();
    }

    const selectorProrroga = document.getElementById('admin-extension-debt');
    const fechaActualProrroga = document.getElementById('admin-extension-current');
    const nuevaFechaProrroga = document.getElementById('admin-extension-new');
    if (selectorProrroga && fechaActualProrroga && nuevaFechaProrroga) {
        const motivoProrroga = document.getElementById('admin-extension-reason');
        const botonProrroga = document.getElementById('admin-extension-submit');
        const resumenProrroga = document.getElementById('admin-extension-summary-grid');
        const mensajeResumenProrroga = document.getElementById('admin-extension-summary-empty');
        const camposResumen = {
            student: document.getElementById('admin-extension-student'),
            enrollment: document.getElementById('admin-extension-enrollment'),
            period: document.getElementById('admin-extension-period'),
            total: document.getElementById('admin-extension-total'),
            status: document.getElementById('admin-extension-status'),
            limit: document.getElementById('admin-extension-limit-summary'),
        };
        const formatearFecha = (fecha) => {
            if (!/^\d{4}-\d{2}-\d{2}$/.test(fecha)) return '—';
            const [anio, mes, dia] = fecha.split('-');
            return `${dia}/${mes}/${anio}`;
        };
        const actualizarProrroga = () => {
            const datos = selectorProrroga.selectedOptions[0]?.dataset || {};
            const limite = datos.limit || '';
            const existeSeleccion = selectorProrroga.value !== '' && limite !== '';
            const limiteVisible = formatearFecha(limite);
            fechaActualProrroga.value = limiteVisible === '—' ? '' : limiteVisible;
            if (camposResumen.student) camposResumen.student.textContent = datos.student || '—';
            if (camposResumen.enrollment) camposResumen.enrollment.textContent = datos.enrollment || '—';
            if (camposResumen.period) camposResumen.period.textContent = datos.period || '—';
            if (camposResumen.total) camposResumen.total.textContent = datos.total ? `$${datos.total}` : '—';
            if (camposResumen.status) camposResumen.status.textContent = datos.status || '—';
            if (camposResumen.limit) camposResumen.limit.textContent = limiteVisible;
            if (resumenProrroga) resumenProrroga.hidden = !existeSeleccion;
            if (mensajeResumenProrroga) mensajeResumenProrroga.hidden = existeSeleccion;
            nuevaFechaProrroga.disabled = !existeSeleccion;
            if (motivoProrroga) motivoProrroga.disabled = !existeSeleccion;
            if (botonProrroga) botonProrroga.disabled = !existeSeleccion;
            if (existeSeleccion) {
                const siguiente = new Date(`${limite}T12:00:00`);
                siguiente.setDate(siguiente.getDate() + 1);
                nuevaFechaProrroga.min = siguiente.toISOString().slice(0, 10);
            } else {
                nuevaFechaProrroga.value = '';
                nuevaFechaProrroga.removeAttribute('min');
                if (motivoProrroga) motivoProrroga.value = '';
            }
        };
        selectorProrroga.addEventListener('change', actualizarProrroga);
        actualizarProrroga();
    }

    const buscadorAdeudo = document.getElementById('admin-extension-debt-search');
    const idAdeudo = document.getElementById('id_adeudo');
    const resultadosAdeudo = document.getElementById('admin-extension-results');
    const datosAdeudo = document.getElementById('admin-extension-debts-data');
    if (buscadorAdeudo && idAdeudo && resultadosAdeudo && datosAdeudo) {
        const adeudos = JSON.parse(datosAdeudo.textContent || '[]');
        const nuevaFecha = document.getElementById('admin-extension-new');
        const motivo = document.getElementById('admin-extension-reason');
        const enviar = document.getElementById('admin-extension-submit');
        const resumen = document.getElementById('admin-extension-summary-grid');
        const guia = document.getElementById('admin-extension-summary-empty');
        const fechaActual = document.getElementById('admin-extension-current');
        const formatoFecha = (fecha) => fecha ? fecha.split('-').reverse().join('/') : '—';
        const actualizarResumenAdeudo = (adeudo = null) => {
            const valores = {student:adeudo?.alumno,enrollment:adeudo?.matricula,period:adeudo?.periodo,total:adeudo?`$${adeudo.total}`:null,status:adeudo?.estado,limit:adeudo?formatoFecha(adeudo.fecha_limite):null};
            Object.entries(valores).forEach(([campo,valor])=>{const nodo=document.getElementById(`admin-extension-${campo}`+(campo==='limit'?'-summary':''));if(nodo)nodo.textContent=valor||'—';});
            if(fechaActual)fechaActual.value=adeudo?formatoFecha(adeudo.fecha_limite):'';
            if(resumen)resumen.hidden=!adeudo;if(guia)guia.hidden=Boolean(adeudo);
            [nuevaFecha,motivo,enviar].forEach((control)=>{if(control)control.disabled=!adeudo;});
            if(adeudo&&nuevaFecha){const d=new Date(`${adeudo.fecha_limite}T12:00:00`);d.setDate(d.getDate()+1);nuevaFecha.min=d.toISOString().slice(0,10);}else{if(nuevaFecha){nuevaFecha.value='';nuevaFecha.removeAttribute('min');}if(motivo)motivo.value='';}
        };
        const cerrar=()=>{resultadosAdeudo.hidden=true;buscadorAdeudo.setAttribute('aria-expanded','false');};
        const limpiarSeleccionAdeudo=()=>{idAdeudo.value='';actualizarResumenAdeudo(null);};
        const seleccionarAdeudo=(adeudo)=>{idAdeudo.value=String(adeudo.id_adeudo);buscadorAdeudo.value=`${adeudo.matricula} - ${adeudo.alumno}`;actualizarResumenAdeudo(adeudo);cerrar();};
        const filtrarAdeudos=()=>{limpiarSeleccionAdeudo();const termino=buscadorAdeudo.value.trim().toLocaleLowerCase('es');resultadosAdeudo.replaceChildren();if(!termino){cerrar();return;}const lista=adeudos.filter((a)=>String(a.matricula).toLowerCase().includes(termino)||String(a.alumno).toLocaleLowerCase('es').includes(termino));if(!lista.length){const p=document.createElement('p');p.className='admin-extension-no-results';p.textContent='No se encontraron adeudos pendientes para esa matrícula.';resultadosAdeudo.append(p);}lista.forEach((a)=>{const b=document.createElement('button');b.type='button';b.className='admin-extension-result';b.setAttribute('role','option');b.textContent=`${a.matricula} - ${a.alumno} - ${a.periodo} - ${formatoFecha(a.fecha_limite)}`;b.addEventListener('click',()=>seleccionarAdeudo(a));resultadosAdeudo.append(b);});resultadosAdeudo.hidden=false;buscadorAdeudo.setAttribute('aria-expanded','true');};
        buscadorAdeudo.addEventListener('input',filtrarAdeudos);
        buscadorAdeudo.addEventListener('keydown',(e)=>{if(e.key==='Escape')cerrar();if(e.key==='Enter'){const primero=resultadosAdeudo.querySelector('.admin-extension-result');if(primero){e.preventDefault();primero.click();}}});
        const inicial=adeudos.find((a)=>String(a.id_adeudo)===idAdeudo.value);inicial?seleccionarAdeudo(inicial):limpiarSeleccionAdeudo();
    }

});

function initAdminStatistics(){
    const charts=[['statistics_income_chart','statistics_income_data'],['statistics_debts_chart','statistics_debts_data'],['statistics_payment_methods_chart','statistics_methods_data'],['statistics_origins_chart','statistics_origins_data'],['statistics_students_chart','statistics_students_data'],['statistics_clarifications_chart','statistics_clarifications_data'],['statistics_extensions_chart','statistics_extensions_data'],['statistics_payments_chart','statistics_payments_data']];
    const colors=['#f58220','#23834f','#2878b8','#d4a017','#b74242','#70757a'];
    const draw=(canvas,data)=>{const wrapper=canvas.closest('.admin-statistics-chart-wrapper')||canvas.parentElement;const width=Math.max(260,wrapper.clientWidth);const height=window.innerWidth<=600?190:(window.innerWidth<=900?210:230);canvas.width=width;canvas.height=height;const ctx=canvas.getContext('2d');ctx.clearRect(0,0,width,height);const values=data.map(x=>Number(x.cantidad??x.total??0));const max=Math.max(...values,1);const left=42,right=14,top=22,bottom=42,plotHeight=height-top-bottom;const slot=(width-left-right)/Math.max(values.length,1);const bar=Math.max(12,Math.min(42,slot*.55));ctx.font=window.innerWidth<=600?'10px Arial':'11px Arial';data.forEach((item,i)=>{const value=values[i];const h=(value/max)*plotHeight;const x=left+i*slot+(slot-bar)/2;const y=top+plotHeight-h;ctx.fillStyle=colors[i%colors.length];ctx.fillRect(x,y,bar,h);ctx.fillStyle='#333';ctx.textAlign='center';ctx.fillText(String(value.toFixed(2).replace('.00','')),x+bar/2,Math.max(13,y-6));const label=String(item.etiqueta??item.periodo??'').slice(0,10);ctx.fillText(label,x+bar/2,height-17);});ctx.strokeStyle='#9aa0a6';ctx.lineWidth=1;ctx.beginPath();ctx.moveTo(left,top);ctx.lineTo(left,top+plotHeight);ctx.lineTo(width-right,top+plotHeight);ctx.stroke();};
    charts.forEach(([canvasId])=>{const canvas=document.getElementById(canvasId);if(!canvas||canvas.parentElement.classList.contains('admin-statistics-chart-wrapper'))return;const wrapper=document.createElement('div');wrapper.className='admin-statistics-chart-wrapper';canvas.parentNode.insertBefore(wrapper,canvas);wrapper.appendChild(canvas);});const render=()=>charts.forEach(([canvasId,dataId])=>{const canvas=document.getElementById(canvasId),node=document.getElementById(dataId);if(!canvas||!node)return;try{const data=JSON.parse(node.textContent||'[]');if(data.length)draw(canvas,data);}catch{}});render();let timer;window.addEventListener('resize',()=>{clearTimeout(timer);timer=setTimeout(render,150);});
}
document.addEventListener('DOMContentLoaded',initAdminStatistics);

function initClarificationStudentSearch(){
    const buscadorAclaracion=document.getElementById('clarification_student_search');
    const idAlumnoAclaracion=document.getElementById('clarification_student_id');
    const resultadosAclaracion=document.getElementById('clarification_student_results');
    const alumnosAclaracionJson=document.getElementById('clarification_students_data');
    const relacionesAclaracionJson=document.getElementById('admin-clarification-relations');
    if(buscadorAclaracion&&idAlumnoAclaracion&&resultadosAclaracion&&alumnosAclaracionJson&&relacionesAclaracionJson){
        const alumnos=JSON.parse(alumnosAclaracionJson.textContent||'[]');
        const relaciones=JSON.parse(relacionesAclaracionJson.textContent||'{"adeudos":[],"pagos":[]}');
        const resumen=document.getElementById('clarification_student_data');
        const guia=document.getElementById('clarification_student_empty');
        const tipo=document.getElementById('admin-clarification-type');
        const asunto=document.getElementById('admin-clarification-subject');
        const descripcion=document.getElementById('descripcion');
        const adeudo=document.getElementById('admin-clarification-debt');
        const pago=document.getElementById('admin-clarification-payment');
        const enviar=document.getElementById('admin-clarification-submit');
        let indiceActivo=-1;
        const normalizar=(valor)=>String(valor||'').trim().toLocaleLowerCase('es');
        const cerrar=()=>{resultadosAclaracion.hidden=true;buscadorAclaracion.setAttribute('aria-expanded','false');indiceActivo=-1;};
        const actualizarFormulario=()=>{const seleccionado=idAlumnoAclaracion.value!=='';[tipo,asunto,descripcion,adeudo,pago].forEach((campo)=>{campo.disabled=!seleccionado;});enviar.disabled=!(seleccionado&&tipo.value&&asunto.value.trim()&&descripcion.value.trim().length>=10);};
        const llenarRelaciones=(alumnoId)=>{const llenar=(select,items,texto)=>{select.replaceChildren(new Option(texto,''));items.forEach((item)=>select.add(new Option(item.etiqueta,String(item.id))));};llenar(adeudo,relaciones.adeudos.filter((x)=>Number(x.id_alumno)===Number(alumnoId)).map((x)=>({id:x.id_adeudo,etiqueta:`${x.periodo} - $${Number(x.total).toFixed(2)} - ${x.estado}`})),'Sin adeudo relacionado');llenar(pago,relaciones.pagos.filter((x)=>Number(x.id_alumno)===Number(alumnoId)).map((x)=>({id:x.id_pago,etiqueta:`${x.folio} - $${Number(x.total_pagado).toFixed(2)} - ${x.estado_validacion}`})),'Sin pago relacionado');};
        const limpiar=()=>{idAlumnoAclaracion.value='';resumen.hidden=true;guia.hidden=false;adeudo.replaceChildren(new Option('Selecciona primero un alumno',''));pago.replaceChildren(new Option('Selecciona primero un alumno',''));actualizarFormulario();};
        const seleccionar=(alumno)=>{idAlumnoAclaracion.value=String(alumno.id_alumno);buscadorAclaracion.value=`${alumno.matricula} - ${alumno.nombre}`;document.getElementById('admin-clarification-summary-matricula').textContent=alumno.matricula||'';document.getElementById('admin-clarification-summary-name').textContent=alumno.nombre||'';document.getElementById('admin-clarification-summary-email').textContent=alumno.correo||'';resumen.hidden=false;guia.hidden=true;llenarRelaciones(alumno.id_alumno);cerrar();actualizarFormulario();};
        const mostrar=(coincidencias)=>{resultadosAclaracion.replaceChildren();coincidencias.slice(0,6).forEach((alumno,indice)=>{const opcion=document.createElement('button');opcion.type='button';opcion.className='admin-clarification-result';opcion.setAttribute('role','option');opcion.dataset.index=String(indice);opcion.textContent=`${alumno.matricula} - ${alumno.nombre} - ${alumno.correo||'Sin correo'}`;opcion.addEventListener('click',()=>seleccionar(alumno));resultadosAclaracion.append(opcion);});if(!coincidencias.length){const mensaje=document.createElement('p');mensaje.className='admin-clarification-no-results';mensaje.textContent='No se encontraron alumnos.';resultadosAclaracion.append(mensaje);}resultadosAclaracion.hidden=false;buscadorAclaracion.setAttribute('aria-expanded','true');indiceActivo=-1;};
        const filtrar=()=>{const termino=normalizar(buscadorAclaracion.value);limpiar();if(!termino){cerrar();return;}mostrar(alumnos.filter((alumno)=>normalizar(`${String(alumno.matricula)} ${alumno.nombre} ${alumno.correo||''}`).includes(termino)));};
        buscadorAclaracion.addEventListener('input',filtrar);
        buscadorAclaracion.addEventListener('focus',()=>{if(!idAlumnoAclaracion.value)filtrar();});
        buscadorAclaracion.addEventListener('keydown',(evento)=>{const opciones=[...resultadosAclaracion.querySelectorAll('.admin-clarification-result')];if(evento.key==='Escape')cerrar();if((evento.key==='ArrowDown'||evento.key==='ArrowUp')&&opciones.length){evento.preventDefault();indiceActivo=evento.key==='ArrowDown'?Math.min(indiceActivo+1,opciones.length-1):Math.max(indiceActivo-1,0);opciones.forEach((opcion,i)=>opcion.classList.toggle('admin-clarification-result-active',i===indiceActivo));opciones[indiceActivo].scrollIntoView({block:'nearest'});}if(evento.key==='Enter'&&opciones.length){evento.preventDefault();opciones[Math.max(indiceActivo,0)].click();}});
        document.addEventListener('click',(evento)=>{if(!resultadosAclaracion.contains(evento.target)&&evento.target!==buscadorAclaracion)cerrar();});
        [tipo,asunto,descripcion].forEach((campo)=>{campo.addEventListener('input',actualizarFormulario);campo.addEventListener('change',actualizarFormulario);});
        limpiar();
    }
}

document.addEventListener('DOMContentLoaded', initClarificationStudentSearch);

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm-submit]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (!window.confirm(button.dataset.confirmSubmit || '¿Deseas continuar?')) {
                event.preventDefault();
            }
        });
    });
});
