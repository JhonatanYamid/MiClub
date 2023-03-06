 <?

	SIMReg::setFromStructure(array(
		"title" => "Correspondencia",
		"table" => "Correspondencia",
		"key" => "IDCorrespondencia",
		"mod" => "Correspondencia"
	));


	$script = "correspondencia";

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
			$NumeroDocumento = $sheet->getCell("A" . $row)->getValue();
			$TipoRecibo = $sheet->getCell("B" . $row)->getValue();
			$NumeroDocumentoUsuarioCrea = $sheet->getCell("C" . $row)->getValue();
			$Vivienda = $sheet->getCell("D" . $row)->getValue();
			$Destinatario = $sheet->getCell("E" . $row)->getValue();
			$FechaRecepcion = $sheet->getCell("F" . $row)->getFormattedValue();

			$pos = strpos($FechaRecepcion, "/");
			if ($pos === false) {
				//echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
			} else {
				$array_nueva_fecha = explode("/", $FechaRecepcion);
				$FechaRecepcion = $array_nueva_fecha[2] . "-" . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
			}

			if (!empty($NumeroDocumento)) {

				$hora = date("H:m:s");

				$IDSocio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = " . $NumeroDocumento . " AND IDClub = " . $IDClub);
				$IDUsuario = $dbo->getFields("Usuario", "IDUsuario", "NumeroDocumento = " . $NumeroDocumentoUsuarioCrea . " AND IDClub = " . $IDClub);
				$IDTipoCorrrespondencia = $dbo->getFields("TipoCorrespondencia", "IDTipoCorrespondencia", "Nombre = '" . $TipoRecibo . "' AND IDClub = " . $IDClub);
				$IDCorrespondeciaEstado = "1";
				$FechaRecepcion .= " " . $hora;

				$sqlInsert = "INSERT INTO Correspondencia (IDClub, IDSocio, IDTipoCorrespondencia, IDUsuarioCrea, IDCorrespondenciaEstado, Vivienda, Destinatario, FechaRecepcion, FechaTrCr, UsuarioTrCr)
										VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDTipoCorrrespondencia . "','" . $IDUsuario . "','" . $IDCorrespondeciaEstado . "','" . $Vivienda . "','" . $Destinatario . "','" . $FechaRecepcion . "',NOW(),'Carga Plano Arcvhivo')";
				$id = $dbo->query($sqlInsert);

				if (!empty($id))
					$numregok++;

				$Mensaje = "Hay correspondencia pediente de recoger";
				$sql_notif = "INSERT INTO ColaNotificacionPush (IDClub, IDSocio, Mensaje, UsuarioTrCr, FechaTrCr) VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $Mensaje . "','Aut',NOW())";
				$dbo->query($sql_notif);

				SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $frm["IDSocio"], $Mensaje, '59');
			} else {
				echo "<br>" . "El numero de documento debe ser numerico: " . $NumeroDocumento;
			}

			$cont++;
		} // end for
		fclose($fp);
		return array("Numregs" => $cont, "RegsOK" => $numregok);
	}


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
                                if(($frm["IDSocios"])){
                                $frm["IDSocio"]=$frm["IDSocios"];
                                }else{
                                $frm["IDSocio"]=$frm["IDSocio"];
                                }
                                
                                

				$fechaRecepcion = $_POST["FechaRecepcion"] . " " . $_POST["HoraFechaRecepcion"];
				$frm["FechaRecepcion"] = $fechaRecepcion;

				$fechaEntrega = $_POST["FechaEntrega"] . " " . $_POST["HoraFechaEntrega"];
				$frm["FechaEntrega"] = $fechaEntrega;

				//UPLOAD de imagenes
				if (isset($_FILES)) {
					$files =  SIMFile::upload($_FILES["Archivo"], CORRESPONDENCIA_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Archivo"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Archivo"] = $files[0]["innername"];
				} //end if


				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);


				//Inserto Cola de notificaciones
				$Mensaje = "Nueva correspondencia: " . utf8_decode($dbo->getFields("TipoCorrespondencia", "Nombre", "IDTipoCorrespondencia = '" . $IDTipoCorrrespondencia . "'"));
				SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $frm["IDSocio"], $Mensaje, '59', $id);

				$sql_notif = "INSERT INTO ColaNotificacionPush (IDClub, IDSocio, Mensaje, UsuarioTrCr, FechaTrCr) VALUES ('" . $frm["IDClub"] . "','" . $frm["IDSocio"] . "','" . $Mensaje . "','Aut',NOW())";
				$dbo->query($sql_notif);


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
				 if(($frm["IDSocios"])){
                                $frm["IDSocio"]=$frm["IDSocios"];
                                }else{
                                $frm["IDSocio"]=$frm["IDSocio"];
                                }
                                
				 
                                $ID= $frm["ID"];
				$fechaRecepcion = $_POST["FechaRecepcion"] . " " . $_POST["HoraFechaRecepcion"];
				$frm["FechaRecepcion"] = $fechaRecepcion;

				$fechaEntrega = $_POST["FechaEntrega"] . " " . $_POST["HoraFechaEntrega"];
				
				$datos = str_replace(':',"-", $fechaEntrega);
				  
				  
				$frm["FechaEntrega"] = $fechaEntrega;
				$estado= $frm["IDCorrespondenciaEstado"];
                                $IDCliente= $frm["IDUsuarioEntrega"];
                                $parametros_codigo_barras= $datos. " ".$ID;
                                if($estado=="2"){
				$codigo_barra= SIMUtil::generar_codigo_barras_correspondencia($parametros_codigo_barras,$IDCliente);
                                $frm["CodigoBarraCorrespondencia"] = $codigo_barra;
                                }else{
                                $frm["CodigoBarraCorrespondencia"] = "";
                                }
				//UPLOAD de imagenes
				if (isset($_FILES)) {

					$files =  SIMFile::upload($_FILES["Archivo"], CORRESPONDENCIA_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Archivo"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Archivo"] = $files[0]["innername"];
				} //end if

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));


				/* $Mensaje = "Nueva correspondencia: " . utf8_decode($dbo->getFields("TipoCorrespondencia", "Nombre", "IDTipoCorrespondencia = '" . $IDTipoCorrrespondencia . "'"));
				SIMUtil::enviar_notificacion_push_general($frm["IDClub"], $frm["IDSocio"], $Mensaje, '59', SIMNet::reqInt("id")); */

				$sql_notif = "INSERT INTO ColaNotificacionPush (IDClub, IDSocio, Mensaje, UsuarioTrCr, FechaTrCr) VALUES ('" . $frm["IDClub"] . "','" . $frm["IDSocio"] . "','" . $Mensaje . "','Aut',NOW())";
				$dbo->query($sql_notif);


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
			$foto = $_GET['doc'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = CORRESPONDENCIA_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
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
			} // if($result["Numregs"] > 0){

			$time_end = SIMUtil::getmicrotime();
			$time = $time_end - $time_start;
			$time = number_format($time, 3);
			SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
			exit;
			break;



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
