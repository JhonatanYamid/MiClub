<?php
	header('Content-Type: text/txt; charset=UTF-8');
	include( "../../procedures/general_async.php" );
	SIMUtil::cache( "text/json" );
	$dbo =& SIMDB::get();
	$frm = SIMUtil::makeSafe( $_POST );
	$frm_get =  SIMUtil::makeSafe( $_GET );

	require_once LIBDIR . "SIMWebServiceAccesos.inc.php";	

	if($frm["Tipo"]!="Salida"):
		$respuesta = SIMWebServiceAccesos::set_entrada_invitado(SIMUser::get("club"),$frm["IDUsuario"],"Usuario",$frm["Mecanismo"],SIMUser::get("IDUsuario"),$frm["OtrosCampos"]);
		//Registro el historial de accesos
		//$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Entrada, FechaIngreso,FechaTrCr) Values ('".$frm["IDSocio"]."','Socio','S',NOW(),NOW())");
	else:
		$respuesta = SIMWebServiceAccesos::set_salida_invitado(SIMUser::get("club"),$frm["IDUsuario"],"Usuario",$frm["Mecanismo"],SIMUser::get("IDUsuario"),$frm["OtrosCampos"]);
		//Registro el historial de accesos
		//$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Salida, FechaSalida, FechaTrCr) Values ('".$frm["IDSocio"]."','Socio','S',NOW(),NOW())");

	endif;

	if(!$respuesta['success']){
		SIMHTML::jsAlert($respuesta['message']);
		SIMHTML::jsRedirect("accesoinvitado.php");
	}
?>
["ok"]
