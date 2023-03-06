<?php

$ids = SIMNet::req("ids");
//Si es coordinador tenemos que mirar que servicio se trae primero
//si es un usuario normal se traen las horas

$fecha = SIMNet::req("fecha");
if (empty($fecha))
	$fecha = date("Y-m-d");

$action = SIMNet::req("action");


switch ($action) {


	case 'insert':
		$frm = SIMUtil::varsLOG($_POST);

		//$invitados = explode("\r",$frm["SocioInvitado"]);
		$invitados = explode("|||", $frm["InvitadoSeleccion"]);
		if (count($invitados) > 0) :
			foreach ($invitados as $nom_invitado) :
				$array_datos = explode("-", $nom_invitado);
				if ($array_datos[0] == "socio") : // socio club
					$datos_invitado[]["IDSocio"] = trim($array_datos[1]);
				elseif ($array_datos[0] == "externo") : // invitado externo
					$datos_invitado[]["Nombre"] = trim($array_datos[1]);
				endif;
			endforeach;
			$array_invitados = json_encode($datos_invitado);
		else :
			$array_invitados = "";
		endif;

		$datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $frm["IDTipoReserva"] . "' ", "array");
		$NumeroTurnos = $datos_tipo_reserva["NumeroTurnos"];


		//$respuesta = SIMWebService::set_reserva_generalV2(SIMUser::get("club"),$frm["IDSocio"],$frm["idelemento"],$frm["ids"],$frm["fecha"],$frm["hora"],"",$array_invitados,$frm["Observaciones"],"Admin",$frm["tee"],"","","","",$frm["IDTipoModalidadEsqui"],$frm["IDAuxiliar"],$frm["IDServicioTipoReserva"],$NumeroTurnos,"","");
		$respuesta = SIMWebService::set_reserva_generalV2(SIMUser::get("club"), $frm["IDSocio"], $frm["idelemento"], $frm["ids"], $frm["fecha"], $frm["hora"], "", $array_invitados, $frm["Observaciones"], "Admin", $frm["tee"], "", "", "", "", $frm["IDTipoModalidadEsqui"], $frm["IDAuxiliar"], $frm["IDTipoReserva"], "", "", "", "", SIMUser::get("IDUsuario"), $frm["CantidadInvitadoSalon"], "");
		//set_reserva_generalV2($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,$Observaciones="",$Admin = "", $Tee="",$IDDisponibilidad="", $Repetir="",$Periodo="",$RepetirFechaFinal="",$IDTipoModalidadEsqui="",$IDAuxiliar="",$IDTipoReserva="",$NumeroTurnos="",$IDReservaGrupos,$IDBeneficiario="",$TipoBeneficiario="")



		if ($respuesta["success"] == "1") {
			//bien
			//SIMNotify::capture( "La reserva se ha creado correctamente" , "info alert-success" );
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Lareservasehacreadocorrectamente', LANGSESSION));
			SIMHtml::jsRedirect("reservas_admin.php?ids=" . $frm["ids"] . "&action=new&fecha=" . $fecha);
			$resultadook = 1;
		} //end if
		else {
			//paila
			//SIMNotify::capture( $respuesta["message"]  , "error alert-danger" );
			SIMHtml::jsAlert(SIMUtil::get_traduccion('', '', 'ATENCIONLARESERVANOPUDOSERTOMADA', LANGSESSION) . ":" . $respuesta["message"]);
			SIMHtml::jsRedirect("reservas.php?action=new&ids=" . $frm["ids"]);
			$resultadook = 1;
		} //end else


		exit;
		break;

	case 'updateinvitado':

		$frm = SIMUtil::varsLOG($_POST);
		//$invitados = explode("\r",$frm["SocioInvitado"]);
		//$invitados=$frm["SocioInvitado"];
		//print_r($frm);

		if ($frm["IDSocioOrig"] != $frm["IDSocio"]) :
			//Actualizo el socio dueño de la reserva
			$ObservacionCambio = "El Usuario " . SIMUser::get("Nombre") . " cambio el dueno de la reserva que era originamlemte " . $frm["IDSocioOrig"];
			$update_reserva = "Update ReservaGeneral Set IDSocio = '" . $frm["IDSocio"] . "', Observaciones= '" . $ObservacionCambio . "',UsuarioTrEd='" . SIMUser::get("Nombre") . " " . $ObservacionCambio . "', FechaTrEd = NOW() Where IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "'";
			$dbo->query($update_reserva);
		endif;

		if ($frm["IDSocioBeneficiarioOrig"] != $frm["IDSocioBeneficiario"]) :
			//Actualizo el socio dueño de la reserva
			$ObservacionCambio = "El Usuario " . SIMUser::get("Nombre") . " cambio el dueno de la reserva que era originamlemte " . $frm["IDSocioOrig"];
			$update_reserva = "Update ReservaGeneral Set IDSocioBeneficiario = '" . $frm["IDSocioBeneficiario"] . "', Observaciones= '" . $ObservacionCambio . "',UsuarioTrEd='" . SIMUser::get("Nombre") . " " . $ObservacionCambio . "', FechaTrEd = NOW() Where IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "'";
			$dbo->query($update_reserva);
		endif;

		//Actualizo cancha y equipo si aplica
		$update_reserva = "Update ReservaGeneral Set Cancha = '" . $frm["Cancha"] . "', Equipo= '" . $frm["Equipo"] . "',UsuarioTrEd='" . SIMUser::get("Nombre") . " " . "', FechaTrEd = NOW() Where IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "'";
		$dbo->query($update_reserva);

		$invitados = explode("|||", $frm["InvitadoSeleccion"]);
		if (count($invitados) > 0) :
			// Borro invitados
			$sql_invidado_reserva_del = "Delete From ReservaGeneralInvitado Where IDReservaGeneral = '" . $frm[IDReservaGeneral] . "'";
			$dbo->query($sql_invidado_reserva_del);
			foreach ($invitados as $nom_invitado) :
				$array_datos = explode("-", $nom_invitado);
				if ($array_datos[0] == "socio") : // socio club
					$inserta_socio =  "Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre)
															Values ('" . $frm[IDReservaGeneral] . "','" . $array_datos[1] . "', '')";
					$dbo->query($inserta_socio);

				elseif ($array_datos[0] == "externo") : // invitado externo
					$inserta_externo = "Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre)
								  Values ('" . $frm[IDReservaGeneral] . "','', '" . $array_datos[1] . "')";
					$dbo->query($inserta_externo);
				endif;
			endforeach;
			$respuesta["success"] = "1";
		endif;
		if ($respuesta["success"] == "1") {
			//bien
			SIMNotify::capture("Invitados modificados correctamente", "info alert-success");
		} //end if
		else {
			//paila
			SIMNotify::capture("Se producjo un error al guardar", "error alert-danger");
		} //end else
		break;


	case 'updatereservatomada':
		$frm = SIMUtil::varsLOG($_POST);

		$sql_invitados_reserva = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "'";
		$qry_invitados_reserva = $dbo->query($sql_invitados_reserva);
		$total_invitados = $dbo->rows($qry_invitados_reserva);
		$invitado_asiste = 0;
		if ($total_invitados > 0) :
			while ($row_invitados_reserva = $dbo->fetchArray($qry_invitados_reserva)) :
				$nombre_campo = "InvitadoCumplio" . $row_invitados_reserva["IDReservaGeneralInvitado"];
				$valor_campo = $frm[$nombre_campo];
				if ($valor_campo == "S")
					$invitado_asiste++;

				$sql_actualiza_invitado = "Update ReservaGeneralInvitado Set Cumplida = '" . $valor_campo . "' Where IDReservaGeneralInvitado = '" . $row_invitados_reserva["IDReservaGeneralInvitado"] . "' and IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "'";
				$dbo->query($sql_actualiza_invitado);
			endwhile;
		endif;

		if ($total_invitados > 0 && $invitado_asiste != $total_invitados || $frm["CumplidaCabeza"] == "N")
			$estado_reserva_cumplida = "P";
		else
			$estado_reserva_cumplida = $frm["Cumplida"];

		//Actualizo Estado de reserva
		$sql_reserva_estado = "Update ReservaGeneral Set Cumplida = '" . $estado_reserva_cumplida . "', CumplidaCabeza = '" . $frm["CumplidaCabeza"] . "', FechaCumplida = NOW(), IDUsuarioCumplida = '" . $frm["IDUsuario"] . "', ObservacionCumplida = '" . $frm["ObservacionCumplida"] . "' Where IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "'";
		$dbo->query($sql_reserva_estado);

		if ($estado_reserva_cumplida == "N") {
			$datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "' ", "array");
			$datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $datos_reserva["IDServicio"] . "' ", "array");
			if ($datos_servicio["NotificarSocioReservaIncumplida"] == "S") {
				SIMUtil::notifica_reserva_incumplida($frm["IDReservaGeneral"]);
			}
		}
		SIMNotify::capture("Reserva actualizada correctamente", "info alert-success");
		break;

	case 'updateconfirmarreserva':
		$frm = SIMUtil::varsLOG($_POST);
		//Actualizo Estado de reserva
		$sql_reserva_estado = "Update ReservaGeneral Set Confirmada = '" . $frm["Confirmada"] . "', FechaConfirmada = NOW(), IDUsuarioConfirmada = '" . $frm["IDUsuario"] . "', ObservacionConfirmada = '" . $frm["ObservacionConfirmada"] . "' Where IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "'";
		$dbo->query($sql_reserva_estado);
		$MensajeConfirma = "Le informamos que su reserva para el dia " . $frm["FechaReservaConfirma"] . " Hora: " . $frm["HoraReservaConfirma"] . " fue confirmada por operaciones: " . $frm["ObservacionConfirmada"];
		SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $frm["IDSocio"], $MensajeConfirma);
		SIMNotify::capture("Reserva confirmada correctamente", "info alert-success");
		break;

	case "new":
		foreach ($elementos[$ids] as $key_elemento => $datos_elemento) {
			$MostrarTodoDia = $dbo->getFields("Servicio", "PermiteReservaCualquierHora", "IDServicio = '" . $ids . "'");


			if ($_GET["ids"] == 1375) : // Gun Club Reservados
				//$horas = SIMWebServiceHotel::get_disponiblidad_elemento_servicioV2( SIMUser::get("club"), $ids, $fecha, "728","S","","","","",$MostrarTodoDia);
				//solo consulto un elemento
				if (!empty($_GET["IDElementoSelecc"]) && (int)$_GET["IDElementoSelecc"] > 0 && $datos_elemento["IDElemento"] == $_GET["IDElementoSelecc"]) :
					$horas = SIMWebService::get_disponiblidad_elemento_servicio(SIMUser::get("club"), $ids, $fecha, $_GET["IDElementoSelecc"], "S", "", "", "", "", $MostrarTodoDia);
				endif;
			else :
				$horas = SIMWebService::get_disponiblidad_elemento_servicio(SIMUser::get("club"), $ids, $fecha, $datos_elemento["IDElemento"], "S", "", "", "", "", $MostrarTodoDia);
			endif;


			unset($array_datos_elemento);

			//Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
			$id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $ids . "'");
			if ($id_servicio_maestro == 15) : //15 = Golf
				foreach ($horas["response"]["0"]["Disponibilidad"] as $key_horas => $todashoras) {
					foreach ($todashoras as $key_todahora => $datos_horas) {
						//print_r($datos_horas);
						if ($datos_horas["IDElemento"] == $datos_elemento["IDElemento"]) :
							//echo "<br>" . $datos_horas["IDElemento"];
							$array_datos_elemento[][] = $datos_horas;
							$array_horas[$datos_horas["IDElemento"]]  = $array_datos_elemento;
						endif;
					}
				} //end for

			else :
				foreach ($horas["response"] as $key_horas => $datos_horas) {
					$array_horas[$datos_elemento["IDElemento"]]  = $datos_horas["Disponibilidad"];
				} //end for

			endif;
		} //end for

		break;
} //end switch



if ($_GET["ids"] == 1375) : // Gun Club Reservados
//print_r($horas);
//exit;
endif;



if (!empty($_GET["idr"])) :
	$detalle_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $_GET["idr"] . "' ", "array");
	$sql_invitado_reserva = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $_GET["idr"] . "'";
	$qry_invitado_reserva = $dbo->query($sql_invitado_reserva);
	while ($row_invitado_reserva = $dbo->fetchArray($qry_invitado_reserva)) :
		$array_invitados[$row_invitado_reserva["IDReservaGeneralInvitado"]] = $row_invitado_reserva;
	endwhile;
endif;



if (empty($view))
	$view = "views/reservas/form.php";
