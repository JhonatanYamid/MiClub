<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Socio";
$key = "IDSocio";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' and Token <> '' ";
$script ="reportesocios";

$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":

		$sql_delete = "DELETE FROM Socio WHERE IDSocio = '" . $_POST["id"] . "' AND IDClub = '" . SIMUser::get("club") . "'  LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );

		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre ASC";
		$_GET['sord'] = "ASC";


	break;

	case "search":

		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'qryString':

					$where .= " AND ( Socio.Nombre LIKE '%" . $search_object->data . "%' OR Socio.Apellido LIKE '%" . $search_object->data . "%' OR Socio.NumeroDocumento LIKE '%" . $search_object->data . "%' OR Accion LIKE '%" . $search_object->data . "%' )  ";
				break;

				default:
					$where .=  $array_buqueda->groupOp . "  Socio." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}

		}//end for




	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{

			$where .= " AND ( Socio.Nombre LIKE '%" . $qryString . "%' OR Socio.Apellido LIKE '%" . $qryString . "%' OR Socio.NumeroDocumento LIKE '%" . $qryString . "%' OR Accion LIKE '%" . $qryString . "%' )  ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database


// Consulto los socios activos los cuales son los que han guardado alguna preferencia, reserva, etc

	/*
$sql_id_socio_activo = "Select IDSocio From ReservaGeneral Where 1 UNION
			    Select IDSocio From Pqr Where 1 UNION
				Select IDSocio From SocioFavorito Where 1 UNION
				Select IDSocio From SocioFavorito Where 1 UNION
				Select IDSocio From SocioSeccionEvento Where 1 UNION
				Select IDSocio From  SocioSeccion Where 1 UNION
				Select IDSocio From  SocioSeccionGaleria Where 1";



$result_id_socio_activo = $dbo->query( $sql_id_socio_activo );
while($row_id_socio = $dbo->fetchArray($result_id_socio_activo)):
	$array_id_socio_activo[$row_id_socio["IDSocio"]] = $row_id_socio["IDSocio"];
endwhile;

if (count($array_id_socio_activo)>0):
	//$where .= " and IDSocio in(" . implode(",",$array_id_socio_activo).") ";
endif;

*/



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
	$limit = 1000000;



$sql = "SELECT " . $table . ".IDSocio, Nombre, Apellido, Accion, TipoSocio, NumeroDocumento, Email, Foto, Dispositivo,  CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;
//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce = "";


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {

	$responce->rows[$i]['id'] = $row[$key];


	if (!empty($row[Foto])) {
		$foto="<img src='".SOCIO_ROOT.$row[Foto]."' width=55 >";
	}
	else{
		$foto="";
	}

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$key => $row[$key],
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
										"TipoSocio" => utf8_encode($row["TipoSocio"]),
										"Accion" => $row["Accion"],
										"Documento" => $row["NumeroDocumento"],
										"Nombre" => $row["Nombre"],
										"Apellido" => $row["Apellido"],
										"Email" => utf8_encode($row["Email"]),
										"Dispositivo" => $row["Dispositivo"],
										"Foto" => $foto,

									);




	$i++;


}



echo json_encode($responce);

?>
