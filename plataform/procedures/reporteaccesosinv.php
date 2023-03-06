<?php

//Si es coordinador tenemos que mirar que servicio se trae primero
//si es un usuario normal se traen las horas
$fecha = date("Y-m-d");
if (!empty($_POST["fecha"]))
	$fecha = $_POST["fecha"];



SIMReg::setFromStructure(array(
	"title" => "ReporteInvitados",
	"table" => "SocioInvitadoEspecial",
	"key" => "IDSocioInvitadoEspecial",
	"mod" => "SocioInvitadoEspecial"
));


$script = "reporteaccesosinv";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
//SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);



require_once LIBDIR . "SIMWebServiceAccesos.inc.php";


switch (SIMNet::req("action")) {

	case "add":
		$view = "views/" . $script . "/form.php";
		$newmode = "insert";
		$titulo_accion = "Crear";
		break;

	case "insert":
		/*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/

		if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG($_POST);

			for ($cont_invitado = 1; $cont_invitado <= ($frm["NumeroInvitados"] - 1); $cont_invitado++) :
				$campo_nombre = "Nombre" . $cont_invitado;
				$campo_apellido = "Apellido" . $cont_invitado;
				$campo_documento = "NumeroDocumento" . $cont_invitado;
				$campo_email = "Email" . $cont_invitado;
				$campo_telefono = "Telefono" . $cont_invitado;
				$campo_tipoaut = "TipoAutorizacion" . $cont_invitado;
				$campo_tipodoc = "IDTipoDocumento" . $cont_invitado;
				$campo_placa = "Placa" . $cont_invitado;
				if (!empty($frm[$campo_nombre]) && !empty($frm[$campo_documento]) && !empty($frm[$campo_tipoaut])) :
					$respuesta = SIMWebServiceAccesos::set_invitado($frm["IDClub"], $frm["IDSocio"], $frm[$campo_documento], $frm[$campo_nombre], $frm["FechaIngreso"]);
					$respuesta = SIMWebServiceAccesos::set_autorizacion_contratista($frm["IDClub"], $frm["IDSocio"], $frm[$campo_tipoaut], $frm["FechaInicio"], $frm["FechaFin"], $frm[$campo_tipodoc], $frm[$campo_documento], $frm[$campo_nombre], $frm[$campo_apellido], $frm[$campo_email], $frm[$campo_placa]);
					if ($respuesta["message"] == "guardado") {
						$insertado++;
					} //end if
					else {
						$respuesta_error =  $respuesta["message"];
						$no_insertado++;
					} //end else
				endif;
			endfor;
			//Servicio de invitados
			//$respuesta = SIMWebService::set_invitado($frm["IDClub"],$frm["IDSocio"],$frm["NumeroDocumento"],$frm["Nombre"],$frm["FechaIngreso"]);

			if ($insertado >= 1) {
				//bien
				SIMNotify::capture("La autorizacion se ha creado correctamente", "info alert-success");
			} //end if
			else {
				//paila
				SIMNotify::capture($respuesta_error, "error alert-danger");
			} //end else

		} else
			exit;


		break;


	case "edit":

		$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
		$view = "views/" . $script . "/form.php";
		$newmode = "update";
		$titulo_accion = "Actualizar";

		break;

	case "registraingreso":
		$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
		$view = "views/" . $script . "/form.php";
		$newmode = "updateingreso";
		$titulo_accion = "Registrar Ingreso";

		break;

	case "editinfo":
		$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
		$view = "views/" . $script . "/form.php";
		$newmode = "updateinfo";
		$titulo_accion = "Actualizar Datos";

		break;

	case "editobservacion":
		$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
		$view = "views/" . $script . "/form.php";
		$newmode = "updateobservacion";
		$titulo_accion = "Actualizar Datos";

		break;


	case "update":

		if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG($_POST);

			print_r($frm);
			exit;




			$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"), "");

			$frm = $dbo->fetchById($table, $key, $id, "array");

			SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
		} else
			exit;
		break;


	case "updateingreso":

		if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG($_POST);

			$sql_ingreso = "Update " . $table . " Set Estado = 'I', FechaIngresoClub = NOW(), Observaciones = '" . $frm["Observaciones"] . "' Where " . $key . " = " . $id;
			$qry_ingreso = $dbo->query($sql_ingreso);

			SIMNotify::capture("Se realizo el ingreso satisfactoriamente", "info");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Serealizoelingresosatisfactoriamente', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=add");
		} else
			exit;

	case "updateinfo":

		if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG($_POST);


			$sql_edit = "Update " . $table . " Set NumeroDocumento = '" . $frm["NumeroDocumento1"] . "', Nombre = '" . $frm["Nombre1"] . "', Observaciones = '" . $frm["Observaciones"] . "' Where " . $key . " = " . $id;
			$qry_edit = $dbo->query($sql_edit);

			SIMNotify::capture("Se realizo la edicion satisfactoriamente", "info");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Serealizolaedicionsatisfactoriamente', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=add");
		} else
			exit;

	case "updateobservacion":

		if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG($_POST);

			$sql_edit = "Update " . $table . " Set Observaciones = '" . $frm["Observaciones"] . "' Where " . $key . " = " . $id;
			$qry_edit = $dbo->query($sql_edit);

			SIMNotify::capture("Se ingreso la observacion satisfactoriamente", "info");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Seingresolaobservacionsatisfactoriamente', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=add");
		} else
			exit;


	case "search":
		$view = "views/" . $script . "/list.php";
		break;

	case "DelImgNot":
		$campo = $_GET['cam'];
		if ($campo == "SWF") {
			$doceliminar = SWFEvento_DIR . $dbo->getFields("Evento", "$campo", "IDEvento = '" . $_GET[id] . "'");
			unlink($doceliminar);
			$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'SWFeliminadoCorrectamente', LANGSESSION));
		} else {
			$doceliminar = IMGEVENTO_DIR . $dbo->getFields("Evento", "$campo", "IDEvento = '" . $_GET[id] . "'");
			unlink($doceliminar);
			$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
		}
		SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
		exit;
		break;


	default:
		$view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
	$view = "views/" . $script . "/form.php";
