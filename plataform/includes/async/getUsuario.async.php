<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

 $tipoReporte = empty( $_SESSION["TipoRepDiagnostico"] )? exit : $_SESSION["TipoRepDiagnostico"];

	$get = SIMUtil::makeSafe( $_GET );

		$condicion = array();
		
	$sql = "SELECT S.IDSocio,TRIM(CONCAT(S.Nombre,' ',S.Apellido)) AS Nombre,S.Celular,S.NumeroDocumento,S.Email,ES.Nombre AS EstadoSalud
					FROM Socio S
					INNER JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
				";
		
	if($tipoReporte == "Funcionario"){
			$sql = "SELECT S.IDUsuario AS IDSocio,TRIM(S.Nombre) AS Nombre,S.Telefono,S.NumeroDocumento,S.Email
							FROM Usuario S 
							";
			
 $condicion[] = "S.Activo = 'S' AND S.Autorizado = 'S' ";
		
			if(!empty($get["id"]))
				$condicion[] = "DR.IDUsuario = '".$get["id"]."'";
}



	
	if(!empty(SIMUser::get("club")))
		$condicion[] = " S.IDClub = " . SIMUser::get("club");

	if(!empty($get["IDES"]))
		$condicion[] = "S.IDEstadoSalud = '".$get["IDES"]."'";
	
	//$qryString = SIMNet::req( "qryString" );
	$get["qryString"] = trim($get["qryString"]);
	
	if( !empty( $get["qryString"] ) ){
		//$condicion[] = " ( S.Nombre LIKE '%" . $get["qryString"] . "%' OR S.Apellido LIKE '%" . $get["qryString"] . "%' )";
		
		if($tipoReporte == "Socio")
			$condicion[] = " ( ".SIMUtil::makeboolean("S.Nombre",$get["qryString"])." OR ".SIMUtil::makeboolean("S.Apellido",$get["qryString"])." ) ";
		if($tipoReporte == "Funcionario")	
			$condicion[] = " ( ".SIMUtil::makeboolean("S.Nombre",$get["qryString"])." ) ";

		
	}//end if

	!empty($condicion)? $sql .= " WHERE ". implode(" AND ", $condicion): "";

	$page = $_GET['page']; // get the requested page
	$limit = $_GET['rows']; // get how many rows we want to have into the grid
	$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
	$sord = $_GET['sord']; // get the direction
	
	if(!$sidx) $sidx = "Nombre";

	
	$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");
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


//$limit = 58;
$sql .= " ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;

$result = $dbo->query( $sql );

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;
$i=0;
$btn_eliminar_3 = string;


while($row = $dbo->fetchArray($result)) {


	$responce->rows[$i]['id'] = $row["IDSocio"];

		$btn_eliminar_3 = "<a class='addEstado' rel='grid-tableEdo_".$row["IDSocio"]."_t' href='#'><i class='ace-icon fa fa-trash-o bigger-130'/></a>";
		

		$class = "a-edit-modal btnAddReg";
		$attr = "rev=\"reload_grid\"";
		if( $origen <> "mobile" )
			$responce->rows[$i]['cell'] = array(
											//"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
											"IDSocio" => $row["IDSocio"],
											"NumeroDocumento" => $row["NumeroDocumento"],
											"EstadoSalud" => $row["EstadoSalud"],
											"Nombre" =>  $row["Nombre"] ,
											"Email" => $row["Email"],
											"BOTON" => $btn_eliminar_3,
										);

	$i++;



}
echo json_encode($responce);
?>
