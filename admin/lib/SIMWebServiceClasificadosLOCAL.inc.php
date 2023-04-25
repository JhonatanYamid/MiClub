<?php
class SIMWebServiceClasificados
{

	public function get_configuracion_clasificado($IDClub, $IDSocio)
	{

		$dbo = &SIMDB::get();
		$response = array();

		$sql = "SELECT * FROM Club  WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$datos_config_clasif = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {

				//Configuracion Clasificados		
				$sqlConfiguracionClasificado = "SELECT PermiteMostrarPalabraMeta,TextoPalabraMeta FROM ConfiguracionClasificados  WHERE IDClub = '" . $IDClub . "' AND ConfiguracionPara='S' AND Activo='S'";
				$qryConfiguracionClasificado = $dbo->query($sqlConfiguracionClasificado);
				$ConfiguracionClasificado = $dbo->fetchArray($qryConfiguracionClasificado);

				if ($ConfiguracionClasificado["PermiteMostrarPalabraMeta"] == "N") {
					$TextoMeta = "";
				} else {
					if (!empty($ConfiguracionClasificado["TextoPalabraMeta"])) {
						$TextoMeta = $ConfiguracionClasificado["TextoPalabraMeta"];
					} else {
						$TextoMeta = "Meta";
					}
				}

				$configuracion["LabelDetalleValor"] = $TextoMeta;
				$configuracion["IDClub"] = $r["IDClub"];
				$configuracion["PermiteCrearClasificado"] = $r["CrearClasificado"];
				$configuracion["TipoCrearClasificado"] = $r["TipoCrearClasificado"];
				$configuracion["URLCLasificado"] = $r["URLCLasificado"];

				$configuracion["LabelWhatsapp"] = $datos_config_clasif["LabelWhatsapp"];
				$configuracion["PermiteBotonMisClasificados"] = $datos_config_clasif["PermiteBotonMisClasificados"];
				$configuracion["PermiteBotonContactar"] = $datos_config_clasif["PermiteBotonContactar"];
				$configuracion["PermiteBotonPreguntar"] = $datos_config_clasif["PermiteBotonPreguntar"];
				$configuracion["PermiteBotonWhatsApp"] = $datos_config_clasif["PermiteBotonWhatsApp"];
				$configuracion["TextoBotonWhatsApp"] = $datos_config_clasif["TextoBotonWhatsApp"];

				if ($IDClub == 49) {
					$configuracion["LabelIDCategoria"] = "Categoria Clasificado";
					$configuracion["LabelNombre"] = "Titulo a nombre de la publicación";
					$configuracion["LabelDescripcion"] = "¿Qué tiene tu producto?";
					$configuracion["LabelTelefono"] = "Teléfono de contacto";
					$configuracion["LabelEmail"] = "Email de contacto";
					$configuracion["LabelValor"] = "Meta de recaudo en números";
					$configuracion["LabelFechaInicio"] = "Fecha Inicio";
					$configuracion["LabelFechaFin"] = "FechaFin";
					$configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";

					$configuracion["LabelDetalleNombre"] = "";
					/* $configuracion["LabelDetalleValor"] = "Meta"; */
					$configuracion["LabelDetalleDescripcion"] = "Tiene:";
				} elseif ($IDClub == 72) {
					$configuracion["LabelDescripcion"] = "Descripción";
					$configuracion["LabelNombre"] = "Nombre del producto o servicio";
					$configuracion["LabelDetalleNombre"] = "";
					$configuracion["LabelDetalleValor"] = " Precio ";
					$configuracion["LabelDetalleDescripcion"] = "Tiene:";
				} elseif ($IDClub == 34) {
					$configuracion["LabelIDCategoria"] = "Categoría de tu producto";
					$configuracion["LabelNombre"] = "Nombre de tu producto";
					$configuracion["LabelDescripcion"] = "Detalle de servicios";
					$configuracion["LabelTelefono"] = "Teléfono de contacto";
					$configuracion["LabelEmail"] = "Mail de contacto";
					$configuracion["LabelValor"] = "Precio Referencial";
					$configuracion["LabelFechaInicio"] = "Fecha inicial";
					$configuracion["LabelFechaFin"] = "Fecha Final";
					$configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";

					$configuracion["LabelDetalleNombre"] = "";
					/* $configuracion["LabelDetalleValor"] = "Precio"; */
					$configuracion["LabelDetalleDescripcion"] = "Detalle de servicios:";
				} elseif ($IDClub == 88) {
					$configuracion["LabelIDCategoria"] = "Categoria Clasificado";
					$configuracion["LabelNombre"] = "Titulo a nombre de la publicación";
					$configuracion["LabelDescripcion"] = "¿Qué tiene tu producto?";
					$configuracion["LabelTelefono"] = "Teléfono de contacto";
					$configuracion["LabelEmail"] = "Email de contacto";
					$configuracion["LabelValor"] = "Meta de recaudo en números";
					$configuracion["LabelFechaInicio"] = "Fecha Inicio";
					$configuracion["LabelFechaFin"] = "FechaFin";
					$configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";

					$configuracion["LabelDetalleNombre"] = "";
					/* $configuracion["LabelDetalleValor"] = ""; */
					$configuracion["LabelDetalleDescripcion"] = "Tiene:";
				} elseif ($IDClub == 26) {
					$configuracion["LabelIDCategoria"] = "Categoria Clasificado";
					$configuracion["LabelNombre"] = "Titulo a nombre de la publicación";
					$configuracion["LabelDescripcion"] = "¿Qué tiene tu producto?";
					$configuracion["LabelTelefono"] = "Teléfono de contacto";
					$configuracion["LabelEmail"] = "Email de contacto";
					$configuracion["LabelValor"] = "Meta de recaudo en números";
					$configuracion["LabelFechaInicio"] = "Fecha Inicio";
					$configuracion["LabelFechaFin"] = "FechaFin";
					$configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";

					$configuracion["LabelDetalleNombre"] = "";
					/* $configuracion["LabelDetalleValor"] = ""; */
					$configuracion["LabelDetalleDescripcion"] = "Experiencia laboral";
				} else {
					$configuracion["LabelIDCategoria"] = "Categoria Clasificado";
					$configuracion["LabelNombre"] = "Titulo a nombre de la publicación";
					$configuracion["LabelDescripcion"] = "¿Qué tiene tu producto?";
					$configuracion["LabelTelefono"] = "Teléfono de contacto";
					$configuracion["LabelEmail"] = "Email de contacto";
					$configuracion["LabelValor"] = "Meta de recaudo en números";
					$configuracion["LabelFechaInicio"] = "Fecha Inicio";
					$configuracion["LabelFechaFin"] = "FechaFin";
					$configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";

					$configuracion["LabelDetalleNombre"] = "";
					/* $configuracion["LabelDetalleValor"] = "Meta"; */
					$configuracion["LabelDetalleDescripcion"] = "Tiene:";

					/*
					$configuracion["LabelIDCategoria"] = "";
					$configuracion["LabelNombre"] = "";
					$configuracion["LabelDescripcion"] = "";
					$configuracion["LabelTelefono"] = "";
					$configuracion["LabelEmail"] = "";
					$configuracion["LabelValor"] = "";
					$configuracion["LabelFechaInicio"] = "";
					$configuracion["LabelFechaFin"] = "";
					$configuracion["LabelIDEstadoClasificado"] = "";

					$configuracion["LabelDetalleNombre"] = "";
					$configuracion["LabelDetalleValor"] = "";
					$configuracion["LabelDetalleDescripcion"] = "";
					*/
				}



				array_push($response, $configuracion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Correspondencianoestáactivo', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		} //end else

		return $respuesta;
	} // fin function

	public function get_categoria_clasificado($IDClub, $TipoApp)
	{
		$dbo = &SIMDB::get();

		if ($TipoApp == "Socio") {
			$condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
		} elseif ($TipoApp == "Empleado") {
			$condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
		}

		$response = array();
		$sql = "SELECT * FROM SeccionClasificados  WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY Orden";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				// verifico que la seccion tenga por lo menos una noticia publicada
				$seccion["IDClub"] = $r["IDClub"];
				$seccion["IDCategoria"] = $r["IDSeccionClasificados"];
				$seccion["Nombre"] = $r["Nombre"];
				$seccion["Descripcion"] = $r["Descripcion"];
				$seccion["SoloIcono"] = $r["SoloIcono"];

				if (!empty($r["Foto"])) :
					$foto = CLASIFICADOS_ROOT . $r["Foto"];
				else :
					$foto = "";
				endif;

				$seccion["Icono"] = $foto;

				array_push($response, $seccion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {

			if ($id_club == 151) {
				$respuesta_serv = "There are no classifieds";
			} else {
				$respuesta_serv = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			}

			$respuesta["message"] = $respuesta_serv;
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		} //end else

		return $respuesta;
	} // fin function

	public function get_clasificado($id_club, $id_categoria = "", $id_clasificado = "", $tag = "", $IDSocio, $IDEstadoClasificado, $IDUsuario)
	{

		$dbo = &SIMDB::get();

		//Socio
		// Seccion Especifica
		if (!empty($IDSocio)) :
			$array_condiciones[] = " IDSocio  = '" . $IDSocio . "' ";
		endif;

		if (!empty($IDUsuario)) :
			$array_condiciones[] = " IDUsuario  = '" . $IDUsuario . "' ";
		endif;

		// Seccion Especifica
		if (!empty($id_categoria)) :
			$array_condiciones[] = " IDSeccionClasificados  = '" . $id_categoria . "' and IDEstadoClasificado  = 1 ";
		endif;

		// Seccion Especifica
		if (!empty($id_clasificado)) :
			$array_condiciones[] = " IDClasificado  = '" . $id_clasificado . "' and IDEstadoClasificado  = 1 ";
		endif;

		// Tag
		if (!empty($tag)) :
			$array_condiciones[] = " (Nombre  like '%" . $tag . "%' or Descripcion like '%" . $tag . "%') and IDEstadoClasificado  = 1";
		endif;

		// Tag
		if (!empty($IDEstadoClasificado)) :
			$array_condiciones[] = " IDEstadoClasificado  = '" . $IDEstadoClasificado . "' ";
		else :
			$array_condiciones[] = " IDEstadoClasificado  > 0 ";
		endif;

		if (count($array_condiciones) > 0) :
			$condiciones = implode(" and ", $array_condiciones);
			$condiciones_clasificado = " and " . $condiciones;
		endif;

		$response = array();
		$sql = "SELECT * FROM Clasificado WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $id_club . "'" . $condiciones_clasificado . " ORDER BY FechaInicio DESC";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {

				$clasificado["IDClasificado"] = $r["IDClasificado"];
				$clasificado["IDSocio"] = $r["IDSocio"];
				$clasificado["IDCategoria"] = $r["IDSeccionClasificados"];
				$clasificado["IDClub"] = $r["IDClub"];
				if ($r["IDEstadoClasificado"] == 5) {
					$clasificado["IDEstadoClasificado"] = 1;
				} else {
					$clasificado["IDEstadoClasificado"] = $r["IDEstadoClasificado"];
				}

				$FechaInicioClasificado = $r["FechaInicio"];
				$FechaFinClasificado = $r["FechaFin"];

				$Fechas = "<br><br>" . "Fecha Inicio Publicacion:" . $FechaInicioClasificado . " " . "Fecha Fin Publicacion:" . $FechaFinClasificado;
				$PermiteMostrarFechas = $dbo->getFields("ConfiguracionClasificados", "PermiteMostrarFechas", "IDClub = '" . $id_club . "' AND Activo='S' AND ConfiguracionPara='S'");

				if ($PermiteMostrarFechas == "N" || $PermiteMostrarFechas == "") {
					$Fechas = "";
				}

				$clasificado["Nombre"] = $r["Nombre"];

				$clasificado["Descripcion"] = strip_tags($r["Descripcion"]) . $Fechas;
				$clasificado["Telefono"] = $r["Telefono"];
				$clasificado["Email"] = $r["Email"];
				$clasificado["TelefonoWhatsApp"] = "+" . $r["Whatsapp"];
				if ($r["Valor"] == 0) {
					$clasificado["Valor"] = "";
				} else {

					//$clasificado["Valor"] = $r["Valor"];
					$clasificado["Valor"] = number_format($r["Valor"], 0, ",", ".");
				}

				$clasificado["FechaInicio"] = $r["FechaInicio"];
				$clasificado["FechaFin"] = $r["FechaFin"];

				$response_fotos = array();
				for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
					$campo_foto = "Foto" . $i_foto;
					if (!empty($r[$campo_foto])) :
						$array_dato_foto["Foto"] = CLASIFICADOS_ROOT . $r[$campo_foto];
						array_push($response_fotos, $array_dato_foto);
					endif;
				endfor;
				$clasificado["Fotos"] = $response_fotos;

				$response_preguntas = array();
				$sql_preguntas = "Select * From ClasificadoPregunta Where IDClasificado = '" . $r["IDClasificado"] . "' Order by FechaTrCr Desc";
				$result_preguntas = $dbo->query($sql_preguntas);
				while ($row_preguntas = $dbo->fetchArray($result_preguntas)) :
					//Consulto el nombre del socio que hizo la pregunta
					$NombreSocioQueHaceLaPregunta = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_preguntas["IDSocioPregunta"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_preguntas["IDSocioPregunta"] . "'");
					$array_preguntas["IDPregunta"] = $row_preguntas["IDClasificadoPregunta"];
					$array_preguntas["Pregunta"] = $NombreSocioQueHaceLaPregunta . "-" . $row_preguntas["Pregunta"];
					$array_preguntas["FechaPregunta"] = $row_preguntas["FechaPregunta"];
					$array_preguntas["Respuesta"] = $row_preguntas["Respuesta"];
					$array_preguntas["FechaRespuesta"] = $row_preguntas["FechaRespuesta"];
					array_push($response_preguntas, $array_preguntas);
				endwhile;

				$clasificado["Preguntas"] = $response_preguntas;

				array_push($response, $clasificado);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		} //end else

		return $respuesta;
	} // fin function

	public function set_pregunta_clasificado($IDClub, $IDClasificado, $Pregunta, $IDSocio)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDClasificado) && !empty($Pregunta)) {

			//verifico que el clasificado exista y pertenezca al club
			$datos_clasificado = $dbo->fetchAll("Clasificado", " IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "' ", "array");

			if (!empty($datos_clasificado["IDClasificado"])) {

				$sql_pregunta = $dbo->query("Insert Into ClasificadoPregunta (IDClasificado, IDSocioPregunta, Pregunta, FechaPregunta, Publicar, UsuarioTrCr, FechaTrCr) Values ('" . $IDClasificado . "','" . $IDSocio . "', '" . $Pregunta . "','" . date("Y-m-d") . "','S','App',NOW())");

				//Envio push con notificacion de pregunta
				$Mensaje = SIMUtil::get_traduccion('', '', 'Tienesunapreguntadelclasificado', LANG) . ": " . $datos_clasificado["Nombre"];
				SIMUtil::enviar_notificacion_push_general($IDClub, $datos_clasificado["IDSocio"], $Mensaje);
				SIMUtil::enviar_pregunta_correo_clasificado_socio($IDClub, $Pregunta, $datos_clasificado["IDSocio"], $Mensaje);

				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Preguntaenviadaconéxito', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = null;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionelclasificadonoexisteonopertenecealclub', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "21." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	public function set_respuesta_clasificado($IDClub, $IDClasificado, $IDPregunta, $Respuesta, $IDSocio)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDClasificado) && !empty($IDPregunta) && !empty($Respuesta)) {

			//verifico que la pregunta exista y pertenezca al club
			$datos_clasificado = $dbo->fetchAll("Clasificado", " IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "' ", "array");
			$datos_pregunta = $dbo->fetchAll("ClasificadoPregunta", " IDClasificadoPregunta = '" . $IDPregunta . "'", "array");

			if (!empty($datos_pregunta["IDClasificadoPregunta"])) {

				$sql_pregunta = $dbo->query("Update ClasificadoPregunta Set Respuesta = '" . $Respuesta . "', FechaRespuesta = '" . date("Y-m-d") . "' Where  IDClasificadoPregunta = '" . $IDPregunta . "'");

				//Envio push con notificacion de respuesta
				$Mensaje = SIMUtil::get_traduccion('', '', 'Recibiounarespuestadesupreguntaalclasificado', LANG) . ": " . $datos_clasificado["Nombre"];
				SIMUtil::enviar_notificacion_push_general($IDClub, $datos_pregunta["IDSocioPregunta"], $Mensaje);

				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Respuestaenviadaconéxito', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = null;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionlapreguntanoexisteonopertenecealclub', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "21." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	public function set_clasificado($IDClub, $IDSocio, $IDCategoria, $Nombre, $Descripcion, $Telefono, $Email, $Valor, $FechaInicio, $FechaFin, $File = "", $Dispositivo, $TipoApp, $IDUsuario = "", $Whatsapp = "")
	{
		$dbo = &SIMDB::get();

		if ($FechaInicio == "" || $FechaFin == "") {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Lasfechassonobligatorias', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
			return $respuesta;
		}

		if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion) && !empty($Telefono)) {

			if (!empty($IDUsuario)) :
				$id_socio = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");
			else :
				//verifico que el socio exista y pertenezca al club
				$id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
			endif;

			if (!empty($id_socio)) {
				if (isset($File)) {
					for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
						$campo_foto = "Foto" . $i_foto;
						$files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
						if (empty($files) && !empty($File[$campo_foto]["name"])) :
							$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
							$respuesta["success"] = false;
							$respuesta["response"] = null;
							return $respuesta;
						endif;
						$$campo_foto = $files[0]["innername"];
					endfor;
				} //end if

				//valido que la fecha sea maximo de 1 mes
				$fecha_actual = date('Y-m-j');
				$fecha_maxima = strtotime('+1 month', strtotime($fecha_actual));
				$fecha_fin_clasif = strtotime($FechaFin);

				if ($fecha_fin_clasif > $fecha_maxima) :
					$FechaFin = date('Y-m-d', $fecha_maxima);
				endif;



				if ($Dispositivo == "iOS") {
					$Nombre = utf8_decode($Nombre);
					$Descripcion = utf8_decode($Descripcion);
				}

				if ($TipoApp == "Empleado") :
					//valido la configuracion del clasificado para insertar el IDEstadoClasificado
					$PublicarClasificadosAutomaticamente = $dbo->getFields("ConfiguracionClasificados", "PublicarClasificadosAutomaticamente", "IDClub = '" . $IDClub . "' AND Activo='S' AND ConfiguracionPara='U'");

					if ($PublicarClasificadosAutomaticamente == "S") {
						$IDEstadoDefecto = 1;
					} else {
						$IDEstadoDefecto = 5;
					}
					$sql_clasificado = $dbo->query("Insert Into Clasificado (IDUsuario, IDSeccionClasificados, IDClub, IDEstadoClasificado, Nombre, Descripcion, Telefono, Email, Valor, FechaInicio, FechaFin, Foto1, Foto2, Foto3, Foto4, Foto5, Foto6, UsuarioTrCr, FechaTrCr, Tipo,Whatsapp) Values ('" . $IDUsuario . "','" . $IDCategoria . "','" . $IDClub . "','" . $IDEstadoDefecto . "','" . $Nombre . "','" . $Descripcion . "','" . $Telefono . "','" . $Email . "','" . $Valor . "','" . $FechaInicio . "','" . $FechaFin . "','" . $Foto1 . "','" . $Foto2 . "','" . $Foto3 . "','" . $Foto4 . "','" . $Foto5 . "','" . $Foto6 . "','App',NOW(), '$TipoApp', '" . $Whatsapp . "')");
				else :
					//valido la configuracion del clasificado para insertar el IDEstadoClasificado
					$PublicarClasificadosAutomaticamente = $dbo->getFields("ConfiguracionClasificados", "PublicarClasificadosAutomaticamente", "IDClub = '" . $IDClub . "' AND Activo='S' AND ConfiguracionPara='S'");

					if ($PublicarClasificadosAutomaticamente == "S") {
						$IDEstadoDefecto = 1;
					} else {
						$IDEstadoDefecto = 5;
					}
					$sql_clasificado = $dbo->query("Insert Into Clasificado (IDSocio, IDSeccionClasificados, IDClub, IDEstadoClasificado, Nombre, Descripcion, Telefono, Email, Valor, FechaInicio, FechaFin, Foto1, Foto2, Foto3, Foto4, Foto5, Foto6, UsuarioTrCr, FechaTrCr, Tipo,Whatsapp) Values ('" . $IDSocio . "','" . $IDCategoria . "','" . $IDClub . "','" . $IDEstadoDefecto . "','" . $Nombre . "','" . $Descripcion . "','" . $Telefono . "','" . $Email . "','" . $Valor . "','" . $FechaInicio . "','" . $FechaFin . "','" . $Foto1 . "','" . $Foto2 . "','" . $Foto3 . "','" . $Foto4 . "','" . $Foto5 . "','" . $Foto6 . "','App',NOW(), '$TipoApp','" . $Whatsapp . "')");
				endif;


				$id_clasificado = $dbo->lastID();

				SIMUtil::notificar_nuevo_clasificado($id_clasificado);

				/*
									$Mensaje = "Se ha creado un nuevo clasificado: " . $Nombre;
									//Consulto que socio se les envia la notificacion
									$sql_socio_clasif = "Select S.IDSocio
									From Socio S, SocioSeccionClasificados SS
									Where S.IDSocio=SS.IDSocio And SS.IDSeccionClasificados = '".$IDCategoria."'";
									$result_socio_clasif =    $dbo->query($sql_socio_clasif);
									while($row_socio_clasif = $dbo->fetchArray($result_socio_clasif)):
									$array_id_socio[]=$row_socio_clasif["IDSocio"];
									endwhile;
									if(count($array_id_socio)>0):
									$IDSocio = implode(",",$array_id_socio);
									SIMUtil::enviar_notificacion_push_clasificado($IDClub,$IDSocio,$Mensaje,$id_clasificado);
									endif;
									 */

				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Graciasporusarelappsuclasificadoestáenrevisiónyserápublicadoenlaspróximashoras.', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = null;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "22." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	public function set_edita_clasificado($IDClub, $IDSocio, $IDClasificado, $IDEstadoClasificado, $IDCategoria, $Nombre, $Descripcion, $Telefono, $Email, $Valor, $FechaInicio, $FechaFin, $File = "", $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5, $IDUsuario, $Whatsapp)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDClasificado) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion) && !empty($Telefono)) {

			//verifico que el socio exista y pertenezca al club
			$id_clasificado = $dbo->getFields("Clasificado", "IDClasificado", "IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "'");

			if (!empty($id_clasificado)) {

				//actualizao la fotos en blanco para que queden solo las enviadas
				$sql_clasificado = "Update Clasificado set Foto1='',Foto2='',Foto3='',Foto4='',Foto5=''
								 Where IDClasificado = '" . $IDClasificado . "'";
				$dbo->query($sql_clasificado);

				if (isset($File)) {
					for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
						$campo_foto = "Foto" . $i_foto;
						$files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
						if (empty($files) && !empty($File[$campo_foto]["name"])) :
							$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
							$respuesta["success"] = false;
							$respuesta["response"] = null;
							return $respuesta;
						else :
							if (!empty($files[0]["innername"])) :
								$actualiza_foto .= " , " . $campo_foto . " = '" . $files[0]["innername"] . "'";
							endif;
						endif;

					endfor;
				} //end if

				if (!empty($UrlFoto1)) {
					$actualiza_foto .= " , Foto1 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto1) . "'";
				}

				if (!empty($UrlFoto2)) {
					$actualiza_foto .= " , Foto2 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto2) . "'";
				}

				if (!empty($UrlFoto3)) {
					$actualiza_foto .= " , Foto3 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto3) . "'";
				}

				if (!empty($UrlFoto4)) {
					$actualiza_foto .= " , Foto4 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto4) . "'";
				}

				if (!empty($UrlFoto5)) {
					$actualiza_foto .= " , Foto5 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto5) . "'";
				}

				$sql_clasificado = "UPDATE Clasificado
											set IDSeccionClasificados = '" . $IDCategoria . "', IDEstadoClasificado = '" . $IDEstadoClasificado . "', Nombre = '" . $Nombre . "', Descripcion = '" . $Descripcion . "',
											Telefono = '" . $Telefono . "', Email = '" . $Email . "', Valor = '" . $Valor . "', FechaInicio = '" . $FechaInicio . "', FechaFin = '" . $FechaFin . "',
											Whatsapp = '" . $Whatsapp . "',
											UsuarioTrEd = '" . $IDSocio . "', FechaTrEd = NOW()  " . $actualiza_foto . "
											Where IDClasificado = '" . $IDClasificado . "'";

				$dbo->query($sql_clasificado);

				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = null;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionelclasificadonoexisteonopertenecealclub', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "22." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	// NUEVAS

	public function get_configuracion_clasificado2($IDClub, $IDSocio)
	{

		$dbo = &SIMDB::get();
		$response = array();

		$sql = "SELECT * FROM Club  WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				$configuracion["IDClub"] = $r["IDClub"];
				$configuracion["PermiteCrearClasificado"] = $r["CrearClasificado"];
				$configuracion["TipoCrearClasificado"] = $r["TipoCrearClasificado"];
				$configuracion["URLCLasificado"] = $r["URLCLasificado"];
				if ($IDClub == 25 || $IDClub == 8) {
					$configuracion["LabelIDCategoria"] = "Categoria";
					$configuracion["LabelNombre"] = "Nombre de la empresa / emprendimiento";
					$configuracion["LabelDescripcion"] = "Descripcion del producto";
					$configuracion["LabelIDEstadoClasificado"] = "Estado de tu producto";
					//Dinamicos por club
					$campo = array();
					$response_campo = array();
					$sql_campo = "SELECT * FROM ClasificadoCampo WHERE IDClub = '" . $IDClub . "' AND Publicar = 'S' Order By 	Orden";
					$r_campo = $dbo->query($sql_campo);
					while ($row_campo = $dbo->FetchArray($r_campo)) :
						$campo["IDClasificadoCampo"] = $row_campo["IDClasificadoCampo"];
						$campo["TipoCampo"] = $row_campo["TipoCampo"];
						$campo["EtiquetaCampo"] = $row_campo["EtiquetaCampo"];
						$campo["Obligatorio"] = $row_campo["Obligatorio"];
						$campo["Valores"] = $row_campo["Valores"];
						$campo["Orden"] = $row_campo["Orden"];
						array_push($response_campo, $campo);
					endwhile;
					$configuracion["Campos"] = $response_campo;
				} else {
					$configuracion["LabelIDCategoria"] = "";
					$configuracion["LabelNombre"] = "";
					$configuracion["LabelDescripcion"] = "";
					$configuracion["LabelTelefono"] = "";
					$configuracion["LabelEmail"] = "";
					$configuracion["LabelValor"] = "";
					$configuracion["LabelFechaInicio"] = "";
					$configuracion["LabelFechaFin"] = "";
					$configuracion["LabelIDEstadoClasificado"] = "";

					$configuracion["LabelDetalleNombre"] = "";
					$configuracion["LabelDetalleValor"] = "";
					$configuracion["LabelDetalleDescripcion"] = "";
				}

				array_push($response, $configuracion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Correspondencianoestáactivo', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		} //end else

		return $respuesta;
	} // fin function

	public function set_clasificado2($IDClub, $IDSocio, $IDCategoria, $Nombre, $Descripcion, $Respuestas, $File = "")
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDSocio) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion)) {

			//verifico que el socio exista y pertenezca al club
			$id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

			if (!empty($id_socio)) {

				if (isset($File)) {

					for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
						$campo_foto = "Foto" . $i_foto;
						$files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
						if (empty($files) && !empty($File[$campo_foto]["name"])) :
							$respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
							$respuesta["success"] = false;
							$respuesta["response"] = null;
							return $respuesta;
						endif;
						$$campo_foto = $files[0]["innername"];
					endfor;
				} //end if

				//valido que la fecha sea maximo de 1 mes
				$fecha_actual = date('Y-m-j');
				$fecha_maxima = strtotime('+1 month', strtotime($fecha_actual));
				$FechaFin = $fecha_maxima;
				$fecha_fin_clasif = strtotime($FechaFin);

				if ($fecha_fin_clasif > $fecha_maxima) :
					$FechaFin = date('Y-m-d', $fecha_maxima);
				endif;

				$sql_clasificado = $dbo->query("Insert Into Clasificado2 (IDSocio	, IDSeccionClasificados, IDClub, IDEstadoClasificado, Nombre, Descripcion, Foto1, Foto2, Foto3, Foto4, Foto5, Foto6, UsuarioTrCr, FechaTrCr	)
																	   Values ('" . $IDSocio . "','" . $IDCategoria . "','" . $IDClub . "','5','" . $Nombre . "','" . $Descripcion . "','" . $Foto1 . "','" . $Foto2 . "','" . $Foto3 . "','" . $Foto4 . "','" . $Foto5 . "','" . $Foto6 . "','App',NOW())");

				$id_clasificado = $dbo->lastID();

				//Inserto el valor de los campos dinamicos
				$Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
				$datos_respuesta = json_decode($Respuestas, true);
				if (count($datos_respuesta) > 0) :
					foreach ($datos_respuesta as $detalle_respuesta) :
						$sql_datos_form = $dbo->query("INSERT INTO ClasificadoRespuesta (IDClasificado, IDSocio, IDClasificadoCampo, Valor, FechaTrCr) Values ('" . $id_clasificado . "','" . $IDSocio . "','" . $detalle_respuesta["IDClasificadoCampo"] . "','" . $detalle_respuesta["Valor"] . "',NOW())");
					endforeach;
				endif;

				//SIMUtil::notificar_nuevo_clasificado2($id_clasificado);

				$respuesta["message"] = "Guardado con exito";
				$respuesta["success"] = true;
				$respuesta["response"] = null;
			} else {
				$respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "22. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	public function get_categoria_clasificado2($id_club)
	{
		$dbo = &SIMDB::get();

		$response = array();
		$sql = "SELECT * FROM SeccionClasificados2  WHERE Publicar = 'S' and IDClub = '" . $id_club . "' " . $condicion . " ORDER BY Orden";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				// verifico que la seccion tenga por lo menos una noticia publicada
				$seccion["IDClub"] = $r["IDClub"];
				$seccion["IDCategoria"] = $r["IDSeccionClasificados"];
				$seccion["Nombre"] = $r["Nombre"];
				$seccion["Descripcion"] = $r["Descripcion"];
				$seccion["SoloIcono"] = $r["SoloIcono"];

				if (!empty($r["Foto"])) :
					$foto = CLASIFICADOS_ROOT . $r["Foto"];
				else :
					$foto = "";
				endif;

				$seccion["Icono"] = $foto;

				array_push($response, $seccion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		} //end else

		return $respuesta;
	} // fin function

	public function get_clasificado2($id_club, $id_categoria = "", $id_clasificado = "", $tag = "", $IDSocio, $IDEstadoClasificado)
	{

		$dbo = &SIMDB::get();

		//Socio
		// Seccion Especifica
		if (!empty($IDSocio)) :
			$array_condiciones[] = " IDSocio  = '" . $IDSocio . "' ";
		endif;

		// Seccion Especifica
		if (!empty($id_categoria)) :
			$array_condiciones[] = " IDSeccionClasificados  = '" . $id_categoria . "' and IDEstadoClasificado  = 1 ";
		endif;

		// Seccion Especifica
		if (!empty($id_clasificado)) :
			$array_condiciones[] = " IDClasificado  = '" . $id_clasificado . "' and IDEstadoClasificado  = 1 ";
		endif;

		// Tag
		if (!empty($tag)) :
			$array_condiciones[] = " (Nombre  like '%" . $tag . "%' or Descripcion like '%" . $tag . "%') and IDEstadoClasificado  = 1";
		endif;

		// Tag
		if (!empty($IDEstadoClasificado)) :
			$array_condiciones[] = " IDEstadoClasificado  = '" . $IDEstadoClasificado . "' ";
		else :
			$array_condiciones[] = " IDEstadoClasificado  > 0 ";
		endif;

		if (count($array_condiciones) > 0) :
			$condiciones = implode(" and ", $array_condiciones);
			$condiciones_clasificado = " and " . $condiciones;
		endif;

		$response = array();
		$sql = "SELECT * FROM Clasificado2 WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $id_club . "'" . $condiciones_clasificado . " ORDER BY FechaInicio DESC";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {

				$FechaInicioClasificado = $r["FechaInicio"];
				$FechaFinClasificado = $r["FechaFin"];
				$Fechas = "<br><br>" . "Fecha Inicio Publicacion:" . $FechaInicioClasificado . " " . "Fecha Fin Publicacion:" . $FechaFinClasificado;
				$PermiteMostrarFechas = $dbo->getFields("ConfiguracionClasificados", "PermiteMostrarFechas", "IDClub = '" . $id_club . "' AND Activo='S' AND ConfiguracionPara='U'");

				if ($PermiteMostrarFechas == "N") {
					$Fechas = "";
				}

				$clasificado["IDClasificado"] = $r["IDClasificado"];
				$clasificado["IDSocio"] = $r["IDSocio"];
				$clasificado["IDCategoria"] = $r["IDSeccionClasificados"];
				$clasificado["IDClub"] = $r["IDClub"];
				$clasificado["IDEstadoClasificado"] = $r["IDEstadoClasificado"];
				$clasificado["Nombre"] = $r["Nombre"];
				$clasificado["Descripcion"] = strip_tags($r["Descripcion"]) . $Fechas;
				$clasificado["Telefono"] = $r["Telefono"];
				$clasificado["Email"] = $r["Email"];
				$clasificado["Valor"] = $r["Valor"];
				$clasificado["FechaInicio"] = $r["FechaInicio"];
				$clasificado["FechaFin"] = $r["FechaFin"];
				$clasificado["TelefonoWhatsApp"] = "+" . $r["Whatsapp"];




				$response_fotos = array();
				for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
					$campo_foto = "Foto" . $i_foto;
					if (!empty($r[$campo_foto])) :
						$array_dato_foto["Foto"] = CLASIFICADOS_ROOT . $r[$campo_foto];
						array_push($response_fotos, $array_dato_foto);
					endif;
				endfor;
				$clasificado["Fotos"] = $response_fotos;

				$response_preguntas = array();
				$sql_preguntas = "Select * From ClasificadoPregunta2 Where IDClasificado = '" . $r["IDClasificado"] . "' Order by FechaTrCr Desc";
				$result_preguntas = $dbo->query($sql_preguntas);
				while ($row_preguntas = $dbo->fetchArray($result_preguntas)) :
					$array_preguntas["IDPregunta"] = $row_preguntas["IDClasificadoPregunta"];
					$array_preguntas["Pregunta"] = $row_preguntas["Pregunta"];
					$array_preguntas["FechaPregunta"] = $row_preguntas["FechaPregunta"];
					$array_preguntas["Respuesta"] = $row_preguntas["Respuesta"];
					$array_preguntas["FechaRespuesta"] = $row_preguntas["FechaRespuesta"];
					array_push($response_preguntas, $array_preguntas);
				endwhile;

				$clasificado["Preguntas"] = $response_preguntas;

				array_push($response, $clasificado);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		} //end else

		return $respuesta;
	} // fin function

	public function set_pregunta_clasificado2($IDClub, $IDClasificado, $Pregunta, $IDSocio)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDClasificado) && !empty($Pregunta)) {

			//verifico que el clasificado exista y pertenezca al club
			$datos_clasificado = $dbo->fetchAll("Clasificado2", " IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "' ", "array");

			if (!empty($datos_clasificado["IDClasificado"])) {

				$sql_pregunta = $dbo->query("Insert Into ClasificadoPregunta2 (IDClasificado, IDSocioPregunta, Pregunta, FechaPregunta, Publicar, UsuarioTrCr, FechaTrCr) Values ('" . $IDClasificado . "','" . $IDSocio . "', '" . $Pregunta . "','" . date("Y-m-d") . "','S','App',NOW())");

				//Envio push con notificacion de pregunta
				$Mensaje = "Tienes una pregunta del clasificado : " . $datos_clasificado["Nombre"];
				SIMUtil::enviar_notificacion_push_general($IDClub, $id_socio_clasificado, $Mensaje);

				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Preguntaenviadaconéxito', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = null;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionelclasificadonoexisteonopertenecealclub', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "21." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	public function set_respuesta_clasificado2($IDClub, $IDClasificado, $IDPregunta, $Respuesta, $IDSocio)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDClasificado) && !empty($IDPregunta) && !empty($Respuesta)) {

			//verifico que la pregunta exista y pertenezca al club
			$datos_clasificado = $dbo->fetchAll("Clasificado2", " IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "' ", "array");
			$datos_pregunta = $dbo->fetchAll("ClasificadoPregunta2", " IDClasificadoPregunta = '" . $IDPregunta . "'", "array");

			if (!empty($datos_pregunta["IDClasificadoPregunta"])) {

				$sql_pregunta = $dbo->query("Update ClasificadoPregunta2 Set Respuesta = '" . $Respuesta . "', FechaRespuesta = '" . date("Y-m-d") . "' Where  IDClasificadoPregunta = '" . $IDPregunta . "'");

				//Envio push con notificacion de respuesta
				$Mensaje = "Recibio una respuesta de su pregunta al clasificado : " . $datos_clasificado["Nombre"];
				SIMUtil::enviar_notificacion_push_general($IDClub, $datos_pregunta["IDSocioPregunta"], $Mensaje);

				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Respuestaenviadaconéxito', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = null;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionlapreguntanoexisteonopertenecealclub', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "21." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}

	public function set_edita_clasificado2($IDClub, $IDSocio, $IDClasificado, $IDEstadoClasificado, $IDCategoria, $Nombre, $Descripcion, $Respuestas, $File = "", $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub) && !empty($IDClasificado) && !empty($IDSocio) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion)) {

			//verifico que el socio exista y pertenezca al club
			$id_clasificado = $dbo->getFields("Clasificado2", "IDClasificado", "IDClasificado = '" . $IDClasificado . "' and IDClub = '" . $IDClub . "'");

			if (!empty($id_clasificado)) {

				//actualizao la fotos en blanco para que queden solo las enviadas
				$sql_clasificado = "Update Clasificado2 set Foto1='',Foto2='',Foto3='',Foto4='',Foto5=''
									   Where IDClasificado = '" . $IDClasificado . "'";
				$dbo->query($sql_clasificado);

				if (isset($File)) {
					for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
						$campo_foto = "Foto" . $i_foto;
						$files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
						if (empty($files) && !empty($File[$campo_foto]["name"])) :
							$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
							$respuesta["success"] = false;
							$respuesta["response"] = null;
							return $respuesta;
						else :
							if (!empty($files[0]["innername"])) :
								$actualiza_foto .= " , " . $campo_foto . " = '" . $files[0]["innername"] . "'";
							endif;
						endif;

					endfor;
				} //end if

				if (!empty($UrlFoto1)) {
					$actualiza_foto .= " , Foto1 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto1) . "'";
				}

				if (!empty($UrlFoto2)) {
					$actualiza_foto .= " , Foto2 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto2) . "'";
				}

				if (!empty($UrlFoto3)) {
					$actualiza_foto .= " , Foto3 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto3) . "'";
				}

				if (!empty($UrlFoto4)) {
					$actualiza_foto .= " , Foto4 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto4) . "'";
				}

				if (!empty($UrlFoto5)) {
					$actualiza_foto .= " , Foto5 = '" . str_replace(CLASIFICADOS_ROOT, "", $UrlFoto5) . "'";
				}

				$sql_clasificado = "UPDATE Clasificado2
												  set IDSeccionClasificados = '" . $IDCategoria . "', IDEstadoClasificado = '" . $IDEstadoClasificado . "', Nombre = '" . $Nombre . "', Descripcion = '" . $Descripcion . "',
												  Telefono = '" . $Telefono . "', Email = '" . $Email . "', Valor = '" . $Valor . "', FechaInicio = '" . $FechaInicio . "', FechaFin = '" . $FechaFin . "',
												  UsuarioTrEd = '" . $IDSocio . "', FechaTrEd = NOW()  " . $actualiza_foto . "
												  Where IDClasificado = '" . $IDClasificado . "'";

				$dbo->query($sql_clasificado);

				//Inserto el valor de los campos dinamicos
				$Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
				$datos_respuesta = json_decode($Respuestas, true);
				if (count($datos_respuesta) > 0) :
					//borar los datos anteriores
					$sql_borra = "DELETE FROM ClasificadoRespuesta Where IDClasificado = '" . $IDClasificado . "'";
					$dbo->query($sql_borra);
					foreach ($datos_respuesta as $detalle_respuesta) :
						$sql_datos_form = $dbo->query("INSERT INTO ClasificadoRespuesta (IDClasificado, IDSocio, IDClasificadoCampo, Valor, FechaTrCr) Values ('" . $IDClasificado . "','" . $IDSocio . "','" . $detalle_respuesta["IDClasificadoCampo"] . "','" . $detalle_respuesta["Valor"] . "',NOW())");
					endforeach;
				endif;

				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);
				$respuesta["success"] = true;
				$respuesta["response"] = null;
			} else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionelclasificadonoexisteonopertenecealclub', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}
		} else {
			$respuesta["message"] = "22." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = null;
		}

		return $respuesta;
	}
} //end class
