 <?

	SIMReg::setFromStructure(array(
		"title" => "EventoVotacion",
		"table" => "VotacionEvento",
		"key" => "IDVotacionEvento",
		"mod" => "VotacionEvento"
	));


	$script = "votacionesevento";

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

				//UPLOAD de imagenes
				if (isset($_FILES)) {

					$files =  SIMFile::upload($_FILES["Icono"], PQR_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Icono"] = $files[0]["innername"];
				} //end if

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

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

				//UPLOAD de imagenes
				if (isset($_FILES)) {


					$files =  SIMFile::upload($_FILES["Icono"], PQR_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Icono"] = $files[0]["innername"];
				} //end if

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
			$filedelete = PQR_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;

			//Votantes
		case "InsertarVotante":
			$frm = SIMUtil::varsLOG($_POST);

			$IDVotacionEvento = $frm["ID"];
			$IDSocio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '" . $frm["Cedula"] . "' and IDClub = '" . $frm["IDClub"] . "'");
			if ((int)$IDSocio <= 0) {
				//echo "Se debe crear";
				$resp = SIMWebServiceApp::set_socio(
					$frm["IDClub"],
					$frm["Cedula"],
					$frm["Cedula"],
					$frm["Parentesco"],
					$frm["Genero"],
					$frm["Nombre"],
					$frm["Apellido"],
					$frm["FechaNacimiento"],
					$frm["Cedula"],
					$frm["CorreoElectronico"],
					$frm["Telefono"],
					$frm["Celular"],
					$frm["Direccion"],
					$frm["TipoSocio"],
					"A",
					"100",
					$frm["Cedula"],
					$frm["NumeroCasa"],
					$frm["Categoria"],
					"S"
				);
				$IDSocio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '" . $frm["Cedula"] . "' and IDClub = '" . $frm["IDClub"] . "'");
			}

			$UsuarioCrea = SIMUser::get("IDUsuario");
			SIMUtil::ingreso_votante($frm["IDClub"], $IDVotacionEvento, $IDSocio, $frm["Nombre"], $frm["NumeroCasa"], $frm["Cedula"], $frm["Coeficiente"], $frm["Consejero"], $frm["Moroso"], $UsuarioCrea);

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=votantes&id=" . $frm["ID"]);
			exit;
			break;

		case "BuscarVotante":
			$frm = SIMUtil::varsLOG($_POST);
			SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=registrovotantes&id=" . $frm["ID"] . "&Parametro=" . base64_encode($frm["NumeroDocumento"]));
			exit;
			break;

		case "EliminaVotante":
			//$id = $dbo->query( "DELETE FROM VotacionVotante WHERE IDVotacionVotante   = '".$_GET["IDVotacionVotante"]."' LIMIT 1" );

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Porseguridadnoesposibleeliminarvotantes!', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=votantes&id=" . $_GET["id"]);
			exit;
			break;

		case "EliminaPoder":
			$datos_poder = $dbo->fetchAll("VotacionPoder", " IDVotacionPoder = '" . $_GET["IDVotacionPoder"] . "' ", "array");
			$datos_eliminado = json_encode($datos_poder);
			$sql_delete = "DELETE FROM VotacionPoder WHERE IDVotacionPoder   = '" . $_GET["IDVotacionPoder"] . "' LIMIT 1 ";
			$nom_usu = SIMUser::get("IDUsuario") . " " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . SIMUser::get("IDUsuario") . "' ");
			SIMLog::insert($nom_usu, "VotacionVotante", "VotacionVotante", "delete",  $sql_delete . $datos_eliminado);

			$id = $dbo->query($sql_delete);

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Podereliminadoconexito', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=registrovotantes&id=" . $_GET["IDVotacionEvento"]);
			exit;
			break;

			//Fin Votantes



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
