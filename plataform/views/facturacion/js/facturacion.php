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

				$frm['HoraCreacion'] = date("hh:mm:ss");
				$frm['HoraVence'] = date("hh:mm:ss");
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
								"IDServicio" => $infoProducto['IDServicioMaestro'],
								"IDTipoPago" => 16,
								"Dirigida" => 'S',
								"ValorPagado" => $producto['Precio'],
								"CantidadTotal" => $infoProducto['NumSesiones'],
								"CantidadPendiente" => $infoProducto['NumSesiones'],
								"TipoMonedero" => 0,
								"SaldoMonedero" => 0,
								"TodosLosServicios" => 0
							];					
						}

						$txtIns = "INSERT INTO RegistroSocioProducto(IDFacturacion, IDProductoFacturacion, IDSocio, FechaInicio, FechaFin,PlanSesiones, Sesiones, SesionesDisponibles, Estado) VALUES";
					
						foreach($beneficiarios as $beneficiario){							
							$estado = 3;
							$fechaInicio = $beneficiario['FechaInicio'];
							
							if($fechaInicio && $fechaInicio != ""){
								$fechaActiva = new DateTime($fechaInicio);
								$estado = $hoy >= $fechaActiva ? 1 : 2; 
							}

							$socioProducto = [
								"IDFacturacion" => $id,
								"IDProductoFacturacion" => $idPr,
								"IDSocio" => $beneficiario['IDSocio'],
								"FechaInicio" => $fechaInicio,
								"FechaFin" => $beneficiario['FechaFin'],
								"PlanSesiones" => $beneficiario['PermiteSesiones'],
								"Sesiones" => $beneficiario['NumSesiones'],
								"SesionesDisponibles" => $beneficiario['NumSesiones'],
								"Estado" => $estado,
							];
							$idRegistroSocioProducto = $dbo->insert($socioProducto, 'RegistroSocioProducto', 'IDRegistroSocioProducto');

							$arrHistorial = [
								"IDRegistroSocioProducto" => $idRegistroSocioProducto,
								"IDUsuario" => $IDUsuario,
								"Fecha" => date("Y-m-d"),
								"Evento" => 'Crear'
							];
							$idRegistroSocioProducto = $dbo->insert($arrHistorial, 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria');

							$arrTalonera['IDSocio'] = $beneficiario['IDSocio'];
							$arrTalonera['FechaCompra'] = $fechaInicio;
							$arrTalonera['FechaVencimiento'] = $beneficiario['FechaFin'];

							$idSocioTalonera = $dbo->insert($arrTalonera, 'SocioTalonera', 'IDSocioTalonera');

							$i++;
						}					
					}
				}

				$cantPagos = count($pagos);
				$j = 1;
				$insPagos = "INSERT INTO FacturacionMediosPago(IDFacturacion, IDMediosPago, ValorPagado) VALUES";
			
				foreach($pagos as $pago){							
					
					$insPagos .= " ('$id','".$pago['IDMediosPago']."',".$pago['ValorPagado'].")";

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


		case "see":

		break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		default:
			$view = "views/" . $script . "/form.php?action=add";
			break;
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/form.php?action=add";

?>