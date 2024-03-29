<?

SIMReg::setFromStructure(array(
	"title" => "Diagnostico",
	"table" => "Diagnostico",
	"key" => "IDDiagnostico",
	"mod" => "Diagnostico"
));


$script = "diagnostico";

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
		/*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/

		if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG($_POST);

			$files =  SIMFile::upload($_FILES["Imagen"], BANNERAPP_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["Imagen"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["Imagen"] = $files[0]["innername"];



			$files =  SIMFile::upload($_FILES["Adjunto1Documento"], BANNERAPP_DIR, "DOC");
			if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["Adjunto1File"] = $files[0]["innername"];

			$files =  SIMFile::upload($_FILES["Adjunto2Documento"], BANNERAPP_DIR, "DOC");
			if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["Adjunto2File"] = $files[0]["innername"];


			//insertamos los datos del asistente
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

			$files =  SIMFile::upload($_FILES["Imagen"], BANNERAPP_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["Imagen"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["Imagen"] = $files[0]["innername"];

			$files =  SIMFile::upload($_FILES["Adjunto1Documento"], BANNERAPP_DIR, "DOC");
			if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["Adjunto1File"] = $files[0]["innername"];

			$files =  SIMFile::upload($_FILES["Adjunto2Documento"], BANNERAPP_DIR, "DOC");
			if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["Adjunto2File"] = $files[0]["innername"];



			$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"), "");



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

	case "delfoto":
		$campo = $_GET['campo'];
		$doceliminar = BANNERAPP_DIR . $dbo->getFields("Encuesta", "$campo", "IDEncuesta = '" . $_GET[id] . "'");
		unlink($doceliminar);
		$dbo->query("UPDATE Encuesta SET $campo = '' WHERE IDEncuesta = $_GET[id] LIMIT 1 ;");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));

		SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
		exit;
		break;

	case "InsertarPregunta":
		$frm = SIMUtil::varsLOG($_POST);
		$id = $dbo->insert($frm, "PreguntaDiagnostico", "IDPreguntaDiagnostico");
		//Guardo opciones de respuesta
		for ($i = 1; $i <= $frm["CantidadOpciones"]; $i++) {
			if (!empty($frm["Respuesta" . $i])) {
				$frm["Respuesta" . $i] = trim(preg_replace('/\s+/', ' ', $frm["Respuesta" . $i]));
				$sql_insert = "INSERT INTO DiagnosticoOpcionesRespuesta (IDDiagnosticoPregunta,IDDiagnosticoPreguntaSiguiente,Opcion,Terminar,Peso,Orden)
														 VALUES('" . $id . "','" . $frm["IDDiagnosticoPreguntaSiguiente" . $i] . "','" . $frm["Respuesta" . $i] . "','" . $frm["Terminar" . $i] . "','" . $frm["Peso" . $i] . "','" . $frm["Orden" . $i] . "')";
				$dbo->query($sql_insert);
			}
		}

		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDDiagnostico]);
		exit;
		break;

	case "ModificaPregunta":
		$frm = SIMUtil::varsLOG($_POST);
		$dbo->update($frm, "PreguntaDiagnostico", "IDPreguntaDiagnostico", $frm["IDPreguntaDiagnostico"]);

		$borro_opciones = "DELETE FROM DiagnosticoOpcionesRespuesta WHERE IDDiagnosticoPregunta = '" . $frm["IDPreguntaDiagnostico"] . "'";
		$dbo->query($borro_opciones);
		for ($i = 1; $i <= $frm["CantidadOpciones"]; $i++) {
			if (!empty($frm["Respuesta" . $i])) {
				$frm["Respuesta" . $i] = trim(preg_replace('/\s+/', ' ', $frm["Respuesta" . $i]));
				$sql_insert = "INSERT INTO DiagnosticoOpcionesRespuesta (IDDiagnosticoPregunta,IDDiagnosticoPreguntaSiguiente,Opcion,Terminar,Peso,Orden)
																 VALUES('" . $frm["IDPreguntaDiagnostico"] . "','" . $frm["IDDiagnosticoPreguntaSiguiente" . $i] . "','" . $frm["Respuesta" . $i] . "','" . $frm["Terminar" . $i] . "','" . $frm["Peso" . $i] . "','" . $frm["Orden" . $i] . "')";
				$dbo->query($sql_insert);
			}
		}


		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDDiagnostico]);
		exit;
		break;

	case "EliminaPregunta":
		$id = $dbo->query("DELETE FROM PreguntaDiagnostico WHERE IDPreguntaDiagnostico   = '" . $_GET["IDPreguntaDiagnostico"] . "' LIMIT 1");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $_GET["id"]);
		exit;
		break;


	case "InsertarNotificacionLocal":
		$frm = SIMUtil::varsLOG($_POST);
		$frm["IDModulo"] = 99;
		$frm["IDDetalle"] = $frm["IDDiagnostico"];
		foreach ($frm["IDDia"] as $Dia_seleccion) :
			$array_dia[] = $Dia_seleccion;
		endforeach;
		if (count($array_dia) > 0) :
			$id_dia = implode("|", $array_dia) . "|";
		endif;
		$frm["Dias"] = $id_dia;
		$id = $dbo->insert($frm, "NotificacionLocal", "IDNotificacionLocal");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $frm["IDDiagnostico"]);
		exit;
		break;

	case "ModificaNotificacionLocal":
		$frm = SIMUtil::varsLOG($_POST);
		foreach ($frm["IDDia"] as $Dia_seleccion) :
			$array_dia[] = $Dia_seleccion;
		endforeach;
		if (count($array_dia) > 0) :
			$id_dia = implode("|", $array_dia) . "|";
		endif;
		$frm["Dias"] = $id_dia;
		$dbo->update($frm, "NotificacionLocal", "IDNotificacionLocal", $frm["IDNotificacionLocal"]);
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $frm["IDDiagnostico"]);
		exit;
		break;

	case "EliminaNotificacionLocal":
		$id = $dbo->query("DELETE FROM NotificacionLocal WHERE IDNotificacionLocal   = '" . $_GET["IDNotificacionLocal"] . "' LIMIT 1");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $_GET["id"]);
		exit;
		break;

	case "DelDocNot":
		$campo = $_GET['cam'];
		$doceliminar = BANNERAPP_DIR . $dbo->getFields("Diagnostico", "$campo", "IDDiagnostico = '" . $_GET[id] . "'");
		unlink($doceliminar);
		$dbo->query("UPDATE Diagnostico SET $campo = '' WHERE IDDiagnostico = $_GET[id] LIMIT 1 ;");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ArchivoEliminadoCorrectamente', LANGSESSION));
		SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
		exit;
		break;


	default:
		$view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
	$view = "views/" . $script . "/form.php";
