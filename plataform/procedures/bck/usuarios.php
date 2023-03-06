 <?

	SIMReg::setFromStructure( array(
						"title" => "Usuario",
						"table" => "Usuario",
						"key" => "IDUsuario",
						"mod" => "Usuario"
	) );


	$script = "usuarios";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );


  function copiar_archivo(&$frm,$file) {
    $filedir=SOCIOPLANO_DIR;
    $nuevo_nombre = rand(0,1000000). "_".date("Y-m-d")."_".$file['file']['name'];
    if (copy($file['file']['tmp_name'], "$filedir/".$nuevo_nombre) ) {
      echo "File : ".$file['file']['name']."... ";
      echo "Size :".$file['file']['size']." Bytes ... ";
      echo "Status : Transfer Ok ...<br>";
      return $nuevo_nombre;

    }
    else{
      echo "error";
    }
  }

  function get_data($nombrearchivo,$file,$IGNORE_FIRTS_ROW,$FIELD_TEMINATED='',$field='',$IDClub){

  $dbo =& SIMDB::get();

  $numregok = 0;
  require_once LIBDIR."excel/PHPExcel-1.8/Classes/PHPExcel.php";
  $archivo = $file;
  $inputFileType = PHPExcel_IOFactory::identify($archivo);
  $objReader = PHPExcel_IOFactory::createReader($inputFileType);
  $objPHPExcel = $objReader->load($archivo);
  $sheet = $objPHPExcel->getSheet(0);
  $highestRow = $sheet->getHighestRow();
  $highestColumn = $sheet->getHighestColumn();
  for ($row = 2; $row <= $highestRow; $row++){
      $NumeroDocumento =  $sheet->getCell("A".$row)->getValue();
      $Nombre =  $sheet->getCell("B".$row)->getValue();
      $Telefono = $sheet->getCell("C".$row)->getValue();
      $Usuario =  utf8_decode($sheet->getCell("D".$row)->getValue());
      $Clave =  utf8_decode($sheet->getCell("E".$row)->getValue());
      $Correo =  $sheet->getCell("F".$row)->getValue();
      $Autorizado =  $sheet->getCell("G".$row)->getValue();
      $PemiteReservar =  $sheet->getCell("H".$row)->getValue();
      $Cargo =  $sheet->getCell("I".$row)->getValue();
      $Perfil =  $sheet->getCell("J".$row)->getFormattedValue();

        //if(is_numeric($NumeroDocumento) && !empty($NumeroDocumento)){
        if(!empty($NumeroDocumento)){

              //Consulto Socio
              $sql_socio = "SELECT IDUsuario
                      From Usuario
                      Where IDClub = '".$IDClub."' and NumeroDocumento = '".$NumeroDocumento."' Limit 1";


              $result_socio = $dbo->query($sql_socio);

              if($dbo->rows($result_socio)>0):
                //Editar datos Socio

                $sql_edit_socio = "UPDATE Usuario Set Nombre = '".$Nombre."', Telefono = '".$Telefono."', Email = '".$Correo."', Autorizado = '".$Autorizado."'
                                   Where NumeroDocumento = '".$NumeroDocumento."' and IDClub = '".$IDClub."'";

                //echo "<br>Editar";
                //echo "<br>" . $sql_edit_socio;
                //exit;
                $dbo->query($sql_edit_socio);
                $numregok++;

              else:

                if(!empty($Usuario) && !empty($Clave) ){
                  //Crear Socio
                  $sql_inserta_socio = "INSERT INTO Usuario(IDClub,NumeroDocumento, Nombre, Telefono,User, Password, Email, Autorizado, Permiso, PermiteReservar, Cargo, Activo,UsuarioTrCr,FechaTrCr)
                                Values ('".$IDClub."','".$NumeroDocumento."','".$Nombre."','".$Telefono."','".$Usuario."',sha1('".$Clave."'),'".$Email."','".$Autorizado."','L','".$PermiteReservar."','".$Cargo."','S', 'Archivo Plano: ".$nombrearchivo."',NOW())";
                  //echo "<br>Crear ";
                  //echo "<br>" . $sql_inserta_socio;
                  //exit;
                  $dbo->query($sql_inserta_socio);
                  $numregok++;
                }
                else{
                  echo "<br>" . "Falta la columna de usuario y clave a:".$NumeroDocumento ;
                }

              endif;



        }
        else{
          echo "<br>" . "El numero de documento debe ser numerico: " . $NumeroDocumento ;
        }


        $cont++;
    } // end for
      fclose($fp);
  return array("Numregs"=>$cont,"RegsOK"=>$numregok);

  }



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
			$frm = SIMUtil::varsLOG( $_POST);



			//Validamos que el usuario no exista
			$SqlUsuario = "SELECT * FROM Usuario WHERE User = '" . $frm["User"]  ."'";
			$QryUsuario = $dbo -> query( $SqlUsuario );
			$NumUsuario = $dbo -> rows( $QryUsuario );
			if( $NumUsuario > 0 )
			{
				SIMHTML::jsAlert("El Usuario " . $frm["User"] . " ya Existe");
				SIMHTML::jsRedirect( $script.".php?action=add" );
				exit;
			}

			if( $frm["Password"] <> $frm["RePassword"] )
			{
				SIMHTML::jsAlert("La contraseña y su confirmación deben ser iguales");
				SIMHTML::jsRedirect( $script.".php?action=add" );
				exit;
			}

			//UPLOAD de imagenes
			if(isset($_FILES)){
				$files =  SIMFile::upload( $_FILES["Foto"] , USUARIO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

				$frm["Foto"] = $files[0]["innername"];

			}//end if

      foreach($_POST["SedeUsuario"] as $id_sede):
          $sede_id.="|".$id_sede."|";
      endforeach;
      $frm["IDCursoSede"]=$sede_id;

			$id = SIMUser::insert( $frm , $table , $key , "Password" );



			if(count($_POST["PerfilUsuario"])>0){
				$borro_perfil_usuarios=$dbo->query("Delete from  UsuarioPerfil Where IDUsuario = '".$id."'");
				foreach($_POST["PerfilUsuario"] as $valor_perfil){
					$inserta_perfil=$dbo->query("Insert into UsuarioPerfil (IDUsuario, IDPerfil) Values ('".$id."','".$valor_perfil."')");
				}
			}

				//Generar Codigo de barras
				$parametros_codigo_barras = $frm[NumeroDocumento];

        if($frm[IDClub]==38 ): // Club Colombia el doc y el caracter punto y coma
					$parametros_codigo_barras .= ";";
        elseif($frm[IDClub]==10):
          $parametros_codigo_barras = $frm["CodigoUsuario"];
				endif;


				$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras_empleado($parametros_codigo_barras,$id);
				//actualizo codigo barras
				$update_codigo=$dbo->query("update Usuario set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDUsuario = '".$id."'");

				//Generar Codigo QR
				$frm["CodigoQR"]=SIMUtil::generar_carne_qr_empleado($frm[IDUsuario],$parametros_codigo_barras);
				$update_codigo=$dbo->query("update Usuario set CodigoQR = '".$frm["CodigoQR"]."' Where IDUsuario = '".$id."'");


			$id_actualizar = SIMUser::update( $frm , $table , $key , SIMNet::reqInt("id") , "Password" , array( "Password" ) );
			$frm = $dbo->fetchById( $table , $key , $id , "array" );

			SIMHTML::jsAlert("Registro Guardado Correctamente");
			SIMHTML::jsRedirect( $script.".php?action=edit&id=".$id );
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

			//Validamos que el usuario no exista
			$SqlUsuario = "SELECT * FROM Usuario WHERE User = '" . $frm["User"]  ."' AND IDUsuario <> '" . $_GET["id"] ."'";
			$QryUsuario = $dbo -> query( $SqlUsuario );
			$NumUsuario = $dbo -> rows( $QryUsuario );
			if( $NumUsuario > 0 )
			{
				SIMHTML::jsAlert("El Usuario " . $frm["User"] . " ya Existe");
				SIMHTML::jsRedirect( $script.".php?action=add" );
				exit;
			}

			if( $frm["Password"] <> $frm["RePassword"] )
			{
				SIMHTML::jsAlert("La contraseña y su confirmación deben ser iguales");
				SIMHTML::jsRedirect( $script.".php?action=add" );
				exit;
			}


			if(count($_POST["PerfilUsuario"])>0){
				$borro_perfil_usuarios=$dbo->query("Delete from  UsuarioPerfil Where IDUsuario = '".$_GET["id"]."'");
				foreach($_POST["PerfilUsuario"] as $valor_perfil){
					$inserta_perfil=$dbo->query("Insert into UsuarioPerfil (IDUsuario, IDPerfil) Values ('".$_GET["id"]."','".$valor_perfil."')");
				}
			}

					$borrar_modulo_perfil = $dbo->query("Delete From UsuarioArea Where IDUsuario = '".$_GET["id"]."'");
					foreach($_POST["AreaUsuario"] as $id_area):
						$sql_inserta_usuarioarea =$dbo->query("Insert into UsuarioArea (IDArea,IDUsuario ) Values ('".$id_area."', '".SIMNet::reqInt("id")."')");
					endforeach;

					$borrar_modulo_perfil_func = $dbo->query("Delete From UsuarioAreaFuncionario Where IDUsuario = '".$_GET["id"]."'");
					foreach($_POST["AreaUsuarioFuncionario"] as $id_area):
						$sql_inserta_usuarioarea =$dbo->query("Insert into UsuarioAreaFuncionario (IDArea,IDUsuario ) Values ('".$id_area."', '".SIMNet::reqInt("id")."')");
					endforeach;

          $sede_id="";
          foreach($_POST["SedeUsuario"] as $id_sede):
              $sede_id.="|".$id_sede."|";
          endforeach;
          $frm["IDCursoSede"]=$sede_id;




					 //Actualizo Servicios del usuario
					   $borro_perfil_usuarios=$dbo->query("Delete from  UsuarioServicio Where IDUsuario = '".$_GET["id"]."'");
					   $servicio_club = "Select SM.*
										From ServicioMaestro SM
										Where SM.IDServicioMaestro in (Select IDServicioMaestro From ServicioClub Where IDClub = '".$frm["IDClub"]."' and Activo = 'S')";

					  $result_servicio = $dbo->query($servicio_club);
					  while($r=$dbo->object($result_servicio)){
						  	$nombre_campo_id_servicio = "IDServicioMaestro".$r->IDServicioMaestro;
							 if (!empty($frm[$nombre_campo_id_servicio])):
								$activo_servicio="S";
							else:
								$activo_servicio="N";
							endif;
							if($activo_servicio=="S"):
								$id_servicio=$dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro = '" . $r->IDServicioMaestro."' and IDClub = '".$frm["IDClub"]."'");
								$array_id_servicio[]=$id_servicio;
								$inserta_perfil=$dbo->query("Insert into UsuarioServicio (IDUsuario, IDServicio) Values ('".$_GET["id"]."','".$id_servicio."')");
							endif;
					  }
				//	FIN Actualizo Servicios del usuario

				 //Actualizo Elementos del usuario
				 	if(count($array_id_servicio)>0){
						   $id_servicio_elemento = implode(",",$array_id_servicio);
						   $borro_perfil_usuarios=$dbo->query("Delete from  UsuarioServicioElemento Where IDUsuario = '".$_GET["id"]."'");
						   $servicio_elemento = "Select SE.*
											From ServicioElemento SE
											Where SE.IDServicio in (".$id_servicio_elemento.")";

						  $result_servicio_elemento = $dbo->query($servicio_elemento);
						  while($r=$dbo->object($result_servicio_elemento)){
								$nombre_campo_id_servicio_elemento = "IDServicioElemento".$r->IDServicioElemento;
								 if (!empty($frm[$nombre_campo_id_servicio_elemento])):
									$activo_servicio="S";
								else:
									$activo_servicio="N";
								endif;
								if($activo_servicio=="S"):
									$inserta_elemento=$dbo->query("Insert into UsuarioServicioElemento (IDUsuario, IDServicioElemento) Values ('".$_GET["id"]."','".$r->IDServicioElemento."')");
								endif;
						  };
					}
				//	FIN Actualizo Servicios del usuario

        //Actualizo Auxiliares del usuario
         if(count($array_id_servicio)>0){
              $id_servicio_elemento = implode(",",$array_id_servicio);
              $borro_perfil_aux=$dbo->query("Delete from  UsuarioAuxiliar Where IDUsuario = '".$_GET["id"]."'");
              $servicio_aux = "Select *
                     From Auxiliar A
                     Where A.IDServicio in (".$id_servicio_elemento.")";

             $result_aux = $dbo->query($servicio_aux);
             while($r=$dbo->object($result_aux)){
                $nombre_campo_id_aux = "IDAuxiliar".$r->IDAuxiliar;
                if (!empty($frm[$nombre_campo_id_aux])):
                 $activo_aux="S";
               else:
                 $activo_aux="N";
               endif;
               if($activo_aux=="S"):
                 $inserta_elemento=$dbo->query("Insert into UsuarioAuxiliar (IDUsuario, IDAuxiliar) Values ('".$_GET["id"]."','".$r->IDAuxiliar."')");
               endif;
             };
         }
       //	FIN Actualizo Auxiliar del usuario


				//UPLOAD de imagenes

				if(isset($_FILES) && !empty($_FILES["Foto"])){
					$files =  SIMFile::upload( $_FILES["Foto"] , USUARIO_DIR , "IMAGE" );
					if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
						SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );

					$frm["Foto"] = $files[0]["innername"];

				}//end if


				//Generar Codigo de barras
				$parametros_codigo_barras = $frm[NumeroDocumento];

				if($frm[IDClub]==38 ): // Club Colombia el doc y el caracter punto y coma
					$parametros_codigo_barras .= ";";
        elseif($frm[IDClub]==10):
          $parametros_codigo_barras = $frm["CodigoUsuario"];
				endif;


				$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras_empleado($parametros_codigo_barras,$frm[IDClub]);


				//Generar Codigo QR
				$frm["CodigoQR"]=SIMUtil::generar_carne_qr_empleado($frm[IDUsuario],$parametros_codigo_barras);


			$id = SIMUser::update( $frm , $table , $key , SIMNet::reqInt("id") , "Password" , array( "Password" ) );

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
				$filedelete = USUARIO_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id."" );
			break;

      case "cargarplano" :
  					$time_start = SIMUtil::getmicrotime();
  					$nombre_archivo = copiar_archivo($_POST,$_FILES);
  					if($nombre_archivo=="error"):
  						echo "Error Transfiriendo Archivo";
  						exit;
  					endif;

            if((int)$_POST["IDClub"]<=0){
              echo "Debe seleccionar un club";
  						exit;
            }



  					$result = get_data($nombre_archivo,SOCIOPLANO_DIR.$nombre_archivo,$_POST['IGNORELINE'],$_POST['FIELD_TEMINATED'],$_POST['field'],$_POST['IDClub']);
  					if($result["Numregs"] > 0){
  						echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
  				} // if($result["Numregs"] > 0){

  				$time_end = SIMUtil::getmicrotime();
  				$time = $time_end - $time_start;
  				$time = number_format($time,3);
  				SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
  				exit;
  		break;

		default:
			$view = "views/".$script."/list.php";





	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
