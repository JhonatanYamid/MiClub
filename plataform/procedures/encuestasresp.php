<?

SIMReg::setFromStructure(array(
	"title" => "Encuestasinfinita",
	"table" => "Encuesta",
	"key" => "IDEncuesta",
	"mod" => "Encuesta"
));


$script = "encuestasresp";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");
require LIBDIR . "SIMWebServiceEncuestas.inc.php";

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);



switch (SIMNet::req("action")) {

 //MUESTRA LAS PREGUNTAS
	case "edit":
		$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
		$view = "views/" . $script . "/form.php";
		$newmode = "update";
		$titulo_accion = "Actualizar";

		$sql_preguntas = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
					FROM Encuesta E
					JOIN Pregunta P ON P.IDEncuesta = E.IDEncuesta
					WHERE E.IDClub =8
					AND E.IDEncuesta =751
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

		$result = $dbo->query($sql_preguntas);
		$numPregunta = 1;
		$array_preguntas = array();
		$array_NoPregunta = array();
		while ($rowPregunta = $dbo->fetchArray($result)) {

			$array_preguntas[$rowPregunta["IDPregunta"]] = str_replace(",", "/", $rowPregunta["EtiquetaCampo"]);
			$array_NoPregunta[$rowPregunta["IDPregunta"]] = "_" . $rowPregunta["IDPregunta"];
		}


		break;

		//ELIMINA LA ENCUESTA
	case "EliminaRegistro":

		$id = $dbo->query("DELETE FROM EncuestaRespuesta WHERE IDEncuestaRespuesta   = '" . $_GET["IDEncuestaRespuesta"] . "' LIMIT 1");
		//$dbo->query("DELETE FROM EventoRegistroDatos WHERE IDEventoRegistro   = '" . $_GET["IDEventoRegistro"] . "' LIMIT 1");


		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroEliminado', LANGSESSION));
		SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=responderencuesta&id=" . $_GET["id"] . "&IDModulo=" . $_GET["IDModulo"]);
		exit;
		break;



	default:
		$view = "views/" . $script . "/form.php";
} // End switch



if (empty($view))
	$view = "views/" . $script . "/form.php";
