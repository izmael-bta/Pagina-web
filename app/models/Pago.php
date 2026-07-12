<?php
// Modelo: registro, consulta y validación transaccional de pagos.
class Pago
{
    private mysqli $conexion;
    public function __construct(mysqli $conexion){$this->conexion=$conexion;}

    public function registrar(int $idAlumno,string $metodoPago,float $totalPagado,string $folio,string $fechaPago,?int $idAdeudo=null): bool
    {
        $estado='Validado';$origen='Portal Alumno';
        $stmt=$this->conexion->prepare('INSERT INTO pagos (id_alumno,id_adeudo,metodo_pago,total_pagado,folio,fecha_pago,estado_validacion,origen,fecha_validacion) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('iisdsssss',$idAlumno,$idAdeudo,$metodoPago,$totalPagado,$folio,$fechaPago,$estado,$origen,$fechaPago);
        return $stmt->execute();
    }

    public function buscarPorFolioYAlumno(int $idAlumno,string $folio,?int $idAdeudo=null): ?array
    {
        $stmt=$this->conexion->prepare('SELECT p.id_pago,p.id_alumno,p.metodo_pago,p.total_pagado,p.folio,p.fecha_pago,a.periodo FROM pagos p LEFT JOIN adeudos a ON a.id_adeudo=p.id_adeudo WHERE p.id_alumno=? AND p.folio=? AND p.estado_validacion="Validado" LIMIT 1');
        $stmt->bind_param('is',$idAlumno,$folio);$stmt->execute();return $stmt->get_result()->fetch_assoc()?:null;
    }

    private function consultaBase(): string{return ' FROM pagos p LEFT JOIN adeudos d ON d.id_adeudo=p.id_adeudo LEFT JOIN alumnos a ON a.id_alumno=p.id_alumno LEFT JOIN usuarios ur ON ur.id_usuario=p.registrado_por LEFT JOIN usuarios uv ON uv.id_usuario=p.validado_por ';}
    public function listarParaAdministrador(string $busqueda='',string $periodo='',string $estado='',string $origen='',int $limite=10,int $offset=0): array
    {
        $patron='%'.$busqueda.'%';$sql='SELECT p.id_pago,p.folio,a.matricula,a.nombre alumno,a.correo,d.periodo,p.metodo_pago,p.total_pagado,p.fecha_pago,p.origen,p.estado_validacion,p.observaciones,ur.nombre registrado_por_nombre,uv.nombre validado_por_nombre,p.fecha_validacion'.$this->consultaBase().' WHERE (?="" OR p.folio LIKE ? OR a.matricula LIKE ? OR a.nombre LIKE ? OR a.correo LIKE ?) AND (?="" OR DATE_FORMAT(d.periodo,"%Y-%m")=?) AND (?="" OR p.estado_validacion=?) AND (?="" OR p.origen=?) ORDER BY p.fecha_pago DESC,p.id_pago DESC LIMIT ? OFFSET ?';
        $stmt=$this->conexion->prepare($sql);$stmt->bind_param('sssssssssssii',$busqueda,$patron,$patron,$patron,$patron,$periodo,$periodo,$estado,$estado,$origen,$origen,$limite,$offset);$stmt->execute();return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function contarParaAdministrador(string $busqueda='',string $periodo='',string $estado='',string $origen=''): int
    {
        $patron='%'.$busqueda.'%';$sql='SELECT COUNT(*) total'.$this->consultaBase().' WHERE (?="" OR p.folio LIKE ? OR a.matricula LIKE ? OR a.nombre LIKE ? OR a.correo LIKE ?) AND (?="" OR DATE_FORMAT(d.periodo,"%Y-%m")=?) AND (?="" OR p.estado_validacion=?) AND (?="" OR p.origen=?)';$stmt=$this->conexion->prepare($sql);$stmt->bind_param('sssssssssss',$busqueda,$patron,$patron,$patron,$patron,$periodo,$periodo,$estado,$estado,$origen,$origen);$stmt->execute();return (int)($stmt->get_result()->fetch_assoc()['total']??0);
    }
    public function buscarDetalleAdministrativo(int $idPago,bool $bloquear=false): ?array
    {
        $sql='SELECT p.id_pago,p.id_adeudo,p.folio,a.id_alumno,a.matricula,a.nombre alumno,a.correo,a.estado alumno_estado,d.periodo,d.estado adeudo_estado,p.metodo_pago,p.total_pagado,p.fecha_pago,p.origen,p.estado_validacion,p.observaciones,ur.nombre registrado_por_nombre,uv.nombre validado_por_nombre,p.fecha_validacion'.$this->consultaBase().' WHERE p.id_pago=?'.($bloquear?' FOR UPDATE':'');$stmt=$this->conexion->prepare($sql);$stmt->bind_param('i',$idPago);$stmt->execute();return $stmt->get_result()->fetch_assoc()?:null;
    }
    public function listarAdeudosPendientesParaPagoManual(): array
    {
        $stmt=$this->conexion->prepare('SELECT d.id_adeudo,a.matricula,a.nombre,d.periodo,d.total FROM adeudos d INNER JOIN alumnos a ON a.id_alumno=d.id_alumno WHERE d.estado="Pendiente" AND a.estado="Activo" AND NOT EXISTS(SELECT 1 FROM pagos p WHERE p.id_adeudo=d.id_adeudo AND p.estado_validacion IN ("Pendiente","Validado")) ORDER BY a.nombre,d.periodo DESC');$stmt->execute();return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function existePagoActivoParaAdeudo(int $idAdeudo): bool{$stmt=$this->conexion->prepare('SELECT 1 FROM pagos WHERE id_adeudo=? AND estado_validacion IN ("Pendiente","Validado") LIMIT 1');$stmt->bind_param('i',$idAdeudo);$stmt->execute();return $stmt->get_result()->fetch_assoc()!==null;}
    public function registrarPagoManual(int $idAdeudo,int $idAdministrador,string $metodo,?string $referencia,?string $observaciones): int|false
    {
        $stmt=$this->conexion->prepare('SELECT d.id_alumno,d.total FROM adeudos d INNER JOIN alumnos a ON a.id_alumno=d.id_alumno WHERE d.id_adeudo=? AND d.estado="Pendiente" AND a.estado="Activo"');$stmt->bind_param('i',$idAdeudo);$stmt->execute();$d=$stmt->get_result()->fetch_assoc();if(!$d||$this->existePagoActivoParaAdeudo($idAdeudo))return false;
        do{$folio='UTSC-MAN-'.date('YmdHis').'-'.random_int(100,999);$s=$this->conexion->prepare('SELECT 1 FROM pagos WHERE folio=?');$s->bind_param('s',$folio);$s->execute();}while($s->get_result()->fetch_assoc());
        $nota=trim(implode(' | ',array_filter([$referencia?'Referencia: '.trim($referencia):null,$observaciones?trim($observaciones):null])));$nota=$nota===''?null:substr($nota,0,255);$fecha=date('Y-m-d H:i:s');$estado='Pendiente';$origen='Manual Administrador';$total=(float)$d['total'];$idAlumno=(int)$d['id_alumno'];
        $i=$this->conexion->prepare('INSERT INTO pagos (id_alumno,id_adeudo,metodo_pago,total_pagado,folio,fecha_pago,estado_validacion,origen,registrado_por,observaciones) VALUES (?,?,?,?,?,?,?,?,?,?)');$i->bind_param('iisdssssis',$idAlumno,$idAdeudo,$metodo,$total,$folio,$fecha,$estado,$origen,$idAdministrador,$nota);return $i->execute()?(int)$this->conexion->insert_id:false;
    }
    public function validarPago(int $idPago,int $idAdministrador): bool
    {
        $this->conexion->begin_transaction();try{$p=$this->buscarDetalleAdministrativo($idPago,true);if(!$p||$p['estado_validacion']!=='Pendiente'||$p['adeudo_estado']!=='Pendiente')throw new RuntimeException();$s=$this->conexion->prepare('SELECT 1 FROM pagos WHERE id_adeudo=? AND estado_validacion="Validado" AND id_pago<>? LIMIT 1');$s->bind_param('ii',$p['id_adeudo'],$idPago);$s->execute();if($s->get_result()->fetch_assoc())throw new RuntimeException();$fecha=date('Y-m-d H:i:s');$u=$this->conexion->prepare('UPDATE pagos SET estado_validacion="Validado",validado_por=?,fecha_validacion=? WHERE id_pago=? AND estado_validacion="Pendiente"');$u->bind_param('isi',$idAdministrador,$fecha,$idPago);$u->execute();$a=$this->conexion->prepare('UPDATE adeudos SET estado="Pagado" WHERE id_adeudo=? AND estado="Pendiente"');$a->bind_param('i',$p['id_adeudo']);$a->execute();if($u->affected_rows!==1||$a->affected_rows!==1)throw new RuntimeException();$this->conexion->commit();return true;}catch(Throwable $e){$this->conexion->rollback();return false;}
    }
    public function rechazarPago(int $idPago,int $idAdministrador,string $motivo): bool
    {
        $this->conexion->begin_transaction();try{$p=$this->buscarDetalleAdministrativo($idPago,true);if(!$p||$p['estado_validacion']!=='Pendiente')throw new RuntimeException();$fecha=date('Y-m-d H:i:s');$u=$this->conexion->prepare('UPDATE pagos SET estado_validacion="Rechazado",validado_por=?,fecha_validacion=?,observaciones=? WHERE id_pago=? AND estado_validacion="Pendiente"');$u->bind_param('issi',$idAdministrador,$fecha,$motivo,$idPago);$u->execute();if($u->affected_rows!==1)throw new RuntimeException();$this->conexion->commit();return true;}catch(Throwable $e){$this->conexion->rollback();return false;}
    }
}
