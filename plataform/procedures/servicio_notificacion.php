<?php

$ids = SIMNet::req("ids");

if (empty($_POST["FechaReserva"])) :
	$fecha = date("Y-m-d");
else :
	$fecha = $_POST["FechaReserva"];
endif;




//Si es coordinador tenemos que mirar que servicio se trae primero
//si es un usuario normal se traen las horas

$action = SIMNet::req("action");
switch ($action) {


	case 'insert':
		$frm = SIMUtil::varsLOG($_POST);
		$condicion = "";

		$condicion .= "AND Tipo <> 'Automatica' ";

		if (!empty($frm['IDServicioElemento']))
			$condicion .= "AND IDServicioElemento = '$frm[IDServicioElemento]'";

		//traer los socios de la reserva del servicio y la hora
		$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM ReservaGeneral, Socio WHERE ReservaGeneral.Fecha = '$fecha' AND ReservaGeneral.IDServicio = '$frm[ids]' AND ReservaGeneral.IDClub = '" . SIMUser::get("club") . "' AND ReservaGeneral.IDSocio = Socio.IDSocio AND Socio.IDClub = '" . SIMUser::get("club") . "' " . $condicion;
		/* 	echo $sql_socios;
			exit; */
		$qry_socios = $dbo->query($sql_socios);

		$notificaciones = $dbo->rows($qry_socios);

		$frm["IDClub"] = SIMUser::get("club");
		$frm["Fecha"] = date("Y-m-d H:i:s");
		$id = $dbo->insert($frm, "ServicioNotificacion", "IDServicioNotificacion");

		while ($r_socios = $dbo->fetchArray($qry_socios)) {

			$users = array(
				array(
					"id" => $r_socios["IDSocio"],
					"idclub" => $r_socios["IDClub"],
					"registration_key" => $r_socios["Token"],
					"deviceType" => $r_socios["Dispositivo"]
				)
			);


			$custom["tipo"] = "app";
			$custom["idmodulo"] = (string)"44";
			$custom["titulo"] = "Notificacion Club";
			$message = $frm["Mensaje"];
			$frm["IDModulo"] = 44;

			//SIMUtil::sendAlerts($users, $message, $custom);

			SIMUtil::envia_cola_notificacion($r_socios, $frm);


			$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle)
              Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $custom["tipo"] . "', '" . $custom["titulo"] . "', '" . $message . "', '" . $custom["idmodulo"] . "','" . $id . "')");
		} //end while	


		SIMNotify::capture("Se han enviado " . $notificaciones . " correctamente", "info alert-success");

		break;
} //end switch



if (empty($view))
	$view = "views/servicio_notificacion/form.php";
