<?

SIMReg::setFromStructure(array(
	"title" => "Dotacion",
	"table" => "Dotacion",
	"key" => "IDDotacion",
	"mod" => "Dotacion"
));


$script = "dotaciones";

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


			$files =  SIMFile::upload($_FILES["ImagenDotacion"], BANNERAPP_DIR, "DOC");
			if (empty($files) && !empty($_FILES["ImagenDotacion"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["ImagenDotacion"] = $files[0]["innername"];


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

		$sql_preguntas = " SELECT P.IDPreguntaDotacion,P.EtiquetaCampo,P.Orden
					FROM Dotacion E
					JOIN PreguntaDotacion P ON P.IDDotacion = E.IDDotacion
					WHERE E.IDClub = " . SIMUser::get("club") . "
					AND E.IDDotacion = " . SIMNet::reqInt("id") . "
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

		$result = $dbo->query($sql_preguntas);
		$numPregunta = 1;
		$array_preguntas = array();
		$array_NoPregunta = array();
		while ($rowPregunta = $dbo->fetchArray($result)) {

			$array_preguntas[$rowPregunta["IDPreguntaDotacion"]] = str_replace(",", "/", $rowPregunta["EtiquetaCampo"]);
			$array_NoPregunta[$rowPregunta["IDPreguntaDotacion"]] = "_" . $rowPregunta["IDPreguntaDotacion"];
		}

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


			$files =  SIMFile::upload($_FILES["ImagenDotacion"], BANNERAPP_DIR, "DOC");
			if (empty($files) && !empty($_FILES["ImagenDotacion"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

			$frm["ImagenDotacion"] = $files[0]["innername"];


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
		$doceliminar = BANNERAPP_DIR . $dbo->getFields("Encuesta", "$campo", "IDDotacion = '" . $_GET[id] . "'");
		unlink($doceliminar);
		$dbo->query("UPDATE Dotacion SET $campo = '' WHERE IDDotacion = $_GET[id] LIMIT 1 ;");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));

		SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
		exit;
		break;

	case "InsertarPreguntaDotacion":
		$frm = SIMUtil::varsLOG($_POST);
		$frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
		$id = $dbo->insert($frm, "PreguntaDotacion", "IDPreguntaDotacion");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDDotacion]);
		exit;
		break;

	case "ModificaPreguntaDotacion":
		$frm = SIMUtil::varsLOG($_POST);
		$frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
		$dbo->update($frm, "PreguntaDotacion", "IDPreguntaDotacion", $frm["IDPreguntaDotacion"]);
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDDotacion]);
		exit;
		break;

	case "EliminaPreguntaDotacion":
		$id = $dbo->query("DELETE FROM PreguntaDotacion WHERE IDPreguntaDotacion   = '" . $_GET["IDPreguntaDotacion"] . "' LIMIT 1");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $_GET["id"]);
		exit;
		break;

	case "InsertarNotificacionLocal":
		$frm = SIMUtil::varsLOG($_POST);
		$frm["IDModulo"] = 102;
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
		$doceliminar = BANNERAPP_DIR . $dbo->getFields("Dotacion", "$campo", "IDDotacion = '" . $_GET[id] . "'");
		unlink($doceliminar);
		$dbo->query("UPDATE Dotacion SET $campo = '' WHERE IDDotacion = $_GET[id] LIMIT 1 ;");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ArchivoEliminadoCorrectamente', LANGSESSION));
		SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
		exit;
		break;


	default:
		$view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
	$view = "views/" . $script . "/form.php";
