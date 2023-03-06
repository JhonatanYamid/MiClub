 <?

	SIMReg::setFromStructure(array(
		"title" => "factura",
		"titleB" => "facturas",
		"table" => "Facturacion",
		"key" => "IDFacturacion",
		"mod" => "Facturacion"
	));

	$script = "facturacion";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	$action = SIMNet::req("action");
	
	$IDUsuario = SIMUser::get("IDUsuario");
	$IDClub = SIMUser::get("club");
	$idPadre = SIMUtil::IdPadre($IDClub);
	$hijos = SIMUtil::ObtenerHijosClubPadre($IDClub);
	$idPerfil = SIMUser::get("IDPerfil");

	$hoy = new DateTime();

	//Verificar permisos
	//SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	//SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

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

				$frm['HoraCreacion'] =  $hoy->format('H:i:s');
				$frm['HoraVence'] = $hoy->format('H:i:s');
				$frm['Estado'] = 1;

				$id = $dbo->insert($frm, $table, $key);

				$sigConsecutivo = intval($frm['Consecutivo']) + 1;
				$updRes = "UPDATE ResolucionFactura SET ConsecutivoFacturas = $sigConsecutivo WHERE IDResolucionFactura = ".$frm['IDResolucionFactura'];
				$resupdR = $dbo->query($updRes);

				$hoy = new DateTime("now");

				$productos = json_decode($frm['productos'],true);
				$pagos = json_decode($frm['Pagos'],true);

				foreach($productos as $producto){
					$producto['IDFacturacion'] = $id;
					$idPr = $dbo->insert($producto, 'FacturacionProducto', 'IDFacturacionProducto');

					if($producto['Beneficiarios']){
						$infoProducto = $producto['objProducto'];
						$beneficiarios = $producto['Beneficiarios']; 
						$cant = count($beneficiarios);
						$i = 1;

						if($infoProducto['PermiteSesiones']=='S'){
							$idTalonera = $dbo->getFields("ProductoPrecio", "IDTalonera", "IDProductoFacturacion = ".$producto['IDProductoFacturacion']." AND IDClub = ".$frm['IDClub']);
							$arrTalonera = [
								"IDClub" => $frm['IDClub'],
								"IDTalonera" => $idTalonera,
								"IDServicio" => $infoProducto['IDServicio'],
								"IDTipoPago" => 16,
								"Dirigida" => 'S',
								"ValorPagado" => $producto['Precio'],
								"CantidadTotal" => $infoProducto['NumSesiones'],
								"CantidadPendiente" => $infoProducto['NumSesiones'],
								"TipoMonedero" => 0,
								"SaldoMonedero" => 0,
								"TodosLosServicios" => 0,
								"Activo" => '0'
							];					
						}

						$txtIns = "INSERT INTO RegistroSocioProducto(IDFacturacion, IDProductoFacturacion, IDSocio, FechaInicio, FechaFin,PlanSesiones, Sesiones, SesionesDisponibles, Estado) VALUES";
					
						foreach($beneficiarios as $beneficiario){			
							$estado = 3;
							$fechaInicio = $beneficiario['FechaInicio'];
							$idSocioTalonera = 0;
							$idPermiso = 0;
							$idPermisoClub = 0;
							
							if($fechaInicio && $fechaInicio != ""){
								$fechaActiva = new DateTime($fechaInicio);
								$estado = $hoy >= $fechaActiva ? 1 : 2; 
							}

							if($infoProducto['PermitirReservar'] == "S"){
								
								if($infoProducto['IDServicio'] != 0){

									if($estado == 1){
										$idPermiso = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "IDSocio = ".$beneficiario['IDSocio']." AND IDClub = ".$frm['IDClub']." AND IDServicio = ".$infoProducto['IDServicio']);
										if(!$idPermiso){
											$arrPermiso = [
												"IDClub" => $frm['IDClub'],
												"IDServicio" => $infoProducto['IDServicio'],
												"IDSocio" => $beneficiario['IDSocio'],
												"NumeroDocumento" => $beneficiario['Documento'],
											];

											$idPermiso = $dbo->insert($arrPermiso, 'SocioPermisoReserva', 'IDSocioPermisoReserva');
										}

										$idPermisoClub = $dbo->getFields("SocioClubPermiso", "IDSocioClubPermiso", "IDClub = ".$frm['IDClub']." AND IDSocio = ".$beneficiario['IDSocio']);
										if(!$idPermisoClub){
											$arrPermisoClub = [
												"IDClub" => $frm['IDClub'],
												"IDSocio" => $beneficiario['IDSocio']
											];

											$idPermisoClub = $dbo->insert($arrPermisoClub, 'SocioClubPermiso', 'IDSocioClubPermiso');
										}
									}

									if($infoProducto['PermiteSesiones'] == 'S'){
										$arrTalonera['IDSocio'] = $beneficiario['IDSocio'];
										$arrTalonera['SociosPosibles'] = $beneficiario['IDSocio']."-".$beneficiario['Nombre'];
										$arrTalonera['FechaCompra'] = $fechaInicio;
										$arrTalonera['FechaVencimiento'] = $beneficiario['FechaFin'];
										
										if($estado == 1){
											$arrTalonera['Activo'] = '1';
										}
										else{
											$arrTalonera['Activo'] = '0';
										}
		
										$idSocioTalonera = $dbo->insert($arrTalonera, 'SocioTalonera', 'IDSocioTalonera');
									}
								}
							}

							$socioProducto = [
								"IDFacturacion" => $id,
								"IDProductoFacturacion" => $producto['IDProductoFacturacion'],
								"IDSocio" => $beneficiario['IDSocio'],
								"FechaInicio" => $fechaInicio,
								"FechaFin" => $beneficiario['FechaFin'],
								"PlanSesiones" => $infoProducto['PermiteSesiones'],
								"Sesiones" => $infoProducto['NumSesiones'],
								"SesionesDisponibles" => $infoProducto['NumSesiones'],
								"Estado" => $estado,
								"IDSocioTalonera" => $idSocioTalonera,
								"IDSocioPermisoReserva" => $idPermiso
							];
							$idRegistroSocioProducto = $dbo->insert($socioProducto, 'RegistroSocioProducto', 'IDRegistroSocioProducto');
							
							if($estado == 1){
								$evento = "Activar";
							}
							else if($estado == 2){
								$evento = "Activo con fecha futura";
							}
							else if($estado == 3){
								$evento = "Sin fecha de activación";
							}

							$arrHistorial = [
								"IDRegistroSocioProducto" => $idRegistroSocioProducto,
								"IDUsuario" => $IDUsuario,
								"Fecha" => date("Y-m-d"),
								"Evento" => $evento,
								"Observacion" => "Registro creado"
							];
							$idRegistroSocioProducto = $dbo->insert($arrHistorial, 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria');				
						}					
					}
				}

				$cantPagos = count($pagos);
				$j = 1;
				$insPagos = "INSERT INTO FacturacionMediosPago(IDFacturacion, IDMediosPago, ValorPagado, Observacion) VALUES";
			
				foreach($pagos as $pago){							
					
					$insPagos .= " ('$id','".$pago['IDMediosPago']."',".$pago['ValorPagado'].",'".$pago['Observacion']."')";

					if($j < $cantPagos){
						$insPagos .= ",";
					}
					$j++;
				}

				$resupdR = $dbo->query($insPagos);

				SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
				SIMHTML::jsRedirect($script . ".php?action=add");
			} else
				exit;

			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;
		
		case "factura":
			$view = "views/" . $script . "/factura.php";
			break;

		default:
			$view = "views/" . $script . "/list.php";
			break;
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/list.php";

?>