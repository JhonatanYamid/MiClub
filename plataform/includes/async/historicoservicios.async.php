<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDUsuario = SIMUser::get("IDUsuario");
$IDClub = SIMUser::get("club");
$idPadre = SIMUtil::IdPadre($IDClub);
$clubHijos = SIMUtil::ObtenerHijosClubPadre($idPadre);
$idSocio = SIMNet::req("idsocio");
$hoy = new DateTime();

//Actualizar socios
//SIMWebService::actualizar_plan_socios($idSocio);

$columns = array();
$origen = SIMNet::req("origen");

$table = "RegistroSocioProducto";
$key = "IDRegistroSocioProducto";
$where = " WHERE r.IDSocio = $idSocio ";
$script = "RegistroSocioProducto";

$nomTime = array(
	1=>"hour",
	2=>"days",
	3=>"month",
);

$nomTiempo = array(
	1=>"Horas",
	2=>"Dias",
	3=>"Meses",
);

$sql = "SELECT 
			r.IDRegistroSocioProducto,r.FechaInicio, r.FechaFin, r.IDProductoFacturacion, r.IDSocio, r.IDSocioTalonera,
			r.IDSocioPermisoReserva, r.Estado, r.PlanSesiones, r.Sesiones, r.SesionesDisponibles, r.IDFacturacion,
			pf.Nombre as Producto, pf.IDServicio, pf.TipoCongelacion,
			IF(c.IDClubPadre = 157,REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH'), c.Nombre) as Sede, 
			s.NumeroDocumento, CONCAT(s.Nombre,' ',s.Apellido) as Socio, s.Accion,
			f.IDClub, f.FechaCreacion, tf.PermitirReservar
		FROM 
			RegistroSocioProducto as r, ProductoFacturacion as pf, Facturacion as f, Socio as s, Club as c, 
			TipoFacturacion as tf
		WHERE 
			r.IDSocio = $idSocio  AND tf.IDCategoriaFacturacion = 1 AND 
			(r.IDProductoFacturacion = pf.IDProductoFacturacion AND r.IDFacturacion = f.IDFacturacion AND 
			r.IDSocio = s.IDSocio AND f.IDClub = c.IDClub AND pf.IDTipoFacturacion = tf.IDTipoFacturacion) ";

$result = $dbo->query($sql);

while($row = $dbo->fetchArray($result)) {

	$hoy = new DateTime();
	$ayer = new DateTime('yesterday');
	$idRegistro = $row['IDRegistroSocioProducto'];
	$idPermiso = $row['IDSocioPermisoReserva'];	
	$idSocioTalonera = $row['IDSocioTalonera'];
	$idServicio = $row['IDServicio'];
	$idestado = $row['Estado'];
	$permiteReservar = $row['PermitirReservar'];

	$fechaInicio = new DateTime($row['FechaInicio']);
	$fechaFin = new DateTime($row['FechaFin']);
	$fechaCreacion = new DateTime($row['FechaCreacion']);

	$unTime = $nomTime[$row['TipoCongelacion']];	
	$unTiempo = $nomTiempo[$row['TipoCongelacion']];

	$arrHistoria = array();
	$permiso = 0;

	$idPermisoClub = $dbo->getFields("SocioClubPermiso", "IDSocioClubPermiso", "IDClub = ".$row['IDClub']." AND IDSocio = ".$idSocio);
	$idPermisoClub = !$idPermisoClub ? 0 : $idPermisoClub;
	
	$arrPermiso = [
		"IDClub" => $row['IDClub'],
		"IDServicio" => $idServicio,
		"IDSocio" => $idSocio,
		"NumeroDocumento" => $row['NumeroDocumento']
	];

	$arrPermisoClub = [
		"IDClub" => $row['IDClub'],
		"IDSocio" => $idSocio
	];

	$arrTalonera = [
		"IDSocio" => $IDSocio,
		"Activo" => '0'
	];	

	$arrEvento = [
		"IDRegistroSocioProducto" => $idRegistro, 
		"IDUsuario" => $IDUsuario, 
		"Fecha" => $hoy->format('Y-m-d')
	];
	$guardarEvento = 0;
	
	$sqlHisto = "SELECT IDRegistroSocioProductoHistoria, Evento, FechaIniciaEvento, diasCongela, IDSocioTransferir, Pendiente, Observacion
				FROM RegistroSocioProductoHistoria 
				WHERE IDRegistroSocioProducto = ".$idRegistro." AND Pendiente = 1";

	$resHisto = $dbo->query($sqlHisto);
	$rowHisto = $dbo->fetchArray($resHisto);

	if($row['FechaInicio'] == 0 && $row['Estado'] < 3){
		$arrEvento['Evento'] = "Sin fecha de activación";
		$arrEvento['Observacion'] = "";

		$idestado = 3;
	}
	else if($fechaInicio > $hoy && $row['Estado'] != 2 && $row['Estado'] != 4 && $row['Estado'] != 5){
		
		$arrEvento['Evento'] = "Activo con fecha futura";
		$arrEvento['Observacion'] = "Estado de proxima activacion";

		$idestado = 2;
	}
	else if($fechaInicio <= $hoy && $fechaFin > $ayer && $row['FechaInicio'] != 0 && $row['Estado'] != 1 && $row['Estado'] != 4){
		$arrEvento['Evento'] = "Activar";
		$arrEvento['Observacion'] = "Inicia contrato en la fecha registrada";

		if($row['Estado'] == 5){
			$arrEvento['Evento'] = "Reactivar";
			$arrEvento['Observacion'] = "Vuelve a estar activo despues de un tiempo en congelación";

		}else if($row['Estado'] == 6){
			$arrEvento['Evento'] = "Reactivar";
			$arrEvento['Observacion'] = "Vueve estar activo despues haber sido finalizado";
		}
		
		$arrTalonera['Activo'] = '1';
		$permiso = 1;
		$idestado = 1;	
	}
	
	if($fechaFin <= $ayer && $row['FechaInicio'] != 0 && $row['FechaFin'] != 0 && $row['Estado'] < 3 ){
		$arrEvento['Evento'] = "Finaliza";
		$arrEvento['Observacion'] = "Finalización tiempo de contrato";

		$idestado = 6;
	}

	if($row['PlanSesiones'] == 'S'){
	
		$cantDisponible = $dbo->getFields("SocioTalonera", "CantidadPendiente", "IDSocioTalonera = ".$idSocioTalonera); 
		$cantActual = $row['SesionesDisponibles'];

		if($cantActual != $cantDisponible){
			$updEv = "UPDATE RegistroSocioProducto SET SesionesDisponibles = $cantDisponible WHERE IDRegistroSocioProducto = ".$idRegistro;
			$resEv = $dbo->query($updEv);
		}
			
		if($cantDisponible <= 0){
			
			$arrEvento['Evento'] = "Finaliza";
			$arrEvento['Observacion'] = "Finalización de sesiones";
			
			$idestado = 6;
		}
	} 
	
	if($row['Estado'] == 3){
		$diffMonth = $fechaCreacion->diff($hoy);

		if($diffMonth->days >= 60){
			$arrEvento['Evento'] = "Finaliza";
			$arrEvento['Observacion'] = "No se ha realizado la activación en ".$diffMonth->days." días";
			
			$idestado = 6;
		}  
	} 

	if(!empty($rowHisto)){

		$fechaIniciaEvento = new DateTime($rowHisto['FechaIniciaEvento']);

		if($rowHisto['Evento'] == "Congelar"){
				
			if($hoy >= $fechaIniciaEvento){
	
				$arrEvento['Evento'] = "Congelado";
				$arrEvento['Observacion'] = "Producto congelado por ".$rowHisto['diasCongela']." $unTiempo";

				$newFechaInicio =  date("Y-m-d",strtotime($row['FechaInicio']."+ ".$rowHisto['diasCongela']." $unTime")); 
				$newFechaFin = date("Y-m-d",strtotime($row['FechaFin']."+ ".$rowHisto['diasCongela']." $unTime")); 

				$arrTalonera['FechaVencimiento'] = $newFechaFin;
				$idestado = 5;

				$dbo->update(array("Pendiente" => '0'), 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria', $rowHisto['IDRegistroSocioProductoHistoria']);
			}
		}
		else if($rowHisto['Evento'] == "Transferir" && ($row['Estado'] >= 1 && $row['Estado'] <= 3)){
			
			$idSocioTransferir = $rowHisto['IDSocioTransferir'];
			
			$sqlSocio = "SELECT Nombre, Apellido, Accion, NumeroDocumento FROM Socio WHERE IDSocio = ".$idSocioTransferir;
			$resSocio = $dbo->query($sqlSocio);
			$rowSocio = $dbo->fetchArray($resSocio);
			$arrTalonera['IDSocio'] = $idSocioTransferir;
		
			if($hoy >= $fechaIniciaEvento){
				
				$arrEvento['Evento'] = "Transferido";
				$arrEvento['Observacion'] = "Servicio transferido a: ".$rowSocio['Nombre']." ". $rowSocio['Apellido'];
				$arrTalonera['Activo'] = '1';

				$idestado = 4;

				$idPermisoNew = 0;
				if($idServicio != 0){
					if($permiteReservar == "S"){
						$idPermisoNew = $dbo->getFields("SocioPermisoReserva", "IDSocioPermisoReserva", "IDSocio = ".$idSocioTransferir." AND IDClub = ".$row['IDClub']." AND IDServicio = ".$idServicio);
						if(!$idPermisoNew){
							$arrPermiso["IDSocio"] = $idSocioTransferir;
							$arrPermiso["NumeroDocumento"] = $rowSocio['NumeroDocumento'];

							$idPermisoNew = $dbo->insert($arrPermiso, 'SocioPermisoReserva', 'IDSocioPermisoReserva');
						}

						$idPermisoClubNew = $dbo->getFields("SocioClubPermiso", "IDSocioClubPermiso", "IDClub = ".$row['IDClub']." AND IDSocio = ".$idSocioTransferir);
						if(!$idPermisoClubNew){
							$arrPermisoClubNew = array(
								"IDClub" => $row['IDClub'], 
								"IDSocio" => $idSocioTransferir
							);

							$idPermisoClubNew = $dbo->insert($arrPermisoClubNew, 'SocioClubPermiso', 'IDSocioClubPermiso');
						}
					}
				}

				$sqlTransf = "SELECT * FROM RegistroSocioProducto WHERE IDRegistroSocioProducto = $idRegistro";
				$resTransf = $dbo->query($sqlTransf);
				$rowTransf = $dbo->fetchArray($resTransf);

				$arrNewReg = [
					"IDFacturacion" => $row['IDFacturacion'],
					"IDProductoFacturacion" => $row['IDProductoFacturacion'],
					"IDSocio" => $idSocioTransferir,
					"IDSocioTalonera" => $idSocioTalonera,
					"IDSocioPermisoReserva" => $idPermisoNew,
					"FechaInicio" => $rowHisto['FechaIniciaEvento'],
					"FechaFin" => $row['FechaFin'],
					"PlanSesiones" => $row['PlanSesiones'],
					"Sesiones" => $row['Sesiones'],
					"SesionesDisponibles" => $row['SesionesDisponibles'],
					"Estado" => 1,
				];
				
				$idRegistroNew = $dbo->insert($arrNewReg, 'RegistroSocioProducto', 'IDRegistroSocioProducto');

				$arrEventos = $arrEvento;
				$arrEventos['IDRegistroSocioProducto'] = $idRegistroNew;
				$arrEventos['Evento'] = 'Creado';
				$arrEventos['FechaIniciaEvento'] = $FechaTransfiere;
				$arrEventos['Observacion'] = "Producto transferido por ".$row['Socio'];

				$arrEventoNew = array(
					'IDRegistroSocioProducto' => $idRegistroNew,
					'Evento' => 'Creado',
					'Observacion' => "Producto transferido por ".$row['Socio'],
					"IDUsuario" => $IDUsuario, 
					"Fecha" => $hoy->format('Y-m-d')
				);

				$dbo->insert($arrEventoNew, 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria');
				$dbo->update(array("Pendiente" => '0'), 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria', $rowHisto['IDRegistroSocioProductoHistoria']);				
			}
		}
	}

	if($row['Estado'] != $idestado){

		if($permiteReservar == "S"){

			if($idSocioTalonera != 0)
				$idSocioTalonera = $dbo->update($arrTalonera, 'SocioTalonera', 'IDSocioTalonera', $idSocioTalonera);
			
			if($permiso == '1'){
				if($idPermiso == 0)
					$idPermiso = $dbo->insert($arrPermiso, 'SocioPermisoReserva', 'IDSocioPermisoReserva');

				if($idPermisoClub == 0)
					$idPermisoClub = $dbo->insert($arrPermisoClub, 'SocioClubPermiso', 'IDSocioClubPermiso');
				
			}else{
				
				if($idPermiso != 0){
					$permisos = $dbo->getFields("RegistroSocioProducto", "COUNT(IDRegistroSocioProducto)", "IDSocioPermisoReserva != ".$idPermiso." AND Estado = 1 AND IDRegistroSocioProducto =".$idRegistro);

					if($permisos == 0){
						$dbo->deleteById('SocioPermisoReserva', 'IDSocioPermisoReserva', $idPermiso);
					}
					
					$idPermiso = 0;
				}

				if($idPermisoClub != 0){
					$permisosClub = $dbo->getFields("SocioPermisoReserva", "COUNT(IDSocioPermisoReserva)", "IDClub = ".$row['IDClub']." AND IDSocio = ".$idSocio);
	
					if($permisosClub == 0){
						$dbo->deleteById('SocioClubPermiso', 'IDSocioClubPermiso', $idPermisoClub);
						$idPermisoClub = 0;
					}
				}
			}
		}

		$arrUpdate = array(
			"Estado" => $idestado,
			"IDSocioPermisoReserva" => $idPermiso
		);

		if($idestado == 5){
			$arrUpdate['FechaInicio'] = $newFechaInicio;
			$arrUpdate['FechaFin'] = $newFechaFin;
		}

		$dbo->update($arrUpdate, 'RegistroSocioProducto', 'IDRegistroSocioProducto', $idRegistro);				
		$dbo->insert($arrEvento, 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria');
	}
}

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'Estado':
					$where .= " AND LOWER(IF(Estado = 1,'Activo', IF(Estado = 2,'Activo(Sin Iniciar)',IF(Estado = 3,'Pendiente Activar',IF(Estado = 4,'Transferido',IF(Estado = 5,'Congelado','Finalizado')))))) LIKE LOWER('%" . $search_object->data . "%')";
				break;

				default:
					$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}
			
		}//end  
	break;

	case "modal":
		$tipo = SIMNet::req("tipo");
		$idRegistro = SIMNet::req("idRegistro");
		
		switch($tipo){
			case "configuracion":
				$idProducto = SIMNet::req("idProducto");

				$op1 = SIMUtil::get_traduccion('', '', 'cambiarfechadeinicio', LANGSESSION);
				$op2 = SIMUtil::get_traduccion('', '', 'cambiarfechafin', LANGSESSION);
				$op3 = SIMUtil::get_traduccion('', '', 'congelaraccesoalservicio', LANGSESSION);
				$op4 = SIMUtil::get_traduccion('', '', 'transferiraccesoalservicio', LANGSESSION);

				$opConf = [1 => $op1, 2 => $op2];

				$sqlTipo = "SELECT tf.IDTIpoFacturacion, tf.Congelaciones, tf.PermitirReservar, pf.NumCongelacion, pf.TimeCongelacion, pf.TipoCongelacion
						    FROM TipoFacturacion as tf, ProductoFacturacion as pf
						    WHERE 
							pf.IDTipoFacturacion = tf.IDTipoFacturacion AND 
							pf.IDProductoFacturacion = $idProducto";

				$resultTipo = $dbo->query($sqlTipo);
				$rowTipo = $dbo->fetchArray($resultTipo);

				$sqlRegistro = "SELECT r.FechaInicio, r.FechaFin, p.Vigencia, p.TipoVigencia,r.Estado
								FROM RegistroSocioProducto as r, ProductoFacturacion as p
								WHERE 
								r.IDProductoFacturacion = p.IDProductoFacturacion AND r.IDRegistroSocioProducto = $idRegistro";

				$resultRegistro = $dbo->query($sqlRegistro);
				$rowRegistro = $dbo->fetchArray($resultRegistro);

				if($rowTipo['Congelaciones'] != 3){
					$valida = 0;

					if($rowTipo['Congelaciones'] == 2){

						$sqlCong = "SELECT COUNT(IDRegistroSocioProductoHistoria) as cuantos 
								    FROM RegistroSocioProductoHistoria 
									WHERE 
									(Evento = 'Congelado' OR Evento = 'Congelar') AND
									IDRegistroSocioProducto = $idRegistro";
						
						$resultCong = $dbo->query($sqlCong);
						$rowCong = $dbo->fetchArray($resultCong);	

						if($rowCong['cuantos'] >= $rowTipo['NumCongelacion'])
							$valida = 1;
					}

					if($rowRegistro['Estado'] == 6)
						$valida = 1;

					if($valida == 0)
						$opConf[3] = $op3;
				}

				if($rowRegistro['Estado'] < 3)
					$opConf[4] = $op4;


				$menu = SIMHTML::formPopupArray($opConf, '', "idConfiguracion", SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', '');
					

				$arrayInfo = [
					'FechaInicio' => $rowRegistro['FechaInicio'],
					'FechaFin' => $rowRegistro['FechaFin'],
					'Vigencia' => $rowRegistro['Vigencia'],
					'TipoVigencia' => $rowRegistro['TipoVigencia'],
					'TimeCongelacion' => $rowTipo['TimeCongelacion'],
					'TipoCongelacion' => $rowTipo['TipoCongelacion'],
				];

				$arrayRes['infoRegistro'] = $arrayInfo;
				$arrayRes['menu'] = $menu;

				echo json_encode($arrayRes);
				exit;
			break;

			case 'socio':
				$qryString = SIMNet::req("qryString");

				if(!empty($clubHijos)){
					$clubes = implode(',',array_values($clubHijos));
					$wrSocio = "IDClub in ($clubes,$idPadre)";
				}else{
					$wrSocio = "IDClub = $IDClub";
				}
				
				$sqlSocio = "SELECT IDSocio,NumeroDocumento,Accion ,Nombre, Apellido
							 FROM Socio 
							 WHERE 
							 	$wrSocio AND (LOWER(Nombre) LIKE LOWER('%$qryString%') OR LOWER(Apellido) LIKE LOWER('%$qryString%') OR 
								LOWER(NumeroDocumento) LIKE LOWER('%$qryString%') OR LOWER(Accion) LIKE LOWER('%$qryString%'))";

				$qrySocio = $dbo->query($sqlSocio);

				while ($rSocio = $dbo->fetchArray($qrySocio)){
                    $arrayRes[] = $rSocio;
                }

				echo json_encode($arrayRes);
				exit;
				
			break;
			case 'guardar':
				$idConf = SIMNet::req("idConf");
				$arrMod = json_decode($_POST['arrMod'],true);

				$arrEv = [
					"IDRegistroSocioProducto" => $idRegistro, 
					"IDUsuario" => $IDUsuario, 
					"Fecha" => $hoy->format('Y-m-d')
				];

				$idSocioTalonera = $dbo->getFields("RegistroSocioProducto", "IDSocioTalonera", "IDRegistroSocioProducto = $idRegistro"); 
				
				switch($idConf){
					case 1:
						$FechaInicioNew = $arrMod['FechaInicioNew'];
						$FechaFinNew = $arrMod['FechaFinNew'];
						$FechaInicioOld = $arrMod['FechaInicio'];
						$observacion = "Cambia la fecha de inicio de ".$arrMod['FechaInicio']." a ".$arrMod['FechaInicioNew'];
						
						if($idSocioTalonera != 0){
							$updTal = "UPDATE SocioTalonera SET FechaCompra = '$FechaInicioNew', FechaVencimiento = '$FechaFinNew' WHERE IDSocioTalonera = $idSocioTalonera";
							$resTal = $dbo->query($updTal);
						}
	
						$estado = $arrMod['FechaInicio'] == 0 ? ",Estado = 2" : "" ;
	
						$updReg = "UPDATE RegistroSocioProducto SET FechaInicio = '$FechaInicioNew', FechaFin = '$FechaFinNew' $estado WHERE IDRegistroSocioProducto = $idRegistro";
						$resReg = $dbo->query($updReg);
	
						$arrEv['Evento'] = 'Cambio de fecha';
						$arrEv['Observacion'] = $observacion;
	
						$idRegistroHistoria = $dbo->insert($arrEv, 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria');
					break;

					case 2:
						$FechaFinNew = $arrMod['FechaFinNew'];
						$FechaFinOld = $arrMod['FechaFin'];
						$observacion = "Cambia la fecha final de $FechaFinOld a $FechaFinNew";
						
						if($idSocioTalonera != 0){
							$updTal = "UPDATE SocioTalonera SET FechaVencimiento = '$FechaFinNew' WHERE IDSocioTalonera = $idSocioTalonera";
							$resTal = $dbo->query($updTal);
						}

						$updReg = "UPDATE RegistroSocioProducto SET FechaFin = '$FechaFinNew' WHERE IDRegistroSocioProducto = $idRegistro";
						$resReg = $dbo->query($updReg);

						$arrEv['Evento'] = 'Cambio de fecha';
						$arrEv['Observacion'] = $observacion;

						$idRegistroHistoria = $dbo->insert($arrEv, 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria');
					break;

					case 3:
						$numCongela = $arrMod['numCongela'];
						$FechaCongela = new DateTime($arrMod['FechaCongela']);
						$observacion = "Crea el evento para congelar el acceso al servicio a partir de: ".$FechaCongela->format('Y-m-d');
						
						$arrEv['Evento'] = 'Congelar';
						$arrEv['FechaIniciaEvento'] = $FechaCongela->format('Y-m-d');
						$arrEv['diasCongela'] = $numCongela;
						$arrEv['Observacion'] = $observacion;
						$arrEv['Pendiente'] = 1;

						$idRegistroHistoria = $dbo->insert($arrEv, 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria');
					break;

					case 4:
						$IDSocioTransfiere = $arrMod['IDSocioTransfiere'];
						$FechaTransfiere = new DateTime($arrMod['FechaTransfiere']);
						
						$arrEv['Evento'] = 'Transferir';
						$arrEv['FechaIniciaEvento'] = $FechaTransfiere->format('Y-m-d');
						$arrEv['Observacion'] = "Transfiere el producto a partir de ".$FechaTransfiere->format('Y-m-d');
						$arrEv['IDSocioTransferir'] = $IDSocioTransfiere;
						$arrEv['Pendiente'] = 1;

						$idRegistroHistoria = $dbo->insert($arrEv, 'RegistroSocioProductoHistoria', 'IDRegistroSocioProductoHistoria');
					break;
				}

				echo true;
				exit;
			break;
		}
	
	break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "FechaCreacion";
// connect to the database
$sqlCount = "SELECT COUNT(*) AS count
			FROM RegistroSocioProducto as r, ProductoFacturacion as pf, Facturacion as f, Club as c, TipoFacturacion as tf
			".$where ." AND tf.IDCategoriaFacturacion = 1 AND (r.IDProductoFacturacion = pf.IDProductoFacturacion AND r.IDFacturacion = f.IDFacturacion 
			AND f.IDClub = c.IDClub AND pf.IDTipoFacturacion = tf.IDTipoFacturacion) ";

$result = $dbo->query($sqlCount);
$row = $dbo->fetchArray($result);
$count = $row['count'];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if( empty( $limit ) )
	$limit = 1000000;


$sql = "SELECT r.IDRegistroSocioProducto, pf.Nombre as Producto, IF(c.IDClubPadre = 157,REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH'), c.Nombre) as Sede, 
				f.FechaCreacion, r.FechaInicio, r.FechaFin , r.IDProductoFacturacion, r.Estado, r.PlanSesiones, r.SesionesDisponibles, r.IDFacturacion
		FROM RegistroSocioProducto as r, ProductoFacturacion as pf, Facturacion as f, Club as c, TipoFacturacion as tf
		$where AND tf.IDCategoriaFacturacion = 1 AND (r.IDProductoFacturacion = pf.IDProductoFacturacion AND r.IDFacturacion = f.IDFacturacion
		AND f.IDClub = c.IDClub AND pf.IDTipoFacturacion = tf.IDTipoFacturacion) 
		ORDER BY $sidx $sord " . $str_limit;

$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;

while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	if($row['Estado'] == 1){
		$color = "green";
		$estado = "Activo";

	}else if($row['Estado'] == 2){
		$color = "#003321";
		$estado = "Activo(Sin Iniciar)";

	}else if($row['Estado'] == 3){
		$color = "orange";
		$estado = "Pendiente de Activación";

	}else if($row['Estado'] == 4){
		$color = "blue";
		$estado = "Transferido";

	}else if($row['Estado'] == 5){
		$color = "#333c87";
		$estado = "Congelado";

	}else{
		$color = "red";
		$estado = "Finalizado";
	}
	
	if($row['PlanSesiones'] == "S"){

		$disponible = $row['SesionesDisponibles']." Sesiones";

		if($row['Estado'] >= 4)
			$disponible = ' 0 Sesiones ';
	}else{
		$fechaI = new DateTime($row["FechaInicio"]." 00:00:00"); 
		$fechaI = $fechaI < $hoy ? new DateTime("yesterday") : $fechaI;
		$fechaF = new DateTime($row["FechaFin"]." 12:59:59");

		$diff = $fechaI->diff($fechaF);

		$disponible = $diff->days . ' Días ';

		if($row['Estado'] == 4 || $row['Estado'] == 6)
			$disponible = ' 0 Días ';
	}

	$botones = '<a class="blue" title="Ver Factura" href="facturacion.php?action=factura&id='.$row['IDFacturacion'].''.'"><i class="ace-icon fa fa-eye bigger-130"/></a>&nbsp';
	
	if($row['Estado'] != 4 && $row['Estado'] != 5){
		$botones .= '<a href="javaScript:void(0)" class="btnConfigurar" title="Configurar" IDProductoFacturacion ="'.$row['IDProductoFacturacion'].'" IDRegistroSocioProducto="'.$row[$key].'" ><i class="ace-icon fa fa-cogs bigger-130"/></a>&nbsp;';
	}
	
	$botones .= '<a href="javaScript:void(0)" class="btnDetalle" title="Detalle de Registro" IDRegistroSocioProducto="'.$row[$key].'" ><i class="ace-icon fa fa-book bigger-130"/></a>&nbsp;';
	
	$responce->rows[$i]['cell'] = array( 
		$key => $row[$key],
		"Producto" => $row["Producto"],
		"Sede" => $row["Sede"],
		"FechaCreacion" => $row["FechaCreacion"],
		"FechaInicio" => $row["FechaInicio"],
		"FechaFin" => $row["FechaFin"],
		"Disponible" => $disponible,
		"Estado" => "<font color='$color'>$estado</font>",
		"Accion" => $botones,
	);

	$i++;
}       

echo json_encode($responce);

?>