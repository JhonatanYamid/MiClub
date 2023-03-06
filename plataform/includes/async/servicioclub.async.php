<?php
	require("../../../admin/config.inc.php");
	if ($_GET['idclub'] != '')
	{
		$sql_servicio = "Select SC.* From ServicioClub SC Where SC.IDClub = '".$_GET['idclub']."' and SC.Activo = 'S' Order by TituloServicio";
		$result_servicio = $dbo->query($sql_servicio);
		while ($row_servicio = $dbo->fetchArray($result_servicio)):

			$IDServicio = $dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro = '".$row_servicio["IDServicioMaestro"]."' and IDClub = '".$_GET['idclub']."' " );

			if(!empty($row_servicio["TituloServicio"]))
				$nombre_servicio = $row_servicio["TituloServicio"];
			else
				$nombre_servicio = utf8_encode($dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$row_servicio["IDServicioMaestro"]."'" ));

			echo '<option value="'.$IDServicio.'">'.$nombre_servicio.'</option>';
		 endwhile;
	}
	?>
