 <?

	SIMReg::setFromStructure(array(
		"title" => "GrupodeEmpleados",
		"table" => "GrupoUsuario",
		"key" => "IDGrupoUsuario",
		"mod" => "NotificacionesGenerales"
	));


	$script = "grupoempleado";

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
			//echo "File : ".$file['file']['name']."... ";
			//echo "Size :".$file['file']['size']." Bytes ... ";
			//echo "Status : Transfer Ok ...<br>";
			return $nuevo_nombre;
		} else {
			return "error";
		}
	}


	function get_data_accion($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $IDGrupoUsuario)
	{

		$dbo = &SIMDB::get();

		$numregok = 0;

		if (!empty($field))
			$strfields = "(" . implode(",", $field) . ")";

		if ($fp = fopen($file, "r")) {
			$cont = 0;
			ini_set('auto_detect_line_endings', true);
			if ($IGNORE_FIRTS_ROW)
				$row = fgets($fp, 4096);

			while (!feof($fp)) {

				$row = fgets($fp, 4096);

				//Relacion de Campos
				$Documento = trim($row);

				if (!empty($Documento)) {



					//Consulto Que exista el usuario
					$sql_usuario = "Select IDUsuario 
										  From Usuario
										  Where IDClub = '" . $IDClub . "' and NumeroDocumento= '" . $Documento . "'";
					$result_usuario = $dbo->query($sql_usuario);

					if ($dbo->rows($result_usuario) > 0) :
						$datos_usuario = $dbo->fetchArray($result_usuario);
						$array_id_usuario[] = $datos_usuario["IDUsuario"];
						$numregok++;

					else :
						$array_usuario_no_existe[] = "El siguiente numero de documento no existe: " . $Documento;
						$numregfail++;
					endif;
				} else {
					echo "<br>" . "El numero de documento esta equivocado: " . $Documento;
				}

				$cont++;
			} // END While
			fclose($fp);


			$array_resultado["Exitosos"] = $numregok;
			$array_resultado["NoExitosos"] = $numregfail;
			$array_resultado["ReporteNoExitoso"] = $array_usuario_no_existe;

			if (count($array_id_usuario) > 0) :
				$id_usuarios = implode("|||", $array_id_usuario);
				$sql_grupo = "Update GrupoUsuario Set IDUsuario = '" . $id_usuarios . "' Where IDGrupoUsuario = '" . $IDGrupoUsuario . "'";
				$dbo->query($sql_grupo);
			endif;

			return $array_resultado;
		} else
			echo "error open $file";
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

				$invitados = explode("|||", $frm["InvitadoSeleccion"]);
				if (count($invitados) > 0) :
					foreach ($invitados as $nom_invitado) :
						$array_datos = explode("-", $nom_invitado);
						if ($array_datos[0] == "socio") : // socio club
							$datos_invitado[] = trim($array_datos[1]);
						endif;
					endforeach;
				endif;

				if (count($datos_invitado) > 0) :
					$frm["IDUsuario"] = implode("|||", $datos_invitado);
				endif;

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


				$invitados = explode("|||", $frm["InvitadoSeleccion"]);
				if (count($invitados) > 0) :
					foreach ($invitados as $nom_invitado) :
						$array_datos = explode("-", $nom_invitado);
						if ($array_datos[0] == "usuario") : // socio club
							$datos_invitado[] = trim($array_datos[1]);
						endif;
					endforeach;
				endif;

				if (count($datos_invitado) > 0) :
					$frm["IDUsuario"] = implode("|||", $datos_invitado);
				endif;

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				$frm = $dbo->fetchById($table, $key, $id, "array");


				//Archivo

				if (!empty($_FILES["file"]["name"])) :

					$time_start = SIMUtil::getmicrotime();
					$nombre_archivo = copiar_archivo($_POST, $_FILES);
					if ($nombre_archivo == "error") :
						echo "Error Transfiriendo Archivo";
						exit;
					endif;

					$array_resultado = get_data_accion($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], SIMNet::reqInt("id"));
					if ((int)$array_resultado["NoExitosos"] > 0) :
						$mensaje_carga = "<br>" . SIMUtil::get_traduccion('', '', 'Registrosexitosos', LANGSESSION) . ":" . $array_resultado["Exitosos"];
						$mensaje_carga .= "<br>" . SIMUtil::get_traduccion('', '', 'Nosepuedoingresar', LANGSESSION) . ":" . $array_resultado["NoExitosos"];
						if (count($array_resultado["ReporteNoExitoso"]) > 0) :
							foreach ($array_resultado["ReporteNoExitoso"] as $mensaje) :
								$mensaje_carga .= $mensaje;
							endforeach;
						endif;

						$time_end = SIMUtil::getmicrotime();
						$time = $time_end - $time_start;
						$time = number_format($time, 3);
					endif;
				endif;



				if ((int)$array_resultado["NoExitosos"] <= 0) :
					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				else :
					SIMHTML::jsAlert($mensaje_carga);
				endif;
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



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>