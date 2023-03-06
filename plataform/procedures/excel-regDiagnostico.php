<?php

	// require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	session_start();
	//handler de sesion
	$simsession = new SIMSession( SESSION_LIMIT );

	$tipoReporte = empty( $_SESSION["TipoRepDiagnostico"] )? exit : $_SESSION["TipoRepDiagnostico"];

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

	if(empty(SIMUser::get("club")))
		exit;

	$get = SIMUtil::makeSafe( $_GET );
	$post = SIMUtil::makeSafe( $_POST );

	require_once LIBDIR."/APPReport.class.php";

	$reportObj = new APPReport();

	$condicion_fecha = "";

	if(!empty($post["FechaInicio"]) && !empty($post["FechaFin"])){
		//$condicion_fecha=" and DATE(DR.FechaTrCr) >= '".$_POST["FechaInicio"]." 00:00:00'
		//		AND DATE(DR.FechaTrCr) <= '".$_POST["FechaFin"]." 23:59:59'";
		$condicion_fecha=" and DATE(DR.FechaTrCr) >= '".$post["FechaInicio"]."'
				AND DATE(DR.FechaTrCr) <= '".$post["FechaFin"]."'";
	}

	$condicion = array();

	if($tipoReporte == "Socio")
		$condicion[] = " S.IDEstadoSocio = 1 ";

	if(!empty($get["week"])){
		$condicion[] = " DATE(DR.FechaTrCr) BETWEEN CURDATE()-INTERVAL ".$get["week"]." WEEK AND CURDATE()";
	}else
		$condicion[] = " DATE(DR.FechaTrCr) BETWEEN CURDATE()-INTERVAL 2 WEEK AND CURDATE()";

	if(!empty($get["DIA"]))
		$condicion[] = "DATE(DR.FechaTrCr) = '".$get["DIA"]."'";

	if(!empty($get["IDES"]))
		$condicion[] = "S.IDEstadoSalud = '".$get["IDES"]."'";

	if( !empty( $get["qryString"] ) ){
		//$condicion[] = " ( S.Nombre LIKE '%" . $get["qryString"] . "%' OR S.Apellido LIKE '%" . $get["qryString"] . "%' )";

		if($tipoReporte == "Socio")
			$condicion[] = " ( ".SIMUtil::makeboolean("S.Nombre",$get["qryString"])." OR ".SIMUtil::makeboolean("S.Apellido",$get["qryString"])." ) ";
		if($tipoReporte == "Funcionario")
			$condicion[] = " ( ".SIMUtil::makeboolean("S.Nombre",$get["qryString"])." ) ";

	}//end if

	!empty($condicion)? $condicionStr = " AND ".implode(" AND ",$condicion): "";


	if($tipoReporte == "Socio")
		$array_columnas = array("S.NumeroDocumento"=> "",
							"TRIM(CONCAT(S.Nombre,' ',S.Apellido)) AS Nombre"=>"",
							"ES.Nombre AS EstadoSalud"=>"",
							"S.Email"=> "",
							"S.Celular"=> "",
							"S.Direccion"=> "",
							"S.NumeroDocumento"=> "",
							"S.Email"=> ""
						);

	if($tipoReporte == "Funcionario")
			$array_columnas = array("S.NumeroDocumento"=> "",
								"TRIM(S.Nombre) AS Nombre"=>"",
								"S.Telefono"=> "",
								"S.Email"=> ""
							);


	$columReport = implode(",",array_keys($array_columnas));
// Origen de datos SQL

if($tipoReporte == "Socio")
 	$sql_export["Registro Diagnosticos"] = "SELECT $columReport,D.IDDiagnostico,D.Nombre AS NomDiag,D.Descripcion,
																								DATE(DR.FechaTrCr) AS Fecha_Diagnostico
																				FROM Diagnostico D
																					JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
																					JOIN Socio S ON DR.IDSocio = S.IDSocio
																					INNER JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
																				WHERE S.IDClub = '".SIMUser::get("club")."' ".$condicion_fecha."
																				$condicionStr
																				GROUP BY S.IDSocio,Fecha_Diagnostico
																			";
if($tipoReporte == "Funcionario")
  	$sql_export["Registro Diagnosticos"] = "SELECT S.IDUsuario AS IDSocio,$columReport,D.IDDiagnostico,D.Nombre AS NomDiag,D.Descripcion,
																								DATE(DR.FechaTrCr) AS Fecha_Diagnostico
																				FROM Diagnostico D
																					JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
																					JOIN Usuario S ON DR.IDUsuario = S.IDUsuario
																				WHERE S.IDClub = '".SIMUser::get("club")."' ".$condicion_fecha."
																				$condicionStr
																				GROUP BY IDSocio,Fecha_Diagnostico
																			";

		$headerPL["f1"] = array("Reporte :"=>"Registro Diagnosticos ".$tipoReporte);
		$headerPL["f2"] = array("Fecha Generacion :"=>date( "d m Y h:i" ));

		$filename = "RegistroDiagnosticos_".$tipoReporte.date( "Y_m_d" );

		$arrayFiles = $reportObj->exportSQL_PHPXLS("Registro Diagnosticos ".$tipoReporte,$sql_export , $filename.".xls","",TRUE,$headerPL);

		exit;
