 <?

	SIMReg::setFromStructure( array(
						"title" => "Pqr's",
						"table" => "Pqr",
						"key" => "IDPqr",
						"mod" => "Pqr"
	) );


	$script = "pqr";

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

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );

			//UPLOAD de imagenes
			if(isset($_FILES)){

				$files =  SIMFile::upload( $_FILES["Archivo1"] , PQR_DIR , "DOC" );
				if( empty( $files ) && !empty( $_FILES["Archivo1"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Archivo1"] = $files[0]["innername"];




			}//end if

			$sql_max_numero = string;
			$sql_max_numero = "Select MAX(Numero) as NumeroMaximo From Pqr Where IDClub = '".$frm["IDClub"]."'";
			$result_numero = $dbo->query($sql_max_numero);
			$row_numero = $dbo->fetchArray($result_numero);
			$siguiente_consecutivo = (int)$row_numero["NumeroMaximo"]+1;
			$frm["Numero"] = $siguiente_consecutivo;

					//insertamos los datos
					$id = $dbo->insert( $frm , $table , $key );

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
					$frm = SIMUtil::varsLOG( $_POST );




					//Quito los campos que no necesito que se actualicen
					unset($frm["Asunto"]);
					unset($frm["Descripcion"]);

					$IDAreaAnt = $frm["IDAreaAnt"];
          $FechaSeguimientoAnt = $frm["FechaSeguimientoAnt"];
          $FechaSeguimiento = $frm["FechaSeguimiento"];
					$IDArea=$frm["IDArea"];
          $IDAreaInteres=$frm["IDAreaInteres"];
					$IDPqrEstadoAnt = $frm["IDPqrEstadoAnt"];
					$IDPqrEstado=$frm["IDPqrEstado"];


					//UPLOAD de imagenes
					if(isset($_FILES)){


						$files =  SIMFile::upload( $_FILES["Archivo1"] , PQR_DIR , "DOC" );
						if( empty( $files ) && !empty( $_FILES["Archivo1"]["name"] ) )
							SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

						$frm["Archivo1"] = $files[0]["innername"];


					}//end if






					$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );
					$frm = $dbo->fetchById( $table , $key , $id , "array" );




					$notificar_cliente=$_POST["NotificarCliente"];
					if (!empty($_POST[Cuerpo])){

            //UPLOAD de imagenes
  					if(isset($_FILES["ArchivoRespuesta"])){


  						$files =  SIMFile::upload( $_FILES["ArchivoRespuesta"] , PQR_DIR , "DOC" );
  						if( empty( $files ) && !empty( $_FILES["ArchivoRespuesta"]["name"] ) )
  							     SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

  						$frm["Archivo"] = $files[0]["innername"];


  					}//end if


						$sql_inserta_respuesta="INSERT INTO Detalle_Pqr (IDPqr, IDUsuario, Fecha, Respuesta, Archivo, UsuarioTrCr, FechaTrCr)
												VALUES ('".$frm[IDPqr]."','".SIMUser::get("IDUsuario")."','".date("Y-m-d")."','".$_POST[Cuerpo]."','".$frm["Archivo"]."','Admin',NOW())";
						$dbo->query($sql_inserta_respuesta);

						if ($notificar_cliente=="S"):
							//Averiguo el nombre del modulo del pqr
							$nombre_modulo = $dbo->getFields( "ClubModulo" , "Titulo" , "IDModulo = '15' and IDClub = '".$frm["IDClub"]."'" );
							if(empty($nombre_modulo))
								$nombre_modulo = "Pqr";


              //Para consado el mensaje debe ser distinto
              if($frm["IDClub"]==51){
                $Mensaje = "Cordial Saludo, se ha dado respuesta a su ".$nombre_modulo.", por favor ingrese al correo electrónico para conocer mas detalles.";
              }
              else{
                $Mensaje = "Cordial Saludo, se ha dado respuesta a su ".$nombre_modulo.", por favor ingrese al app para conocer mas detalles.";
              }

							SIMUtil::envia_respuesta_cliente($frm,SIMNet::reqInt("id"),$_POST[Cuerpo],$frm["IDClub"],$IDAreaInteres);
							SIMUtil::enviar_notificacion_push_general($frm["IDClub"],$frm["IDSocio"],$Mensaje,15,$frm["IDPqr"]);

						endif;


					}

					//Si se reasiga el pqr envio el mail de confirmación
					if($IDAreaAnt!=$IDArea){
						$nueva_area =$dbo->getFields( "Area" , "Nombre" , "IDArea = '".$IDArea."'" );
						$sql_inserta_respuesta="INSERT INTO Detalle_Pqr (IDPqr, IDUsuario, Fecha, Respuesta,MostrarSocio,UsuarioTrCr, FechaTrCr)
												VALUES ('".$frm[IDPqr]."','".SIMUser::get("IDUsuario")."','".date("Y-m-d")."','Se asigno al area: ".$nueva_area."','N','Admin',NOW())";
						$dbo->query($sql_inserta_respuesta);

						SIMUtil::noticar_nuevo_pqr(SIMNet::reqInt("id"),"Re-asignacion");
					}

          if($FechaSeguimientoAnt!=$FechaSeguimiento && $FechaSeguimiento!=""){
						$nueva_area =$dbo->getFields( "Area" , "Nombre" , "IDArea = '".$IDArea."'" );
						$sql_inserta_respuesta="INSERT INTO Detalle_Pqr (IDPqr, IDUsuario, Fecha, Respuesta,MostrarSocio,UsuarioTrCr, FechaTrCr)
												VALUES ('".$frm[IDPqr]."','".SIMUser::get("IDUsuario")."','".date("Y-m-d")."','Se asigno Fecha de seguimiento: ".$FechaSeguimiento."','N','Admin',NOW())";
						$dbo->query($sql_inserta_respuesta);

						//SIMUtil::noticar_seguimiento_pqr(SIMNet::reqInt("id"));
					}

					//Si se cambia de estado el pqr guardo en la bitacora
					if($IDPqrEstadoAnt!=$IDPqrEstado){
						$nuevo_estado =$dbo->getFields( "PqrEstado" , "Nombre" , "IDPqrEstado = '".$IDPqrEstado."'" );
						$sql_inserta_respuesta="INSERT INTO Detalle_Pqr (IDPqr, IDUsuario, Fecha, Respuesta,MostrarSocio,UsuarioTrCr, FechaTrCr)
												VALUES ('".$frm[IDPqr]."','".SIMUser::get("IDUsuario")."','".date("Y-m-d")."','Se cambio estado: ".$nuevo_estado."','N','Admin',NOW())";
						$dbo->query($sql_inserta_respuesta);

            // Si se cierra envio correo al respondable que se cerro
            if($IDPqrEstado==3){
              	SIMUtil::noticar_cierre_pqr(SIMNet::reqInt("id"));
            }

					}






					SIMHTML::jsAlert("Registro Guardado Correctamente");
					SIMHTML::jsRedirect( $script.".php?action=edit&id=".SIMNet::reqInt("id") );
				}
				else
					exit;

		break;

		case "search" :
			$view = "views/".$script."/list.php";
		break;

			case "delfoto":
				$foto = $_GET['foto'];
				$campo = $_GET['campo'];
				$id = $_GET['id'];
				$filedelete = SERVICIO_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( $script.".php?action=edit&id=".SIMNet::reqInt("id") );
			break;


		case "delDoc":
				$foto = $_GET['doc'];
				$campo = $_GET['campo'];
				$id = $_GET['id'];
				$filedelete = PQR_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = ".$_GET[id]."   LIMIT 1 ;");
				SIMHTML::jsAlert("Documento Eliminado Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id."" );
		break;


		default:
			$view = "views/".$script."/list.php";





	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
