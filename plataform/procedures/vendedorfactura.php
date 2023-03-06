 <?

	SIMReg::setFromStructure(array(
		"title" => "Vendedor",
		"titleB" => "Vendedores",
		"table" => "VendedorFactura",
		"key" => "IDVendedorFactura",
		"mod" => "VendedorFactura"
	));

	$script = "vendedorfactura";

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

				$repetido = $dbo->getFields("VendedorFactura", "COUNT(IDVendedorFactura)", "Codigo = '".$frm['Codigo']."' AND IDClub = ".$frm['IDClub'] );

				if($repetido > 0){
					SIMHTML::jsAlert("Elcodigoyaexiste,porfavorverifiquelo");
					SIMHTML::jsRedirect("?mod=" . $mod . "&action=add");
				}
				else{
					//insertamos los datos
					$id = $dbo->insert($frm, $table, $key);

					SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
					SIMHTML::jsRedirect($script . ".php");
				}

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
				
				$cod = $dbo->getFields("VendedorFactura", "Codigo", "IDVendedorFactura = ".SIMNet::reqInt("id"));

				if($cod != $frm['Codigo']){
				
					$repetido = $dbo->getFields("VendedorFactura", "COUNT(IDVendedorFactura)", "Codigo = '".$frm['Codigo']."' AND IDClub = ".$frm['IDClub'] );

					if($repetido > 0){
						SIMHTML::jsAlert("Elcodigoyaexiste,porfavorverifiquelo");
						SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
					}
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

		default:
			$view = "views/" . $script . "/list.php";
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/form.php";

?>