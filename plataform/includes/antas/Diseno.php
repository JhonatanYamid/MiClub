 <?

SIMReg::setFromStructure( array(
					"title" => "Diseno",
					"table" => "Diseno",
					"key" => "IDDiseno",
					"mod" => "Diseno"
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




		switch ( $action ) {
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
			
				$files =  SIMFile::upload( $_FILES["Foto1"] , DISENO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto1"] = $files[0]["innername"];				
	
				
				$files =  SIMFile::upload( $_FILES["Foto2"] , DISENO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto2"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto2"] = $files[0]["innername"];				
				
				$files =  SIMFile::upload( $_FILES["Foto3"] , DISENO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto3"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto3"] = $files[0]["innername"];				
				
				
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
						
					
						$files =  SIMFile::upload( $_FILES["Foto1"] , DISENO_DIR , "IMAGE" );
						if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
							SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
						
						$frm["Foto1"] = $files[0]["innername"];				
						
						
						$files =  SIMFile::upload( $_FILES["Foto2"] , DISENO_DIR , "IMAGE" );
						if( empty( $files ) && !empty( $_FILES["Foto2"]["name"] ) )
							SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
						$frm["Foto2"] = $files[0]["innername"];				
						
						$files =  SIMFile::upload( $_FILES["Foto3"] , DISENO_DIR , "IMAGE" );
						if( empty( $files ) && !empty( $_FILES["Foto3"]["name"] ) )
							SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
						$frm["Foto3"] = $files[0]["innername"];				
						
						
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
			
			case "delfoto":
				$foto = $_GET['foto'];
				$campo = $_GET['campo'];
				$id = $_GET['id'];
				$filedelete = DISENO_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id."" );
			break;
			
			
			case "list" :
				$where_array = array();
				$fieldInt = array();
						
				$fieldStr = array ( "Estado" , "Probabilidad" );		 	
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
			<td  class="columnafija" > Nombre </td><td><input id=Nombre type=text size=25  name=Nombre class="input mandatory " title="Nombre" value="<?=$frm[Nombre] ?>"> </td>
			</tr>
			<tr>
			  <td  class="columnafija" >Descripcion</td>
			  <td><textarea rows="5" id=Descripcion cols=60 wrap=virtual class="input mandatory" title="Descripcion" name=Descripcion><?=$frm[Descripcion]?></textarea></td>
			  </tr>
			<tr>
			  <td  class="columnafija" > Clase </td>
			  <td><input id=Clase type=text size=25  name=Clase class="input mandatory " title="Clase" value="<?=$frm[Clase] ?>"></td>
			  </tr>
			<tr>
			  <td  class="columnafija" > Color </td>
			  <td><input name="Color" type="color" value="<?php if (empty($frm["Color"])) { echo "#FFFFFF"; } else{ echo $frm["Color"]; }    ?>" /></td>
			  </tr>
			<tr>
			  <td class="columnafija"><? if (!empty($frm[Foto1])) {
					echo "<img src='".DISENO_ROOT."$frm[Foto1]' width=55 >";
					?>
			    <a
					href="<? echo "?mod=$mod&action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=".$frm[$key]; ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    Foto 1</td>
			  <td><input name="Foto1" id=file class=""
					title="Foto1" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
			<tr>
			  <td class="columnafija"><? if (!empty($frm[Foto2])) {
					echo "<img src='".DISENO_ROOT."$frm[Foto2]' width=55 >";
					?>
			    <a
					href="<? echo "?mod=$mod&action=delfoto&foto=$frm[Foto2]&campo=Foto2&id=".$frm[$key]; ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    Foto 2</td>
			  <td><input name="Foto2" id=file class=""
					title="Logo Grande" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
			<tr>
			  <td class="columnafija"><? if (!empty($frm[Foto3])) {
					echo "<img src='".DISENO_ROOT."$frm[Foto3]' width=55 >";
					?>
			    <a
					href="<? echo "?mod=$mod&action=delfoto&foto=$frm[Foto3]&campo=Foto3&id=".$frm[$key]; ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    Foto 3</td>
			  <td><input name="Foto3" id=file class=""
					title="Logo Peque–o" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
            
			<tr>
			  <td colspan=2 align=center>
			    
			    <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			    <input type="button" onclick="location.href='?mod=Ciudad'" class="submit" value="Cancelar" name="submit">
			    <input type=hidden name=ID value="<? echo $frm[$key] ?>">
			    <input type=hidden name=action value=<?=$newmode?>>
  </td>
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
		
		if( empty( $sql ) )
			$sql =  "SELECT * FROM " . $table . " ORDER BY " . $key;
			
		$result =& SIMUtil::createPag( $sql , 20 );	
	
?>	
	
	
	
<table class="adminheading">
		<tr>
			<th><?php echo SIMReg::get( "title" )?></th>
		</tr>
	</table>
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
					<th class=title colspan=7   ><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?></th>
					
				</tr>


<tr>
					<th class=texto colspan=7  ><?php echo $result["info"]?></th>
					
				</tr>
<tr>
<th align=center valign=middle width=64>Editar</th>
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
  	
<tr class=<%echo SIMUtil::repetition()?'row0':'row1';%>>
<td align=center width=64 ><a href='<?php echo "?mod=" . $mod . "&amp;action=edit&amp;id=" . $r->$key?>'><img src='images/edit.png' border='0'></a></td>
<td nowrap><? echo $r->Nombre ?></td> <td nowrap><? echo $r->Descripcion ?></td> 
<td align=center width=64 ><a href='<? echo "?mod=" . $mod . "&amp;action=del&amp;id=" . $r->$key?>'><img src='images/trash.png' border='0'></a></td></tr>
<? } // END for
?>
<tr>
					<th class=texto colspan=4 nowrap  ><?php echo $result["pages"]?></th>
					
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
	function filtrar(){
?>

<form name="frm" action='<?php echo SIMUtil::lastURI()?>' method="get" >

<table width=100% align=center class="adminlist">
<tr>
	
    <th align="center" class="title" >
		BUSCAR
     </th>
  </tr>
<tr>
	<td align="center" >
	
<legend>Filtrar</legend> 
	<select name="Buscar_por" id="Buscar_por" class="popup">
		<option value=''>Buscar por...</option>
<option value="Nombre">Nombre</option>
<option value="Publicar">Publicar</option>

	</select> <input type="text" size="16" name="QryString" class=input> 
	<select name="Ordenar_por" id=Ordenar_por class="popup">
		<option value=''>Ordenar por </option>
<option value="Nombre">Nombre</option>
<option value="Publicar">Publicar</option>

	</select> de forma 
	<select name="in_order" class="popup" id=in_order>
		<option value="ASC">Ascendente</option>
		<option value="DESC">Descendente</option>
	</select>
	Listar <select name="listar" class="popup">
			<option value="10">10</option>
			<option value="15">15</option>
			<option value="20">20</option>
			<option value="25">25</option>
			<option value="30">30</option>
		</select> 
		<input type="hidden" name="mod" value="<?=$MOD?>">
		<input type="hidden" name="action" value="list">
		<input type="submit" name="submit" value="Buscar" class="submit">
	</td>
</tr>
</table>
</form>
<?		
	}//End function filtrar
?>

