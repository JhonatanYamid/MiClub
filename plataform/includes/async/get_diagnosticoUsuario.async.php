<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$tipoReporte = empty( $_SESSION["TipoRepDiagnostico"] )? exit : $_SESSION["TipoRepDiagnostico"];


$frm_get =  SIMUtil::makeSafe( $_GET );

//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$key = "IDDiagnosticoRespuesta";

// Origen de datos SQL 

   $sql="SELECT P.IDPreguntaDiagnostico,P.EtiquetaCampo,DOR.Opcion,DR.IDDiagnosticoRespuesta
		FROM Diagnostico D
		JOIN PreguntaDiagnostico P ON D.IDDiagnostico = P.IDDiagnostico
		JOIN DiagnosticoOpcionesRespuesta DOR ON DOR.IDDiagnosticoPregunta = IDPreguntaDiagnostico
		JOIN DiagnosticoRespuesta DR ON DR.IDDiagnosticoOpcionesRespuesta = DOR.IDDiagnosticoOpcionesRespuesta
        ";


// Crear constrain/condiciones para filtrar datos $where
	$condicion = array();

	if( !empty( $_GET["id"] ) )
	{
		list($idDiagnostico,$idSocio,$fecha) = explode("A",$frm_get["id"]);
		$idDiagnostico = SIMUtil::makeSafe( $idDiagnostico );
		$idSocio = SIMUtil::makeSafe( $idSocio );
		$condicion[] = " D.IDDiagnostico = $idDiagnostico ";
      
      if($tipoReporte == "Socio")
   		$condicion[] = " DR.IDSocio = $idSocio ";
      if($tipoReporte == "Funcionario")
   		$condicion[] = " DR.IDUsuario = $idSocio ";


		$condicion[] = " DATE(DR.FechaTrCr) = '$fecha' ";
				
	}//end if
	
	if(!empty(SIMUser::get("club"))){
		$condicion[] = " D.IDClub = " . SIMUser::get("club");
	}

		
!empty($condicion)? $sql .= " WHERE ". implode(" AND ", $condicion): "";


// $sql .=" GROUP BY SCES.IDCampoEditarSocio ";

$page = $frm_get['page']; // get the requested page
$limit = $frm_get['rows']; // get how many rows we want to have into the grid
$sidx = $frm_get['sidx']; // get index row - i.e. user click to sort
$sord = $frm_get['sord']; // get the direction
if(!$sidx) $sidx = "Orden";
// connect to the database

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


$sql = $sql." ORDER BY P.$sidx $sord $groupby LIMIT " . $start . ",".$limit;

//exit;
//var_dump($sql);
$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;

$i=0;

while($row = $dbo->fetchArray($result)) {
	
	$responce->rows[$i]['id'] = $row[$key];

	if( $origen <> "mobile" )
		$responce->rows[$i]['cell'] = array(
						$key => $row[$key],
							"Pregunta" => $row["EtiquetaCampo"],
							"Respuesta" => $row["Opcion"],
						);

	$i++;
}

echo json_encode($responce);

?>
