<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Invitado";
$key = "IDInvitado";
$where = " WHERE 1 ";

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
				case 'Socio':

					$where .= " AND ( Invitado.Nombre LIKE '%" . $search_object->data . "%' OR Invitado.Apellido LIKE '%" . $search_object->data . "%' OR Invitado.NumeroDocumento LIKE '%" . $search_object->data . "%' OR Accion LIKE '" . $search_object->data . "%' )  ";
				break;

				default:
					$where .=  $array_buqueda->groupOp . "  Invitado." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}

		}//end for




	break;

	case "searchurl":
			$where .= " AND ( Invitado.NumeroDocumento like '%" . $_GET["qryString"] . "%' )  ";
	break;

	default:
		$where .= " AND FechaIngreso = CURDATE()  ";
	break;


}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = 'FechaIngreso'; // get index row - i.e. user click to sort
$sord = "DESC"; // get the direction
if(!$sidx) $sidx = "FechaIngreso";
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
	$limit = 1000000;



//$sql = "SELECT " . $table . ".*, CONCAT( Invitado.Nombre, ' ', Invitado.Apellido ) AS Socio FROM " . $table . " , Socio " . $where . " AND Invitado.IDClub = '" . SIMUser::get("club")  . "' AND Invitado.IDSocio = Invitado.IDSocio ORDER BY $key $sord " . $str_limit;
$sql = "SELECT " . $table . ".* FROM " . $table . " " . $where . " Group by NumeroDocumento ORDER BY $key $sord " . $str_limit;
//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = (int)$total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {


		if($row["IDClub"]==9){
			$Correo="";
		}
		else{
			$Correo=$row["Email"];
		}

	$responce->rows[$i]['id'] = $row[$key];

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if( $origen <> "mobile" ):


		$responce->rows[$i]['cell'] = array(
										"IDInvitado" => $row["IDInvitado"],
										"NumeroDocumento" => "" .$row["NumeroDocumento"],
										"NombreSugerido" => "" .addslashes( utf8_encode($row["Nombre"]) ) . " " .addslashes( utf8_encode($row["Apellido"]) ),
										"Nombre" => "" .addslashes( utf8_encode($row["Nombre"]) ),
										"Apellido" => "" .addslashes( utf8_encode($row["Apellido"]) ),
										"Telefono" => "" .addslashes( utf8_encode($row["Telefono"]) ),
										"Email" => "" .addslashes( utf8_encode($Correo) ),
										"FechaNacimiento" => "" .addslashes( $row["FechaNacimiento"] ),
										"Observaciones" => "" .addslashes( $row["ObservacionGeneral"] ),
										"TipoSangre" => "" .addslashes( $row["TipoSangre"] )
									);

	endif;

	$i++;
}

echo json_encode($responce);

?>
