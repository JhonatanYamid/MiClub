<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	session_start();

	$tipoReporte = empty( $_SESSION["TipoRepDiagnostico"] )? exit : $_SESSION["TipoRepDiagnostico"];

//handler de sesion
$simsession = new SIMSession( SESSION_LIMIT );

//traemos lo datos de la session
$datos = $simsession->verificar();

if( !is_object( $datos ) || empty($_POST['IDSocio']))
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

	require_once LIBDIR."/APPReport.class.php";

	$reportObj = new APPReport();

	if($tipoReporte == "Socio"){

 	 $sql_usuario = "SELECT
											 CONCAT( S.Nombre, ' ', S.Apellido ) AS Nombre,
											S.Direccion,
											S.Celular AS Telefono,
											CL.Nombre AS EmpresaPpal,
											S.Email
											FROM Socio S
											INNER JOIN Club CL ON S.IDClub = CL.IDClub
											WHERE S.IDClub = '".SIMUser::get("club")."' AND S.IDSocio = ".$_POST['IDSocio'];

											$sql_export["Perfil"] = "SELECT CED.Nombre AS Pregunta ,SCES.Valor AS Respuesta
																									FROM CampoEditarSocio CED
																										JOIN SocioCampoEditarSocio SCES ON CED.IDCampoEditarSocio = SCES.IDCampoEditarSocio
																									WHERE CED.IDClub = '".SIMUser::get("club")."' AND SCES.IDSocio = ".$_POST['IDSocio']."
																									GROUP BY SCES.IDCampoEditarSocio
																									ORDER BY CED.Orden ";
	}
	if($tipoReporte == "Funcionario"){
		$sql_usuario = "SELECT
			 											  S.Nombre,
									 											S.Telefono,
									 											CL.Nombre AS EmpresaPpal,
									 											S.Email
									 											FROM Usuario S
									 											INNER JOIN Club CL ON S.IDClub = CL.IDClub
									 											WHERE S.IDClub = '".SIMUser::get("club")."' AND S.IDUsuario = ".$_POST['IDSocio'];

		$sql_export["Perfil"] = "SELECT CED.Nombre AS Pregunta ,SCES.Valor AS Respuesta
											 					FROM CampoEditarUsuario CED
												 					JOIN UsuarioCampoEditarUsuario SCES ON CED.IDCampoEditarUsuario = SCES.IDCampoEditarUsuario
											 					WHERE CED.IDClub = '".SIMUser::get("club")."' AND SCES.IDUsuario = ".$_POST['IDSocio']."
											 					GROUP BY SCES.IDCampoEditarUsuario
											 					ORDER BY CED.Orden ";

 }

		$result = $dbo->query($sql_usuario);
		$rowUsuario = $dbo->fetchArray($result);

		$headerPL["f1"] = array("Reporte :"=>"Cierre Epidemiologico");
//		$headerPL["f2"] = array("Empresa :"=>$rowUsuario["EmpresaPpal"]);
		$headerPL["f3"] = array("Fecha Generacion :"=>date( "d m Y h:m" ));
		$headerPL["f4"] = array("COD Empleado :"=> $rowUsuario["CodigoEmpleado"],"Empleado : "=>$rowUsuario["Nombre"]);
		$headerPL["f5"] = array("Telefono"=> $rowUsuario["Telefono"],"Email"=> $rowUsuario["Email"]);

		$filename = "CierreEpidemiologico".date( "Y_m_d" );




 	$sql_export["Contacto_Cercano"] ="SELECT RC.IDSocio,RC.Fecha,RC.Lugar,RC.Latitud,RC.Longitud,RCP.NombreExterno,CCE.Nombre AS Variable,RCEOD.Valor AS Valor_Variable,CRC.Nombre AS Campo,RCO.Valor
				FROM RegistroContacto RC
				LEFT JOIN RegistroContactoPersona RCP ON RCP.IDRegistroContacto = RC.IDRegistroContacto
				LEFT JOIN RegistroContactoOtrosDatos RCO ON RCO.IDRegistroContacto = RCP.IDRegistroContacto
				LEFT JOIN RegistroContactoExternoOtrosDatos RCEOD ON RCEOD.IDRegistroContacto = RC.IDRegistroContacto
				LEFT JOIN CampoContactoExterno CCE ON CCE.IDCampoContactoExterno = RCEOD.IDCampoContactoExterno
				LEFT JOIN CampoRegistroContacto CRC ON CRC.IDCampoRegistroContacto = RCO.IDCampoRegistroContacto
				WHERE RC.IDClub = '".SIMUser::get("club")."' AND RC.IDSocio = ".$_POST['IDSocio']."
				ORDER BY RC.Fecha
	";

	 $sql_export["Seguimiento"] ="SELECT ES.Nombre AS Estado,SS.Fecha,SS.Observacion,SS.FechaTrCr AS Fecha_Registro
					FROM SocioSeguimiento SS
					JOIN EstadoSalud ES ON SS.IDEstadoSalud = ES.IDEstadoSalud
					JOIN Socio S ON S.IDSocio = SS.IDSocio
					 WHERE S.IDClub = '".SIMUser::get("club")."' AND S.IDSocio = ".$_POST['IDSocio']."
	";

	$arrayFiles = $reportObj->exportSQL_PHPXLS("Cierre Epidemiologico",$sql_export , $filename.".xls","",TRUE,$headerPL);

	exit;

?>
