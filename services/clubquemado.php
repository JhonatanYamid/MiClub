<?php


/**** SERVICIOS JSON PARA APPS MOVILES ******/
/**** Creación: Jorge Chirivi ******/
/**** Fecha de Creación: 17 de Septiembre de 2015 ******/
/**** Ultima Modificación': 11 de Febrero de 2016 Jorge Chirivi ******/
/**** Comentarios ULtima Modificación: ******/
/**** Scripts Iniciales ******/



require( LIBDIR . "SIMWebServiceQuemado.inc.php" );
header("Content-type: application/json; charset=utf-8");



if($_GET["AppVersion"])
	$AppVersion = SIMNet::req( "AppVersion" );
else
	$AppVersion = $_POST["AppVersion"];




//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('".$_SERVER["REMOTE_ADDR"]."','".json_encode($_POST)."','".json_encode($_GET)."')");
if($_POST["action"]=="gettoken"){
	$Usuario = $_POST["Usuario"];
	$Clave = $_POST["Clave"];
	$respuesta = SIMWebServiceToken::get_token($Usuario,$Clave);
	//inserta _log
	//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_token','".json_encode($_POST)."','".json_encode($respuesta)."')");
	die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
	exit;
}



if(!empty($_GET["TokenID"]) || !empty($_POST["TokenID"]) || $AppVersion>=26 ){
	if(!empty($_GET["TokenID"]))
		$Token=$_GET["TokenID"];
	else
		$Token=$_POST["TokenID"];

	//Valido el Token
	$respuesta = SIMWebServiceToken::valida_token($Token);
	if(!$respuesta["success"]){
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	}
	//inserta _log
	//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('valida_token','".json_encode($_GET)."','".json_encode($respuesta)."')");
}


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


$nowserver = date("Y-m-d H:i:s");

//Validar KEY en vesiones inferiores a 26
if( ($key <> KEY_SERVICES && (int)$AppVersion<26 ) || empty($IDClub))
	exit;

if($action!="siteoption" && $action!="getsocio")
	$IDClub = 94;


//Verificar Acciones
switch( $action ){


	//Validar Socio
	case "getsocio":
		$email = SIMNet::req( "email" );
		$clave = SIMNet::req( "clave" );
		$AppVersion = SIMNet::req( "AppVersion" );
		$Data = SIMNet::req( "data" );
		$respuesta = SIMWebServiceQuemado::valida_socio($email,$clave,$IDClub,$AppVersion,$Data);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getsocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getsocioencript":
		$email = SIMNet::req( "email" );
		$clave = SIMNet::req( "clave" );
		$AppVersion = SIMNet::req( "AppVersion" );

		//Validar Socio
		$respuesta = SIMWebServiceQuemado::valida_socio($email,$clave,$IDClub,$AppVersion);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getsocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Validar Socio
	case "getsocioaccion":
		$Accion = SIMNet::req( "Accion" );
		$respuesta = SIMWebServiceQuemado::valida_socio_accion($IDClub,$Accion);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getsocio','".$_SERVER['HTTPS']."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getperfil":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_perfil($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getperfil','".$_SERVER['HTTPS']."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Validar Usuario administrador (porteria)
	case "getusuarioweb":
		$email = SIMNet::req( "email" );
		$clave = SIMNet::req( "clave" );
		//Validar Usuario
		$respuesta = SIMWebServiceQuemado::valida_usuario_web($email,$clave,$IDClub,"","");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getusuarioweb','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar splash del club
	case "getbannerapp":
		//Traer banner
		$respuesta = SIMWebServiceQuemado::get_banner_app($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar splash de empleados
	case "getbannerappempleado":
		//Traer banner
		$respuesta = SIMWebServiceQuemado::get_banner_app_empleado($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar la configuracion del club, nombre, color, etc
	case "siteoption":
		$TipoApp = SIMNet::req( "TipoApp" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDSocio = SIMNet::req( "IDSocio" );


		if($IDClub=="8" ):
			//$respuesta_version = SIMWebServiceQuemado::solicitar_cerrar_sesion($IDClub);
		endif;

		if($IDClub=="8" ):
			//$respuesta_version = SIMWebServiceQuemado::verifica_version_app($IDClub,$_GET["AppVersion"],$_GET,$TipoApp);
		endif;
		if($TipoApp=="Empleado"):
			$respuesta = SIMWebServiceQuemado::get_app_empleado($IDClub,$IDUsuario);
			//inserta _log
			//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('siteoptionempleado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		else:
			$respuesta = SIMWebServiceQuemado::get_club($IDClub,$IDSocio);
			//inserta _log
			//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('siteoption','".json_encode($_GET)."','".json_encode($respuesta)."')");
		endif;
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar la configuracion del app de empleados
	case "siteoptionempleado":
		$TipoApp = SIMNet::req( "TipoApp" );
		//if($IDClub=="8" || $IDClub=="1"):
			//$respuesta_version = SIMWebServiceQuemado::verifica_version_app($IDClub,$_GET["AppVersion"],$_GET,$TipoApp);
		//endif;
		$respuesta = SIMWebServiceQuemado::get_app_empleado($IDClub);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('siteoptionempleado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar secciones de noticia del club o del socio
	case "getsubmodulo":
		$IDModulo = SIMNet::req( "IDModulo" );
		$respuesta = SIMWebServiceQuemado::get_submodulo($IDClub, $IDModulo);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar secciones de noticia del club o del socio
	case "getseccion":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$TipoApp=SIMNet::req( "TipoApp" );
		$respuesta = SIMWebServiceQuemado::get_seccion($IDClub, $IDSocio,$IDUsuario,$TipoApp);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccion','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getseccionnoticia2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$TipoApp=SIMNet::req( "TipoApp" );
		$respuesta = SIMWebServiceQuemado::get_seccion($IDClub, $IDSocio,$IDUsuario,$TipoApp,"2");
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccionnoticia2','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getseccionnoticia3":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$TipoApp=SIMNet::req( "TipoApp" );
		$respuesta = SIMWebServiceQuemado::get_seccion($IDClub, $IDSocio,$IDUsuario,$TipoApp,"3");
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccionnoticia2','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar secciones de eventos del club o del socio
	case "getseccionevento":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_seccionevento($IDClub, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar secciones de eventos del club o del socio
	case "getseccionevento2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_seccionevento($IDClub, $IDSocio,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar secciones de galerias del club o del socio
	case "getsecciongaleria":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_secciongaleria($IDClub, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar secciones del club o del socio completas de noticias, eventos y galerias
	case "getseccionclub":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_seccion_club($IDClub, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//ConfiguracionClasificado
	case "getconfiguracionclasificado":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_clasificado($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	case "getcatagoriaclasificado":
		$respuesta = SIMWebServiceQuemado::get_categoria_clasificado($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getclasificado":
		$Tag= SIMNet::req( "Tag" );
		$IDCategoria = SIMNet::req( "IDCategoria" );
		$IDClasificado = SIMNet::req( "IDClasificado" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDEstadoClasificado = SIMNet::req( "IDEstadoClasificado" );
		$respuesta = SIMWebServiceQuemado::get_clasificado($IDClub,$IDCategoria,$IDClasificado,$Tag,$IDSocio,$IDEstadoClasificado);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setpreguntaclasificado":
		$IDSocio = $_POST["IDSocio"];
		$IDClasificado = $_POST["IDClasificado"];
		$Pregunta = $_POST["Pregunta"];
		$respuesta = SIMWebServiceQuemado::set_pregunta_clasificado($IDClub,$IDClasificado,$Pregunta,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreguntaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setrespuestaclasificado":
		$IDSocio = $_POST["IDSocio"];
		$IDClasificado = $_POST["IDClasificado"];
		$IDPregunta = $_POST["IDPregunta"];
		$Respuesta = $_POST["Respuesta"];
		$respuesta = SIMWebServiceQuemado::set_respuesta_clasificado($IDClub,$IDClasificado,$IDPregunta,$Respuesta,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setclasificado":

		if(count($_FILES)>0)
			$_POST = array_map('utf8_encode', $_POST);

		$IDSocio = $_POST["IDSocio"];
		$IDCategoria = $_POST["IDCategoria"];
		$Nombre = $_POST["Nombre"];
		$Descripcion = $_POST["Descripcion"];
		$Telefono = $_POST["Telefono"];
		$Email = $_POST["Email"];
		$Valor = $_POST["Valor"];
		$FechaInicio = $_POST["FechaInicio"];
		$FechaFin = $_POST["FechaFin"];
		$respuesta = SIMWebServiceQuemado::set_clasificado($IDClub,$IDSocio,$IDCategoria,$Nombre,$Descripcion,$Telefono,$Email,$Valor,$FechaInicio,$FechaFin,$_FILES);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "seteditaclasificado":

		if(count($_FILES)>0)
			$_POST = array_map('utf8_encode', $_POST);

		$IDSocio = $_POST["IDSocio"];
		$IDClasificado = $_POST["IDClasificado"];
		$IDEstadoClasificado = $_POST["IDEstadoClasificado"];
		$IDCategoria = $_POST["IDCategoria"];
		$Nombre = $_POST["Nombre"];
		$Descripcion = $_POST["Descripcion"];
		$Telefono = $_POST["Telefono"];
		$Email = $_POST["Email"];
		$Valor = $_POST["Valor"];
		$FechaInicio = $_POST["FechaInicio"];
		$FechaFin = $_POST["FechaFin"];
		$UrlFoto1 = $_POST["UrlFoto1"];
		$UrlFoto2 = $_POST["UrlFoto2"];
		$UrlFoto3 = $_POST["UrlFoto3"];
		$UrlFoto4 = $_POST["UrlFoto4"];
		$UrlFoto5 = $_POST["UrlFoto5"];
		$respuesta = SIMWebServiceQuemado::set_edita_clasificado($IDClub,$IDSocio,$IDClasificado,$IDEstadoClasificado,$IDCategoria,$Nombre,$Descripcion,$Telefono,$Email,$Valor,$FechaInicio,$FechaFin,$_FILES,$UrlFoto1,$UrlFoto2,$UrlFoto3,$UrlFoto4,$UrlFoto5);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('seteditaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Clasificados 2
	//ConfiguracionClasificado
	case "getconfiguracionclasificado2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_clasificado2($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado2','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	case "getcatagoriaclasificado2":
		$respuesta = SIMWebServiceQuemado::get_categoria_clasificado2($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getclasificado2":
		$Tag= SIMNet::req( "Tag" );
		$IDCategoria = SIMNet::req( "IDCategoria" );
		$IDClasificado = SIMNet::req( "IDClasificado" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDEstadoClasificado = SIMNet::req( "IDEstadoClasificado" );
		$respuesta = SIMWebServiceQuemado::get_clasificado2($IDClub,$IDCategoria,$IDClasificado,$Tag,$IDSocio,$IDEstadoClasificado);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getclasificado2','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setpreguntaclasificado2":
		$IDSocio = $_POST["IDSocio"];
		$IDClasificado = $_POST["IDClasificado"];
		$Pregunta = $_POST["Pregunta"];
		$respuesta = SIMWebServiceQuemado::set_pregunta_clasificado2($IDClub,$IDClasificado,$Pregunta,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreguntaclasificado2','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setrespuestaclasificado2":
		$IDSocio = $_POST["IDSocio"];
		$IDClasificado = $_POST["IDClasificado"];
		$IDPregunta = $_POST["IDPregunta"];
		$Respuesta = $_POST["Respuesta"];
		$respuesta = SIMWebServiceQuemado::set_respuesta_clasificado2($IDClub,$IDClasificado,$IDPregunta,$Respuesta,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestaclasificado2','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setclasificado2":
		$IDSocio = $_POST["IDSocio"];
		$IDCategoria = $_POST["IDCategoria"];
		$Nombre = $_POST["Nombre"];
		$Descripcion = $_POST["Descripcion"];
		$Respuestas = $_POST["Respuestas"];
		$respuesta = SIMWebServiceQuemado::set_clasificado2($IDClub,$IDSocio,$IDCategoria,$Nombre,$Descripcion,$Respuestas,$_FILES);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "seteditaclasificado2":
		$IDSocio = $_POST["IDSocio"];
		$IDClasificado = $_POST["IDClasificado"];
		$IDEstadoClasificado = $_POST["IDEstadoClasificado"];
		$IDCategoria = $_POST["IDCategoria"];
		$Nombre = $_POST["Nombre"];
		$Descripcion = $_POST["Descripcion"];
		$Respuestas = $_POST["Respuestas"];
		$UrlFoto1 = $_POST["UrlFoto1"];
		$UrlFoto2 = $_POST["UrlFoto2"];
		$UrlFoto3 = $_POST["UrlFoto3"];
		$UrlFoto4 = $_POST["UrlFoto4"];
		$UrlFoto5 = $_POST["UrlFoto5"];
		$respuesta = SIMWebServiceQuemado::set_edita_clasificado2($IDClub,$IDSocio,$IDClasificado,$IDEstadoClasificado,$IDCategoria,$Nombre,$Descripcion,$Respuestas,$_FILES,$UrlFoto1,$UrlFoto2,$UrlFoto3,$UrlFoto4,$UrlFoto5);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('seteditaclasificado2','".json_encode($_POST)."','".json_encode($respuesta)."')");

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	//FIN Clasificados 2



	case "getconfiguracionofertas":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_ofertas($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionofertas','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setoferta":
		$IDSocio = $_POST["IDSocio"];
		$IDIndustria = $_POST["IDIndustria"];
		$TipoContrato = $_POST["TipoContrato"];
		$NombreEmpresa = $_POST["NombreEmpresa"];
		$PublicarEmpresa = $_POST["PublicarEmpresa"];
		$Cargo = $_POST["Cargo"];
		$Ciudad = $_POST["Ciudad"];
		$NombreEncargado = $_POST["NombreEncargado"];
		$CorreoContacto = $_POST["CorreoContacto"];
		$DescripcionCargo = $_POST["DescripcionCargo"];
		$ComentarioAdicional = $_POST["ComentarioAdicional"];
		$FechaInicio = $_POST["FechaInicio"];
		$FechaFin = $_POST["FechaFin"];
		$respuesta = SIMWebServiceQuemado::set_oferta($IDClub,$IDSocio,$IDIndustria,$TipoContrato,$NombreEmpresa,$PublicarEmpresa,$Cargo,$Ciudad,$NombreEncargado,$CorreoContacto,$DescripcionCargo,$ComentarioAdicional,$FechaInicio,$FechaFin);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setoferta','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "seteditaoferta":
		$IDOferta = $_POST["IDOferta"];
		$IDEstadoOferta = $_POST["IDEstadoOferta"];
		$IDSocio = $_POST["IDSocio"];
		$IDIndustria = $_POST["IDIndustria"];
		$TipoContrato = $_POST["TipoContrato"];
		$NombreEmpresa = $_POST["NombreEmpresa"];
		$PublicarEmpresa = $_POST["PublicarEmpresa"];
		$Cargo = $_POST["Cargo"];
		$Ciudad = $_POST["Ciudad"];
		$NombreEncargado = $_POST["NombreEncargado"];
		$CorreoContacto = $_POST["CorreoContacto"];
		$DescripcionCargo = $_POST["DescripcionCargo"];
		$ComentarioAdicional = $_POST["ComentarioAdicional"];
		$FechaInicio = $_POST["FechaInicio"];
		$FechaFin = $_POST["FechaFin"];
		$respuesta = SIMWebServiceQuemado::set_edita_oferta($IDClub,$IDOferta,$IDEstadoOferta,$IDSocio,$IDIndustria,$TipoContrato,$NombreEmpresa,$PublicarEmpresa,$Cargo,$Ciudad,$NombreEncargado,$CorreoContacto,$DescripcionCargo,$ComentarioAdicional,$FechaInicio,$FechaFin);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('seteditaoferta','".json_encode($_POST)."','".json_encode($respuesta)."')");

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setaplicaroferta":

	if(count($_FILES)>0)
		$_POST = array_map('utf8_encode', $_POST);

		$IDSocio = $_POST["IDSocio"];
		$IDOferta = $_POST["IDOferta"];
		$NombreRecomendado = $_POST["NombreRecomendado"];
		$Telefono = $_POST["Telefono"];
		$CorreoElectronico = $_POST["CorreoElectronico"];

		$respuesta = SIMWebServiceQuemado::set_aplicar_oferta($IDClub,$IDSocio,$IDOferta,$NombreRecomendado,$Telefono,$CorreoElectronico,$_FILES);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setaplicaroferta','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getofertas":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDEstadoOferta = SIMNet::req( "IDEstadoOferta" );
		$IDOferta = SIMNet::req( "IDOferta" );
		$respuesta = SIMWebServiceQuemado::get_ofertas($IDClub, $IDSocio, $IDEstadoOferta);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccion','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Objetos Perdidos
	//Configuracion Objetos Perdidos
	case "getconfiguracionobjetosperdidos":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_objetos_perdidos($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	case "getcategoriaobjetosperdidos":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_categoria_objetos_perdidos($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getobjetosperdidos":
		$Tag= SIMNet::req( "Tag" );
		$IDCategoria = SIMNet::req( "IDCategoria" );
		$IDObjetoPerdido = SIMNet::req( "IDObjetoPerdido" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDEstadoObjetosPerdidos = SIMNet::req( "IDEstadoObjetosPerdidos" );
		$respuesta = SIMWebServiceQuemado::get_objetos_perdidos($IDClub,$IDCategoria,$IDObjetoPerdido,$Tag,$IDSocio,$IDEstadoObjetosPerdidos);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setpertenencia":
		$IDSocio = $_POST["IDSocio"];
		$IDObjetoPerdido = $_POST["IDObjetoPerdido"];
		$respuesta = SIMWebServiceQuemado::set_pertenencia($IDClub,$IDObjetoPerdido,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpertenencia','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getestadoobjetosperdidos":
		$respuesta = SIMWebServiceQuemado::get_estado_objetos_perdidos($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setobjetoperdido":
		$IDUsuario = $_POST["IDUsuario"];
		$IDCategoriaObjetosPerdidos = $_POST["IDCategoriaObjetosPerdidos"];
		$Nombre = $_POST["Nombre"];
		$Descripcion = $_POST["Descripcion"];
		$FechaInicio = $_POST["FechaInicio"];
		$IDEstadoObjetosPerdidos = $_POST["FechaInicio"];
		$respuesta = SIMWebServiceQuemado::set_objeto_perdido($IDClub,$IDSocio,$IDCategoriaObjetosPerdidos,$Nombre,$Descripcion,$FechaInicio,$IDUsuario,$_FILES);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "seteditaobjetoperdido":
	if(count($_FILES)>0)
		$_POST = array_map('utf8_encode', $_POST);

		$IDUsuario = $_POST["IDUsuario"];
		$IDObjetoPerdido = $_POST["IDObjetoPerdido"];
		$IDEstadoObjetosPerdidos = $_POST["IDEstadoObjetosPerdidos"];
		$IDCategoriaObjetosPerdidos = $_POST["IDCategoriaObjetosPerdidos"];
		$Nombre = $_POST["Nombre"];
		$Descripcion = $_POST["Descripcion"];
		$FechaInicio = $_POST["FechaInicio"];
		$UrlFoto1 = $_POST["UrlFoto1"];
		$UrlFoto2 = $_POST["UrlFoto2"];
		$UrlFoto3 = $_POST["UrlFoto3"];
		$UrlFoto4 = $_POST["UrlFoto4"];
		$UrlFoto5 = $_POST["UrlFoto5"];
		$respuesta = SIMWebServiceQuemado::set_edita_objeto_perdido($IDClub,$IDObjetoPerdido,$IDEstadoObjetosPerdidos,$IDCategoriaObjetosPerdidos,$Nombre,$Descripcion,$FechaInicio,$IDUsuario,$_FILES,$UrlFoto1,$UrlFoto2,$UrlFoto3,$UrlFoto4,$UrlFoto5);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('$IDEstadoObjetosPerdidos','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "setentregaobjetoperdido":

	if(count($_FILES)>0)
		$_POST = array_map('utf8_encode', $_POST);


		$IDUsuario = $_POST["IDUsuario"];
		$IDSocio = $_POST["IDSocio"];
		$IDObjetoPerdido = $_POST["IDObjetoPerdido"];
		$TipoReclamante = $_POST["IDEstadoObjetosPerdidos"];
		$NombreParticular = $_POST["IDCategoriaObjetosPerdidos"];
		$DocumentoParticular = $_POST["Nombre"];
		$IDTipoDocumentoParticular = $_POST["IDTipoDocumentoParticular"];
		$Observaciones = $_POST["Observaciones"];
		$respuesta = SIMWebServiceQuemado::set_entrega_objeto_perdido($IDClub,$IDSocio,$IDObjetoPerdido,$TipoReclamante,$NombreParticular,$DocumentoParticular,$IDTipoDocumentoParticular,$Observaciones,$IDUsuario,$_FILES);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('$IDEstadoObjetosPerdidos','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getmissolictudesobjetosperdidos":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_mis_solictudes_objetos_perdidos($IDClub,$IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Fin Objetos perdidos



	case "getcategoriabeneficio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_categoria_beneficio($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getbeneficio":
		$Tag= SIMNet::req( "Tag" );
		$IDCategoria = SIMNet::req( "IDCategoria" );
		$IDBeneficio = SIMNet::req( "IDBeneficio" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_beneficio($IDClub,$IDCategoria,$IDBeneficio,$Tag,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getseccionsociobeneficio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_seccion_socio_beneficio($IDClub, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setpreferenciasbeneficio":
		$IDSocio = $_POST["IDSocio"];
		$SeccionesBeneficio = $_POST["SeccionesBeneficio"];
		$respuesta = SIMWebServiceQuemado::set_preferencias_beneficio($IDClub, $IDSocio, $SeccionesBeneficio);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreferencias','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setlistaespera":
		$IDSocio = $_POST["IDSocio"];
		$IDServicio = $_POST["IDServicio"];
		$IDServicioElemento = $_POST["IDServicioElemento"];
		$IDAuxiliar = $_POST["IDAuxiliar"];
		$FechaInicio = $_POST["FechaInicio"];
		$FechaFin = $_POST["FechaFin"];
		$HoraInicio = $_POST["HoraInicio"];
		$HoraFin = $_POST["HoraFin"];
		$AceptoTerminos = $_POST["AceptoTerminos"];
		$Celular = $_POST["Celular"];
		$Tipo = $_POST["Tipo"];
		$respuesta = SIMWebServiceQuemado::set_lista_espera($IDClub,$IDSocio,$IDServicio,$IDServicioElemento,$IDAuxiliar,$FechaInicio,$FechaFin,$HoraInicio,$HoraFin,$AceptoTerminos,$Celular,$Tipo);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreguntaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar areas del club
	case "getareaclub":
		$respuesta = SIMWebServiceQuemado::get_area_club($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar noticias del club o del socio o de la seccion o busqueda
	case "getnoticias":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDSeccion = SIMNet::req( "IDSeccion" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_noticias($IDClub, $IDSeccion, $IDSocio, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getnoticias2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDSeccion = SIMNet::req( "IDSeccion" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_noticias($IDClub, $IDSeccion, $IDSocio, $Tag,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getnoticias3":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDSeccion = SIMNet::req( "IDSeccion" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_noticias($IDClub, $IDSeccion, $IDSocio, $Tag,"3");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar noticias del club o del socio o de la seccion o busqueda
	case "getnoticiasempleados":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDSeccion = SIMNet::req( "IDSeccion" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_noticias_empleados($IDClub, $IDSeccion, $IDUsuario, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar eventos del club o del socio o de la seccion o busqueda
	case "geteventos":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Fecha = SIMNet::req( "Fecha" );

		if(!empty(SIMNet::req( "IDSeccion" ))):
			$IDSeccionEvento = SIMNet::req( "IDSeccion" );
		endif;

		if(!empty(SIMNet::req( "IDSeccionEvento" ))):
			$IDSeccionEvento = SIMNet::req( "IDSeccionEvento" );
		endif;

		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_eventos($IDClub, $IDSeccionEvento, $IDSocio, $Tag, $Fecha);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "geteventos2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Fecha = SIMNet::req( "Fecha" );

		if(!empty(SIMNet::req( "IDSeccion" ))):
			$IDSeccionEvento = SIMNet::req( "IDSeccion" );
		endif;

		if(!empty(SIMNet::req( "IDSeccionEvento" ))):
			$IDSeccionEvento = SIMNet::req( "IDSeccionEvento" );
		endif;

		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_eventos($IDClub, $IDSeccionEvento, $IDSocio, $Tag, $Fecha,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Guardar campos formulario Evento
	case "setformularioevento":
		$IDEvento = $_POST["IDEvento"];
		$IDSocio = $_POST["IDSocio"];
		$IDSocioBeneficiario = $_POST["IDSocioBeneficiario"];
		$ValoresFormulario = $_POST["ValoresFormulario"];
		//$OtrosDatosFormulario = "NumeroPersonas: " . $_POST["personas"] . " NombreInvitados" . $_POST["nomInvitados"] . " Comentarios:".$_POST["comentarios"];
		$OtrosDatosFormulario = "";
		$respuesta = SIMWebServiceQuemado::set_formulario_evento($IDClub,$IDEvento,$IDSocio,$IDSocioBeneficiario,$ValoresFormulario,$OtrosDatosFormulario);

			if(!empty($_POST["paginaretorno"]))
				header("Location: ".$_POST["paginaretorno"]."?mensaje=".$respuesta[message]);

		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setformularioevento','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );

		exit;
	break;

	//Guardar campos formulario Evento
	case "setformularioevento2":
		$IDEvento = $_POST["IDEvento"];
		$IDSocio = $_POST["IDSocio"];
		$IDSocioBeneficiario = $_POST["IDSocioBeneficiario"];
		$ValoresFormulario = $_POST["ValoresFormulario"];
		$OtrosDatosFormulario = "NumeroPersonas: " . $_POST["personas"] . " NombreInvitados" . $_POST["nomInvitados"] . " Comentarios:".$_POST["comentarios"];
		$respuesta = SIMWebServiceQuemado::set_formulario_evento($IDClub,$IDEvento,$IDSocio,$IDSocioBeneficiario,$ValoresFormulario,$OtrosDatosFormulario,"2");

			if(!empty($_POST["paginaretorno"]))
				header("Location: ".$_POST["paginaretorno"]."?mensaje=".$respuesta[message]);

			$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setformularioevento2','".json_encode($_POST)."','".json_encode($respuesta)."')");
			die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );

		exit;
	break;




	//Enviar eventos del club o del socio o de la seccion o busqueda
	case "geteventosempleados":
		$IDUsuario = SIMNet::req( "IDUsuario" );

		if(!empty(SIMNet::req( "IDSeccion" ))):
			$IDSeccionEvento = SIMNet::req( "IDSeccion" );
		endif;

		if(!empty(SIMNet::req( "IDSeccionEvento" ))):
			$IDSeccionEvento = SIMNet::req( "IDSeccionEvento" );
		endif;

		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_eventos_empleados($IDClub, $IDSeccionEvento, $IDUsuario, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar directorio del club
	case "getdirectorio":
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_directorio($IDClub,$Tag);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdirectorio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getcategoriadirectorio":
		$respuesta = SIMWebServiceQuemado::get_categoria_directorio($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar directorio del club
	case "getdirectoriosocio":
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_directorio_socio($IDClub,$Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	case "getcategoriadirectoriosocio":
		$respuesta = SIMWebServiceQuemado::get_categoria_directorio_socio($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Calificacion directorio club
	case "setcalificaciondirectorio":
		$IDSocio = $_POST["IDSocio"];
		$IDDirectorio = $_POST["IDDirectorio"];
		$Comentario = utf8_decode($_POST["Comentario"]);
		$Calificacion = utf8_decode($_POST["Calificacion"]);
		$respuesta = SIMWebServiceQuemado::set_calificacion_directorio($IDClub,$IDSocio, $IDDirectorio, $Comentario, $Calificacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Calificacion directorio socios
	case "setcalificaciondirectoriosocios":
		$IDSocio = $_POST["IDSocio"];
		$IDDirectorio = $_POST["IDDirectorioSocio"];
		$Comentario = utf8_decode($_POST["Comentario"]);
		$Calificacion = utf8_decode($_POST["Calificacion"]);
		$respuesta = SIMWebServiceQuemado::set_calificacion_directorio_socios($IDClub,$IDSocio, $IDDirectorio, $Comentario, $Calificacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Consultar comentario de Calificacion directorio socios
	case "getcalificaciondirectorio":
		$IDDirectorio = SIMNet::req( "IDDirectorio" );
		$respuesta = SIMWebServiceQuemado::get_calificacion_directorio($IDClub,$IDDirectorio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Consultar comentario de Calificacion directorio socios
	case "getcalificaciondirectoriosocios":
		$IDDirectorio = SIMNet::req( "IDDirectorio" );
		$respuesta = SIMWebServiceQuemado::get_calificacion_directorio_socios($IDClub,$IDDirectorio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar Productos Domicilios
	case "getproducto":
		$Tag= SIMNet::req( "Tag" );
		$IDProducto = SIMNet::req( "IDProducto" );
		$respuesta = SIMWebServiceQuemado::get_producto($IDClub,$IDProducto ,$Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Productos Domicilios
	case "getproductocategoria":
		$Tag= SIMNet::req( "Tag" );
		$IDCategoria = SIMNet::req( "IDCategoria" );
		$respuesta = SIMWebServiceQuemado::get_producto_categoria($IDClub,$IDCategoria ,$Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Eliminar Pedido
	case "eliminarpedido":
		$IDSocio = $_GET["IDSocio"];
		$IDDomicilio = $_GET["IDDomicilio"];
		$respuesta = SIMWebServiceQuemado::elimina_pedido($IDClub,$IDSocio,$IDDomicilio);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminarpedido','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Productos Domicilios
	case "getdomiciliosocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_domicilio_socio($IDClub,$IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//ConfiguracionDomicilio
	case "getconfiguraciondomicilio":
		if($IDClub=="7" ):
			$respuesta_version = SIMWebServiceQuemado::verifica_version_app($IDClub,$_GET["AppVersion"],$_GET,$TipoApp);
		endif;
		$Fecha = SIMNet::req( "Fecha" );
		$IDSocio = SIMNet::req( "IDSocio" );

		$respuesta = SIMWebServiceQuemado::get_configuracion_domicilio($IDClub,$IDSocio,$Fecha);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Fechas Disponibles para domicilios
	case "getfechasdomicilio":
		$respuesta = SIMWebServiceQuemado::get_fechas_domicilio($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Horas Disponibles para domicilios
	case "gethorasentrega":
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebServiceQuemado::get_horas_entrega($IDClub,$Fecha);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gethorasentrega','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Productos Domicilios
	case "getdomiciliodetalle":
		$IDDomicilio = SIMNet::req( "IDDomicilio" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_domicilio_detalle($IDClub,$IDDomicilio,$IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar domicilio Domicilios
	case "setdomicilio":
		$IDClub = $_POST["IDClub"];
		$IDSocio = $_POST["IDSocio"];
		$HoraEntrega = $_POST["HoraEntrega"];
		$ComentariosSocio =$_POST["ComentariosSocio"];
		$DetallePedido = $_POST["DetallePedido"];
		$Celular = $_POST["Celular"];
		$Direccion = $_POST["Direccion"];
		$ValorDomicilio = $_POST["ValorDomicilio"];
		$FormaPago = $_POST["FormaPago"];


		//Por si viene de la pagina
		if(empty($DetallePedido)):
			$HoraEntrega = $_POST["FechaDomicilio"] . " " .$_POST["HoraEntrega"];
			$contador=0;
			foreach($_POST["IDProductos"] as $id_producto):
				$array_productos[$contador]["IDProducto"]=$id_producto;
				$array_productos[$contador]["Cantidad"]=$_POST["cantidad".$id_producto];
				$array_productos[$contador]["ValorUnitario"]=$_POST["PrecioProducto".$id_producto];
				$contador++;
			endforeach;
			$array_productos = json_encode($array_productos);
			$DetallePedido = $array_productos;
		endif;

		$respuesta = SIMWebServiceQuemado::set_domicilio($IDClub,$IDSocio,$HoraEntrega,$ComentariosSocio,$DetallePedido,$Celular,$Direccion,$ValorDomicilio,$FormaPago);



		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setdomicilio','".json_encode($_POST)."','".json_encode($respuesta)."')");

		if(!empty($_POST["paginaretorno"]))
			header("Location: ".$_POST["paginaretorno"]);


		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Domicilios 2
	//Enviar Productos Domicilios
	case "getproducto2":
		$Tag= SIMNet::req( "Tag" );
		$IDProducto = SIMNet::req( "IDProducto" );
		$respuesta = SIMWebServiceQuemado::get_producto($IDClub,$IDProducto ,$Tag,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Productos Domicilios
	case "getproductocategoria2":
		$Tag= SIMNet::req( "Tag" );
		$IDCategoria = SIMNet::req( "IDCategoria" );
		$respuesta = SIMWebServiceQuemado::get_producto_categoria($IDClub,$IDCategoria ,$Tag,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Eliminar Pedido
	case "eliminarpedido2":
		$IDSocio = $_GET["IDSocio"];
		$IDDomicilio = $_GET["IDDomicilio"];
		$respuesta = SIMWebServiceQuemado::elimina_pedido($IDClub,$IDSocio,$IDDomicilio,"","","2");
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminarpedido','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Productos Domicilios
	case "getdomiciliosocio2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_domicilio_socio($IDClub,$IDSocio,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//ConfiguracionDomicilio
	case "getconfiguraciondomicilio2":
		if($IDClub=="7" ):
			$respuesta_version = SIMWebServiceQuemado::verifica_version_app($IDClub,$_GET["AppVersion"],$_GET,$TipoApp);
		endif;
		$Fecha = SIMNet::req( "Fecha" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_domicilio($IDClub,$IDSocio,$Fecha,"2");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Fechas Disponibles para domicilios
	case "getfechasdomicilio2":
		$respuesta = SIMWebServiceQuemado::get_fechas_domicilio($IDClub,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Horas Disponibles para domicilios
	case "gethorasentrega2":
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebServiceQuemado::get_horas_entrega($IDClub,$Fecha,"2");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gethorasentrega','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Productos Domicilios
	case "getdomiciliodetalle2":
		$IDDomicilio = SIMNet::req( "IDDomicilio" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_domicilio_detalle($IDClub,$IDDomicilio,$IDSocio,"","2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar domicilio Domicilios
	case "setdomicilio2":
		$IDClub = $_POST["IDClub"];
		$IDSocio = $_POST["IDSocio"];
		$HoraEntrega = $_POST["HoraEntrega"];
		$ComentariosSocio =$_POST["ComentariosSocio"];
		$DetallePedido = $_POST["DetallePedido"];
		$Celular = $_POST["Celular"];
		$Direccion = $_POST["Direccion"];
		$ValorDomicilio = $_POST["ValorDomicilio"];
		$FormaPago = $_POST["FormaPago"];


		//Por si viene de la pagina
		if(empty($DetallePedido)):
			$HoraEntrega = $_POST["FechaDomicilio"] . " " .$_POST["HoraEntrega"];
			$contador=0;
			foreach($_POST["IDProductos"] as $id_producto):
				$array_productos[$contador]["IDProducto"]=$id_producto;
				$array_productos[$contador]["Cantidad"]=$_POST["cantidad".$id_producto];
				$array_productos[$contador]["ValorUnitario"]=$_POST["PrecioProducto".$id_producto];
				$contador++;
			endforeach;
			$array_productos = json_encode($array_productos);
			$DetallePedido = $array_productos;
		endif;

		$respuesta = SIMWebServiceQuemado::set_domicilio($IDClub,$IDSocio,$HoraEntrega,$ComentariosSocio,$DetallePedido,$Celular,$Direccion,$ValorDomicilio,$FormaPago,"2");



		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setdomicilio','".json_encode($_POST)."','".json_encode($respuesta)."')");

		if(!empty($_POST["paginaretorno"]))
			header("Location: ".$_POST["paginaretorno"]);


		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	//Fin Domicilios 2


	case "getpuertas":
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_puertas($IDClub,$Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setabrirpuerta":
		$IDSocio = $_POST["IDSocio"];
		$IDPuerta= $_POST["IDPuerta"];
		$respuesta = SIMWebServiceQuemado::set_abrir_puerta($IDClub,$IDSocio,$IDPuerta);
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setabrirpuerta','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar directorio del club
	case "getrestaurante":
		$respuesta = SIMWebServiceQuemado::get_restaurante($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getcamposinvitados":
		$respuesta = SIMWebServiceQuemado::get_campos_invitados($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getcamposcontratista":
		$respuesta = SIMWebServiceQuemado::get_campos_contratista($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar publicidad
	case "getpublicidad":
		$IDModulo = SIMNet::req( "IDModulo" );
		$IDCategoria = SIMNet::req( "IDCategoria" );
		$TipoApp = SIMNet::req( "TipoApp" );
		$respuesta = SIMWebServiceQuemado::get_publicidad($IDClub, $IDModulo, $IDCategoria, $TipoApp);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getpublicidad','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar documentos del club
	case "getdocumento":
		$IDTipoArchivo = SIMNet::req( "IDTipoArchivo" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebServiceQuemado::get_documento($IDClub, $IDTipoArchivo,$IDServicio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumento','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar documentos del socio
	case "getdocumentopersonal":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_documento_personal($IDClub, $IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentopersonal','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getdocumentodinamico":
		$IDSubmodulo = SIMNet::req( "IDSubmodulo" );
		$respuesta = SIMWebServiceQuemado::get_documento_dinamico($IDClub,$IDSubmodulo,"");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getdocumentodinamico2":
		$IDSubmodulo = SIMNet::req( "IDSubmodulo" );
		$respuesta = SIMWebServiceQuemado::get_documento_dinamico($IDClub,$IDSubmodulo,"2");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico2','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getdocumentodinamico3":
		$IDSubmodulo = SIMNet::req( "IDSubmodulo" );
		$respuesta = SIMWebServiceQuemado::get_documento_dinamico($IDClub,$IDSubmodulo,"3");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico3','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getdocumentodinamicofuncionario":
		$respuesta = SIMWebServiceQuemado::get_documento_dinamico_funcionario($IDClub,"");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getdocumentodinamicofuncionario2":
		$respuesta = SIMWebServiceQuemado::get_documento_dinamico_funcionario($IDClub,"2");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getdocumentodinamicofuncionario3":
		$respuesta = SIMWebServiceQuemado::get_documento_dinamico_funcionario($IDClub,"3");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar tipo de archivos
	case "gettipoarchivo":
		$IDTipoArchivo = SIMNet::req( "IDTipoArchivo" );
		$IDTipoArchivo = SIMNet::req( "IDSubmodulo" );
		$respuesta = SIMWebServiceQuemado::get_tipoarchivo($IDClub,$IDTipoArchivo,$IDSubmodulo);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettipoarchivo','".json_encode($_GET)."','".json_encode($respuesta)."')");

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar galerias del club o del socio o de la seccion o busqueda
	case "getgaleria":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDSeccionGaleria = SIMNet::req( "IDSeccion" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_galeria($IDClub, $IDSeccionGaleria, $IDSocio, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar galerias del club o del empleado o de la seccion o busqueda
	case "getgaleriaempleados":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDSeccionGaleria = SIMNet::req( "IDSeccion" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_galeria_empleados($IDClub, $IDSeccionGaleria, $IDUsuario, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	// Guardar las preferncias del usuario
	case "setpreferencias":
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$SeccionesContenido= $_POST["SeccionesContenido"];
		$SeccionesContenido2= $_POST["SeccionesContenido2"];
		$SeccionesContenido3= $_POST["SeccionesContenido3"];
		$SeccionesEvento= $_POST["SeccionesEvento"];
		$SeccionesEvento2= $_POST["SeccionesEvento2"];
		$SeccionesGaleria= $_POST["SeccionesGaleria"];
		$SeccionesClasificado= $_POST["SeccionesClasificado"];
		$respuesta = SIMWebServiceQuemado::set_preferencias($IDClub, $IDSocio, $SeccionesContenido, $SeccionesEvento,$SeccionesGaleria,$SeccionesClasificado,$SeccionesContenido2,$SeccionesEvento2,$SeccionesContenido3,$IDUsuario);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreferencias','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	// Guardar las preferncias del Empleado
	case "setpreferenciasempleado":
		$IDusuario = $_POST["IDusuario"];
		$SeccionesContenido= $_POST["SeccionesContenido"];
		$SeccionesEvento= $_POST["SeccionesEvento"];
		$SeccionesGaleria= $_POST["SeccionesGaleria"];
		$respuesta = SIMWebServiceQuemado::set_preferencias_empleado($IDClub, $IDusuario, $SeccionesContenido, $SeccionesEvento,$SeccionesGaleria);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	// Guardar Socios Favoritos
	case "setsociofavorito":
		$IDSocio = $_POST["IDSocio"];
		$SocioFavorito = $_POST["SocioFavorito"];
		$EstadoFavorito = $_POST["EstadoFavorito"];
		$respuesta = SIMWebServiceQuemado::set_socio_favorito($IDClub, $IDSocio, $SocioFavorito, $EstadoFavorito);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Enviar socios del club
	case "getsociosclub":
		$IDSocio = SIMNet::req( "IDSocio" );
		$NumeroDocumento = SIMNet::req( "NumeroDocumento" );
		$NumeroDerecho = SIMNet::req( "NumeroDerecho" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_socios_club($IDClub, $NumeroDocumento, $NumeroDerecho, $Tag, $IDSocio);
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getsociosclub','".json_encode($_GET)."','')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar informacion del socio
	case "getinfosocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$AppVersion = SIMNet::req( "AppVersion" );
		$TipoApp = SIMNet::req( "TipoApp" );
		$Data = SIMNet::req( "data" );
		$respuesta = SIMWebService::get_info_socio($IDClub, $IDSocio,$AppVersion,$Data,$TipoApp);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Busca invitados Generales
	case "getinvitadogeneral":
		$NumeroDocumento = SIMNet::req( "NumeroDocumento" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_invitado_general($IDClub, $NumeroDocumento, $Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar invitados socios del dia
	case "getinvitadodiasocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$NumeroDocumento = SIMNet::req( "NumeroDocumento" );
		$Nombre = SIMNet::req( "Nombre" );
		$Fecha= SIMNet::req( "Fecha" );
		$respuesta = SIMWebServiceQuemado::get_invitado_dia_socio($IDClub, $NumeroDocumento, $Nombre, $Fecha, $IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar socios del club
	case "getservicios":

		$TipoApp = SIMNet::req( "TipoApp" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDSocio = SIMNet::req( "IDSocio" );
		//Verifico Version del app
		if($IDClub=="8"):
			//$respuesta_version = SIMWebServiceQuemado::verifica_version_app($IDClub,$_GET["AppVersion"],$_GET["Dispositivo"],$TipoApp);
		endif;
		$respuesta = SIMWebServiceQuemado::get_servicios($IDClub,$TipoApp,$IDUsuario,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getservicios','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar pqr socio
	case "getpqrsocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDPqr = SIMNet::req( "IDPqr" );
		$respuesta = SIMWebServiceQuemado::get_pqr_socio($IDClub,$IDSocio,$IDPqr);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getpqrsocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar estado pqr
	case "getestadopqr":
		$respuesta = SIMWebServiceQuemado::get_estado_pqr($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Guardar contactos del club
	case "setcontacto":
		$IDSocio = $_POST["IDSocio"];
		$Nombre = $_POST["Nombre"];
		$Telefono = $_POST["Telefono"];
		$Ciudad = $_POST["Ciudad"];
		$Direccion = $_POST["Direccion"];
		$Email = $_POST["Email"];
		$Comentario = $_POST["Comentario"];
		$respuesta = SIMWebServiceQuemado::set_contacto($IDClub,$IDSocio,$Telefono,$Ciudad,$Direccion,$Email,$Comentario,$Nombre);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar tipo de pqr
	case "gettipopqr":
		$respuesta = SIMWebServiceQuemado::get_tipo_pqr($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar Pqr
	case "setpqr":
		$IDArea = $_POST["IDArea"];
		$IDSocio = $_POST["IDSocio"];
		$Archivo = $_POST["Archivo"];
		if(count($_FILES)>0 && $_POST["Dispositivo"]=="Android")
			$_POST = array_map('utf8_encode', $_POST);

		$TipoPqr = $_POST["TipoPqr"];
		$IDTipoPqr = $_POST["IDTipoPqr"];
		$Asunto = $_POST["Asunto"];
		$Comentario = $_POST["Comentario"];
		$NombreColaborador = $_POST["NombreColaborador"];
		$ApellidoColaborador = $_POST["ApellidoColaborador"];
		$respuesta = SIMWebServiceQuemado::set_pqr($IDClub,$IDArea, $IDSocio,$TipoPqr, $Asunto, $Comentario, $Archivo, $_FILES, $IDTipoPqr,$NombreColaborador,$ApellidoColaborador);
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpqr','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setpqrws":
		$Code = $_POST["code"];
		$Hub_Code = $_POST["hub_code"];
		$Status = $_POST["status"];

		$respuesta = SIMWebServiceQuemado::actualizar_pqr_ws($IDClub,$Code,$Hub_Code,$Status);
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpqrws','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Guardar Respuesta Socio Pqr
	case "setpqrrespuesta":
		$IDSocio = $_POST["IDSocio"];
		$IDPqr = $_POST["IDPqr"];
		$Comentario = $_POST["Comentario"];
		$IDPqrEstado = $_POST["IDPqrEstado"];
		$respuesta = SIMWebServiceQuemado::set_pqr_respuesta($IDClub,$IDSocio, $IDPqr, $Comentario, $IDPqrEstado);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Calificacion pqr
	case "setcalificacionpqr":
		$IDSocio = $_POST["IDSocio"];
		$IDPqr = $_POST["IDPqr"];
		$Comentario = $_POST["Comentario"];
		$Calificacion = $_POST["Calificacion"];
		$respuesta = SIMWebServiceQuemado::set_calificacion_pqr($IDClub,$IDSocio, $IDPqr, $Comentario, $Calificacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


//Funcionarios PQR
	//Enviar pqr socio funcionario
	case "getpqrfuncionario":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDPqr = SIMNet::req( "IDPqr" );
		$respuesta = SIMWebServiceQuemado::get_pqr_funcionario($IDClub,$IDUsuario,$IDPqr);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getpqrsociofuncionario":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDPqr = SIMNet::req( "IDPqr" );
		$respuesta = SIMWebServiceQuemado::get_pqr_socio_funcionario($IDClub,$IDUsuario,$IDPqr);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Las pqr que envia un funcionario y en la cual otro funcionario debe responder
	case "getpqrasignadafuncionario":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDPqr = SIMNet::req( "IDPqr" );
		$respuesta = SIMWebServiceQuemado::get_pqr_asignada_funcionario($IDClub,$IDUsuario,$IDPqr);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar tipo de pqr funcionario
	case "gettipopqrfuncionario":
		$respuesta = SIMWebServiceQuemado::get_tipo_pqr_funcionario($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar Pqr funcionario
	case "setpqrfuncionario":
		$IDArea = $_POST["IDArea"];
		$IDUsuario = $_POST["IDUsuario"];
		if(count($_FILES)>0 && $_POST["Dispositivo"]=="Android")
			$_POST = array_map('utf8_encode', $_POST);

		$TipoPqr = $_POST["TipoPqr"];
		$IDTipoPqr = $_POST["IDTipoPqr"];
		$Asunto = $_POST["Asunto"];
		$Comentario = $_POST["Comentario"];
		$Archivo = $_POST["Archivo"];
		$respuesta = SIMWebServiceQuemado::set_pqr_funcionario($IDClub,$IDArea, $IDUsuario,$TipoPqr, $Asunto, $Comentario, $Archivo, $_FILES, $IDTipoPqr);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar Respuesta Socio Pqr funcionario
	case "setpqrrespuestafuncionario":
		$IDUsuario = $_POST["IDUsuario"];
		$IDPqr = $_POST["IDPqr"];
		$Comentario = $_POST["Comentario"];
		$respuesta = SIMWebServiceQuemado::set_pqr_respuesta_funcionario($IDClub,$IDUsuario, $IDPqr, $Comentario);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar Respuesta Socio Pqr funcionario
	case "setpqrrespuestaparasocio":
		$IDUsuario = $_POST["IDUsuario"];
		$IDPqr = $_POST["IDPqr"];
		$Comentario = $_POST["Comentario"];
		$IDPqrEstado = $_POST["IDPqrEstado"];
		$respuesta = SIMWebServiceQuemado::set_pqr_respuesta_para_socio($IDClub,$IDUsuario, $IDPqr, $Comentario, $IDPqrEstado);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar Respuesta a un pqr creado por un Funcionario donde otro funcionario le responde
	case "setpqrrespuestaparafuncionario":
		$IDUsuario = $_POST["IDUsuario"];
		$IDPqr = $_POST["IDPqr"];
		$Comentario = $_POST["Comentario"];
		$IDPqrEstado = $_POST["IDPqrEstado"];
		$respuesta = SIMWebServiceQuemado::set_pqr_respuesta_para_funcionario($IDClub,$IDUsuario, $IDPqr, $Comentario, $IDPqrEstado);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Calificacion pqr funcionario
	case "setcalificacionpqrfuncionario":
		$IDUsuario = $_POST["IDUsuario"];
		$IDPqr = $_POST["IDPqr"];
		$Comentario = $_POST["Comentario"];
		$Calificacion = $_POST["Calificacion"];
		$respuesta = SIMWebServiceQuemado::set_calificacion_pqr_funcionario($IDClub,$IDUsuario, $IDPqr, $Comentario, $Calificacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar areas del club funcionario
	case "getareaclubfuncionario":
		$respuesta = SIMWebServiceQuemado::get_area_club_funcionario($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
// FIN Funcionarios PQR



	//Enviar listado de clubes con convenio del club
	case "getlistaclubes":
		$respuesta = SIMWebServiceQuemado::get_lista_clubes($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar solicitudes socio
	case "getsolicitudcanje":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDCanjeSolicitud = SIMNet::req( "IDCanjeSolicitud" );
		$respuesta = SIMWebServiceQuemado::get_solicitud_canje($IDClub,$IDSocio,$IDCanjeSolicitud);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar Solicitud Canje
	case "setsolicitudcanje":
		$IDSocio = $_POST["IDSocio"];
		$IDListaClubes = $_POST["IDListaClubes"];
		$FechaInicio = $_POST["FechaInicio"];
		$CantidadDias = $_POST["CantidadDias"];
		$Beneficiarios = $_POST["Beneficiarios"];
		$ValoresFormulario = $_POST["ValoresFormulario"];
		$respuesta = SIMWebServiceQuemado::set_solicitud_canje($IDClub,$IDSocio, $IDListaClubes,$FechaInicio, $CantidadDias, $Beneficiarios,$ValoresFormulario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsolicitudcanje','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Guardar Foto Socio
	case "setfotosocio":
		$IDSocio = $_POST["IDSocio"];
		$Archivo = $_POST["Archivo"];
		$respuesta = SIMWebServiceQuemado::set_foto_socio($IDClub, $IDSocio,$Archivo, $_FILES);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Verificar si Foto Socio ya fue cargada
	case "getactualizarfotosocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_actualizar_foto_socio($IDClub,$IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Guardar Foto Empleado
	case "setfotoempleado":
		$IDUsuario = $_POST["IDUsuario"];
		$Archivo = $_POST["Archivo"];
		$respuesta = SIMWebServiceQuemado::set_foto_empleado($IDClub, $IDUsuario,$Archivo, $_FILES);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Verificar si Foto Empleado ya fue cargada
	case "getactualizarfotoempleado":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceQuemado::get_actualizar_foto_empleado($IDClub,$IDUsuario);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Guardar invitados del socio club
	case "setinvitado":
		$IDSocio = $_POST["IDSocio"];
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$Nombre = $_POST["Nombre"];
		$FechaIngreso = $_POST["FechaIngreso"];
		$ValoresFormulario = $_POST["ValoresFormulario"];
		$respuesta = SIMWebServiceQuemado::set_invitado($IDClub,$IDSocio,$NumeroDocumento,$Nombre,$FechaIngreso,$ValoresFormulario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setinvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar invitados del socio club
	case "setinvitadogruporeserva":
		$IDSocio = $_POST["IDSocio"];
		$IDReserva = $_POST["IDReserva"];
		$respuesta = SIMWebServiceQuemado::set_invitado_grupo_reserva($IDClub,$IDSocio,$IDReserva);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setinvitadogruporeserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
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
		$respuesta = SIMWebServiceQuemado::set_invitado_update($IDClub,$IDSocio,$IDInvitado,$NumeroDocumento,$Nombre,$FechaIngreso);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Eliminar Reserva
	case "cancelarinvitacion":
		$IDSocio = SIMNet::req( "IDSocio" );;
		$IDInvitacion = SIMNet::req( "IDInvitacion" );
		$respuesta = SIMWebServiceQuemado::cancela_invitacion($IDClub,$IDSocio,$IDInvitacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "seteliminarinvitadov1":
		$IDSocio = $_POST["IDSocio"];
		$IDSocioInvitado = $_POST["IDSocioInvitado"];
		$respuesta = SIMWebServiceQuemado::set_eliminar_invitado_v1($IDClub,$IDSocio,$IDSocioInvitado);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getingresosalidainvitadov1":
		$IDSocio = SIMNet::req("IDSocio");
		$IDSocioInvitado = SIMNet::req("IDSocioInvitado");
		$respuesta = SIMWebServiceQuemado::get_ingreso_salida_invitadov1($IDClub,$IDSocio,$IDSocioInvitado);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar tipo de pqr
	case "gettipoingreso":
		$IDSocio = SIMNet::req("IDSocio");
		$respuesta = SIMWebServiceQuemado::get_tipo_ingreso($IDClub,$IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Guardar invitados a una reserva
	case "setinvitadoservicio":
		$IDReserva = $_POST["IDReserva"];
		$Invitados = $_POST["Invitados"];
		$respuesta = SIMWebServiceQuemado::set_invitado_servicio($IDClub,$IDReserva,$Invitados);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setinvitadoservicio','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Elimina invitados a una reserva
	case "delinvitadoservicio":
		$IDReserva = $_POST["IDReserva"];
		$IDReservaGeneralInvitado = $_POST["IDReservaGeneralInvitado"];
		$respuesta = SIMWebServiceQuemado::del_invitado_servicio($IDClub,$IDReserva,$IDReservaGeneralInvitado);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('delinvitadoservicio','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar Los invitados de un socio
	case "getinvitado":
		$IDSocio = SIMNet::req( "IDSocio" );
		$NumeroDocumento = SIMNet::req( "NumeroDocumento" );
		$FechaIngreso = SIMNet::req( "FechaIngreso" );
		$respuesta = SIMWebServiceQuemado::get_invitado($IDClub,$IDSocio,$NumeroDocumento, $FechaIngreso);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Los invitados de un socio
	case "getmisinvitados":
		$IDSocio = SIMNet::req( "IDSocio" );
		$NumeroDocumento = SIMNet::req( "NumeroDocumento" );
		$respuesta = SIMWebServiceQuemado::get_mis_invitado($IDClub,$IDSocio,$NumeroDocumento);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Los invitados de un socio
	case "getbeneficiarios":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Fecha = SIMNet::req( "Fecha" );
		$Hora = SIMNet::req( "Hora" );
		$Tipo = SIMNet::req( "tipo" );
		$respuesta = SIMWebServiceQuemado::get_beneficiarios($IDClub,$IDSocio,$Fecha,$Hora,$Tipo);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Guardar invitados del socio club
	case "setautorizacioninvitado":
		$IDSocio = $_POST["IDSocio"];
		$FechaIngreso = $_POST["FechaIngreso"];
		$FechaSalida = $_POST["FechaSalida"];
		$DatosInvitado = $_POST["DatosInvitado"];
		$ValoresFormulario = $_POST["ValoresFormulario"];
		$respuesta = SIMWebServiceQuemado::set_autorizacion_invitado($IDClub,$IDSocio,$FechaIngreso,$FechaSalida,$DatosInvitado,"",$ValoresFormulario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setautorizacioninvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Actualizar invitados del socio club
	case "setautorizacioninvitadoupdate":
		$IDSocio = $_POST["IDSocio"];
		$IDInvitacion = $_POST["IDInvitacion"];
		$FechaIngreso = $_POST["FechaIngreso"];
		$FechaSalida = $_POST["FechaSalida"];
		$DatosInvitado = $_POST["DatosInvitado"];
		$ValoresFormulario = $_POST["ValoresFormulario"];
		$respuesta = SIMWebServiceQuemado::set_autorizacion_invitado_update($IDClub,$IDSocio,$IDInvitacion,$FechaIngreso,$FechaSalida,$DatosInvitado,$ValoresFormulario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setautorizacioninvitadoupdate','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Eliminar Invitacion Especial
	case "cancelarautorizacioninvitado":
		$IDSocio = SIMNet::req( "IDSocio" );;
		$IDInvitacion = SIMNet::req( "IDInvitacion" );
		$respuesta = SIMWebServiceQuemado::cancela_autorizacion_invitado($IDClub,$IDSocio,$IDInvitacion);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('cancelarautorizacioninvitado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar Los invitados de accesos especiales de un socio
	case "getmisautorizacionesinvitados":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Tag = SIMNet::req( "Tag" );
		$FechaIngreso = SIMNet::req( "FechaIngreso" );
		$respuesta = SIMWebServiceQuemado::get_mis_autorizaciones_invitados($IDClub,$IDSocio,$Tag,$FechaIngreso,"Futuro");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getmisautorizacionesinvitadosanterior":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Tag = SIMNet::req( "Tag" );
		$FechaIngreso = SIMNet::req( "FechaIngreso" );
		$respuesta = SIMWebServiceQuemado::get_mis_autorizaciones_invitados($IDClub,$IDSocio,$Tag,$FechaIngreso,"Pasado");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisautorizacionesinvitadosanterior','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "eliminamisautorizacionesinvitadosanterior":
		$IDSocio = $_POST["IDSocio"];
		$IDInvitacion = $_POST["IDInvitacion"];
		$respuesta = SIMWebServiceQuemado::elimina_misautorizaciones_invitados_anterior($IDClub,$IDSocio,$IDInvitacion);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisautorizacionesinvitadosanterior','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getingresosalidaautorizacionesinvitados":
		$IDSocio = SIMNet::req("IDSocio");
		$IDInvitacion = SIMNet::req("IDInvitacion");
		$respuesta = SIMWebServiceQuemado::get_ingresosalida_autorizaciones_invitados($IDClub,$IDSocio,$IDInvitacion);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisautorizacionesinvitadosanterior','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "eliminamisautorizacionescontratistaanterior":
		$IDSocio = $_POST["IDSocio"];
		$IDAutorizacion = $_POST["IDAutorizacion"];
		$respuesta = SIMWebServiceQuemado::elimina_misautorizaciones_contratista_anterior($IDClub,$IDSocio,$IDAutorizacion);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminamisautorizacionescontratistaanterior','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getingresosalidaautorizacionescontratista":
		$IDSocio = SIMNet::req("IDSocio");
		$IDAutorizacion = SIMNet::req("IDAutorizacion");
		$respuesta = SIMWebServiceQuemado::get_ingresosalida_autorizaciones_contratista($IDClub,$IDSocio,$IDAutorizacion);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminamisautorizacionescontratistaanterior','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;




	//Guardar Invitacion Contratista
	case "setautorizacioncontratista":
		$IDSocio = $_POST["IDSocio"];
		$TipoAutorizacion = $_POST["TipoAutorizacion"];
		$FechaIngreso = $_POST["FechaIngreso"];
		$FechaSalida = $_POST["FechaSalida"];
		$TipoDocumento = $_POST["TipoDocumento"];
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$Nombre = $_POST["Nombre"];
		$Apellido = $_POST["Apellido"];
		$Email = $_POST["Email"];
		$Placa = $_POST["Placa"];
		$ObservacionSocio = $_POST["Observaciones"];
		$ValoresFormulario = $_POST["ValoresFormulario"];
		$respuesta = SIMWebServiceQuemado::set_autorizacion_contratista($IDClub,$IDSocio,$TipoAutorizacion,$FechaIngreso,$FechaSalida,$TipoDocumento,$NumeroDocumento,$Nombre,$Apellido,$Email,$Placa,"","","","","","","","","",$ObservacionSocio,$ValoresFormulario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setautorizacioncontratista','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Eliminar Invitacion Contratista
	case "cancelaautorizacioncontratista":
		$IDSocio = SIMNet::req( "IDSocio" );;
		$IDAutorizacion = SIMNet::req( "IDAutorizacion" );
		$respuesta = SIMWebServiceQuemado::cancela_autorizacion_contratista($IDClub,$IDSocio,$IDAutorizacion);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('cancelaautorizacioncontratista','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Actualizar datos contratista
	case "setcontratistaoupdate":
		$IDSocio = $_POST["IDSocio"];
		$TipoDocumento = $_POST["TipoDocumento"];
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$Nombre = $_POST["Nombre"];
		$Apellido = $_POST["Apellido"];
		$Email = $_POST["Email"];
		$Placa = $_POST["Placa"];
		$ObservacionSocio = $_POST["Observaciones"];
		$respuesta = SIMWebServiceQuemado::set_contratista_update($IDClub,$IDSocio,$TipoDocumento,$NumeroDocumento,$Nombre,$Apellido,$Email,$Placa);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Actualizar autorizacion contratista
	case "setcontratistaupdateautorizacion":
		$IDInvitacion = $_POST["IDInvitacion"];
		$IDSocio = $_POST["IDSocio"];
		$TipoAutorizacion = $_POST["TipoAutorizacion"];
		$FechaIngreso = $_POST["FechaIngreso"];
		$FechaSalida = $_POST["FechaSalida"];
		$TipoDocumento = $_POST["TipoDocumento"];
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$Nombre = $_POST["Nombre"];
		$Apellido = $_POST["Apellido"];
		$Email = $_POST["Email"];
		$Placa = $_POST["Placa"];
		$ObservacionSocio = $_POST["Observaciones"];
		$ValoresFormulario = $_POST["ValoresFormulario"];
		$respuesta = SIMWebServiceQuemado::set_contratista_update_autorizacion($IDClub,$IDSocio,$IDInvitacion,$TipoAutorizacion,$FechaIngreso,$FechaSalida,$TipoDocumento,$NumeroDocumento,$Nombre,$Apellido,$Email,$Placa,"","","","","","","","","",$ObservacionSocio,$ValoresFormulario);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Enviar Los invitados Contratistas de un socio
	case "getmisautorizacionescontratista":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Tag = SIMNet::req( "Tag" );
		$FechaIngreso = SIMNet::req( "FechaIngreso" );
		$respuesta = SIMWebServiceQuemado::get_mis_autorizaciones_contratista($IDClub,$IDSocio,$Tag,$FechaIngreso,"Futuro");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisautorizacionescontratista','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Los invitados Contratistas de un socio
	case "getmisautorizacionescontratistaanterior":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Tag = SIMNet::req( "Tag" );
		$FechaIngreso = SIMNet::req( "FechaIngreso" );
		$respuesta = SIMWebServiceQuemado::get_mis_autorizaciones_contratista($IDClub,$IDSocio,$Tag,$FechaIngreso,"Pasado");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;






	//Enviar Los elementos de un servicio
	case "getelementos":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebServiceQuemado::get_elementos($IDClub,$IDSocio,$IDServicio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getelementos','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;




	//Enviar Las horas de un servicio
	case "gethoras":
	//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_horasI','IDClub: ".$IDClub. " IDSocio:" . $IDSocio . " . IDServicio: ".$IDServicio." Elemento: ".$IDElemento."','".json_encode($_GET)."')");


		$IDSocio = SIMNet::req( "IDSocio" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebServiceQuemado::get_horas($IDClub,$IDSocio,$IDServicio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar Los campos de un servicio
	case "getcampos":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$respuesta = SIMWebServiceQuemado::get_campos($IDClub,$IDSocio,$IDServicio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar la disponibilidad de un elemento en una fecha, marca las horas con disponible si o no
	case "getdisponiblidadelemento":
		$IDElemento = SIMNet::req( "IDElemento" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		//$respuesta = SIMWebServiceQuemado::get_disponiblidad_elemento($IDClub,$IDElemento,$IDServicio,$Fecha);
		$respuesta = SIMWebServiceQuemado::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"",$UnElemento=1,"",$IDTipoReserva);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdisponiblidadelemento','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar la disponibilidad de varios elementos de un servicio en una fecha, marca las horas con disponible si o no
	case "getdisponiblidadelementoservicio":
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$IDElemento = SIMNet::req( "IDElemento" );
		$IDTipoReserva = SIMNet::req( "IDTipoReserva" );
		$NumeroTurnos = SIMNet::req( "NumeroTurnos" ); // Recibo cuantos turnos seguidos desea reservar
		$respuesta = SIMWebServiceQuemado::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"","",$NumeroTurnos,$IDTipoReserva);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdisponiblidadelementoservicio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar la disponibilidad de los elementos en una fecha hora especifica
	case "getdisponiblidadfechahora":
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$Hora = SIMNet::req( "Hora" );
		$respuesta = SIMWebServiceQuemado::get_disponiblidad_fecha_hora($IDClub,$IDServicio,$Fecha,$Hora,$NumeroTurnos);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar las horas Dsiponibles consultando todos los elementos en una fecha especifica
	case "gethoradisponible":
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebServiceQuemado::get_hora_disponible($IDClub,$IDServicio,$Fecha,$Hora);
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
		$NumeroTurnos = $_POST["NumeroTurnos"]; // Recibo cuantos turnos seguidos desea reservar
		$respuesta = SIMWebServiceQuemado::set_separa_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,$Tee,$Fecha,$Hora,$IDTipoReserva,$NumeroTurnos);

		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setseparareserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setpushgeneral":
		$Accion = $_POST["Accion"];
		$Mensaje = $_POST["Mensaje"];
		$respuesta = SIMWebServiceQuemado::set_push_general($IDClub,$Accion,$Mensaje);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpushgeneral','".json_encode($_POST)."','".json_encode($respuesta)."')");
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
		$respuesta = SIMWebServiceQuemado::set_libera_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,$Tee,$Fecha,$Hora);
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
		$ListaAuxiliar = $_POST["ListaAuxiliar"];
		//Modalidad
		$IDTipoModalidadEsqui = $_POST["IDTipoModalidad"];
		//Tee
		$Tee = $_POST["Tee"];
		//Si se asigna la reserva a un beneficiario
		$IDBeneficiario = $_POST["IDBeneficiario"];
		$TipoBeneficiario = $_POST["TipoBeneficiario"];
		// Tipo Reserva (Dobles, sencillos, etc)
		$IDTipoReserva = $_POST["IDTipoReserva"];
		$NumeroTurnos = $_POST["NumeroTurnos"]; // Recibo cuantos turnos seguidos desea reservar
		//Si la reserva la hace un Empleado
		$IDUsuarioReserva = $_POST["IDUsuario"];
		//Numero de invitados cuando es salon por ejemplo
		$CantidadInvitadoSalon = $_POST["CantidadInvitadoSalon"];
		$Altitud = $_POST["Altitud"];
		$Longitud = $_POST["Longitud"];
		$respuesta = SIMWebServiceQuemado::set_reserva_generalV2($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,"","",$Tee,$IDDisponibilidad,$Repetir,$Periodo,$RepetirFechaFinal,$IDTipoModalidadEsqui,$IDAuxiliar,$IDTipoReserva,$NumeroTurnos,"",$IDBeneficiario,$TipoBeneficiario,$IDUsuarioReserva,$CantidadInvitadoSalon,$ListaAuxiliar,$Altitud,$Longitud);
		//$respuesta = SIMWebServiceQuemado::set_reserva_generalV2_test($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,"","",$Tee,$IDDisponibilidad,$Repetir,$Periodo,$RepetirFechaFinal,$IDTipoModalidadEsqui,$IDAuxiliar,$IDTipoReserva,$NumeroTurnos,"",$IDBeneficiario,$TipoBeneficiario,$IDUsuarioReserva,$CantidadInvitadoSalon,$ListaAuxiliar);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_reserva_generalV2','".json_encode($_POST)."','".json_encode($respuesta)."')");

		if(!empty($_POST["paginaretorno"]))
			header("Location: ".$_POST["paginaretorno"]."?mensaje=".$respuesta[message]);

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
		$ListaAuxiliar = $_POST["ListaAuxiliar"];
		//Modalidad
		$IDTipoModalidadEsqui = $_POST["IDTipoModalidad"];
		//Tee
		$Tee = $_POST["Tee"];
		//Si se asigna la reserva a un beneficiario
		$IDBeneficiario = $_POST["IDBeneficiario"];
		$TipoBeneficiario = $_POST["TipoBeneficiario"];
		// Tipo Reserva (Dobles, sencillos, etc)
		$IDTipoReserva = $_POST["IDTipoReserva"];
		//Numero de invitados cuando es salon por ejemplo
		$CantidadInvitadoSalon = $_POST["CantidadInvitadoSalon"];
		//Si la reserva la hace un Empleado
		$IDUsuarioReserva = $_POST["IDUsuario"];
		$Altitud = $_POST["Altitud"];
		$Longitud = $_POST["Longitud"];

		$respuesta = SIMWebServiceQuemado::set_reserva_generalV2($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,"","",$Tee,$IDDisponibilidad,$Repetir,$Periodo,$RepetirFechaFinal,$IDTipoModalidadEsqui,$IDAuxiliar,$IDTipoReserva,"",$IDBeneficiario,$TipoBeneficiario,$IDUsuarioReserva,$CantidadInvitadoSalon,$ListaAuxiliar,$Altitud,$Longitud);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;




	//Eliminar Reserva
	case "eliminareservageneral":
		$IDSocio = $_POST["IDSocio"];
		$IDReserva = $_POST["IDReserva"];
		$respuesta = SIMWebServiceQuemado::elimina_reserva_general($IDClub,$IDSocio,$IDReserva,"");
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminareservageneral','".json_encode($_POST)."','".json_encode($respuesta)."')");

		if(!empty($_POST["paginaretorno"]))
			header("Location: ".$_POST["paginaretorno"]."?mensaje=".$respuesta[message]);


		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );

		exit;
	break;


	//Enviar si socio cumplio o no la reserva
	case "setreservacumplida":
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$IDReservaGeneral = $_POST["IDReservaGeneral"];
		$ReservaCumplida = $_POST["ReservaCumplida"];
		$ReservaCumplidaSocio = $_POST["ReservaCumplidaSocio"];
		$Observacion = $_POST["Observacion"];
		$Invitados = $_POST["Invitados"];
		$ReservaCumplida = "S";
		$respuesta = SIMWebServiceQuemado::set_reserva_cumplida($IDClub,$IDSocio,$IDUsuario,$IDReservaGeneral,$ReservaCumplida,$ReservaCumplidaSocio,$Observacion,$Invitados);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setreservacumplida','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar si socio cumplio o no la reserva
	case "seteditaauxiliarreserva":
		$IDSocio = $_POST["IDSocio"];
		$IDReservaGeneral = $_POST["IDReservaGeneral"];
		$ListaAuxiliar = $_POST["ListaAuxiliar"];
		$respuesta = SIMWebServiceQuemado::set_edita_auxiliar_reserva($IDClub,$IDSocio,$IDReservaGeneral,$ListaAuxiliar);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('seteditaauxiliarreserva','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Validar reserva con pago
	case "validapagoreserva":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDReservaGeneral = SIMNet::req( "IDReservaGeneral" );
		$respuesta = SIMWebServiceQuemado::valida_pago_reserva($IDClub,$IDSocio,$IDReservaGeneral);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "validapagodomicilio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDDomicilio = SIMNet::req( "IDDomicilio" );
		$respuesta = SIMWebServiceQuemado::valida_pago_domicilio($IDClub,$IDSocio,$IDDomicilio,"");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "validapagodomicilio2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDDomicilio = SIMNet::req( "IDDomicilio" );
		$respuesta = SIMWebServiceQuemado::valida_pago_domicilio($IDClub,$IDSocio,$IDDomicilio,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Validar reserva con pago
	case "validapagoevento":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDEventoRegistro = SIMNet::req( "IDEventoRegistro" );
		$respuesta = SIMWebServiceQuemado::valida_pago_evento($IDClub,$IDSocio,$IDEventoRegistro);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Validar reserva con pago
	case "validapagoevento2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDEventoRegistro = SIMNet::req( "IDEventoRegistro" );
		$respuesta = SIMWebServiceQuemado::valida_pago_evento($IDClub,$IDSocio,$IDEventoRegistro,"2");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar Campos de golf disponibles
	case "getdisponibilidadfecha":
		$IDCampo = SIMNet::req( "IDCampo" );
		$Fecha = SIMNet::req( "Fecha" );
		$Hora = SIMNet::req( "Hora" );
		$respuesta = SIMWebServiceQuemado::get_disponibilidad_fecha($IDClub,$IDCampo,$Fecha,$Hora);
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdisponibilidadfecha','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar Campos de golf disponibles
	case "getdisponibilidadcampo":
		$IDCampo = SIMNet::req( "IDCampo" );
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebServiceQuemado::get_disponibilidad_campo($IDClub,$IDCampo,$Fecha);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Inscripcion a eventos
	case "geteventosocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDEvento = SIMNet::req( "IDEvento" );
		$respuesta = SIMWebServiceQuemado::get_evento_socio($IDClub,$IDSocio,200,$IDEvento);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('geteventosocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Inscripcion a eventos
	case "geteventosocio2":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDEvento = SIMNet::req( "IDEvento" );
		$respuesta = SIMWebServiceQuemado::get_evento_socio($IDClub,$IDSocio,200,$IDEvento,"2");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('geteventosocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Elimina reserva inscripcion
	case "eliminaeventosocio":
		$IDSocio = $_POST["IDSocio"];
		$IDEventoRegistro = $_POST["IDEventoRegistro"];
		$respuesta = SIMWebServiceQuemado::elimina_evento_socio($IDClub,$IDSocio,$IDEventoRegistro);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminaeventosocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "eliminaeventosocio2":
		$IDSocio = $_POST["IDSocio"];
		$IDEventoRegistro = $_POST["IDEventoRegistro"];

		$respuesta = SIMWebServiceQuemado::elimina_evento_socio($IDClub,$IDSocio,$IDEventoRegistro,"2");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminaeventosocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Reservas del socio ultimas 15
	case "getreservasocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDReserva = SIMNet::req( "IDReserva" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceQuemado::get_reservas_socio($IDClub,$IDSocio,200,$IDReserva,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservasocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Reservas del socio todas
	case "getreservasociotodas":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_reservas_socio($IDClub,$IDSocio,0);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservasocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Reservas del socio donde es invitado
	case "getreservasocioinvitado":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_reservas_socio_invitado($IDClub,$IDSocio,"","");

		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservasocioinvitado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Reservas asociadas
	case "getreservaasociada":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDReserva = SIMNet::req( "IDReserva" );
		$respuesta = SIMWebServiceQuemado::get_reserva_asociada($IDClub,$IDSocio,$IDReserva);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Reservas que ha realizado el empleado
	case "getreservaempleado":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDReserva = SIMNet::req( "IDReserva" );
		$respuesta = SIMWebServiceQuemado::get_reservas_empleado($IDClub,$IDUsuario,15,$IDReserva);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservaempleado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Verificar Grupos golf
	case "verificarsociogrupo":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebServiceQuemado::verificar_socio_grupo_fecha($IDClub,$IDSocio,$Fecha,$IDServicio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('verificarsociogrupo','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar fechas disponibles o no para reservas servicio
	case "getfechasdisponiblesservicio":
		$IDSocio = SIMNet::req( "IDSocio" );
		//Verifico Version del app
		$TipoApp = SIMNet::req( "TipoApp" );
		if($IDClub=="8" || $IDClub=="1"):
			//$respuesta_version = SIMWebServiceQuemado::verifica_version_app($IDClub,$_GET["AppVersion"],$_GET,$TipoApp);
		endif;
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebServiceQuemado::get_fecha_disponibilidad_servicio($IDClub,$IDServicio,$Fecha,"App");
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getfechasdisponiblesservicio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar auxiliares boleadores
	case "getauxiliares":
		$IDServicio = SIMNet::req( "IDServicio" );
		$Fecha = SIMNet::req( "Fecha" );
		$Hora = SIMNet::req( "Hora" );
		$ListaEspera=SIMNet::req( "ListaEspera" );

		//Si lista de espera es S llamo el servicio para ver a Todos
		if($ListaEspera=="S")
			$VerSoloDisponibles="N";
		else
			$VerSoloDisponibles="S";

		$respuesta = SIMWebServiceQuemado::get_auxiliares($IDClub,$IDServicio,$Fecha,$Hora,$VerSoloDisponibles);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getauxiliares','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Enviar modalidades club
	case "getmodalidades":
		$IDTipoModalidadEsqui = SIMNet::req( "IDTipoModalidadEsqui" );
		$IDElemento = SIMNet::req( "IDElemento" );
		$respuesta = SIMWebServiceQuemado::get_modalidades($IDClub,$IDTipoModalidadEsqui,$IDElemento);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Enviar mensajes socio push
	case "getnotificaciones":
		$IDSocio = SIMNet::req( "IDSocio" );
		$TipoApp = SIMNet::req( "TipoApp" );
		$respuesta = SIMWebServiceQuemado::get_notificaciones($IDClub,$IDSocio,$TipoApp);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getnotificaciones','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setnotificacionleida":
		$IDSocio = $_POST["IDSocio"];
		$IDLogNotificacion = $_POST["IDLogNotificacion"];
		$respuesta = SIMWebServiceQuemado::set_notificacion_leida($IDClub,$IDSocio, $IDLogNotificacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getnoticacionlocal":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceQuemado::get_noticacion_local($IDClub, $IDSocio,$IDUsuario);
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
		//$respuesta = SIMWebServiceQuemado::set_reserva_golf($IDClub,$IDSocio,$IDCampo,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,$Tee);
		$respuesta = SIMWebServiceQuemado::set_reserva_general($IDClub,$IDSocio,$IDCampo,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,"","",$Tee);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Guardar Tokenm
	case "settoken":
		$IDSocio = $_POST["IDSocio"];
		$Dispositivo = $_POST["Dispositivo"];
		$Token = $_POST["Token"];
		$ClubToken = $_POST["ClubToken"];

		if(empty($Token) && !empty($ClubToken)){
			$Token = $ClubToken;
		}

		$respuesta = SIMWebServiceQuemado::set_token($IDClub,$IDSocio,$Dispositivo,$Token);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settoken','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "settokenempleado":
		$IDUsuario = $_POST["IDUsuario"];
		$Dispositivo = $_POST["Dispositivo"];
		$Token = $_POST["Token"];
		$ClubToken = $_POST["ClubToken"];

		if(empty($Token) && !empty($ClubToken)){
			$Token = $ClubToken;
		}
		$respuesta = SIMWebServiceQuemado::set_token_empleado($IDClub,$IDUsuario,$Dispositivo,$Token);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settokenempleado','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Guardar Token
	case "setcambiarclave":
		$IDSocio = $_POST["IDSocio"];
		$Clave = $_POST["Clave"];
		$SegundaClave = $_POST["SegundaClave"];
		$Correo = $_POST["Correo"];
		$respuesta = SIMWebServiceQuemado::set_cambiar_clave($IDClub,$IDSocio,$Clave,$SegundaClave,$Correo);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcambiarclave','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setcambiarclaveempleado":
		$IDUsuario = $_POST["IDUsuario"];
		$Clave = $_POST["Clave"];
		$respuesta = SIMWebServiceQuemado::set_cambiar_clave_empleado($IDClub,$IDUsuario,$Clave);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Guardar Segunda clave
	case "setcambiarsegundaclave":
		$IDSocio = $_POST["IDSocio"];
		$Clave = $_POST["Clave"];
		$Correo = $_POST["Correo"];
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcambiarsegundaclave','".json_encode($_POST)."','".json_encode($respuesta)."')");
		$respuesta = SIMWebServiceQuemado::set_cambiar_segunda_clave($IDClub,$IDSocio,$Clave,$Correo);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Buscar Presalida
	case "getpresalida":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Tag= SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_presalida($IDClub,$IDSocio,$Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getpresalida','".json_encode($_POST)."','".json_encode($respuesta)."')");
		exit;
	break;

	//Marcar Presalida cuando se hace por el módulo de busqueda de cualquier persona
	case "setpresalidacualquiera":
		$IDSocio = $_POST["IDSocio"];
		$IDInvitacion = $_POST["IDInvitacion"];
		$TipoInvitacion = $_POST["TipoInvitacion"];
		$TipoSocio = $_POST["TipoSocio"];
		$respuesta = SIMWebServiceQuemado::set_presalida_cualquiera($IDClub,$IDSocio,$IDInvitacion,$TipoInvitacion,$TipoSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpresalidacualquiera','".json_encode($_POST)."','".json_encode($respuesta)."')");
		exit;
	break;


	//Presalida Invitado
	case "setpresalida":
		$IDSocio = $_POST["IDSocio"];
		$IDInvitacion = $_POST["IDInvitacion"];
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcambiarsegundaclave','".json_encode($_POST)."','".json_encode($respuesta)."')");
		$respuesta = SIMWebServiceQuemado::set_presalida($IDClub,$IDSocio,$IDInvitacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Presalida Contratista
	case "setpresalidacontratista":
		$IDSocio = $_POST["IDSocio"];
		$IDInvitacion = $_POST["IDInvitacion"];
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcambiarsegundaclave','".json_encode($_POST)."','".json_encode($respuesta)."')");
		$respuesta = SIMWebServiceQuemado::set_presalida_contratista($IDClub,$IDSocio,$IDInvitacion);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "validasegundaclave":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Clave = SIMNet::req( "Clave" );
		$respuesta = SIMWebServiceQuemado::valida_segunda_clave($IDClub,$IDSocio,$Clave);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//get informacion de acceso del club
	case "getparametroacceso":
		$respuesta = SIMWebServiceQuemado::get_parametro_acceso($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//get informacion de acceso de app porteria
	case "getparametrosempleados":
		$respuesta = SIMWebServiceQuemado::get_parametros_empleados($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//traer invitados/contratista por cedula
	case "getinvitadodocumento":
		$AppVersion = SIMNet::req( "AppVersion" );
		$Documento = SIMNet::req( "Documento" );
		if((int)$AppVersion<=11):
			$respuesta = SIMWebServiceQuemado::get_invitado_documento($IDClub,$Documento);
			$serv="getinvitadodocumentov1";
		else:
			$respuesta = SIMWebServiceQuemado::get_invitado_documento_v2($IDClub,$Documento);
			$serv="getinvitadodocumentov2";
		endif;
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('".$serv."','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//registrar ingreso invitado/contratista
	case "setentradainvitado":
		$IDInvitacion = $Tee = $_POST["IDInvitacion"];
		$TipoInvitacion = $_POST["TipoInvitacion"];
		$IDUsuario = $_POST["IDUsuario"];
		$Mecanismo = $_POST["Mecanismo"];
		$Respuestas = $_POST["Respuestas"];
		$respuesta = SIMWebServiceQuemado::set_entrada_invitado($IDClub,$IDInvitacion,$TipoInvitacion,$Mecanismo,$IDUsuario,$Respuestas);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setentradainvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//registrar salida invitado/contratista
	case "setsalidainvitado":
		$IDInvitacion = $Tee = $_POST["IDInvitacion"];
		$TipoInvitacion = $_POST["TipoInvitacion"];
		$IDUsuario = $_POST["IDUsuario"];
		$Mecanismo = $_POST["Mecanismo"];
		$Respuestas = $_POST["Respuestas"];
		$respuesta = SIMWebServiceQuemado::set_salida_invitado($IDClub,$IDInvitacion,$TipoInvitacion,$Mecanismo,$IDUsuario,$Respuestas);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsalidainvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setfacturaconsumo":
		$Accion = $_POST["Accion"];
		$Cedula = $_POST["Cedula"];
		$RucAsociado = $_POST["RucAsociado"];
		$TipoSocio = $_POST["TipoSocio"];
		$NumeroDocumentoFactura = $_POST["NumeroDocumentoFactura"];
		$Total = $_POST["Total"];
		$Iva = $_POST["Iva"];
		$Servicio = $_POST["Servicio"];
		$Estado = $_POST["Estado"];
		$IDFactura=$_POST["IDFactura"];
		$SubTotal0=$_POST["SubTotal0"];
		$SubTotal12=$_POST["SubTotal12"];
		$Detalle = utf8_encode($_POST["Detalle"]);
		$TextoRecibo = utf8_encode($_POST["TextoRecibo"]);
		$respuesta = SIMWebServiceQuemado::set_factura_consumo($IDClub,$Accion,$Cedula,$TipoSocio,$NumeroDocumentoFactura,$Total,$Iva,$Servicio,$Estado,$IDFactura,$Detalle,$TextoRecibo,$SubTotal0,$SubTotal12,$RucAsociado);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setfacturaconsumo','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getestadofacturaconsumo":
		$Accion = $_POST["Accion"];
		$Cedula = $_POST["Cedula"];
		$NumeroDocumentoFactura = $_POST["NumeroDocumentoFactura"];
		$respuesta = SIMWebServiceQuemado::get_estado_factura_consumo($IDClub,$Accion,$Cedula,$NumeroDocumentoFactura);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getestadofacturaconsumo','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "getagenda":
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$Fecha = SIMNet::req( "Fecha" );
		$respuesta = SIMWebServiceQuemado::get_agenda($IDClub,$IDUsuario,$Fecha);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getagenda','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	//Facturas
	case "getfactura":

		$IDSocio = SIMNet::req( "IDSocio" );
		$FechaInicio = SIMNet::req( "FechaInicio" );
		$FechaFin = SIMNet::req( "FechaFin" );
		$Dispositivo = SIMNet::req( "Dispositivo" );

		if($IDClub==10)//Rincon
			$respuesta = SIMWebServiceQuemado::get_factura_ftp($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==25)//Gun
			//$respuesta = SIMWebServiceQuemado::get_factura_zeus($IDClub,$IDSocio,$FechaInicio,$FechaFin);
			$respuesta = SIMWebServiceQuemado::get_factura_ftp_gun($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==46)//Anapoima
			$respuesta = SIMWebServiceQuemado::get_factura_ftp_anapoima($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==12)//Rancho
			$respuesta = SIMWebServiceQuemado::get_factura_ftp_r($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==72)//BTCC
			$respuesta = SIMWebServiceQuemado::get_factura_ftp_btcc($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==1)//Guaymaral
				$respuesta = SIMWebServiceQuemado::get_factura_ftp_g($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==29)//Farallones
					$respuesta = SIMWebServiceQuemado::get_factura_ftp_f($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==15)//Pereira
			$respuesta = SIMWebServiceQuemado::get_factura_pereira($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==71)//Puerto Tranquilo
				$respuesta = SIMWebServiceQuemado::get_factura_pto_tranquilo($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		//elseif($IDClub==41)//Mi Conjunto
				//$respuesta = SIMWebServiceQuemado::get_factura_mi_conjunto($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==59 || $IDClub==58)// Conjuntos fontanar
				$respuesta = SIMWebServiceQuemado::get_factura_fontanar($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==50)//Uraki
				$respuesta = SIMWebServiceQuemado::get_factura_uraki($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==41 || $IDClub==87) //Sigma
				$respuesta = SIMWebServiceQuemado::get_factura_sigma($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==20)//Medellin
				$respuesta = SIMWebServiceQuemado::get_factura_medellin($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==8)//Mi Club
				$respuesta = SIMWebServiceQuemado::get_factura_mi_club($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==38)//Club Colombia
				$respuesta = SIMWebServiceQuemado::get_factura_colombia($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==54)//Italiano
				$respuesta = SIMWebServiceQuemado::get_factura_italiano($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==23)//Arrayanes E
				$respuesta = SIMWebServiceQuemado::get_factura_arrayanes_ec($IDClub,$IDSocio,$FechaInicio,$FechaFin);
		elseif($IDClub==7){//Lagartos
			$respuesta = SIMWebServiceQuemado::get_factura_lagartos($IDClub,$IDSocio,$FechaInicio,$FechaFin);
			//inserta _log
			$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getfacturalagartos','".json_encode($_GET)."','".json_encode($respuesta)."')");
		}
		else
			$respuesta = SIMWebServiceQuemado::get_factura($IDClub,$IDSocio,$FechaInicio,$FechaFin,$Dispositivo);


		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getfactura','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;




	//Detalle Facturas
	case "getdetallefactura":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDFactura = SIMNet::req( "IDFactura" );
		$NumeroFactura = SIMNet::req( "NumeroFactura" );

		if($IDClub==10)//Rincon
			$respuesta = SIMWebServiceQuemado::get_detalle_factura_app($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==25)//Gun
			//$respuesta = SIMWebServiceQuemado::get_detalle_factura_zeus($IDClub,$IDFactura,$NumeroFactura);
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_gun($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==15)//Gun
			$respuesta = SIMWebServiceQuemado::get_detalle_factura_pereira($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==12)//Rancho
			$respuesta = SIMWebServiceQuemado::get_detalle_factura_rancho($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==72)//BTCC
			$respuesta = SIMWebServiceQuemado::get_detalle_factura_btcc($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==46)//Anapoima
			$respuesta = SIMWebServiceQuemado::get_detalle_factura_anapoima($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==1)//Guaymaral
			$respuesta = SIMWebServiceQuemado::get_detalle_factura_guaymaral($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==29)//Farallones
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_farallones($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==7)//Lagartos
			$respuesta = SIMWebServiceQuemado::get_detalle_factura_lagartos($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==71)//Pto Tranquilo
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_pto_tranquilo($IDClub,$IDFactura,$NumeroFactura);
		//elseif($IDClub==41)//Mi Conjunto
				//$respuesta = SIMWebServiceQuemado::get_detalle_factura_mi_conjunto($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==38)//Club Colombia
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_colombia($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==54)//Italiano
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_italiano($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==58 || $IDClub==59)//Pto Tranquilo
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_fontanar($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==50)//Uraki
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_uraki($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==41 || $IDClub==87)//Sigma
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_sigma($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==20)//Medellin
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_medellin($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==23)//Arrayanes EC
				$respuesta = SIMWebServiceQuemado::get_detalle_factura_arrayanes_ec($IDClub,$IDFactura,$NumeroFactura);
		elseif($IDClub==8)//Mi Club
			$respuesta = SIMWebServiceQuemado::get_detalle_factura_mi_club($IDClub,$IDFactura,$NumeroFactura);
		else
			$respuesta = SIMWebServiceQuemado::get_detalle_factura($IDClub,$IDFactura,$NumeroFactura);


		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdetallefactura','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Certificados
	case "getcertificado":
		$IDSocio = SIMNet::req( "IDSocio" );
		$FechaInicio = SIMNet::req( "FechaInicio" );
		$FechaFin = SIMNet::req( "FechaFin" );
		$Dispositivo = SIMNet::req( "Dispositivo" );

		if($IDClub==12)//Rancho
			$respuesta = SIMWebServiceQuemado::get_certificado($IDClub,$IDSocio,$FechaInicio,$FechaFin);

		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getfactura','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Codigo Pago
	case "setcodigopago":
		$IDSocio = $_POST["IDSocio"];
		$IDFactura = $_POST["IDFactura"];
		$Codigo = $_POST["Codigo"];
		$respuesta = SIMWebServiceQuemado::set_codigo_pago($IDClub,$IDSocio,$IDFactura,$Codigo);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcodigopago','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Extractos Gun Club
	case "getextracto":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceZeus::consulta_extracto($IDClub,$IDSocio);
		//$respuesta = SIMWebServiceZeus::consulta_extracto("25","85176"); //TEMPORAL PARA PRUEBAS
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getextracto','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Movimientos Gun Club
	case "getmovimiento":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Mes=SIMNet::req( "Mes" );
		//$respuesta = SIMWebServiceZeus::consulta_extracto($IDClub,$IDSocio,$Mes);
		//$respuesta = SIMWebServiceZeus::consulta_movimiento("25","85176","08"); //TEMPORAL PARA PRUEBAS
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getextracto','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//registrar estado cuenta ws
	case "setestadocuentaws":
		$IDRegistro =  $_POST["IDRegistro"];
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$Accion = $_POST["Accion"];
		$Secuencia = $_POST["Secuencia"];
		$Concepto = $_POST["Concepto"];
		$Valor = $_POST["Valor"];
		$Fecha = $_POST["Fecha"];
		$Observaciones = $_POST["Observaciones"];
		$respuesta = SIMWebServiceQuemado::set_estado_cuenta_ws($IDClub,$IDRegistro,$NumeroDocumento,$Accion,$Secuencia,$Concepto,$Valor,$Fecha,$Observaciones);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setestadocuentaws','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//registrar estado cuenta ws
	case "setborrarestadocuentaws":
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$respuesta = SIMWebServiceQuemado::set_borrar_estado_cuenta_ws($IDClub,$NumeroDocumento);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_borrar_estado_cuenta_ws','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	/******************************************************
	/*SERVICIOS FEDEGOLF
	/******************************************************/

	//Clubes Fedegolf
	case "getclubfedegolf":
		$respuesta = SIMWebServiceFedegolf::get_clubes();
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Canchas Fedegolf segun club
	case "getcanchafedegolf":
		$IDClubFedegolf = SIMNet::req( "IDClubFedegolf" );
		$respuesta = SIMWebServiceFedegolf::get_canchas($IDClubFedegolf);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Marcas Fedegolf segun club - cancha
	case "getmarcafedegolf":
		$IDClubFedegolf = SIMNet::req( "IDClubFedegolf" );
		$IDCancha = SIMNet::req( "IDCancha" );
		$respuesta = SIMWebServiceFedegolf::get_marcas($IDClubFedegolf, $IDCancha);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Marcas Fedegolf segun club - cancha
	case "gethandicapfedegolf":
		$Curva = SIMNet::req( "Curva" );
		$Indice = SIMNet::req( "Indice" );
		$respuesta = SIMWebServiceFedegolf::get_handicap($Curva, $Indice);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Autenticacion Usuarios Fedegolf
	case "getautenticacionfedegolf":
		$Email = SIMNet::req( "Email" );
		$Pwd = SIMNet::req( "Pwd" );
		$respuesta = SIMWebServiceFedegolf::get_autenticacion($Email, $Pwd);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//usuario por numero de codigo Fedegolf
	case "getusuariocodigofedegolf":
		$Codigo = SIMNet::req( "Codigo" );
		$respuesta = SIMWebServiceFedegolf::get_usuario_codigo($Codigo);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Buscar usuario por parametro Fedegolf
	case "getusuarionombrefedegolf":
		$Tag = SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceFedegolf::get_usuario_nombre($Tag);
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getusuarionombrefedegolf','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Carne
	case "carnefedegolf":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceFedegolf::carne_fedegolf($IDClub,$IDSocio);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Lista de juegos
	case "getgamesjugador":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Codigo = SIMNet::req( "Codigo" );
		$respuesta = SIMWebServiceFedegolf::get_games_jugador($IDClub,$IDSocio,$Codigo);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Detalle tarjeta juego
	case "getgamesfedegolf":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Codigo = SIMNet::req( "Codigo" );
		$Score = SIMNet::req( "Score" );
		$respuesta = SIMWebServiceFedegolf::get_games($IDClub,$IDSocio,$Score,$Codigo);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	// Crear socio
	case "setsocio":
		$Accion= $_POST["Accion"];
		$AccionPadre= $_POST["AccionPadre"];
		$Parentesco= $_POST["Parentesco"];
		$Genero= $_POST["Genero"];
		$Nombre= $_POST["Nombre"];
		$Apellido= $_POST["Apellido"];
		$FechaNacimiento= $_POST["FechaNacimiento"];
		$NumeroDocumento= $_POST["NumeroDocumento"];
		$CorreoElectronico= $_POST["CorreoElectronico"];
		$Telefono= $_POST["Telefono"];
		$Celular= $_POST["Celular"];
		$Direccion= $_POST["Direccion"];
		$TipoSocio= $_POST["TipoSocio"];
		$EstadoSocio= $_POST["EstadoSocio"];
		$InvitacionesPermitidasMes= $_POST["InvitacionesPermitidasMes"];
		$UsuarioApp= $_POST["UsuarioApp"];
		$Predio= $_POST["Predio"];
		$Categoria= $_POST["Categoria"];
		$CodigoCarne= $_POST["CodigoCarne"];
		$datos_post = $Accion . " " . $AccionPadre . " " . $Parentesco . " " . $Genero . " " . $Nombre . " " . $Apellido . " " .  $FechaNacimiento . " " .$NumeroDocumento . " " .$CorreoElectronico . " " .$Telefono . " " .$Celular . " " .$Direccion	. " " .$TipoSocio . " " .$EstadoSocio . " " .$InvitacionesPermitidasMes;

		$respuesta = SIMWebServiceQuemado::set_socio($IDClub,$Accion,$AccionPadre,$Parentesco,$Genero,$Nombre,$Apellido,$FechaNacimiento,$NumeroDocumento,$CorreoElectronico,$Telefono,$Celular,$Direccion,$TipoSocio,$EstadoSocio,$InvitacionesPermitidasMes,$UsuarioApp,$Predio,$Categoria,"",$CodigoCarne);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','POST 1: ".$datos_post."','".json_encode($respuesta)."')");
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','POST 2 ".json_encode($_POST)."','".json_encode($respuesta)."')");
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','GET: ".json_encode($_GET)."','".json_encode($respuesta)."')");

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	// Actualizar datos socios
	case "setpropietario":

		$Nombre= utf8_encode($_POST["Nombre"]);
		$Apellido= utf8_encode($_POST["Apellido"]);
		$NumeroDocumento= $_POST["NumeroDocumento"];
		$CorreoElectronico= $_POST["CorreoElectronico"];
		$Telefono= $_POST["Telefono"];
		$Celular= $_POST["Celular"];
		$Portal= utf8_encode($_POST["Portal"]);
		$Casa= $_POST["Casa"];
		$Llave= $_POST["Llave"];
		$AccionRegistro= $_POST["AccionRegistro"];	// Update, delete, insert
		$datos_post = $Nombre . " " . $Apellido . " " . $NumeroDocumento . " " . $CorreoElectronico . " " . $Telefono . " " . $Celular . " " . $Portal . " " .$Casa . " " .$Llave . " " .$AccionRegistro;
		$respuesta = SIMWebServiceQuemado::set_propietario($IDClub,$Nombre,$Apellido,$NumeroDocumento,$CorreoElectronico,$Telefono,$Celular,$Portal,$Casa,$Llave,$AccionRegistro);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpropietario','".$datos_post."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	/******************************************************
	/*FIN SERVICIOS FEDEGOLF
	/******************************************************/



	/******************************************************
	/*SERVICIOS HOTEL
	/******************************************************/

	case "getconfiguracionhotel":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceHotel::get_configuracion_hotel($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	// Reservas hotel
	case "getmisreservashotel":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDReserva = SIMNet::req( "IDReserva" );
		//$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
		if($IDClub==27): //Payande
			$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisreservashotel&IDSocio=".$IDSocio);
			$respuesta= json_decode($datos, true);
		else:
				$respuesta = SIMWebServiceHotel::get_mis_reservas($IDSocio,$IDReserva);
		endif;

		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpropietario','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Valida fecha
	case "getvalidafecha":
		$FechaInicio = SIMNet::req( "FechaInicio" );
		$FechaFin = SIMNet::req( "FechaFin" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$version = SIMNet::req( "AppVersion" );

		if($IDClub==27): //Payande
			if((int)$version<26){
				$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('mensajeversion','".json_encode($_GET)."','".json_encode($respuesta)."')");
				$respuesta["message"] = "Por favor descargue la ultima version del app";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
				exit;
			}
			else{
				$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getvalidafecha&FechaInicio=".$FechaInicio."&FechaFin=".$FechaFin);
				$respuesta= json_decode($datos, true);
			}
		else:
			$respuesta = SIMWebServiceHotel::get_valida_fecha($IDClub, $FechaInicio,$FechaFin,"N",$IDSocio);
		endif;
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getvalidafecha','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );

	break;

	case "settipopagoreservahotel":
		$IDSocio = $_POST["IDSocio"];
		$IDReserva = $_POST["IDReserva"];
		$IDTipoPago = $_POST["IDTipoPago"];
		$CodigoPago = $_POST["CodigoPago"];
		if($IDClub==27){ //Payande
			$key="P4y4nd3Reser";
			$action="settipopagoreservahotel";
			$url="http://www.clubpayande.com/reservas/services/club.php";
			$post = [
				'key' => $key,
				'action' => $action,
				'IDSocio'   => $IDSocio,
				'IDReserva'   => $IDReserva,
				'IDTipoPago'   => $IDTipoPago,
				'CodigoPago'   => $CodigoPago
			];

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			// execute!
			$response = curl_exec($ch);
			// close the connection, release resources used
			curl_close($ch);
			print_r($response);
			exit;

		}
		else{
			$respuesta = SIMWebServiceQuemado::set_tipo_pago_reserva_hotel($IDClub,$IDSocio,$IDReserva,$IDTipoPago,$CodigoPago);
		}

		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Invitados Hotel
	case "getmisinvitadoshotel":
		$IDSocio = SIMNet::req( "IDSocio" );
		//$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
		if($IDClub==27): //Payande
			$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisinvitadoshotel&IDSocio=".$IDSocio);
			$respuesta= json_decode($datos, true);
		else:
			$respuesta = SIMWebServiceHotel::get_mis_invitados($IDSocio);
		endif;

		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpropietario','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Invitados Hotel
	case "setinvitadoshotel":
		$IDSocio = SIMNet::req( "IDSocio" );
		//$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
		$Documento = $_POST["Documento"];
		$Nombre = $_POST["Nombre"];
		$Apellido = $_POST["Apellido"];
		$Email = $_POST["Email"];
		$MenorEdad = $_POST["MenorEdad"];

		if($IDClub==27): //Payande
			$key="P4y4nd3Reser";
			$action="setinvitadoshotel";
			$url="http://www.clubpayande.com/reservas/services/club.php";
			$post = [
				'key' => $key,
				'action' => $action,
				'IDSocio'   => $IDSocio,
				'Documento'   => $Documento,
				'Nombre'   => $Nombre,
				'Apellido'   => $Apellido,
				'Email'   => $Email
			];

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			// execute!
			$response = curl_exec($ch);
			// close the connection, release resources used
			curl_close($ch);
			print_r($response);
			exit;
		else:
			$respuesta = SIMWebServiceHotel::set_invitados_hotel($IDSocio,$Documento,$Nombre,$Apellido,$Email,$MenorEdad);
			$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setinvitadoshotel','".json_encode($_POST)."','".json_encode($respuesta)."')");

		endif;

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "seteditainvitadohotel":
		$IDSocio = $_POST["IDSocio"];
		$IDReserva = $_POST["IDReserva"];
		$IDReservaHotelInvitado =$_POST["IDReservaHotelInvitado"];
		$IDSocioInvitado = $_POST["IDSocioInvitado"];
		$TipoInvitado = $_POST["TipoInvitado"];
		$Documento = $_POST["Documento"];
		$Nombre = $_POST["Nombre"];
		$Apellido = $_POST["Apellido"];
		$Email = $_POST["Email"];
		$MenorEdad = $_POST["MenorEdad"];
		$respuesta = SIMWebServiceHotel::set_edita_invitado_hotel($IDSocio,$IDReserva,$IDReservaHotelInvitado,$IDSocioInvitado,$TipoInvitado,$Documento,$Nombre,$Apellido,$Email,$MenorEdad);
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setinvitadoshotel','".json_encode($_POST)."','".json_encode($respuesta)."')");

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setagregainvitadohotel":
		$IDSocio = $_POST["IDSocio"];
		$IDReserva = $_POST["IDReserva"];
		$AcompananteSocio= $_POST["AcompananteSocio"];
		$respuesta = SIMWebServiceHotel::set_agrega_invitado_hotel($IDClub,$IDSocio,$IDReserva,$AcompananteSocio);
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_agrega_invitado_hotel','".json_encode($_POST)."','".json_encode($respuesta)."')");

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "seteliminainvitadohotel":
		$IDReserva = SIMNet::req( "IDReserva" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDReservaHotelInvitado = SIMNet::req( "IDReservaHotelInvitado" );
		$IDSocioInvitado = SIMNet::req( "IDSocioInvitado" );
		$TipoInvitado = SIMNet::req( "TipoInvitado" );
		$respuesta = SIMWebServiceHotel::set_elimina_invitado_hotel($IDReserva,$IDSocio,$IDReservaHotelInvitado,$TipoInvitado,$IDSocioInvitado);
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('seteliminainvitadoshotel','".json_encode($_GET)."','".json_encode($respuesta)."')");

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setreservahotel":

		$IDSocioMiClub = $_POST["IDSocio"];
		$AccionSocio = $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$IDSocioMiClub."'" );
		//Averiguo el id del socio en el sistema de payande
		$IDSocio = $_POST["IDSocio"];
		//$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
		$IDBeneficiario = $_POST["IDBeneficiario"];
		$IDHabitacion = $_POST["IDHabitacion"];
		$IDPromocion = $_POST["IDPromocion"];
		$IDTemporadaAlta = $_POST["IDTemporadaAlta"];
		$Temporada = $_POST["Temporada"];
		$CabezaReserva = $_POST["CabezaReserva"];
		$Estado = $_POST["Estado"];
		$FechaInicio = $_POST["FechaInicio"];
		$FechaFin = $_POST["FechaFin"];
		$Ninera = $_POST["Ninera"];
		$Corral = $_POST["Corral"];
		$Observaciones = $_POST["Observaciones"];
		//$Valor = $_POST["Valor"];
		//$IVA = $_POST["IVA"];
		$NumeroPersonas = $_POST["NumeroPersonas"];
		$Adicional = $_POST["Adicional"];
		$IDInvitadoHotel = $_POST["IDInvitadoHotel"];
		//$Pagado = $_POST["Pagado"];
		//$FechaReserva = $_POST["FechaReserva"];

		$NombreDuenoReserva=$_POST["Nombre"];
		$DocumentoDuenoReserva=$_POST["Cedula"];
		$EmailDuenoReserva=$_POST["Email"];

		//Ajusto los datos que se envian al servicio de payande
		$datos_invitado= json_decode($_POST["AcompananteSocio"], true);
		$contador_invitado=0;
		if (count($datos_invitado)>0):
			foreach($datos_invitado as $datos_invitado_reserva):
				$IDSocioInvitadoTurno = $datos_invitado_reserva["IDSocio"];
				$NombreSocioInvitadoTurno = $datos_invitado_reserva["Nombre"];
				$Familiar="N";
				$array_datos_acompanante [$contador_invitado]["IDSocio"] = $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$datos_invitado_reserva["IDSocio"]."'" );
				$array_datos_acompanante [$contador_invitado]["Nombre"] = $NombreSocioInvitadoTurno;
				$array_datos_acompanante [$contador_invitado]["IDInvitado"] = $datos_invitado_reserva["IDInvitado"];
				//Verifico si es familiar
				if(!empty($IDSocioInvitadoTurno)):
					$AccionSocioInvitado = $dbo->getFields( "Socio" , "AccionPadre" , "IDSocio = '".$IDSocioInvitadoTurno."'" );
					$array_datos_acompanante [$contador_invitado]["Nombre"]  = $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocioInvitadoTurno."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocioInvitadoTurno."'" );
					if($AccionSocioInvitado==$AccionSocio)
						$Familiar="S";
				endif;
				$array_datos_acompanante [$contador_invitado]["Familiar"] = $Familiar;
				$contador_invitado++;
			endforeach;
		endif;

		$AcompananteSocio= json_encode($array_datos_acompanante);

		if($IDClub==27): //Payande

				$key="P4y4nd3Reser";
				$action="setreserva";

				$url="http://www.clubpayande.com/reservas/services/club.php";

				$post = [
					'key' => $key,
					'action' => $action,
					'IDSocioMiClub'   => $IDSocioMiClub,
					'IDInvitadoHotel'   => $IDInvitadoHotel,
					'IDBeneficiario'   => $IDBeneficiario,
					'AccionSocio'   => $AccionSocio,
					'IDSocio'   => $IDSocio,
					'IDHabitacion'   => $IDHabitacion,
					'IDPromocion'   => $IDPromocion,
					'IDTemporadaAlta'   => $IDTemporadaAlta,
					'Temporada'   => $Temporada,
					'CabezaReserva'   => $CabezaReserva,
					'FechaInicio'   => $FechaInicio,
					'FechaFin'   => $FechaFin,
					'Ninera'   => $Ninera,
					'Corral'   => $Corral,
					'NumeroPersonas'   => $NumeroPersonas,
					'Adicional'   => $Adicional,
					'AcompananteSocio'   => $AcompananteSocio
				];

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

				// execute!
				$response = curl_exec($ch);

				// close the connection, release resources used
				curl_close($ch);
				//inserta _log
				$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setreservahotel','".json_encode($_POST)."','".json_encode($response)."')");
				print_r($response);
				exit;
		else:
			$AcompananteSocio= $_POST["AcompananteSocio"];
			$respuesta = SIMWebServiceHotel::set_reserva($IDClub,$IDSocio,$IDInvitadoHotel,$IDBeneficiario,$IDHabitacion,$IDPromocion,$IDTemporadaAlta,$Temporada,$CabezaReserva,$Estado,$FechaInicio,$FechaFin,$Ninera,$Corral,$IVA,$NumeroPersonas,$Adicional,$Pagado,$FechaReserva,$AcompananteSocio,"","",$NombreDuenoReserva,$DocumentoDuenoReserva,$EmailDuenoReserva,$Observaciones);
			//inserta _log
			$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setreservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");

		endif;

		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Notificar lista de espera
	case "notificarlistaesperahotel":
		$FechaInicio = $_POST["FechaInicio"];
		$FechaFin = $_POST["FechaFin"];
		$respuesta = SIMUtil::notificar_lista_espera_hotel($IDClub,$FechaInicio,$FechaFin);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Eliminar Reserva Hotel
	case "eliminareservahotel":
		$IDSocio = $_POST["IDSocio"];
		$IDReserva = $_POST["IDReserva"];
		if($IDClub==27): //Payande
				$key="P4y4nd3Reser";
				$action="eliminareservahotel";
				$url="http://www.clubpayande.com/reservas/services/club.php";
				$post = [
					'key' => $key,
					'action' => $action,
					'IDSocioMiClub'   => $IDSocio,
					'IDReserva'   => $IDReserva
				];
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				// execute!
				$response = curl_exec($ch);
				// close the connection, release resources used
				curl_close($ch);
				print_r($response);
				//inserta _log
				$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminareservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
				exit;
		else:
			$respuesta = SIMWebServiceHotel::elimina_reserva_hotel($IDClub,$IDSocio,$IDReserva);
		endif;

		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('eliminareservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Validar reserva con pago
	case "validapagoreservahotel":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDReserva = SIMNet::req( "IDReserva" );

		if($IDClub==27): //Payande
				$key="P4y4nd3Reser";
				$action="validapagoreservahotel";
				$url="http://www.clubpayande.com/reservas/services/club.php";
				$post = [
					'key' => $key,
					'action' => $action,
					'IDSocio'   => $IDSocio,
					'IDReserva'   => $IDReserva
				];
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				// execute!
				$response = curl_exec($ch);
				// close the connection, release resources used
				curl_close($ch);
				print_r($response);
				$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('validapagoreservahotel','".json_encode($_GET)."','".json_encode($respuesta)."')");
				exit;
		else:
			//inserta _log
			$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('validapagoreservahotel','".json_encode($_GET)."','".json_encode($respuesta)."')");
			$respuesta = SIMWebServiceHotel::valida_pago_reserva_hotel($IDSocio,$IDReserva);
		endif;


		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	/******************************************************
	/*FIN SERVICIOS HOTEL
	/******************************************************/



	/******************************************************
	SERVICIOS ESPECIAL CONDADO
	/******************************************************/
	case "setcrearcuenta":
		$Accion = $_POST["Accion"];
		$Identificacion = $_POST["Identificacion"];
		$CorreoElectronico = $_POST["Correo"];
		$respuesta = SIMWebServiceQuemado::set_crear_cuenta($IDClub,$Accion,$Identificacion,$CorreoElectronico);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcrearcuenta','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Header para nuevo diseño de club
 	case "getheaderclub":
 		$respuesta = SIMWebServiceQuemado::get_header_club($IDClub);
 		//inserta _log
 		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getheaderclub','".json_encode($_GET)."','".json_encode($respuesta)."')");
 		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
 		exit;
 	break;

	case "setactualizadatos":
		$IDSocio = $_POST["IDSocio"];
		$Direccion = $_POST["Direccion"];
		$Telefono = $_POST["Telefono"];
		$DireccionOficina = $_POST["DireccionOficina"];
		$TelefonoOficina = $_POST["TelefonoOficina"];
		$Celular = $_POST["Celular"];
		$CorreoElectronico = $_POST["CorreoElectronico"];
		$respuesta = SIMWebServiceQuemado::set_actualiza_datos($IDClub,$IDSocio,$Direccion,$Telefono,$DireccionOficina,$TelefonoOficina,$Celular,$CorreoElectronico);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setactualizadatos','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getdatossociows":
 		$respuesta = SIMWebServiceQuemado::get_datos_socio_ws($IDClub,$IDSocio);
 		//inserta _log
 		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getheaderclub','".json_encode($_GET)."','".json_encode($respuesta)."')");
 		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
 		exit;
 	break;

	case "getdeudasocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_deuda_socio($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdeudasocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setdeudasocio":
		$IDSocio = $_POST["IDSocio"];
		$Documento = $_POST["Documento"];
		$ValorPagar= $_POST["ValorPagar"];
		$respuesta = SIMWebServiceQuemado::set_deuda_socio($IDClub,$IDSocio,$Documento,$ValorPagar);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setdeudasocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getplacetopaytransacciones":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_place_to_pay_transacciones($IDClub,$IDSocio);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setdeudasocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	/******************************************************
	FIN SERVICIOS ESPECIAL CONDADO
	/******************************************************/


	//Pago Reserva
	case "settipopagoreserva":
		$IDSocio = $_POST["IDSocio"];
		$IDReserva = $_POST["IDReserva"];
		$IDTipoPago = $_POST["IDTipoPago"];
		$CodigoPago = $_POST["CodigoPago"];
		$respuesta = SIMWebServiceQuemado::set_tipo_pago_reserva($IDClub,$IDSocio,$IDReserva,$IDTipoPago,$CodigoPago);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getcodigopago":
		$CodigoPago = SIMNet::req( "CodigoPago" );
		$respuesta = SIMWebServiceQuemado::get_codigo_pago($IDClub,$CodigoPago);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcodigopago','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Pago Evento
	case "settipopagoevento":
		$IDSocio = $_POST["IDSocio"];
		$IDEventoRegistro = $_POST["IDEventoRegistro"];
		$IDTipoPago = $_POST["IDTipoPago"];
		$CodigoPago = $_POST["CodigoPago"];
		$respuesta = SIMWebServiceQuemado::set_tipo_pago_evento($IDClub,$IDSocio,$IDEventoRegistro,$IDTipoPago,$CodigoPago);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "settipopagodomicilio":
		$IDSocio = $_POST["IDSocio"];
		$IDDomicilio = $_POST["IDDomicilio"];
		$IDTipoPago = $_POST["IDTipoPago"];
		$CodigoPago = $_POST["CodigoPago"];
		$respuesta = SIMWebServiceQuemado::set_tipo_pago_domicilio($IDClub,$IDSocio,$IDDomicilio,$IDTipoPago,$CodigoPago);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagodomicilio','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "settipopagodomicilio2":
		$IDSocio = $_POST["IDSocio"];
		$IDDomicilio = $_POST["IDDomicilio"];
		$IDTipoPago = $_POST["IDTipoPago"];
		$CodigoPago = $_POST["CodigoPago"];
		$respuesta = SIMWebServiceQuemado::set_tipo_pago_domicilio($IDClub,$IDSocio,$IDDomicilio,$IDTipoPago,$CodigoPago,"2");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "settipopagoevento2":
		$IDSocio = $_POST["IDSocio"];
		$IDEventoRegistro = $_POST["IDEventoRegistro"];
		$IDTipoPago = $_POST["IDTipoPago"];
		$CodigoPago = $_POST["CodigoPago"];
		$respuesta = SIMWebServiceQuemado::set_tipo_pago_evento($IDClub,$IDSocio,$IDEventoRegistro,$IDTipoPago,$CodigoPago,"2");
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "getcodigopagoevento":
		$CodigoPago = SIMNet::req( "CodigoPago" );
		$respuesta = SIMWebServiceQuemado::get_codigo_pago($IDClub,$CodigoPago);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcodigopago','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "setcerrarsesion":
		$IDSocio = SIMNet::req( "IDSocio" );
		//$respuesta = SIMWebServiceQuemado::set_cerrar_sesion($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcerrarsesion','".json_encode($_GET)."','".json_encode($respuesta)."')");
		//die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "getdatossocio":
		$Identificacion = SIMNet::req( "Identificacion" );
		$Todos=SIMNet::req( "Todos" ); // S / N
		$respuesta = SIMWebServiceQuemado::get_datos_socio($IDClub,$Identificacion, $Todos);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcerrarsesion','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getmodulowebview":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDModulo = SIMNet::req( "IDModulo" );
		$respuesta = SIMWebServiceQuemado::get_moduloweb_view($IDClub,$IDSocio,$IDModulo);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmodulowebview','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getcampoacceso":
		$respuesta = SIMWebServiceQuemado::get_campo_acceso($IDClub);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcampoacceso','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;

		case "getdiagnostico":
			$IDSocio = SIMNet::req( "IDSocio" );
			$IDUsuario = SIMNet::req( "IDUsuario" );
			$respuesta = SIMWebServiceQuemado::get_diagnostico($IDClub,$IDSocio,$IDUsuario);
			//inserta _log
			//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnostico','".json_encode($_GET)."','".json_encode($respuesta)."')");
			die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
			exit;
		break;

		case "setrespuestadiagnostico":
			$IDSocio = $_POST["IDSocio"];
			$IDUsuario = $_POST["IDUsuario"];
			$IDDiagnostico = $_POST["IDDiagnostico"];
			$NumeroDocumento = $_POST["NumeroDocumento"];
			$Nombre = $_POST["Nombre"];
			$Respuestas = $_POST["Respuestas"];
			$respuesta = SIMWebServiceQuemado::set_respuesta_diagnostico($IDClub,$IDSocio,$IDDiagnostico,$Respuestas,$IDUsuario,$NumeroDocumento,$Nombre);
			//inserta _log
			$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestadiagnostico','".json_encode($_POST)."','".json_encode($respuesta)."')");
			die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
			exit;
		break;

		case "getdiagnosticopersona":
			$Fecha = $_POST["Fecha"];
			$NumeroDocumento = $_POST["NumeroDocumento"];
			$respuesta = SIMWebServiceQuemado::get_diagnostico_persona($IDClub,$NumeroDocumento,$Fecha);
			//inserta _log
			//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnosticopersona','".json_encode($_POST)."','".json_encode($respuesta)."')");
			die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
			exit;
		break;

		case "getconfiguracionencuestacalificacion":
			$IDSocio = SIMNet::req( "IDSocio" );
			$IDUsuario = SIMNet::req( "IDUsuario" );
			$respuesta = SIMWebServiceQuemado::get_configuracion_encuesta_calificacion($IDClub,$IDSocio,$IDUsuario);
			//inserta _log
			//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionenecuestacalificacion','".json_encode($_GET)."','".json_encode($respuesta)."')");
			die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
			exit;
		break;


		case "getencuestacalificacion":
			$IDSocio = SIMNet::req( "IDSocio" );
			$IDUsuario = SIMNet::req( "IDUsuario" );
			$respuesta = SIMWebServiceQuemado::get_encuesta_calificacion($IDClub,$IDSocio,$IDUsuario);
			//inserta _log
			//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnostico','".json_encode($_GET)."','".json_encode($respuesta)."')");
			die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
			exit;
		break;

		case "setrespuestaencuestacalificacion":
			$IDSocio = $_POST["IDSocio"];
			$IDUsuario = $_POST["IDUsuario"];
			$IDEncuesta = $_POST["IDEncuesta"];
			$Respuestas = $_POST["Respuestas"];
			$respuesta = SIMWebServiceQuemado::set_respuesta_encuesta_calificacion($IDClub,$IDSocio,$IDEncuesta,$Respuestas,$IDUsuario);
			//inserta _log
			$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestaencuestacalificacion','".json_encode($_POST)."','".json_encode($respuesta)."')");
			die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
			exit;
		break;



	case "getencuesta":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceQuemado::get_encuesta($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_encuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setrespuestaencuesta":
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$IDEncuesta = $_POST["IDEncuesta"];
		$Respuestas = $_POST["Respuestas"];
		$respuesta = SIMWebServiceQuemado::set_respuesta_encuesta($IDClub,$IDSocio,$IDEncuesta,$Respuestas,$IDUsuario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestaencuesta','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getdotacion":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceQuemado::get_dotacion($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdotacion','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setrespuestadotacion":
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$IDDotacion = $_POST["IDDotacion"];
		$Respuestas = $_POST["Respuestas"];
		$respuesta = SIMWebServiceQuemado::set_respuesta_dotacion($IDClub,$IDSocio,$IDDotacion,$Respuestas,$IDUsuario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestadotacion','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getrutas":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$Tag = SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_rutas($IDClub,$IDSocio,$IDUsuario,$Tag);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getrutas','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getpersonasruta":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDRuta = SIMNet::req( "IDRuta" );
		$respuesta = SIMWebServiceQuemado::get_personas_ruta($IDClub,$IDSocio,$IDUsuario,$IDRuta);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getpersonasruta','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setingresoruta":
		$IDRuta = $_POST["IDRuta"];
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$IDPersona = $_POST["IDPersona"];
		$Tipo = $_POST["Tipo"]; //Socio,Funcionario
		$respuesta = SIMWebServiceQuemado::set_ingreso_ruta($IDClub,$IDRuta,$IDSocio,$IDUsuario,$IDPersona,$Tipo);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setingresoruta','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getconfiguracionvotacion":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_votacion($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionvotacion','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getvotacion":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceQuemado::get_votacion($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_encuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setrespuestavotacion":
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$IDVotacion = $_POST["IDVotacion"];
		$Respuestas = $_POST["Respuestas"];
		$respuesta = SIMWebServiceQuemado::set_respuesta_votacion($IDClub,$IDSocio,$IDVotacion,$Respuestas,$IDUsuario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestavotacion','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	case "setactualizadatossocio":
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$Campos = $_POST["Campos"];
		$TipoApp = $_POST["TipoApp"];

		$respuesta = SIMWebServiceQuemado::set_actualiza_datos_socio($IDClub,$IDSocio,$Campos,$IDUsuario,$TipoApp);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setactualizadatossocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Correspondencia
	case "setcorrespondencia":
	if(count($_FILES)>0)
		$_POST = array_map('utf8_encode', $_POST);

		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$IDTipoCorrrespondencia = $_POST["IDTipoCorrrespondencia"];
		$Vivienda = $_POST["Vivienda"];
		$Destinatario = $_POST["Destinatario"];
		$FechaRecepcion = $_POST["FechaRecepcion"];
		$HoraRecepcion = $_POST["HoraRecepcion"];
		$EntregarATodos = $_POST["EntregarATodos"];
		$Archivo = $_POST["Archivo"];

		$respuesta = SIMWebServiceQuemado::set_correspondencia($IDClub,$IDSocio,$IDUsuario,$IDTipoCorrrespondencia,$Vivienda,$Destinatario,$FechaRecepcion,$HoraRecepcion,$EntregarATodos,$Archivo, $_FILES);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcorrespondencia','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setentregacorrespondencia":
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$IDCorrespondencia = $_POST["IDCorrespondencia"];
		$FechaEntrega = $_POST["FechaEntrega"];
		$HoraEntrega = $_POST["HoraEntrega"];
		$EntregadoA = $_POST["EntregadoA"];
		$respuesta = SIMWebServiceQuemado::set_entrega_correspondencia($IDClub,$IDSocio,$IDUsuario,$IDCorrespondencia,$FechaEntrega,$HoraEntrega,$EntregadoA);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setentregacorrespondencia','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getcorrespondencia":
		$IDSocio = SIMNet::req( "IDSocio" );
		$Tag = SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceQuemado::get_correspondencia($IDClub,$IDSocio,$Tag);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "gettipocorrespondencia":
		$respuesta = SIMWebServiceQuemado::get_tipo_correspondencia($IDClub);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettipocorrespondencia','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	//Configuracion correspondencia
	case "getconfiguracioncorrespondencia":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_correspondencia($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracioncorrespondencia','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	// Fin Correspondencia

	//Configuracion  Noticia
	case "getconfiguracionnoticias":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_noticias($IDClub,$IDSocio);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionnoticias','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;



	case "setcodigoqr":
		$IDSocio = $_POST["IDSocio"];
		$CodigoQR = $_POST["CodigoQR"];
		$respuesta = SIMWebServiceQuemado::set_codigo_qr($IDClub,$IDSocio,$CodigoQR);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setentregacorrespondencia','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setsolicitarvehiculo":
		$IDSocio = $_POST["IDSocio"];
		$Placa = $_POST["Placa"];
		$Tercero = $_POST["Tercero"];
		$respuesta = SIMWebServiceQuemado::set_solicitar_vehiculo($IDClub,$IDSocio,$Placa,$Tercero);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsolicitarvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setrecibirvehiculo":
		$IDUsuario = $_POST["IDUsuario"];
		$IDSocio = $_POST["IDSocio"];
		$Placa = $_POST["Placa"];
		$Cedula = $_POST["Cedula"];
		$Nombre = $_POST["Nombre"];
		$NumeroParqueadero = $_POST["NumeroParqueadero"];
		$respuesta = SIMWebServiceQuemado::set_recibir_vehiculo($IDClub,$IDSocio,$IDUsuario,$Placa,$Cedula,$Nombre,$NumeroParqueadero);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrecibirvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setentregarvehiculo":
		$IDUsuario = $_POST["IDUsuario"];
		$IDSocio = $_POST["IDSocio"];
		$Placa = $_POST["Placa"];
		$respuesta = SIMWebServiceQuemado::set_entregar_vehiculo($IDClub,$IDSocio,$IDUsuario,$Placa);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setentregarvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setcancelarentregarvehiculo":
		$IDUsuario = $_POST["IDUsuario"];
		$IDSocio = $_POST["IDSocio"];
		$Placa = $_POST["Placa"];
		$Tercero = $_POST["Tercero"];
		$respuesta = SIMWebServiceQuemado::set_cancelar_entregar_vehiculo($IDClub,$IDSocio,$IDUsuario,$Placa,$Tercero);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcancelarentregarvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getconfiguracionvalet":
		$respuesta = SIMWebServiceQuemado::get_configuracion_valet($IDClub);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionofertas','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getvehiculoregistradosocio":
		$IDSocio = SIMNet::req( "IDSocio" );
		$respuesta = SIMWebServiceQuemado::get_vehiculo_registrado_socio($IDClub,$IDSocio);

		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getvehiculoregistradosocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getsolicitudentrega":
		$Placa = SIMNet::req( "Placa" );
		$respuesta = SIMWebServiceQuemado::get_solicitud_entrega($IDClub,$Placa);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionofertas','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getconfiguracionregistrocontacto":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceQuemado::get_configuracion_registro_contacto($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionregistrocontacto','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getverificaaccion":
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDNotificacion = $_POST["IDNotificacion"];
		$respuesta = SIMWebServiceQuemado::get_verifica_accion($IDClub,$IDSocio,$IDUsuario,$IDNotificacion);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getnotificacionlocal','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setregistrocontacto":
		$IDUsuario = $_POST["IDUsuario"];
		$IDSocio = $_POST["IDSocio"];
		$FechaHora = $_POST["FechaHora"];
		$Lugar = $_POST["Lugar"];
		$Latitud = $_POST["Latitud"];
		$Longitud = $_POST["Longitud"];
		$Contactos = $_POST["Contactos"];
		$CamposFormulario = $_POST["CamposFormulario"];
		$respuesta = SIMWebServiceQuemado::set_registro_contacto($IDClub,$IDSocio,$IDUsuario,$FechaHora,$Lugar,$Latitud,$Longitud,$Contactos,$CamposFormulario);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setregistrocontacto','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//FAQS
	case "getconfiguracionfaq":
		require( LIBDIR . "SIMWebServiceFaq.inc.php" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceFaq::get_configuracion_faq($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	case "getcategoriasfaq":
		require( LIBDIR . "SIMWebServiceFaq.inc.php" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceFaq::get_categorias_faq($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	case "getpreguntasfaq":
		require( LIBDIR . "SIMWebServiceFaq.inc.php" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDCategoria = SIMNet::req( "IDCategoria" );
		$Tag = SIMNet::req( "Tag" );
		$respuesta = SIMWebServiceFaq::get_preguntas_faq($IDClub,$IDSocio,$IDUsuario,$IDCategoria,$Tag);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	case "setcalificarfaq":
		require( LIBDIR . "SIMWebServiceFaq.inc.php" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$IDPregunta = SIMNet::req("IDPregunta");
		$ResultoUtil = SIMNet::req( "ResultoUtil");
		$respuesta = SIMWebServiceFaq::set_calificar_faq($IDClub, $IDSocio, $IDUsuario, $IDPregunta, $ResultoUtil);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setpreguntafaq":
		require( LIBDIR . "SIMWebServiceFaq.inc.php" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$Correo = SIMNet::req("Correo");
		$Pregunta = SIMNet::req( "Pregunta");
		$respuesta = SIMWebServiceFaq::set_pregunta_faq($IDClub, $IDSocio, $IDUsuario, $Correo, $Pregunta);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;
	//FIN FAQS

	case "getmovilidad":
		require( LIBDIR . "SIMWebServiceEncuestaArbol.inc.php" );
		$IDSocio = SIMNet::req( "IDSocio" );
		$IDUsuario = SIMNet::req( "IDUsuario" );
		$respuesta = SIMWebServiceEncuestaArbol::get_movilidad($IDClub,$IDSocio,$IDUsuario);
		//inserta _log
		//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnostico','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setrespuestamovilidad":
		require( LIBDIR . "SIMWebServiceEncuestaArbol.inc.php" );
		$IDSocio = $_POST["IDSocio"];
		$IDUsuario = $_POST["IDUsuario"];
		$IDMovilidad = $_POST["IDMovilidad"];
		$NumeroDocumento = $_POST["NumeroDocumento"];
		$Nombre = $_POST["Nombre"];
		$Respuestas = $_POST["Respuestas"];
		$respuesta = SIMWebServiceEncuestaArbol::set_respuesta_movilidad($IDClub,$IDSocio,$IDMovilidad,$Respuestas,$IDUsuario,$NumeroDocumento,$Nombre);
		//inserta _log
		$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestamovilidad','".json_encode($_POST)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getlabels":
		$respuesta = SIMWebServiceQuemado::get_labels($IDClub);
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;


	//Recuperar clave de socio y enviarla al email
	case "getrecuperarclave":
		$email = SIMNet::req( "email" );
		$accion = SIMNet::req( "accion" );
		//verificar si viene Email
		if( !empty( $email ) )
		{
			//Condado con accion
			if($IDClub==51){
				if($accion==""){
					$message = "Codigo de socio o membresia es requerido";
					die( json_encode( array( 'success' => true, 'message'=>$message, 'response' => $response, 'date' => $nowserver ) ) );
					exit;
				}
				else{
					$condicion_accion=" and Accion = '".SIMNet::req( "accion" )."' ";
				}
			}


			if(SIMNet::req( "VerificaAccion" )=="1")://En rancho se pide la accion para poder cambiarla cuando es por web
				$condicion_accion=" and Accion = '".SIMNet::req( "Accion" )."' ";
			endif;

			if($IDClub==18)://En fontanar se envia a otra persona ya que mi club no maneja las claves de los usuarios
				$message = "Su solicitud fue enviada correctamente, pronto nos pondremos en contacto";
				//Enviamos el correo al usario de conectar
						$dest = trim( "coordinaciongeneral@invermetros.com" );

						$head  = "From: " . "info@miclubapp.com" . "\r\n";
						$head .= "To: " . $dest . " \r\n";

						// Ahora creamos el cuerpo del mensaje
						$msg  = "Mensaje desde la Aplicación de Hacienda Fontanar \n\n";
						$msg .= "Cordial Saludo, \n\n El usuario con el siguiente correo ha solicitado sea recordada su clave: \n Email: ".$email."\n \n Notificaciones automaticas Mi Club.";

						// Finalmente enviamos el mensaje
						mail( $dest, "Recordar Clave Fontanar", $msg, $head );
						die( json_encode( array( 'success' => false, 'message'=>$message, 'response' => $response, 'date' => $nowserver ) ) );
						exit;
			elseif($IDClub==25):// Gun club la solicitude llega al club y ellos gestionan el cambio de clave
						$message = "Su solicitud fue enviada correctamente, pronto nos pondremos en contacto para reestablecer su clave";
						//Enviamos el correo al usario de conectar
						$dest = trim( "comunicaciones@gunclub.com.co,c.comunicaciones@gunclub.com.co" );

						$head  = "From: " . "info@miclubapp.com" . "\r\n";
						$head .= "To: " . $dest . " \r\n";

						// Ahora creamos el cuerpo del mensaje
						$msg  = "Mensaje desde app Gun Club \n\n";
						$msg .= "Cordial Saludo, \n\n El usuario con el siguiente correo ha solicitado sea recordada su clave: \n Email: ".$email."\n \n Notificaciones automaticas Mi Club.";

						// Finalmente enviamos el mensaje
						mail( $dest, "Recordar Clave Gun Club", $msg, $head );
						die( json_encode( array( 'success' => false, 'message'=>$message, 'response' => $response, 'date' => $nowserver ) ) );
						exit;
			elseif($IDClub==38):// Club Colombia la clave se maneja en la base de ellos por lo tanto se envia por WS

				$sql_verifica = "SELECT * FROM Socio WHERE CorreoElectronico = '".$email ."' and IDClub = '".$IDClub."'".$condicion_accion;
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

					$head  = "From: " . "info@miclubapp.com" . "\r\n";
					$head .= "To: " . $dest . " \r\n";

					// Ahora creamos el cuerpo del mensaje
					$msg  = "Mensaje desde la App de Clubes \n\n";
					$msg .= "Cordial Saludo, \n\n Le recordamos que los datos de acceso al sistema de clubes es: \n Usuario: ".$datos_socio["Email"]."\n Clave: ".$nueva_clave."\n\n Notificaciones automaticas Clubes.";

					// Finalmente enviamos el mensaje
					mail( $dest, "Recordar Clave Club Colombia", $msg, $head );
					//mail( "sistemas@clubcolombia.org", "Recordar Clave Club Colombia", $msg, $head );


					$token_socio = SIMWebServiceQuemado::obtener_token_colombia("1107051301","24281107");
					if(empty($token_socio)):
						$message = "No fue posible conectarse al servidor, por favor intente mas tarde";
						die( json_encode( array( 'success' => true, 'message'=>$message, 'response' => $response, 'date' => $nowserver ) ) );
						exit;
					else:
						  $resultado=SIMWebServiceQuemado::set_recordar_clave_colombia($token_socio,$email,$nueva_clave);
					 endif;


					//actualizo clave
					$sql_update_clave = $dbo->query("Update Socio Set Clave =  sha1('".$nueva_clave."') Where CorreoElectronico = '".$email."' and IDClub = '".$IDClub."'");
					$message = "Clave enviada correctamente";
					die( json_encode( array( 'success' => true, 'message'=>$message, 'response' => $response, 'date' => $nowserver ) ) );
					exit;
				}
				elseif($IDClub==44):
					$message = "Para cambiar usuario y contraseña por favor diríjase a https://www.countryclubdebogota.com/members";

			else:
					$sql_verifica = "SELECT * FROM Socio WHERE CorreoElectronico = '".$email ."' and IDClub = '".$IDClub."'".$condicion_accion;
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

						$head  = "From: " . "info@miclubapp.com" . "\r\n";
						$head .= "To: " . $dest . " \r\n";

						// Ahora creamos el cuerpo del mensaje
						$msg  = "Mensaje desde la Aplicación de Clubes \n\n";
						$msg .= "Cordial Saludo, \n\n Le recordamos que los datos de acceso al sistema de clubes es: \n Usuario: ".$datos_socio["Email"]."\n Clave: ".$nueva_clave."\n\n Notificaciones automaticas Clubes.";

						// Finalmente enviamos el mensaje
						mail( $dest, "Contacto desde la Aplicación de Clubes", $msg, $head );

						//actualizo clave
						$sql_update_clave = $dbo->query("Update Socio Set Clave =  sha1('".$nueva_clave."') Where CorreoElectronico = '".$email."' and IDClub = '".$IDClub."'");
						$message = "Clave enviada correctamente.";
						die( json_encode( array( 'success' => true, 'message'=>$message, 'response' => $response, 'date' => $nowserver ) ) );
						exit;
					}
			endif;

			//inserta _log
			//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getrecuperarclave','".$sql_verifica."','".json_encode($respuesta)."')");




		}//end if
		else{
			$message = "Error faltan parametros";
			die( json_encode( array(  'success' => false, 'message'=>$message, 'response' => $response, 'update' => $update, 'date' => $nowserver ) ) );
		}

		die( json_encode( array(  'success' => false, 'message'=>$message, 'response' => $response, 'update' => $update, 'date' => $nowserver ) ) );
	break;

	//Recuperar clave de socio y enviarla al email
	case "getrecuperarclaveempleado":
		$email = SIMNet::req( "email" );
		//verificar si viene Email
		if( !empty( $email ) )
		{
			$sql_verifica = "SELECT * FROM Usuario WHERE Email = '".$email ."' and IDClub = '".$IDClub."'";
			$qry_verifica = $dbo->query( $sql_verifica );
			if( $dbo->rows( $qry_verifica ) == 0 )
			{
				$message = "No encontrado";
			}//end if
			else{
				$datos_Usuario = $dbo->fetchArray( $qry_verifica );

				$nueva_clave =substr($datos_Usuario[Nombre],0,3).rand(1,20000).substr($datos_Usuario[Apellido],2,2);
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
				$sql_update_clave = $dbo->query("Update Usuario Set Password =  sha1('".$nueva_clave."') Where Email = '".$email."' and IDClub = '".$IDClub."'");

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
		die( json_encode( array(  'success' => false, 'message'=>'no action.'.$action, 'response' => '', 'date' => $nowserver ) ) );
	break;

}///end sw
?>
