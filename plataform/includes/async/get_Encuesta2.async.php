<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

	$get = SIMUtil::makeSafe( $_GET );

		 $sql_preguntas = " SELECT P.IDPreguntaEncuesta2,P.EtiquetaCampo,P.Orden
					FROM PreguntaEncuesta2 P 
					WHERE P.IDEncuesta2 = ".SIMNet::reqInt("id")."
					AND P.Publicar = 'S'
					ORDER BY P.Orden";
					
			$result = $dbo->query($sql_preguntas);
			$numPregunta = 1;
			$array_preguntas = array();
			$array_NoPregunta = array();
			
			while($rowPregunta = $dbo->fetchArray($result)){
				
				$array_preguntas["_".$rowPregunta["IDPreguntaEncuesta2"]] = "";
				$array_NoPregunta[$rowPregunta["IDPreguntaEncuesta2"]] = $rowPregunta["IDPreguntaEncuesta2"];

			}
			
			$array_preguntasDefault = $array_preguntas;
		
	 $sql = "SELECT S.IDSocio,CONCAT( S.Nombre, ' ', S.Apellido ) AS Nombre,
			P.IDPreguntaEncuesta2,ER.Valor,DATE(ER.FechaTrCr) AS Fecha
			FROM Encuesta2 E
			JOIN PreguntaEncuesta2 P ON P.IDEncuesta2 = E.IDEncuesta2
			JOIN Encuesta2Respuesta ER ON ER.IDPreguntaEncuesta2 = P.IDPreguntaEncuesta2
			JOIN Socio S ON ER.IDSocio = S.IDSocio
			WHERE E.IDClub = ".SIMUser::get("club")."
			AND E.IDEncuesta2 = ".$get["id"]."
			AND P.Publicar = 'S'
			ORDER BY ER.IDEncuesta2Respuesta DESC
		";
		// ORDER BY Fecha,S.IDSocio,P.Orden


	
	$page = $_GET['page']; // get the requested page

	$limit = $_GET['rows']; // get how many rows we want to have into the grid
/*
	$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
	$sord = $_GET['sord']; // get the direction
	
	if(!$sidx) $sidx = "Nombre";

	
	$result = $dbo->query("SELECT COUNT(*) AS count FROM ( $sql )  AS Total ");
	$row = $dbo->fetchArray($result);
	$count = $row['count'];

*/
//$limit = 58;
$sql .= " LIMIT ".($limit * count($array_preguntas));


		$result = $dbo->query($sql);
	//	echo $responce->records;
	//	$numPregunta = 1;
		$array_preguntas = array();
		$arrayItems = array();
		$cont = 0;
		while($rowItem = $dbo->fetchArray($result)){
			  $keyRow = $rowItem["IDSocio"].$rowItem["Fecha"];
			
			if(!array_key_exists($keyRow,$arrayItems)){
				
				  $arrayUsuario =  array(
							'IDSocio'=>$rowItem["IDSocio"],
							'Nombre'=>$rowItem['Nombre'],
							'Fecha'=>$rowItem['Fecha'],
							);
				  
				$arrayItems[$keyRow] = array_merge( $arrayUsuario,$array_preguntasDefault);
				
			}
		
			$arrayItems[$keyRow]["_".$rowItem["IDPreguntaEncuesta2"]] = $rowItem["Valor"];
		
		}
		
$count = count($arrayItems);
//exit;

	if( $count >0 ) {
		$total_pages = ceil($count/$limit);
	} else {
		$total_pages = 0;
	}
	if ($page > $total_pages) $page=$total_pages;
	$start = $limit * $page - $limit; // do not put $limit*($page - 1)

	if( empty( $limit ) )
		$limit = 1000000;

$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;


//while($row = $dbo->fetchArray($result)) {
$i=0;
foreach($arrayItems AS $row){
	$responce->rows[$i]['id'] = $row["IDSocio"];

		//  $class = "a-edit-modal btnAddReg";
		//$attr = "rev=\"reload_grid\"";
		//if( $origen <> "mobile" )
			$responce->rows[$i]['cell'] = $row;
			/*array(
								//"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
								"IDSocio" => $row["IDSocio"],
								"Nombre" => $row["Nombre"],
								"Valor" => $row["Valor"],
								"Nombre" =>  $row["Nombre"] ,
								"Fecha" => $row["Fecha"],
							);*/

	$i++;

}
echo json_encode($responce);
?>
