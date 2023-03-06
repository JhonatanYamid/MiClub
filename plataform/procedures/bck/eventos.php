 <?

	SIMReg::setFromStructure( array(
						"title" => "Evento",
						"table" => "Evento",
						"key" => "IDEvento",
						"mod" => "Evento"
	) );
	
	
	$script = "eventos";
	
	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	//Verificar permisos
	SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );	



switch ( SIMNet::req( "action" ) ) {

		case "add" :	
			$view = "views/".$script."/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
		break;

		case "insert" :	
		/*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/
		
		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST );
			
			$files =  SIMFile::upload( $_FILES["EventoImagen"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["EventoImagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["EventoFile"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto1"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto1"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto2"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto2"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto2"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto3"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto3"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto3"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["FotoDestacada"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["FotoDestacada"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["FotoDestacada"] = $files[0]["innername"];
			
			
			$files =  SIMFile::upload( $_FILES["SWF"] , SWFEvento_DIR );
			if( empty( $files ) && !empty( $_FILES["SWF"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["SWF"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Adjunto1Documento"] , IMGEVENTO_DIR , "DOC" );
			if( empty( $files ) && !empty( $_FILES["Adjunto1Documento"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Adjunto1File"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Adjunto2Documento"] , IMGEVENTO_DIR , "DOC" );
			if( empty( $files ) && !empty( $_FILES["Adjunto2Documento"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Adjunto2File"] = $files[0]["innername"];

			
			//insertamos los datos del asistente
			$id = $dbo->insert( $frm , $table , $key );

			//verificar si se notifica
			if( $frm["NotificarPush"] =="S" )
			{
				//notiifcar push
				if($frm["DirigidoA"]=="S"):
						//traer socios a los que les interesa la noticia
						$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccionEvento WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccionEvento.IDSocio AND SocioSeccionEvento.IDSeccionEvento = '" . $frm["IDSeccionEvento"] . "' and Socio.Token <> '' and Socio.Token <> '2byte' ";
		
						$qry_socios = $dbo->query( $sql_socios );
						while( $r_socios = $dbo->fetchArray( $qry_socios ) )
						{
							$users = array( array( "id" => $r_socios["IDSocio"],
								"idclub"=>$frm["IDClub"], 
								"registration_key"=>$r_socios["Token"] ,
								"deviceType"=>$r_socios["Dispositivo"] )
		
							);
		
							$message = ucwords( strtolower( $frm["Titular"] ) );		
		
							SIMUtil::sendAlerts($users, $message);
							
							$sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle) Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Socio','".$custom["tipo"]."', '".$custom["titulo"]."', '".$message."', '".$custom["idmodulo"]."','".$custom["idseccion"]."', '".$custom["iddetalle"]."')");
						
						}//end while
				elseif($frm["DirigidoA"]=="E"): //Empleados
						//traer empleados a los que les interesa la noticia
						 $sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
						 $qry_empleados = $dbo->query( $sql_empleados );
						while( $r_empleados = $dbo->fetchArray( $qry_empleados ) )
						{
							
							$users = array( array( "id" => $r_empleadosv["IDUsuario"],
								"idclub"=>$frm["IDClub"], 
								"registration_key"=>$r_empleados["Token"] ,
								"deviceType"=>$r_empleados["Dispositivo"] )
		
							);
		
							$message = ucwords( strtolower( $frm["Titular"] ) );
		
							$custom["tipo"] = "evento";
							$custom["idseccion"] = (string)$frm["IDSeccionEvento"] ;
							$custom["iddetalle"] = (string)$frm["ID"];
							$custom["idmodulo"] = (string)"4";
							$custom["titulo"] = $frm["Titular"];
							
							SIMUtil::sendAlerts($users, $message, $custom,"Empleado");
							
						}//end while
				endif;					


			}//end if
			
			
			foreach($frm["IDTipoPago"] as $Pago_seleccionado):
				$sql_servicio_forma_pago = $dbo->query("Insert into EventoTipoPago (IDEvento, IDTipoPago) Values ('".$id."', '".$Pago_seleccionado."')");
			endforeach;
			
			SIMHTML::jsAlert("Registro Guardado Correctamente");
			SIMHTML::jsRedirect( $script.".php" );
		}
		else
			exit;
		
			
		break;
		
		
		case "edit":
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );	
		$view = "views/".$script."/form.php";
		$newmode = "update";
		$titulo_accion = "Actualizar";
		
	break ;
		
		case "update" :	
		
		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST);
			
			
			$files =  SIMFile::upload( $_FILES["EventoImagen"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["EventoImagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["EventoFile"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto1"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto1"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto2"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto2"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto2"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto3"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto3"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto3"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["FotoDestacada"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["FotoDestacada"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["FotoDestacada"] = $files[0]["innername"];
			
			
			$files =  SIMFile::upload( $_FILES["SWF"] , SWFEvento_DIR );
			if( empty( $files ) && !empty( $_FILES["SWF"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["SWF"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Adjunto1Documento"] , IMGEVENTO_DIR , "DOC" );
			if( empty( $files ) && !empty( $_FILES["Adjunto1Documento"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Adjunto1File"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Adjunto2Documento"] , IMGEVENTO_DIR , "DOC" );
			if( empty( $files ) && !empty( $_FILES["Adjunto2Documento"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Adjunto2File"] = $files[0]["innername"];


			
			$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"),"" );

			//verificar si se notifica
			if( $frm["NotificarPush"] =="S" )
			{
				//notiifcar push				
				if($frm["DirigidoA"]=="S"):
						//traer socios a los que les interesa la noticia
						 $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccionEvento WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccionEvento.IDSocio AND SocioSeccionEvento.IDSeccionEvento = '" . $frm["IDSeccionEvento"] . "'  and Socio.Token <> '' and Socio.Token <> '2byte' Order by IDSocio Desc";
						 //$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccionEvento WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccionEvento.IDSocio AND SocioSeccionEvento.IDSeccionEvento = '" . $frm["IDSeccionEvento"] . "'  and Socio.Token <> '' and Socio.Token <> '2byte' and Socio.IDSocio = '61803'";
						 
						$qry_socios = $dbo->query( $sql_socios );
						while( $r_socios = $dbo->fetchArray( $qry_socios ) )
						{
							$users = array( array( "id" => $r_socios["IDSocio"],
								"idclub"=>$frm["IDClub"], 
								"registration_key"=>$r_socios["Token"] ,
								"deviceType"=>$r_socios["Dispositivo"] )
		
							);
		
							$message = ucwords( strtolower( $frm["Titular"] ) );
		
							$custom["tipo"] = "evento";
							$custom["idseccion"] = (string)$frm["IDSeccionEvento"] ;
							$custom["iddetalle"] = (string)$frm["ID"];
							$custom["idmodulo"] = (string)"4";
							$custom["titulo"] = $frm["Titular"];
		
							SIMUtil::sendAlerts($users, $message, $custom);
							
							$sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle) Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Socio','".$custom["tipo"]."', '".$custom["titulo"]."', '".$message."', '".$custom["idmodulo"]."','".$custom["idseccion"]."', '".$custom["iddetalle"]."')");
						
						}//end while
				elseif($frm["DirigidoA"]=="E"): //Empleados
						//traer empleados a los que les interesa la noticia
						 $sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
						 $qry_empleados = $dbo->query( $sql_empleados );
						while( $r_empleados = $dbo->fetchArray( $qry_empleados ) )
						{
							
							$users = array( array( "id" => $r_empleadosv["IDUsuario"],
								"idclub"=>$frm["IDClub"], 
								"registration_key"=>$r_empleados["Token"] ,
								"deviceType"=>$r_empleados["Dispositivo"] )
		
							);
		
							$message = ucwords( strtolower( $frm["Titular"] ) );
		
							$custom["tipo"] = "evento";
							$custom["idseccion"] = (string)$frm["IDSeccionEvento"] ;
							$custom["iddetalle"] = (string)$frm["ID"];
							$custom["idmodulo"] = (string)"4";
							$custom["titulo"] = $frm["Titular"];
							
							SIMUtil::sendAlerts($users, $message, $custom,"Empleado");
							
						}//end while
				endif;						


			}//end if


			$delete_tipo_pago = $dbo->query("Delete From EventoTipoPago Where IDEvento = '".SIMNet::reqInt("id")."'");
			foreach($frm["IDTipoPago"] as $Pago_seleccionado):
				$sql_servicio_forma_pago = $dbo->query("Insert into EventoTipoPago (IDEvento, IDTipoPago) Values ('".SIMNet::reqInt("id")."', '".$Pago_seleccionado."')");
			endforeach;
			
			$frm = $dbo->fetchById( $table , $key , $id , "array" );
			
			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
			SIMHTML::jsAlert("Registro Guardado Correctamente");
			SIMHTML::jsRedirect( $script.".php?action=edit&id=".SIMNet::reqInt("id") );
		}
		else
			exit;
		
		
		
			
				
		
					
		
		break;

		case "search" :
			$view = "views/".$script."/list.php";
		break;
		
		case "DelImgNot":
			$campo = $_GET['cam'];
			if($campo=="SWF"){
				$doceliminar = SWFEvento_DIR.$dbo->getFields( "Evento" , "$campo" , "IDEvento = '" . $_GET[id] . "'" );
				unlink($doceliminar);
				$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert("SWF eliminado Correctamente");
			}else{
				$doceliminar = IMGEVENTO_DIR.$dbo->getFields( "Evento" , "$campo" , "IDEvento = '" . $_GET[id] . "'" );
				unlink($doceliminar);
				$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");	
			}
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id] );
			exit;
	break;
	
			case "InsertarCampoFormularioEvento":
				$frm = SIMUtil::varsLOG( $_POST );
				$id = $dbo->insert( $frm , "CampoFormularioEvento" , "IDCampoFormularioEvento" );
				SIMHTML::jsAlert("Registro Exitoso");
				SIMHTML::jsRedirect( $script.".php?action=edit&tabevento=formulario&id=".$frm[IDEvento] );	
				exit;
			break;
			
			case "ModificaCampoFormularioEvento":
						$frm = SIMUtil::varsLOG( $_POST );	
						$dbo->update( $frm , "CampoFormularioEvento" , "IDCampoFormularioEvento" , $frm["IDCampoFormularioEvento"] );			   
						SIMHTML::jsAlert("Modificacion Exitoso");
						SIMHTML::jsRedirect( $script.".php?action=edit&tabevento=formulario&id=".$frm[IDEvento] );	
						exit;
			break;
			
			 case "EliminaCampoFormularioEvento":
					$id = $dbo->query( "DELETE FROM CampoFormularioEvento WHERE IDCampoFormularioEvento   = '".$_GET["IDCampoFormularioEvento"]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( $script.".php?action=edit&tabevento=formulario&id=".$_GET["id"] );	 
					exit;
			break;
			
			case "DelDocNot":				
				$campo = $_GET['cam'];
				$doceliminar = IMGEVENTO_DIR.$dbo->getFields( "Evento" , "$campo" , "IDEvento = '" . $_GET[id] . "'" );
				unlink($doceliminar);
				$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert("Archivo Eliminado Correctamente");					
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id] );
				exit;
			break;
		
		
		default:
			$view = "views/".$script."/list.php";
		
		
		
		
	
	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>