 <?
	$action = SIMNet::req("action");
	if($action=="mediospago"):
	$reporte="Reportes por medios de pagos";
	elseif ($action=="productos"): 
	$reporte="Reportes de productos";
	elseif ($action=="detalleventa"): 
	$reporte="Reportes por detalles de ventas";
	elseif ($action=="porvendedor"): 
	$reporte="Reportes por vendedor";
	elseif ($action=="afiliadosactivos"): 
	$reporte="Reportes de afiliados activos";
	elseif ($action=="porvencimientos"): 
	$reporte="Reportes vencidos";
	elseif ($action=="afiliadosnuevos"): 
	$reporte="Reportes de afiliados nuevos";
	elseif ($action=="reportefinanciero"): 
	$reporte="Reporte financiero";
	
	endif;
	 
	
	SIMReg::setFromStructure(array(
		"title" => $reporte, 
		"table" => "Facturacion",
		"key" => "IDFacturacion",
		"mod" => "Facturacion"
	));

	$script = "facturacionreporte";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	$action = SIMNet::req("action");
	
	$IDUsuario = SIMUser::get("IDUsuario");
	$IDClub = SIMUser::get("club");
	$idPadre = SIMUtil::IdPadre($IDClub);
	$hijos = SIMUtil::ObtenerHijosClubPadre($IDClub);
	$idPerfil = SIMUser::get("IDPerfil");

	$hoy = new DateTime();

	//Verificar permisos
	//SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	//SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	switch ($action) { 
		case "insert": 
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);
				//medios de pagos
				if($frm['opcion']=="mediospago"):
				
                                $reporte=$frm['reporte'];
				$club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence']; 
                                 
				$sqlSede = "SELECT Nombre FROM Club WHERE IDClub=$club"; 
		                $qrySede = $dbo->query($sqlSede);
   			 	$sede = $dbo->fetchArray( $qrySede);
   			 	$nombre=$sede["Nombre"]; 
				$orden= $frm['Orden']; 
				$respuesta=1; 
				
				//productos
				elseif($frm['opcion']=="productos"):
				
				$reporte=$frm['reporte'];
				$club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence']; 
				 
				$sqlSede = "SELECT Nombre FROM Club WHERE IDClub=$club"; 
		                $qrySede = $dbo->query($sqlSede);
   			 	$sede = $dbo->fetchArray( $qrySede);
   			 	$nombre=$sede["Nombre"]; 
				$orden= $frm['Orden']; 
				$respuesta=1;  
				
				 //detalles de ventas
				elseif($frm['opcion']=="detalleventa"):
				 
				$reporte=$frm['reporte'];
				$club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence']; 
				 
				$sqlSede = "SELECT Nombre FROM Club WHERE IDClub=$club"; 
		                $qrySede = $dbo->query($sqlSede);
   			 	$sede = $dbo->fetchArray( $qrySede);
   			 	$nombre=$sede["Nombre"]; 
				$orden= $frm['Orden']; 
				$respuesta=1;  
				
				//por vendedor
				elseif($frm['opcion']=="porvendedor"):
				
				$reporte=$frm['reporte'];
				$club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence']; 
				 
				$sqlSede = "SELECT Nombre FROM Club WHERE IDClub=$club"; 
		                $qrySede = $dbo->query($sqlSede);
   			 	$sede = $dbo->fetchArray( $qrySede);
   			 	$nombre=$sede["Nombre"]; 
				$orden= $frm['Orden']; 
				$respuesta=1;  
				
				//afiliados activos
				elseif($frm['opcion']=="afiliadosactivos"):
				
				$reporte=$frm['reporte'];
				$club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence']; 
				 
				$sqlSede = "SELECT Nombre FROM Club WHERE IDClub=$club"; 
		                $qrySede = $dbo->query($sqlSede);
   			 	$sede = $dbo->fetchArray( $qrySede);
   			 	$nombre=$sede["Nombre"]; 
				$orden= $frm['Orden']; 
				$respuesta=1;  
				 
				 
				  
				//afiliados vencidos
				elseif($frm['opcion']=="afiliadosvencidos"):
				
				$reporte=$frm['reporte'];
				$club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence']; 
				 
				$sqlSede = "SELECT Nombre FROM Club WHERE IDClub=$club"; 
		                $qrySede = $dbo->query($sqlSede);
   			 	$sede = $dbo->fetchArray( $qrySede);
   			 	$nombre=$sede["Nombre"]; 
				$orden= $frm['Orden']; 
				$respuesta=1;  
				
				//afiliados nuevos
				elseif($frm['opcion']=="afiliadosnuevos"):
				
				$reporte=$frm['reporte'];
				$club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence']; 
				 
				$sqlSede = "SELECT Nombre FROM Club WHERE IDClub=$club"; 
		                $qrySede = $dbo->query($sqlSede);
   			 	$sede = $dbo->fetchArray( $qrySede);
   			 	$nombre=$sede["Nombre"]; 
				$orden= $frm['Orden']; 
				$respuesta=1;   
				 
				//reporte financiero 
				elseif($frm['opcion']=="reportefinanciero"):
				
				$reporte=$frm['reporte'];
				$club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence']; 
				 
				$sqlSede = "SELECT Nombre FROM Club WHERE IDClub=$club"; 
		                $qrySede = $dbo->query($sqlSede);
   			 	$sede = $dbo->fetchArray( $qrySede);
   			 	$nombre=$sede["Nombre"]; 
				$orden= $frm['Orden']; 
				$respuesta=1;   
				 
				
				
				
				
				 
				endif;
 
                break;
		case "mediospago":
			$view = "views/" . $script . "/form.php";
			break;
		
		case "productos":
			$view = "views/" . $script . "/form.php";
			break;
		case "detalleventa":
			$view = "views/" . $script . "/form.php";
			break;
		case "porvendedor":
			$view = "views/" . $script . "/form.php";
			break;
		case "afiliadosactivos":
			$view = "views/" . $script . "/form.php";
			break;
		case "porvencimientos":
			$view = "views/" . $script . "/form.php";
			break;
		case "afiliadosnuevos":
			$view = "views/" . $script . "/form.php";
			break;	
		case "reportefinanciero":
			$view = "views/" . $script . "/form.php";
			break;	  

		default:
			$view = "views/" . $script . "/form.php";
			break;
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/form.php";

?>
