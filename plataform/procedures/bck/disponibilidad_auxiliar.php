<?php

	$ids = SIMNet::req("ids");
	
	
	$action = SIMNet::req("action");
	switch ( $action ) {
		
		
		case 'insert':
		$frm = SIMUtil::varsLOG( $_POST );
		
		// Inserto el encabezado
		$id_disponibilidad = $dbo->insert( $frm , "AuxiliarDisponibilidad" , "IDAuxiliarDisponibilidad" );
		
		//Inserto el detalle
		foreach($frm["IDDia"] as $Dia_seleccion):
			$array_dia []= $Dia_seleccion;
		endforeach;
		if(count($array_dia)>0):
			$id_dia=implode("|",$array_dia) . "|";;
		endif;
		$frm["IDDia"]=$id_dia;
		$frm["IDAuxiliarDisponibilidad"] = $id_disponibilidad;
		//Auxiliares 
		foreach($frm["IDAuxiliar"] as $IDAuxiliar):
			$array_auxiliar []= $IDAuxiliar;
		endforeach;
		if(count($array_auxiliar)>0):
			$ID_Auxiliar=implode("|",$array_auxiliar). "|";
		endif;
		$frm["IDAuxiliar"]=$ID_Auxiliar;
		
		for ($contador_horas = 1; $contador_horas <= $frm["contador_horas"]; $contador_horas++):
			$campos_intervalo = "Intervalo".	$contador_horas;
			$campos_desde = "HoraDesde".	$contador_horas;
			$campos_hasta = "HoraHasta".	$contador_horas;
			if (!empty($frm[$campos_desde]) && !empty($frm[$campos_hasta])):
				$campos_intervalo = "Intervalo".	$contador_horas;
				$frm["HoraDesde"]=$frm[$campos_desde];
				$frm["HoraHasta"]=$frm[$campos_hasta];
				$id = $dbo->insert( $frm , "AuxiliarDisponibilidadDetalle" , "IDAuxiliarDisponibilidadDetalle" );
			endif;
		endfor;	
		
		?>
		<script>
		window.top.location.href = "serviciosclub.php?action=edit&tab=auxiliares&ids=<?php echo $frm[IDServicio]?>"; 
		</script>
        <?php
		SIMNotify::capture( "La disponibilidad se ha creado correctamente" , "info alert-success" );	
		SIMHTML::jsAlert("Disponibilidad Creada");
		//SIMHTML::jsRedirect( "serviciosclub.php?action=edit&tab=disponibilidad&ids=".$frm[IDServicio] );	
		exit;
			

		break;
		
		
		case "update":
		$frm = SIMUtil::varsLOG( $_POST );
		
		// Actualizo encabezado
		$dbo->update( $frm , "AuxiliarDisponibilidad" , "IDAuxiliarDisponibilidad" , $frm[IDAuxiliarDisponibilidad] );		
		
		//Actualizo el detalle		
				foreach($frm["IDDia"] as $Dia_seleccion):
					$array_dia []= $Dia_seleccion;
				endforeach;
				
				if(count($array_dia)>0):
					$id_dia=implode("|",$array_dia) . "|";
				endif;
				$frm["IDDia"]=$id_dia;
				
				//Auxiliares 
				foreach($frm["IDAuxiliar"] as $IDAuxiliar):
					$array_auxiliar []= $IDAuxiliar;
				endforeach;
				if(count($array_auxiliar)>0):
					$ID_Auxiliar=implode("|",$array_auxiliar). "|";
				endif;
				$frm["IDAuxiliar"]=$ID_Auxiliar;
				
	
				//Borro anterior
				$dbo->query("Delete From AuxiliarDisponibilidadDetalle Where IDAuxiliarDisponibilidad  = '".$frm[IDAuxiliarDisponibilidad]."'");
				for ($contador_horas = 1; $contador_horas <= $frm["contador_horas"]; $contador_horas++):
				$campos_intervalo = "Intervalo".	$contador_horas;
				$campos_desde = "HoraDesde".	$contador_horas;
				$campos_hasta = "HoraHasta".	$contador_horas;
				if (!empty($frm[$campos_desde]) && !empty($frm[$campos_hasta])):
					$frm["Intervalo"]=$frm[$campos_intervalo];
					$frm["HoraDesde"]=$frm[$campos_desde];
					$frm["HoraHasta"]=$frm[$campos_hasta];
					$id = $dbo->insert( $frm , "AuxiliarDisponibilidadDetalle" , "IDAuxiliarDisponibilidadDetalle" );					
				endif;
			endfor;	
			
			?>
			<script>
			window.top.location.href = "serviciosclub.php?action=edit&tab=auxiliares&ids=<?php echo $frm[IDServicio]?>"; 
			</script>
			<?php
			SIMNotify::capture( "La disponibilidad se ha creado correctamente" , "info alert-success" );	
			SIMHTML::jsAlert("Disponibilidad actualizada");
			//SIMHTML::jsRedirect( "serviciosclub.php?action=edit&tab=disponibilidad&ids=".$frm[IDServicio] );	
			exit;
				
		break;
		
	}//end switch

	

	if( empty( $view ) )
		$view = "views/disponibilidad_auxiliar/form.php";


		

?>