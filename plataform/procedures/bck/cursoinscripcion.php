 <?

	SIMReg::setFromStructure( array(
						"title" => "Inscripcion",
						"table" => "CursoInscripcion",
						"key" => "IDCursoInscripcion",
						"mod" => "Cursos"
	) );


	$script = "cursoinscripcion";

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
      $array_cursos[]=$frm["IDCursoCalendario"];

      if($frm["TipoInscripcion"]=="Trimestre"){
        $frm["IDCursoCalendarioTrimestre"];
        $array_otros=explode(",",$frm["IDCursoCalendarioTrimestre"]);
        foreach ($array_otros as $key => $value) {
          $array_cursos[]=$value;
        }
        if(count($array_cursos)!="3"){
            echo "Ocurrio un problema al calcular los proximos cursos";
            exit;
        }
      }

      foreach($array_cursos as $id_curso => $curso){
        $respuesta = SIMWebServiceApp::set_curso_inscribir($frm["IDClub"],$frm["IDSocio"], $frm["IDCursoHorario"],$curso,SIMUser::get( "IDUsuario" ),$frm["HoraDesde"],$frm["Cupos"],$frm["Valor"]);
        $mensaje_respuesta.="<br>".$respuesta["message"];
      }

      SIMNotify::capture( $mensaje_respuesta , "info alert-success" );
			//SIMHTML::jsAlert($respuesta["message"]);
			//SIMHTML::jsRedirect( $script.".php" );
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

		case "BuscarCurso":
			$frm = SIMUtil::varsLOG( $_POST );
      $resultado=SIMWebServiceApp::curso_buscar($frm["IDClub"],$frm["IDSocio"],$frm["IDCursoSede"],$frm["IDCursoTipo"],$frm["IDCursoEntrenador"]);
		break;



		default:
			$view = "views/".$script."/list.php";





	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


    if(SIMUser::get("IDPerfil") <= 1){
      $condicion_sede="";
    }
    else{
      $array_sede_usuario=explode("|",SIMUser::get("IDCursoSede"));
      foreach ($array_sede_usuario as $id_sede => $sede) {
        if(!empty($sede)){
          $array_id_sede[]=$sede;
        }
      }
      if(count($array_id_sede)>0)
        $id_consulta_sede=implode(",",$array_id_sede);
      else
        $id_consulta_sede=0;

      $condicion_sede=" and IDCursoSede in (".$id_consulta_sede.")  ";
    }


?>
