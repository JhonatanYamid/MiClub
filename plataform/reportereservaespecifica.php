<?php
//Script para exportar reporte de contactos por rango de fechas
require( dirname( __FILE__ ) . "/../admin/config.inc.php" );


	
		 $sql = "Select * From ReservaGeneral Where (Fecha =  '2016-07-16' or Fecha =  '2016-07-23' or Fecha =  '2016-07-30') and IDClub = '7' and (IDServicio = '24' or IDServicio = 98 or IDServicio = 99)";
		
		$nombre = "Reservas" . date( "Y_m_d H:i:s" );
							  
		$qry = $dbo->query( $sql );
		$Num=$dbo->rows( $qry );
	
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>NUMERO ACCION</th>";
		$html .= "<th>TIPO SOCIO</th>";
		$html .= "<th>SOCIO</th>";
		$html .= "<th>FECHA RESERVA</th>";
		$html .= "<th>HORA</th>";
		$html .= "<th>OBSERVACIONES</th>";
		$html .= "<th>TOTAL JUGADORES</th>";
		$html .= "<th>JUGADORES</th>";
		$html .= "</tr>";
		$item=0;		
		while( $row = $dbo->fetchArray( $qry,$a ) )
		{	
			$nombre_invitados = "";
			unset($array_invitados);
			
			$html .= "<tr>";
			$html .= "<td>".$dbo->getFields( "Socio" , "Accion" , "IDSocio = '" . $row["IDSocio"] . "'")."</td>";
			$html .= "<td>".$dbo->getFields( "Socio" , "TipoSocio" , "IDSocio = '" . $row["IDSocio"] . "'")."</td>";
			$html .= "<td>".$dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $row["IDSocio"] . "'"). " ". $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $row["IDSocio"] . "'")."</td>";
			$html .= "<td>".strtoupper($row["Fecha"])."</td>";
			$html .= "<td>".strtoupper($row["Hora"])."</td>";			
			$html .= "<td>".strtoupper($row["Observaciones"])."</td>";
			//Consulto el total de jugadores invitados
			$sql_invitado = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$row["IDReservaGeneral"]."'");
			$total_invitado = $dbo->rows($sql_invitado);			
			while($row_invitado = $dbo->fetchArray($sql_invitado)):
				if(!empty($row_invitado["Nombre"])):
					$array_invitados[]=$row_invitado["Nombre"]; 
				else:
					$array_invitados[]=$dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $row_invitado["IDSocio"] . "'"). " ". $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $row_invitado["IDSocio"] . "'"); 	
				endif;	
			endwhile;
			
			if(count($array_invitados)>0):
				$nombre_invitados = implode(",",$array_invitados);
			endif;
			
			
			$html .= "<td>".$total_invitado."</td>";
			$html .= "<td>".$nombre_invitados."</td>";
			
			$html .= "</tr>";
		}
		$html .= "</table>";
		
		//construimos el excel
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");		
		echo $html;
		exit();
        
?>