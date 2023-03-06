<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$tipoReporte = empty( $_SESSION["TipoRepDiagnostico"] )? exit : $_SESSION["TipoRepDiagnostico"];

(!empty($_POST))?$frm = SIMUtil::makeSafe( $_POST ):$frm = SIMUtil::makeSafe( $_GET );

$columns = array();

$table = "SocioSeguimiento";
$key = "IDSocioSeguimiento";
$where = " WHERE S.IDSocio = '" . $frm["IDSocio"]. "' ";
$script ="";

$oper = SIMNet::req("oper");

switch( $oper )
{
	case "insert" :
		$idClub = SIMUser::get("club");

		if($tipoReporte == "Socio"){
						$sql_insert="INSERT INTO SocioSeguimiento (IDSocioSeguimiento,
									IDSocio,
									IDUsuario,IDEstadoSalud,
									Observacion,Fecha,FechaTrCr
									)
							VALUES ('','".$frm["IDSocio"]."','0',
								'".$frm["IDSS"]."','".$frm["Observacion"]."',CURDATE(),now(),'". SIMUser::get("IDUsuario")."') ";
					
			
					$sql_update = "UPDATE Socio SET IDEstadoSalud = '".$frm["IDSS"]."',
									FechaTrEd = NOW()
								WHERE IDSocio = '".$frm["IDSocio"]."' AND IDClub = '".$idClub."'";
		}
		
		
		if($tipoReporte == "Funcionario"){
						$sql_insert="INSERT INTO SocioSeguimiento (IDSocioSeguimiento,
									IDSocio,
									IDUsuario,IDEstadoSalud,
									Observacion,Fecha,FechaTrCr,UsuarioTrCr
									)
							VALUES ('','0','".$frm["IDSocio"]."',
								'".$frm["IDSS"]."','".$frm["Observacion"]."',CURDATE(),now(),'". SIMUser::get("IDUsuario")."') ";
					
			
					$sql_update = "UPDATE Socio SET IDEstadoSalud = '".$frm["IDSS"]."',
									FechaTrEd = NOW()
								WHERE IDSocio = '".$frm["IDSocio"]."' AND IDClub = '".$idClub."'";
		}
		
	
		$dbo->query($sql_insert);
	
		$dbo->query($sql_update);
		
				// GET TOKEN
				// En el caso de UCatolica Actualizar Acceso Carne para los estados de salud != a normal

		echo json_encode(array("sucess"=>true,"msg"=>"Seguimiento Registrado Correctamente !! "),true);
		exit;
	break;
	case "update" :
		$sql_insert="UPDATE $table SET Observacion '".$frm["Observacion"]."',
						FechaTrCr = now()
				WHERE  IDSocio = '".$frm["IDSocio"]."' AND IDSocioSeguimiento = '".$frm["IDSocioSeguimiento"]."'";
		$dbo->query($sql_insert);
		
		
		echo json_encode(array("sucess"=>"true","msg"=>"Seguimiento Actualziado Correctamente !! "));
		exit;
	break;
	case "del":

		$sql_delete = "DELETE FROM $table WHERE  IDSocio = '".$frm["IDSocio"]."' AND IDSocioSeguimiento = '".$frm["IDSocioSeguimiento"]."'";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );

		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Fecha";
		$_GET['sord'] = "DESC";
		
		echo json_encode(array("sucess"=>"true","msg"=>"Seguimiento Actualziado Correctamente !! "));
		exit;

	break;


}

	$sql = "SELECT S.IDSocioSeguimiento,S.IDEstadoSalud,S.IDSocio,S.Observacion,S.Fecha,ES.Nombre AS Estado
			FROM SocioSeguimiento S
			JOIN EstadoSalud ES ON S.IDEstadoSalud = ES.IDEstadoSalud
		";


		
	$sql .= $where;

	$page = $_GET['page']; // get the requested page
	$limit = $_GET['rows']; // get how many rows we want to have into the grid
	$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
	$sord = $_GET['sord']; // get the direction
	if(!$sidx) $sidx = "Fecha";
	// connect to the database

	//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
	$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");
	$row = $dbo->fetchArray($result);
	$count = $row['count'];
	
	if( $count >0 ) {
		$total_pages = ceil($count/$limit);
	} else {
		$total_pages = 1;
	}
	if ($page > $total_pages) $page=$total_pages;
	$start = $limit*$page - $limit; // do not put $limit*($page - 1)

	if( empty( $limit ) )
		$limit = 1000000;

	$sql .= " ORDER BY S.$sidx $sord $groupby LIMIT " . $start . ",".$limit;

//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

		$responce->rows[$i]['cell'] = array(
						$key => $row[$key],
					//	"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
						"Estado" => $row["Estado"],
						"Fecha" => $row["Fecha"],
						"Observacion" => $row["Observacion"],
						"Usuario" => $row["IDUsuario"],
					);


	$i++;
}

echo json_encode($responce);

?>
