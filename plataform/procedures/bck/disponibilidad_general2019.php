<?php

	$ids = SIMNet::req("ids");
	
	
	$action = SIMNet::req("action");
	switch ( $action ) {
		
		
		case 'insert':
		$frm = SIMUtil::varsLOG( $_POST );
		
		// Inserto el encabezado
		$id_disponibilidad = $dbo->insert( $frm , "Disponibilidad" , "IDDisponibilidad" );
		
		
		//Inserto el detalle
		foreach($frm["IDDia"] as $Dia_seleccion):
			$array_dia []= $Dia_seleccion;
		endforeach;
		if(count($array_dia)>0):
			$id_dia=implode("|",$array_dia) . "|";;
		endif;
		$frm["IDDia"]=$id_dia;
		$frm["IDDisponibilidad"] = $id_disponibilidad;
		//Elementos 
		foreach($frm["IDServicioElemento"] as $IDServicioElemnto):
			$array_servicio_elemento []= $IDServicioElemnto;
		endforeach;
		if(count($array_servicio_elemento)>0):
			$ID_Servicio_Elemento=implode("|",$array_servicio_elemento). "|";
		endif;
		$frm["IDServicioElemento"]=$ID_Servicio_Elemento;
		
		for ($contador_horas = 1; $contador_horas <= $frm["contador_horas"]; $contador_horas++):
			$campos_intervalo = "Intervalo".	$contador_horas;
			$campos_desde = "HoraDesde".	$contador_horas;
			$campos_hasta = "HoraHasta".	$contador_horas;
			if (!empty($frm[$campos_desde]) && !empty($frm[$campos_hasta])):
				$campos_intervalo = "Intervalo".	$contador_horas;
				$frm["HoraDesde"]=$frm[$campos_desde];
				$frm["HoraHasta"]=$frm[$campos_hasta];
				$id = $dbo->insert( $frm , "ServicioDisponibilidad" , "IDServicioDisponibilidad" );
			endif;
		endfor;	
		
		?>
		<script>
		window.top.location.href = "serviciosclub.php?action=edit&tab=disponibilidad&ids=<?php echo $frm[IDServicio]?>"; 
		</script>
        <?php
		SIMNotify::capture( "La reserva se ha creado correctamente" , "info alert-success" );	
		SIMHTML::jsAlert("Disponibilidad Creada");
		//SIMHTML::jsRedirect( "serviciosclub.php?action=edit&tab=disponibilidad&ids=".$frm[IDServicio] );	
		exit;
			

		break;
		
		
		case "update":
		$frm = SIMUtil::varsLOG( $_POST );
		
	
		// Actualizo encabezado
		$dbo->update( $frm , "Disponibilidad" , "IDDisponibilidad" , $frm[IDDisponibilidad] );		
		
		//Actualizo el detalle		
				foreach($frm["IDDia"] as $Dia_seleccion):
					$array_dia []= $Dia_seleccion;
				endforeach;
				
				if(count($array_dia)>0):
					$id_dia=implode("|",$array_dia) . "|";
				endif;
				$frm["IDDia"]=$id_dia;
				
				//Elementos 
				foreach($frm["IDServicioElemento"] as $IDServicioElemnto):
					$array_servicio_elemento []= $IDServicioElemnto;
				endforeach;
				if(count($array_servicio_elemento)>0):
					$ID_Servicio_Elemento=implode("|",$array_servicio_elemento). "|";
				endif;
				$frm["IDServicioElemento"]=$ID_Servicio_Elemento;
				
	
				//Borro anterior
				$dbo->query("Delete From ServicioDisponibilidad Where IDDisponibilidad  = '".$frm[IDDisponibilidad]."'");
				for ($contador_horas = 1; $contador_horas <= $frm["contador_horas"]; $contador_horas++):
				$campos_intervalo = "Intervalo".	$contador_horas;
				$campos_desde = "HoraDesde".	$contador_horas;
				$campos_hasta = "HoraHasta".	$contador_horas;
				$campos_par = "HoraPar".	$contador_horas;
				if (!empty($frm[$campos_desde]) && !empty($frm[$campos_hasta])):
					$frm["Intervalo"]=$frm[$campos_intervalo];
					$frm["HoraDesde"]=$frm[$campos_desde];
					$frm["HoraHasta"]=$frm[$campos_hasta];
					$frm["HoraPar"]=$frm[$campos_par];
					$id = $dbo->insert( $frm , "ServicioDisponibilidad" , "IDServicioDisponibilidad" );					
				endif;
			endfor;	
			
			?>
			<script>
			window.top.location.href = "serviciosclub.php?action=edit&tab=disponibilidad&ids=<?php echo $frm[IDServicio]?>"; 
			</script>
			<?php
			SIMNotify::capture( "La reserva se ha creado correctamente" , "info alert-success" );	
			SIMHTML::jsAlert("Disponibilidad actualizada");
			//SIMHTML::jsRedirect( "serviciosclub.php?action=edit&tab=disponibilidad&ids=".$frm[IDServicio] );	
			exit;
				
		break;
		
	}//end switch

	

	if( empty( $view ) )
		$view = "views/disponibilidad_general/form.php";


		

?>