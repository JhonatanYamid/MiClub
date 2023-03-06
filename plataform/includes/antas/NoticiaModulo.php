 <?

SIMReg::setFromStructure( array(
					"title" => "Noticia Modulo",
					"table" => "NoticiaModulo",
					"key" => "IDNoticiaModulo",
					"mod" => "NoticiaModulo"
) );


//para validar los campos del formulario
$array_valida = array(  
	"Nombre" => "Nombre" , "Publicar" => "Publicar" ,  "Orden" => "Orden" 	
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
				/*if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					$files =  SIMFile::upload( $_FILES["Foto"] , IMGNOTICIA_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
					//insertamos los datos
					$id = $dbo->insert( $frm , $table , $key );
					
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id . "&m=insertarexito" );
				}
				else
					print_form( $_POST , "insert" , "Agregar Registro" );*/
				if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					$frm["Ubicacion"] = implode(",",$frm["Ubicacion"]);
					
					
					//$frm['IDNoticiaModulo']=$frm['IDPadre'];
					$files =  SIMFile::upload( $_FILES["Foto"] , IMGNOTICIA_DIR, "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del archivo. Verifique que no contenga errores y que el tipo de archivo sea permitido." , "error" );
			$frm["Foto"] = $files[0]["innername"];
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
				/*if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					$files =  SIMFile::upload( $_FILES["Foto"] , IMGNOTICIA_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
					$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );
					
					$frm = $dbo->fetchById( $table , $key , $id , "array" );
					
					SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
					
					print_form( $frm , "update" ,  "Realizar Cambios" );
				}
				else
					print_form( $_POST , "update" ,  "Realizar Cambios" );*/
				if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					//$frm['IDNoticiaModulo']=$frm['IDPadre'];
					$frm["Ubicacion"] = implode(",",$frm["Ubicacion"]);

					$files =  SIMFile::upload( $_FILES["Foto"] , IMGNOTICIA_DIR, "IMAGE"  );
					
			if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga del archivo. Verifique que no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm["Foto"] = $files[0]["innername"];
			
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
				
				SIMHTML::jsRedirect( "?mod=" . $mod . "&amp;m=eliminarrexito" );
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
			
			case "DelImgNot":
				$doceliminar = IMGNOTICIA_DIR.$dbo->getFields( $table , "Foto" , "IDNoticiaModulo = '" . $_GET[id] . "'" );
				unlink($doceliminar);
				$dbo->query("UPDATE $table SET Foto = '' WHERE IDNoticiaModulo = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id );
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
	$dbo =& SIMDB::get();
	$URLPopUp = "PopUpSeccion.php";
?>
<script language="javascript">
		function callCategorias(){
		    var url = "<?= $URLPopUp ?>";
		    url     += "?id_div=id_div";
		    url     += "&id_campo_code=idpadre";
		    url     += "&id_campo_name=id_name";
		    window.open(url,'categorias','toolbar=no,directories=no,location=no,status=no,menubar=no,resizable=yes,scrollbars=yes,width=500,height=400');
		}
	</script>

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
							 <td>Padre </td>
							 <td>
							<input type="hidden" id="IDPadre" name="IDPadre" value="<?php echo $frm["IDPadre"];?>">
							<input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields( "NoticiaModulo" , "Nombre" , "IDNoticiaModulo = '" . $frm["IDPadre"] . "'" )?>" readonly="readonly">
							<a href="PopupSeccion.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;"><img alt="Seccion" src="images/magnifier.png" border="0"></a>	<a style="cursor:pointer;" onclick="document.frm.NombreSeccion.value = '';document.frm.IDPadre.value = '';">Borrar</a>						</td>				
						</tr>
			<tr>
			<td  class="columnafija" > Nombre </td><td><input id=Nombre type=text size=25  name=Nombre class="input mandatory " title="Nombre" value="<?=$frm[Nombre] ?>"> </td>
			</tr>
			<tr>
			  <td  class="columnafija" > Descripcion </td><td><textarea rows="5" id=Descripcion cols=60 wrap=virtual class="input" title="Descripcion" name=Descripcion><?=$frm[Descripcion]?></textarea></td>
			  </tr>
			<tr>
							<td> Foto ( 950x240 )  </td>
							<td>
							<?php 
							if($frm["Foto"])
							{
								?>
								<img alt="<?php echo $frm["Foto"] ?>" src="<?php echo IMGNOTICIA_ROOT.$frm["Foto"]?>">
								<a href="<? echo "?mod=" . SIMReg::get( "mod" ) . "&action=DelImgNot&id=" .$frm[ $key ]?>"><img src='images/trash.png' border='0'></a>
							<?php 
							}
							else
							{
							?>
							<input type="file" name="Foto" id="Foto" class="popup" title="Foto">
							<?php 
							}
							?>							</td>
						</tr>
			<tr>
			<tr>
			<td  class="columnafija" > Publicar </td><td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?></td>
			</tr>
			<tr>
			<td  class="columnafija" > Privada </td><td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Privada"] , 'Privada' , "class='input mandatory'" ) ?></td>
			</tr>
			<tr>
			<td  class="columnafija" > Ubicacion </td><td><? 
								$arrayop = array();
								$arrayop = array('Menu','Seccion','Left'); 
								$array_opcion  = array();
									foreach($arrayop AS $opcion)
										$array_opcion[$opcion] = $opcion;
									$selection = split(',',$frm[Ubicacion]);
									echo SIMHTML::formcheckgroup($array_opcion,$selection,"Ubicacion[]");
								?></td>
			</tr>
			<tr>
			<td  class="columnafija" > Orden </td><td><input id=Orden type=text size=25  name=Orden class="input mandatory " title="Orden" value="<?=$frm[Orden] ?>"> </td>
			</tr>
			<tr>
			  <td  class="columnafija" >Color (solo secciones padres)</td>
			  <td><select name="Estilo" id="Estilo">
              
              	<option value="" >Seleccione</option>
			    <option value="1" style="color:#9e0b0f;" <?php if($frm[Estilo]=="1"){?> selected="selected" <?php } ?> >Estilo 1</option>
			    <option value="2" style="color:#007236;" <?php if($frm[Estilo]=="2"){?> selected="selected" <?php } ?>>Estilo 2</option>
			    <option value="3" style="color:#0072bc;" <?php if($frm[Estilo]=="3"){?> selected="selected" <?php } ?>>Estilo 3</option>
			    <option value="4" style="color:#92278f;" <?php if($frm[Estilo]=="4"){?> selected="selected" <?php } ?>>Estilo 4</option>			    
			    </select></td>
			  </tr>
			
			<tr>
			<td  class="columnafija" > URL </td><td><textarea rows="5" id=URL cols=60 wrap=virtual class="input" title="URL" name=URL><?=$frm[URL]?></textarea></td>
			</tr>
			<tr>
			<td  class="columnafija" > SEO_Title </td><td><textarea rows="5" id=SEO_Title cols=60 wrap=virtual class="input" title="SEO_Title" name=SEO_Title><?=$frm[SEO_Title]?></textarea></td>
			</tr>
			<tr>
			<td  class="columnafija" > SEO_Description </td><td><textarea rows="5" id=SEO_Description cols=60 wrap=virtual class="input" title="SEO_Description" name=SEO_Description><?=$frm[SEO_Description]?></textarea></td>
			</tr>
			<tr>
			<td  class="columnafija" > SEO_KeyWords </td><td><textarea rows="5" id=SEO_KeyWords cols=60 wrap=virtual class="input" title="SEO_KeyWords" name=SEO_KeyWords><?=$frm[SEO_KeyWords]?></textarea></td>
			</tr>
			<tr>
			<td colspan=2 align=center>
			
			<input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			<input type="button" onclick="location.href='?mod=NoticiaModulo'" class="submit" value="Cancelar" name="submit">
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
	function list_r( $sql = "" )
{	
	
	//Funcion Crea Arbol
	function CreaArbolSecciones($ValorSecciones)
	{
		$dbo =& SIMDB::get();
		$Padre=$ValorSecciones['IDNoticiaModulo'];
		$RegistrosHijos=$dbo->all("NoticiaModulo","IDPadre = '".$Padre."'");
			while($RHijos=$dbo->fetchArray( $RegistrosHijos ))
					$ArrayHijos[$RHijos['IDNoticiaModulo']]=$RHijos;
		?>
		<li>
			<span class="folder"><a href="?mod=NoticiaModulo&action=edit&id=<?php echo $ValorSecciones['IDNoticiaModulo']?>&idlang=1"><?php echo $ValorSecciones['Nombre'];?></a></span>
		<?php
		if( $ArrayHijos != Null )
		{
			?>
			<ul>
			<?php
			foreach($ArrayHijos as $clave => $valor)
				CreaArbolSecciones($valor);
			?>
			</ul>
			<?php 
		}
		?>
		</li>
		<?php 	
		return true;
	}	
	//fin funcion
	
$dbo =& SIMDB::get();
$key = SIMReg::get( "key" );
$table = SIMReg::get( "table" );

//Secciones Padre
$Secciones=$dbo->all("NoticiaModulo","IDPadre = '0' order by Orden");
while( $RSeccioones = $dbo->fetchArray( $Secciones ) )
	$ArraySecciones[$RSeccioones[IDNoticiaModulo]]=$RSeccioones;	
?>
<table class="adminheading">
	<tbody><tr>
		<th>Seleccione la secci&oacute;n deseada haciendo clic.</th>
	</tr>
</tbody></table>
<br>
		<ul id="ArbolSecciones" class="filetree">
			<?php
				foreach($ArraySecciones as $ClaveSeccion => $ValorSecciones)							
						CreaArbolSecciones($ValorSecciones)
			?>
		</ul>
<?php
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
		<option value="IDNoticiaModulo">IDNoticiaModulo</option>
<option value="IDPadre">IDPadre</option>
<option value="Nombre">Nombre</option>
<option value="Descripcion">Descripcion</option>
<option value="Plantilla">Plantilla</option>
<option value="Publicar">Publicar</option>
<option value="Privada">Privada</option>
<option value="Ubicacion">Ubicacion</option>
<option value="Empresa">Empresa</option>
<option value="Orden">Orden</option>
<option value="Foto">Foto</option>
<option value="FotoName">FotoName</option>
<option value="URL">URL</option>
<option value="SEO_Title">SEO_Title</option>
<option value="SEO_Description">SEO_Description</option>
<option value="SEO_KeyWords">SEO_KeyWords</option>

	</select> <input type="text" size="16" name="QryString" class=input> 
	<select name="Ordenar_por" id=Ordenar_por class="popup">
		<option value=''>Ordenar por </option>
			<option value="IDNoticiaModulo">IDNoticiaModulo</option>
<option value="IDPadre">IDPadre</option>
<option value="Nombre">Nombre</option>
<option value="Descripcion">Descripcion</option>
<option value="Plantilla">Plantilla</option>
<option value="Publicar">Publicar</option>
<option value="Privada">Privada</option>
<option value="Ubicacion">Ubicacion</option>
<option value="Empresa">Empresa</option>
<option value="Orden">Orden</option>
<option value="Foto">Foto</option>
<option value="FotoName">FotoName</option>
<option value="URL">URL</option>
<option value="SEO_Title">SEO_Title</option>
<option value="SEO_Description">SEO_Description</option>
<option value="SEO_KeyWords">SEO_KeyWords</option>

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

