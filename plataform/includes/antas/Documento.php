 <?

SIMReg::setFromStructure( array(
					"title" => $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$_SESSION[IDClub]."'")." :: " . " Documento",
					"table" => "Documento",
					"key" => "IDDocumento",
					"mod" => "Documento"
) );


//para validar los campos del formulario
$array_valida = array(  
	 "Nombre" => "Nombre"  	
); 



//extraemos las variables
$table = SIMReg::get( "table" );
$key = SIMReg::get( "key" );
$mod = SIMReg::get( "mod" );
$dbo =& SIMDB::get();

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );	




		switch ( SIMNet::req( "action" ) ) {
			case "add" :
				print_form( "" , "insert" , "Agregar Registro" );
			break;
			
			case "insert" :	
				if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					
			//UPLOAD de imagenes
			if(isset($_FILES)){
			
				$files =  SIMFile::upload( $_FILES["Archivo1"] , DOCUMENTO_DIR , "DOC" );
				if( empty( $files ) && !empty( $_FILES["Archivo1"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Archivo1"] = $files[0]["innername"];				
	
				
				
				
			}//end if				
					
					//insertamos los datos
					$id = $dbo->insert( $frm , $table , $key );
					
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id . "&m=insertarexito" );
				}
				else
					print_form( $_POST , "insert" , "Agregar Registro" );
			break;
			
			case "edit":
			
				$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );		
				print_form( $frm , "update" , "Realizar Cambios" );
				
			break ;
			
			case "update" :	
				if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					
					//UPLOAD de imagenes
					if(isset($_FILES)){
						
					
						$files =  SIMFile::upload( $_FILES["Archivo1"] , DOCUMENTO_DIR , "DOC" );
						if( empty( $files ) && !empty( $_FILES["Archivo1"]["name"] ) )
							SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
						
						$frm["Archivo1"] = $files[0]["innername"];				
						
						
					}//end if								
					
					$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );
					
					$frm = $dbo->fetchById( $table , $key , $id , "array" );
					
					SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
					
					print_form( $frm , "update" ,  "Realizar Cambios" );
				}
				else
					print_form( $_POST , "update" ,  "Realizar Cambios" );	
			break;
			
			case "del":
				$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id"), "array" );
				print_form( $frm , "delete" , "Remover Registro" );
			break ;
					
			case "delete" :
				$dbo =& SIMDB::get();
				$dbo->deleteById( $table , $key , SIMNet::reqInt("ID") );
				SIMHTML::jsAlert( "Ciudad Eliminada correctamente" );
				SIMHTML::jsRedirect( "?mod=" . $mod . "&amp;m=eliminarrexito" );
			break;
			
			case "delDoc":
				$foto = $_GET['doc'];
				$campo = $_GET['campo'];
				$id = $_GET['id'];
				$filedelete = DOCUMENTO_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = ".$_GET[id]."   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."" );
			break;
			
			
			case "list" :
				$where_array = array();
				if(!empty($_SESSION[IDClub])):
					$_GET[IDClub] = $_SESSION[IDClub];
				endif;
				
				$fieldInt = array("IDClub","IDTipoArchivo");
						
				$fieldStr = array ( "Nombre" , "Email" );		 	
				$listjoin = array();
				$fromjoin = array();
					 
				$wherejoin = array();
												
				$params = SIMUtil::filter( $fieldInt , $fieldStr , $fromjoin , $listjoin , $where_array , $wherejoin );
						
				$sql = " SELECT " . $params["fields"] . " FROM " . $table . " V " . $params["from"] . $params["where"];
				
				list_r( $sql );
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
	$mod = SIMReg::get( "mod" );
?>


<table class=adminheading>
		<tr>
			<th> 
			<?php echo SIMReg::get( "title" )?> </th>
			
			
		</tr>
</table>

<?php include( "includes/menuclub.php" )?> 

<?
//imprime el HTML de errores
SIMNotify::each();
?>


<div id="tabsform">
	<div id="">
		<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" method="post" enctype="multipart/form-data" class="formvalida">
		<table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">	

			
			<tr>
			  <td  class="columnafija" >Tipo Archivo</td>
			  <td><?php echo SIMHTML::formPopUp( "TipoArchivo" , "Nombre" , "Nombre" , "IDTipoArchivo" , $frm["IDTipoArchivo"] , "[Seleccione el Tipo de Archivo]" , "popup mandatory" , "title = \"IDTipo Archivo\"" )?></td>
			  </tr>
			<tr>
			  <td  class="columnafija" >Servicio</td>
			  <td><?php echo SIMHTML::formPopUp( "Servicio" , "Nombre" , "Nombre" , "IDServicio" , $frm["IDServicio"] , "[Seleccione el Tipo de Servicio]" , "popup" , "title = \"Servicio\"" )?>(solo si es un reglamento)</td>
			  </tr>
			<tr>
			<td  class="columnafija" > Titulo </td><td><input id=Nombre type=text size=25  name=Nombre class="input mandatory " title="Nombre" value="<?=$frm[Nombre] ?>"> </td>
			</tr>
			<tr>
			  <td  class="columnafija" >Subtitulo</td>
			  <td><input id=Subtitular type=text size=25  name=Nombre2 class="input" title="Subtitular" value="<?=$frm[Subtitular] ?>"></td>
			  </tr>
			<tr>
			  <td  class="columnafija" >Fecha</td>
			  <td><input id="Fecha" type="text" size="10" title="Fecha" name="Fecha" class="input calendar" value="<?php echo $frm["Fecha"] ?>" readonly /></td>
			  </tr>
			<tr>
			  <td  class="columnafija" >Descripcion</td>
			  <td><textarea rows="5" id=Descripcion cols=60 wrap=virtual class="input mandatory" title="Descripcion" name=Descripcion><?=$frm[Descripcion]?></textarea></td>
			  </tr>
			<tr>
			  <td class="columnafija">
			    Archivo </td>
			  <td>
			    <? if (!empty($frm[Archivo1])) { ?>
			    <a target="_blank" href="<?php echo DOCUMENTO_ROOT.$frm[Archivo1] ?>"><?php echo $frm[Archivo1]; ?></a>
			    <a
					href="<? echo "?mod=$mod&action=delDoc&doc=$frm[Archivo1]&campo=Archivo1&id=".$frm[$key]; ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    <input name="Archivo1" id=file class=""
					title="Archivo1" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
			<tr>
			  <td> Publicar </td>
			  <td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , "Publicar" , "title=\"Publicar\"" )?></td>
			  </tr>
            
			<tr>
			  <td colspan=2 align=center>
			    
			    <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			    <input type="button" onclick="location.href='?mod=Documento'" class="submit" value="Cancelar" name="submit">
			    <input type=hidden name=ID value="<? echo $frm[$key] ?>">
			    <input type=hidden name=action value=<?=$newmode?>>
			    <input type="hidden" name="IDClub" value="<?php if(empty($frm["IDClub"])) echo $_SESSION[IDClub]; else echo $frm["IDClub"];  ?>" /></td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
</form>

			
</div>
</div>


<?
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		$key = SIMReg::get( "key" );
		$table = SIMReg::get( "table" );
		$mod =  SIMReg::get( "mod" );
		
		if(!empty($_SESSION[IDClub])):
			$condicion = " and IDClub = '".$_SESSION[IDClub]."'";
		endif;
		
		if( empty( $sql ) )
			$sql =  "SELECT * FROM " . $table . " Where 1 ".$condicion." ORDER BY " . $key;
			
		$result =& SIMUtil::createPag( $sql , 20 );	
	
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


<table width=100%  cellpadding=0 cellspacing=0 align=center>
	<tr>
		<td>
			<table class=adminlist width=100% >
	
	<tr>
					<th class=title colspan=8   ><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?>
				    <input type="button" onclick="location.href='?mod=Documento&action=add'" class="submit" value="Crear Nuevo <?php  echo strtoupper( SIMReg::get( "table" ) ); ?>" name="submit2" ></th>
					
				</tr>


<tr>
					<th class=texto colspan=8  ><?php echo $result["info"]?></th>
					
				</tr>
<tr>
<th align=center valign=middle width=64>Editar</th>
<th>Tipo</th>
				<th>
					Nombre&nbsp;
				</th>
				<th>
					Descripcion&nbsp;
				</th>
					
<th align=center valign=middle width=64>Eliminar</th>
</tr>

<? 
$dbo =&SIMDB::get();
while( $r = $dbo->object( $result["result"] ) )
{
	
?>
  	
<tr class=<?echo SIMUtil::repetition()?'row0':'row1';?>>
<td align=center width=64 ><a href='<?php echo "?mod=" . $mod . "&amp;action=edit&amp;id=" . $r->$key?>'><img src='images/edit.png' border='0'></a></td>
<td nowrap><? echo $dbo->getFields( "TipoArchivo" , "Nombre" , "IDTipoArchivo = '".$r->IDTipoArchivo."'"); ?></td>
<td nowrap><? echo $r->Nombre ?></td> <td nowrap><? echo $r->Descripcion ?></td> 
<td align=center width=64 ><a href='<? echo "?mod=" . $mod . "&amp;action=del&amp;id=" . $r->$key?>'><img src='images/trash.png' border='0'></a></td></tr>
<? } // END for
?>
<tr>
					<th class=texto colspan=5 nowrap  ><?php echo $result["pages"]?></th>
					
				</tr>		
</table></td>
</tr>
</table>	

<? 			
}// End if$rows


else
{
	SIMNotify::capture( "No se han encontrado registros" , "error" );
	//imprime el HTML de errores
	SIMNotify::each();
}//end else



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
						<td width="100">Nombre</td>
						<td width="131"><input type="text" size="14" value="<?php echo SIMNet::get( "Nombre" )?>" name="Nombre" id="Nombre" class="input" /></td>
						<td width="100">Tipo</td>
						<td width="131"><?php echo SIMHTML::formPopUp( "TipoArchivo" , "Nombre" , "Nombre" , "IDTipoArchivo" , $frm["IDTipoArchivo"] , "[Seleccione el Tipo de Archivo]" , "popup mandatory" , "title = \"IDTipo Archivo\"" )?></td>
						<td width="100">&nbsp;</td>
						<td width="131">&nbsp;</td>
					</tr>
					<tr>
						<td width="100"></td>	
						<td width="131"></td>
						<td width="100"></td>
						<td width="131"></td>
						<td width="100"></td>
						<td width="131"></td>
					</tr>
					<tr>
						<td colspan="3" align="center"><input type="submit" name="buscar" class="submit" value="Buscar"></td>						
						<td colspan="3" align="center"><input type="reset" name="submit" class="submit" value="Limpiar Campos"></td>
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

