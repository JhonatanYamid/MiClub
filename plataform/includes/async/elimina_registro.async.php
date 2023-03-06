<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoBorrar");
if ($Permiso == 0) :
?>
	["NO TIENE PERMISOS PARA ELIMINAR REGISTROS"]
<?php
	exit;
endif;

if ($frm["Tabla"] == "Socio") {
	$usuario_elimina = "Eliminado por: " . SIMUser::get("IDUsuario") . " " . SIMUser::get("Nombre");
	$sql_copia_socio = "INSERT  INTO SocioEliminado (IDSocio,IDSocioSistemaExterno,IDClub,IDCategoria,IDParentesco,IDParentescoZeus,IDTipoSocioZeus,IDEstadoSocio,IDSocioPresalida,IDCursoNivel,Accion,
	AccionPadre,Parentesco,NumeroDerecho,Genero,Nombre,Apellido,FechaNacimiento,NumeroDocumento,Email,Clave,ClaveSistemaExterno,CorreoElectronico,Dispositivo,Token,Foto,TipoSocio,NumeroInvitados,NumeroAccesos,
	PermiteReservar,Predio,Torre,FechaTrEd,UsuarioTrEd) SELECT IDSocio,IDSocioSistemaExterno,IDClub,IDCategoria,IDParentesco,IDParentescoZeus,IDTipoSocioZeus,IDEstadoSocio,IDSocioPresalida,IDCursoNivel,Accion,
	AccionPadre,Parentesco,NumeroDerecho,Genero,Nombre,Apellido,FechaNacimiento,NumeroDocumento,Email,Clave,ClaveSistemaExterno,CorreoElectronico,Dispositivo,Token,Foto,TipoSocio,NumeroInvitados,NumeroAccesos,
	PermiteReservar,Predio,Torre,NOW(),'" . $usuario_elimina . "' FROM Socio WHERE IDSocio = '" . $frm["ID"] . "'";
	$dbo->query($sql_copia_socio);

	$sql_updateAutorizacion = "UPDATE SocioAutorizacion SET FechaFin = '" . date('Y-m-d') . "', HoraFin = '00:00:00', FechaCancelacion='" . date('Y-m-d H:i:s') . "' WHERE IDSocio = " . $frm["ID"];
	$dbo->query($sql_updateAutorizacion);

	/*
	$array_dependecias=array("Pqr","Reservageneral","SocioInvitado","SocioInvitadoEspecial","SocioAutorizacion","Correspondencia","EncuestaRespuesta","EventoRegistro","Clasificado","ReservaHotel","VotacionRespuesta");
	foreach ($array_dependecias as $tabla_dependencia){
		$sql_dep="Select * From ".$tabla_dependencia." Where ID".$frm["Tabla"]." = '".$frm["ID"]."'";
		$result_dep=$dbo->query($sql_dep);
		$dbo->rows($result_dep);
		if($dbo->rows($result_dep)>0){
			//$mensaje_dep="Lo sentimos, el registro no se puede eliminar ya que esta asociado a: " . $tabla_dependencia;?>["<?php echo $mensaje_dep;?>"]<?php
			//exit;
		}
	}
	*/
	//verifico dependencias

}



if ($frm["Tabla"] == "Pqr") {
	$usuario_elimina = "Eliminado por: " . SIMUser::get("IDUsuario") . " " . SIMUser::get("Nombre");
	$sql_copia_pqr = "INSERT  INTO PqrEliminado (IDPqr,Numero,IDTipoPqr,IDClub,IDArea,IDUsuario,IDSocio,IDPqrEstado,IDPqrMedio,Tipo,Asunto,
	Descripcion,NombreArchivoPqr,Archivo1,Fecha,FechaSeguimiento,Calificacion,ComentarioCalificacion,FechaCalificacion,NombreColaborador,
	ApellidoColaborador,FechaFinalizacion,UsuarioTrCr,FechaTrCr,FechaTrEd,UsuarioTrEd)
	SELECT IDPqr,Numero,IDTipoPqr,IDClub,IDArea,IDUsuario,IDSocio,IDPqrEstado,IDPqrMedio,Tipo,Asunto,
	Descripcion,NombreArchivoPqr,Archivo1,Fecha,FechaSeguimiento,Calificacion,ComentarioCalificacion,
	FechaCalificacion,NombreColaborador,ApellidoColaborador,FechaFinalizacion,UsuarioTrCr,FechaTrCr,NOW(),'" . $usuario_elimina . "' FROM Pqr WHERE IDPqr = '" . $frm["ID"] . "'";

	$dbo->query($sql_copia_pqr);
}


if ($frm["Tabla"] == "DomiciliarioEliminados") {
	$usuario_elimina = "Eliminado por: " . SIMUser::get("IDUsuario") . " " . SIMUser::get("Nombre");
	$sql_copia_domiciliario = "INSERT INTO DomiciliarioEliminados(IDDomiciliario, IDClub, IDSocio, IDUsuario, Empresa, Fecha, Hora, Nombre, Documento, Estado, Observaciones, EntregaRealizadaPor, UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd, FechaHoraIngreso)
	SELECT IDDomiciliario, IDClub, IDSocio, IDUsuario, Empresa, Fecha, Hora, Nombre, Documento, Estado, Observaciones, EntregaRealizadaPor, UsuarioTrCr, FechaTrCr, '" . $usuario_elimina . "' , NOW(), FechaHoraIngreso FROM Domiciliario WHERE IDDomiciliario = '" . $frm["ID"] . "'";

	$dbo->query($sql_copia_domiciliario);
}


if ($frm["Tabla"] == "CheckinLaboral") {
	$usuario_elimina = "Eliminado por: " . SIMUser::get("IDUsuario") . " " . SIMUser::get("Nombre");
	$sql_copia_checkin = "INSERT  INTO CheckinLaboralBck (IDCheckinLaboral ,IDClub,IDSocio,IDUsuario,LatitudEntrada,LongitudEntrada,LatitudSalida,LongitudSalida,Entrada,Salida,Estado,
	FechaCambioEstado,ComentarioRevision,FechaMovimientoEntrada,FechaMovimientoSalida,UltimoMovimiento,Observaciones,HoraEntradaEstablecida,HoraSalidaEstablecida,ObservacionEntrada,
	ObservacionSalida,FechaMovimientoEntradaDespuesDelTurno,FechaMovimientoSalidaDespuesDelTurno,UsuarioTrCr,FechaTrCr,FechaTrEd,UsuarioTrEd)
	SELECT IDCheckinLaboral ,IDClub,IDSocio,IDUsuario,LatitudEntrada,LongitudEntrada,LatitudSalida,LongitudSalida,Entrada,Salida,Estado,
	FechaCambioEstado,ComentarioRevision,FechaMovimientoEntrada,FechaMovimientoSalida,UltimoMovimiento,Observaciones,HoraEntradaEstablecida,HoraSalidaEstablecida,ObservacionEntrada,
	ObservacionSalida,FechaMovimientoEntradaDespuesDelTurno,FechaMovimientoSalidaDespuesDelTurno,UsuarioTrCr,FechaTrCr,NOW(),'" . $usuario_elimina . "' FROM CheckinLaboral WHERE IDCheckinLaboral = '" . $frm["ID"] . "'";

	$dbo->query($sql_copia_checkin);
}

if ($frm["Tabla"] == "Talonera") {
	$usuario_elimina = "Eliminado por: " . SIMUser::get("IDUsuario") . " " . SIMUser::get("Nombre");
	$sql_copia_talonera = "INSERT  INTO TaloneraEliminada (IDTalonera,IDClub,IDServicio,DirigidoA,NombreTalonera,DescripcionTalonera,ValorSocio,ValorUsuario,ValorGrupoFamiliar,ValorPorMiembro,CantidadEntradas,Duracion,MedicionDuracion,TaloneraMonedero,SaldoTaloneraMonedero,TodosLosServicios,Activa,UsuarioTrCr,FechaTrCr,FechaTrEd,UsuarioTrEd)
	SELECT IDTalonera,IDClub,IDServicio,DirigidoA,NombreTalonera,DescripcionTalonera,ValorSocio,ValorUsuario,ValorGrupoFamiliar,ValorPorMiembro,CantidadEntradas,Duracion,MedicionDuracion,TaloneraMonedero,SaldoTaloneraMonedero,TodosLosServicios,Activa,UsuarioTrCr,FechaTrCr,NOW(),'" . $usuario_elimina . "' FROM Talonera WHERE IDTalonera = '" . $frm["ID"] . "'";
	$dbo->query($sql_copia_talonera);
}


if ($frm["Tabla"] == "PqrFuncionario") {
	$usuario_elimina = "Eliminado por: " . SIMUser::get("IDUsuario") . " " . SIMUser::get("Nombre");
	$sql_copia_pqrFuncionario = "INSERT  INTO PqrFuncionarioEliminado (IDPqr,Numero,IDTipoPqr,IDClub,IDArea,IDUsuario,IDUsuarioCreacion,IDPqrEstado,Tipo,Asunto,
	Descripcion,NombreArchivoPqr,Archivo1,Archivo2,Archivo3,Fecha,Calificacion,ComentarioCalificacion,FechaCalificacion,
	UsuarioTrCr,FechaTrCr,FechaTrEd,UsuarioTrEd)
	SELECT IDPqr,Numero,IDTipoPqr,IDClub,IDArea,IDUsuario,IDUsuarioCreacion,IDPqrEstado,Tipo,Asunto,
	Descripcion,NombreArchivoPqr,Archivo1,Archivo2,Archivo3,Fecha,Calificacion,ComentarioCalificacion,
	FechaCalificacion,UsuarioTrCr,FechaTrCr,NOW(),'" . $usuario_elimina . "' FROM PqrFuncionario WHERE IDPqr = '" . $frm["ID"] . "'";

	$dbo->query($sql_copia_pqrFuncionario);
}

if ($frm["Tabla"] == "ReservaHotel") {
	$sql_copia = "INSERT IGNORE INTO ReservaHotelEliminada (IDReserva,IDClub,IDSocio,IDInvitado,IDHabitacion,IDPromocion,IDTemporadaAlta,IDTipoPago,IDUsuarioReserva,Temporada,CabezaReserva,DocumentoDuenoReserva, NombreDuenoReserva, EmailDuenoReserva, Estado,FechaInicio, FechaFin, Ninera, Corral, Valor,IVA, NumeroPersonas,Adicional,Pagado,EstadoTransaccion, FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,PagoPayu,FechaReserva,Observaciones,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd)
	SELECT IDReserva,IDClub,IDSocio,IDInvitado,IDHabitacion,IDPromocion,IDTemporadaAlta,IDTipoPago,IDUsuarioReserva,Temporada,CabezaReserva,DocumentoDuenoReserva, NombreDuenoReserva, EmailDuenoReserva, Estado,FechaInicio, FechaFin, Ninera, Corral, Valor,IVA, NumeroPersonas,Adicional,Pagado,EstadoTransaccion, FechaTransaccion,CodigoRespuesta,MedioPago,TipoMedioPago,PagoPayu,FechaReserva,Observaciones,UsuarioTrCr,FechaTrCr,'" . SIMUser::get("IDUsuario") . "',NOW()
	FROM ReservaHotel
	WHERE  IDReserva = '" . $frm["ID"] . "' ";
	$dbo->query($sql_copia);
	$datos_reserva_hotel = $dbo->fetchAll("ReservaHotel", " IDReserva = '" . $frm["ID"] . "' ", "array");
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDReserva = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
	SIMUtil::notificar_lista_espera_hotel($datos_reserva_hotel["IDClub"], $datos_reserva_hotel["FechaInicio"], $datos_reserva_hotel["FechaFin"]);
}

if ($frm["Tabla"] == "Noticia2") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDNoticia = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "Seccion2") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDSeccion = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "Producto2" || $frm["Tabla"] == "Producto3" || $frm["Tabla"] == "Producto4") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDProducto = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "RestauranteDomicilio2" || $frm["Tabla"] == "RestauranteDomicilio3" || $frm["Tabla"] == "RestauranteDomicilio4") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDRestauranteDomicilio = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "PqrFuncionario") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDPqr = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "CheckinLaboralHorasExtras") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDCheckinLaboral = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "CategoriaProducto2" || $frm["Tabla"] == "CategoriaProducto3" || $frm["Tabla"] == "CategoriaProducto4") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDCategoriaProducto = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "Documento2" || $frm["Tabla"] == "Documento3") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDDocumento = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "TipoArchivo2" || $frm["Tabla"] == "TipoArchivo3") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDTipoArchivo = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == "AreaFuncionario") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDArea = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}
if ($frm["Tabla"] == "Vacuna2") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDVacuna = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}
if ($frm["Tabla"] == "Festivos") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDFestivo = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}
if ($frm["Tabla"] == "ClubTipoSocio") {
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE IDClub = '" . SIMUser::get('club') . "' AND IDTipoSocio = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if (!empty($frm["Tabla"]) && !empty($frm["ID"]))
	$sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE ID" . $frm["Tabla"] . " = '" . $frm["ID"] . "' LIMIT 1";

if ($frm["Tabla"] == "Talega") {
	$sql_delete = "UPDATE " . $frm["Tabla"] . " SET Activo = 'N' WHERE IDTalega = '" . $frm["ID"] . "' LIMIT 1";
	$frm["Tabla"] = "";
}

if ($frm["Tabla"] == 'CategoriaFacturacion' || $frm["Tabla"] == 'TipoFacturacion') {
	$sqlDel = "DELETE FROM " . $frm["Tabla"] . "Club WHERE ID" . $frm["Tabla"] . " = '" . $frm["ID"] . "' LIMIT 1";
	$qryDel = $dbo->query($sqlDel);
}

if ($frm["Tabla"] == "CategoriaReconocimiento") {
	$sql_delete_Opcion_Reconocimiento = "DELETE FROM OpcionReconocimiento WHERE IDCategoriaReconocimiento = '" . $frm["ID"] . "'";
	$dbo->query($sql_delete_Opcion_Reconocimiento);
}

if ($frm["Tabla"] == "SocioPagosPendientesPoloClub") {
	$sql_delete_SocioPagosPendientesPoloClub = "DELETE FROM SocioPagosPendientesPoloClub WHERE Fecha = '" . $frm["ID"] . "'";
	$dbo->query($sql_delete_SocioPagosPendientesPoloClub);
}

//echo "<br>";
$qry_delete = $dbo->query($sql_delete);


$nom_usu = SIMUser::get("IDUsuario") . " " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . SIMUser::get("IDUsuario") . "' ");

SIMLog::insert($nom_usu, $frm["Tabla"], $frm["Tabla"], "delete",  $sql_delete);
?>
["ok"]
