<?php
require_once __DIR__.'/../models/Adeudo.php';
require_once __DIR__.'/../models/Alumno.php';
require_once __DIR__.'/../models/ConfiguracionPago.php';
require_once __DIR__.'/../helpers/Csrf.php';
require_once __DIR__.'/../helpers/PeriodoHelper.php';

// Controlador: consulta, creación y edición segura de adeudos.
class AdminAdeudoController
{
    private Adeudo $adeudoModelo; private Alumno $alumnoModelo; private ConfiguracionPago $configuracionModelo;
    public function __construct(mysqli $conexion){$this->adeudoModelo=new Adeudo($conexion);$this->alumnoModelo=new Alumno($conexion);$this->configuracionModelo=new ConfiguracionPago($conexion);}

    public function index(): void
    {
        $this->requerirAdmin();
        $this->adeudoModelo->actualizarRecargosVencidos();
        $busqueda=trim(is_string($_GET['buscar']??null)?$_GET['buscar']:'');
        $periodo=is_string($_GET['periodo']??null)&&preg_match('/^\d{4}-\d{2}$/',$_GET['periodo'])?$_GET['periodo']:'';
        $estado=in_array($_GET['estado']??'', ['Pendiente','Pagado'],true)?$_GET['estado']:'';
        $pagina=filter_var($_GET['pagina']??1,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:1;$porPagina=10;
        $totalResultados=$this->adeudoModelo->contarParaAdministrador($busqueda,$periodo,$estado);$totalPaginas=max(1,(int)ceil($totalResultados/$porPagina));$pagina=min($pagina,$totalPaginas);
        $adeudos=$this->adeudoModelo->listarParaAdministrador($busqueda,$periodo,$estado,$porPagina,($pagina-1)*$porPagina);
        $rutaAdminActiva='admin-adeudos';$flash=$this->flashObtener();
        require __DIR__.'/../views/admin/adeudos/index.php';
    }

    public function crear(): void{$this->requerirAdmin();$configuracion=$this->configuracionModelo->obtenerActiva();if(!$configuracion){$this->flash('error','No existe una configuración de pagos activa.');$this->redirigir('admin-configuracion');}$alumnos=$this->alumnoModelo->listarActivosParaSelector();$adeudo=['id_alumno'=>'','periodo'=>'','mensualidad'=>number_format((float)$configuracion['mensualidad'],2,'.',''),'aportacion_tsu'=>number_format((float)$configuracion['aportacion_tsu'],2,'.',''),'atraso'=>'0.00','recargo'=>'0.00','total'=>number_format((float)$configuracion['mensualidad']+(float)$configuracion['aportacion_tsu'],2,'.','')];$recargoProgramado=(float)$configuracion['recargo_vencimiento'];$diaLimite=(int)$configuracion['dia_limite'];$modo='crear';$rutaAdminActiva='admin-adeudos';$csrfToken=Csrf::token();require __DIR__.'/../views/admin/adeudos/crear.php';}

    public function guardar(): void
    {
        $this->postCsrf('admin-adeudo-crear');$idAlumno=$this->id($_POST['id_alumno']??null);$alumno=$idAlumno?$this->alumnoModelo->buscarPorId($idAlumno):null;
        if(!$alumno){$this->flash('error','Alumno no encontrado.');$this->redirigir('admin-adeudo-crear');}
        if(($alumno['estado']??'')!=='Activo'){$this->flash('error','El alumno está inactivo.');$this->redirigir('admin-adeudo-crear');}
        $configuracion=$this->configuracionModelo->obtenerActiva();if(!$configuracion){$this->flash('error','No existe una configuración de pagos activa.');$this->redirigir('admin-configuracion');}
        $datos=$this->datos();if(!$datos)$this->redirigir('admin-adeudo-crear');$datos['id_alumno']=$idAlumno;$datos['recargo']=0.00;$datos['monto_recargo_vencimiento']=(float)$configuracion['recargo_vencimiento'];$datos['dia_limite']=(int)$configuracion['dia_limite'];$datos['total']=round($datos['mensualidad']+$datos['aportacion_tsu']+$datos['atraso'],2);
        if($this->adeudoModelo->existeParaAlumnoYPeriodo($idAlumno,$datos['periodo'])){$this->flash('error','Ya existe un adeudo para este alumno y periodo.');$this->redirigir('admin-adeudo-crear');}
        try{$ok=$this->adeudoModelo->crear($datos)!==false;}catch(Throwable $e){$ok=false;}
        $this->flash($ok?'exito':'error',$ok?'Adeudo creado correctamente.':'No fue posible completar la operación.');$this->redirigir('admin-adeudos');
    }

    public function ver(): void{$this->requerirAdmin();$adeudo=$this->detalle();$periodoTexto=PeriodoHelper::formatear($adeudo['periodo']);$rutaAdminActiva='admin-adeudos';require __DIR__.'/../views/admin/adeudos/ver.php';}
    public function editar(): void
    {
        $this->requerirAdmin();$adeudo=$this->detalle();if($adeudo['estado']==='Pagado'){$this->flash('error','Los adeudos pagados no pueden modificarse.');$this->redirigir('admin-adeudo-ver&id='.$adeudo['id_adeudo']);}
        $adeudo['periodo']=substr((string)$adeudo['periodo'],0,7);$modo='editar';$rutaAdminActiva='admin-adeudos';$csrfToken=Csrf::token();require __DIR__.'/../views/admin/adeudos/editar.php';
    }
    public function actualizar(): void
    {
        $this->postCsrf('admin-adeudos');$id=$this->id($_POST['id_adeudo']??null);$actual=$id?$this->adeudoModelo->buscarDetallePorId($id):null;
        if(!$actual){$this->flash('error','El adeudo no fue encontrado.');$this->redirigir('admin-adeudos');}
        if($actual['estado']!=='Pendiente'){$this->flash('error','Los adeudos pagados no pueden modificarse.');$this->redirigir('admin-adeudo-ver&id='.$id);}
        $datos=$this->datos();if(!$datos)$this->redirigir('admin-adeudo-editar&id='.$id);
        if($this->adeudoModelo->existeParaAlumnoYPeriodo((int)$actual['id_alumno'],$datos['periodo'],$id)){$this->flash('error','Ya existe un adeudo para este alumno y periodo.');$this->redirigir('admin-adeudo-editar&id='.$id);}
        try{$ok=$this->adeudoModelo->actualizarPendiente($id,$datos);}catch(Throwable $e){$ok=false;}
        $this->flash($ok?'exito':'error',$ok?'Adeudo actualizado correctamente.':'No fue posible completar la operación.');$this->redirigir('admin-adeudos');
    }

    private function datos(): ?array
    {
        $mes=is_string($_POST['periodo']??null)?$_POST['periodo']:'';
        if(!preg_match('/^(\d{4})-(\d{2})$/',$mes,$m)||!checkdate((int)$m[2],1,(int)$m[1])){$this->flash('error','El periodo no es válido.');return null;}
        $datos=['periodo'=>sprintf('%04d-%02d-01',(int)$m[1],(int)$m[2])];
        foreach(['mensualidad','aportacion_tsu','atraso','recargo'] as $campo){$valor=filter_var($_POST[$campo]??null,FILTER_VALIDATE_FLOAT);if($valor===false||$valor<0){$this->flash('error','Los importes no pueden ser negativos.');return null;}$datos[$campo]=round((float)$valor,2);}
        $datos['total']=round($datos['mensualidad']+$datos['aportacion_tsu']+$datos['atraso']+$datos['recargo'],2);return $datos;
    }
    private function detalle(): array{$id=$this->id($_GET['id']??null);$a=$id?$this->adeudoModelo->buscarDetallePorId($id):null;if(!$a){$this->flash('error','El adeudo no fue encontrado.');$this->redirigir('admin-adeudos');}return $a;}
    private function postCsrf(string $ruta): void{$this->requerirAdmin();if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);header('Allow: POST');exit('Método no permitido.');}if(!Csrf::validar($_POST['csrf_token']??null)){$this->flash('error','Token de seguridad inválido.');$this->redirigir($ruta);}}
    private function requerirAdmin(): void{if(!isset($_SESSION['id_usuario'])||($_SESSION['rol']??'')!=='Administrador'){$this->redirigir('login-admin');}}
    private function id(mixed $v): int{return filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])?:0;}
    private function flash(string $t,string $m): void{$_SESSION['flash']=['tipo'=>$t,'mensaje'=>$m];}
    private function flashObtener(): ?array{$f=$_SESSION['flash']??null;unset($_SESSION['flash']);return is_array($f)?$f:null;}
    private function redirigir(string $r): never{header('Location: '.BASE_URL.'/index.php?ruta='.$r);exit;}
}
