 <?

	SIMReg::setFromStructure(array(
		"title" => "ModuloClub",
		"table" => "ClubModulo",
		"key" => "IDClubModulo",
		"mod" => "ClubModulo"
	));


	$script = "modulosclub";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);


	switch (SIMNet::req("action")) {

		case "add":

			break;

		case "insert":

			break;

		case "edit":
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";
			break;

		case "update":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);


				//Actualizo Servicios
				$query_servicios = $dbo->query("Select * from ServicioMaestro Where Publicar = 'S' Order by Nombre");
				while ($r = $dbo->object($query_servicios)) {
					$nombre_campo_orden_servicio = "OrdenServicio" . $r->IDServicioMaestro;
					$nombre_campo_titulo_servicio = "TituloServicio" . $r->IDServicioMaestro;

					$sql_actualiza_servicio = "UPDATE ServicioClub Set Orden = '" . $frm[$nombre_campo_orden_servicio] . "', TituloServicio = '" . $frm[$nombre_campo_titulo_servicio] . "' WHERE IDClub = '" . SIMUser::get("club") . "' AND IDServicioMaestro = '" . $r->IDServicioMaestro . "'";
					$dbo->query($sql_actualiza_servicio);
				}

				//Actualizo Modulos
				$query_modulos = $dbo->query("Select * from Modulo Where Publicar = 'S' Order by Nombre");
				while ($r = $dbo->object($query_modulos)) {
					$nombre_campo_id = "IDModulo" . $r->IDModulo;
					$nombre_campo_titulo = "Titulo" . $r->IDModulo;
					$nombre_campo_titulo_lat = "TituloLateral" . $r->IDModulo;
					$nombre_campo_orden = "Orden" . $r->IDModulo;
					$nombre_campo_icono = "Icono" . $r->IDModulo;
					$nombre_campo_icono_actual = "ImagenOriginal" . $r->IDModulo;
					$nombre_campo_icono_lateral = "IconoLateral" . $r->IDModulo;
					$nombre_campo_icono_actual_lateral = "ImagenOriginalLateral" . $r->IDModulo;
					$nombre_campo_ubicacion = "UbicacionModulo" . $r->IDModulo;
					$ubicacion_modulo = "";

					if (count($frm[$nombre_campo_ubicacion] > 0)) :
						$ubicacion_modulo = implode("|", $frm[$nombre_campo_ubicacion]);
					endif;

					if (!empty($_FILES[$nombre_campo_icono]["name"])) :
						$files =  SIMFile::upload($_FILES[$nombre_campo_icono], MODULO_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES[$nombre_campo_icono]["name"]))
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						$frm[$nombre_campo_icono] = $files[0]["innername"];
					else :
						$frm[$nombre_campo_icono] = $frm[$nombre_campo_icono_actual];
					endif;

					if (!empty($_FILES[$nombre_campo_icono_lateral]["name"])) :
						$files =  SIMFile::upload($_FILES[$nombre_campo_icono_lateral], MODULO_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES[$nombre_campo_icono_lateral]["name"]))
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						$frm[$nombre_campo_icono_lateral] = $files[0]["innername"];
					else :
						$frm[$nombre_campo_icono_lateral] = $frm[$nombre_campo_icono_actual_lateral];
					endif;

					$sql_actualiza_modulo = "Update ClubModulo Set Titulo = '" . $frm[$nombre_campo_titulo] . "', TituloLateral = '" . $frm[$nombre_campo_titulo_lat] . "',  Orden = '" . $frm[$nombre_campo_orden] . "', Icono = '" . $frm[$nombre_campo_icono] . "', IconoLateral = '" . $frm[$nombre_campo_icono_lateral] . "', Ubicacion = '" . $ubicacion_modulo . "' Where  IDClub = '" . SIMUser::get("club") . "'	and IDModulo = '" . $r->IDModulo . "'";
					$dbo->query($sql_actualiza_modulo);
				}

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit");
			} else
				exit;
			break;

		default:
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";
	} // End switch



	?>
