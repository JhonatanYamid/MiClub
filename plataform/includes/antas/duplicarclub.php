<?php
	require( "config.inc.php" );

	$IDClubPadre=85;
	$IDClubDestino=104;
	exit;

	//Preguntas Perfil
	$sql_secc="INSERT INTO ClubModulo (IDClub,IDModulo,Titulo,TituloLateral,Icono,IconoLateral,Orden,Ubicacion,Activo)
						 SELECT ".$IDClubDestino.",IDModulo,Titulo,TituloLateral,Icono,IconoLateral,Orden,Ubicacion,Activo
						 FROM ClubModulo
						 WHERE IDClub = '".$IDClubPadre."' ";
	$dbo->query($sql_secc);


	//Preguntas Perfil
	$sql_secc="INSERT INTO CampoEditarSocio (IDClub,Nombre,CampoTabla,Tipo,Valores,PermiteEditar,Obligatorio,Orden)
						 SELECT ".$IDClubDestino.",Nombre,CampoTabla,Tipo,Valores,PermiteEditar,Obligatorio,Orden
						 FROM CampoEditarSocio
						 WHERE IDClub = '".$IDClubPadre."' ";
	$dbo->query($sql_secc);



	//Duplicar archivos
	$sql_seccion="SELECT * From TipoArchivo Where IDClub = '".$IDClubPadre."'";
	$r_seccion=$dbo->query($sql_seccion);
	while($row=$dbo->fetchArray($r_seccion)){
			$sql_secc="INSERT INTO TipoArchivo (IDClub,DirigidoA,Nombre,Tipo,Icono,Mostrar,SoloIcono,Publicar)
								 SELECT ".$IDClubDestino.",DirigidoA,Nombre,Tipo,Icono,Mostrar,SoloIcono,Publicar
								 FROM TipoArchivo
								 WHERE IDClub = '".$IDClubPadre."' and IDTipoArchivo = '".$row["IDTipoArchivo"]."'";
			$dbo->query($sql_secc);
			$id_nuevo = $dbo->lastID();
			//Selecciono las not
			$sql_hijo="SELECT * From Documento Where IDClub = '".$IDClubPadre."' and IDTipoArchivo = '".$row["IDTipoArchivo"]."' ";
			$r_hijo=$dbo->query($sql_hijo);
			while($row_hijo=$dbo->fetchArray($r_hijo)){
				$sql_duplicar="INSERT INTO Documento (IDClub,IDTipoArchivo,IDServicio,Nombre,Subtitular,Descripcion,Fecha,Archivo1,Icono,Orden,Publicar)
									 SELECT ".$IDClubDestino.",".$id_nuevo.",IDServicio,Nombre,Subtitular,Descripcion,Fecha,Archivo1,Icono,Orden,Publicar
									 FROM Documento
									 WHERE IDClub = '".$IDClubPadre."' and IDDocumento= '".$row_hijo["IDDocumento"]."'";
				$dbo->query($sql_duplicar);
			}
	}
	//Fin duplicar archivos



	//Diagnostico
	$sql_seccion="SELECT * From Diagnostico Where IDClub = '".$IDClubPadre."'";
	$r_seccion=$dbo->query($sql_seccion);
	while($row=$dbo->fetchArray($r_seccion)){
			$sql_secc="INSERT INTO Diagnostico (IDClub,IDGrupoSocio,DirigidoA,Nombre,Descripcion,Orden,SolicitarAbrirApp,UnaporSocio,FechaInicio,FechaFin,Imagen,DirigidoAGeneral,InvitadoSeleccion,PesoMaximo,EmailAlerta,MensajeBien,MensajeMal,Publicar)
								 SELECT ".$IDClubDestino.",IDGrupoSocio,DirigidoA,Nombre,Descripcion,Orden,SolicitarAbrirApp,UnaporSocio,FechaInicio,FechaFin,Imagen,DirigidoAGeneral,InvitadoSeleccion,PesoMaximo,EmailAlerta,MensajeBien,MensajeMal,Publicar
								 FROM Diagnostico
								 WHERE IDClub = '".$IDClubPadre."' and IDDiagnostico = '".$row["IDDiagnostico"]."'";
			$dbo->query($sql_secc);
			$id_nuevo = $dbo->lastID();
			//Selecciono las preguntas
			$sql_hijo="SELECT * From PreguntaDiagnostico Where IDDiagnostico = '".$row["IDDiagnostico"]."' ";
			$r_hijo=$dbo->query($sql_hijo);
			while($row_hijo=$dbo->fetchArray($r_hijo)){
				$sql_duplicar="INSERT INTO PreguntaDiagnostico (IDDiagnostico,TipoCampo,EtiquetaCampo,Obligatorio,Valores,Orden,Publicar)
									 SELECT ".$id_nuevo.",TipoCampo,EtiquetaCampo,Obligatorio,Valores,Orden,Publicar
									 FROM PreguntaDiagnostico
									 WHERE IDDiagnostico= '".$row_hijo["IDDiagnostico"]."' and IDPreguntaDiagnostico = '".$row_hijo["IDPreguntaDiagnostico"]."'";
				$dbo->query($sql_duplicar);
				$id_nueva_pregunta = $dbo->lastID();
				//selecciono las respuestas
				$sql_resp="SELECT * From DiagnosticoOpcionesRespuesta Where IDDiagnosticoPregunta = '".$row_hijo["IDPreguntaDiagnostico"]."' ";
				$r_resp=$dbo->query($sql_resp);
				while($row_resp=$dbo->fetchArray($r_resp)){
					$sql_duplicar_resp="INSERT INTO DiagnosticoOpcionesRespuesta (IDDiagnosticoPregunta,IDDiagnosticoPreguntaSiguiente,Opcion,Terminar,Peso,Orden)
										 SELECT ".$id_nueva_pregunta.",IDDiagnosticoPreguntaSiguiente,Opcion,Terminar,Peso,Orden
										 FROM DiagnosticoOpcionesRespuesta
										 WHERE IDDiagnosticoOpcionesRespuesta= '".$row_resp["IDDiagnosticoOpcionesRespuesta"]."'";
					$dbo->query($sql_duplicar_resp);
				}
			}
	}
	//Fin duplicar diiagnostico








	//Duplicar noticias
	$sql_seccion="SELECT * From Seccion Where IDClub = '".$IDClubPadre."'";
	$r_seccion=$dbo->query($sql_seccion);
	while($row=$dbo->fetchArray($r_seccion)){
			$sql_secc="INSERT INTO Seccion (IDClub,IDPadre,DirigidoA,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase,PublicarZona,UbicacionZona)
								 SELECT ".$IDClubDestino.",IDPadre,DirigidoA,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase,PublicarZona,UbicacionZona
								 FROM Seccion
								 WHERE IDClub = '".$IDClubPadre."' and IDSeccion = '".$row["IDSeccion"]."'";
			$dbo->query($sql_secc);
			$id_nuevo = $dbo->lastID();
			//Selecciono las not
			$sql_hijo="SELECT * From Noticia Where IDClub = '".$IDClubPadre."' and IDSeccion = '".$row["IDSeccion"]."' ";
			$r_hijo=$dbo->query($sql_hijo);
			while($row_hijo=$dbo->fetchArray($r_hijo)){
				$sql_duplicar="INSERT INTO Noticia (IDClub,IDSeccion,DirigidoA,TipoSocio,URL,Titular,SubTitular,Introduccion,Cuerpo,Plantilla,Publicar,Servicio,Home,Destacada,Orden,FechaInicio,FechaFin,NoticiaFile,Adjunto1File,Adjunto2File,Foto1,Foto2,Foto3,FotoDestacada,Tag,FechaNotificacion,Video)
									 SELECT ".$IDClubDestino.",".$id_nuevo.",DirigidoA,TipoSocio,URL,Titular,SubTitular,Introduccion,Cuerpo,Plantilla,Publicar,Servicio,Home,Destacada,Orden,FechaInicio,FechaFin,NoticiaFile,Adjunto1File,Adjunto2File,Foto1,Foto2,Foto3,FotoDestacada,Tag,FechaNotificacion,Video
									 FROM Noticia
									 WHERE IDClub = '".$IDClubPadre."' and IDNoticia= '".$row_hijo["IDNoticia"]."'";
				$dbo->query($sql_duplicar);
			}
	}
	//Fin duplicar noticias



	//Duplicar eventos
	$sql_seccion="SELECT * From SeccionEvento Where IDClub = '".$IDClubPadre."'";
	$r_seccion=$dbo->query($sql_seccion);
	while($row=$dbo->fetchArray($r_seccion)){
			$sql_secc="INSERT INTO SeccionEvento (IDClub,IDPadre,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase,PublicarZona,UbicacionZona)
								 SELECT ".$IDClubDestino.",IDPadre,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase,PublicarZona,UbicacionZona
								 FROM SeccionEvento
								 WHERE IDClub = '".$IDClubPadre."' and IDSeccionEvento = '".$row["IDSeccionEvento"]."'";
			$dbo->query($sql_secc);
			$id_nuevo = $dbo->lastID();
			//Selecciono las not
			$sql_hijo="SELECT * From Evento Where IDClub = '".$IDClubPadre."' and IDSeccionEvento = '".$row["IDSeccionEvento"]."' ";
			$r_hijo=$dbo->query($sql_hijo);
			while($row_hijo=$dbo->fetchArray($r_hijo)){
				$sql_duplicar="INSERT INTO Evento (IDClub,IDSeccionEvento,DirigidoA,TipoSocio,Titular,SubTitular,Introduccion,Cuerpo,CuerpoEmail,Publicar,Home,Destacada,Orden,FechaInicio,FechaFin,EventoFile,Adjunto1File,Adjunto2File,Adjunto3File,Adjunto4File,
									Foto1,Foto2,Foto3,FotoDestacada,Visitas,Lugar,Hora,FechaEvento,FechaFinEvento,Valor,EmailContacto,Tag,InscripcionApp,MensajePagoInscripcion,ValorInscripcion,EmailNotificacionInscripcion,MaximoParticipantes,PermiteRepetir,FechaLimiteInscripcion,
									HoraLimiteInscripcion,FechaNotificacion)
									 SELECT ".$IDClubDestino.",".$id_nuevo.",DirigidoA,TipoSocio,Titular,SubTitular,Introduccion,Cuerpo,CuerpoEmail,Publicar,Home,Destacada,Orden,FechaInicio,FechaFin,EventoFile,Adjunto1File,Adjunto2File,Adjunto3File,Adjunto4File,Foto1,Foto2,Foto3,FotoDestacada,
									 Visitas,Lugar,Hora,FechaEvento,FechaFinEvento,Valor,EmailContacto,Tag,InscripcionApp,MensajePagoInscripcion,ValorInscripcion,EmailNotificacionInscripcion,MaximoParticipantes,PermiteRepetir,FechaLimiteInscripcion,HoraLimiteInscripcion,FechaNotificacion
									 FROM Evento
									 WHERE IDClub = '".$IDClubPadre."' and IDEvento= '".$row_hijo["IDEvento"]."'";
				$dbo->query($sql_duplicar);
			}
	}
	//Fin duplicar eventos





	//Duplicar Publicidad
	$sql_reg="INSERT INTO Publicidad (IDClub,DirigidoA,Nombre,Descripcion,AccionClick,Cuerpo,Url,Header,Footer,Foto1,FechaInicio,FechaFin,Orden,Publicar)
						 SELECT ".$IDClubDestino.",DirigidoA,Nombre,Descripcion,AccionClick,Cuerpo,Url,Header,Footer,Foto1,FechaInicio,FechaFin,Orden,Publicar
						 FROM Publicidad
						 WHERE IDClub = '".$IDClubPadre."'";
	$dbo->query($sql_reg);


	//Duplicar Pqr

	$sql_reg="INSERT INTO TipoPqr (IDClub,Nombre,Descripcion,Publicar)
						 SELECT ".$IDClubDestino.",Nombre,Descripcion,Publicar
						 FROM TipoPqr
						 WHERE IDClub = '".$IDClubPadre."'";
	$dbo->query($sql_reg);


	$sql_reg="INSERT INTO Area (IDClub,Nombre,Responsable,CorreoResponsable,MostrarApp,Orden,Icono,Activo)
						 SELECT ".$IDClubDestino.",Nombre,Responsable,CorreoResponsable,MostrarApp,Orden,Icono,Activo
						 FROM Area
						 WHERE IDClub = '".$IDClubPadre."'";
	$dbo->query($sql_reg);



	echo "FIN";
	exit;

//Duplicar Diagnostico
/*
$sql_seccion="SELECT * From Diagnostico Where IDClub = '".$IDClubPadre."'";
$r_seccion=$dbo->query($sql_seccion);
while($row=$dbo->fetchArray($r_seccion)){
		$sql_secc="INSERT INTO Diagnostico (IDClub,IDGrupoSocio,DirigidoA,Nombre,Descripcion,Orden,SolicitarAbrirApp,UnaporSocio,FechaInicio,FechaFin,Imagen,DirigidoAGeneral,InvitadoSeleccion,PesoMaximo,EmailAlerta,MensajeBien,MensajeMal,Publicar)
							 SELECT ".$IDClubDestino.",IDGrupoSocio,DirigidoA,Nombre,Descripcion,Orden,SolicitarAbrirApp,UnaporSocio,FechaInicio,FechaFin,Imagen,DirigidoAGeneral,InvitadoSeleccion,PesoMaximo,EmailAlerta,MensajeBien,MensajeMal,Publicar
							 FROM Diagnostico
							 WHERE IDClub = '".$IDClubPadre."' and IDDiagnostico = '".$row["IDDiagnostico"]."'";
		$dbo->query($sql_secc);
		$id_nuevo = $dbo->lastID();
		//Selecciono las preguntas
		$sql_hijo="SELECT * From PreguntaDiagnostico Where IDDiagnostico = '".$row["IDDiagnostico"]."' ";
		$r_hijo=$dbo->query($sql_hijo);
		while($row_hijo=$dbo->fetchArray($r_hijo)){
			$sql_duplicar="INSERT INTO PreguntaDiagnostico (IDDiagnostico,TipoCampo,EtiquetaCampo,Obligatorio,Valores,Orden,Publicar)
								 SELECT ".$id_nuevo.",TipoCampo,EtiquetaCampo,Obligatorio,Valores,Orden,Publicar
								 FROM PreguntaDiagnostico
								 WHERE IDDiagnostico= '".$row_hijo["IDDiagnostico"]."' and IDPreguntaDiagnostico = '".$row_hijo["IDPreguntaDiagnostico"]."'";
			$dbo->query($sql_duplicar);
			$id_nueva_pregunta = $dbo->lastID();
			//selecciono las respuestas
			$sql_resp="SELECT * From DiagnosticoOpcionesRespuesta Where IDDiagnosticoPregunta = '".$row_hijo["IDPreguntaDiagnostico"]."' ";
			$r_resp=$dbo->query($sql_resp);
			while($row_resp=$dbo->fetchArray($r_resp)){
				$sql_duplicar_resp="INSERT INTO DiagnosticoOpcionesRespuesta (IDDiagnosticoPregunta,IDDiagnosticoPreguntaSiguiente,Opcion,Terminar,Peso,Orden)
									 SELECT ".$id_nueva_pregunta.",IDDiagnosticoPreguntaSiguiente,Opcion,Terminar,Peso,Orden
									 FROM DiagnosticoOpcionesRespuesta
									 WHERE IDDiagnosticoOpcionesRespuesta= '".$row_resp["IDDiagnosticoOpcionesRespuesta"]."'";
				$dbo->query($sql_duplicar_resp);
			}
		}
}
//Fin duplicar diiagnostico
*/



	exit;


	$sql_pregunta="INSERT INTO PreguntaEncuesta2 (IDEncuesta2, TipoCampo,  EtiquetaCampo,  Obligatorio , Valores, Orden ,  Publicar  )
						 SELECT '4', TipoCampo,  EtiquetaCampo,  Obligatorio , Valores, Orden ,  Publicar
						 FROM PreguntaEncuesta2
						 WHERE IDEncuesta2 = '2'";
	echo $sql_pregunta;
	exit;

	$sql_secc="INSERT INTO Encuesta2 (IDClub,IDGrupoSocio,DirigidoA,Nombre, Descripcion , Orden , SolicitarAbrirApp , UnaporSocio ,  FechaInicio ,  FechaFin ,  Imagen ,  DirigidoAGeneral ,  InvitadoSeleccion , PesoMaximo ,  EmailAlerta , MensajeBien,  MensajeMal ,  Publicar )
						 SELECT ".$IDClubDestino.",IDClub,IDGrupoSocio,DirigidoA,Nombre, Descripcion , Orden , SolicitarAbrirApp , UnaporSocio ,  FechaInicio ,  FechaFin ,  Imagen ,  DirigidoAGeneral ,  InvitadoSeleccion , PesoMaximo ,  EmailAlerta , MensajeBien,  MensajeMal ,  Publicar
						 FROM Encuesta2
						 WHERE IDClub = '".$IDClubPadre."'";

echo $sql_secc;
exit;
	$dbo->query($sql_secc);

exit;


//Duplicar Galerias
$sql_secc="INSERT INTO SeccionGaleria (IDClub,IDPadre,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase)
					 SELECT ".$IDClubDestino.",IDPadre,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase
					 FROM SeccionGaleria
					 WHERE IDClub = '".$IDClubPadre."'";
$dbo->query($sql_secc);



$IDSeccion=$dbo->getFields( "SeccionGaleria", "IDSeccionGaleria", "IDClub = '" . $IDClubDestino . "'" );
$sql_gal="INSERT INTO Galeria (IDClub,IDSeccionGaleria,DirigidoA,TipoSocio,Nombre,Descripcion,Foto,Fecha,Visitas,Publicar,Home,Destacada,FechaNotificacion)
					 SELECT ".$IDClubDestino.",".$IDSeccion.",DirigidoA,TipoSocio,Nombre,Descripcion,Foto,Fecha,Visitas,Publicar,Home,Destacada,FechaNotificacion
					 FROM Galeria
					 WHERE IDClub = '".$IDClubPadre."'";
$dbo->query($sql_gal);

echo "terminado";

?>
