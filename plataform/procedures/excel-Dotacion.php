<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	session_start();
//handler de sesion
$simsession = new SIMSession( SESSION_LIMIT );

//traemos lo datos de la session
$datos = $simsession->verificar();

if( !is_object( $datos ))
{
	SIMHTML::jsTopRedirect( "/login.php?msg=NSA" );
	exit;
}//ebd if

//veriificamos el club de la sesion
if( !empty( $_SESSION["club"] ) )
	$datos->club = $_SESSION["club"];
else
	$datos->club = $datos->IDClub;

	//encapsulamos los parammetros
	SIMUser::setFromStructure( $datos );
	$get = SIMUtil::makeSafe( $_GET );
		
	require_once LIBDIR."/APPReport.class.php";

	$reportObj = new APPReport();

// Crear constrain/condiciones para filtrar datos $where
	$condicion = array();

	$condicion[0] = "P.Publicar = 'S'";
	
	if(!empty(SIMUser::get("club")))
	{
		$condicion[1] = "E.IDClub = " . SIMUser::get("club");
	}
	else
	{
		exit;
	}
	if(!empty($get["FechaInicio"]) && !empty($get["FechaFin"])){
		$condicionFecha = " ( ER.FechaTrCr >= '".$get["FechaInicio"]." 00:00:00'  AND ER.FechaTrCr <= '".$get["FechaFin"]." 23:59:59' )";
	}
	if(!empty($get["id"]))
		$condicion[2] = " E.IDDotacion = ".$get["id"];
	
		$headerPL["f1"] = array("Reporte :"=>"Reporte Dotacion");
		$headerPL["f2"] = array("Empresa :"=>"");
		$headerPL["f3"] = array("Fecha Generacion :"=>date( "d m Y H:i" ));
		$headerPL["f4"] = array("Formulario :"=> "");
		$headerPL["f5"] = array(""=> "");
		$headerPL["f6"] = array("No "=> "Pregunta");
		
		
		if(!empty($condicion))
			$strCondicion = " WHERE ". implode(" AND ",$condicion);
	
	
	  	$sql_preguntas = " SELECT C.Nombre AS NombreEmpresa,E.Nombre as Encuesta,P.IDPreguntaDotacion,P.EtiquetaCampo,P.Orden
					FROM Dotacion E
						JOIN PreguntaDotacion P ON P.IDDotacion = E.IDDotacion
						JOIN Club C ON C.IDClub = E.IDClub
					$strCondicion
					ORDER BY P.Orden";
					

		$result = $dbo->query($sql_preguntas);
		$numPregunta = 1;
		$array_preguntas = array();
		while($rowPregunta = $dbo->fetchArray($result)){
			$nombreEmpresa = $rowPregunta["NombreEmpresa"];
			$tituloEncuesta = utf8_decode($rowPregunta["Encuesta"]);
			$headerPL["f".(5+$numPregunta)] = array($rowPregunta["IDPreguntaDotacion"]=> utf8_decode($rowPregunta["EtiquetaCampo"]));
			
			$array_preguntas["_".$rowPregunta["IDPreguntaDotacion"]] = "";
			$array_NoPregunta[$numPregunta] = $rowPregunta["IDPreguntaDotacion"];
			$numPregunta++;
		}
		
		$array_preguntasDefault = $array_preguntas;

		$headerPL["f2"] = array("Empresa :"=>$nombreEmpresa);
		$headerPL["f4"] = array("Formulario :"=> $tituloEncuesta);

		$filename = "Reporte_Dotacion_".date( "Y_m_d" );

		$dirigido = $dbo->getFields("Dotacion","DirigidoA","IDDotacion = ".$get["id"]);		

		if($dirigido == "E")
		{
			$sql_diagnostico = "SELECT U.IDUsuario, U.NumeroDocumento, U.Nombre AS Nombre,
			P.IDPreguntaDotacion, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
			FROM Dotacion E
			JOIN PreguntaDotacion P ON P.IDDotacion = E.IDDotacion
			JOIN DotacionRespuesta ER ON ER.IDPreguntaDotacion = P.IDPreguntaDotacion
			JOIN Usuario U ON ER.IDSocio = U.IDUsuario
			$strCondicion AND $condicionFecha
			ORDER BY ER.IDDotacionRespuesta DESC
			";
		}
		else
		{			
			$sql_diagnostico = "SELECT S.IDSocio, S.NumeroDocumento, CONCAT( S.Nombre, ' ', S.Apellido ) AS Nombre,
			P.IDPreguntaDotacion,ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
			FROM Dotacion E
			JOIN PreguntaDotacion P ON P.IDDotacion = E.IDDotacion
			JOIN DotacionRespuesta ER ON ER.IDPreguntaDotacion = P.IDPreguntaDotacion
			JOIN Socio S ON ER.IDSocio = S.IDSocio
			$strCondicion AND $condicionFecha
			ORDER BY ER.IDDotacionRespuesta DESC
			";
		}

				
		$result = $dbo->query($sql_diagnostico);
	//	$numPregunta = 1;
		$array_preguntas = array();
		$arrayItems = array();
		$cont = 0;
		while($rowItem = $dbo->fetchArray($result)){
			  $keyRow = $rowItem["IDSocio"].$rowItem["FechaTrCr"];
			
			if(!array_key_exists($keyRow,$arrayItems)){
				
				$arrayUsuario =  array(
							'NO_DOCUMENTO'=>$rowItem["NumeroDocumento"],
							'Nombre'=>$rowItem['Nombre'],
							'Fecha'=>$rowItem['FechaTrCr'],);
				  
				$arrayItems[$keyRow] = array_merge( $arrayUsuario,$array_preguntasDefault);
			
			}
		
			$arrayItems[$keyRow]["_".$rowItem["IDPreguntaDotacion"]] = $rowItem["Valor"];
		
		}
		
	//echo count($arrayItems);
	
		$data_export["Respuestas"]  = $arrayItems;
	
	//print_r($data_export);	

	$arrayFiles = $reportObj->exportPHPXLS("Reporte Dotacion", $data_export, $filename.".xls","",TRUE,$headerPL);


	exit;
	
?>
