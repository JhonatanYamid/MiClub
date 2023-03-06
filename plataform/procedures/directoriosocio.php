 <?

	SIMReg::setFromStructure(array(
		"title" => "DirectorioSocio",
		"table" => "DirectorioSocio",
		"key" => "IDDirectorioSocio",
		"mod" => "DirectorioSocio"
	));


	$script = "directoriosocio";

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

					$files =  SIMFile::upload($_FILES["Foto1"], DIRECTORIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["IconoWhatsapp"], DIRECTORIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["IconoWhatsapp"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["IconoWhatsapp"] = $files[0]["innername"];
				} //end if

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				//Actualizo si tiene otros campos
				if ((int)$frm["NumeroCampos"] > 0) :
					for ($i = 1; $i <= $frm["NumeroCampos"]; $i++) :
						$nombre_campo = "OtroCampo" . $i;
						$id_campo = "IdentificadorCampo" . $i;
						if (!empty($frm[$nombre_campo])) :
							//Si existe la edito si no la creo
							$id_directorio_valor = $dbo->getFields("CampoDirectorioSocioValor", "IDCampoDirectorioSocioValor", "IDDirectorioSocio = '" . $id . "' and IDCampoDirectorioSocio = '" . $frm[$id_campo] . "'");
							if ((int)$id_directorio_valor > 0) :
								$sql_edita = "Update CampoDirectorioSocioValor Set Valor = '" . $frm[$nombre_campo] . "' Where IDCampoDirectorioSocioValor = '" . $id_directorio_valor . "'";
								$dbo->query($sql_edita);
							else :
								$sql_inserta = "Insert Into CampoDirectorioSocioValor (IDCampoDirectorioSocio,IDDirectorioSocio, Valor)
														  Values ('" . $frm[$id_campo] . "','" . $id . "','" . $frm[$nombre_campo] . "')";
								$dbo->query($sql_inserta);
							endif;
						endif;
					endfor;
				endif;
				//Fin Actualizo si tiene otros campos

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


					$files =  SIMFile::upload($_FILES["Foto1"], DIRECTORIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["IconoWhatsapp"], DIRECTORIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["IconoWhatsapp"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["IconoWhatsapp"] = $files[0]["innername"];
				} //end if

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));


				//Actualizo si tiene otros campos
				if ((int)$frm["NumeroCampos"] > 0) :
					for ($i = 1; $i <= $frm["NumeroCampos"]; $i++) :
						$nombre_campo = "OtroCampo" . $i;
						$id_campo = "IdentificadorCampo" . $i;
						if (!empty($frm[$nombre_campo])) :
							//Si existe la edito si no la creo
							$id_directorio_valor = $dbo->getFields("CampoDirectorioSocioValor", "IDCampoDirectorioSocioValor", "IDDirectorioSocio = '" . SIMNet::reqInt("id") . "' and IDCampoDirectorioSocio = '" . $frm[$id_campo] . "'");
							if ((int)$id_directorio_valor > 0) :
								$sql_edita = "Update CampoDirectorioSocioValor Set Valor = '" . $frm[$nombre_campo] . "' Where IDCampoDirectorioSocioValor = '" . $id_directorio_valor . "'";
								$dbo->query($sql_edita);
							else :
								$sql_inserta = "Insert Into CampoDirectorioSocioValor (IDCampoDirectorioSocio,IDDirectorioSocio, Valor)
												  Values ('" . $frm[$id_campo] . "','" . SIMNet::reqInt("id") . "','" . $frm[$nombre_campo] . "')";
								$dbo->query($sql_inserta);
							endif;
						endif;
					endfor;
				endif;
				//Fin Actualizo si tiene otros campos



				$frm = $dbo->fetchById($table, $key, $id, "array");




				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			}



			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = DIRECTORIO_DIR . $foto;
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
