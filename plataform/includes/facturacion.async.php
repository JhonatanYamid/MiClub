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
					$where .= " AND ( Consecutivo LIKE LOWER('%" . $search_object->data . "%' ))";
				break;

				case 'Estado':
					$where .= " AND LOWER(IF(Estado = 1,'Pagada', IF(Estado = 2,'Aprobada','Anulada'))) LIKE LOWER('%" . $search_object->data . "%')";
				break;

				case 'Cliente':
					$where .= " AND (LOWER(s.Nombre) LIKE LOWER('%" . $search_object->data . "%') OR LOWER(s.Apellido) LIKE LOWER('%" . $search_object->data . "%')";
				break;
				
				default:
					$where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
				break;
			}
			
		}//end for
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
									t.Beneficiarios, t.FechaActivacion, p.Vigencia, p.TipoVigencia, p.IDServicioMaestro, t.NumSesiones as PermiteSesiones, p.NumSesiones, p.Profesional
							FROM ProductoPrecio as pp
								LEFT JOIN ProductoFacturacion as p ON pp.IDProductoFacturacion = p.IDProductoFacturacion
								LEFT JOIN TipoFacturacion as t ON p.IDTipoFacturacion = t.IDTipoFacturacion
								LEFT JOIN CategoriaFacturacion as c ON p.IDCategoriaFacturacion = c.IDCategoriaFacturacion
								LEFT JOIN Impuestos as i ON p.IDImpuestos = i.IDImpuestos
							WHERE p.Activo = 'S' AND pp.IDClub = $selClub AND (LOWER(p.Nombre) LIKE LOWER('%$qryString%') OR LOWER(p.Codigo) LIKE LOWER('%$qryString%'))";

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
				$sqlClub = "SELECT IDResolucionFactura, IF(IDClupPadre = 157,REPLACE(Nombre, 'SEDE', 'ACTIVE BODYTECH'),Nombre) as Nombre, Direccion, Email, 
									Telefono, Numero, Fecha, ValorInicial, ValorFin, Tipo, Prefijo, IF(ConsecutivoFacturas = 0,ValorInicial,ConsecutivoFacturas) as ConsecutivoFacturas
							FROM Club as c 
								LEFT JOIN ResolucionFactura as r ON c.IDClub = r.IDClub 
							WHERE c.IDClub = $selClub AND r.Activo = 'S'";

				$qryClub = $dbo->query($sqlClub);
				$rClub = $dbo->fetchArray($qryClub);

				echo json_encode($rClub);
				exit;
			break;

			case 'descuentos':
				
				$selClub = SIMNet::req("idClub");
				$val = SIMNet::req("val");
				$opDescuentos = array();
				$arrayDesc = array();
				
				$sqlDescuentos = "SELECT d.IDDescuentos,d.Nombre,d.TipoCalculo,d.EnFactura,d.PermisoAdmin,d.ValorDescuento
								FROM DescuentosClub as dc
									LEFT JOIN Descuentos as d ON dc.IDDescuentos = d.IDDescuentos
								WHERE dc.IDClub = $selClub AND d.Activo = 'S' ORDER BY d.Nombre";

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

				$menu = SIMHTML::formPopupArray($opDescuentos, $val, "Descuentos", SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), $class = "", 'onchange="adminDescuento()"');
				
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
		$hoy = date("Y-m-d"); 
		
		$updFac = "UPDATE Facturacion SET Estado = 3, FechaAnulacion = $hoy, MotivoAnulacion ='$motivo' WHERE IDFacturacion = ".$idFactura;
		$resFac = $dbo->query($updFac);
		
		echo true;
		exit;
	break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		if(!empty($qryString)){
			$where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database
$sqlCount = "SELECT COUNT(*) AS count FROM Facturacion as f 
				LEFT JOIN Club as c ON f.IDClub = c.IDClub 
				LEFT JOIN Socio as s ON f.IDSocio = s.IDSocio ".$where . " ";

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
			IDFacturacion, c.Nombre, CONCAT(Prefijo,Consecutivo) as Consecutivo, FechaCreacion, CONCAT(s.Nombre,' ',s.Apellido) as Cliente, NumeroDocumento, Total, Estado, IF(Estado = 1,'Pagada', IF(Estado = 2,'Aprobada','Anulada')) as nmEstado 
		FROM Facturacion as f 
			LEFT JOIN Club as c ON f.IDClub = c.IDClub 
			LEFT JOIN Socio as s ON f.IDSocio = s.IDSocio ".$where ."ORDER BY $sidx $sord " . $str_limit;
//echo $sql;
//exit;
// var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$color = "orange";
	$anular = '<a href="javaScript:void(0)" class="btnAnular" title="Anular Factura" factura="'.$row[$key].'"><i class="ace-icon fa fa-close bigger-130 red"/></a>';
	
	if($row["Estado"] == 2)
		$color = "green";

	if($row["Estado"] == 3){
		$color = "red";
	//	$anular = "";
	}

	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array( 
			$key => $row[$key],
			"Ver" => '<a class="blue" title="Ver Factura" href="'.$script.'.php?action=see&id='.$row[$key].''.'"><i class="ace-icon fa fa-eye bigger-130"/></a>',
			"Sede" => $row["Nombre"],
			"Consecutivo" => $row["Consecutivo"],
			"FechaCreacion" => $row["FechaCreacion"],
			"Cliente" => $row["Cliente"],
			"NumeroDocumento" => $row["NumeroDocumento"],
			"Total" => $row["Total"],
			"Estado" => "<font color='$color'>".$row["nmEstado"]."</font>",
			"Anular" => $anular
		);

	$i++;
}        

echo json_encode($responce);

?>