 <?

	SIMReg::setFromStructure(array(
		"title" => "Documento",
		"table" => "Documento",
		"key" => "IDDocumento",
		"mod" => "Documento"
	));


	$script = "documentos";

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
				
				$ids=$frm["IDTipoSocio"];
				$grupo="";
                  for ($i=0;$i<count($ids);$i++)    
                   {    
                   $grupo.=$ids[$i]."|||";
                   } 
                      $frm["IDTipoSocio"]="$grupo";

				//UPLOAD de imagenes
				if (isset($_FILES)) {

					$files =  SIMFile::upload($_FILES["Archivo1"], DOCUMENTO_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Archivo1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Archivo1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Icono"], DOCUMENTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Icono"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Icono"] = $files[0]["innername"];
				} //end if				

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {

					$frm["IDModulo"] = 6;
					$frm["TipoNotificacion"] = 'documentos';
					$frm["Mensaje"] = "Se ha creado un nuevo documento: " . $frm["Nombre"];

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
	                        $ids=$frm["IDTipoSocio"];
				$grupo="";
                  for ($i=0;$i<count($ids);$i++)    
                   {    
                   $grupo.=$ids[$i]."|||";
                   } 
                      $frm["IDTipoSocio"]="$grupo";
				//UPLOAD de imagenes
				if (isset($_FILES)) {


					$files =  SIMFile::upload($_FILES["Archivo1"], DOCUMENTO_DIR, "DOC");
					if (empty($files) && !empty($_FILES["Archivo1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Archivo1"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Icono"], DOCUMENTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Icono"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Icono"] = $files[0]["innername"];
				} //end if								

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				//verificar si se notifica
				if ($frm["NotificarPush"] == "S") {

					$frm["IDModulo"] = 6;
					$frm["TipoNotificacion"] = 'documentos';
					$frm["Mensaje"] = "Se ha actualizado el documento: " . $frm["Nombre"];

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

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMHTML::jsAlert('RegistroGuardadoCorrectamente'.$grupo);
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			} else
				exit;

			break;

		case "search":
			$view = "views/" . $script . "/list.php";
			break;

		case "delDoc":
			$foto = $_GET['doc'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = DOCUMENTO_DIR . $foto;
			unlink($filedelete);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'DocumentoEliminadoCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;

		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = DOCUMENTO_DIR . $foto;
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
