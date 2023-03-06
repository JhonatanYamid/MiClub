 <?

	SIMReg::setFromStructure(array(
		"title" => "Reconocimiento",
		"table" => "Reconocimiento",
		"key" => "IDReconocimiento",
		"mod" => "Reconocimiento"
	));


	$script = "reconocimiento";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");


	//Verificar permisos
	SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




	switch (SIMNet::req("action")) {

		case "add":
			$view = "views/" . $script . "/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;

		case "insert":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);
				$frm["IDSocioVotado"] = $frm["IDSocio"];
				$frm["IDUsuario"] = SIMUser::get("IDUsuario");

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				//guardar las opciones
				$sql_opcion = "SELECT IDOpcionReconocimiento FROM OpcionReconocimiento WHERE IDCategoriaReconocimiento = '" . $frm["IDCategoriaReconocimiento"] . "'";
				$r_opcion = $dbo->query($sql_opcion);
				while ($row_opcion = $dbo->fetchArray($r_opcion)) {
					if ($frm["Opcion" . $row_opcion["IDOpcionReconocimiento"]]) {
						$sql_insert = "INSERT INTO ReconocimientoOpcion (IDOpcionReconocimiento,IDReconocimiento) VALUES ('" . $row_opcion["IDOpcionReconocimiento"] . "','" . $id . "')";
						$dbo->query($sql_insert);
					}
				}


				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {
					$mensaje = "Se ha generado un reconocimiento."  . " " . $frm["Comentario"];
					$IDModulo = 108;
					//enviar notificacion al usuario de que se le asigno una nueva reconocimiento
					SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $frm["IDSocio"], $mensaje, $IDModulo, "");

					//enviar notificacion al jefe la persona que se le asigno un reconocimiento
					$NombreSocio = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $frm["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $frm["IDSocio"] . "'");
					$DocumentoJefe = $dbo->getFields("Socio", "DocumentoJefe", "IDSocio = '" . $frm["IDSocio"] . "'");
					if ($DocumentoJefe > 0) {
						$mensajeJefe = "Se ha generado un reconocimiento a la siguiente persona:" . $NombreSocio;
						$IDSocioJefe = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '" . $DocumentoJefe . "'");
						SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $IDSocioJefe, $mensajeJefe, $IDModulo, "");
					}
				}


				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php");
			} else
				exit;

			break;


		case "edit":
			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";

			break;

		case "update":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$frm["IDSocioVotado"] = $frm["IDSocio"];
				$frm["IDUsuario"] = SIMUser::get("IDUsuario");

				//Borro las anteriores reconocimientos
				$sql_borra = "DELETE FROM ReconocimientoOpcion WHERE IDReconocimiento = '" . SIMNet::reqInt("id") . "'";
				$dbo->query($sql_borra);
				//guardar las opciones
				$sql_opcion = "SELECT IDOpcionReconocimiento FROM OpcionReconocimiento WHERE IDCategoriaReconocimiento = '" . $frm["IDCategoriaReconocimiento"] . "'";
				$r_opcion = $dbo->query($sql_opcion);
				while ($row_opcion = $dbo->fetchArray($r_opcion)) {
					if ($frm["Opcion" . $row_opcion["IDOpcionReconocimiento"]]) {
						$sql_insert = "INSERT INTO ReconocimientoOpcion (IDOpcionReconocimiento,IDReconocimiento) VALUES ('" . $row_opcion["IDOpcionReconocimiento"] . "','" . SIMNet::reqInt("id") . "')";
						$dbo->query($sql_insert);
					}
				}

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {
					$mensaje = "Se ha generado un reconocimiento."  . " " . $frm["Comentario"];
					$IDModulo = 108;
					//enviar notificacion al usuario de que se le asigno una nueva reconocimiento
					SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $frm["IDSocio"], $mensaje, $IDModulo, "");

					//enviar notificacion al jefe la persona que se le asigno un reconocimiento
					$NombreSocio = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $frm["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $frm["IDSocio"] . "'");
					$DocumentoJefe = $dbo->getFields("Socio", "DocumentoJefe", "IDSocio = '" . $frm["IDSocio"] . "'");
					if ($DocumentoJefe > 0) {
						$mensajeJefe = "Se ha generado un reconocimiento a la siguiente persona:" . $NombreSocio;
						$IDSocioJefe = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '" . $DocumentoJefe . "'");
						SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $IDSocioJefe, $mensajeJefe, $IDModulo, "");
					}
				}

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			} else
				exit;

			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = SERVICIO_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));

			SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			break;


		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = IMGPRODUCTO_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));

			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
