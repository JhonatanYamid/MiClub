#!/usr/bin/php -q
<?php
exit;
//require( "../admin/config.inc.php" );
//include("/var/www/vhosts/miclubapp.com/httpdocs/admin/config.inc.php");
include("/home/http/miclubapp/httpdocs/admin/config.inc.php");


//Dejo todo en publicado: no para que quede solo las que vienen por el web service
$sql_update_noticia = $dbo->query("Update Noticia Set Publicar = 'N' Where IDClub = 12 and UsuarioTrCr = 'WebService'");
$sql_update_evento = $dbo->query("Update Evento Set Publicar = 'N' Where IDClub = 12 and UsuarioTrCr = 'WebService'");
$sql_update_galeria = $dbo->query("Update Galeria Set Publicar = 'N' Where IDClub = 12 and UsuarioTrCr = 'WebService'");

//$sql_update_seccnoticia = $dbo->query("Update Seccion Set Publicar = 'N' Where IDClub = 12");
$sql_update_seccevento = $dbo->query("Update SeccionEvento Set Publicar = 'N' Where IDClub = 12");
$sql_update_seccgaleria = $dbo->query("Update SeccionGaleria Set Publicar = 'N' Where IDClub = 12");



function verifica_seccion($TablaSeccion, $datos_noticia){	
	$dbo =& SIMDB::get();
	switch($TablaSeccion):
		case "Seccion":
				//Verifico si la seccion existe para crearla	
				if(!empty($datos_noticia["subtipo"])):
					$id_seccion = $dbo->getFields( "Seccion" , "IDSeccion" , "Nombre = '" . $datos_noticia["subtipo"] . "' and IDClub = 12" );
					if(empty($id_seccion)):
						if(!empty($datos_noticia["tipo"])):
							$id_seccion = $dbo->getFields( "Seccion" , "IDSeccion" , "Nombre = '" . $datos_noticia["tipo"] . "' and IDClub = 12" );
							if(empty($id_seccion)):
								//crear seccion
								$sql_seccion = "Insert into Seccion (IDClub, Nombre, Descripcion, Publicar)
												Values(12,'".$datos_noticia["subtipo"]."','WebService ".$datos_noticia["subtipo"]."','S') ";
								$dbo->query($sql_seccion);				
								$id_seccion = $dbo->lastID();
							endif;	
						endif;    
					endif;    
				else:
					if(!empty($datos_noticia["tipo"])):
						$id_seccion = $dbo->getFields( "Seccion" , "IDSeccion" , "Nombre = '" . $datos_noticia["tipo"] . "' and IDClub = 12" );
						if(empty($id_seccion)):
							//crear seccion
							$sql_seccion = "Insert into Seccion (IDClub, Nombre, Descripcion, Publicar)
											Values(12,'".$datos_noticia["tipo"]."','WebService ".$datos_noticia["tipo"]."','S') ";
							$dbo->query($sql_seccion);				
							$id_seccion = $dbo->lastID();
						endif;	
					endif;		
				endif;
				
				// Si la seccion es vacio la noticia se asocia a una seccion general ya que no puede quedar huerfana	
				if(empty($id_seccion)):
					$id_seccion = 117;
				endif;
		break;
		case "SeccionEvento":			
		
				if(!empty($datos_noticia["subtipo_evento"])):
					$id_seccion = $dbo->getFields( "SeccionEvento" , "IDSeccionEvento" , "Nombre = '" . $datos_noticia["subtipo_evento"] . "' and IDClub = 12" );
					if(empty($id_seccion)):
								//crear seccion
								$sql_seccion = "Insert into SeccionEvento (IDClub, Nombre, Descripcion, Publicar)
												Values(12,'".$datos_noticia["subtipo_evento"]."','WebService ".$datos_noticia["subtipo_evento"]."','S') ";
								$dbo->query($sql_seccion);
								$id_seccion = $dbo->lastID();
					endif;    
				else:
					if(!empty($datos_noticia["tipo_evento"])):
						$id_seccion = $dbo->getFields( "SeccionEvento" , "IDSeccionEvento" , "Nombre = '" . $datos_noticia["tipo_evento"] . "' and IDClub = 12" );
						if(empty($id_seccion)):
									//crear seccion
									$sql_seccion = "Insert into SeccionEvento (IDClub, Nombre, Descripcion, Publicar)
													Values(12,'".$datos_noticia["tipo_evento"]."','WebService ".$datos_noticia["tipo_evento"]."','S') ";
									$dbo->query($sql_seccion);
									$id_seccion = $dbo->lastID();
						endif;    
					endif;
				
				endif;
		
				
				// Si la seccion es vacio la noticia se asocia a una seccion general ya que no puede quedar huerfana	
				if(empty($id_seccion)):
					$id_seccion = 32;
				endif;
		break;	
		case "SeccionGaleria":
		if(!empty($datos_noticia["evento"])):
					$id_seccion = $dbo->getFields( "SeccionGaleria" , "IDSeccionGaleria" , "Nombre = '" . $datos_noticia["evento"] . "' and IDClub = 12" );
					if(empty($id_seccion)):
								//crear seccion
								$sql_seccion = "Insert into SeccionGaleria (IDClub, Nombre, Descripcion, Publicar)
												Values(12,'".$datos_noticia["evento"]."','WebService ".$datos_noticia["evento"]."','S') ";
								$dbo->query($sql_seccion);
								$id_seccion = $dbo->lastID();				
					endif;							    
		endif;			
				
				// Si la seccion es vacio la noticia se asocia a una seccion general ya que no puede quedar huerfana	
				if(empty($id_seccion)):
					$id_seccion = 35;
				endif;
		
		
		break;
				
	endswitch;	
	return $id_seccion;
	
}

$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";

/*****************************************************************************
 NOTICIAS
*****************************************************************************/
	
	$data = file_get_contents("http://clubelrancho.com/contenidoRancho.noticias");
	$noticias = json_decode($data, true);
	
	
	$contenido_noticia = "";
	$id_seccion="";
	unset($array_fecha);
	$fecha_noticia = "";
		
	
		
	foreach ($noticias as $datos_noticia) {	
	
		$contenido_noticia ="";
		$id_seccion = verifica_seccion("Seccion",$datos_noticia);
			
		
		//dejo el contenido en usa sola variable
		foreach($datos_noticia["contenido"] as $datos_contenido):
			$contenido_noticia .= $datos_contenido["texto"]; 
			if(!empty($datos_contenido["url_imagen"])):
				$contenido_noticia .=  '<img src="'.$datos_contenido["url_imagen"].'" width="400px" height="400px">';				
			endif;
			if(!empty($datos_contenido["youtube_id"])):
				$contenido_noticia .=  '<iframe width="400" height="300" src="https://www.youtube.com/embed/'.$datos_contenido["youtube_id"].'" frameborder="0" allowfullscreen></iframe>';
			endif;
			if(!empty($datos_contenido["url_adjunto"])):
				$contenido_noticia .=  '<a href="'.$datos_contenido["url_adjunto"].'">Ver Archivo</a>';				
			endif;
			
			
		endforeach;
		
		$array_fecha = explode("/",$datos_noticia["fecha"]);
		$fecha_noticia = $array_fecha[2]."-".$array_fecha[1]."-".$array_fecha[0];
		if($array_fecha[2]==date("Y")):
		
			$datos_noticia["titulo"] = str_replace("'"," ",$datos_noticia["titulo"]);
			
			//Verifico si la noticia ya existe para actualizarla o crearla
			$id_noticia = $dbo->getFields( "Noticia" , "IDNoticia" , "IDSeccion = '" . $id_seccion . "' and Titular = '".$datos_noticia["titulo"]."'" );
			
			if(!empty($id_seccion))
				$dbo->query("Update Seccion Set Publicar = 'S' Where IDSeccion = '".$id_seccion."' and IDClub = 12");
			
			if(empty($id_noticia)):
				//Inserto Noticia			
				$sql_noticia = "Insert Into Noticia (IDClub, IDSeccion, Titular, Introduccion, Cuerpo, Publicar, FechaInicio, FechaFin, NoticiaFile, UsuarioTrCr, FechaTrCr)
								Values(12,'".$id_seccion."','".$datos_noticia["titulo"]."','".$datos_noticia["resumen"]."','".$contenido_noticia."','S','".$fecha_noticia."','2022-01-01','".$datos_noticia["url_imagen"]."','WebService','".date("Y-m-d H:is")."')";
				$dbo->query($sql_noticia);
			else:
				//Actualizo Noticia				
				$sql_update_noticia = "Update Noticia set  Titular = '".$datos_noticia["titulo"]."', 
														   Introduccion = '".$datos_noticia["resumen"]."',  
														   Cuerpo = '".$contenido_noticia."', 													    
														   FechaInicio = '".$fecha_noticia."', 
														   NoticiaFile = '".$datos_noticia["url_imagen"]."', 
														   UsuarioTrEd = 'WebService', 
														   FechaTrEd = '".date("Y-m-d H:is")."',
														   Publicar = 'S'
														   Where IDNoticia = '".$id_noticia."'
														   ";
				
				$dbo->query($sql_update_noticia);										   
		endif;		
	endif;
		
	}
	
	
	
/*****************************************************************************
 FIN NOTICIAS
*****************************************************************************/


/*****************************************************************************
 EVENTOS
*****************************************************************************/
	
	$data = file_get_contents("http://clubelrancho.com/contenidoRancho.eventos");
	$eventos = json_decode($data, true);
	
	$contenido_evento = "";
	$id_seccion="";
	unset($array_fecha);
	$fecha_noticia = "";
		
	foreach ($eventos as $datos_noticia) {		
	
		 $id_seccion = verifica_seccion("SeccionEvento",$datos_noticia);	
		
		$fecha_noticia = $datos_noticia["ano"]."-01-01";		
		
		//Verifico si la noticia ya existe para actualizarla o crearla
		$id_evento = $dbo->getFields( "Evento" , "IDEvento" , "IDSeccionEvento = '" . $id_seccion . "' and Titular = '".$datos_noticia["titulo"]."'" );
		
		if(!empty($id_seccion))
			$dbo->query("Update SeccionEvento Set Publicar = 'S' Where IDSeccionEvento = '".$id_seccion."' and IDClub = 12");
				
		if(empty($id_evento)):
			//Inserto Evento			
			$sql_noticia = "Insert Into Evento (IDClub, IDSeccionEvento, Titular, Introduccion, Cuerpo, Publicar, FechaEvento, FechaInicio, FechaFin, EventoFile	, Orden, UsuarioTrCr, FechaTrCr)
							Values(12,'".$id_seccion."','".$datos_noticia["titulo"]."','".$datos_noticia["descripcion"]."','".$datos_noticia["descripcion"]."','S','".$fecha_noticia."','".$fecha_noticia."','2022-01-01','".$datos_noticia["imagen_popup"]."','".$datos_noticia["orden"]."','WebService','".date("Y-m-d H:is")."')";
			$dbo->query($sql_noticia);
		else:
			//Actualizo Noticia				
			$sql_update_noticia = "Update Evento set  Titular = '".$datos_noticia["titulo"]."', 
													   Introduccion = '".$datos_noticia["descripcion"]."',  
													   Cuerpo = '".$datos_noticia["descripcion"]."',
													   FechaEvento = '".$fecha_noticia."', 
													   FechaInicio = '".$fecha_noticia."', 
													   EventoFile = '".$datos_noticia["imagen_popup"]."', 
													   Orden = '".$datos_noticia["orden"]."',
													   UsuarioTrEd = 'WebService', 													   
													   FechaTrEd = '".date("Y-m-d H:is")."',
													   Publicar = 'S'
										Where IDEvento = '".$id_evento."'";			
			$dbo->query($sql_update_noticia);										   
		endif;		
	}	


/*****************************************************************************
 FIN EVENTOS
*****************************************************************************/


/*****************************************************************************
 GALERIAS
*****************************************************************************/
	
	$data = file_get_contents("http://clubelrancho.com/contenidoRancho.galerias");
	$galerias = json_decode($data, true);
	
	$contenido_evento = "";
	$id_seccion="";
	unset($array_fecha);
	$fecha_noticia = "";
		
	foreach ($galerias as $datos_noticia) {	
	
		$id_seccion = verifica_seccion("SeccionGaleria",$datos_noticia);	
		
		if(!empty($id_seccion))
			$dbo->query("Update SeccionGaleria Set Publicar = 'S' Where IDSeccionGaleria = '".$id_seccion."' and IDClub = 12");
		
		//Verifico si la galeia ya existe para actualizarla o crearla
		$id_galeria = $dbo->getFields( "Galeria" , "IDGaleria" , "IDSeccionGaleria = '" . $id_seccion . "' and Nombre = '".$datos_noticia["titulo"]."'" );
		if(empty($id_galeria)):		
			/*
			//Guardar imagen en el servidor
			$cad = "";
			for($i=0;$i<8;$i++) {
				$cad .= substr($str,rand(0,62),1);
			}			
			$array_dato_imagen = explode(".",$datos_noticia["portada"]);
			$extension_imagen = end($array_dato_imagen);						
			$numero_imagen = rand(1,100000);
			$nombre_imagen_portada = $numero_imagen."_".$cad.'.'.$extension_imagen;						
			$imagen = file_get_contents($datos_noticia["portada"]);					
			$save = file_put_contents(GALERIA_DIR.$nombre_imagen_portada,$imagen);
			*/
			
			//Inserto Evento			
			$sql_noticia = "Insert Into Galeria (IDClub, IDSeccionGaleria, Nombre, Descripcion, Foto, Fecha, Publicar, UsuarioTrCr, FechaTrCr)
							Values(12,'".$id_seccion."','".$datos_noticia["titulo"]."','".$datos_noticia["titulo"]."','".$datos_noticia["portada"]."','".date("Y-m-d")."','S','WebService','".date("Y-m-d H:is")."')";
			$dbo->query($sql_noticia);
			$id_galeria = $dbo->lastID();
		else:
			
			//Actualizo Noticia				
			$sql_update_noticia = "Update Galeria set  Nombre = '".$datos_noticia["titulo"]."', 
													   Descripcion = '".$datos_noticia["titulo"]."',  													   
													   Fecha = '".date("Y-m-d")."', 													   
													   Foto = '".$datos_noticia["portada"]."',
													   UsuarioTrEd = 'WebService', 
													   FechaTrEd = '".date("Y-m-d H:is")."',
													   Publicar = 'S'
													   Where IDGaleria = '".$id_galeria."'
													   ";
			$dbo->query($sql_update_noticia);										   
		endif;		
		
		
		//$dbo->query("Delete from FotoGaleria Where IDGaleria = '".$id_galeria."'");		
		if(!empty($id_galeria)):			
			//agrego las imagenes			
			if(count($datos_noticia["imagenes"])>0):
				foreach($datos_noticia["imagenes"] as $datos_imagen):
					$id_foto = $dbo->getFields( "FotoGaleria" , "IDFoto" , "IDGaleria = '" . $id_galeria . "' and Foto = '".$datos_imagen."'" );
					if(empty($id_foto)):											
						/*
						//Guardar imagen en el servidor
						$cad = "";
						for($i=0;$i<8;$i++) {
							$cad .= substr($str,rand(0,62),1);
						}
						$array_dato_imagen = explode(".",$datos_imagen);
						$extension_imagen = end($array_dato_imagen);						
						$numero_imagen = rand(1,100000);
						$nombre_guarda_imagen = $numero_imagen."_".$cad.'.'.$extension_imagen;						
						$imagen = file_get_contents($datos_imagen);					
						$save = file_put_contents(GALERIA_DIR.$nombre_guarda_imagen,$imagen);						
						*/
						$sql_inserta_imagen = "Insert Into FotoGaleria (IDGaleria, Nombre, Foto)
												   Values('".$id_galeria."','".$datos_imagen."','".$datos_imagen."')";
						$dbo->query($sql_inserta_imagen);					   
					else:
						$sql_update_imagen = "Update FotoGaleria Set Foto = '".$datos_imagen."' Where IDFoto = '".$id_foto."'";	
						$dbo->query($sql_update_imagen);	 							
					endif;					   					
				endforeach;
			endif;
		endif;
		
		
	}
	
/*****************************************************************************
 FIN GALERIAS
*****************************************************************************/

	
		



echo "Terminado";
?>