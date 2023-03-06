<?php



	$ids = SIMNet::req("ids");
	//Si es coordinador tenemos que mirar que servicio se trae primero
	//si es un usuario normal se traen las horas
	
	$fecha = SIMNet::req("fecha");
	$hora = SIMNet::req("hora");
	$idelemento = SIMNet::req("idelemento");
	$idservicio = SIMNet::req("idservicio");
	$tee = SIMNet::req("tee");


	$action = SIMNet::req("action");
	switch ( $action ) {
		
		
		case 'insert':
			$frm = SIMUtil::varsLOG( $_POST );
			
			
			//crear elemento en la tabla de reservas grupos
			$frm["Fecha"] = $frm["fecha"];
			$frm["IDElemento"] = $frm["idelemento"];
			$frm["IDServicio"] = $frm["idservicio"];
			$frm["Hora"] = $frm["hora"];
			$frm["Tee"] = $frm["tee"];
			$frm["IDClub"] = SIMUser::get("club");

			$id_grupo = $dbo->insert( $frm , "ReservaGrupos" , "IDReservaGrupos" );

			//crear reserva --- Debe quedar con el nombre del grupo
			$respuesta = SIMWebService::set_reserva_generalV2(SIMUser::get("club"),$frm["IDSocio"],$frm["idelemento"],$frm["ids"],$frm["fecha"],$frm["hora"],"",$array_invitados,$frm["Observaciones"],"Admin",$frm["tee"],$IDDisponibilidad, $Repetir,$Periodo,$RepetirFechaFinal,$IDTipoModalidadEsqui,$IDAuxiliar,$IDTipoReserva,$NumeroTurnos,$id_grupo);


			if( $respuesta["success"] == "1" )
			{
				//bien
				SIMNotify::capture( "La reserva se ha creado correctamente" , "info alert-success" );	
			}//end if
			else
			{
				//paila
				SIMNotify::capture( $respuesta["message"]  , "error alert-danger" );
			}//end else

		break;
		
		
		
	}//end switch

	
	
		
		
	
	


	if( empty( $view ) )
		$view = "views/reservas_grupos/form.php";


		

?>