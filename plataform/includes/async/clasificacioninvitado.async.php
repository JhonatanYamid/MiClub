<?php
	require("../../../admin/config.inc.php");	
	if ($_GET['idtipo'] != '')
	{
		$sql = "SELECT * FROM ClasificacionInvitado WHERE IDTipoInvitado = '".$_GET['idtipo']."'  ORDER BY Nombre ASC";
		$qry = $dbo->query( $sql );
		echo '<option value="">[Seleccione]</option>';
		while( $r= $dbo->fetchArray( $qry ) )
		{
			  echo '<option value="'.$r['IDClasificacionInvitado'].'">'.$r[Nombre].'</option>';
		}
	}
	?>