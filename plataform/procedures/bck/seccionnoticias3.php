 <?

	SIMReg::setFromStructure( array(
						"title" => "Secciones de Noticias",
						"table" => "Seccion3",
						"key" => "IDSeccion",
						"mod" => "Seccion"
	) );


	$script = "seccionnoticias3";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	//Verificar permisos
	SIMUtil::verificar_permiso( "Noticia" , SIMUser::get("IDPerfil") );

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
			$frm["IDPadre"] = SIMNet::post( "IDSeccion" );
			$frm["Ubicacion"] = implode(",",$frm["Ubicacion"]);

			$files =  SIMFile::upload( $_FILES["SeccionImagen"] , IMGSECCION_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["SeccionImagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );


			$frm["SeccionFile"] = $files[0]["innername"];

			//insertamos los datos del asistente
			$id = $dbo->insert( $frm , $table , $key );

			//Creo la seccion para todos los socios
			$IDClub = $_POST["IDClub"];
			$sql_soc = "SELECT * FROM Socio WHERE IDClub = '".$IDClub."' ";
			$result_soc = $dbo->query($sql_soc);
			while($row_soc = $dbo->fetchArray($result_soc)):
				//Verifico si ya el socio la tiene si no se la creo
				$insert_secc = "Insert into SocioSeccion (IDSocio, IDSeccion) Values ('".$row_soc["IDSocio"]."','".$id."')";
				$dbo->query($insert_secc);
			endwhile;

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
			$frm["IDPadre"] = SIMNet::post( "IDSeccion" );

			$files =  SIMFile::upload( $_FILES["SeccionImagen"] , IMGSECCION_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["SeccionImagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );


			$frm["SeccionFile"] = $files[0]["innername"];


			$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"));


			$frm = $dbo->fetchById( $table , $key , $id , "array" );

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
			$filedelete = DIRECTORIO_DIR.$foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert("Imagen Eliminada Correctamente");
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id."" );
		break;



		default:
			$view = "views/".$script."/list.php";





	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
