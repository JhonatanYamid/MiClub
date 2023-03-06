<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "LaboralCertificado";
$key = "IDLaboralCertificado";
$where = " WHERE " . $table . ".IDClub = " . SIMUser::get("club") ;
$script ="laboralcertificados";

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
			switch ($search_object->field) 
			{
				case 'Usuario':					
				case'UsuarioAutoriza':
					$table.=", Usuario U";
					$where.=" and U.IDUsuario = ".$table.".IDUsuarioAutoriza ";
					$where.=" and U.IDUsuario = ".$table.".IDUsuario ";
					$where .= " AND (  U.Nombre LIKE '%" . $search_object->data . "%' or U.Apellido LIKE '%" . $search_object->data . "%'  )  ";
				break;
				break;

				case'Socio':
					$table.=", Socio S";
					$where.=" and S.IDSocio = ".$table.".IDSocio ";
					$where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
				break;

				case 'Tipo':

					foreach(SIMResources::$tipo_certificado_laboral as $id_tipo => $tipo)
					{
						if($tipo == $search_object->data)
						{
							$where .= " AND (  IDTipoCertificado = " . $id_tipo . ") ";
							break;
						}
						
					}				

				break;

				case 'Estado':

					foreach(SIMResources::$estado_laboral as $id_estado => $estado)
					{
						if($estado == $search_object->data)
						{
							$where .= " AND (  IDEstado = " . $id_estado . ") ";
							break;
						}
						
					}

				break;
				
				case 'Anombrede':
					
					$where .= " AND   AnombreDe = ".$search_object->data."";

				break;

				default:
					 $where .=   $array_buqueda->groupOp . " ".$table."." .$search_object->field . " LIKE '%" . $search_object->data . "%' ";
				break;
			}

		}//end for

	break;	
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx = "IDLaboralCertificado";
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

	$sql = "SELECT " . $table . ".* FROM " . $table . $where ." ORDER BY $sidx $sord LIMIT " . $start . ",".$limit;//exit;
	/* var_dump($row);
	exit; */
	
	$result = $dbo->query( $sql );


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
while($row = $dbo->fetchArray($result)) 
{
	

	$responce->rows[$i]['id'] = $row[$key];
	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	
		//Para el Rancho todos pueden cancelar reserva
		if(SIMUser::get("IDPerfil") <= 1 ):
				$btn_eliminar = '<a class="red eliminar_registro" rel='.$table.' id='.$row[$key].' lang = '.$script.' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
		else:
			$btn_eliminar = '';
		endif;

		if($row["IDSocio"] == 0)
		{
			$columna_resultado = $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" .$row["IDUsuario"] . "'" ) . " " . $dbo->getFields( "Usuario" , "Apellido" , "IDUsuario = '" .$row["IDUsuario"] . "'" );
		}
		else
		{
			$columna_resultado = $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$row["IDSocio"] . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$row["IDSocio"] . "'" );
		}

		foreach(SIMResources::$tipo_certificado_laboral as $id_tipo => $tipo)
		{
			if($id_tipo == $row['IDTipoCertificado'])
			{
				$tipo_mostrar = $tipo;
				break;				
			}
			
		}

		foreach(SIMResources::$estado_laboral as $id_estado => $estado)
		{
			if($id_estado == $row['IDEstado'])
			{
				$estado_mostrar = $estado;	
				break;			
			}
			
		}


	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
										$key => $row[$key],
										"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',										
										"Usuario" => $columna_resultado,
										"UsuarioAutoriza" => $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" .$row["IDUsuarioAutoriza"] . "'" ) . " " . $dbo->getFields( "Usuario" , "Apellido" , "IDUsuario = '" .$row["IDUsuarioAutoriza"] . "'" ),
										"Tipo" => $tipo_mostrar,
										"Estado" =>  $estado_mostrar,
										"Fechas" => $row['Fechas'],
										"Anombrede" => $row['AnombreDe'],
										"Comentario" => $row['Comentario'],
										"ComentarioAprobacion" => $row['ComentarioAprobacion'],
										"FechaAprobacion" => $row['FechaAprobacion'],
										"Eliminar" => $btn_eliminar
									);


	$i++;
}
/* var_dump($responce);
exit; */
echo json_encode($responce);

?>
