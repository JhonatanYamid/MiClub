 <?

	SIMReg::setFromStructure(array(
		"title" => "PqrsFuncionarios",
		"table" => "PqrFuncionario",
		"key" => "IDPqr",
		"mod" => "Pqr"
	));


	$script = "pqrfuncionario";

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

					$files =  SIMFile::upload($_FILES["Archivo1"], PQR_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Archivo1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Archivo1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Archivo2"], PQR_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Archivo2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Archivo2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Archivo3"], PQR_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Archivo3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Archivo3"] = $files[0]["innername"];
				} //end if

				$sql_max_numero = string;
				$sql_max_numero = "Select MAX(Numero) as NumeroMaximo From PqrFuncionario Where IDClub = '" . $frm["IDClub"] . "'";
				$result_numero = $dbo->query($sql_max_numero);
				$row_numero = $dbo->fetchArray($result_numero);
				$siguiente_consecutivo = (int)$row_numero["NumeroMaximo"] + 1;
				$frm["Numero"] = $siguiente_consecutivo;


				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				SIMUtil::noticar_nuevo_pqr_funcionario($id);
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




				//Quito los campos que no necesito que se actualicen
				unset($frm["Asunto"]);
				unset($frm["Descripcion"]);

				$IDAreaAnt = $frm["IDAreaAnt"];
				$IDArea = $frm["IDArea"];
				$IDPqrEstadoAnt = $frm["IDPqrEstadoAnt"];
				$IDPqrEstado = $frm["IDPqrEstado"];


				//UPLOAD de imagenes
				if (isset($_FILES)) {


					$files =  SIMFile::upload($_FILES["Archivo1"], PQR_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Archivo1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Archivo1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Archivo2"], PQR_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Archivo2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Archivo2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Archivo3"], PQR_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Archivo3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Archivo3"] = $files[0]["innername"];
				} //end if






				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
				$frm = $dbo->fetchById($table, $key, $id, "array");




				$notificar_cliente = $_POST["NotificarCliente"];
				if (!empty($_POST[Cuerpo])) {
					$_POST[Cuerpo] = utf8_decode($_POST[Cuerpo]);

					$sql_inserta_respuesta = "INSERT INTO Detalle_PqrFuncionario (IDPqr, IDUsuario, Fecha, Respuesta,UsuarioTrCr, FechaTrCr)
												VALUES ('" . $frm[IDPqr] . "','" . SIMUser::get("IDUsuario") . "','" . date("Y-m-d") . "','" . $_POST[Cuerpo] . "','Admin',NOW())";

					$dbo->query($sql_inserta_respuesta);

					if ($notificar_cliente == "S") :
						//Averiguo el nombre del modulo del pqr
						$nombre_modulo = utf8_encode($dbo->getFields("ClubModulo", "Titulo", "IDModulo = '15' and IDClub = '" . $frm["IDClub"] . "'"));
						if (empty($nombre_modulo))
							$nombre_modulo = "Pqr";

						$Mensaje = "Cordial Saludo, se ha dado respuesta a su " . $nombre_modulo . ", por favor ingrese al app para conocer mas detalles.";

						SIMUtil::envia_respuesta_funcionario($frm, SIMNet::reqInt("id"), $_POST[Cuerpo], $frm["IDClub"]);
						SIMUtil::enviar_notificacion_push_general_funcionario($frm["IDClub"], $frm["IDUsuarioCreacion"], $Mensaje);

					endif;
				}

				//Si se reasiga el pqr envio el mail de confirmación
				if ($IDAreaAnt != $IDArea) {
					$nueva_area = $dbo->getFields("AreaFuncionario", "Nombre", "IDArea = '" . $IDArea . "'");
					$sql_inserta_respuesta = "INSERT INTO Detalle_PqrFuncionario (IDPqr, IDUsuario, Fecha, Respuesta,UsuarioTrCr, FechaTrCr)
												VALUES ('" . $frm[IDPqr] . "','" . SIMUser::get("IDUsuario") . "','" . date("Y-m-d") . "','Se asigno al area: " . $nueva_area . "','Admin',NOW())";
					$dbo->query($sql_inserta_respuesta);

					SIMUtil::noticar_nuevo_pqr_funcionario(SIMNet::reqInt("id"));
				}

				//Si se cambia de estado el pqr guardo en la bitacora
				if ($IDPqrEstadoAnt != $IDPqrEstado) {
					$nuevo_estado = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $IDPqrEstado . "'");
					$sql_inserta_respuesta = "INSERT INTO Detalle_PqrFuncionario (IDPqr, IDUsuario, Fecha, Respuesta,UsuarioTrCr, FechaTrCr)
												VALUES ('" . $frm[IDPqr] . "','" . SIMUser::get("IDUsuario") . "','" . date("Y-m-d") . "','Se cambio estado: " . $nuevo_estado . "','Admin',NOW())";
					$dbo->query($sql_inserta_respuesta);

					// Si se cierra envio correo al respondable que se cerro
					if ($IDPqrEstado == 3) {
						SIMUtil::noticar_cierre_pqr_func(SIMNet::reqInt("id"));
					}
				}





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


		case "delDoc":
			$foto = $_GET['doc'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = PQR_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = " . $_GET[id] . "   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;


		case "PqrCerrados":
			$view = "views/" . $script . "/listPqrCerrados.php";
			break;


		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
