<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "SocioInvitadoEspecial";
$key = "IDSocioInvitadoEspecial";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script ="socios";

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

					$where .= " AND ( Invitado.Nombre LIKE '%" . $search_object->data . "%' OR Invitado.Apellido LIKE '%" . $search_object->data . "%' OR Invitado.NumeroDocumento LIKE '" . $search_object->data . "%'  )  ";
				break;

				default:
					$where .=  $array_buqueda->groupOp . "  Invitado." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}

		}//end for


	break;

	case "searchurl":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{

			$where .= " AND ( Invitado.Nombre LIKE '%" . $qryString . "%' OR Invitado.Apellido LIKE '%" . $qryString . "%' OR Invitado.NumeroDocumento LIKE '" . $qryString . "%')  ";
		}//end if
	break;
	case "searchurlaccion":
		$qryString = SIMNet::req( "qryString" );
		if( !empty( $qryString ) )
		{

			$where .= " AND (  Accion LIKE '" . $qryString . "' )   ";
		}//end if
	break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "Nombre";
// connect to the database

//$limit = 58;
$fecha_consulta=SIMNet::req("FechaConsulta");
if(empty($fecha_consulta))
	$fecha_consulta=date("Y-m-d");

$sql = "SELECT Invitado.*, Socio.Nombre NombreSocio, Socio.Apellido ApellidoSocio, Socio.Accion AccionSocio   FROM " . $table .", Socio, Invitado " . $where . " and Socio.IDSocio=SocioInvitadoEspecial.IDSocio and Invitado.IDInvitado=SocioInvitadoEspecial.IDInvitado and FechaFin <= '".$fecha_consulta."' ";
//var_dump($sql);
$result = $dbo->query( $sql );

$responce = "";


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$btn_eliminar_3 = string;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {

	$responce->rows[$i]['id'] = $row[$key];

		if( $origen <> "mobile" )
			$responce->rows[$i]['cell'] = array(
											$key => $row[$key],
											"NumeroDocumento" => $row["NumeroDocumento"],
											"Nombre" => utf8_encode( $row["Nombre"] ),
											"Apellido" => utf8_encode($row["Apellido"]),
											"Socio" => "(".utf8_encode($row["NombreSocio"] .  " " .$row["ApellidoSocio"] . " Accion: " . $row["AccionSocio"]).")",
										);

	$i++;



}



echo json_encode($responce);

?>
