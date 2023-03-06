 <?

	SIMReg::setFromStructure(array(
		"title" => "Usuario",
		"table" => "Usuario",
		"key" => "IDUsuario",
		"mod" => "Usuario"
	));


	$script = "cambiarclave";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




	switch (SIMNet::req("action")) {


		case "updateclave":
			//Validamos que el usuario  exista
			$id = base64_decode($_GET["IDUsuario"]);
			$frm = $dbo->fetchById($table, $key, $id, "array");
			break;


		case "cambiarclave":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				//Validamos que la clave actual sea la misma
				$SqlUsuario = "SELECT * FROM Usuario Where IDUsuario = '" . $frm["ID"] . "' and Password = '" . sha1($frm["PasswordActual"]) . "' ";
				$QryUsuario = $dbo->query($SqlUsuario);
				$NumUsuario = $dbo->rows($QryUsuario);
				if ($NumUsuario <= 0) {
					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Laclaveactualnocorrespondeporfavorverifique', LANGSESSION));
					SIMHTML::jsRedirect($script . ".php?action=updateclave&IDUsuario=" . base64_encode($frm["ID"]));
					exit;
				}

				if ($frm["Password"] <> $frm["RePassword"]) {
					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Lacontraseñaysuconfirmacióndebenseriguales', LANGSESSION));
					SIMHTML::jsRedirect($script . ".php?action=updateclave&IDUsuario=" . base64_encode($frm["ID"]));
					exit;
				}

				if (sha1($frm["Password"]) == sha1($frm["PasswordActual"])) {
					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Lacontraseñanopuedeserlamismaalaanterior', LANGSESSION));
					SIMHTML::jsRedirect($script . ".php?action=updateclave&IDUsuario=" . base64_encode($frm["ID"]));
					exit;
				}

				$update_clave = "Update Usuario Set Password = '" . sha1($frm["Password"]) . "' , FechaCambioClave = CURDATE() Where IDUsuario = '" . $frm["ID"] . "' Limit 1";
				$dbo->query($update_clave);

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Claveactualizadaconexito', LANGSESSION));
				SIMHTML::jsRedirect("validausuario.php?action=Salir");
				//SIMHTML::jsRedirect( $script.".php?action=updateclave&IDUsuario=".base64_encode($frm["ID"]));
			} else
				exit;

			break;

		default:
			$view = "views/" . $script . "/form.php";
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
