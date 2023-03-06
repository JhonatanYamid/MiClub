<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "ReservaGeneral";
$key = "IDReservaGeneral";
$where = " WHERE RG.IDClub = '" . SIMUser::get("club") . "'";
$script ="reportereservas";

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
				case 'qryString':
					$where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' OR Apellido LIKE '%" . $search_object->data . "%')";
				break;

				default:
					if($search_object->field=="Servicio"):
						$id_servicio_maestro = $dbo->getFields( "ServicioMaestro" , "IDServicioMaestro" , "Nombre LIKE '%".$search_object->data."%'");
						$id_servicio = $dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro = '" . $id_servicio_maestro . "' and IDClub= '".SIMUser::get("club")."'" );
						$where .=  $array_buqueda->groupOp . " IDServicio = '" . $id_servicio . "' ";
					else:
						if($search_object->field=="UsuarioTrCr"):
							$identificador_tabla = "ReservaGeneral.";
						endif;

						$where .=  $array_buqueda->groupOp . "  " . $identificador_tabla . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
					endif;

				break;
			}

		}//end for
	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );

		if( !empty( $qryString ) )
		{
			$where .= " AND ( Nombre LIKE '%" . $qryString . "%' OR Apellido LIKE '%" . $qryString . "%' )";
		}//end if
	break;
}



if(!empty(SIMNet::req("NumeroDocumento")))
	$where .= " AND ( S.NumeroDocumento = " . SIMNet::req("NumeroDocumento") . "  )";

if(!empty(SIMNet::req("Accion")))
	$where .= " AND ( S.Accion = " . SIMNet::req("Accion") . "  )";

$where .= " AND (RG.Fecha >= '".SIMNet::req("FechaInicio")."' AND RG.Fecha <= '".SIMNet::req("FechaFin")."')";


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database


$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
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
	$limit = 10000;


$where .= " AND (RG.IDSocio = S.IDSocio OR RG.IDSocioBeneficiario = S.IDSocio )";
$sql = "SELECT RG.IDReservaGeneral, RG.IDServicio, RG.IDClub, RG.IDServicioElemento, RG.UsuarioTrCr, RG.Fecha, RG.Hora, RG.IDUsuarioReserva, S.IDSocio, S.Nombre, S.Apellido, S.Accion, S.TipoSocio, CONCAT( S.Nombre, ' ', S.Apellido ) AS Socio
		FROM " . $table . " RG, Socio S " . $where . " 
		UNION
SELECT RG.IDReservaGeneral, RG.IDServicio, RG.IDClub, RG.IDServicioElemento, RG.UsuarioTrCr, RG.Fecha, RG.Hora, RG.IDUsuarioReserva,  S.IDSocio, S.Nombre, S.Apellido, S.Accion, S.TipoSocio, CONCAT( S.Nombre, ' ', S.Apellido ) AS Socio
FROM ReservaGeneralBck RG, Socio S " . $where . "
ORDER BY IDReservaGeneral DESC LIMIT " . $limit;

// echo $sql;

$result = $dbo->query( $sql );

$responce = "";
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');

$sql_servicio="SELECT IDServicio,IDServicioMaestro FROM Servicio Where IDClub = ".SIMUser::get("club");
$r_servicio=$dbo->query($sql_servicio);
while($row_servicio=$dbo->fetchArray($r_servicio)){
	$array_servicio[$row_servicio["IDServicio"]]=$row_servicio;
}

$sql_servicio_m="SELECT IDServicioMaestro FROM ServicioMaestro Where 1";
$r_servicio_m=$dbo->query($sql_servicio_m);
while($row_servicio_m=$dbo->fetchArray($r_servicio_m)){
	$array_servicio_m[$row_servicio_m["IDServicioMaestro"]]=$row_servicio_m["IDServicioMaestro"];
}


$sql_servicio_c="SELECT * FROM ServicioClub  Where IDClub = ".SIMUser::get("club");
$r_servicio_c=$dbo->query($sql_servicio_c);
while($row_servicio_c=$dbo->fetchArray($r_servicio_c)){
	$array_servicio_c[$row_servicio_c["IDClub"]][$row_servicio_c["IDServicioMaestro"]] = $row_servicio_c["TituloServicio"];
}

while($row = $dbo->fetchArray($result)) {

	$responce->rows[$i]['id'] = $row[$key];

	$id_servicio_maestro = $array_servicio[$row["IDServicio"]]["IDServicioMaestro"];
	$nombre_servicio_maestro=$array_servicio_c[$row["IDClub"]][$id_servicio_maestro];

	$Elemento = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $row["IDServicioElemento"] . "'" );

			if($row["UsuarioTrCr"]=="Starter" || $row["UsuarioTrCr"]=="Empleado"):
				$creada_por =  "Starter " . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" . $row["IDUsuarioReserva"] . "'" );
			else:
				$creada_por =  $row["UsuarioTrCr"];
			endif;


	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$key => $row[$key],
										"Servicio" => $nombre_servicio_maestro . "(".$Elemento.")",
										"Nombre" => utf8_encode($row["Nombre"]),
										"Apellido" => utf8_encode($row["Apellido"]),
										"Fecha" => $row["Fecha"] . " " . $row["Hora"],
										"Creada Por" => $creada_por


									);

	$i++;


}



echo json_encode($responce);

?>
