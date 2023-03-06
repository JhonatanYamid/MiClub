 <?

	SIMReg::setFromStructure(array(
		"title" => "DomiciliosFuncionario",
		"table" => "Domicilio",
		"key" => "IDDomicilio",
		"mod" => "Domicilio"
	));


	$script = "domiciliosusuarios";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	require_once LIBDIR . "SIMWebServiceDomicilios.inc.php";


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

					$files =  SIMFile::upload($_FILES["Foto1"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];
				} //end if

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

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

			if ($_GET["opc"] == "imprimir") {
				$IDDomicilioImprimir = SIMNet::reqInt("id");
				SIMWebServiceDomicilios::imprime_recibo_domicilio($IDDomicilioImprimir, "");
			}

			break;

		case "update":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				//UPLOAD de imagenes
				if (isset($_FILES)) {


					$files =  SIMFile::upload($_FILES["Foto1"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto1"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

					$frm["Foto1"] = $files[0]["innername"];


					$files =  SIMFile::upload($_FILES["Foto2"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto2"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto2"] = $files[0]["innername"];

					$files =  SIMFile::upload($_FILES["Foto3"], IMGPRODUCTO_DIR, "IMAGE");
					if (empty($files) && !empty($_FILES["Foto3"]["name"]))
						SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
					$frm["Foto3"] = $files[0]["innername"];
				} //end if

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));


				if ($frm["NotificarPush"] == "S") :
					//traer todos los socios del club que tengan token
					$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND  IDSocio = '" . $frm["IDSocio"] . "' AND Token <> '' and Token <> '2byte' Limit 1";
					//$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND IDSocio = '5533' AND Token <> '' and Token <> '2byte' Limit 1";

					$qry_socios = $dbo->query($sql_socios);
					$notificaciones = $dbo->rows($qry_socios);

					$datos_club = $dbo->fetchAll("Club", " IDClub = '" . SIMUser::get("club") . "' ", "array");

					while ($r_socios = $dbo->fetchArray($qry_socios)) {
						$users = array(
							array(
								"id" => $r_socios["IDSocio"],
								"idclub" => $r_socios["IDClub"],
								"registration_key" => $r_socios["Token"],
								"deviceType" => $r_socios["Dispositivo"]
							)

						);


						$EstadoSocio = $dbo->getFields("EstadoDomicilio", "Nombre", "IDEstadoDomicilio = '" . $frm["IDEstadoDomicilio"] . "' ");

						$message = "Estimado Socio, le informamos que su pedido esta en estado: " . $EstadoSocio;
						$custom = array(
							"titulo" => "Notificaciones " . $datos_club["Nombre"],
							'idseccion'    => 0,
							'tipo'         => 'General',
							'iddetalle'   => 0
						);

						///enviar notificación
						SIMUtil::sendAlerts($users, $message, $custom);

						//Guardo el log
						$sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha) Values ('" . SIMNet::reqInt("id") . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW())");
					} //end while
				endif;




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
			$filedelete = IMGPRODUCTO_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
			SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
			break;

		case "CrearOrdenCompleta":
			/* 	print_r($_POST);
			exit; */

			$array_datos = array();
			$datos = array();

			$totalDomicilio = 0;
			$IDClub = $_POST["IDClub"];
			$IDUsuario = $_POST["IDUsuario"];
			//$IDSocio = $_POST["IDSocio"];
			$HoraEntrega = $_POST["FechaEntrega"] . " " . $_POST["HoraEntrega"];
			$ComentariosSocio = $_POST["ComentariosSocio"];

			foreach ($_POST["Producto"] as $dato => $valor) :

				$precio = $dbo->getFields("Producto", "Precio", "IDProducto = '" . $valor . "'");

				$datos["IDProducto"] = $valor;
				$datos["Caracteristicas"] = "";
				$datos["Cantidad"] = $_POST["Cantidad"][$valor];
				$datos["ValorUnitario"] = $precio;
				$datos["Comentario"] = $_POST["Cometario"][$valor];

				$total = $_POST["Cantidad"][$valor] * $precio;
				$totalDomicilio += $total;

				$datos["Total"] = $total;

				array_push($array_datos, $datos);

			endforeach;

			$DetallePedido = json_encode($array_datos);

			$Celular = $_POST["Celular"];
			$Direccion = $_POST["Direccion"];
			$ValorDomicilio = $totalDomicilio;
			$FormaPago = "";
			$Version = "";
			$IDRestaurante = $_POST["IDRestauranteDomicilio"];
			$NumeroMesa = $_POST["Mesa"];
			$CamposFormulario = "";
			$Propina = 'N';

			$respuesta = SIMWebServiceApp::set_domicilio($IDClub, "", $HoraEntrega, $ComentariosSocio, $DetallePedido, $Celular, $Direccion, $ValorDomicilio, $FormaPago, $Version, $IDRestaurante, $NumeroMesa, $CamposFormulario, $Propina, $IDUsuario);


			if ($respuesta["success"]) {
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'OrdenCreadaexitosamente', LANGSESSION));
				SIMHTML::jsRedirect("?action=add");
			} else {
				SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Erroralcrearorden', LANGSESSION) . ":" . $respuesta["message"]);
				SIMHTML::jsRedirect("?action=add");
			}


			break;

		case "historial":
			$view = "views/" . $script . "/listHistorial.php";
			break;



		default:
			$view = "views/" . $script . "/list.php";
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
