<?php

//Si es coordinador tenemos que mirar que servicio se trae primero
//si es un usuario normal se traen las horas
$fecha = date("Y-m-d");
if (!empty($_POST["fecha"]))
	$fecha = $_POST["fecha"];



SIMReg::setFromStructure(array(
	"title" => "Invitado",
	"table" => "SocioInvitado",
	"key" => "IDSocioInvitado",
	"mod" => "SocioInvitado"
));


$script = "invitados";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
//SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);


require(LIBDIR . "SIMWebServiceAccesos.inc.php");

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
			$frm["Estado"] = "P";
			for ($cont_invitado = 1; $cont_invitado <= ($frm["NumeroInvitados"] - 1); $cont_invitado++) :
				$campo_nombre = "Nombre" . $cont_invitado;
				$campo_documento = "NumeroDocumento" . $cont_invitado;
				if (!empty($frm[$campo_nombre]) && !empty($frm[$campo_documento])) :

					// Guardar campos dinamicos si el club lo solicito

					// Se convierte las respuestas del formulario a json 
					$arr_campo_formulario = array();
					$sql_campo_form = "SELECT * FROM CampoFormularioInvitado WHERE IDClub= '" . SIMUser::get('club') . "' and Publicar = 'S' order by Orden ";
					$qry_campo_form = $dbo->query($sql_campo_form);
					while ($r_campo = $dbo->fetchArray($qry_campo_form)) :
						$respuesta['IDCampoFormularioInvitado']  = $r_campo['IDCampoFormularioInvitado'];
						$respuesta['Valor']  = $frm[$r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado];
						array_push($arr_campo_formulario, $respuesta);
					endwhile;
					$ValoresFormulario = json_encode($arr_campo_formulario);

					$IDUsuario = SIMUser::get("IDUsuario");
					$respuesta = SIMWebServiceAccesos::set_invitado($frm["IDClub"], $frm["IDSocio"], $frm[$campo_documento], $frm[$campo_nombre], $frm["FechaIngreso"], $ValoresFormulario, $IDUsuario);


					//Envio a zeus
					if ($frm["NotificarZeus"]) :
						$array_datos_token = SIMWebServiceZeus::obtener_token();
						if (!empty($array_datos_token["SessionIDTokenResult"]["SessionID"]) && $array_datos_token["SessionIDTokenResult"]["Status"] == "SUCCESS") :
							$result_envia_invitacion = SIMWebServiceZeus::envia_invitacion($array_datos_token["SessionIDTokenResult"]["SessionID"], $frm["IDSocio"], $frm[$campo_documento], $frm[$campo_nombre], $frm["FechaIngreso"]);
						endif;
					endif;

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
				SIMNotify::capture("La invitacion se ha creado correctamente", "info alert-success");
			} //end if
			else {
				//paila
				SIMNotify::capture($respuesta_error, "error alert-danger");
			} //end else

			//$array_datos_regla = SIMUtil::consulta_regla_invitacion($frm["IDSocio"],$frm["IDClub"]);

			//insertamos los datos del asistente
			//$id = $dbo->insert( $frm , $table , $key );

			//Verificamos si es la tercera vez en el mes que se hace la invitación
			/*
			$mes_actual = date("m");
			$sql_numero_invitacion = $dbo->query("Select * From SocioInvitado Where IDClub = '".$frm["IDClub"]."' and NumeroDocumento = '".$frm["NumeroDocumento"]."' and MONTH(FechaIngreso) = '".$mes_actual."'");
			$numero_invitaciones = $dbo->rows($sql_numero_invitacion);

			if ((int)$numero_invitaciones>$array_datos_regla["MaximoRepeticionInvitado"]):
				$mensaje_limite_invitacion = "Atencion, esta persona se ha invitado mas del maximo permitido por la regla.";
			endif;
			*/

			//SIMHTML::jsAlert($mensaje_limite_invitacion." Registro Guardado Correctamente.");
			//SIMHTML::jsRedirect( $script.".php?action=add" );
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

			$sql_edit = "Update " . $table . " Set NumeroDocumento = '" . $frm["NumeroDocumento1"] . "', Nombre = '" . $frm["Nombre1"] . "', Observaciones = '" . $frm["Observaciones"] . "', Placa = '{$frm['Placa1']}' Where " . $key . " = " . $id;
			$qry_edit = $dbo->query($sql_edit);


			// Guardar campos dinamicos si el club lo solicito

			// Se convierte las respuestas del formulario a json 
			$cont_invitado = 1;
			$arr_campo_formulario = array();
			$sql_campo_form = "SELECT * FROM CampoFormularioInvitado WHERE IDClub= '" . SIMUser::get('club') . "' and Publicar = 'S' order by Orden ";
			$qry_campo_form = $dbo->query($sql_campo_form);
			while ($r_campo = $dbo->fetchArray($qry_campo_form)) :
				$respuesta['IDCampoFormularioInvitado']  = $r_campo['IDCampoFormularioInvitado'];
				$respuesta['Valor']  = $frm[$r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado];
				if (is_array($respuesta['Valor'])) {
					$respuesta['Valor'] = implode(',', $respuesta['Valor']);
				}
				array_push($arr_campo_formulario, $respuesta);
			endwhile;

			// Se eliminan las respuestas anteriores para registrar las nuevas modificaciones.
			$delete_datos_form = $dbo->query("DELETE FROM InvitadosOtrosDatos WHERE IDInvitacion = $id");
			if ($delete_datos_form) {
				foreach ($arr_campo_formulario as $detalle_datos) :
					$sql_datos_form = $dbo->query("Insert Into InvitadosOtrosDatos (IDInvitacion, IDCampoFormularioInvitado, Valor) Values ('" . $id . "','" . $detalle_datos["IDCampoFormularioInvitado"] . "','" . $detalle_datos["Valor"] . "')");
				// $OtrosDatosFormulario .= " " . $detalle_datos["Valor"];
				// if (filter_var(trim($detalle_datos["Valor"]), FILTER_VALIDATE_EMAIL)) {
				// 	$parametros_codigo_qr = $NumeroDocumento;
				// 	SIMUtil::enviar_codigo_qr_invitado($id_solicitud, $parametros_codigo_qr, "Invitado", trim($detalle_datos["Valor"]));
				// }

				endforeach;
			}

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
