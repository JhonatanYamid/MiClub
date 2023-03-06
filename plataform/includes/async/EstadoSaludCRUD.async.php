<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();


(!empty($_POST))?$frm = SIMUtil::makeSafe( $_POST ):$frm = SIMUtil::makeSafe( $_GET );

$columns = array();

$table = "EstadoSalud";
$key = "IDEstadoSalud";
$where = " WHERE IDClub = '" . SIMUser::get("club"). "' OR  IDEstadoSalud <= 3 ";
$script ="";

$oper = SIMNet::req("oper");

switch( $oper )
{
	case "insert" :
		 $sql_insert="INSERT INTO $table (IDEstadoSalud,
						IDClub,
						Nombre,Descripcion,UsuarioTrCr,
						FechaTrCr
						)
				VALUES ('','".SIMUser::get("club")."','". $frm["Nombre"]."',
					'".$frm["Descripcion"]."','". SIMUser::get("IDUsuario")."',now()) ";
		
			if($dbo->query($sql_insert))
				echo json_encode(array("sucess"=>true,"msg"=>"Estado Creado Correctamente !! "),true);
			else
				echo json_encode(array("sucess"=>false,"msg"=>"No fue posible crear intente nuevamente !! "),true);

		exit;
	break;
	case "edit" :
		$sql_insert="UPDATE $table SET Nombre ='".$frm["Nombre"]."',Descripcion = '".$frm["Descripcion"]."',
						FechaTrCr = now()
				WHERE IDEstadoSalud > 3 AND IDClub = '".SIMUser::get("club")."' AND IDEstadoSalud = '".$frm["id"]."'";
		$dbo->query($sql_insert);
		
		echo json_encode(array("sucess"=>"true","msg"=>"Estado Actualziado Correctamente !! "));
		exit;
	break;
	case "del":

		$sql_delete = "DELETE FROM $table WHERE  IDClub = '".$frm["IDClub"]."' AND IDEstadoSalud = '".$frm["IDEstadoSalud"]."'";
		//echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );

		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Fecha";
		$_GET['sord'] = "DESC";
		
		echo json_encode(array("sucess"=>"true","msg"=>"Estado Eliminado Correctamente !! "));
		exit;

	break;


}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "IDEstadoSalud";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
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


	$sql = "SELECT * FROM $table ";
		
$sql .= $where." ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;

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

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";

		$responce->rows[$i]['cell'] = array(
						$key => $row[$key],
					//	"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'" onclic><i class="ace-icon fa fa-pencil bigger-130"/></a>',
						"Nombre" => $row["Nombre"],
						"Descripcion" => $row["Descripcion"]
					);


	$i++;
}

echo json_encode($responce);

?>
