 <?

	SIMReg::setFromStructure(array(
		"title" => "Socio",
		"table" => "Socio",
		"key" => "IDSocio",
		"mod" => "Socio"
	));





	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//Verificar permisos
	SIMUtil::verificar_permiso("Reportes", SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	$script = "reporteinvitacionesespeciales";


	switch (SIMNet::req("action")) {

		case "insert":
			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$frm[IDPais] = $frm[IDPais];
				$frm[IDClub] = SIMUser::get("club");
				$frm[IDDepartamento] = $frm[IDDepartamento];
				$frm[IDCiudad] = $frm[IDCiudad];
				$frm[Clave] = sha1($frm[Clave]);

				$comprobar_correo = $dbo->fetchAll("Socio", "(Email = '" . $frm[Email] . "' or NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
				if (!empty($comprobar_correo[IDSocio])) :

					SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Error:Yaexisteelemailoeldocumentoenesteclub,porfavorverifique', LANGSESSION));
					SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $frm[ID] . "#Socio");
					exit;
				endif;

				//UPLOAD de imagenes
				if (isset($_FILES)) {
					$files =  SIMFile::upload($_FILES["Foto"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto"] = $files[0]["innername"];
				} //end if	


				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);


				//Actualizo Secciones Noticia
				foreach ($frm[SocioSeccion] as $id_seccion) :
					$sql_interta_seccion = $dbo->query("Insert into SocioSeccion (IDSocio, IDSeccion) Values ('" . SIMNet::reqInt("id") . "', '" . $id_seccion . "')");
				endforeach;

				//Actualizo Secciones Evento
				foreach ($frm[SocioSeccionEvento] as $id_seccion_evento) :
					$sql_interta_seccion = $dbo->query("Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('" . SIMNet::reqInt("id") . "', '" . $id_seccion_evento . "')");
				endforeach;

				//Actualizo Secciones Galeria
				foreach ($frm[SocioSeccionGaleria] as $id_seccion_galeria) :
					$sql_interta_seccion = $dbo->query("Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('" . SIMNet::reqInt("id") . "', '" . $id_seccion_galeria . "')");
				endforeach;

				//Generar Codigo de barras
				$parametros_codigo_barras = $id . "-" . $frm[Nombre] . "-" . $frm[NumeroDocumento];
				$frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $id);
				//actualizo codigo barras
				$update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $id . "'");


				SIMHTML::jsRedirect("socios.php?m=insertarexito");
			} else
				print_form($_POST, "insert", "Agregar Registro");
			break;



		case "update":
			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$frm[IDPais] = $frm[IDPais];
				$frm[IDClub] = SIMUser::get("club");
				$frm[IDDepartamento] = $frm[IDDepartamento];
				$frm[IDCiudad] = $frm[IDCiudad];

				if ($frm[Clave] != $frm[ClaveAnt])
					$frm[Clave] = sha1($frm[Clave]);

				//Compruebo que no exista el correo				
				if ($frm[Email] != $frm[EmailAnt]) :
					$comprobar_correo = $dbo->fetchAll("Socio", "(Email = '" . $frm[Email] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
					if (!empty($comprobar_correo[IDSocio])) :

						SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Error:Yaexisteelemailenesteclub,porfavorverifique', LANGSESSION));
						SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $frm[ID]);
						exit;
					endif;
				endif;

				//Compruebo que no exista el documento
				if ($frm[NumeroDocumento] != $frm[NumeroDocumentoAnt]) :
					$comprobar_correo = $dbo->fetchAll("Socio", "(NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
					if (!empty($comprobar_correo[IDSocio])) :

						SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Error:Yaexisteelnumerodedocumentoenesteclub,porfavorverifique', LANGSESSION));
						SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $frm[ID]);
						exit;
					endif;
				endif;


				//UPLOAD de imagenes
				if (isset($_FILES)) {
					$files =  SIMFile::upload($_FILES["Foto"], SOCIO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto"] = $files[0]["innername"];
				} //end if								

				//Generar Codigo de barras
				$parametros_codigo_barras = $frm[IDClub] . "-" . $frm[Nombre] . "-" . $frm[NumeroDocumento];
				$frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $frm[IDClub]);

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				/*
				//Actualizo Secciones Noticia
				$sql_borra_seccion = $dbo->query("Delete From SocioSeccion Where IDSocio  = '".SIMNet::reqInt("id")."'");
				foreach($frm[SocioSeccion] as $id_seccion):
					$sql_interta_seccion=$dbo->query("Insert into SocioSeccion (IDSocio, IDSeccion) Values ('".SIMNet::reqInt("id")."', '".$id_seccion."')");
				endforeach;
				
				//Actualizo Secciones Evento
				$sql_borra_seccion_evento = $dbo->query("Delete From SocioSeccionEvento Where IDSocio  = '".SIMNet::reqInt("id")."'");
				foreach($frm[SocioSeccionEvento] as $id_seccion_evento):
					$sql_interta_seccion=$dbo->query("Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('".SIMNet::reqInt("id")."', '".$id_seccion_evento."')");
				endforeach;
				
				//Actualizo Secciones Galeria
				$sql_borra_seccion_galeria = $dbo->query("Delete From SocioSeccionGaleria Where IDSocio  = '".SIMNet::reqInt("id")."'");
				foreach($frm[SocioSeccionGaleria] as $id_seccion_galeria):
					$sql_interta_seccion=$dbo->query("Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('".SIMNet::reqInt("id")."', '".$id_seccion_galeria."')");
				endforeach;
				*/



				$frm = $dbo->fetchById($table, $key, $id, "array");
				SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
				SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
			} else
				print_form($_POST, "update",  "Realizar Cambios");
			break;


		case "edit":
			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";

			break;

		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['id'];
			$filedelete = SOCIO_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;

		case "search":
			$view = "views/reporteinvitacionesespeciales/list.php";
			break;

		default;
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;
	} // End switch



	if (empty($view))
		$view = "views/reporteinvitacionesespeciales/form.php";


	?>