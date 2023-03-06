 <?

	SIMReg::setFromStructure(array(
		"title" => "mediosdepago",
		"titleB" => "mediodepago",
		"table" => "MediosPago",
		"key" => "IDMediosPago",
		"mod" => "MediosPago"
	));

	$script = "mediospago";

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
					
				if($frm['RequiereReferencia'] == '')
					$frm['RequiereReferencia'] = 'N';
					
				if($frm['RequiereAutorizacion'] == '')
					$frm['RequiereAutorizacion'] = 'N';
					
				if($frm['RequiereFecha'] == '')
					$frm['RequiereFecha'] = 'N';
				
				if($frm['EsCuenta'] == '')
					$frm['EsCuenta'] = 'N';
			
				if($frm['AplicaDebito'] == '')
					$frm['AplicaDebito'] = 'N';

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

		default:
			$view = "views/" . $script . "/list.php";
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/form.php";

?>