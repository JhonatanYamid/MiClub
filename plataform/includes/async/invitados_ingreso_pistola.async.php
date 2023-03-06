<?php
	include( "../../procedures/general_async.php" );
	SIMUtil::cache( "text/json" );
	$dbo =& SIMDB::get();

	$frm = SIMUtil::makeSafe( $_POST );


	$table = "SocioInvitado";
	$key = "IDSocioInvitado";
	$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "'  ";

	//verificar si hay invitado para esa fecha
	echo $sql_invitado = " SELECT * FROM  " . $table . $where . " AND FechaIngreso = CURDATE() AND NumeroDocumento = '" . $frm["numerodocumento"] . "' " ;
	$qry_invitado = $dbo->query( $sql_invitado );
	if( $dbo->rows( $qry_invitado ) > 0 )
	{

		$r_invitado = $dbo->fetchArray( $qry_invitado );
		$sql_socio = "SELECT * FROM Socio WHERE IDSocio = '" . $r_invitado["IDSocio"] . "' AND IDClub = '" . SIMUser::get("club") . "' ";
		$qry_socio = $dbo->query( $sql_socio );
		$r_socio = $dbo->fetchArray( $qry_socio );

		$sql_ingreso = "UPDATE " . $table . " SET Estado = 'I', FechaIngresoClub = NOW() " . $where . "  AND FechaIngreso = CURDATE() AND NumeroDocumento = '" . $frm["numerodocumento"] . "'  ";
		$qry_ingreso = $dbo->query($sql_ingreso);

		//respoder
		$responce->msg = "El ingreso se ha registrado correctamente. Socio que realiza la invitación: " . $r_socio["Nombre"] . " " . $r_socio["Apellido"];

	}//end if
	else
	{
		//paila no está
		$responce->msg = "El número de documento ingresado no tiene invitación vigente";

	}//end else

	

	echo json_encode($responce);

?>