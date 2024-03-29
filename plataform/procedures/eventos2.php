 <?

	SIMReg::setFromStructure(array(
		"title" => "Evento",
		"table" => "Evento2",
		"key" => "IDEvento2",
		"mod" => "Evento"
	));


	$script = "eventos2";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	require_once LIBDIR . "SIMWebServiceEventos.inc.php";

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
			/*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$files =  SIMFile::upload($_FILES["EventoImagen"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["EventoImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["EventoFile"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto1"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto1"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto1"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto2"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto2"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto2"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto3"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto3"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto3"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["FotoDestacada"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["FotoDestacada"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["FotoDestacada"] = $files[0]["innername"];


				$files =  SIMFile::upload($_FILES["SWF"], SWFEvento_DIR);
				if (empty($files) && !empty($_FILES["SWF"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["SWF"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Adjunto1Documento"], IMGEVENTO_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Adjunto1File"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Adjunto2Documento"], IMGEVENTO_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Adjunto2File"] = $files[0]["innername"];


				//insertamos los datos del asistente
				$id = $dbo->insert($frm, $table, $key);

				$frm["IDModulo"] = 4;
				$frm["IDSeccion"] = $frm["IDSeccionEvento22"];
				$frm["TipoNotificacion"] = 'evento'; //socio
				$frm["Mensaje"] = $frm["Introduccion"];
				if (empty($frm["Mensaje"])) $frm["Mensaje"] = ".";

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {

					//notiifcar push
					if ($frm["DirigidoA"] == "S" || $frm["DirigidoA"] == "T") :
						$frm["TipoUsuario"] = 'S'; //socio
						//traer socios a los que les interesa la noticia
						$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccionEvento22 WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccionEvento22.IDSocio AND SocioSeccionEvento22.IDSeccionEvento22 = '" . $frm["IDSeccionEvento22"] . "' and Socio.Token <> '' and Socio.Token <> '2byte' ";

						$qry_socios = $dbo->query($sql_socios);
						while ($r_socios = $dbo->fetchArray($qry_socios)) {
							SIMUtil::envia_cola_notificacion($r_socios, $frm);

							$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
              Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . utf8_decode($frm["Titular"]) . "', '" . utf8_decode($frm["Mensaje"]) . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $frm["ID"] . "')");
						} //end while
					elseif ($frm["DirigidoA"] == "E") : //Empleados
						$frm["TipoUsuario"] = 'E'; //socio
						//traer empleados a los que les interesa la noticia
						$sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
						$qry_empleados = $dbo->query($sql_empleados);
						while ($r_empleados = $dbo->fetchArray($qry_empleados)) {

							SIMUtil::envia_cola_notificacion($r_empleados, $frm);
						} //end while
					endif;
				} //end if


				foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
					$sql_servicio_forma_pago = $dbo->query("Insert into EventoTipoPago2 (IDEvento2, IDTipoPago) Values ('" . $id . "', '" . $Pago_seleccionado . "')");

				endforeach;

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


				$files =  SIMFile::upload($_FILES["EventoImagen"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["EventoImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["EventoFile"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto1"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto1"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto1"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto2"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto2"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto2"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Foto3"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["Foto3"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Foto3"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["FotoDestacada"], IMGEVENTO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["FotoDestacada"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["FotoDestacada"] = $files[0]["innername"];


				$files =  SIMFile::upload($_FILES["SWF"], SWFEvento_DIR);
				if (empty($files) && !empty($_FILES["SWF"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["SWF"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Adjunto1Documento"], IMGEVENTO_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Adjunto1File"] = $files[0]["innername"];

				$files =  SIMFile::upload($_FILES["Adjunto2Documento"], IMGEVENTO_DIR, "DOC");
				if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["Adjunto2File"] = $files[0]["innername"];



				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"), "");

				$frm["IDModulo"] = 4;
				$frm["IDSeccion"] = $frm["IDSeccionEvento2"];
				$frm["TipoNotificacion"] = 'evento'; //socio
				$frm["Mensaje"] = $frm["Introduccion"];

				if (empty($frm["Mensaje"])) $frm["Mensaje"] = ".";

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {
					//notiifcar push
					if ($frm["DirigidoA"] == "S" || $frm["DirigidoA"] == "T") :
						$frm["TipoUsuario"] = 'S'; //socio
						//traer socios a los que les interesa la noticia
						$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccionEvento2 WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccionEvento2.IDSocio AND SocioSeccionEvento2.IDSeccionEvento2 = '" . $frm["IDSeccionEvento2"] . "'  and Socio.Token <> '' and Socio.Token <> '2byte' Order by IDSocio Desc";

						//$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccionEvento2 WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccionEvento2.IDSocio AND SocioSeccionEvento2.IDSeccionEvento2 = '" . $frm["IDSeccionEvento2"] . "'  and Socio.Token <> '' and Socio.Token <> '2byte' and (Socio.IDSocio = '144147' or Socio.IDSocio = 184054)";

						//$sql_socios = "SELECT * FROM Socio WHERE IDSocio = '84887'";

						$qry_socios = $dbo->query($sql_socios);
						while ($r_socios = $dbo->fetchArray($qry_socios)) {
							SIMUtil::envia_cola_notificacion($r_socios, $frm);
							$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
              Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . utf8_decode($frm["Titular"]) . "', '" . utf8_decode($frm["Mensaje"]) . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $frm["ID"] . "')");
						} //end while
					elseif ($frm["DirigidoA"] == "E") : //Empleados
						$frm["TipoUsuario"] = 'E'; //empleado
						//traer empleados a los que les interesa la noticia
						$sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
						$qry_empleados = $dbo->query($sql_empleados);
						while ($r_empleados = $dbo->fetchArray($qry_empleados)) {
							SIMUtil::envia_cola_notificacion($r_empleados, $frm);
						} //end while
					endif;
				} //end if


				$delete_tipo_pago = $dbo->query("Delete From EventoTipoPago2 Where IDEvento2 = '" . SIMNet::reqInt("id") . "'");
				foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
					$sql_servicio_forma_pago = $dbo->query("Insert into EventoTipoPago2 (IDEvento2, IDTipoPago) Values ('" . SIMNet::reqInt("id") . "', '" . $Pago_seleccionado . "')");
				endforeach;

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			} else
				exit;

			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		case "DelImgNot":
			$campo = $_GET['cam'];
			if ($campo == "SWF") {
				$doceliminar = SWFEvento_DIR . $dbo->getFields("Evento2", "$campo", "IDEvento2 = '" . $_GET[id] . "'");
				unlink($doceliminar);
				$dbo->query("UPDATE Evento2 SET $campo = '' WHERE IDEvento2 = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'SWFeliminadoCorrectamente', LANGSESSION));
			} else {
				$doceliminar = IMGEVENTO_DIR . $dbo->getFields("Evento2", "$campo", "IDEvento2 = '" . $_GET[id] . "'");
				unlink($doceliminar);
				$dbo->query("UPDATE Evento2 SET $campo = '' WHERE IDEvento2 = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			}
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
			exit;
			break;

		case "InsertarCampoFormularioEvento":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "CampoFormularioEvento2", "IDCampoFormularioEvento2");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabevento=formulario&id=" . $frm[IDEvento2]);
			exit;
			break;

		case "ModificaCampoFormularioEvento":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "CampoFormularioEvento2", "IDCampoFormularioEvento2", $frm["IDCampoFormularioEvento2"]);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabevento=formulario&id=" . $frm[IDEvento2]);
			exit;
			break;

		case "EliminaCampoFormularioEvento2":
			$id = $dbo->query("DELETE FROM CampoFormularioEvento2 WHERE IDCampoFormularioEvento2   = '" . $_GET["IDCampoFormularioEvento2"] . "' LIMIT 1");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabevento=formulario&id=" . $_GET["id"]);
			exit;
			break;


		case "EliminaRegistro":

			$id = $dbo->query("DELETE FROM EventoRegistro2 WHERE IDEventoRegistro2   = '" . $_GET["IDEventoRegistro2"] . "' LIMIT 1");
			$dbo->query("DELETE FROM EventoRegistroDatos2 WHERE IDEventoRegistro2   = '" . $_GET["IDEventoRegistro2"] . "' LIMIT 1");

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabevento=invitaciones&id=" . $_GET["id"]);
			exit;
			break;

		case "insertasocioevento":
			$r_campos = $dbo->all("CampoFormularioEvento2", "IDEvento2 = '" . $_POST["IDEvento2"]  . "'");
			$response = array();
			while ($r = $dbo->object($r_campos)) {
				$array_dinamicos["IDCampoFormularioEvento"] = $r->IDCampoFormularioEvento2;
				$array_dinamicos["Valor"] = $_POST["Campo" . $r->IDCampoFormularioEvento2];
				array_push($response, $array_dinamicos);
			}
			$ValoresFormulario = json_encode($response);

			if ($_POST['EsSocio'] == 'N') {
				$arrNoSocios['NumeroDocumento'] = $_POST['NumeroDocumento'];
				$arrNoSocios['Nombre'] = $_POST['NombreNoSocio'];
				$arrNoSocios['CorreoElectronico'] = $_POST['CorreoElectronico'];
				$arrNoSocios['Celular'] = $_POST['Celular'];
				$arrNoSocios['FechaNacimiento'] = $_POST['FechaNacimiento'];
				$arrNoSocios['IDClub'] = $_POST['IDClub'];

				$NoSocios = $dbo->getFields("NoSocios", array("IDNoSocios", "Nombre", "CorreoElectronico"), "NumeroDocumento = '" . $_POST['NumeroDocumento'] . "' AND IDClub = " . $_POST['IDClub']);

				if ($NoSocios) {
					$IDNoSocios = $NoSocios['IDNoSocios'];
					$dbo->update($arrNoSocios, "NoSocios", "IDNoSocios", $IDNoSocios);
				} else {
					$IDNoSocios = $dbo->insert($arrNoSocios, "NoSocios", "IDNoSocios");
				}
			}

			$respuesta = SIMWebServiceEventos::set_formulario_evento($_POST["IDClub"], $_POST["IDEvento2"], $_POST["IDSocio"], $_POST["IDSocioBeneficiario"], $ValoresFormulario, $OtrosDatosFormulario, "2", $UsuarioCrea, $IDNoSocios);
			if (!$respuesta['success'])
				SIMHTML::jsAlert($respuesta['message']);
			else
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'DatosGuardados', LANGSESSION));

			SIMHTML::jsRedirect($script . ".php?action=edit&tabevento=invitaciones&id=" . $_POST["IDEvento2"]);
			exit;
			break;

		case "DelDocNot":
			$campo = $_GET['cam'];
			$doceliminar = IMGEVENTO_DIR . $dbo->getFields("Evento2", "$campo", "IDEvento2 = '" . $_GET[id] . "'");
			unlink($doceliminar);
			$dbo->query("UPDATE Evento2 SET $campo = '' WHERE IDEvento2 = $_GET[id] LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ArchivoEliminadoCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
			exit;
			break;


		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
