 <?

	SIMReg::setFromStructure( array(
						"title" => "Club",
						"table" => "Club",
						"key" => "IDClub",
						"mod" => "Club"
	) );


	$script = "clubes";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );

	if(empty($_GET["FechaInicio"])):
		$_GET["FechaInicio"]=date("Y-m-d");
		$_GET["FechaFin"]=date("Y-m-d");;
	endif;

	if(!empty($_GET["IDTipoInvitado"])):
		$condicion_busqueda .= " and IDTipoInvitado = '".$_GET["IDTipoInvitado"]."'";
	endif;

	if(!empty($_GET["FechaInicio"])):
		$condicion_fecha_ingreso  .= " and FechaIngreso >= '".$_GET["FechaInicio"]." 00:00:00'";
		$condicion_fecha_salida  .= " and FechaSalida >= '".$_GET["FechaInicio"]." 00:00:00'";
		$condicion_fecha_ingreso_ocupacion  = " and FechaIngreso <= '".$_GET["FechaInicio"]." 23:59:59'";
	endif;

	if(!empty($_GET["FechaFin"])):
		$condicion_fecha_ingreso  .= " and FechaIngreso <= '".$_GET["FechaFin"]." 23:59:59'";
		$condicion_fecha_salida  .= " and FechaSalida <= '".$_GET["FechaFin"]." 23:59:59'";
	endif;


  $fecha_hoy=date("Y-m-d")." 00:00:00";
  $sql_vista="INSERT IGNORE INTO `LogAccesoVista` SELECT * FROM `LogAcceso` WHERE FechaTrCr >= '".$fecha_hoy."'";
  $dbo->query($sql_vista);
  
	//Resumen Entradas
	$sql_ocupacion_entrada = "Select  *
					  From LogAcceso
					  Where Tipo <> '' and IDClub = '".SIMUser::get("club")."' and Entrada = 'S' " .  $condicion_fecha_ingreso . " ". $condicion_busqueda . " Group by IDInvitacion";
  $result_ocupacion_entrada = $dbo->query($sql_ocupacion_entrada);
	while( $r_ocupacion_entrada = $dbo->fetchArray( $result_ocupacion_entrada ) ):
			$tipo_entrada=$r_ocupacion_entrada["Tipo"];
			if($tipo_entrada=="Contratista"):
				$IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$r_ocupacion_entrada["IDInvitacion"]."'" );
				$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );
				$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$IDTipoInvitado."'" );
				if(!empty($tipo_invitado))
					$tipo_entrada=$tipo_invitado;
			endif;
			$array_ocupacion_entrada[ $tipo_entrada ] ++;
	endwhile;
	//FIN Resumen Entradas



	//Resumen Salidas
	$sql_ocupacion_salida = "Select *
					  From LogAcceso
					  Where Tipo <> '' and IDClub = '".SIMUser::get("club")."' and Salida = 'S' "  . $condicion_fecha_salida . " ". $condicion_busqueda . " Group by IDInvitacion";

	$result_ocupacion_salida = $dbo->query($sql_ocupacion_salida);
	while( $r_ocupacion_salida = $dbo->fetchArray( $result_ocupacion_salida ) ):
			$tipo_salida=$r_ocupacion_salida["Tipo"];
			if($tipo_salida=="Contratista"):
				$IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$r_ocupacion_salida["IDInvitacion"]."'" );
				$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );
				$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$IDTipoInvitado."'" );
				if(!empty($tipo_invitado))
					$tipo_salida=$tipo_invitado;
			endif;
			$array_ocupacion_salida[ $tipo_salida ] ++;
	endwhile;

	//FIN Resumen Salidas


	//Ocupacion Actual
  if($_GET["FechaInicio"]==$_GET["FechaFin"]){
	   $array_adentro = SIMUtil::consulta_ocupacion($_GET,SIMUser::get("club"),"Totales" );
  }
	//FIN Ocupacion actual


?>
