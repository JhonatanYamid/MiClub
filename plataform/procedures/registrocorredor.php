 <?

	SIMReg::setFromStructure(array(
		"title" => "Corredor",
		"titleB" => "Corredores",
		"table" => "RegistroCorredor",
		"key" => "IDRegistroCorredor",
		"mod" => "RegistroCorredor"
	));

	$script = "registrocorredor";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	$action = SIMNet::req("action");

	$IDClub = SIMUser::get("club");
	$UsuarioTrCr = SIMUser::get("Nombre");
	$datos_club = $dbo->getFields("Club", array("Nombre", "FotoLogoApp"), "IDClub = $IDClub");
	$hoy = date("Y-m-j h:i:s");
	$TipoSocio = "Plan Básico";

	function copiar_archivo(&$frm, $file, $hoy)
	{

		$filedir = TRIATLON_DIR;
		$nuevo_nombre = rand(0, 1000000) . "_" . $hoy . "_" . $IDClub . "_" . $file['file']['name'];

		if (copy($file['file']['tmp_name'], "$filedir/$nuevo_nombre")) {

			echo "File : " . $file['file']['name'] . "... ";
			echo "Size :" . $file['file']['size'] . " Bytes ... ";
			echo "Status : Transfer Ok ...<br>";

			return $nuevo_nombre;
		} else {
			echo "error";
		}
	}

	function get_data($nombre_archivo, $archivo, $idCarrera, $idCategoria, $ingresarCamisetaManual = "")
	{

		$dbo = &SIMDB::get();

		$hoy = date("Y-m-j h:i:s");
		$IDClub = SIMUser::get("club");
		$datos_club = $dbo->getFields("Club", array("Nombre", "FotoLogoApp"), "IDClub = $IDClub");
		$UsuarioTrCr = SIMUser::get("Nombre");
		$arrCorreos = [];
		$arrCodigos = [];

		$numregok = 0;
		$cont = 0;
		$arrRes = array();

		require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";

		$inputFileType = PHPExcel_IOFactory::identify($archivo);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($archivo);
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		for ($row = 2; $row <= $highestRow; $row++) {

			$numeroDocumento = $sheet->getCell("A" . $row)->getValue();
			$nombre = $sheet->getCell("B" . $row)->getValue();
			$apellido = $sheet->getCell("C" . $row)->getValue();
			$email = $sheet->getCell("D" . $row)->getValue();
			$numeroCamisetas = $sheet->getCell("E" . $row)->getValue();

			$arrCorreo = [];
			$arrCodigo = [];

			echo "<br>*" . $numeroDocumento . "-" . $nombre . "-" . $apellido . "-" . $email . "";

			if ($numeroDocumento != '') {

				if ($ingresarCamisetaManual == "S") {
					$NumCamiseta = $numeroCamisetas;
				} else {
					$NumCamiseta = $dbo->getFields("RegistroCorredor", "Max(NumCamiseta)", "IDCategoriaTriatlon = $idCategoria");
					$NumCamiseta = !$NumCamiseta ? $dbo->getFields("CategoriaTriatlon", "NumInicial", "IDCategoriaTriatlon = $idCategoria") : $NumCamiseta + 1;
				}

				$idSocio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '$numeroDocumento' AND IDClub = $IDClub");


				echo "-" . $idSocio . "-" . $NumCamiseta . "<br>";

				$arrSocio = [
					"NumeroDocumento" => $numeroDocumento,
					"Nombre" => $nombre,
					"Apellido" => $apellido,
					"CorreoElectronico" => $email,
					"IDClub" => $IDClub,
					"UsuarioTrEd" => $UsuarioTrCr,
					"FechaTrEd" => $hoy,
					"TipoSocio" => $TipoSocio
				];

				$arrCorredor = [
					"NumeroDocumento" => $numeroDocumento,
					"Nombre" => $nombre,
					"Apellido" => $apellido,
					"Email" => $email,
					"IDCategoriaTriatlon" => $idCategoria,
					"IDCarrera" => $idCarrera,
					"IDClub" => $IDClub,
					"NumCamiseta" => $NumCamiseta,
					"UsuarioTrCr" => $UsuarioTrCr,
					"FechaTrCr" => $hoy,
					"UsuarioTrEd" => $UsuarioTrCr,
					"FechaTrEd" => $hoy,
					"TipoSocio" => $TipoSocio
				];

				if ($idSocio > 0) {

					if ($nombre != '' && $apellido != '' && $email != '') {
						$socio = $dbo->getFields("Socio", array("Nombre", "Apellido", "CorreoElectronico"), "IDSocio = $idSocio");

						if ($nombre != $socio['Nombre'] || $apellido != $socio['Apellido'] || $email != $socio['CorreoElectronico'])
							$dbo->update($arrSocio, 'Socio', 'IDSocio', $idSocio);
					} else {
						$socio = $dbo->getFields("Socio", array("Nombre", "Apellido", "CorreoElectronico"), "IDSocio = $idSocio");
						$arrCorredor['Nombre'] = $socio["Nombre"];
						$arrCorredor['Apellido'] = $socio["Apellido"];
						$arrCorredor['Email'] = $socio["CorreoElectronico"];
					}
				} else {

					//if ($nombre != '' && $apellido != '' && $email != '') {
					if ($nombre != '' && $apellido != '') {
						$arrSocio['Accion'] = $numeroDocumento;
						$arrSocio['NumeroDerecho'] = $numeroDocumento;
						$arrSocio['Email'] = $numeroDocumento;
						$arrSocio['Clave'] = sha1($numeroDocumento);
						$arrSocio['UsuarioTrCr'] = $UsuarioTrCr;
						$arrSocio['FechaTrCr'] = $hoy;
						$arrSocio['SolicitaEditarPerfil'] = 'S';
						$arrSocio['IDEstadoSocio'] = '1';
						$arrSocio['TipoSocio'] = $TipoSocio;

						$idSocio = $dbo->insert($arrSocio, 'Socio', 'IDSocio');

						$arrCorreo['nombre'] = $nombre . " " . $apellido;
						$arrCorreo['documento'] = $numeroDocumento;
						$arrCorreo['email'] = $email;
						$arrCorreo['tipo'] = "nuevo";

						array_push($arrCorreos, $arrCorreo);
					} else {

						echo "<BR>Error en la fila $row!,si el socio no existe, los campos Nombre, Apellido no pueden ser vacios.<BR>";
						$idSocio = 0;
					}
				}

				if ($idSocio > 0) {

					$arrCorredor['IDSocio'] = $idSocio;
					$idCorredor = $dbo->getFields("RegistroCorredor", "IDRegistroCorredor", "IDSocio = $idSocio AND IDClub = $IDClub AND IDCategoriaTriatlon = $idCategoria");

					if ($idCorredor > 0) {
						echo "<BR>Error en la fila $row!,el participante ya se encuentra registrado en la carrera.<BR>";
					} else {

						$idCorredor = $dbo->insert($arrCorredor, 'RegistroCorredor', 'IDRegistroCorredor');

						$parametros_codigo_qr = $NumCamiseta . "|" . $numeroDocumento . "|" . $arrCorredor['Nombre'] . " " . $arrCorredor['Apellido'];
						$arrCodigo['parametros'] = $parametros_codigo_qr;
						$arrCodigo['IDSocio'] = $idSocio;
						$arrCodigo['IDRegistroCorredor'] = $idCorredor;
						array_push($arrCodigos, $arrCodigo);

						$arrCorreo['IDRegistroCorredor'] = $idCorredor;
						$arrCorreo['nombre'] = $nombre . " " . $apellido;
						$arrCorreo['documento'] = $numeroDocumento;
						$arrCorreo['email'] = $email;
						$arrCorreo['tipo'] = "ingreso";
						array_push($arrCorreos, $arrCorreo);

						$numregok++;
					}
				}
			} else {
				echo "<BR>Error en la fila $row!,El campo Numero de Documento no puede ser vacio.<BR>";
			}

			$cont++;
		}

		fclose($fp);

		$rutas = [];
		$PNG_TEMP_DIR = TRIATLON_DIR  . "qr/";

		//html PNG location prefix
		include LIBDIR . "phpqrcode/qrlib.php";

		//ofcourse we need rights to create temp dir
		if (!file_exists($PNG_TEMP_DIR)) {
			mkdir($PNG_TEMP_DIR);
		}

		$matrixPointSize = 5;
		$errorCorrectionLevel = 'L';

		//genera codigos
		foreach ($arrCodigos as $arrCodigo) {

			$filename = $PNG_TEMP_DIR . 'QR_corredor_' . $arrCodigo['IDRegistroCorredor'] . '_' . rand(1, 10000) . '.png';
			QRcode::png($arrCodigo['parametros'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);

			$ruta_qr_triatlon = basename($filename);

			$dbo->update(array('CodigoQr' => $ruta_qr_triatlon), 'RegistroCorredor', 'IDRegistroCorredor', $arrCodigo['IDRegistroCorredor']);

			$rutas[$arrCodigo['IDRegistroCorredor']] = $ruta_qr_triatlon;
		}

		print_r($rutas);
		//envia correos
		foreach ($arrCorreos as $arrCorreo) {
			if ($arrCorreo['tipo'] == 'nuevo') {

				$msg = "<br>Señor(a): " . $arrCorreo['nombre'] . " <br><br>
							Le damos la bienvenida a ser parte de la familia " . $datos_club['Nombre'] . ". 
							Desde ahora puede ingresar a nuestra App con los siguientes datos:<br>
							<b>Usuario:</b> " . $arrCorreo['documento'] . "<br>
							<b>Clave:</b> " . $arrCorreo['documento'] . "<br><br>											
							Por favor no responda este correo<br><br>
							<b>Mi Club App</b>";

				$mensaje = "
							<body>
								<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
									<tr>
										<td>
											<img src='" . CLUB_ROOT . $datos_club['FotoLogoApp'] . "'>
										</td>
									</tr>
									<tr>
										<td> $msg </td>
									</tr>
								</table>
							</body>
									";

				$asunto = "Bienvenido a " . $datos_club['Nombre'];
			} else {

				$carrera = $dbo->getFields("Carrera", "Nombre", "IDCarrera = $idCarrera");
				$categoria = $dbo->getFields("CategoriaTriatlon", "Nombre", "IDCategoriaTriatlon = $idCategoria");

				$msg = "<br>Señor(a): " . $arrCorreo['nombre'] . " <br><br>
							La inscripcion a la carrera: <b>$carrera</b> en la categoria <b>$categoria</b> se realizo exitosamente.<br>
							A continuacion encontrara su codigo Qr<br><br>
							<img src='" . TRIATLON_ROOT . "qr/" . $rutas[$arrCorreo['IDRegistroCorredor']] . "'>
							<br>
							Por favor no responda este correo<br><br>
							<b>Mi Club App</b>";

				$mensaje = "
							<body>
								<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
									<tr>
										<td>
											<img src='" . CLUB_ROOT . $datos_club['FotoLogoApp'] . "'>
										</td>
									</tr>
									<tr>
										<td> $msg </td>
									</tr>
								</table>
							</body>
									";

				$asunto = "Inscripcion a la carrera $carrera";
			}

			SIMUtil::envia_correo_general($IDClub, $arrCorreo['email'], $mensaje, $asunto);
		}

		return array("Numregs" => $cont, "RegsOK" => $numregok);
	}

	//Verificar permisos
	//SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	switch ($action) {

		case "add":
			$view = "views/" . $script . "/list.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;

		case "insert":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				/* 	print_r($frm);
				exit; */


				if ($frm['IngresarCamisetaManual'] == "S" && !empty($frm["CampoIngresarCamisetaManual"])) {
					$NumCamiseta = $frm["CampoIngresarCamisetaManual"];
				} else {
					$NumCamiseta = $dbo->getFields("RegistroCorredor", "Max(NumCamiseta)", "IDCategoriaTriatlon = " . $frm['IDCategoriaTriatlon']);
					$NumCamiseta = !$NumCamiseta ? $dbo->getFields("CategoriaTriatlon", "NumInicial", "IDCategoriaTriatlon = " . $frm['IDCategoriaTriatlon']) : $NumCamiseta + 1;
				}
				$frm['NumCamiseta'] = $NumCamiseta;

				$arrSocio = [
					"NumeroDocumento" => $frm['NumeroDocumento'],
					"Nombre" => $frm['Nombre'],
					"Apellido" => $frm['Apellido'],
					"CorreoElectronico" => $frm['Email'],
					"UsuarioTrEd" => $UsuarioTrCr,
					"FechaTrEd" => $hoy,
					"TipoSocio" => $TipoSocio
				];

				if ($frm['IDSocio'] != '') {
					$socio = $dbo->getFields("Socio", array("Nombre", "Apellido", "CorreoElectronico"), "IDSocio = " . $frm['IDSocio']);

					if ($frm['Nombre'] != $socio['Nombre'] || $frm['Apellido'] != $socio['Apellido'] || $frm['Email'] != $socio['CorreoElectronico'])
						$dbo->update($arrSocio, 'Socio', 'IDSocio', $frm['IDSocio']);
				} else {

					$arrSocio['Accion'] = $frm['NumeroDocumento'];
					$arrSocio['NumeroDerecho'] = $frm['NumeroDocumento'];
					$arrSocio['Email'] = $frm['NumeroDocumento'];
					$arrSocio['Clave'] = sha1($frm['NumeroDocumento']);
					$arrSocio['SolicitaEditarPerfil'] = 'S';
					$arrSocio['IDClub'] = $frm['IDClub'];
					$arrSocio['UsuarioTrCr'] = $UsuarioTrCr;
					$arrSocio['FechaTrCr'] = $hoy;
					$arrSocio['TipoSocio'] = $TipoSocio;
					$arrSocio['IDEstadoSocio'] = "1";

					$frm['IDSocio'] = $dbo->insert($arrSocio, 'Socio', 'IDSocio');

					$msg = "<br>Señor(a): " . $frm['Nombre'] . " " . $frm['Apellido'] . " <br><br>
								Le damos la bienvenida a ser parte de la familia " . $datos_club['Nombre'] . ". 
								Desde ahora puede ingresar a nuestra App con los siguientes datos:<br>
								<b>Usuario:</b> " . $frm['NumeroDocumento'] . "<br>
								<b>Clave:</b> " . $frm['NumeroDocumento'] . "<br><br>											
								Por favor no responda este correo<br><br>
								<b>Mi Club App</b>";

					$mensaje = "
								<body>
									<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
										<tr>
											<td>
												<img src='" . CLUB_ROOT . $datos_club['FotoLogoApp'] . "'>
											</td>
										</tr>
										<tr>
											<td> $msg </td>
										</tr>
									</table>
								</body>
										";

					$asunto = "Bienvenido a " . $datos_club['Nombre'];

					SIMUtil::envia_correo_general($IDClub, $frm['Email'], $mensaje, $asunto);
				}

				$idCorredor = $dbo->getFields("RegistroCorredor", "IDRegistroCorredor", "IDSocio = " . $frm['IDSocio'] . " AND IDClub = $IDClub AND IDCategoriaTriatlon = " . $frm['IDCategoriaTriatlon']);

				if ($idCorredor === false) {

					$idCorredor = $dbo->insert($frm, 'RegistroCorredor', 'IDRegistroCorredor');

					$parametros_codigo_qr = $NumCamiseta . "|" . $frm['NumeroDocumento'] . "|" . $frm['Nombre'] . " " . $frm['Apellido'];
					$ruta_qr_triatlon = SIMUtil::generar_codigo_qr_triatlon($parametros_codigo_qr, $idSocio);

					$dbo->update(array('CodigoQr' => $ruta_qr_triatlon), 'RegistroCorredor', 'IDRegistroCorredor', $idCorredor);

					$carrera = $dbo->getFields("Carrera", "Nombre", "IDCarrera = " . $frm['IDCarrera']);
					$categoria = $dbo->getFields("CategoriaTriatlon", "Nombre", "IDCategoriaTriatlon = " . $frm['IDCategoriaTriatlon']);

					$msg = "<br>Señor(a): " . $frm['Nombre'] . " " . $frm['Apellido'] . "<br><br>
								La inscripcion a la carrera: <b>$carrera</b> en la categoria: <b>$categoria</b> se realizo exitosamente.<br>
								A continuacion encontrara su codigo Qr<br><br>
								<img src='" . TRIATLON_ROOT . "qr/" . $ruta_qr_triatlon . "'>
								<br>
								Por favor no responda este correo<br><br>
								<b>Mi Club App</b>";

					$mensaje = "
								<body>
									<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
										<tr>
											<td>
												<img src='" . CLUB_ROOT . $datos_club['FotoLogoApp'] . "'>
											</td>
										</tr>
										<tr>
											<td> $msg </td>
										</tr>
									</table>
								</body>";

					$asunto = "Inscripcion a la carrera $carrera";

					SIMUtil::envia_correo_general($IDClub, $frm['Email'], $mensaje, $asunto);
				} else {
					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Elcorredoryaseencuentraregistradoenlacarrera', LANGSESSION));
					SIMHTML::jsRedirect($script . ".php?action=add");
					exit;
				}

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=add");
			}

			break;


		case "edit":
			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
			$view = "views/" . $script . "/list.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";

			break;

		case "update":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$arrSocio = [
					"NumeroDocumento" => $frm['NumeroDocumento'],
					"Nombre" => $frm['Nombre'],
					"Apellido" => $frm['Apellido'],
					"CorreoElectronico" => $frm['Email'],
					"UsuarioTrEd" => $UsuarioTrCr,
					"FechaTrEd" => $hoy
				];

				if ($frm['IDSocio'] != '') {

					$socio = $dbo->getFields("Socio", array("Nombre", "Apellido", "CorreoElectronico"), "IDSocio = " . $frm['IDSocio']);

					if ($frm['Nombre'] != $socio['Nombre'] || $frm['Apellido'] != $socio['Apellido'] || $frm['Email'] != $socio['CorreoElectronico'])
						$dbo->update($arrSocio, 'Socio', 'IDSocio', $frm['IDSocio']);
				} else {

					$arrSocio['Accion'] = $frm['NumeroDocumento'];
					$arrSocio['NumeroDerecho'] = $frm['NumeroDocumento'];
					$arrSocio['Email'] = $frm['NumeroDocumento'];
					$arrSocio['Clave'] = sha1($frm['NumeroDocumento']);
					$arrSocio['SolicitaEditarPerfil'] = 'S';
					$arrSocio['IDClub'] = $IDClub;
					$arrSocio['UsuarioTrCr'] = $UsuarioTrCr;
					$arrSocio['FechaTrCr'] = $hoy;

					$frm['IDSocio'] = $dbo->insert($arrSocio, 'Socio', 'IDSocio');

					$msg = "<br>Señor(a): " . $frm['Nombre'] . " " . $frm['Apellido'] . " <br><br>
								Le damos la bienvenida a ser parte de la familia " . $datos_club['Nombre'] . ". 
								Desde ahora puede ingresar a nuestra App con los siguientes datos:<br>
								<b>Usuario:</b> " . $frm['NumeroDocumento'] . "<br>
								<b>Clave:</b> " . $frm['NumeroDocumento'] . "<br><br>											
								Por favor no responda este correo<br><br>
								<b>Mi Club App</b>";

					$mensaje = "
								<body>
									<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
										<tr>
											<td>
												<img src='" . CLUB_ROOT . $datos_club['FotoLogoApp'] . "'>
											</td>
										</tr>
										<tr>
											<td> $msg </td>
										</tr>
									</table>
								</body>
										";

					$asunto = "Bienvenido a " . $datos_club['Nombre'];

					SIMUtil::envia_correo_general($IDClub, $frm['Email'], $mensaje, $asunto);
				}

				$idCorredor = $dbo->getFields("RegistroCorredor", "IDRegistroCorredor", "IDRegistroCorredor != " . SIMNet::reqInt("id") . " AND IDSocio = " . $frm['IDSocio'] . " AND IDClub = $IDClub AND IDCategoriaTriatlon = " . $frm['IDCategoriaTriatlon']);

				if ($idCorredor === false) {

					$infoAnt = $dbo->getFields("RegistroCorredor", array("IDCategoriaTriatlon", "NumeroDocumento", "Nombre", "Apellido", "NumCamiseta", "CodigoQr"), "IDRegistroCorredor = " . SIMNet::reqInt("id"));

					$ruta_qr_triatlon = $infoAnt['CodigoQr'];

					if ($infoAnt['NumeroDocumento'] != $frm['NumeroDocumento'] || $infoAnt['Nombre'] != $frm['Nombre'] || $infoAnt['Apellido'] != $frm['Apellido'] || $infoAnt['NumCamiseta'] != $frm['NumCamiseta']) {
						$parametros_codigo_qr = $frm['NumCamiseta'] . "|" . $frm['NumeroDocumento'] . "|" . $frm['Nombre'] . " " . $frm['Apellido'];
						$ruta_qr_triatlon = SIMUtil::generar_codigo_qr_triatlon($parametros_codigo_qr, $idSocio);
					}

					$frm['CodigoQr'] = $ruta_qr_triatlon;

					$dbo->update($frm, 'RegistroCorredor', 'IDRegistroCorredor', SIMNet::reqInt("id"));

					if ($infoAnt['IDCategoriaTriatlon'] != $frm['IDCategoriaTriatlon'] || $infoAnt['CodigoQr'] != $frm['CodigoQr']) {

						$carrera = $dbo->getFields("Carrera", "Nombre", "IDCarrera = " . $frm['IDCarrera']);
						$categoria = $dbo->getFields("CategoriaTriatlon", "Nombre", "IDCategoriaTriatlon = " . $frm['IDCategoriaTriatlon']);

						$msg = "<br>Señor(a): " . $frm['Nombre'] . " " . $frm['Apellido'] . "<br><br>
									La inscripcion a la carrera: <b>$carrera</b> en la categoria: <b>$categoria</b> se realizo exitosamente.<br>
									A continuacion encontrara su codigo Qr<br><br>
									<img src='" . TRIATLON_ROOT . "qr/" . $ruta_qr_triatlon . "'>
									<br>
									Por favor no responda este correo<br><br>
									<b>Mi Club App</b>";

						$mensaje = "
									<body>
										<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
											<tr>
												<td>
													<img src='" . CLUB_ROOT . $datos_club['FotoLogoApp'] . "'>
												</td>
											</tr>
											<tr>
												<td> $msg </td>
											</tr>
										</table>
									</body>";

						$asunto = "Inscripcion a la carrera $carrera";

						SIMUtil::envia_correo_general($IDClub, $frm['Email'], $mensaje, $asunto);
					}
				} else {
					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Elcorredoryaseencuentraregistradoenlacarrera', LANGSESSION));
					SIMHTML::jsRedirect($script . ".php?action=add");
					exit;
				}

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=add");

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=add");
			} else
				exit;

			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;

		case "cargarlote":
			$time_start = SIMUtil::getmicrotime();
			$nombre_archivo = copiar_archivo($_POST, $_FILES, $hoy);

			if ($nombre_archivo == "error") :
				echo "Error Transfiriendo Archivo";
				exit;
			endif;


			$result = get_data($nombre_archivo, TRIATLON_DIR . $nombre_archivo, $_POST['IDCarrera'], $_POST['IDCategoriaTriatlon'], $_POST['IngresarCamisetaManual']);
			if ($result["Numregs"] > 0) {
				echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
			}

			$time_end = SIMUtil::getmicrotime();
			$time = $time_end - $time_start;
			$time = number_format($time, 3);

			SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
			exit;
			break;

		default:
			$view = "views/" . $script . "/list.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;
	} // End switch

	if (empty($view)) {
		$view = "views/" . $script . "/list.php";
		$newmode = "insert";
		$titulo_accion = "Crear";
	}

	?>