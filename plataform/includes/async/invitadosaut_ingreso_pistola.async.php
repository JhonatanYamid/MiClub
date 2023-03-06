<?php
	include( "../../procedures/general_async.php" );
	SIMUtil::cache( "text/json" );
	$dbo =& SIMDB::get();

	$frm = SIMUtil::makeSafe( $_POST );


	$table = "SocioAutorizacion";
	$key = "IDSocioAutorizacion";
	$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'  ";
	
	//Consulto el id del invitado
	$datos_invitado = $dbo->fetchAll( "Invitado", " NumeroDocumento = '" . $frm["numerodocumento"] . "' ", "array" );

	//verificar si hay invitado para esa fecha
	$sql_invitado = " SELECT * FROM  " . $table . $where . " AND Fechainicio = CURDATE() AND IDInvitado = '" . $datos_invitado["IDInvitado"] . "' and Ingreso = 'N' Limit 1" ;
	$qry_invitado = $dbo->query( $sql_invitado );
	if( $dbo->rows( $qry_invitado ) > 0 )
	{	
		$datos_invitacion = $dbo->fetchArray($qry_invitado );
		$sql_ingreso = $dbo->query("Update SocioAutorizacion Set Ingreso = 'S', FechaIngreso = NOW() Where IDSocioAutorizacion = '".$datos_invitacion["IDSocioAutorizacion"]."'");
		//respoder
		$responce->msg = "Se realizo el ingreso con éxito.";

	}//end if
	else
	{
		//paila no está
		$responce->msg = "El número de documento ingresado no tiene invitación vigente";

	}//end else

	

	echo json_encode($responce);

?>