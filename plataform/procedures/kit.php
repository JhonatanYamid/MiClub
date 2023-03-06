<?

SIMReg::setFromStructure(array(
	"title" => "Kit",
	"table" => "Kit",
	"key" => "IDKit",
	"mod" => "Socio"
));


$script = "kit";

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

		$sql_preguntas = " SELECT P.IDPreguntaKit,P.EtiquetaCampo,P.Orden
					FROM Kit K,
					PreguntaKit P  
					WHERE
					P.IDKit = K.IDKit AND
					K.IDClub = " . SIMUser::get("club") . "
					AND K.IDKit = " . SIMNet::reqInt("id") . "
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

		//echo "sqlPreguntas:" . $sql_preguntas;
		$result = $dbo->query($sql_preguntas);
		$numPregunta = 1;
		$array_preguntas = array();
		$array_NoPregunta = array();
		while ($rowPregunta = $dbo->fetchArray($result)) {

			$array_preguntas[$rowPregunta["IDPreguntaKit"]] = str_replace(",", "/", $rowPregunta["EtiquetaCampo"]);
			$array_NoPregunta[$rowPregunta["IDPreguntaKit"]] = "_" . $rowPregunta["IDPreguntaKit"];
		}


		break;

	case "update":


		if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG($_POST);


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
		$frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
		$id = $dbo->insert($frm, "PreguntaKit", "IDPreguntaKit");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabkit=formulario&id=" . $frm[IDKit]);
		exit;
		break;

	case "ModificaPregunta":
		$frm = SIMUtil::varsLOG($_POST);
		$frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
		$dbo->update($frm, "PreguntaKit", "IDPreguntaKit", $frm["IDPreguntaKit"]);
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabkit=formulario&id=" . $frm[IDKit]);
		exit;
		break;

	case "EliminaPregunta":
		$id = $dbo->query("DELETE FROM PreguntaKit WHERE IDPreguntaKit   = '" . $_GET["IDPreguntaKit"] . "' LIMIT 1");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabkit=formulario&id=" . $_GET["id"]);
		exit;
		break;

	case "EliminarRespuesta":

		for ($i = 1; $i <= $_GET["cantidad"]; $i++) {
			$sql = "DELETE FROM EncuestaRespuesta WHERE IDEncuesta  = '" . $_GET["IDEncuesta"] . "' AND IDSocio = '" . $_GET["IDSocio"] . "' ORDER BY FechaTrCr DESC LIMIT 1";
			$dbo->query($sql);
		}

		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&id=" . $_GET["IDEncuesta"]);
		exit;

		break;



	case "DelDocNot":
		$campo = $_GET['cam'];
		$doceliminar = BANNERAPP_DIR . $dbo->getFields("Encuesta", "$campo", "IDEncuesta = '" . $_GET[id] . "'");
		unlink($doceliminar);
		$dbo->query("UPDATE Encuesta SET $campo = '' WHERE IDEncuesta = $_GET[id] LIMIT 1 ;");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ArchivoEliminadoCorrectamente', LANGSESSION));
		SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
		exit;
		break;

	case "EliminaRegistro":

		$id = $dbo->query("DELETE FROM EncuestaRespuesta WHERE IDEncuestaRespuesta   = '" . $_GET["IDEncuestaRespuesta"] . "' LIMIT 1");
		//$dbo->query("DELETE FROM EventoRegistroDatos WHERE IDEventoRegistro   = '" . $_GET["IDEventoRegistro"] . "' LIMIT 1");


		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroEliminado', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=responderencuesta&id=" . $_GET["id"]);
		exit;
		break;



	default:
		$view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
	$view = "views/" . $script . "/form.php";
