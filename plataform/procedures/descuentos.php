 <?

	SIMReg::setFromStructure(array(
		"title" => "descuento",
		"titleB" => "descuentos",
		"table" => "Descuentos",
		"key" => "IDDescuentos",
		"mod" => "Descuentos"
	));

	$script = "descuentos";

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

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);
				
				if($IDClub == $idPadre){
					$arrSel = $frm['clubes'];
					$cont = count($arrSel);
					$j = 1;

					if(!empty($arrSel)){
						$sqlIns = "INSERT INTO DescuentosClub(IDDescuentos, IDClub) VALUES ";

						foreach($arrSel as $idCl){
							
							$sqlIns .= "($id,$idCl)";
				
							if($j < $cont)
								$sqlIns .= ",";
		
							$j++;
						}
					}
				}else{
					$sqlIns = "INSERT INTO DescuentosClub(IDDescuentos, IDClub) VALUES ($id,$IDClub)";
				}

				$resIns = $dbo->query($sqlIns);

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

				if($IDClub == $idPadre){
					$arrSel = $frm['clubes'];
					$cont = count($arrSel);
					$j = 1;

					$sqlDel = "DELETE FROM DescuentosClub WHERE IDDescuentos = $id";
					$resDel = $dbo->query($sqlDel);

					if(!empty($arrSel)){
						$sqlIns = "INSERT INTO DescuentosClub(IDDescuentos, IDClub) VALUES ";

						foreach($arrSel as $idCl){
							
							$sqlIns .= "($id,$idCl)";
							
							if($j < $cont)
								$sqlIns .= ",";
		
							$j++;
						}
						$resIns = $dbo->query($sqlIns);
					}
				}

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