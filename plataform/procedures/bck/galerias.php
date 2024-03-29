 <?

	SIMReg::setFromStructure( array(
						"title" => "Galeria",
						"table" => "Galeria",
						"key" => "IDGaleria",
						"mod" => "Galeria"
	) );


	$script = "galerias";

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

		// establecer el arreglo con los datos a validar
		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_validacion ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST );

			if( empty( $_FILES["Foto"]["name"] ) ){
				$id = $dbo->insert( $frm , $table , $key );
			}
			else
			{
				$files =  SIMFile::upload( $_FILES["Foto"] , GALERIA_DIR , "IMAGE" );
				if( empty( $files ) )
				{
					SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
					print_form( $frm , "insert" , "Agregar Registro" );
					exit;
				}

				$frm["Foto"] = $files[0]["innername"];
				$frm["FotoName"] = $files[0]["innername"];
				$id = $dbo->insert( $frm , $table , $key );
			}

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

		/* TRAEMOS TODAS LAS FOTOS */
		$qry_fotos = $dbo->query( "SELECT * FROM FotoGaleria WHERE IDGaleria= '".SIMNet::reqInt("id")."' ;" );
		while( $r_fotos = $dbo->fetchArray( $qry_fotos ) )
			$array_fotos[ $r_fotos[IDFoto] ] = $r_fotos;



	break ;

		case "update" :

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas



			$frm =  $_POST ;

			if( empty( $_FILES["Foto"]["name"] ) )
			{
				$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"),  array( "Foto" ) );
			}//end if
			else
			{
				$files =  SIMFile::upload( $_FILES["Foto"] , GALERIA_DIR , "IMAGE" );
				if( empty( $files ) )
				{
					SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
					print_form( $frm , "insert" , "Agregar Registro" );
					exit;
				}

				$frm["Foto"] = $files[0]["innername"];
				$frm["FotoName"] = $files[0]["innername"];
				$frm["Foto"] = $files[0]["innername"];



				$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );


			}



			//verificar si se notifica
			if( $frm["NotificarPush"] =="S" )
			{
				//notiifcar push
				if($frm["DirigidoA"]=="S"):
						//traer socios a los que les interesa la noticia
						$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccionGaleria WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccionGaleria.IDSocio AND SocioSeccionGaleria.IDSeccionGaleria = '" . $frm["IDSeccionGaleria"] . "' and Socio.Token <> '' and Socio.Token <> '2byte' ";
						//$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccionGaleria WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccionGaleria.IDSocio AND SocioSeccionGaleria.IDSeccionGaleria = '" . $frm["IDSeccionGaleria"] . "' and Socio.Token <> '' and Socio.Token <> '2byte' and Socio.IDSocio = '5533' ";

						$qry_socios = $dbo->query( $sql_socios );
						while( $r_socios = $dbo->fetchArray( $qry_socios ) )
						{

							if($r_socios["Token"]!="2byte"){
								$users = array( array( "id" => $r_socios["IDSocio"],
									"idclub"=>$frm["IDClub"],
									"registration_key"=>$r_socios["Token"] ,
									"deviceType"=>$r_socios["Dispositivo"] )

								);

								$message =  $frm["Nombre"];
								$custom["tipo"] = "galeria";
								$custom["idseccion"] = (string)$frm["IDSeccionGaleria"] ;
								$custom["iddetalle"] = (string)$frm["ID"];
								$custom["idmodulo"] = (string)"5";
								$custom["titulo"] = $frm["Nombre"];

								SIMUtil::sendAlerts($users, $message, $custom);
								$sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle) Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Socio','".$custom["tipo"]."', '".utf8_decode($custom["titulo"])."', '".utf8_decode($message)."', '".$custom["idmodulo"]."','".$custom["idseccion"]."', '".$custom["iddetalle"]."')");
							}

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

								$message =  $frm["Nombre"];
								$custom["tipo"] = "galeria";
								$custom["idseccion"] = (string)$frm["IDSeccionGaleria"] ;
								$custom["iddetalle"] = (string)$frm["ID"];
								$custom["idmodulo"] = (string)"5";
								$custom["titulo"] = $frm["Nombre"];

							SIMUtil::sendAlerts($users, $message, $custom,"Empleado");

						}//end while
				endif;


			}//end if




			$frm = $dbo->fetchById( $table , $key , $id , "array" );

			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
			SIMHTML::jsAlert("Registro Guardado Correctamente");
			SIMHTML::jsRedirect( $script.".php?action=edit&id=".SIMNet::reqInt("id") );
		}
		else
			exit;

		break;


		case "delfoto":
				$foto = $_GET['foto'];
				$campo = $_GET['campo'];
				$id = $_GET['id'];
				$filedelete = GALERIA_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."" );
			break;


	case "Galeria":

			$files =  SIMFile::upload( $_FILES , GALERIA_DIR , "IMAGE" );

			foreach( $files as $llave => $archivo ){

        $imagen=$archivo["innername"];
        $path = "../file/galeria/".$imagen;
        $array_extension=explode(".",$imagen);
        $extension=$array_extension["1"];
        $path_destino = GALERIA_DIR.$imagen;
        $result=SIMFile::redimensionarIMAGEN ($path, $path_destino, "340", "350", "ancho",$extension);

				$sql_foto = "INSERT INTO FotoGaleria ( IDGaleria, Nombre, Foto, FotoSize, FotoType, FechaTrCr, UsuarioTrCr ) VALUES ( '" . $_POST["ID"] . "','" . $archivo["origname"] . "','" . $archivo["innername"] . "','" . $archivo["size"] . "','" . $archivo["type"] . "',NOW(), '" . SIMUser::get( "Nombre" ) . "' )";
				$dbo->query( $sql_foto );
			}//end for

			if( empty( $files ) )
			{

				$frm = $dbo->fetchById( $table , $key , $_POST["ID"] , "array" );
				SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
				SIMHTML::jsAlert("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.");
				print_form( $frm , "insert" , "Agregar Registro" );
				exit;
			}//end if

			SIMHTML::jsAlert("Imagen Cargada Correctamente");
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_POST["ID"] );
	break;

	case "delfotogaleria" :
		$foto = $dbo->getFields( "FotoGaleria" , array( "Nombre", "IDFoto", "Foto") , "IDFoto = '$_GET[IDFoto]'");
		$archivo = GALERIA_DIR . "/" .$foto["Foto"];
		unlink( $archivo );
		$dbo->query("DELETE FROM FotoGaleria WHERE IDFoto = '$_GET[IDFoto]' ");


		SIMHTML::jsAlert("Imagen Eliminada Correctamente");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id] );
    break;


		case "search" :
			$view = "views/".$script."/list.php";
		break;


		default:
			$view = "views/".$script."/list.php";





	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
