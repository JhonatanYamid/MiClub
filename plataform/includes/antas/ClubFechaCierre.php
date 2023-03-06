 <?

SIMReg::setFromStructure( array(
					"title" => $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$_SESSION[IDClub]."'")." :: " ." Fecha Cierre",
					"table" => "ClubFechaCierre",
					"key" => "IDClubFechaCierre",
					"mod" => "ClubFechaCierre"
) );


//para validar los campos del formulario
$array_valida = array(  
	 "Fecha" => "Fecha" , "Motivo" => "Motivo" 	
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
					
					$files =  SIMFile::upload( $_FILES["RestauranteImagen"] , IMGEVENTO_DIR , "IMAGE" );
					if( empty( $files ) && !empty( $_FILES["RestauranteImagen"]["name"] ) )
						SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
					
					$frm["RestauranteFile"] = $files[0]["innername"];
					
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
					
					$files =  SIMFile::upload( $_FILES["RestauranteImagen"] , IMGEVENTO_DIR , "IMAGE" );
					if( empty( $files ) && !empty( $_FILES["RestauranteImagen"]["name"] ) )
						SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
					
					$frm["RestauranteFile"] = $files[0]["innername"];
					
					
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
				SIMHTML::jsAlert("Pais Eliminado Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&amp;m=eliminarrexito" );
			break;
			
			
			case "DelImgNot":
				$campo = $_GET['cam'];
				if($campo=="SWF"){
					$doceliminar = SWFEvento_DIR.$dbo->getFields( "Evento" , "$campo" , "IDEvento = '" . $_GET[id] . "'" );
					unlink($doceliminar);
					$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
					SIMHTML::jsAlert("SWF eliminado Correctamente");
				}else{
					$doceliminar = IMGEVENTO_DIR.$dbo->getFields( "Restaurante" , "$campo" , "IDRestaurante = '" . $_GET[id] . "'" );
					unlink($doceliminar);
					$dbo->query("UPDATE Restaurante SET $campo = '' WHERE IDRestaurante = $_GET[id] LIMIT 1 ;");
					SIMHTML::jsAlert("Imagen Eliminada Correctamente");	
				}
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id] );
				exit;
			break;

			
			case "list" :
				$where_array = array();
				
				if(!empty($_SESSION[IDClub])):
					$_GET[IDClub] = $_SESSION[IDClub];
				endif;
				
				$fieldInt = array("IDClub");
						
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
  <td  class="columnafija" >Fecha Ingreso</td>
  <td><input id="Fecha" type="text" size="10" title="Fecha" name="Fecha" class="input mandatory calendar" value="<?php echo $frm["Fecha"] ?>" readonly /></td>
</tr>
<tr>
  <td  class="columnafija" >Motivo</td>
  <td><textarea name="Motivo" id="Motivo" cols="40" rows="5"><?php echo $frm["Motivo"] ?></textarea></td>
</tr>
<tr>
  <td colspan=2 align=center>
    
    <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
    <input type="button" onclick="location.href='?mod=Invitado'" class="submit" value="Cancelar" name="submit">
    <input type=hidden name=ID value="<? echo $frm[$key] ?>">
    <input type=hidden name=action value=<?=$newmode?>>
    <input type="hidden" name="IDClub" value="<?php if(empty($frm["IDClub"])) echo $_SESSION[IDClub]; else echo $frm["IDClub"];  ?>" />
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
		
		
		
		if(!empty($_SESSION[IDClub])):
			$condicion = " and IDClub = '".$_SESSION[IDClub]."'";
		endif;
		
		if( empty( $sql ) )
			$sql = "SELECT * FROM " . $table . " Where 1 ".$condicion." ORDER BY FechaTrCr DESC";
			
			
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
					<th class=title colspan=5   ><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?>
				    <input type="button" onclick="location.href='?mod=Invitado&action=add'" class="submit" value="Crear Invitado <?php  echo strtoupper( SIMReg::get( "table" ) ); ?>" name="submit2" ></th>
					
				</tr>


<tr>
					<th class=texto colspan=5  ><?php echo $result["info"]?></th>
					
				</tr>
<tr>
<th align=center valign=middle width=64>Editar</th>
<th>
					Fecha&nbsp;
				</th>
				<th>
					Motivo&nbsp;
				</th>
					
<th align=center valign=middle width=64>Eliminar</th>
</tr>

<? 
$dbo =&SIMDB::get();
while( $r = $dbo->object( $result["result"] ) )
{
?>
  	
<tr class=<?php echo SIMUtil::repetition()?'row0':'row1'; ?>>
<td align=center width=64 ><a href='<?php echo "?mod=" . $mod . "&amp;action=edit&amp;id=" . $r->$key?>'><img src='images/edit.png' border='0'></a></td>
<td nowrap><? echo $r->Fecha ?></td> <td nowrap><? echo $r->Motivo ?></td> 
<td align=center width=64 ><a href='<? echo "?mod=" . $mod . "&amp;action=del&amp;id=" . $r->$key?>'><img src='images/trash.png' border='0'></a></td>
</tr>
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
						<td width="100">&nbsp;</td>
						<td width="131">&nbsp;</td>
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

