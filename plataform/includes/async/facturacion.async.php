<?php
include( "../../procedures/general_async.php" );

SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );

$IDClub = SIMUser::get("club");
$idPadre = SIMUtil::IdPadre($IDClub);
$clubHijos = SIMUtil::ObtenerHijosClubPadre($idPadre);

$columns = array();
$origen = SIMNet::req("origen");

$table = "Facturacion";
$key = "IDFacturacion";
$where = " WHERE f.IDClub = $IDClub ";
$script = "facturacion";

$hoy = date("Y-m-d"); 

if($IDClub == $idPadre && !empty($clubHijos)){
	$where = " WHERE c.IDClubPadre = $IDClub ";
}
	
$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM Facturacion WHERE IDFacturacion = '" . $_POST["id"] . "' LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
		
		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre";
		$_GET['sord'] = "ASC";

	break;
	
	case "search":
		
		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':
					$where .= " AND (LOWER(CONCAT(Prefijo,Consecutivo)) LIKE LOWER('%" . $search_object->data . "%') OR LOWER(s.Nombre) LIKE LOWER('%" . $search_object->data . "%') OR LOWER(s.Apellido) LIKE LOWER('%" . $search_object->data . "%') OR NumeroDocumento LIKE ('%" . $search_object->data . "%'))";
				break;

				case 'FechaInicio':
                    $fechainicio = $search_object->data;

                    if($fechainicio != ""){
                        $where .= " AND FechaCreacion >= '$fechainicio'";
                    }                    
                break;
                
                case 'FechaFin':
                    $fechafin = $search_object->data;

                    if($fechafin != ""){
                        $where .= " AND FechaCreacion <= '$fechafin'";
                    }                    
                break;

				case 'Estado':
					$where .= " AND LOWER(IF(Estado = 1,'Pagada', IF(Estado = 2,'Aprobada','Anulada'))) LIKE LOWER('%" . $search_object->data . "%')";
				break;

				case 'Cliente':
					$where .= " AND (LOWER(s.Nombre) LIKE LOWER('%" . $search_object->data . "%') OR LOWER(s.Apellido) LIKE LOWER('%" . $search_object->data . "%'))";
				break;

				case 'Consecutivo':
					$where .= " AND LOWER(CONCAT(Prefijo,Consecutivo)) LIKE LOWER('%" . $search_object->data . "%')";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}
			
		}//end for
	break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if(!empty($qryString)){
			$where .= " AND (LOWER(CONCAT(Prefijo,Consecutivo)) LIKE LOWER('%" . $qryString . "%') OR LOWER(s.Nombre) LIKE LOWER('%" . $qryString . "%') OR LOWER(s.Apellido) LIKE LOWER('%" . $qryString . "%') OR NumeroDocumento LIKE ('%" . $qryString . "%'))";
		}//end if
	break;

	case "autocomplete":
		$qryString = SIMNet::req("qryString");
        $tipo = SIMNet::req("tipo");

		switch ($tipo) {
			
			case 'cliente':
				
				if(!empty($clubHijos)){
					$clubes = implode(',',array_values($clubHijos));
					$wrSocio = "IDClub in ($clubes,$idPadre)";
				}else{
					$wrSocio = "IDClub = $IDClub";
				}
				
				$sqlSocio = "SELECT IDSocio,NumeroDocumento,Accion ,Nombre, Apellido ,Direccion, Telefono,Celular
							 FROM Socio 
							 WHERE 
							 	$wrSocio AND TIMESTAMPDIFF(YEAR, FechaNacimiento, CURDATE()) > 17 AND 
								(LOWER(Nombre) LIKE LOWER('%$qryString%') OR LOWER(Apellido) LIKE LOWER('%$qryString%') OR 
								LOWER(NumeroDocumento) LIKE LOWER('%$qryString%') OR LOWER(Accion) LIKE LOWER('%$qryString%'))";

				$qrySocio = $dbo->query($sqlSocio);

				while ($rSocio = $dbo->fetchArray($qrySocio)){
                    $arrayRes[] = $rSocio;
                }

				echo json_encode($arrayRes);
				exit;
				
			break;
			
			case 'producto':
				$selClub = SIMNet::req("idClub");

				$sqlProducto = "SELECT pp.IDProductoFacturacion, c.Nombre as Categoria, p.Codigo, t.Nombre as Tipo, p.Nombre, pp.Precio, i.Nombre as Impuesto, i.ValorImpuesto,
										t.IDTipoFacturacion, t.PermitirReservar, t.Beneficiarios, t.FechaActivacion, p.Vigencia, p.TipoVigencia, p.IDServicioMaestro, p.IDServicio, t.NumSesiones as PermiteSesiones, p.NumSesiones, p.Profesional
								FROM ProductoPrecio as pp, ProductoFacturacion as p, TipoFacturacion as t, CategoriaFacturacion as c, Impuestos as i
								WHERE 
									(pp.IDProductoFacturacion = p.IDProductoFacturacion AND p.IDTipoFacturacion = t.IDTipoFacturacion AND p.IDCategoriaFacturacion = c.IDCategoriaFacturacion AND p.IDImpuestos = i.IDImpuestos) AND
									('$hoy' BETWEEN p.FacturacionInicio AND p.FacturacionFin) AND p.Activo = 'S' AND pp.IDClub = $selClub AND 
									(LOWER(p.Nombre) LIKE LOWER('%$qryString%') OR LOWER(p.Codigo) LIKE LOWER('%$qryString%'))";

				$qryProducto = $dbo->query($sqlProducto);

				while ($rProducto = $dbo->fetchArray($qryProducto)){
                    $arrayRes[] = $rProducto;
                }
				
				echo json_encode($arrayRes);
				exit;
				
			break;

			case 'beneficiario':
				$accion = SIMNet::req("accion");
				
				$sqlBenf = "SELECT IDSocio,NumeroDocumento,Accion,Nombre, Apellido
							 FROM Socio 
							 WHERE (AccionPadre = $accion OR Accion = $accion) AND (LOWER(Nombre) LIKE LOWER('%$qryString%') OR LOWER(Apellido) LIKE LOWER('%$qryString%') OR 
								LOWER(NumeroDocumento) LIKE LOWER('%$qryString%') OR LOWER(Accion) LIKE LOWER('%$qryString%'))";

				$qryBenf = $dbo->query($sqlBenf);

				while ($rBenf = $dbo->fetchArray($qryBenf)){
                    $arrayRes[] = $rBenf;
                }

				echo json_encode($arrayRes);
				exit;
				
			break;
		}
	break;

	case "form":
        $proceso = SIMNet::req("proceso");

		switch ($proceso) {
			
			case 'sedes':
				
				$selClub = SIMNet::req("idClub");
				$sqlClub = "SELECT IDResolucionFactura, IF(IDClubPadre = 157,REPLACE(Nombre, 'SEDE', 'ACTIVE BODYTECH'),Nombre) as Nombre, Direccion, Email, 
									Telefono, Numero, Fecha, ValorInicial, ValorFin, Tipo, Prefijo, IF(ConsecutivoFacturas = 0,ValorInicial,ConsecutivoFacturas) as ConsecutivoFacturas
							FROM Club as c, ResolucionFactura as r
							WHERE c.IDClub = r.IDClub AND c.IDClub = $selClub AND r.Activo = 'S'";

				$qryClub = $dbo->query($sqlClub);
				$rClub = $dbo->fetchArray($qryClub);

				$rClub['vendedores'] = SIMHTML::formPopup('VendedorFactura', 'Nombre', 'IDVendedorFactura', 'IDVendedorFactura', '', SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), 'mandatory', "title='".SIMUtil::get_traduccion('', '', 'Vendedor', LANGSESSION)."'", "AND Activo = 'S' AND IDClub = $selClub");

				echo json_encode($rClub);
				exit;
			break;

			case 'descuentos':
				
				$selClub = SIMNet::req("idClub");
				$val = SIMNet::req("val");
				$opDescuentos = array();
				$arrayDesc = array();
				
				$sqlDescuentos = "SELECT d.IDDescuentos,d.Nombre,d.TipoCalculo,d.EnFactura,d.PermisoAdmin,d.ValorDescuento
								FROM DescuentosClub as dc, Descuentos as d
								WHERE dc.IDDescuentos = d.IDDescuentos AND dc.IDClub = $selClub AND d.Activo = 'S' ORDER BY d.Nombre";

				$qryDescuentos = $dbo->query($sqlDescuentos);

				while ($rDescuentos = $dbo->fetchArray($qryDescuentos)){
					$idDescuento = $rDescuentos['IDDescuentos'];
					$nombreDescuento = $rDescuentos['Nombre'];

					if($rDescuentos['EnFactura'] != 'S'){
						$valDscnto = number_format($rDescuentos['ValorDescuento'], 0, ',', '.');
						$pr = $rDescuentos['TipoCalculo'] == 1 ? "%" : "";
						
						$nombreDescuento .= " ($valDscnto $pr)";
					}

					$arrayDesc[$idDescuento] = $rDescuentos;
					$opDescuentos[$idDescuento] = $nombreDescuento;
				}

				$menu = SIMHTML::formPopupArray($opDescuentos, $val, "Descuentos", SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION),'', 'onchange="adminDescuento()"');
				
				$arrayRes['menu'] = $menu;
				$arrayRes['descuentos'] = $arrayDesc;

				echo json_encode($arrayRes);
				exit;
			break;

			case 'usuario':
				$usuario = SIMUtil::antiinjection($_POST["usuario"]);
				$clave = SIMUtil::antiinjection($_POST["clave"]);
				$selClub = SIMNet::req("idClub");
				$res = 3;

				$infoUser = $dbo->fetchAll( "Usuario" , "User = '".$usuario."' AND Password = '".sha1($clave)."' AND Autorizado = 'S' "  , "array");
				
				if($infoUser){
					$res = $infoUser['IDPerfil'] == 1 ? 1 : 2 ;
				}

				echo $res;
				exit;
			break;
		}
	break;
	
	case "anular":
		$idFactura = SIMNet::req("idFactura");
		$motivo = SIMNet::req("motivo");

		$idClub = $dbo->getFields("Facturacion", "IDClub", "IDFacturacion = $idFactura");
		
		$sqlRegSocio = "SELECT IDRegistroSocioProducto, IDSocioTalonera, IDSocioPermisoReserva, IDSocio, IDProductoFacturacion, Estado FROM RegistroSocioProducto WHERE IDFacturacion = $idFactura";

		$qryRegSocio = $dbo->query($sqlRegSocio);

		while ($rRegSocio = $dbo->fetchArray($qryRegSocio)){
			
			$idRegistro = $rRegSocio['IDRegistroSocioProducto'];
			$idSocio = $rRegSocio['IDSocio'];
			$idPermiso = $rRegSocio['IDSocioPermisoReserva'];
			$idSocioTalonera = $rRegSocio['IDSocioTalonera'];
			$idProductoFacturacion = $rRegSocio['IDProductoFacturacion'];

			//valida si el registro tienen un permiso asociado, y verifica que ese permiso no este asociado a otros registros
			if($idPermiso != 0){
				$permisos = $dbo->getFields("RegistroSocioProducto", "COUNT(IDRegistroSocioProducto)", "IDSocioPermisoReserva = ".$idPermiso." AND Estado = 1 AND IDRegistroSocioProducto !=".$idRegistro);
				
				if($idSocioTalonera > 0){
					$consumos = $dbo->getFields("ConsumoSocioTalonera", "COUNT(IDConsumoSocioTalonera", "IDSocioTalonera = ".$idSocioTalonera." AND FechaConsumo <= NOW()");

					if($consumos > 0){
						echo "Error!, No es posible anular la factura, ya se han consumido reservas de esta.";
						exit;
					}else{
						$sqlDelReservas = "DELETE FROM ReservaGeneral WHERE IDReservaGeneral IN (SELECT IDReservaGeneral FROM ConsumoSocioTalonera WHERE IDSocioTalonera = $idSocioTalonera)";
						$qryDelReservas= $dbo->query($sqlDelReservas);

						$dbo->deleteById('SocioTalonera', 'IDSocioTalonera', $idSocioTalonera);
					}
				}					

				if($permisos == 0){
					$dbo->deleteById('SocioPermisoReserva', 'IDSocioPermisoReserva', $idPermiso);

					$idServicio = $dbo->getFields("ProductoFacturacion", "IDServicio", "IDProductoFacturacion = $idProductoFacturacion");

					$reservasPasadas = $dbo->getFields("ReservaGeneral", "COUNT(IDReservaGeneral)", "CONCAT(FECHA,' ',HORA) < NOW() AND IDServicio = $idServicio AND IDClub = $idClub AND IDSocio = $idSocio");

					if($reservasPasadas > 0){
						echo "Error!, No es posible anular la factura, ya se han consumido reservas de esta.";
						exit;
					}

					$sqlDelReservas = "DELETE FROM ReservaGeneral WHERE CONCAT(FECHA,' ',HORA) > NOW() AND IDServicio = $idServicio AND IDClub = $idClub AND IDSocio = $idSocio";
					$qryDelReservas= $dbo->query($sqlDelReservas);
				}

				//verifica si existen permisos para el club
				$idPermisoClub = $dbo->getFields("SocioClubPermiso", "IDSocioClubPermiso", "IDClub = $idClub AND IDSocio = $idSocio");
				$idPermisoClub = !$idPermisoClub ? 0 : $idPermisoClub;
				
				if($idPermisoClub != 0){
				$permisosClub = $dbo->getFields("SocioPermisoReserva", "COUNT(IDSocioPermisoReserva)", "IDClub = $idClub AND IDSocio = $idSocio");

					if($permisosClub == 0){
						$dbo->deleteById('SocioClubPermiso', 'IDSocioClubPermiso', $idPermisoClub);
					}
				}	
			}
			$dbo->deleteById('RegistroSocioProducto', 'IDRegistroSocioProducto', $idRegistro);
		}
		
		$updFac = "UPDATE Facturacion SET Estado = 3, FechaAnulacion = '$hoy', MotivoAnulacion ='$motivo' WHERE IDFacturacion = ".$idFactura;
		$resFac = $dbo->query($updFac);
		
		echo true;
		exit;
	break;

	case "guardarRespuesta":
		$respuesta = SIMNet::req("respuesta");
		$idFactura = SIMNet::req("idFactura");
		$arrRes = json_decode(stripslashes($respuesta), true );
		$arrUpdate = array();

		if($arrRes['status'] == 2){
			$fechaAutoriza = DateTime::createFromFormat('d/m/Y H:i:s',$arrRes["fechaExpedicion"]);

			$arrUpdate['Estado'] = 2;
			$arrUpdate['MensajeEstupendo'] = $arrRes["message"];
			$arrUpdate['FacturaPDF'] = $arrRes["pdf"] ;
			$arrUpdate['CodigoQR'] = $arrRes["qrcode"];
			$arrUpdate['FechaAutoriza'] = $fechaAutoriza->format('Y-m-d');

		}else{

			$arrUpdate['Estado'] = 4;
			$arrUpdate['MensajeEstupendo'] = $arrRes["message"];
			if($arrRes['msn'] == '__KO__'){
				$arrUpdate['DetalleEstado'] = $arrRes["message"];
			}else{
				$arrUpdate['DetalleEstado'] = $arrRes["error_detail"];
			}			
		}

		$dbo->update($arrUpdate, 'Facturacion', 'IDFacturacion', $idFactura);

		echo true;
		exit;
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database
$sqlCount = "SELECT COUNT(*) AS count 
			FROM Facturacion as f, Club as c, Socio as s
			".$where . " AND (f.IDClub = c.IDClub AND f.IDSocio = s.IDSocio) ";

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


$sql = "SELECT 
			IDFacturacion, f.IDClub, IF(IDClubPadre = 157,REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH'), c.Nombre) as Nombre, CONCAT('SETT',Consecutivo) as Consecutivo, FechaCreacion, HoraCreacion, 
			CONCAT(s.Nombre,' ',s.Apellido) as Cliente, NumeroDocumento, Total, Estado, IF(Estado = 1,'Pagada', IF(Estado = 2,CONCAT('Autorizada(',FechaAutoriza,')'),IF(Estado = 3,'Anulada','Rechazada'))) as nmEstado, 
			MotivoAnulacion, TxtEstupendo, FacturaPDF, DetalleEstado, f.CodigoQR 
		FROM Facturacion as f, Club as c, Socio as s
		".$where ." AND (f.IDClub = c.IDClub AND f.IDSocio = s.IDSocio) ORDER BY $sidx $sord " . $str_limit;
// echo $sql;
// exit;
// var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;

while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$nit = $dbo->getFields("InformacionFactura", "Nit", "IDClub = ".$row['IDClub']." AND Activo = 'S'");
	$arrNit = explode("-", $nit);

	$verFactura = '<a class="blue" title="Ver Factura" href="'.$script.'.php?action=factura&id='.$row[$key].''.'"><i class="ace-icon fa fa-eye bigger-130"/></a>&nbsp';
	$anular = "";
	$estupendo = "";
	$verTxt = "";
	$verDetalle = "";
	$aPDF = "";
	$aQR = "";

	if($row['TxtEstupendo'] != "")
		$verTxt = '<a class="blue" title="Ver archivo plano" href="'.ESTUPENDO_ROOT.$row['TxtEstupendo'].'" download="'.$row['TxtEstupendo'].'"><i class="ace-icon fa fa-file-text-o bigger-130"/></a>&nbsp';
	
	if($row["Estado"] == 1){
		$color = "blue";

		$anular = '<a href="javaScript:void(0)" class="btnAnular" title="Anular Factura" factura="'.$row[$key].'"><i class="ace-icon fa fa-close bigger-130 red"/></a>&nbsp';
		$estupendo = '<a href="javaScript:void(0)" class="btnCargarEstupendo" title="Cargar factura a Estupendo" factura="'.$row[$key].'" nit="'.$arrNit[0].'" consecutivo="'.$row['Consecutivo'].'"><i class="ace-icon fa fa-upload bigger-130 green"/></a>&nbsp';
	
	}else if($row["Estado"] == 2){
		$color = "green";
		// fa-qrcode
		if($row['FacturaPDF'] != "")
			$aPDF = '<a class="blue" title="Ver factura pdf" href="'.$row['FacturaPDF'].'" target="_blank" download="'.$row['Consecutivo'].'.pdf"><i class="ace-icon fa fa-file-pdf-o bigger-130"/></a>&nbsp';
		
		if($row['CodigoQR'] != "")
			$aQR = '<a class="blue" title="Ver factura electronica" href="'.$row['CodigoQR'].'" target="_blank"><i class="ace-icon fa fa-file-text bigger-130"/></a>&nbsp';

	}else if($row["Estado"] == 3){

		$color = "red";
		$verDetalle = '<a href="javaScript:void(0)" class="btnDetalle" title="Ver Detalle" titulo="Motivo de anulación" texto="'.$row['MotivoAnulacion'].'"><i class="ace-icon fa fa-question bigger-130 orange"/></a>&nbsp';

	}else if($row["Estado"] == 4){
		$mnsError = $row['DetalleEstado'] == '' ? $row['MensajeEstupendo']: $row['DetalleEstado'];
		$color = "red";
	 	$verDetalle = '<a href="javaScript:void(0)" class="btnDetalle" title="Ver Detalle" titulo="Motivo de Rechazo" texto="'.$row['DetalleEstado'].'"><i class="ace-icon fa fa-question bigger-130 orange"/></a>&nbsp';
	}

	$botones = $verFactura.$anular.$estupendo.$verTxt.$aPDF.$aQR.$verDetalle;

	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
			$key => $row[$key],
			"Sede" => $row["Nombre"],
			"Consecutivo" => $row["Consecutivo"],
			"FechaCreacion" => $row["FechaCreacion"],
			"HoraCreacion" => $row["HoraCreacion"],
			"Cliente" => $row["Cliente"],
			"NumeroDocumento" => $row["NumeroDocumento"],
			"Total" => $row["Total"],
			"Estado" => "<font color='$color'>".$row["nmEstado"]."</font>",
			"Accion" => $botones
		);

	$i++;
}        

echo json_encode($responce);

?>