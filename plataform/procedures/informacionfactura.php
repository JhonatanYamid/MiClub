 <?

	SIMReg::setFromStructure(array(
		"title" => "informaciondefactura",
		"titleB" => "informaciondefactura",
		"table" => "InformacionFactura",
		"key" => "IDInformacionFactura",
		"mod" => "InformacionFactura"
	));

	$script = "informacionfactura";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	$action = SIMNet::req("action");

	$IDClub = SIMUser::get("club");
	$idPadre = SIMUtil::IdPadre($IDClub);

	//Verificar permisos
	//SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	switch ($action) {

		case "add":
			$view = "views/" . $script . "/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;

		case "insert":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);		
			
				if($frm['Activo'] == '')
					$frm['Activo'] = 'S';

				if (isset($_FILES)) {
					$files =  SIMFile::upload($_FILES["logo"], FACTURA_DIR, "IMAGE");
				
					if (empty($files) && !empty($_FILES["logo"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["logo"] = $files[0]["innername"];
				}

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
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

				if (isset($_FILES)) {
                    $imgAnt = $dbo->getFields("InformacionFactura", "Logo", "IDInformacionFactura = ".SIMNet::reqInt("id"));

                    if($imgAnt != ''){
                        $filedelete = FACTURA_ROOT . $imgAnt;
                        unlink($filedelete);
                    }
                    
                    $files =  SIMFile::upload($_FILES["Logo"], FACTURA_DIR, "IMAGE");

                    if (empty($files) && !empty($_FILES["Logo"]["name"]))
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    $frm["Logo"] = $files[0]["innername"];
                }

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
				SIMHTML::jsRedirect($script . ".php");
			} else
				exit;

			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		case "delfoto":

			$logo = $_GET['logo'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];

			$filedelete = FACTURA_ROOT . $logo;
			unlink($filedelete);

			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id");
			
			SIMHTML::jsAlert("Imagen Eliminada Correctamente");
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");

			break;

		default:
			$view = "views/" . $script . "/list.php";
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/form.php";

?>