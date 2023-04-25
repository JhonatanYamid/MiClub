<?php
class SIMWebServiceReconocimiento
{

	function get_configuracion_reconocimiento($IDClub, $IDSocio, $IDUsuario)
	{
		$dbo = &SIMDB::get();
		$response = array();

		$sql = "SELECT * FROM ConfiguracionReconocimiento  WHERE IDClub = '" . $IDClub . "' ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				$configuracion["IDClub"] = $r["IDClub"];
				$configuracion["Nombre"] = $r["Nombre"];
				$configuracion["IDModuloHuella"] = 109;
				$configuracion["NombreHuella"] = $r["NombreHuella"];
				$configuracion["ImagenHuella"] = CLUB_ROOT . $r["ImagenHuella"];
				$configuracion["DescripcionHuella"] = $r["DescripcionHuella"];
				$configuracion["IDModuloSeguridad"] = 110;
				$configuracion["NombreSeguridad"] = $r["NombreSeguridad"];
				$configuracion["ImagenSeguridad"] = CLUB_ROOT . $r["ImagenSeguridad"];
				$configuracion["DescripcionSeguridad"] = $r["DescripcionSeguridad"];
				$configuracion["LabelReconocer"] = $r["LabelReconocer"];
				$configuracion["LabelMisReconocimientos"] = $r["LabelMisReconocimientos"];
				$configuracion["LabelBuscadorCompanero"] = $r["LabelBuscadorCompanero"];
				$configuracion["LabelReconocerIndividual"] = $r["LabelReconocerIndividual"];
				$configuracion["LabelReconocerGrupal"] = $r["LabelReconocerGrupal"];
				$configuracion["LabelBotonHistorial"] = $r["LabelBotonHistorial"];
				$configuracion["LabelReconocerSeguridad"] = $r["LabelReconocerSeguridad"];

				$configuracion["LabelBotonDetalle"] = $r["LabelBotonDetalle"];
				$configuracion["LabelRazonReconocer"] = $r["LabelRazonReconocer"];
				$configuracion["LabelBotonEnviarReconocimiento"] = $r["LabelBotonEnviarReconocimiento"];
				$configuracion["OcultarAgregarGruposPorPersona"] = $r["OcultarAgregarGruposPorPersona"];
				$configuracion["PermiteAgregarGruposPrevios"] = $r["PermiteAgregarGruposPrevios"];
				$configuracion["LabelAgregarGruposPrevios"] = $r["LabelAgregarGruposPrevios"];
				$configuracion["LabelAgregarGrupoPorPersona"] = $r["LabelAgregarGrupoPorPersona"];
				$configuracion["PermiteFiltroGrupal"] = $r["PermiteFiltroGrupal"];
				$configuracion["LabelFiltroGrupal"] = $r["LabelFiltroGrupal"];

				array_push($response, $configuracion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Configuracionnoestáactivo', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function get_categoria_reconocimiento($IDClub, $IDSocio, $IDUsuario, $IDSubModulo)
	{
		$dbo = &SIMDB::get();

		$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
		if ($IDSubModulo == 109) { //Huella
			$Tipo = "Huella";
		} else {
			$Tipo = "Cultura";
			$condicion = " and Areas like '%|" . $datos_socio["IDAreaSocio"] . "%'";
		}

		$sql_opc = "SELECT IDOpcionReconocimiento,Texto FROM OpcionReconocimiento WHERE 1 ";
		$r_opc = $dbo->query($sql_opc);
		while ($row_opc = $dbo->fetchArray($r_opc)) {
			$array_opc[$row_opc["IDOpcionReconocimiento"]] = $row_opc["Texto"];
		}

		$response = array();
		$sql = "SELECT * FROM CategoriaReconocimiento  WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and Tipo = '" . $Tipo . "' " . $condicion . " ORDER BY Orden";
		//echo $sql;
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				// verifico que la seccion tenga por lo menos una noticia publicada
				$seccion["IDClub"] = $r["IDClub"];
				$seccion["IDCategoriaReconocimiento"] = $r["IDCategoriaReconocimiento"];
				$seccion["Nombre"] = $r["Nombre"];
				$seccion["Descripcion"] = $r["Descripcion"];
				$seccion["PermiteReconocimiento"] = $r["PermiteReconocimiento"];


				if (empty($r["ColorLetra"]))
					$ColorLetra = "#000";
				else
					$ColorLetra = $r["ColorLetra"];

				$seccion["ColorLetra"] = $ColorLetra;

				if (!empty($r["ImagenCategoria"])) :
					$foto = CLUB_ROOT . $r["ImagenCategoria"];
				else :
					$foto = "";
				endif;
				$seccion["ImagenCategoria"] =  $foto;

				if (!empty($r["BannerCategoria"])) :
					$fotoban = CLUB_ROOT . $r["BannerCategoria"];
				else :
					$fotoban = "";
				endif;
				$seccion["BannerCategoria"] =  $fotoban;

				if (!empty($r["BannerInterna"])) :
					$fotoban2 = CLUB_ROOT . $r["BannerInterna"];
				else :
					$fotoban2 = "";
				endif;
				$seccion["BannerInterna"] =  $fotoban2;


				//Calcula votos
				$response_votos = array();
				$TotalVotos = 0;


				if ($IDSubModulo == 109) { //Huella
					$sql_votacion = "SELECT R.IDReconocimiento, S.Nombre, S.Apellido, R.IDSocioVotante,R.IDSocioVotado,R.IDGrupoReconocimiento,R.IDCategoriaReconocimiento,R.IDUsuario,R.Comentario,R.FechaTrCr
																 FROM Reconocimiento R, Socio S
																 WHERE R.IDSocioVotante=S.IDSocio and IDSocioVotado = '" . $IDSocio . "' and IDCategoriaReconocimiento = '" . $r["IDCategoriaReconocimiento"] . "'";
					if ($IDClub == 188) {

						$sql_votacion = "SELECT R.IDReconocimiento, S.Nombre, S.Apellido, R.IDSocioVotante,R.IDSocioVotado,R.IDGrupoReconocimiento,R.IDCategoriaReconocimiento,R.IDUsuario,R.Comentario,R.FechaTrCr
																 FROM Reconocimiento R, Socio S
																 WHERE R.IDSocioVotante=S.IDSocio  and IDCategoriaReconocimiento = '" . $r["IDCategoriaReconocimiento"] . "' AND R.IDClub='" . $IDClub . "' ORDER BY R.FechaTrCr DESC LIMIT 100";
					}
					//echo $sql_votacion;
				} else {
					$sql_votacion = "SELECT R.IDReconocimiento, U.Nombre, R.IDSocioVotado,R.IDGrupoReconocimiento,R.IDCategoriaReconocimiento,R.IDUsuario,R.Comentario,R.FechaTrCr
																 FROM Reconocimiento R, Usuario U
																 WHERE R.IDUsuario=U.IDUsuario and IDSocioVotado = '" . $IDSocio . "' and IDCategoriaReconocimiento = '" . $r["IDCategoriaReconocimiento"] . "'";
				}

				$r_votacion = $dbo->query($sql_votacion);
				while ($row_votacion = $dbo->fetchArray($r_votacion)) {
					$TotalVotos++;

					$Conductas = "";
					$sql_conduc = "SELECT IDOpcionReconocimiento  FROM ReconocimientoOpcion WHERE IDReconocimiento = '" . $row_votacion["IDReconocimiento"] . "'";


					$r_conduc = $dbo->query($sql_conduc);
					while ($row_conduc = $dbo->fetchArray($r_conduc)) {
						$Conductas .= $array_opc[$row_conduc["IDOpcionReconocimiento"]] . "\n\n ";
					}


					//$datos_voto["Votante"] = $row_votacion["Nombre"] . " " . $row_votacion["Apellido"];
					$datos_voto["Votante"] = "Persona que hace el reconocimiento:" . $row_votacion["Nombre"] . " " . $row_votacion["Apellido"] . "\n\n Persona que recibe el reconocimiento:" . $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_votacion[IDSocioVotado] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_votacion[IDSocioVotado] . "'");
					$datos_voto["Comentario"] = "\n\n" . $row_votacion["Comentario"] . "\n\n Conductas: \n\n" . $Conductas;
					$datos_voto["Fecha"] = substr($row_votacion["FechaTrCr"], 0, 10);
					array_push($response_votos, $datos_voto);
				}
				$seccion["Votos"] = $TotalVotos;
				$seccion["DetalleVotos"] = $response_votos;
				//Fin Votos

				//Opciones categoria
				$response_opciones = array();
				$sql_opciones = "SELECT IDOpcionReconocimiento,Texto
															 FROM OpcionReconocimiento
															 WHERE IDCategoriaReconocimiento = '" . $r["IDCategoriaReconocimiento"] . "' and Texto <>'' ";
				$r_opciones = $dbo->query($sql_opciones);
				while ($row_opciones = $dbo->fetchArray($r_opciones)) {
					$datos_opcion["IDOpcionReconocimiento"] = $row_opciones["IDOpcionReconocimiento"];
					$datos_opcion["Texto"] = $row_opciones["Texto"];
					array_push($response_opciones, $datos_opcion);
				}
				$seccion["Opciones"] = $response_opciones;
				//Fin Preguntas

				array_push($response, $seccion);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	function get_grupo_reconocimiento($IDClub, $IDSocio, $IDUsuario)
	{
		$dbo = &SIMDB::get();

		$response = array();
		$sql = "SELECT IDGrupoReconocimiento,Nombre FROM GrupoReconocimiento  WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' ORDER BY Nombre ";
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				// verifico que la seccion tenga por lo menos una noticia publicada
				$grupo["IDGrupoReconocimiento"] = $r["IDGrupoReconocimiento"];
				$grupo["Nombre"] = $r["Nombre"];

				//Opciones categoria
				$response_persona = array();
				$sql_personas = "SELECT S.IDSocio,S.Nombre,S.Apellido
																 FROM Socio S, GrupoReconocimientoSocio GRS
																 WHERE S.IDSocio=GRS.IDSocio and IDGrupoReconocimiento = '" . $r["IDGrupoReconocimiento"] . "'";
				$r_personas = $dbo->query($sql_personas);
				while ($row_personas = $dbo->fetchArray($r_personas)) {
					$datos_persona["IDSocio"] = $row_personas["IDSocio"];
					$datos_persona["Nombre"] = $row_personas["Nombre"];
					array_push($response_persona, $datos_persona);
				}
				$grupo["Personas"] = $response_persona;
				//Opciones cat

				array_push($response, $grupo);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	public function get_grupos_reconocimiento($IDClub, $IDSocio, $IDUsuario, $Tag)
	{
		$dbo = &SIMDB::get();

		if (!empty($Tag)) {
			$sql_socio = "SELECT  IDSocio FROM Socio WHERE Nombre LIKE '%" . $Tag . "%'";
			$qry_socio = $dbo->query($sql_socio);
			$dato_socio = $dbo->fetchArray($qry_socio);

			$condicion = " AND (Nombre LIKE '%" . $Tag . "%' OR GRS.IDSocio='" . $dato_socio["IDSocio"] . "')";
		}

		$response = array();
		$sql = "SELECT DISTINCT GP.IDGrupoReconocimiento,GP.Nombre FROM GrupoReconocimiento GP,GrupoReconocimientoSocio GRS  WHERE GP.IDGrupoReconocimiento=GRS.IDGrupoReconocimiento AND Publicar = 'S' and IDClub = '" . $IDClub . "' $condicion ORDER BY Nombre ";
		//echo  $sql;
		$qry = $dbo->query($sql);
		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) {
				// verifico que la seccion tenga por lo menos una noticia publicada
				$grupo["IDGrupoReconocimiento"] = $r["IDGrupoReconocimiento"];
				$grupo["Nombre"] = $r["Nombre"];

				//Opciones categoria
				$response_persona = array();
				$sql_personas = "SELECT S.IDSocio,S.Nombre,S.Apellido
																 FROM Socio S, GrupoReconocimientoSocio GRS
																 WHERE S.IDSocio=GRS.IDSocio and IDGrupoReconocimiento = '" . $r["IDGrupoReconocimiento"] . "'";
				$r_personas = $dbo->query($sql_personas);
				while ($row_personas = $dbo->fetchArray($r_personas)) {
					$datos_persona["IDSocio"] = $row_personas["IDSocio"];
					$datos_persona["Nombre"] = $row_personas["Nombre"];
					array_push($response_persona, $datos_persona);
				}
				$grupo["Personas"] = $response_persona;
				//Opciones cat

				array_push($response, $grupo);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	} // fin function

	public function  get_filtro_categoria_reconocimiento($IDClub, $IDSocio, $IDUsuario)
	{
		if (!empty($IDSocio) || !empty($IDUsuario)) {

			$response_areas = array();
			$Areas =  SIMResources::$areassoycentral;
			//print_r($Areas);
			foreach ($Areas as $key => $areas) {
				$datos_areas["IDFiltro"] = (string)$key;
				$datos_areas["Nombre"] = $areas;
				array_push($response_areas, $datos_areas);
			}


			$respuesta["message"] = "6 Encontrados";
			$respuesta["success"] = true;
			$respuesta["response"] = $response_areas;
		} else {
			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	}

	function set_reconocimiento($IDClub, $IDSocio, $IDUsuario, $IDCategoriaReconocimiento, $IDSocioReconocido, $IDGrupoReconocimiento, $Comentario, $Opciones, $GrupoReconocimiento)
	{
		$dbo = &SIMDB::get();
		if (!empty($IDClub)  && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDCategoriaReconocimiento) && !empty($Opciones)) {


			$datos_opciones = json_decode($Opciones, true);
			if (empty(trim($Comentario)) || count($datos_opciones) <= 0) {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Debescompletartodoslosdatos', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = $response;
				return $respuesta;
			}


			if (!empty($IDUsuario)) {
				$IDSocio = $IDUsuario;
				$TipoUsuario = "Funcionario";
				$condicion_unica = " and IDUsuario='" . $IDUsuario . "'";
			} elseif (!empty($IDSocio)) {
				$TipoUsuario = "Socio";
				$condicion_unica = " and IDSocio='" . $IDSocio . "'";
				$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
			}


			$NombreCat = $dbo->getFields("CategoriaReconocimiento", "Nombre", "IDCategoriaReconocimiento = '" . $IDCategoriaReconocimiento . "'");
			$MensajeNotificacion = $dbo->getFields("CategoriaReconocimiento", "MensajeNotificacion", "IDCategoriaReconocimiento = '" . $IDCategoriaReconocimiento . "'");
			$Mensaje = $MensajeNotificacion;
			$datos_invitado_turno = json_decode($GrupoReconocimiento, true);
			if (count($datos_invitado_turno) > 0) {
				foreach ($datos_invitado_turno as $detalle_datos_turno) {
					$IDSocioReconocidoG = $detalle_datos_turno["IDSocio"];
					$NombreSocioInvitadoTurno = $detalle_datos_turno["Nombre"];
					$sql_datos = "INSERT INTO Reconocimiento (IDClub, IDSocioVotante, IDSocioVotado, IDGrupoReconocimiento, IDCategoriaReconocimiento, Tipo, Comentario, UsuarioTrCr, FechaTrCr)
															VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDSocioReconocidoG . "','" . $IDGrupoReconocimiento . "','" . $IDCategoriaReconocimiento . "','Grupal','" . $Comentario . "','WS',NOW())";
					$dbo->query($sql_datos);

					$id_reconocimiento = $dbo->lastID();
					//Reconocimiento opciones
					foreach ($datos_opciones as $detalle_opcion) :
						$sql_rec = "INSERT INTO ReconocimientoOpcion (IDOpcionReconocimiento,IDReconocimiento) VALUES('" . $detalle_opcion["IDOpcionReconocimiento"] . "','" . $id_reconocimiento . "')";
						$dbo->query($sql_rec);
					endforeach;
					$IDModulo = 108;
					//enviar notificacion al usuario de que se le asigno una nueva reconocimiento
					SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocioReconocidoG, $Mensaje, $IDModulo, "");



					//enviar notificacion al jefe la persona que se le asigno un reconocimiento
					$NombreSocio = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioReconocidoG . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioReconocidoG . "'");
					$DocumentoJefe = $dbo->getFields("Socio", "DocumentoJefe", "IDSocio = '" . $IDSocioReconocidoG . "'");


					if ($DocumentoJefe > 0) {
						$mensajeJefe = "Se ha generado un reconocimiento a la siguiente persona:" . $NombreSocio;
						$IDSocioJefe = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '" . $DocumentoJefe . "'");
						SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocioJefe, $mensajeJefe, $IDModulo, "");
					}

					self::enviar_notificacion_reconocimiento($IDClub, $IDSocio, $IDSocioReconocidoG, $IDCategoriaReconocimiento, $Comentario, $datos_opciones);
				}
			} else {
				$sql_datos = "INSERT INTO Reconocimiento (IDClub, IDSocioVotante, IDSocioVotado, IDGrupoReconocimiento, IDCategoriaReconocimiento, Tipo, Comentario, UsuarioTrCr, FechaTrCr)
														VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDSocioReconocido . "','" . $IDGrupoReconocimiento . "','" . $IDCategoriaReconocimiento . "','Individual','" . $Comentario . "','WS',NOW())";
				$dbo->query($sql_datos);
				$id_reconocimiento = $dbo->lastID();
				//Reconocimiento opciones
				foreach ($datos_opciones as $detalle_opcion) :
					$sql_rec = "INSERT INTO ReconocimientoOpcion (IDOpcionReconocimiento,IDReconocimiento) VALUES('" . $detalle_opcion["IDOpcionReconocimiento"] . "','" . $id_reconocimiento . "')";
					$dbo->query($sql_rec);
				endforeach;

				$IDModulo = 108;
				SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocioReconocido, $Mensaje, $IDModulo, "");
				//enviar notificacion al jefe la persona que se le asigno un reconocimiento
				$NombreSocio = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioReconocido . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioReconocido . "'");
				$DocumentoJefe = $dbo->getFields("Socio", "DocumentoJefe", "IDSocio = '" . $IDSocioReconocido . "'");


				if ($DocumentoJefe > 0) {
					$mensajeJefe = "Se ha generado un reconocimiento a la siguiente persona:" . $NombreSocio;
					$IDSocioJefe = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento = '" . $DocumentoJefe . "'");
					SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocioJefe, $mensajeJefe, $IDModulo, "");
				}

				self::enviar_notificacion_reconocimiento($IDClub, $IDSocio, $IDSocioReconocido, $IDCategoriaReconocimiento, $Comentario, $datos_opciones);
			}


			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Reconocimientoenviado.¡Graciaspordestacaraquienesdejanhuella!', LANG);
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} else {
			$respuesta["message"] = "R1." .  SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}

		return $respuesta;
	}

	function get_reconocimiento_enviado($IDClub, $IDSocio, $IDUsuario)
	{
		$dbo = &SIMDB::get();


		if (!empty($IDSocio) || !empty($IDUsuario)) {

			$response = array();
			$sql = "SELECT IDReconocimiento,IDSocioVotante,IDSocioVotado,IDCategoriaReconocimiento,Tipo,Tipo,FechaTrCr,Comentario
									FROM Reconocimiento
									WHERE IDSocioVotante = '" . $IDSocio . "'
									ORDER BY FechaTrCr DESC ";
			$qry = $dbo->query($sql);
			if ($dbo->rows($qry) > 0) {
				$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
				while ($r = $dbo->fetchArray($qry)) {
					$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $r["IDSocioVotado"] . "' ", "array");
					// verifico que la seccion tenga por lo menos una noticia publicada
					$reconocimiento["IDReconocimiento"] = $r["IDReconocimiento"];
					$reconocimiento["NombreReconocido"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
					$reconocimiento["Fecha"] = substr($r["FechaTrCr"], 0, 10);
					$reconocimiento["Categoria"] = $dbo->getFields("CategoriaReconocimiento", "Nombre", "IDCategoriaReconocimiento = '" . $r["IDCategoriaReconocimiento"] . "'");
					$reconocimiento["Tipo"] = $r["Tipo"];
					$reconocimiento["Comentario"] = $r["Comentario"];
					array_push($response, $reconocimiento);
				} //ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;
			} //End if
			else {
				$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			} //end else

		} else {
			$respuesta["message"] = "R." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}


		return $respuesta;
	} // fin function

	public function enviar_notificacion_reconocimiento($IDClub, $IDSocio, $IDSocioReconocidoG, $IDCategoriaReconocimiento, $Comentario, $datos_opciones)
	{
		$dbo = &SIMDB::get();

		$datos_socio_reconoce = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
		$datos_socio_reconocido = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioReconocidoG . "' ", "array");
		$datos_categoria = $dbo->fetchAll("CategoriaReconocimiento", "IDCategoriaReconocimiento = $IDCategoriaReconocimiento");

		//Reconocimiento opciones
		$ListaOpciones = "";
		if (count($datos_opciones) > 0) {
			foreach ($datos_opciones as $detalle_opcion) {
				$NombreOpcion = $dbo->getFields("OpcionReconocimiento", "Texto", "IDOpcionReconocimiento = '" . $detalle_opcion["IDOpcionReconocimiento"] . "'");
				$opciones .= "<li>" . $NombreOpcion . "</li>";
			}
			$ListaOpciones = "<strong>Comportamientos destacados:</strong><br> <ol>" . $opciones . "</ol>";
		}

		if (!empty($datos_socio_reconoce["IDSocio"]) && !empty($datos_socio_reconocido["IDSocio"])) {




			$Correo = $datos_socio_reconocido["CorreoJefe"];


			$correo_persona_reconocida = $dbo->getFields("ConfiguracionReconocimiento", "PermiteEnviarCorreoPersonaReconocida", "IDClub = '" . $IDClub . "'");
			if ($correo_persona_reconocida == "S" && !empty($datos_socio_reconocido["CorreoElectronico"])) {

				if (!empty($datos_socio_reconocido["CorreoJefe"])) {

					$Correo .= "," . $datos_socio_reconocido["CorreoElectronico"];
				} else {
					$Correo = $datos_socio_reconocido["CorreoElectronico"];
				}
			}
			if (!empty($Correo)) {

				// if (!empty($CorreoJefe)) {
				// 	$Correo = $datos_socio_reconocido["CorreoElectronico"] . "," . $CorreoJefe;
				// }

				$datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_socio_reconoce["IDClub"] . "' ", "array");

				$NombreReconoce = $datos_socio_reconoce["Nombre"] . " " . $datos_socio_reconoce["Apellido"];

				$comentarios = $Comentario;

				$nombre = ucwords($datos_socio_reconocido["Nombre"] . " " . $datos_socio_reconocido["Apellido"]);

				//$datos_categoria[CorreoReconocimiento] = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $datos_categoria[CorreoReconocimiento]);


				$Mensaje = "
				<tr>

				<br><br>

				</tr>
					<tr>	
				
						<td>
							<b>" . 	$nombre . "</b> integrante de tu equipo, ha recibido
					 		una INSIGNIA <br> como reconocimiento en la categoria  <b>" . $datos_categoria[Nombre]  . "</b>
						</td>
			
					</tr>
					<tr>

					<br><br>

					</tr>
					<tr>

						<td>
							<b>Nombre de la persona que hace el reconocimiento:</b>" . $NombreReconoce . "
						</td>

					</tr>
			

					<tr>

					<br><br>

					</tr>
					<tr>
						<td>
							" . 	$ListaOpciones . "		
						</td>
					</tr>
					<tr>

					<br><br>

					</tr>
				
					<tr>
						<td>
							<b>Comentario:</b>" . 	$comentarios . "		
						</td>
		
					</tr>
				
					<tr>

					<br><br>

					</tr>
					<tr>
						<td>
							<center><b>¡Felicitaciones, tu equipo esta marcando la diferencia!</b></center>
						</td>
					</tr>
					
					<tr>

					<br><br>

					</tr>";



				$html = "<table border='0' cellpadding='0' cellspacing='0' width='900px' align='center'>			
							<tr>
								<td>
									<img src='" . CLUB_ROOT . $datos_categoria[BannerInterna] . "'>
								</td>
							</tr>

							<tr>
								<td>" . $Mensaje . "</td>							
							</tr>

							
							

						</table>";

				$html = str_replace("[Nombre]", $nombre, $html);

				$url_baja = URLROOT . "contactenos.php";
				$mail = new phpmailer();
				$array_correo = explode(",", $Correo);
				if (count($array_correo) > 0) {
					foreach ($array_correo as $correo_value) {
						$mail->AddAddress($correo_value);
					}
				}


				$mail->Subject = "Nueva INSIGNIA para tu equipo";
				$mail->Body = $html;
				$mail->IsHTML(true);
				$mail->Sender = $datos_club["CorreoRemitente"];
				$mail->Timeout = 120;
				//$mail->IsSMTP();
				$mail->Port = PUERTO_SMTP;
				$mail->SMTPAuth = true;
				$mail->Host = HOST_SMTP;
				$mail->CharSet = 'UTF-8';
				//$mail->Mailer = 'smtp';
				$mail->Password = PASSWORD_SMPT;
				$mail->Username = USER_SMTP;
				$mail->From = $datos_club["CorreoRemitente"];
				$mail->FromName = $datos_club["RemitenteCorreo"];
				$mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
				$confirm = $mail->Send();
			}
		}
		return true;
	}
} //end class
