 <?

	SIMReg::setFromStructure(array(
		"title" => "Clasificadofuncionario",
		"table" => "Clasificado",
		"key" => "IDClasificado",
		"mod" => "Clasificado"
	));


	$script = "clasificados2";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");


	//Verificar permisos
	SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




	switch (SIMNet::req("action")) {

		case "add":
			$view = "views/" . $script . "/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;

		case "insert":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				//UPLOAD de imagenes
				if (isset($_FILES)) {

					$files =  SIMFile::upload($_FILES["Foto1"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto4"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto4"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto4"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto5"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto5"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto5"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto6"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto6"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto6"] = $files[0]["innername"];
				} //end if

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {

					$frm["IDModulo"] = 46;
					$frm["TipoNotificacion"] = 'calsificados';
					$frm["Mensaje"] = "Se ha creado un nuevo clasificado: " . $frm["Nombre"];

					//notiifcar push
					//traer socios a los que les interesa la noticia
					$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Token <> '' and Token <> '2byte'";
					$qry_socios = $dbo->query($sql_socios);

					while ($r_socios = $dbo->fetchArray($qry_socios)) {
						SIMUtil::envia_cola_notificacion($r_socios, $frm);

						//Guardo el log
						$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
								Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . $frm["Titular"] . "', '" . $frm["Mensaje"] . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $id . "')");
					}
				} //end if

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php");
			} else
				exit;

			break;


		case "edit":
			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";

			break;

		case "update":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				//UPLOAD de imagenes
				if (isset($_FILES)) {


					$files =  SIMFile::upload($_FILES["Foto1"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto4"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto4"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto4"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto5"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto5"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto5"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto6"], CLASIFICADOS_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto6"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto6"] = $files[0]["innername"];
				} //end if

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				if ($frm["NotificarPush"] == "S") {
					/* $Mensaje = "Se ha creado un nuevo clasificado: " . $Nombre;
						//Consulto que socio se les envia la notificacion
						$sql_socio_clasif = "Select S.IDSocio
												From Socio S, SocioSeccionClasificados SS
												Where S.IDSocio=SS.IDSocio And SS.IDSeccionClasificados = '".$frm["IDSeccionClasificados"]."'";

						$result_socio_clasif =	$dbo->query($sql_socio_clasif);
						while($row_socio_clasif = $dbo->fetchArray($result_socio_clasif)):
							$array_id_socio[]=$row_socio_clasif["IDSocio"];
						endwhile;
						if(count($array_id_socio)>0):
							$IDSocio = implode(",",$array_id_socio);
							//SIMUtil::enviar_notificacion_push_clasificado($frm["IDClub"],$IDSocio,$Mensaje,SIMNet::reqInt("id"));
						endif; */

					$frm["IDModulo"] = 46;
					$frm["TipoNotificacion"] = 'calsificados';
					$frm["Mensaje"] = "Se ha actualizado el estado del clasificado: " . $frm["Nombre"];

					//notiifcar push
					//traer socios a los que les interesa la noticia
					$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Token <> '' and Token <> '2byte'";
					$qry_socios = $dbo->query($sql_socios);

					while ($r_socios = $dbo->fetchArray($qry_socios)) {
						SIMUtil::envia_cola_notificacion($r_socios, $frm);

						//Guardo el log
						$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
							Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . $frm["Titular"] . "', '" . $frm["Mensaje"] . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $id . "')");
					}
				}

				//verificar si se notifica
				/*
					if( $frm["NotificarPush"] =="S" )
					{
						$frm["IDModulo"] = 46;
						$frm["TipoNotificacion"] = 'calsificados';
						$frm["Mensaje"] = "Se ha actualizado el clasificado: ".$frm["Nombre"];

							//notiifcar push
							//traer socios a los que les interesa la noticia
							$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Token <> '' and Token <> '2byte'";
							$qry_socios = $dbo->query( $sql_socios );

							while( $r_socios = $dbo->fetchArray( $qry_socios ) )
							{
								SIMUtil::envia_cola_notificacion($r_socios,$frm);

								//Guardo el log
								$sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
								Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Socio','".$frm["TipoNotificacion"]."', '".$frm["Titular"]."', '".$frm["Mensaje"]."', '".$frm["IDModulo"]."','".$frm["IDSeccion"]."', '".$id."')");
							}

					}//end if
          */

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			} else
				exit;

			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = SERVICIO_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			break;


		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = CLASIFICADOS_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
