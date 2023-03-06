<?php
include_once("jscript/fckeditor/fckeditor.php"); // FCKEditor

//Encapsulando datos globales
SIMReg::setFromStructure( array(
					"title" => $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$_SESSION[IDClub]."'")." :: " ." Eventos",
					"table" => "Evento",
					"key" => "IDEvento",
					"mod" => "Evento"
) );

//Para validar los campos del formulario
$array_valida = array(
	"Titular" => "Titular"
);


//permisos
SIMUtil::verify( 0 , SIMUser::get( "Nivel" ) );

//extraemos las variables
$table = SIMReg::get( "table" );
$key = SIMReg::get( "key" );
$mod = SIMReg::get( "mod" );
$dbo =& SIMDB::get();

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );	


switch ( SIMNet::req( "action" ) ) 
{
	case "add" :
		print_form( "" , "insert" , "Agregar Registro" );
	break;
	
	case "insert" :
		
		/*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/
		$_POST["param"]["Evento"]["IDSeccionEvento"] = SIMNet::post( "IDSeccionEvento" );
		$_POST["param"]["Evento"]["Introduccion"] = $_POST[ "Introduccion" ];
		$_POST["param"]["Evento"]["Cuerpo"] = $_POST[ "Cuerpo" ];
		$_POST["param"]["Evento"]["CuerpoEmail"] = $_POST[ "CuerpoEmail" ];
		$_POST["param"]["Evento"]["Tag"] = SIMNet::post( "Tag" );
		if( !SIMNotify::capture( SIMUtil::valida( $_POST["param"]["Evento"] , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST["param"]["Evento"] );
			
			$files =  SIMFile::upload( $_FILES["EventoImagen"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["EventoImagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["EventoFile"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto1"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto1"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto2"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto2"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto2"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto3"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto3"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto3"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["FotoDestacada"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["FotoDestacada"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["FotoDestacada"] = $files[0]["innername"];
			
			
			$files =  SIMFile::upload( $_FILES["SWF"] , SWFEvento_DIR );
			if( empty( $files ) && !empty( $_FILES["SWF"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["SWF"] = $files[0]["innername"];
			
			//insertamos los datos del asistente
			$id = $dbo->insert( $frm , $table , $key );
			
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id ."&m=insertarexito" );
		}
		else
			print_form( $_POST["param"]["Evento"] , "insert" , "Agregar Registro" );
	break;
	
	case "edit":
	
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );		
		print_form( $frm , "update" , "Realizar Cambios" );
		
	break ;
	
	case "update" :
	
		$_POST["param"]["Evento"]["IDSeccionEvento"] = SIMNet::post( "IDSeccionEvento" );
		$_POST["param"]["Evento"]["Introduccion"] = $_POST[ "Introduccion" ];
		$_POST["param"]["Evento"]["Cuerpo"] = $_POST[ "Cuerpo" ];
		$_POST["param"]["Evento"]["CuerpoEmail"] = $_POST[ "CuerpoEmail" ];
		$_POST["param"]["Evento"]["Tag"] = SIMNet::post( "Tag" );
		$_POST["param"]["Evento"]["EventoFile"] = $_FILES["EventoImagen"]["name"];
		
		if( !SIMNotify::capture( SIMUtil::valida( $_POST["param"]["Evento"] , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST["param"]["Evento"] );
			
			
			$files =  SIMFile::upload( $_FILES["EventoImagen"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["EventoImagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["EventoFile"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto1"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto1"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto2"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto2"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto2"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["Foto3"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto3"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto3"] = $files[0]["innername"];
			
			$files =  SIMFile::upload( $_FILES["FotoDestacada"] , IMGEVENTO_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["FotoDestacada"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["FotoDestacada"] = $files[0]["innername"];
			
			
			$files =  SIMFile::upload( $_FILES["SWF"] , SWFEvento_DIR );
			if( empty( $files ) && !empty( $_FILES["SWF"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del SWF. Verifique que el SWF no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["SWF"] = $files[0]["innername"];

			
			$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"),"" );
			
			
			$frm = $dbo->fetchById( $table , $key , $id , "array" );
			
			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
			
			print_form( $frm , "update" ,  "Realizar Cambios" );
		}
		else
			print_form( $_POST["param"]["Evento"] , "update" ,  "Realizar Cambios" );
	break;
	
	case "del":
		
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id"), "array" );
		
		print_form( $frm , "delete" , "Remover Registro" );
	break ;
			
	case "delete" :
		$dbo =& SIMDB::get();
		$dbo->deleteById( $table , $key , SIMNet::reqInt("ID") );
		
		SIMHTML::jsAlert("Registro Eliminado Correctamente");
		
		SIMHTML::jsRedirect( "?mod=" . $mod . "&amp;m=eliminarrexito" );
	break;
	
	case "list" :			    
		$where_array = array();
		
		if(!empty($_SESSION[IDClub])):
					$_GET[IDClub] = $_SESSION[IDClub];
		endif;
				
		$fieldInt = array("IDClub");
		
		$fieldStr = array ( "Titular" );		 	
			 		
		$fromjoin = $fieldInt;
			 	
		$wherejoin = $fieldInt;
			 					      	
		$params = SIMUtil::filter( $fieldInt , $fieldStr , $fromjoin , $where_array , $wherejoin );
			 
		$sql = " SELECT V.* FROM " . $table . " V " . $params["from"] . $params["where"] . "";
			
		
		
		list_r( $sql );
	break;


	case "DelImgNot":
		$campo = $_GET['cam'];
		if($campo=="SWF"){
			$doceliminar = SWFEvento_DIR.$dbo->getFields( "Evento" , "$campo" , "IDEvento = '" . $_GET[id] . "'" );
			unlink($doceliminar);
			$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
			SIMHTML::jsAlert("SWF eliminado Correctamente");
		}else{
			$doceliminar = IMGEVENTO_DIR.$dbo->getFields( "Evento" , "$campo" , "IDEvento = '" . $_GET[id] . "'" );
			unlink($doceliminar);
			$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
			SIMHTML::jsAlert("Imagen Eliminada Correctamente");	
		}
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id] );
		exit;
	break;

	
	case "SubirDocumento":
		$campos = array("IDEvento" => "$_POST[id]","Nombre" => "$_POST[Nombre]","Descripcion" => "$_POST[Descripcion]","NombreFile" => "","TypeFile" => "","SizeFile" => "","ExtenFile" => "","UsuarioTrEd" => SIMUser::get( "Nombre" ) ,"FechaTrEd" => date("Y-m-d"));
		$Doc = new SIMArchivo($_FILES[DocumentoEvento] , $_FILES[DocumentoEvento][tmp_name] , FILEvento_DIR , "DocumentoEvento" ,$campos , "IDDocumentoEvento");
		$mensaje = $Doc->SubeArchivo();
		SIMHTML::jsAlert($mensaje);
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id."#DocumentosEvento" );
		exit;
	break;
	
	case "ModificaDocumento":
		$dbo->query("UPDATE DocumentoEvento SET Nombre = '$_POST[Nombre]', Descripcion = '$_POST[Descripcion]' WHERE IDDocumentoEvento = $_POST[IDDocumentoEvento] LIMIT 1 ;");
		SIMHTML::jsAlert("Documento Modificado");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_POST[id]."#DocumentosEvento" );
		exit;
	break;
	
	case "EliminaDocumento":
		$doceliminar = FILEvento_DIR.$dbo->getFields( "DocumentoEvento" , "NombreFile" , "IDDocumentoEvento = '" . $_GET[IDDocumentoEvento] . "'" );
		unlink($doceliminar);
		$dbo->query("DELETE FROM DocumentoEvento WHERE IDDocumentoEvento = $_GET[IDDocumentoEvento] LIMIT 1 ;");
		SIMHTML::jsAlert("Documento Eliminada Correctamente");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."#DocumentosEvento" );
		exit;
	break;
	
	
	default : 
		list_r();
	break;
		
} // End switch



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form( $frm = "" , $newmode , $submit_caption )
{
	$dbo =& SIMDB::get();
	$key = SIMReg::get( "key" );
	$table = SIMReg::get( "table" );
?>
<table class="adminheading">
	<tr>
		<th><?php echo SIMReg::get( "title" )?></th>
	</tr>
</table>

<?php include( "includes/menuclub.php" )?> 
<?php
//imprime el HTML de errores
SIMNotify::each();
include( "includes/tabs.html" );
?>
<div id="tabsform">
	<ul>
		<li>
			<a href="#EventoActual" title="Eventos"><span>Evento</span></a>
		</li>
	</ul>
	<div id="EventoActual">
		<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" method="post" enctype="multipart/form-data" class="formvalida">
		<table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">	
						<tr>
							<td class="columnafija"> Seccion </td>
							<td>
							<input type="hidden" id="IDSeccionEvento" name="IDSeccionEvento" value="<?php echo $frm["IDSeccionEvento"];?>">
							<input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields( "SeccionEvento" , "Nombre" , "IDSeccionEvento = '" . $frm["IDSeccionEvento"] . "'" )?>" readonly>
							<a href="PopupSeccionEvento.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;"><img alt="Seccion" src="images/magnifier.png" border="0"></a>							</td>
						</tr>
						<tr>
							<td> Titular </td>
							<td><input id="param[Evento][Titular]" type="text" size="25" title="Titular" name="param[Evento][Titular]" class="input mandatory" value="<?php echo $frm["Titular"] ?>" /> </td>
						</tr>
                        <tr>
							<td> Introduccion </td>
							<td>
							<textarea rows="5" cols="50" id="Introduccion" name="Introduccion" class="input"><?php echo $frm["Introduccion"] ?></textarea>							</td>
						</tr>
						<tr>
						  <td>Lugar</td>
						  <td><input id="param[Evento][Lugar]" type="text" size="25" title="Lugar" name="param[Evento][Lugar]" class="input mandatory" value="<?php echo $frm["Lugar"] ?>" /></td>
					  </tr>
						<tr>
						  <td>Fecha Evento</td>
						  <td><input id="param[Evento][FechaEvento]" type="text" size="10" title="Fecha Evento" name="param[Evento][FechaEvento]" class="input mandatory calendar" value="<?php echo $frm["FechaEvento"] ?>" readonly /></td>
					  </tr>
						<tr>
						  <td>Fecha Fin Evento</td>
						  <td><input id="param[Evento][FechaFinEvento]" type="text" size="10" title="Fecha Fin Evento" name="param[Evento][FechaFinEvento]" class="input mandatory calendar" value="<?php echo $frm["FechaFinEvento"] ?>" readonly /></td>
					  </tr>
						<tr>
						  <td>Descripcion Valor $</td>
						  <td><textarea rows="5" cols="50" id="param[Evento][Valor]" name="param[Evento][Valor]" class="input"><?php echo $frm["Valor"] ?></textarea></td>
					  </tr>
						<tr>
						  <td>EmailContacto</td>
						  <td><input id="param[Evento][EmailContacto]" type="text" size="25" title="Email Contacto" name="param[Evento][EmailContacto]" class="input mandatory" value="<?php echo $frm["EmailContacto"] ?>" /></td>
					  </tr>
						<tr>
							<td> Cuerpo </td>
							<td>
							<?php
								$oCuerpo = new FCKeditor( "Cuerpo" ) ;
								$oCuerpo->BasePath = "jscript/fckeditor/";
								$oCuerpo->Height = 400;
								//$oCuerpo->EnterMode = "p";
								$oCuerpo->Value =  $frm["Cuerpo"];
								$oCuerpo->Create() ;
							?>							</td>
						</tr>
						<tr>
						  <td>Cuerpo para Email</td>
						  <td><?php
								$oCuerpo = new FCKeditor( "CuerpoEmail" ) ;
								$oCuerpo->BasePath = "jscript/fckeditor/";
								$oCuerpo->Height = 400;
								//$oCuerpo->EnterMode = "p";
								$oCuerpo->Value =  $frm["CuerpoEmail"];
								$oCuerpo->Create() ;
							?></td>
					  </tr>
						<tr>
							<td> Publicar </td>
							<td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , "param[Evento][Publicar]" , "title=\"Publicar\"" )?> </td>
						</tr>
						<tr>
							<td> Destacada Home </td>
							<td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["Destacada"] , "param[Evento][Destacada]" , "title=\"Destacada\"" )?> </td>
						</tr>
						<tr>
							<td> Orden </td>
							<td><input id="param[Evento][Orden]" type="text" size="25" title="Orden" name="param[Evento][Orden]" class="input mandatory" value="<?php echo $frm["Orden"] ?>" /> </td>
						</tr>
						<tr>
							<td> Fecha Inicio Publicacion</td>
							<td><input id="param[Evento][FechaInicio]" type="text" size="10" title="Fecha Inicio" name="param[Evento][FechaInicio]" class="input mandatory calendar" value="<?php echo $frm["FechaInicio"] ?>" readonly /> </td>
						</tr>
						<tr>
							<td> Fecha Fin Publicacion</td>
							<td><input id="param[Evento][FechaFin]" type="text" size="10" title="Fecha Fin" name="param[Evento][FechaFin]" class="input mandatory calendar" value="<?php echo $frm["FechaFin"] ?>" readonly /> </td>
						</tr>
						<tr>
							<td> Imagen Evento  </td>
							<td>
                           
							<?php 
							if($frm["EventoFile"])
							{
								?>
								<img alt="<?php echo $frm["EventoFile"] ?>" src="<?php echo IMGEVENTO_ROOT.$frm["EventoFile"]?>">
								<a href="<? echo "?mod=" . SIMReg::get( "mod" ) . "&action=DelImgNot&cam=EventoFile&id=" .$frm[ $key ]?>"><img src='images/trash.png' border='0'></a>
							<?php 
							}
							else
							{
							?>
							<input type="file" name="EventoImagen" id="EventoImagen" class="popup" title="Evento Imagen">
							<?php 
							}
							?>							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="submit" name="submit" value="<?php echo $submit_caption ?>" class="submit" />							</td>
						</tr>
					</table>
			  </td>
			</tr>
		</table>
					
		<input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
		<input type="hidden" name="Visitas"  id="Visitas" value="<?php echo $frm[ "Visitas" ] ?>" />
		<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
		<input type="hidden" name="param[Evento][IDClub]" id="param[Evento][IDClub]" value="<?php if(empty($frm["IDClub"])) echo $_SESSION[IDClub]; else echo $frm["IDClub"];  ?>" />
		</form>
	</div>
	
</div>

<?php

}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
function list_r( $sql = "" )
{	
	$key = SIMReg::get( "key" );
	$table = SIMReg::get( "table" );
	
	if(!empty($_SESSION[IDClub])):
			$condicion = " and IDClub = '".$_SESSION[IDClub]."'";
	endif;
	
	if( empty( $sql ) )
	 	$sql =  "SELECT * FROM " . $table . " Where 1 ".$condicion." ORDER BY " . $key;
	 	
 	$result =& SIMUtil::createPag( $sql , 100 );	
?>
	<table class="adminheading">
		<tr>
			<th><?php echo SIMReg::get( "title" )?></th>
		</tr>
	</table>
    
    <?php include( "includes/menuclub.php" )?> 
    
	<?php
	filtrar();
	
	if( $result["rows"] > 0 )
	{			
		//imprime el HTML de errores
		SIMNotify::each();
	?>	
	<table width="100%" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
				<table class="adminlist">
					<tr>
						<th class="title" colspan="13"><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?>
					    <input type="button" onclick="location.href='?mod=Evento&action=add'" class="submit" value="Crear Nuevo <?php  echo strtoupper( SIMReg::get( "table" ) ); ?>" name="submit2" ></th>
					</tr>
					<tr>
						<th class="texto" colspan="13"><?php echo $result["info"]?></th>
					</tr>
					<tr>
						<th align="center" valign="middle" width="64">Editar</th>
						<th>Seccion</th>
						<th>Titular</th>
						<th>Fecha</th>
						<th>Lugar</th>
						<th>Publicar</th>
						<th align="center" valign="middle" width="64">Eliminar</th>
					</tr>
	
	<?php
		$dbo =&SIMDB::get();
		while( $r = $dbo->object( $result["result"] ) )
		{
	?>
					<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
						<td align="center" width="64">
							<a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&amp;action=edit&amp;id=" . $r->$key."&amp;idlang=1"?>"><img src='images/edit.png' border='0'></a>						</td>
						<td><?php echo $dbo->getFields( "SeccionEvento" , "Nombre" , "IDSeccionEvento = '" . $r->IDSeccionEvento . "'" )?></td>
						<td><?php echo $r->Titular?></td>
						<td><?php echo $r->FechaEvento?></td>
						<td><?php echo $r->Lugar?></td>
						<td><?php echo $r->Publicar?></td>
						<td align="center" width="64">
							<a href="<? echo "?mod=" . SIMReg::get( "mod" ) . "&amp;action=del&amp;id=" . $r->$key."&amp;idlang=1"?>"><img src='images/trash.png' border='0'></a>						</td>
					</tr>
	<?php 
		}
	?>
					<tr>
						<th class="texto" colspan="13" width="64"><?php echo $result["pages"]?></th>
					</tr>
				</table>
		  </td>
		</tr>
	</table>	
	
	<?php		
	}
	else
	{
		SIMNotify::capture( "No se han encontrado registros" , "error" );
		//imprime el HTML de errores
		SIMNotify::each();
	}
}// Enf function list()				

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
function filtrar()
{
?>
<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" method="get">			
<table width="100%" align="center" class="adminlist">
		<tr>
	   		<th align="center" class="title">BUSCAR</th>
	  	</tr>
		<tr>
			<td align="center">
				<table width="100%" border="0" cellspacing="2" cellpadding="0">
					<tr>
						<td width="100">Titular</td>
						<td width="131"><input type="text" size="14" value="" name="Titular" id="Titular" class="input" /></td>
						<td width="100">&nbsp;</td>
						<td width="131">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="131">&nbsp;</td>
					</tr>
					<tr>
						<td width="100">&nbsp;</td>
						<td width="131">&nbsp;</td>
						<td>&nbsp;</td>
						<td><input type="submit" name="buscar" class="submit" value="Buscar"></td>
						<td></td>
						<td><input type="reset" name="submit" class="submit" value="Limpiar Campos"></td>
					</tr>
				</table>
			</td>
		</tr>
</table>
<input type="hidden" name="mod" id="mod" value="<?php echo SIMReg::get( "mod" )?>" />
<input type="hidden" name="action" id="action" value="list" />
</form>
<?		
	}//End function filtrar
?>