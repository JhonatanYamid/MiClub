<?php

/**** SERVICIOS JSON PARA APPS MOVILES ******/
/**** Creación: Jorge Chirivi ******/
/**** Fecha de Creación: 17 de Septiembre de 2015 ******/
/**** Ultima Modificación': 11 de Febrero de 2016 Jorge Chirivi ******/
/**** Comentarios ULtima Modificación: ******/
/**** Scripts Iniciales ******/
require("../admin/config.inc.php");
header("Content-type: application/json; charset=utf-8");

if($_GET["key"])
	$key = SIMNet::req( "key" );
else
	$key = $_POST["key"];
	
if($_GET["action"])
	$action = SIMNet::req( "action" );
else
	$action = $_POST["action"];	

if($_GET["IDClub"])
	$IDClub = SIMNet::req( "IDClub" );
else
	$IDClub = $_POST["IDClub"];		
	

//$action = SIMNet::req( "action" );
//$IDClub = SIMNet::req( "IDClub" );

$nowserver = date("Y-m-d H:i:s");

//Validar KEY
if( $key <> KEY_SERVICES || empty($IDClub))
	exit;

//Verificar Acciones
switch( $action ){
	
	
	//Validar Socio
	case "getsocio":
		$email = SIMNet::req( "email" );
		$clave = SIMNet::req( "clave" );
		//Validar Socio
		$respuesta = SIMWebService::valida_socio($email,$clave,$IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar splash del club
	case "getbannerapp":
		//Traer banner
		$respuesta = SIMWebService::get_banner_app($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar la configuracion del club, nombre, color, etc
	case "siteoption":
		$respuesta = SIMWebService::get_club($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar secciones de noticia del club o del socio
	case "getseccion":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebService::get_seccion($IDClub, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar secciones de eventos del club o del socio
	case "getseccionevento":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebService::get_seccionevento($IDClub, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar secciones de galerias del club o del socio
	case "getsecciongaleria":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebService::get_secciongaleria($IDClub, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar secciones del club o del socio completas de noticias, eventos y galerias
	case "getseccionclub":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebService::get_seccion_club($IDClub, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar areas del club
	case "getareaclub":		
		$respuesta = SIMWebService::get_area_club($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar noticias del club o del socio o de la seccion o busqueda
	case "getnoticias":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDSeccion = SIMNet::req( "IDSeccion" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebService::get_noticias($IDClub, $IDSeccion, $IDSocio, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar eventos del club o del socio o de la seccion o busqueda
	case "geteventos":
		$IDSocio = SIMNet::req( "IDSocio" );
		
		if(!empty(SIMNet::req( "IDSeccion" ))):
			$IDSeccionEvento = SIMNet::req( "IDSeccion" );
		endif;
		
		if(!empty(SIMNet::req( "IDSeccionEvento" ))):
			$IDSeccionEvento = SIMNet::req( "IDSeccionEvento" );
		endif;
		
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebService::get_eventos($IDClub, $IDSeccionEvento, $IDSocio, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar directorio del club
	case "getdirectorio":
		$respuesta = SIMWebService::get_directorio($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar directorio del club
	case "getrestaurante":
		$respuesta = SIMWebService::get_restaurante($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar documentos del club
	case "getdocumento":
		$IDTipoArchivo = SIMNet::req( "IDTipoArchivo" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebService::get_documento($IDClub, $IDTipoArchivo,$IDServicio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar tipo de archivos
	case "gettipoarchivo":	
		$IDTipoArchivo = SIMNet::req( "IDTipoArchivo" );
		$respuesta = SIMWebService::get_tipoarchivo($IDClub,$IDTipoArchivo);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar galerias del club o del socio o de la seccion o busqueda
	case "getgaleria":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDSeccionGaleria = SIMNet::req( "IDSeccion" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebService::get_galeria($IDClub, $IDSeccionGaleria, $IDSocio, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	// Guardar las preferncias del usuario
	case "setpreferencias":
		$IDSocio = $_POST["IDSocio"];
		$SeccionesContenido= $_POST["SeccionesContenido"];
		$SeccionesEvento= $_POST["SeccionesEvento"];
		$SeccionesGaleria= $_POST["SeccionesGaleria"];
		$respuesta = SIMWebService::set_preferencias($IDClub, $IDSocio, $SeccionesContenido, $SeccionesEvento,$SeccionesGaleria);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	// Guardar Socios Favoritos
	case "setsociofavorito":
		$IDSocio = $_POST["IDSocio"];
		$SocioFavorito = $_POST["SocioFavorito"];
		$EstadoFavorito = $_POST["EstadoFavorito"];
		$respuesta = SIMWebService::set_socio_favorito($IDClub, $IDSocio, $SocioFavorito, $EstadoFavorito);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	
	//Enviar socios del club
	case "getsociosclub":
		$IDSocio = SIMNet::req( "IDSocio" );
		$NumeroDocumento = SIMNet::req( "NumeroDocumento" );
		$NumeroDerecho = SIMNet::req( "NumeroDerecho" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebService::get_socios_club($IDClub, $NumeroDocumento, $NumeroDerecho, $Tag, $IDSocio);
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('getsociosclub','".json_encode($_GET)."','')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar socios del club
	case "getservicios":
		$respuesta = SIMWebService::get_servicios($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar pqr socio
	case "getpqrsocio":		
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDPqr = SIMNet::req( "IDPqr" );		
		$respuesta = SIMWebService::get_pqr_socio($IDClub,$IDSocio,$IDPqr);		
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Guardar contactos del club
	case "setcontacto":
		$IDSocio = $_POST["IDSocio"];
		$Comentario = $_POST["Comentario"];
		$respuesta = SIMWebService::set_contacto($IDClub,$IDSocio,$Comentario);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Guardar Pqr
	case "setpqr":
		$IDArea = $_POST["IDArea"];
		$IDSocio = $_POST["IDSocio"];
		$TipoPqr = $_POST["TipoPqr"];
		$Asunto = utf8_decode($_POST["Asunto"]);
		$Comentario = utf8_decode($_POST["Comentario"]);
		$Archivo = $_POST["Archivo"];
		
		$respuesta = SIMWebService::set_pqr($IDClub,$IDArea, $IDSocio,$TipoPqr, $Asunto, $Comentario, $Archivo, $_FILES);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Guardar Respuesta Socio Pqr
	case "setpqrrespuesta":
		$IDSocio = $_POST["IDSocio"];
		$IDPqr = $_POST["IDPqr"];
		$Comentario = utf8_decode($_POST["Comentario"]);
		$respuesta = SIMWebService::set_pqr_respuesta($IDClub,$IDSocio, $IDPqr, $Comentario);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Guardar Foto Socio
	case "setfotosocio":
		$IDSocio = $_POST["IDSocio"];
		$Archivo = $_POST["Archivo"];		
		$respuesta = SIMWebService::set_foto_socio($IDClub, $IDSocio,$Archivo, $_FILES);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Verificar si Foto Socio ya fue cargada
	case "getactualizarfotosocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebService::get_actualizar_foto_socio($IDClub,$IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Guardar invitados del socio club
	case "setinvitado":
		$IDSocio = $_POST["IDSocio"];
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$Nombre = $_POST["Nombre"];
		$FechaIngreso = $_POST["FechaIngreso"];
		$respuesta = SIMWebService::set_invitado($IDClub,$IDSocio,$NumeroDocumento,$Nombre,$FechaIngreso);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Actualizar invitados del socio club
	case "setinvitadoupdate":
		$IDSocio = $_POST["IDSocio"];
		$IDInvitado = $_POST["IDInvitado"];
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$Nombre = $_POST["Nombre"];
		$FechaIngreso = $_POST["FechaIngreso"];
		$respuesta = SIMWebService::set_invitado_update($IDClub,$IDSocio,$IDInvitado,$NumeroDocumento,$Nombre,$FechaIngreso);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Eliminar Reserva
	case "cancelarinvitacion":
		$IDSocio = SIMNet::req( "IDSocio" );;
		$IDInvitacion = SIMNet::req( "IDInvitacion" );
		$respuesta = SIMWebService::cancela_invitacion($IDClub,$IDSocio,$IDInvitacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Guardar invitados a una reserva
	case "setinvitadoservicio":
		$IDReserva = $_POST["IDReserva"];
		$Invitados = $_POST["Invitados"];
		$respuesta = SIMWebService::set_invitado_servicio($IDClub,$IDReserva,$Invitados);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Elimina invitados a una reserva
	case "delinvitadoservicio":
		$IDReserva = $_POST["IDReserva"];
		$IDReservaGeneralInvitado = $_POST["IDReservaGeneralInvitado"];
		$respuesta = SIMWebService::del_invitado_servicio($IDClub,$IDReserva,$IDReservaGeneralInvitado);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar Los invitados de un socio
	case "getinvitado":
		$IDSocio = SIMNet::req( "IDSocio" );
		$NumeroDocumento = SIMNet::req( "NumeroDocumento" );
		$FechaIngreso = SIMNet::req( "FechaIngreso" );
		$respuesta = SIMWebService::get_invitado($IDClub,$IDSocio,$NumeroDocumento, $FechaIngreso);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar Los invitados de un socio
	case "getmisinvitados":
		$IDSocio = SIMNet::req( "IDSocio" );
		$NumeroDocumento = SIMNet::req( "NumeroDocumento" );
		$respuesta = SIMWebService::get_mis_invitado($IDClub,$IDSocio,$NumeroDocumento);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar Los elementos de un servicio
	case "getelementos":	
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebService::get_elementos($IDClub,$IDSocio,$IDServicio);
		//inserta _log				
		//$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('getelementos','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar Las horas de un servicio
	case "gethoras":
	//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('get_horasI','IDClub: ".$IDClub. " IDSocio:" . $IDSocio . " . IDServicio: ".$IDServicio." Elemento: ".$IDElemento."','".json_encode($_GET)."')");
	
	
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebService::get_horas($IDClub,$IDSocio,$IDServicio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar Los campos de un servicio
	case "getcampos":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebService::get_campos($IDClub,$IDSocio,$IDServicio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar la disponibilidad de un elemento en una fecha, marca las horas con disponible si o no
	case "getdisponiblidadelemento":
		$IDElemento = SIMNet::req( "IDElemento" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		//$respuesta = SIMWebService::get_disponiblidad_elemento($IDClub,$IDElemento,$IDServicio,$Fecha);
		$respuesta = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"",$UnElemento=1);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar la disponibilidad de varios elementos de un servicio en una fecha, marca las horas con disponible si o no
	case "getdisponiblidadelementoservicio":		
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$IDElemento = SIMNet::req( "IDElemento" );
		$respuesta = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar la disponibilidad de los elementos en una fecha hora especifica
	case "getdisponiblidadfechahora":
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$Hora = SIMNet::req( "Hora" );
		$respuesta = SIMWebService::get_disponiblidad_fecha_hora($IDClub,$IDServicio,$Fecha,$Hora);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar las horas Dsiponibles consultando todos los elementos en una fecha especifica
	case "gethoradisponible":
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebService::get_hora_disponible($IDClub,$IDServicio,$Fecha,$Hora);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar la disponibilidad de los elementos en una fecha hora especifica
	case "setseparareserva":
		$IDSocio = $_POST["IDSocio"];
		$IDElemento = $_POST["IDElemento"];
		$IDServicio = $_POST["IDServicio"];
		$IDTipoReserva = $_POST["IDTipoReserva"];
		$Tee = $_POST["Tee"];
		$Fecha = $_POST["Fecha"]; 
		$Hora = $_POST["Hora"]; 		
		$respuesta = SIMWebService::set_separa_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,$Tee,$Fecha,$Hora,$IDTipoReserva);
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('setseparareserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	case "setliberareserva":
		$IDSocio = $_POST["IDSocio"];
		$IDElemento = $_POST["IDElemento"];
		$IDServicio = $_POST["IDServicio"];
		$Tee = $_POST["Tee"];
		$Fecha = $_POST["Fecha"]; 
		$Hora = $_POST["Hora"]; 		
		$respuesta = SIMWebService::set_libera_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,$Tee,$Fecha,$Hora);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Guardar Reserva General
	case "setreservageneral":
		$IDSocio = $_POST["IDSocio"];
		$IDElemento = $_POST["IDElemento"];
		$IDServicio = $_POST["IDServicio"];
		$Fecha = $_POST["Fecha"]; 
		$Hora = $_POST["Hora"]; 
		$Campos = $_POST["Campos"]; 
		$Invitados = $_POST["Invitados"]; 
		//Repeticion reserva
		$IDDisponibilidad = $_POST["IDDisponibilidad"]; 
		$Repetir = $_POST["Repetir"]; 
		$Periodo = $_POST["Periodo"];
		$RepetirFechaFinal = $_POST["RepetirFechaFinal"];  				
		//Auxiliar-Boleador
		$IDAuxiliar = $_POST["IDAuxiliar"];
		//Modalidad
		$IDTipoModalidadEsqui = $_POST["IDTipoModalidad"];
		//Tee
		$Tee = $_POST["Tee"];
		// Tipo Reserva (Dobles, sencillos, etc)
		$IDTipoReserva = $_POST["IDTipoReserva"];
		
		$respuesta = SIMWebService::set_reserva_generalV2($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,"","",$Tee,$IDDisponibilidad,$Repetir,$Periodo,$RepetirFechaFinal,$IDTipoModalidadEsqui,$IDAuxiliar,$IDTipoReserva);
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('set_reserva_generalV2','".json_encode($_POST)."','".json_encode($respuesta)."')");
	
		
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Guardar Reserva General
	case "setreservageneralv2":
		$IDSocio = $_POST["IDSocio"];
		$IDElemento = $_POST["IDElemento"];
		$IDServicio = $_POST["IDServicio"];
		$Fecha = $_POST["Fecha"]; 
		$Hora = $_POST["Hora"]; 
		$Campos = $_POST["Campos"]; 
		$Invitados = $_POST["Invitados"]; 
		//Repeticion reserva
		$IDDisponibilidad = $_POST["IDDisponibilidad"]; 
		$Repetir = $_POST["Repetir"]; 
		$Periodo = $_POST["Periodo"]; 
		$RepetirFechaFinal = $_POST["RepetirFechaFinal"];
		//Auxiliar-Boleador
		$IDAuxiliar = $_POST["IDAuxiliar"];
		//Modalidad
		$IDTipoModalidadEsqui = $_POST["IDTipoModalidad"];
		//Tee
		$Tee = $_POST["Tee"];
		// Tipo Reserva (Dobles, sencillos, etc)
		$IDTipoReserva = $_POST["IDTipoReserva"];
		
		$respuesta = SIMWebService::set_reserva_generalV2($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,"","",$Tee,$IDDisponibilidad,$Repetir,$Periodo,$RepetirFechaFinal,$IDTipoModalidadEsqui,$IDAuxiliar,$IDTipoReserva);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	
	//Eliminar Reserva
	case "eliminareservageneral":
		$IDSocio = $_POST["IDSocio"];
		$IDReserva = $_POST["IDReserva"];
		$respuesta = SIMWebService::elimina_reserva_general($IDClub,$IDSocio,$IDReserva,"");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar Campos de golf disponibles
	case "getdisponibilidadfecha":	
		$IDCampo = SIMNet::req( "IDCampo" );
		$Fecha = SIMNet::req( "Fecha" );
		$Hora = SIMNet::req( "Hora" );
		$respuesta = SIMWebService::get_disponibilidad_fecha($IDClub,$IDCampo,$Fecha,$Hora);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar Campos de golf disponibles
	case "getdisponibilidadcampo":		
		$IDCampo = SIMNet::req( "IDCampo" );
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebService::get_disponibilidad_campo($IDClub,$IDCampo,$Fecha);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Reservas del socio ultimas 15
	case "getreservasocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDReserva = SIMNet::req( "IDReserva" );
		$respuesta = SIMWebService::get_reservas_socio($IDClub,$IDSocio,15,$IDReserva);
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('getreservasocio','".json_encode($_GET)."','".json_encode($respuesta)."')");	
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Reservas del socio todas
	case "getreservasociotodas":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebService::get_reservas_socio($IDClub,$IDSocio,0);
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('getreservasocio','".json_encode($_GET)."','".json_encode($respuesta)."')");		
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Reservas asociadas
	case "getreservaasociada":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDReserva = SIMNet::req( "IDReserva" );
		$respuesta = SIMWebService::get_reserva_asociada($IDClub,$IDSocio,$IDReserva);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Verificar Grupos golf
	case "verificarsociogrupo":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebService::verificar_socio_grupo_fecha($IDClub,$IDSocio,$Fecha,$IDServicio);
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('verificarsociogrupo','".json_encode($_GET)."','".json_encode($respuesta)."')");		
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar fechas disponibles o no para reservas servicio
	case "getfechasdisponiblesservicio":
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebService::get_fecha_disponibilidad_servicio($IDClub,$IDServicio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	//Enviar auxiliares boleadores
	case "getauxiliares":
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$Hora = SIMNet::req( "Hora" );
		$respuesta = SIMWebService::get_auxiliares($IDClub,$IDServicio,$Fecha,$Hora);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Enviar modalidades club
	case "getmodalidades":
		$IDTipoModalidadEsqui = SIMNet::req( "IDTipoModalidadEsqui" );		
		$IDElemento = SIMNet::req( "IDElemento" );		
		$respuesta = SIMWebService::get_modalidades($IDClub,$IDTipoModalidadEsqui,$IDElemento);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Guardar Reserva Golf
	case "setreservagolf":
		$IDSocio = $_POST["IDSocio"];
		$IDCampo = $_POST["IDCampo"];
		$IDServicio = $_POST["IDServicio"];
		$Fecha = $_POST["Fecha"];
		$Hora = $_POST["Hora"];
		$Campos = $_POST["Campos"];
		$Invitados = $_POST["Jugadores"];
		$Tee = $_POST["Tee"];
		//$respuesta = SIMWebService::set_reserva_golf($IDClub,$IDSocio,$IDCampo,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,$Tee);
		$respuesta = SIMWebService::set_reserva_general($IDClub,$IDSocio,$IDCampo,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,"","",$Tee);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Guardar Tokenm
	case "settoken":
		$IDSocio = $_POST["IDSocio"];
		$Dispositivo = $_POST["Dispositivo"];
		$Token = $_POST["Token"];
		$respuesta = SIMWebService::set_token($IDClub,$IDSocio,$Dispositivo,$Token);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Guardar Tokenm
	case "setcambiarclave":
		$IDSocio = $_POST["IDSocio"];
		$Clave = $_POST["Clave"];
		$respuesta = SIMWebService::set_cambiar_clave($IDClub,$IDSocio,$Clave);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	
	
	//Recuperar clave de socio y enviarla al email
	case "getrecuperarclave":
		$email = SIMNet::req( "email" );
		//verificar si viene Email
		if( !empty( $email ) )
		{
			$sql_verifica = "SELECT * FROM Socio WHERE Email = '".$email ."' and IDClub = '".$IDClub."'";
			$qry_verifica = $dbo->query( $sql_verifica );
			if( $dbo->rows( $qry_verifica ) == 0 )
			{
				$message = "No encontrado";
			}//end if
			else{
				$datos_socio = $dbo->fetchArray( $qry_verifica );
				
				$nueva_clave =substr($datos_socio[Nombre],0,3).rand(1,20000).substr($datos_socio[Apellido],2,2);
				$nueva_clave = strtoupper($nueva_clave);
				
				//Enviamos el correo al usario de conectar
				$dest = trim( $email );
		
				$head  = "From: " . "info@22cero2.com" . "\r\n";	  
				$head .= "To: " . $dest . " \r\n";
		  
				// Ahora creamos el cuerpo del mensaje
				$msg  = "Mensaje desde la Aplicación de Clubes \n\n";	  	       	  
				$msg .= "Cordial Saludo, \n\n Le recordamos que los datos de acceso al sistema de clubes es: \n Usuario: ".$email."\n Clave: ".$nueva_clave."\n\n Notificaciones automaticas Clubes.";
		  
				// Finalmente enviamos el mensaje
				mail( $dest, "Contacto desde la Aplicación de Clubes", $msg, $head );
				
				//actualizo clave
				$sql_update_clave = $dbo->query("Update Socio Set Clave =  sha1('".$nueva_clave."') Where Email = '".$email."' and IDClub = '".$IDClub."'");
				
				$message = "Clave enviada correctamente";
				
				die( json_encode( array( 'success' => true, 'message'=>$message, 'response' => $response, 'date' => $nowserver ) ) );	
				exit;
			}
		}//end if
		else{
			$message = "Error faltan parametros";
			die( json_encode( array(  'success' => false, 'message'=>$message, 'response' => $response, 'update' => $update, 'date' => $nowserver ) ) );
		}
		
		die( json_encode( array(  'success' => false, 'message'=>$message, 'response' => $response, 'update' => $update, 'date' => $nowserver ) ) );
	break;


	default:
		die( json_encode( array(  'success' => false, 'message'=>'no action', 'response' => '', 'date' => $nowserver ) ) );
	break;

}///end sw
?>
