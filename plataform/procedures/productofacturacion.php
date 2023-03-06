 <?

	SIMReg::setFromStructure(array(
		"title" => "producto",
		"titleB" => "productos",
		"table" => "ProductoFacturacion",
		"key" => "IDProductoFacturacion",
		"mod" => "ProductoFacturacion"
	));

	$script = "productofacturacion";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	$action = SIMNet::req("action");

	$IDClub = SIMUser::get("club");
	$idPadre = SIMUtil::IdPadre($IDClub);
	$arrHijos = SIMUtil::ObtenerHijosClubPadre($idPadre);

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

				$idTipo = $frm['IDTipoFacturacion'];
				
				$sqlTipo = "SELECT * FROM TipoFacturacion WHERE IDTipoFacturacion = $idTipo";
				$resultTipo = $dbo->query($sqlTipo);
				$rowTipo = $dbo->fetchArray($resultTipo);

				$frm['NumSesiones'] = $rowTipo['PermitirReservar'] == 'S' && $rowTipo['NumSesiones'] == 'S' ? $frm['NumSesiones'] : "";	
				$frm['NumCongelacion'] = $rowTipo['PermitirReservar'] == 'S' && $rowTipo['Congelaciones'] == 2 ? $frm['NumCongelacion'] : '';
				$frm['TimeCongelacion'] = $rowTipo['PermitirReservar'] != 'S' || $rowTipo['Congelaciones'] == 3 ? '' : $frm['TimeCongelacion'];
				$frm['TipoCongelacion'] = $rowTipo['PermitirReservar'] != 'S' || $rowTipo['Congelaciones'] == 3 ? '' : $frm['TipoCongelacion'];
				
				if($rowTipo['PermitirReservar'] != 'S'){
					$frm['IDServicioMaestro'] = '';
				}

				if($rowTipo['ControlAcceso'] != 'S'){
					$frm['AccesoSedes'] = "";
					$frm['JornadasAcceso'] = "";
				} 

				if($rowTipo['Precio'] != 'S'){ 
					$frm['IDImpuestos'] = "";
					$frm['FacturacionInicio'] = "";
					$frm['FacturacionFin'] = "";
					$frm['IngresoTerceros'] = "";
				} 
				
				$frm['Editar'] = $frm['Editar'] == '' ? 'S' : $frm['Editar'];
				$frm['Eliminar'] = $frm['Eliminar'] == '' ? 'S' : $frm['Eliminar'];
				$frm['Activo'] = $frm['Activo'] == '' ? 'S' : $frm['Activo'];

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				$tipoVigencia = "Dias";

				if($frm['TipoVigencia'] == 1)
					$tipoVigencia = "Horas";
				
				if($frm['TipoVigencia'] == 3)
					$tipoVigencia = "Meses";

				$servicio = '';
				$idTalonera = 0;
				$talonera = [
					"NombreTalonera" => $frm['Nombre'],
					"ValorSocio" => 0,
					"CantidadEntradas" => $frm['NumSesiones'],
					"Duracion" => $frm['Vigencia'],
					"MedicionDuracion" => $tipoVigencia,
					"Activa" => 1,
					"TaloneraMonedero" => 0,
					"TodosLosServicios" => 0,
					"Activa" => 1
				];
				
				if($IDClub == $idPadre && !empty($arrHijos)){
					$arrPrecios = explode(",",$frm['precios']);
					$cont = count($arrPrecios);
					$j = 1;

					$sqlIns = "INSERT INTO ProductoPrecio(IDClub,IDProductoFacturacion, IDTalonera, Precio) VALUES ";
					
					if(!empty($arrPrecios)){

						foreach($arrPrecios as $precioTxt){
							$arrPrecio = explode("|",$precioTxt);
							$idCl = $arrPrecio[0];
							$precioCl = $arrPrecio[1] == '' || $rowTipo['Precio'] != 'S' ? 0 : $arrPrecio[1];

							if($frm['IDServicioMaestro'] != ''){
								$servicio = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = ".$frm['IDServicioMaestro']." AND IDClub = ".$idCl);
							}

							if($rowTipo['NumSesiones'] == 'S'){
								$talonera['IDClub'] = $idCl;
								$talonera['ValorSocio'] = $precioCl;
								$talonera['IDServicio'] = $servicio;

								$idTalonera = $dbo->insert($talonera, 'Talonera', 'IDTalonera');
							}
							
							$sqlIns .= "($idCl,$id,$idTalonera,$precioCl)";
				
							if($j < $cont)
								$sqlIns .= ",";
		
							$j++;

							$sqlCateg = "SELECT * FROM CategoriaFacturacionClub WHERE IDCategoriaFacturacion = ".$frm['IDCategoriaFacturacion']." AND IDClub = $idCl";
							$resCateg = $dbo->query($sqlCateg);
							$rowsCateg = $dbo->rows($resCateg);

							if($rowsCateg == 0){
								$sqlInsCat = "INSERT INTO CategoriaFacturacionClub(IDCategoriaFacturacion, IDClub) VALUES (".$frm['IDCategoriaFacturacion'].",$idCl) ";
								$resInsCat = $dbo->query($sqlInsCat);
							}

							$sqlTipo = "SELECT * FROM TipoFacturacionClub WHERE IDTipoFacturacion = ".$frm['IDTipoFacturacion']." AND IDClub = $idCl";
							$resTipo = $dbo->query($sqlTipo);
							$rowsTipo = $dbo->rows($resTipo);

							if($rowsTipo == 0){
								$sqlInsTipo = "INSERT INTO TipoFacturacionClub(IDTipoFacturacion, IDClub) VALUES (".$frm['IDTipoFacturacion'].",$idCl) ";
								$resInsTipo = $dbo->query($sqlInsTipo);
							}
						}
					}
					
				}else{
					$precioCl = $frm['precio'] == '' || $rowTipo['Precio'] != 'S' ? 0 : $frm['precio'];

					if($frm['IDServicioMaestro'] != ''){
						$servicio = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = ".$frm['IDServicioMaestro']." AND IDClub = ".$IDClub);
					}
					
					if($rowTipo['NumSesiones'] == 'S'){
						$talonera['IDClub'] = $IDClub;
						$talonera['ValorSocio'] = $precioCl;
						$talonera['IDServicio'] = $servicio;

						$idTalonera = $dbo->insert($talonera, 'Talonera', 'IDTalonera');
					}
					
					$sqlIns = "INSERT INTO ProductoPrecio(IDClub,IDProductoFacturacion,IDTalonera,Precio) VALUES ($IDClub,$id,$idTalonera,$precioCl)";
				}

				$resIns = $dbo->query($sqlIns);
				$id = $dbo->update(array("IDServicio" => $servicio), $table, $key, $id);
				
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
				
				$id = SIMNet::reqInt("id");
				$idTipo = $frm['IDTipoFacturacion'];
				$sqlTipo = "SELECT * FROM TipoFacturacion WHERE IDTipoFacturacion = $idTipo";
				$resultTipo = $dbo->query($sqlTipo);
				$rowTipo = $dbo->fetchArray($resultTipo);

				$frm['NumSesiones'] = $rowTipo['PermitirReservar'] == 'S' && $rowTipo['NumSesiones'] == 'S' ? $frm['NumSesiones'] : "";
				$frm['NumCongelacion'] = $rowTipo['PermitirReservar'] == 'S' && $rowTipo['Congelaciones'] == 2 ? $frm['NumCongelacion'] : '';
				$frm['TimeCongelacion'] = $rowTipo['PermitirReservar'] != 'S' || $rowTipo['Congelaciones'] == 3 ? '' : $frm['TimeCongelacion'];
				$frm['TipoCongelacion'] = $rowTipo['PermitirReservar'] != 'S' || $rowTipo['Congelaciones'] == 3 ? '' : $frm['TipoCongelacion'];
				
				if($rowTipo['PermitirReservar'] != 'S'){
					$frm['IDServicioMaestro'] = '';
				}

				if($rowTipo['ControlAcceso'] != 'S'){
					$frm['AccesoSedes'] = "";
					$frm['JornadasAcceso'] = "";
				} 

				if($rowTipo['Precio'] != 'S'){ 
					$frm['IDImpuestos'] = "";
					$frm['FacturacionInicio'] = "";
					$frm['FacturacionFin'] = "";
					$frm['IngresoTerceros'] = "";
				} 				

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
				
				$tipoVigencia = "Dias";

				if($frm['TipoVigencia'] == 1)
					$tipoVigencia = "Horas";
				
				if($frm['TipoVigencia'] == 3)
					$tipoVigencia = "Meses";

				$frm['IDServicio'] = '';
				$idTalonera = 0;
				$talonera = [
					"NombreTalonera" => $frm['Nombre'],
					"ValorSocio" => 0,
					"CantidadEntradas" => $frm['NumSesiones'],
					"Duracion" => $frm['Vigencia'],
					"MedicionDuracion" => $tipoVigencia,
					"TaloneraMonedero" => 0,
					"TodosLosServicios" => 0,
					"Activa" => 1
				];

				if($IDClub == $idPadre && !empty($arrHijos)){
					
					$sqlTal = "SELECT IDTalonera, IDClub FROM ProductoPrecio WHERE IDProductoFacturacion = $id";
					$resultTal = $dbo->query($sqlTal);
					while($rowTal = $dbo->fetchArray($resultTal)){
						$idsTal[$rowTal['IDClub']] = $rowTal['IDTalonera'];
					}

					$sqlDel = "DELETE FROM ProductoPrecio WHERE IDProductoFacturacion = $id";
					$resDel = $dbo->query($sqlDel);

					$arrPrecios = explode(",",$frm['precios']);
					$cont = count($arrPrecios);
					$j = 1;

					$sqlIns = "INSERT INTO ProductoPrecio(IDClub,IDProductoFacturacion,IDTalonera,Precio) VALUES ";
					
					if(!empty($arrPrecios)){
						foreach($arrPrecios as $precioTxt){
							$arrPrecio = explode("|",$precioTxt);
							$idCl = $arrPrecio[0];
							$precioCl = $arrPrecio[1] == '' || $rowTipo['Precio'] != 'S' ? 0 : $arrPrecio[1];

							if($frm['IDServicioMaestro'] != ''){
								$frm['IDServicio'] = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = ".$frm['IDServicioMaestro']." AND IDClub = ".$idCl);
							}
	
							if($rowTipo['NumSesiones'] == 'S'){

								$talonera['IDClub'] = $idCl;
								$talonera['ValorSocio'] = $precioCl;
								$talonera['IDServicio'] = $frm['IDServicio'];

								//Si la talonera ya existe se modifica, si no se crea una nueva. 
								if($idsTal[$idCl] && $idsTal[$idCl] != 0){
									$idTalonera = $dbo->update($talonera, 'Talonera', 'IDTalonera', $idsTal[$idCl]);
								}else{
									$idTalonera = $dbo->insert($talonera, 'Talonera', 'IDTalonera');
								}
							}
							
							$sqlIns .= "($idCl,$id,$idTalonera,$precioCl)";

							if($j < $cont)
								$sqlIns .= ",";
		
							$j++;

							$sqlCateg = "SELECT * FROM CategoriaFacturacionClub WHERE IDCategoriaFacturacion = ".$frm['IDCategoriaFacturacion']." AND IDClub = $idCl";
							$resCateg = $dbo->query($sqlCateg);
							$rowsCateg = $dbo->rows($resCateg);

							if($rowsCateg == 0){
								$sqlInsCat = "INSERT INTO CategoriaFacturacionClub(IDCategoriaFacturacion, IDClub) VALUES (".$frm['IDCategoriaFacturacion'].",$idCl) ";
								$resInsCat = $dbo->query($sqlInsCat);
							}

							$sqlTipo = "SELECT * FROM TipoFacturacionClub WHERE IDTipoFacturacion = ".$frm['IDTipoFacturacion']." AND IDClub = $idCl";
							$resTipo = $dbo->query($sqlTipo);
							$rowsTipo = $dbo->rows($resTipo);

							if($rowsTipo == 0){
								$sqlInsTipo = "INSERT INTO TipoFacturacionClub(IDTipoFacturacion, IDClub) VALUES (".$frm['IDTipoFacturacion'].",$idCl) ";
								$resInsTipo = $dbo->query($sqlInsTipo);
							}
						}

						$resIns = $dbo->query($sqlIns);
					}
				}else{
					$precioCl = $frm['precio'] == '' || $rowTipo['Precio'] != 'S' ? 0 : $frm['precio'];
					$idTalonera = 0;

					if($frm['IDServicioMaestro'] != ''){
						$frm['IDServicio'] = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = ".$frm['IDServicioMaestro']." AND IDClub = ".$IDClub);
					}
					
					$talonera['IDClub'] = $IDClub;
					$talonera['ValorSocio'] = $precioCl;
					$talonera['IDServicio'] = $frm['IDServicio'];
					
					if($rowTipo['NumSesiones'] == 'S'){
						$idTaloneraPP = $dbo->getFields("ProductoPrecio", "IDTalonera", "IDProductoFacturacion = $id AND IDClub = $IDClub");
						//Si la talonera ya existe se modifica, si no se crea una nueva. 
						if($idTaloneraPP != 0){
							$idTalonera = $dbo->update($talonera, 'Talonera', 'IDTalonera', $idTaloneraPP);
						}else{
							$idTalonera = $dbo->insert($talonera, 'Talonera', 'IDTalonera');
						}
					}
					
					$sqlUp = "UPDATE ProductoPrecio SET Precio = $precioCl, IDTalonera = $idTalonera WHERE IDProductoFacturacion = $id AND IDClub = $IDClub";
					$resUp = $dbo->query($sqlUp);
				}

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
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