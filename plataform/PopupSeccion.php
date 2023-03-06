<?php
	include( "procedures/general.php" );
	SIMUtil::cache();
	
	if( SIMNet::get( "mod" ) == "PlanMedios" )
		session_start();
	
	//handler de sesion
	$simsession = new SIMSession( SESSION_LIMIT );
	
	//traemos lo datos de la session
	$datos = $simsession->verificar();
	
	if( !is_object( $datos ) )
	{
		header( "location:index.php");
		exit;
	}
	
	//encapsulamos los parammetros
	SIMUser::setFromStructure( $datos );

	
	function CreaArbolSecciones($ValorSecciones)
	{
		$dbo =& SIMDB::get();
		$Padre=$ValorSecciones['IDSeccion'];
		$RegistrosHijos=$dbo->all("Seccion"," IDPadre = '".$Padre."'");
			while($RHijos=$dbo->fetchArray( $RegistrosHijos ))
					$ArrayHijos[$RHijos['IDSeccion']]=$RHijos;
		?>
		<li>
			<span class="folder"><a href="JavaScript:close();" onclick="window.opener.document.frmnoticias.NombreSeccion.value = '<?php echo $ValorSecciones['Nombre'];?>';window.opener.document.frmnoticias.IDSeccion.value = '<?php echo $ValorSecciones['IDSeccion'];?>';"><?php echo $ValorSecciones['Nombre'];?></a></span>
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

if(!empty(SIMUser::get("club"))):
	$condicion=" and IDClub = " . SIMUser::get("club");
endif;
//Secciones Padre
$Secciones=$dbo->all("Seccion"," IDPadre = '0' ".$condicion);

while( $RSeccioones = $dbo->fetchArray( $Secciones ) )
	$ArraySecciones[$RSeccioones[IDSeccion]]=$RSeccioones;	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es" dir="ltr">
<head>
<title><?php echo APP_TITLE?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


	<link rel="stylesheet" href="../admin/css/estilos.css" type="text/css" />
	
	<script type="text/javascript" src="../admin/jscript/jquery-1.2.6.js"></script>
	
	<script type="text/javascript" src="../admin/jscript/calendar/jquery.datePicker.js"></script>
	
	<script type="text/javascript" src="../admin/jscript/common.js"></script>
	
	<script src="../admin/jscript/treeview/jquery.cookie.js" type="text/javascript"></script>
	<script src="../admin/jscript/treeview/jquery.treeview.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../admin/jscript/treeview/jquery.treeview.css" />
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