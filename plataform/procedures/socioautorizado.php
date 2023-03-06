 <?

	SIMReg::setFromStructure(array(
		"title" => "SociosAutorizadoReservar",
		"table" => "SocioPermisoReserva",
		"key" => "IDSocioPermisoReserva",
		"mod" => "SocioAutorizacion"
	));


	$script = "socioautorizado";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");


	//Verificar permisos
	SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	function copiar_archivo(&$frm, $file)
	{
		$filedir = SOCIOPLANO_DIR;
		$nuevo_nombre = rand(0, 1000000) . "_" . date("Y-m-d") . "_" . $file['file']['name'];
		if (copy($file['file']['tmp_name'], "$filedir/" . $nuevo_nombre)) {
			echo "File : " . $file['file']['name'] . "... ";
			echo "Size :" . $file['file']['size'] . " Bytes ... ";
			echo "Status : Transfer Ok ...<br>";
			return $nuevo_nombre;
		} else {
			echo "error";
		}
	}

	function get_data($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub)
	{

		$dbo = &SIMDB::get();

		$numregok = 0;
		require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
		$archivo = $file;
		$inputFileType = PHPExcel_IOFactory::identify($archivo);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($archivo);
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		for ($row = 2; $row <= $highestRow; $row++) {
			$Cedula =  $sheet->getCell("A" . $row)->getValue();
			$Handicap =  $sheet->getCell("B" . $row)->getValue();



			if (!empty($Cedula)) {
				$sql_socios = "	SELECT NumeroDocumento 
								FROM SocioPermisoReserva 
								WHERE NumeroDocumento = '" . $Cedula . "'";
				$r_socios = $dbo->query($sql_socios);
				$row_socios = $dbo->fetchArray($r_socios);

				$Documento = $row_socios["NumeroDocumento"];



				if (!empty($Documento)) :

					$sql_edit_socio = "	UPDATE SocioPermisoReserva 
										Set NumeroDocumento = '" . $Cedula . "', Handicap = " . $Handicap . "
										Where NumeroDocumento = '" . $Documento . "'";
					$dbo->query($sql_edit_socio);
					$numregok++;
				else :
					if (!empty($Handicap)) {
						$sql_inserta_socio = "	INSERT INTO SocioPermisoReserva (IDClub, NumeroDocumento, Handicap, UsuarioTrCr, FechaTrCr)
												Values ('" . $IDClub . "','" . $Cedula . "'," . $Handicap . ",'Archivo Plano: " . $nombrearchivo . "',NOW())";

						$dbo->query($sql_inserta_socio);
						$numregok++;
					} else {
						echo "<br>" . "El Handicap es obligatorio";
					}
				endif;
			} else {
				echo "<br>" . "El Numero de Documento es obligatorio";
			}
			$cont++;
		} // end for
		fclose($fp);
		return array("Numregs" => $cont, "RegsOK" => $numregok);
	}


	switch (SIMNet::req("action")) {

		case "insert":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				//UPLOAD de imagenes
				if (isset($_FILES)) {
					$files =  SIMFile::upload($_FILES["Foto1"], BANNERAPP_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], BANNERAPP_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], BANNERAPP_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];
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


					$files =  SIMFile::upload($_FILES["Foto1"], BANNERAPP_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], BANNERAPP_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], BANNERAPP_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];
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
			$filedelete = BANNERAPP_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;

		case "cargarplano":

			$time_start = SIMUtil::getmicrotime();
			$nombre_archivo = copiar_archivo($_POST, $_FILES);
			if ($nombre_archivo == "error") :
				echo "Error Transfiriendo Archivo";
				exit;
			endif;

			$result = get_data($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub']);
			if ($result["Numregs"] > 0) {
				echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
			}
			$time_end = SIMUtil::getmicrotime();
			$time = $time_end - $time_start;
			$time = number_format($time, 3);
			SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
			exit;
			break;

		default;
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;
	} // End switch


	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
