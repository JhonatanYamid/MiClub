 <?

	SIMReg::setFromStructure(array(
		"title" => "Invitado",
		"table" => "Invitado",
		"key" => "IDInvitado",
		"mod" => "SocioInvitado"
	));


	$script = "invitadosgeneral";

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

				$files =  SIMFile::upload($_FILES["FotoImagen"], IMGINVITADO_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["FotoImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["FotoFile"] = $files[0]["innername"];

				//verifico que el docuemnto no este registrado
				$id_invitado_valida = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . (int)$frm["NumeroDocumento"] . "'");
				if ((int)$id_invitado_valida > 0) :

					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Elnumerodedocumentoyaexiste,porfavorverifique', LANGSESSION));
					SIMHTML::jsRedirect($script . ".php?qryString=" . $frm["NumeroDocumento"] . "&action=search");
					exit;
				endif;


				//insertamos los datos
				$id_i = $dbo->insert($frm, $table, $key);

				//INSERT VACUNA

				$dbo = &SIMDB::get();
				$query = $dbo->query("SELECT V.IDVacuna FROM Invitado I LEFT JOIN Vacuna V ON I.IDInvitado=V.IDInvitado WHERE I.IDInvitado=" . $id_i);
				$consult = $dbo->fetch($query);

				if (empty($consult['IDVacuna'])) {
					$frm["IDInvitado"] = $id_i;
					$id = $dbo->insert($frm, 'Vacuna', 'IDVacuna');
				} else {
					$frm["IDInvitado"] = $id_i;
					$id = $dbo->update($frm, 'Vacuna', 'IDVacuna', $consult['IDVacuna']);
				}

				for ($i = 0; $i < $frm["campos_dinamicos"]["keys"]; $i++) {
					$frm_dinamico = [];
					$frm_dinamico["Valor"] = $frm["campos_dinamicos"]["Valor_" . $i];
					$frm_dinamico["Dosis"] = $frm["campos_dinamicos"]["Dosis_" . $i];
					$frm_dinamico["IDSocio"] = $frm["campos_dinamicos"]["IDSocio_" . $i];
					$frm_dinamico["IDCampoVacunacion"] = $frm["campos_dinamicos"]["IDCampoVacunacion_" . $i];
					$frm_dinamico["IDVacunaCampoVacunacion"] = $frm["campos_dinamicos"]["IDVacunaCampoVacunacion_" . $i];

					$frm_dinamico = SIMUtil::varsLOG($frm_dinamico);

					if ($frm_dinamico["IDVacunaCampoVacunacion"] == null && $frm_dinamico["IDVacunaCampoVacunacion"] == '') {
						$id = $dbo->insert($frm_dinamico, 'VacunaCampoVacunacion', 'IDVacunaCampoVacunacion');
					} else {
						$id = $dbo->update($frm_dinamico, 'VacunaCampoVacunacion', 'IDVacunaCampoVacunacion', $frm_dinamico["IDVacunaCampoVacunacion"]);
					}
				}

				//UPLOAD de imagenes
				if (isset($_FILES)) {

					if (!empty($_FILES['ImagenPrimeraDosis']['name'])) {
						$files = SIMFile::upload($_FILES["ImagenPrimeraDosis"], VACUNA_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						}

						$frm["ImagenPrimeraDosis"] = $files[0]["innername"];
					}

					if (!empty($_FILES['ImagenSegundaDosis']['name'])) {
						$files = SIMFile::upload($_FILES["ImagenSegundaDosis"], VACUNA_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						}

						$frm["ImagenSegundaDosis"] = $files[0]["innername"];
					}

					if (!empty($_FILES['ImagenTerceraDosis']['name'])) {
						$files = SIMFile::upload($_FILES["ImagenTerceraDosis"], VACUNA_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						}

						$frm["ImagenTerceraDosis"] = $files[0]["innername"];
					}
					if (!empty($_FILES['ImagenTerceraDosis']['name'])) {
						$files = SIMFile::upload($_FILES["ImagenTerceraDosis"], VACUNA_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						}

						$frm["ImagenTerceraDosis"] = $files[0]["innername"];
					}

					if (!empty($_FILES['PdfTerceraDosis']['name'])) {
						$files = SIMFile::upload($_FILES["PdfTerceraDosis"], VACUNA_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						}

						$frm["PdfTerceraDosis"] = $files[0]["innername"];
					}
				} //end if




				$id = $dbo->update($frm, 'Vacuna', 'IDVacuna', $id);

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


				$files =  SIMFile::upload($_FILES["FotoImagen"], IMGINVITADO_DIR, "IMAGE");

				if (empty($files) && !empty($_FILES["FotoImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

				$frm["FotoFile"] = $files[0]["innername"];

				$id_i = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));


				//INSERT VACUNA

				$dbo = &SIMDB::get();
				$query = $dbo->query("SELECT V.IDVacuna FROM Invitado I LEFT JOIN Vacuna V ON I.IDInvitado=V.IDInvitado WHERE I.IDInvitado=" . $id_i);
				$consult = $dbo->fetch($query);

				if (empty($consult['IDVacuna'])) {
					$frm["IDInvitado"] = $id_i;
					$id = $dbo->insert($frm, 'Vacuna', 'IDVacuna');
				} else {
					$frm["IDInvitado"] = $id_i;
					$id = $dbo->update($frm, 'Vacuna', 'IDVacuna', $consult['IDVacuna']);
				}

				for ($i = 0; $i < $frm["campos_dinamicos"]["keys"]; $i++) {
					$frm_dinamico = [];
					$frm_dinamico["Valor"] = $frm["campos_dinamicos"]["Valor_" . $i];
					$frm_dinamico["Dosis"] = $frm["campos_dinamicos"]["Dosis_" . $i];
					$frm_dinamico["IDSocio"] = $frm["campos_dinamicos"]["IDSocio_" . $i];
					$frm_dinamico["IDCampoVacunacion"] = $frm["campos_dinamicos"]["IDCampoVacunacion_" . $i];
					$frm_dinamico["IDVacunaCampoVacunacion"] = $frm["campos_dinamicos"]["IDVacunaCampoVacunacion_" . $i];

					$frm_dinamico = SIMUtil::varsLOG($frm_dinamico);

					if ($frm_dinamico["IDVacunaCampoVacunacion"] == null && $frm_dinamico["IDVacunaCampoVacunacion"] == '') {
						$id = $dbo->insert($frm_dinamico, 'VacunaCampoVacunacion', 'IDVacunaCampoVacunacion');
					} else {
						$id = $dbo->update($frm_dinamico, 'VacunaCampoVacunacion', 'IDVacunaCampoVacunacion', $frm_dinamico["IDVacunaCampoVacunacion"]);
					}
				}

				//UPLOAD de imagenes
				if (isset($_FILES)) {

					if (!empty($_FILES['ImagenPrimeraDosis']['name'])) {
						$files = SIMFile::upload($_FILES["ImagenPrimeraDosis"], VACUNA_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						}

						$frm["ImagenPrimeraDosis"] = $files[0]["innername"];
					}

					if (!empty($_FILES['ImagenSegundaDosis']['name'])) {
						$files = SIMFile::upload($_FILES["ImagenSegundaDosis"], VACUNA_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						}

						$frm["ImagenSegundaDosis"] = $files[0]["innername"];
					}

					if (!empty($_FILES['ImagenTerceraDosis']['name'])) {
						$files = SIMFile::upload($_FILES["ImagenTerceraDosis"], VACUNA_DIR, "IMAGE");
						if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
							SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
						}

						$frm["ImagenTerceraDosis"] = $files[0]["innername"];
					}
					if ($_FILES['PdfTerceraDosis']['name'] != '') {
						//Valido el pseo del archivo
						$tamano_archivo = $_FILES["PdfTerceraDosis"]['size'];
						if ($tamano_archivo >= 6000000) {
							SIMHTML::jsAlert("El archivo supera el limite de peso permitido de 6 megas, por favor verifique");
							SIMHTML::jsRedirect("?action=edit&id=" . $frm['IDSocio'] . "&tabsocio=vacuna2");
							return $respuesta;
						}
						//UPLOAD de imagenes
						$files = SIMFile::upload($_FILES['PdfTerceraDosis'], VACUNA_DIR, "");
						if (empty($files) && !empty($_FILES["PdfTerceraDosis"]["name"])) :
							SIMHTML::jsAlert("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.");
							SIMHTML::jsRedirect("?action=edit&id=" . $frm['IDSocio'] . "&tabsocio=vacuna2");
							return $respuesta;
						endif;
						$frm["PdfTerceraDosis"] = $files[0]["innername"];
					}
				} //end if

				$id = $dbo->update($frm, 'Vacuna', 'IDVacuna', $id);



				//fin update vacuna

				$frm = $dbo->fetchById($table, $key, $id_i, "array");




				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));

				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));


				//print_form( $frm , "update" ,  "Realizar Cambios" );
			} else
				exit;
			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;



		case "del-vacuna-image":
			$archivo = $_GET['archivo'];
			$numImagen = $_GET['num_img'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$idSocio = $_GET['IDSocio'];
			$filedelete = VACUNA_DIR . $archivo;
			unlink($filedelete);
			$queryUpdate = "UPDATE Vacuna SET Imagen$numImagen" . "Dosis=NULL WHERE IDVacuna=$id";
			$dbo->query($queryUpdate);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $idSocio . "");
			break;
		case "del-vacuna-image":
			$archivo = $_GET['archivo'];
			$numImagen = $_GET['num_img'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$idSocio = $_GET['IDSocio'];
			$filedelete = VACUNA_DIR . $archivo;
			unlink($filedelete);
			$queryUpdate = "UPDATE Vacuna SET Imagen$numImagen" . "Dosis=NULL WHERE IDVacuna=$id";
			$dbo->query($queryUpdate);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $idSocio . "");
			break;
		case "del-pdf":
			$archivo = $_GET['archivo'];
			$numImagen = $_GET['num_img'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$idSocio = $_GET['IDSocio'];
			$filedelete = VACUNA_DIR . $archivo;
			unlink($filedelete);
			$queryUpdate = "UPDATE Vacuna SET PdfTercera" . "Dosis=NULL WHERE IDVacuna=$id";
			$dbo->query($queryUpdate);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $idSocio . "");
			break;
		case "DelImgNot":
			$campo = $_GET['cam'];

			$doceliminar = IMGINVITADO_DIR . $dbo->getFields("Invitado", "$campo", "IDInvitado = '" . $_GET[id] . "'");
			unlink($doceliminar);
			$dbo->query("UPDATE Invitado SET $campo = '' WHERE IDInvitado = $_GET[id] LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));

			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			exit;
			break;

		case "InsertarVehiculo":
			$frm = SIMUtil::varsLOG($_POST);
			//Verifico que no exista la placa
			$placa = $dbo->getFields("Vehiculo", "Placa", "Placa = '" . $frm[Placa] . "' and IDInvitado = '" . $frm["ID"] . "'");
			if (empty($placa)) :
				$id = $dbo->insert($frm, "Vehiculo", "IDVehiculo");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			else :

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Laplacayaexisteporfavorverifique', LANGSESSION));
			endif;
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=vehiculos&id=" . $frm[ID]);
			exit;
			break;

		case "ModificaVehiculo":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "Vehiculo", "IDVehiculo", $frm[IDVehiculo]);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=vehiculos&id=" . $frm[ID]);
			exit;
			break;

		case "EliminaVehiculo":
			$id = $dbo->query("DELETE FROM Vehiculo WHERE IDVehiculo   = '" . $_GET[IDVehiculo] . "' LIMIT 1");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=vehiculos&id=" . $_GET["id"]);
			exit;
			break;

		case "InsertarEquipo":
			$frm = SIMUtil::varsLOG($_POST);
			//Verifico que no exista la placa
			$id = $dbo->insert($frm, "Equipo", "IDEquipo");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=equipos&id=" . $frm[ID]);
			exit;
			break;

		case "ModificaEquipo":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "Equipo", "IDEquipo", $frm[IDEquipo]);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=equipos&id=" . $frm[ID]);
			exit;
			break;

		case "EliminaEquipo":
			$id = $dbo->query("DELETE FROM Equipo WHERE IDEquipo   = '" . $_GET[IDEquipo] . "' LIMIT 1");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=equipos&id=" . $_GET["id"]);
			exit;
			break;

		case "InsertarLicenciaInvitado":
			$frm = SIMUtil::varsLOG($_POST);
			//Verifico que no exista la categoria
			$placa = $dbo->getFields("LicenciaInvitado", "Categoria", "Categoria = '" . $frm[Categoria] . "' and IDInvitado = '" . $frm["ID"] . "'");
			if (empty($placa)) :
				$id = $dbo->insert($frm, "LicenciaInvitado", "IDLicenciaInvitado");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			else :
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Lacategoriayaexisteporfavorverifique', LANGSESSION));

			endif;
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=licencias&id=" . $frm[ID]);
			exit;
			break;

		case "ModificaLicenciaInvitado":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "LicenciaInvitado", "IDLicenciaInvitado", $frm[IDLicenciaInvitado]);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=licencias&id=" . $frm[ID]);
			exit;
			break;

		case "EliminaLicenciaInvitado":
			$id = $dbo->query("DELETE FROM LicenciaInvitado WHERE IDLicenciaInvitado   = '" . $_GET[IDLicenciaInvitado] . "' LIMIT 1");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=licencias&id=" . $_GET["id"]);
			exit;
			break;

		case "InsertarObservacion":
			$frm = SIMUtil::varsLOG($_POST);
			//Verifico que no exista la categoria
			$id = $dbo->insert($frm, "ObservacionInvitado", "IDObservacionInvitado");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=observaciones&id=" . $frm[ID]);
			exit;
			break;

		case "ModificaObservacion":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "ObservacionInvitado", "IDObservacionInvitado", $frm[IDObservacionInvitado]);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=observaciones&id=" . $frm[ID]);
			exit;
			break;

		case "EliminaObservacion":
			$id = $dbo->query("DELETE FROM ObservacionInvitado WHERE IDObservacionInvitado   = '" . $_GET[IDObservacionInvitado] . "' LIMIT 1");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabinvitado=observaciones&id=" . $_GET["id"]);
			exit;
			break;



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>