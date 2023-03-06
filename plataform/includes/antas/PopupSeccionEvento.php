<?php
	require( "config.inc.php" );
	SIMUtil::cache();
	
	if( SIMNet::get( "mod" ) == "PlanMedios" )
		session_start();
	
	//handler de sesion
	$simsession = new SIMSession( SESSION_LIMIT );
	
	//traemos lo datos de la session
	$datos = $simsession->verificar();
	
	if( !is_object( $datos ) )
	{
		header( "location:login.php?msg=" . $datos );
		exit;
	}
	
	//encapsulamos los parammetros
	SIMUser::setFromStructure( $datos );

	
	function CreaArbolSecciones($ValorSecciones)
	{
		$dbo =& SIMDB::get();
		$Padre=$ValorSecciones['IDSeccionEvento'];
		$RegistrosHijos=$dbo->all("SeccionEvento"," IDPadre = '".$Padre."'");
			while($RHijos=$dbo->fetchArray( $RegistrosHijos ))
					$ArrayHijos[$RHijos['IDSeccionEvento']]=$RHijos;
		?>
		<li>
			<span class="folder"><a href="JavaScript:close();" onclick="window.opener.document.frm.NombreSeccion.value = '<?php echo $ValorSecciones['Nombre'];?>';window.opener.document.frm.IDSeccionEvento.value = '<?php echo $ValorSecciones['IDSeccionEvento'];?>';"><?php echo $ValorSecciones['Nombre'];?></a></span>
		<?php
		if( $ArrayHijos != Null )
		{
			?>
			<ul>
			<?php
			foreach($ArrayHijos as $clave => $valor)
				CreaArbolSecciones($valor);
			?>
			</ul>
			<?php 
		}
		?>
		</li>
		<?php 	
		return true;
	}	
	//fin funcion
	
$dbo =& SIMDB::get();
$key = SIMReg::get( "key" );
$table = SIMReg::get( "table" );

if(!empty($_SESSION[IDClub])):
	$condicion=" and IDClub = " . $_SESSION[IDClub];
endif;
//Secciones Padre
$Secciones=$dbo->all("SeccionEvento"," IDPadre = '0' ".$condicion);

while( $RSeccioones = $dbo->fetchArray( $Secciones ) )
	$ArraySecciones[$RSeccioones[IDSeccionEvento]]=$RSeccioones;	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es" dir="ltr">
<head>
<title><?php echo APP_TITLE?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


	<link rel="stylesheet" href="css/estilos.css" type="text/css" />
	
	<script type="text/javascript" src="jscript/jquery-1.2.6.js"></script>
	
	<script type="text/javascript" src="jscript/calendar/jquery.datePicker.js"></script>
	
	<script type="text/javascript" src="jscript/common.js"></script>
	
	<script src="jscript/treeview/jquery.cookie.js" type="text/javascript"></script>
	<script src="jscript/treeview/jquery.treeview.js" type="text/javascript"></script>
	<link rel="stylesheet" href="jscript/treeview/jquery.treeview.css" />
	</head>
<body>
<table class="adminheading">
	<tbody><tr>
		<th>Seleccione la secci&oacute;n deseada haciendo clic.</th>
	</tr>
</tbody></table>
<br>
		<ul id="ArbolSecciones" class="filetree">
			<?php
				foreach($ArraySecciones as $ClaveSeccion => $ValorSecciones)							
						CreaArbolSecciones($ValorSecciones)
			?>
		</ul>
</body>
</html>