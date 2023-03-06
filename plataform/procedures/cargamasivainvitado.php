 <?php

	$titulo = "Invitado";


	SIMReg::setFromStructure(array(
		"title" => "InvitadosEspeciales",
		"table" => "SocioInvitadoEspecial",
		"key" => "IDSocioInvitadoEspecial",
		"mod" => "SocioInvitado"
	));


	$script = "invitadosespeciales";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	require( LIBDIR . "SIMWebServiceAccesos.inc.php" );


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

			//Relacion de Campos
			$CedulaAutoriza = $sheet->getCell("A" . $row)->getValue();
			$FechaIngreso = $sheet->getCell("B" . $row)->getFormattedValue();
			$FechaSalida = $sheet->getCell("C" . $row)->getFormattedValue();
			$DocumentoInvitado = utf8_encode($sheet->getCell("D" . $row)->getValue());
			$Nombre = utf8_encode($sheet->getCell("E" . $row)->getValue());
			$Apellido = $sheet->getCell("F" . $row)->getValue();
			$Email = $sheet->getCell("G" . $row)->getValue();
			$Telefono = $sheet->getCell("H" . $row)->getValue();
			$TipoSangre = $sheet->getCell("I" . $row)->getValue();
			$Placa = $sheet->getCell("J" . $row)->getValue();

			if (is_numeric($DocumentoInvitado)) :
				if (is_numeric($CedulaAutoriza) && !empty($CedulaAutoriza)) :
					if (!empty($FechaIngreso) && !empty($FechaSalida)) :
						if (!empty($Nombre) && !empty($Apellido)) :

							$pos = strpos($FechaIngreso, "/");
							if ($pos === false) {
								//echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
							} else {
								$array_nueva_fecha = explode("/", $FechaIngreso);

								if ((int)$array_nueva_fecha["0"] < 10)
									$mas = "0";

								$FechaIngreso = $array_nueva_fecha[2] . "-" . $mas . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
							}

							$pos = strpos($FechaSalida, "/");
							if ($pos === false) {
								//echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
							} else {
								$array_nueva_fecha = explode("/", $FechaSalida);

								if ((int)$array_nueva_fecha["0"] < 10)
									$mas = "0";

								$FechaSalida = $array_nueva_fecha[2] . "-" . $mas . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
							}

							if (strlen($FechaIngreso) == 10 && strlen($FechaSalida) == 10) {

								//Consulto Socio
								$sql_socio = "Select *
												From Socio
												Where IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $CedulaAutoriza . "'";
								$result_socio = $dbo->query($sql_socio);

								if ($dbo->rows($result_socio) > 0) :


									$row_datos_socio = $dbo->fetchArray($result_socio);
									//Crear invitacion
									//Servicio de invitados
									$array_datos = array();

									$array_datos_invitado["IDTipoDocumento"] = "2";
									$array_datos_invitado["NumeroDocumento"] = $DocumentoInvitado;
									$array_datos_invitado["Nombre"] = $Nombre;
									$array_datos_invitado["Apellido"] = $Apellido;
									$array_datos_invitado["Email"] = $Email;
									$array_datos_invitado["TipoInvitado"] = $TipoSangre;
									$array_datos_invitado["Placa"] = $Placa;
									$array_datos_invitado["CabezaInvitacion"] = "N";
									$array_datos_invitado["TipoSangre"] = $TipoSangre;
									array_push($array_datos, $array_datos_invitado);
									$DatosInvitado = json_encode($array_datos);

									$respuesta = SIMWebServiceAccesos::set_autorizacion_invitado($IDClub, $row_datos_socio["IDSocio"], $FechaIngreso, $FechaSalida, $DatosInvitado, "", "", "S");
									print_r($respuesta["message"]);
									echo "<br><br>";
									$numregok++;
								else :
									echo "<br>" . "La cedula de quien invita no existe en la base: " . $CedulaAutoriza;

								endif;
							} else {
								echo "<br>" . "Las fechas tienen un formato invalido: FILA" . $row;;
							}
						else :
							echo "<br>" . "Nombre y Apellido son obligatorios, FILA: " . $row;
						endif;
					else :
						echo "<br>" . "Las fechas de ingreso y salida son obligatorios, FILA: " . $row;
					endif;
				else :
					echo "<br>" . "El numero de documento de quien autoriza debe ser numerico o esta vacio, FILA: " . $row;
				endif;
			else :
				echo "<br>" . "El numero de documento debe ser numerico, FILA:" . $row;
			endif;




			$cont++;
		} // END While

		fclose($fp);
		return array("Numregs" => $cont, "RegsOK" => $numregok);


		return false;
	}




	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//Verificar permisos
	SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);


	switch (SIMNet::req("action")) {


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
			display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
			exit;
			break;
	} // End switch



	?>
