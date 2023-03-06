<?php

	// require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

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

	if(empty(SIMUser::get("club")))
		exit;

	$get = SIMUtil::makeSafe( $_GET );
	$post = SIMUtil::makeSafe( $_POST );

	require_once LIBDIR."/APPReport.class.php";

	$reportObj = new APPReport();

	$query = $dbo->query("SELECT * FROM VacunaMarca");
	$marcaVacunas = $dbo->fetch($query);

	//Condiciones de Busqueda
	$condiciones = '';
	$tipoReporte = $get["reporte"];

	if(isset($get["estadoVacunados"]) && !is_null($get["estadoVacunados"])){
		if($get["estadoVacunados"]=='N'){
			$condiciones .= " AND V.Vacunado<>'S'";
		}else{
			$condiciones .= " AND V.Vacunado='{$get['estadoVacunados']}'";
		}    
	}

	if(isset($get["fechaInicio"]) && !is_null($get["fechaInicio"])){
			$fechaInicio = $get["fechaInicio"];
			$fechaFin = $get["fechaFin"];
			$condiciones .= " AND (V.FechaPrimeraDosis BETWEEN '$fechaInicio' AND '$fechaFin' OR V.FechaSegundaDosis BETWEEN '$fechaInicio' AND '$fechaFin')";
	}

	if(isset($get["citaVacuna"]) && !is_null(($get["citaVacuna"]))){
		$citaVacuna = $get["citaVacuna"];
		if($citaVacuna=="ninguna"){
			$condiciones .= " AND V.FechaPrimeraDosis=0000-00-00 AND V.FechaSegundaDosis=0000-00-00";
		}else if($citaVacuna=="primera"){
			$condiciones .= " AND V.FechaPrimeraDosis<>0000-00-00 AND V.FechaSegundaDosis=0000-00-00";
		}else if($citaVacuna=="segunda"){
			$condiciones .= " AND V.FechaPrimeraDosis<>0000-00-00 AND V.FechaSegundaDosis<>0000-00-00";
		}else if($citaVacuna=="ambas"){
			$condiciones .= " AND (V.FechaPrimeraDosis<>0000-00-00 OR V.FechaSegundaDosis<>0000-00-00)";
		}
		
	}

	if(isset($get["tipoInvitado"]) && !is_null($get["tipoInvitado"])){
		$tipoInvitado = $get["tipoInvitado"];
		$condiciones .= " AND I.IDTipoInvitado=$tipoInvitado ";
	}
	
	
	if(isset($get["tipoClasificacionInvitado"]) && !is_null($get["tipoClasificacionInvitado"])){
		$tipoClasificacionInvitado = $get["tipoClasificacionInvitado"];
		$condiciones .= " AND CI.IDClasificacionInvitado=$tipoClasificacionInvitado ";
	}
	

	if(isset($get["idVacunaMarca"]) && !is_null($get["idVacunaMarca"])){
			$idMarcaVacuna = $get["idVacunaMarca"];
			$condiciones .= " AND V.IDVacunaMarca=$idMarcaVacuna ";
	}

	if(isset($get["entidadVacuna"]) && !is_null($get["entidadVacuna"])){
			$entidad = $get["entidadVacuna"];
			$condiciones .= " AND V.Entidad LIKE '%$entidad%' ";

		}
	

	/* $array_columnas_socio = array(
						"V.Vacunado" => "",
						"V.Entidad AS 'Entidad Vacuna'" => "",
						"VM.Nombre AS 'Marca vacuna'"=>"",					
						
						"V.LugarCitaPrimera" => "",
						"V.FechaPrimeraDosis" => "",												
						
						"V.LugarCitaSegunda" => "",
						"V.FechaSegundaDosis " => "",
						
						"VacunadoTerceraDosis" => "", 
						"V.FechaTerceraDosis" => "",
						"V.LugarTerceraDosis" => "",						

            			"S.NumeroDocumento"=> "",
            			"S.Accion"=> "",
						"S.Nombre"=>"",
						"S.Apellido"=>"",
						"S.Email"=> "",
						"S.Telefono"=> "",
						"S.Direccion"=> "",
						"S.Celular"=> "",
						"S.FechaNacimiento"=>"",
						
						"CONCAT('" . VACUNA_ROOT . "',V.ImagenPrimeraDosis) AS 'Enlace foto certificado primera dosis'" => "",
						"CONCAT('" . VACUNA_ROOT . "',V.ImagenSegundaDosis) AS 'Enlace foto certificado segunda dosis'" => "",
						"CONCAT('" . VACUNA_ROOT . "',V.ImagenTerceraDosis) AS 'Enlace foto certificado tercera dosis'" => "",

					); */

  /* $array_columnas_usuario = array(
						"V.Vacunado" => "",
						"V.Entidad AS 'Entidad Vacuna'" => "",
						"VM.Nombre AS 'Marca vacuna'"=>"",						
						"V.LugarCitaPrimera" => "",
						"V.FechaPrimeraDosis" => "",
						"V.FechaSegundaDosis" => "",
						"V.LugarCitaSegunda" => "",

						"VacunadoTerceraDosis" => "", 
						"V.FechaTerceraDosis" => "",
						"V.LugarTerceraDosis" => "",

            			"U.TipoUsuario" => "",
            			"U.NumeroDocumento" => "",
            			"U.Nombre" => "",
            			"U.Telefono" => "",
            			"U.User" => "",
            			"U.Email" => "",
						
						"CONCAT('" . VACUNA_ROOT ."',V.ImagenPrimeraDosis) AS 'Enlace foto certificado primera dosis'" => "",
						"CONCAT('" . VACUNA_ROOT ."',V.ImagenSegundaDosis) AS 'Enlace foto certificado segunda dosis'" => "",
						"CONCAT('" . VACUNA_ROOT ."',V.ImagenTerceraDosis) AS 'Enlace foto certificado tercera dosis'" => "",

  );  
 */
	$columReportSocios = implode(",",array_keys($array_columnas_socio));
  $columReportUsuarios = implode(",",array_keys($array_columnas_usuario));

// Origen de datos SQL
/* if((!isset($get["tipoVacunados"]) && is_null($get["tipoVacunados"])) || $get["tipoVacunados"]=="socio"){

	if(isset($get["numeroDocumento"]) && !is_null($get["numeroDocumento"])){
				
		$numeroDocumento = $get["numeroDocumento"];
		$condiciones .= " AND  S.NumeroDocumento='$numeroDocumento'";   
	
	}	

	 	$sql_export["Registro_vacunados_socios"] = "
	    SELECT $columReportSocios
	      FROM Vacuna V
	      INNER JOIN Socio S ON V.IDSocio=S.IDSocio 
		  LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca		 
				WHERE S.IDClub=".SIMUser::get("club")." ".
				$condiciones .
				" ORDER BY S.IDSocio"
			 ;
}

if((!isset($get["tipoVacunados"]) && is_null($get["tipoVacunados"])) || $get["tipoVacunados"]=="usuario"){

	if(isset($get["numeroDocumento"]) && !is_null($get["numeroDocumento"])){
				
		$numeroDocumento = $get["numeroDocumento"];
		$condiciones .= " AND  U.NumeroDocumento='$numeroDocumento'";   
	
	}	

		 $sql_export["Registro_vacunados_usuarios"] = "
	     SELECT $columReportUsuarios
	       FROM Vacuna V
	       INNER JOIN Usuario U ON V.IDUsuario=U.IDUsuario
		   LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca		
	 			WHERE U.IDClub=".SIMUser::get("club")." ".
	 			$condiciones .
	 			" ORDER BY U.IDUsuario"
	 		 ;
}
 */



$sql_export["Registro_vacunacion"]  = "
SELECT 
I.IDInvitado,
V.Vacunado,
TI.nombre AS 'Tipo Invitado',
CI.Nombre as 'Clasificacion Invitado',
V.Entidad AS 'Entidad Vacuna',
VM.Nombre AS 'Marca vacuna',
VE.Nombre AS 'Entidad vacuna',
V.LugarCitaPrimera,
V.FechaPrimeraDosis,
V.FechaSegundaDosis,
V.LugarCitaSegunda,
I.NumeroDocumento,
I.Nombre,
I.Apellido,
I.Email,
 I.Telefono
		FROM Vacuna V
		INNER JOIN Invitado I ON V.IDInvitado=I.IDInvitado
		LEFT JOIN TipoInvitado TI ON I.IDTipoInvitado = TI.IDTipoInvitado
	   LEFT JOIN ClasificacionInvitado CI ON I.IDClasificacionInvitado = CI.IDClasificacionInvitado
		LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca
		LEFT JOIN VacunaEntidad VE ON V.IDVacunaEntidad=VE.IDVacunaEntidad
			  WHERE I.IDClub=".SIMUser::get("club")." ".
			  $condiciones .
			   " ORDER BY I.IDInvitado";



		$headerPL["f1"] = array("Reporte :"=>"REGISTRO VACUNACION");
		$headerPL["f2"] = array("Fecha Generacion :"=>date( "d m Y h:i" ));

		$filename = "RegistroVacunacion_".date( "Y_m_d" );

		$arrayFiles = $reportObj->exportSQL_PHPXLS("Registro Diagnosticos",$sql_export , $filename.".xls","",TRUE,$headerPL);

		exit;
?>
