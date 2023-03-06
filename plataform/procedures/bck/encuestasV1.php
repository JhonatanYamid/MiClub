<?

	SIMReg::setFromStructure( array(
						"title" => "Encuesta",
						"table" => "Encuesta",
						"key" => "IDEncuesta",
						"mod" => "Encuesta"
	) );


	$script = "encuestas";

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

			$files =  SIMFile::upload( $_FILES["Imagen"] , BANNERAPP_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Imagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

			$frm["Imagen"] = $files[0]["innername"];



			$files =  SIMFile::upload( $_FILES["Adjunto1Documento"] , BANNERAPP_DIR , "DOC" );
			if( empty( $files ) && !empty( $_FILES["Adjunto1Documento"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

			$frm["Adjunto1File"] = $files[0]["innername"];

			$files =  SIMFile::upload( $_FILES["Adjunto2Documento"] , BANNERAPP_DIR , "DOC" );
			if( empty( $files ) && !empty( $_FILES["Adjunto2Documento"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

			$frm["Adjunto2File"] = $files[0]["innername"];


			//insertamos los datos del asistente
			$id = $dbo->insert( $frm , $table , $key );

// --------Inicio Notificación----------------------------

$nombre_club = string;
$nombre_club = $dbo->getFields( "Club" , "Nombre" , "IDClub = '" . $frm["IDClub"] . "'" );
$TituloNotificacion = "Notificaciones " . $nombre_club;

//verificar si se notifica
if( $frm["NotificarPush"] =="S" )
{

	$frm["IDModulo"] =58;
	$frm["TipoNotificacion"] ='encuestas'; //socio
	$frm["Mensaje"] =$frm["Introduccion"];
	if(empty($frm["Mensaje"])) $frm["Mensaje"]=".";

	//notiifcar push
	if($frm["DirigidoA"]=="S"):

		$frm["TipoUsuario"] ='S'; //socio

		/* if($frm["TipoSocio"]=="Socio")
		{
		 	$condicion_tipo=" and Socio.TipoSocio = '".$frm["TipoSocio"]."'";
		}
		elseif($frm["TipoSocio"]=="Estudiante")
		{
		 	$condicion_tipo=" and Socio.TipoSocio = '".$frm["TipoSocio"]."'";
		} */


		//traer socios a los que les interesa la encuesta
		$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "'  and Token <> '' and Token <> '2byte'  ";
		//  . $condicion_tipo;
		//$sql_socios = "SELECT * FROM Socio WHERE IDSocio = '141131'";

		$qry_socios = $dbo->query($sql_socios);

		while( $r_socios = $dbo->fetchArray( $qry_socios ) )
		{
			SIMUtil::envia_cola_notificacion($r_socios, $frm);
			//Guardo el log
			$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
			Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Socio','".$frm["TipoNotificacion"]."', '".$frm["Titular"]."', '".$frm["Mensaje"]."', '".$frm["IDModulo"]."','".$frm["IDSeccion"]."', '".$id."')");

		}//end while

	elseif($frm["DirigidoA"]=="E"): //Empleados
		$frm["TipoUsuario"] ='E'; //socio
		//traer empleados a los que les interesa la noticia
		$sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
		$qry_empleados = $dbo->query( $sql_empleados );

		while( $r_empleados = $dbo->fetchArray( $qry_empleados ) )
		{
			SIMUtil::envia_cola_notificacion($r_socios,$frm);
		}//end while
	endif;

}//end if

// --------Fin Notificación----------------------------

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


			$files =  SIMFile::upload( $_FILES["Imagen"] , BANNERAPP_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Imagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

			$frm["Imagen"] = $files[0]["innername"];

			$files =  SIMFile::upload( $_FILES["Adjunto1Documento"] , BANNERAPP_DIR , "DOC" );
			if( empty( $files ) && !empty( $_FILES["Adjunto1Documento"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

			$frm["Adjunto1File"] = $files[0]["innername"];

			$files =  SIMFile::upload( $_FILES["Adjunto2Documento"] , BANNERAPP_DIR , "DOC" );
			if( empty( $files ) && !empty( $_FILES["Adjunto2Documento"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

			$frm["Adjunto2File"] = $files[0]["innername"];



			$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"),"" );



			$frm = $dbo->fetchById( $table , $key , $id , "array" );
			
// --------Inicio Notificación----------------------------

$nombre_club = string;
$nombre_club = $dbo->getFields( "Club" , "Nombre" , "IDClub = '" . $frm["IDClub"] . "'" );
$TituloNotificacion = "Notificaciones " . $nombre_club;

//verificar si se notifica
if( $frm["NotificarPush"] =="S" )
{

	$frm["IDModulo"] =58;
	$frm["TipoNotificacion"] ='encuestas'; //socio
	$frm["Mensaje"] =$frm["Introduccion"];
	if(empty($frm["Mensaje"])) $frm["Mensaje"]=".";

	//notiifcar push
	if($frm["DirigidoA"]=="S"):

		$frm["TipoUsuario"] ='S'; //socio

		/* if($frm["TipoSocio"]=="Socio")
		{
		 	$condicion_tipo=" and Socio.TipoSocio = '".$frm["TipoSocio"]."'";
		}
		elseif($frm["TipoSocio"]=="Estudiante")
		{
		 	$condicion_tipo=" and Socio.TipoSocio = '".$frm["TipoSocio"]."'";
		} */


		//traer socios a los que les interesa la encuesta
		$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "'  and Token <> '' and Token <> '2byte'  ";
		//  . $condicion_tipo;
		//$sql_socios = "SELECT * FROM Socio WHERE IDSocio = '141131'";

		$qry_socios = $dbo->query($sql_socios);

		while( $r_socios = $dbo->fetchArray( $qry_socios ) )
		{
			SIMUtil::envia_cola_notificacion($r_socios, $frm);
			//Guardo el log
			$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
			Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Socio','".$frm["TipoNotificacion"]."', '".$frm["Titular"]."', '".$frm["Mensaje"]."', '".$frm["IDModulo"]."','".$frm["IDSeccion"]."', '".$id."')");

		}//end while

	elseif($frm["DirigidoA"]=="E"): //Empleados
		$frm["TipoUsuario"] ='E'; //socio
		//traer empleados a los que les interesa la noticia
		$sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
		$qry_empleados = $dbo->query( $sql_empleados );

		while( $r_empleados = $dbo->fetchArray( $qry_empleados ) )
		{
			SIMUtil::envia_cola_notificacion($r_socios,$frm);
		}//end while
	endif;

}//end if

// --------Fin Notificación----------------------------

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

		case "delfoto":
				$campo = $_GET['campo'];
				$doceliminar = BANNERAPP_DIR.$dbo->getFields( "Encuesta" , "$campo" , "IDEncuesta = '" . $_GET[id] . "'" );
				unlink($doceliminar);
				$dbo->query("UPDATE Encuesta SET $campo = '' WHERE IDEncuesta = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");

			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id] );
			exit;
	break;

			case "InsertarPregunta":
				$frm = SIMUtil::varsLOG( $_POST );
				$frm["Valores"]=trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
				$id = $dbo->insert( $frm , "Pregunta" , "IDPregunta" );
				SIMHTML::jsAlert("Registro Exitoso");
				SIMHTML::jsRedirect( $script.".php?action=edit&tabencuesta=formulario&id=".$frm[IDEncuesta] );
				exit;
			break;

			case "ModificaPregunta":
						$frm = SIMUtil::varsLOG( $_POST );
						$frm["Valores"]=trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
						$dbo->update( $frm , "Pregunta" , "IDPregunta" , $frm["IDPregunta"] );
						SIMHTML::jsAlert("Modificacion Exitoso");
						SIMHTML::jsRedirect( $script.".php?action=edit&tabencuesta=formulario&id=".$frm[IDEncuesta] );
						exit;
			break;

			case "EliminaPregunta":
					$id = $dbo->query( "DELETE FROM Pregunta WHERE IDPregunta   = '".$_GET["IDPregunta"]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( $script.".php?action=edit&tabencuesta=formulario&id=".$_GET["id"] );
					exit;
			break;

			case "EliminaRespuesta":
				for($i = 1; $i<= $_GET["cantidad"]; $i++)
					$id = $dbo->query("DELETE FROM EncuestaRespuesta WHERE IDEncuesta  = '".$_GET["IDEncuesta"]."' AND IDSocio = '".$_GET["IDSocio"]."' LIMIT 1" );
					
				SIMHTML::jsAlert("Eliminacion Exitoso");
				SIMHTML::jsRedirect( $script.".php?action=edit&tabencuesta=formulario&id=".$_GET["id"] );
				exit;
			break;

			case "InsertarNotificacionLocal":
				$frm = SIMUtil::varsLOG( $_POST );
				$frm["IDModulo"]=58;
				$frm["IDDetalle"]=$frm["IDDiagnostico"];
				foreach($frm["IDDia"] as $Dia_seleccion):
		        $array_dia []= $Dia_seleccion;
		    endforeach;
		    if(count($array_dia)>0):
		      $id_dia=implode("|",$array_dia) . "|";
		    endif;
		    $frm["Dias"]=$id_dia;
				$id = $dbo->insert( $frm , "NotificacionLocal" , "IDNotificacionLocal" );
				SIMHTML::jsAlert("Registro Exitoso");
				SIMHTML::jsRedirect( $script.".php?action=edit&tabencuesta=notificacionlocal&id=".$frm["IDDiagnostico"] );
				exit;
			break;

			case "ModificaNotificacionLocal":
					$frm = SIMUtil::varsLOG( $_POST );
					foreach($frm["IDDia"] as $Dia_seleccion):
			        $array_dia []= $Dia_seleccion;
			    endforeach;
			    if(count($array_dia)>0):
			      $id_dia=implode("|",$array_dia) . "|";
			    endif;
			    $frm["Dias"]=$id_dia;
					$dbo->update( $frm , "NotificacionLocal" , "IDNotificacionLocal" , $frm["IDNotificacionLocal"] );
					SIMHTML::jsAlert("Modificacion Exitoso");
					SIMHTML::jsRedirect( $script.".php?action=edit&tabencuesta=notificacionlocal&id=".$frm["IDDiagnostico"] );
					exit;
			break;

			 case "EliminaNotificacionLocal":
				 	$id = $dbo->query( "DELETE FROM NotificacionLocal WHERE IDNotificacionLocal   = '".$_GET["IDNotificacionLocal"]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( $script.".php?action=edit&tabencuesta=notificacionlocal&id=".$_GET["id"] );
					exit;
			break;

			case "DelDocNot":
				$campo = $_GET['cam'];
				$doceliminar = BANNERAPP_DIR.$dbo->getFields( "Encuesta" , "$campo" , "IDEncuesta = '" . $_GET[id] . "'" );
				unlink($doceliminar);
				$dbo->query("UPDATE Encuesta SET $campo = '' WHERE IDEncuesta = $_GET[id] LIMIT 1 ;");
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
