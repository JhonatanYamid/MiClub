 <?

	SIMReg::setFromStructure(array(
		"title" => "ConfiguracionDomicilios",
		"table" => "ConfiguracionDomicilios4",
		"key" => "IDConfiguracionDomicilios",
		"mod" => "ConfiguracionDomicilios"
	));


	$script = "configuraciondomicilios4";

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
				$frm["IDPadre"] = SIMNet::post("IDSeccionEvento");
				$frm["Ubicacion"] = implode(",", $frm["Ubicacion"]);

				$files =  SIMFile::upload($_FILES["SeccionImagen"], IMGSECCION_DIR, "IMAGE");
				if (empty($files) && !empty($_FILES["SeccionImagen"]["name"]))
					SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");


				$frm["SeccionFile"] = $files[0]["innername"];


				//insertamos los datos del asistente
				$id = $dbo->insert($frm, $table, $key);


				foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
					$sql_servicio_forma_pago = $dbo->query("Insert into DomicilioTipoPago4 (IDConfiguracionDomicilio, IDTipoPago) Values ('" . SIMNet::reqInt("id") . "', '" . $Pago_seleccionado . "')");
				endforeach;

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

				foreach ($frm["IDDia"] as $Dia_seleccion) :
					$array_dia[] = $Dia_seleccion;
				endforeach;

				if (count($array_dia) > 0) :
					$id_dia = implode("|", $array_dia) . "|";
				endif;
				$frm["Dias"] = $id_dia;


				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				$delete_tipo_pago = $dbo->query("Delete From DomicilioTipoPago4 Where IDConfiguracionDomicilio = '" . SIMNet::reqInt("id") . "'");
				foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
					$sql_servicio_forma_pago = $dbo->query("Insert into DomicilioTipoPago4 (IDConfiguracionDomicilio, IDTipoPago) Values ('" . SIMNet::reqInt("id") . "', '" . $Pago_seleccionado . "')");
				endforeach;

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
			$filedelete = DIRECTORIO_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;

			//categorias
		case "InsertarPropiedadProducto":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "PropiedadProducto", "IDPropiedadProducto");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=categoriap&id=" . $frm["IDConfiguracionDomicilio"]);
			exit;
			break;

		case "ModificaPropiedadProducto":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "PropiedadProducto", "IDPropiedadProducto", $frm["IDPropiedadProducto"]);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=categoriap&id=" . $frm["IDConfiguracionDomicilio"]);
			exit;
			break;

		case "EliminaPropiedadProducto":
			$id = $dbo->query("DELETE FROM PropiedadProducto WHERE IDPropiedadProducto   = '" . $_GET["IDPropiedadProducto"] . "' LIMIT 1");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitosa', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=categoriap&id=" . $_GET["IDConfiguracionDomicilio"] . "&id=" . $_GET["id"]);
			exit;
			break;
			//Fin preguntas

			//Caracteristica
		case "InsertarCaracteristicaProducto":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "CaracteristicaProducto", "IDCaracteristicaProducto");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=caracteristicap&id=" . $frm["IDConfiguracionDomicilio"]);
			exit;
			break;

		case "ModificaCaracteristicaProducto":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "CaracteristicaProducto", "IDCaracteristicaProducto", $frm["IDCaracteristicaProducto"]);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=caracteristicap&id=" . $frm["IDConfiguracionDomicilio"]);
			exit;
			break;

		case "EliminaCaracteristicaProducto":
			$id = $dbo->query("DELETE FROM CaracteristicaProducto WHERE IDCaracteristicaProducto   = '" . $_GET["IDCaracteristicaProducto"] . "' LIMIT 1");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitosa', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=caracteristicap&id=" . $_GET["IDConfiguracionDomicilio"] . "&id=" . $_GET["id"]);
			exit;
			break;
			//Fin preguntas

			//preguntas
		case "InsertarDomicilioPregunta":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "DomicilioPregunta", "IDDomicilioPregunta");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=domiciliopregunta&id=" . $frm["IDConfiguracionDomicilio"]);
			exit;
			break;

		case "ModificaDomicilioPregunta":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "DomicilioPregunta", "IDDomicilioPregunta", $frm["IDDomicilioPregunta"]);
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=domiciliopregunta&id=" . $frm["IDConfiguracionDomicilio"]);
			exit;
			break;

		case "EliminaDomicilioPregunta":
			$id = $dbo->query("DELETE FROM DomicilioPregunta WHERE IDDomicilioPregunta   = '" . $_GET["IDDomicilioPregunta"] . "' LIMIT 1");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitosa', LANGSESSION));
			SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=caracteristica&tab=domiciliopregunta&id=" . $_GET["IDConfiguracionDomicilio"] . "&id=" . $_GET["id"]);
			exit;
			break;
			//Fin preguntas



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
