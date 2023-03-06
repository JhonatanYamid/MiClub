<?php



//Encapsulando datos globales
SIMReg::setFromStructure( array(
					"title" => $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$_SESSION[IDClub]."'")." :: " ." Secciones Noticia",
					"table" => "Seccion",
					"key" => "IDSeccion",
					"mod" => "Seccion"
) );

//Para validar los campos del formulario
$array_valida = array(
	"Nombre" => "Nombre"
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
		
		$_POST["param"]["seccion"]["IDPadre"] = SIMNet::post( "IDSeccion" );
		
		if( !SIMNotify::capture( SIMUtil::valida( $_POST["param"]["seccion"] , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST["param"]["seccion"] );
			$frm["Ubicacion"] = implode(",",$frm["Ubicacion"]);
			
			$files =  SIMFile::upload( $_FILES["SeccionImagen"] , IMGSECCION_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["SeccionImagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			
			$frm["SeccionFile"] = $files[0]["innername"];
			
			
			//insertamos los datos del asistente
			$id = $dbo->insert( $frm , $table , $key );
			
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id . "&idlang=".$frm[IDLenguaje]."&m=insertarexito" );
		}
		else
			print_form( $_POST["param"]["seccion"] , "insert" , "Agregar Registro" );
	break;
	
	case "edit":
	
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id"), "array" );		
		print_form( $frm , "update" , "Realizar Cambios" );
		
	break ;
	
	case "update" :
	
		$_POST["param"]["seccion"]["IDPadre"] = SIMNet::post( "IDSeccion" );
		$_POST["param"]["seccion"]["SeccionFile"] = $_FILES["SeccionImagen"]["name"];
		
		if( !SIMNotify::capture( SIMUtil::valida( $_POST["param"]["seccion"] , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST["param"]["seccion"] );
		
			
			$files =  SIMFile::upload( $_FILES["SeccionImagen"] , IMGSECCION_DIR , "IMAGE" );
			if( empty( $files ) && !empty( $_FILES["SeccionImagen"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			
			$frm["SeccionFile"] = $files[0]["innername"];
			
			
			$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"));
			
			
			$frm = $dbo->fetchById( $table , $key , $id , "array" );
			
			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
			
			print_form( $frm , "update" ,  "Realizar Cambios" );
		}
		else
			print_form( $_POST["param"]["seccion"] , "update" ,  "Realizar Cambios" );
	break;
	
	case "del":
		
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id")." AND IDLenguaje = ".$_GET['idlang'], "array" );
		
		print_form( $frm , "delete" , "Remover Registro" );
	break ;
			
	case "delete" :
		$dbo =& SIMDB::get();
		$dbo->deleteById( $table , $key , SIMNet::reqInt("ID"));
		
		SIMHTML::jsAlert("Registro Eliminado Correctamente");
		
		SIMHTML::jsRedirect( "?mod=" . $mod . "&amp;m=eliminarrexito" );
	break;
	
	case "DelImgSec":
		$doceliminar = IMGSECCION_DIR.$dbo->getFields( "Seccion" , "SeccionFile" , "IDSeccion = '" . $_GET[id] );
		unlink($doceliminar);
		$dbo->query("UPDATE Seccion SET SeccionFile = '' WHERE IDSeccion = $_GET[id] LIMIT 1 ;");
		SIMHTML::jsAlert("Imagen Eliminada Correctamente");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id] );
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

<?php include( "includes/menuclub.php" ); ?>

<?php
//imprime el HTML de errores
SIMNotify::each();

?>
<div id="tabsform">
	<div id="seccion">
		<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" method="post" enctype="multipart/form-data" class="formvalida">
		<table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">	
						<tr>
							<td class="columnafija"> Seccion Padre </td>
							<td>
							<input type="hidden" id="IDSeccion" name="IDSeccion" value="<?php echo $frm["IDPadre"];?>">
							<input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields( "Seccion" , "Nombre" , "IDSeccion = '" . $frm["IDPadre"] . "'" )?>" readonly>
							<a href="PopupSeccion.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;"><img alt="Seccion" src="images/magnifier.png" border="0"></a>							</td>
						</tr>
						<tr>
							<td> Nombre </td>
							<td><input id="param[seccion][Nombre]" type="text" size="25" title="Nombre" name="param[seccion][Nombre]" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" /> </td>
						</tr>
						<tr>
							<td> Descripcion </td>
							<td>
							<textarea rows="5" cols="60" id="param[seccion][Descripcion]" title="Descripcion" name="param[seccion][Descripcion]" class="input"><?php echo $frm["Descripcion"] ?></textarea>							</td>
						</tr>
						<tr>
							<td> Publicar </td>
							<td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , "param[seccion][Publicar]" , "title=\"Publicar\"" )?> </td>
						</tr>
						<tr>
							<td> Orden </td>
							<td><input id="param[seccion][Orden]" type="text" size="25" title="Orden" name="param[seccion][Orden]" class="input mandatory" value="<?php echo $frm["Orden"] ?>" /> </td>
						</tr>
						<tr>
							<td> Imagen Seccion </td>
							<td>
                            
							<?php 
							
							if($frm["SeccionFile"])
							{
								?>
								<img alt="<?php echo $frm["SeccionFile"] ?>" src="<?php echo IMGSECCION_ROOT.$frm["SeccionFile"]?>">
								<a href="<? echo "?mod=" . SIMReg::get( "mod" ) . "&action=DelImgSec&id=" .$frm[ $key ]?>"><img src='images/trash.png' border='0'></a>
							<?php 
							}
							else
							{
							?>
							<input type="file" name="SeccionImagen" id="SeccionImagen" class="popup" title="Seccion Imagen">
							<?php 
							}
							?>							</td>
						</tr>
						<tr>
							<td> URL </td>
							<td><input id="param[seccion][URL]" type="text" size="25" title="URL" name="param[seccion][URL]" class="input" value="<?php echo $frm["URL"] ?>" /> </td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="submit" name="submit" value="<?php echo $submit_caption ?>" class="submit" />
								<input type="button" onclick="location.href='?mod=<?php echo SIMReg::get( "mod" ); ?>'" class="submit" value="Regresar" name="submit3"></td>
						</tr>
					</table>
			  </td>
			</tr>
		</table>
		<input type="hidden" name="param[seccion][ID]"  id="param[seccion][ID]" value="<?php echo $frm[ $key ] ?>" />
		<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
		<input type="hidden" name="param[seccion][IDClub]" id="param[seccion][IDClub]" value="<?php if(empty($frm["IDClub"])) echo $_SESSION[IDClub]; else echo $frm["IDClub"];  ?>" />
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
	
	//Funcion Crea Arbol
	function CreaArbolSecciones($ValorSecciones)
	{
		$dbo =& SIMDB::get();
		$Padre=$ValorSecciones['IDSeccion'];
		$RegistrosHijos=$dbo->all("Seccion","IDPadre = '".$Padre."'");
			while($RHijos=$dbo->fetchArray( $RegistrosHijos ))
					$ArrayHijos[$RHijos['IDSeccion']]=$RHijos;
		?>
		<li>
			<span class="folder"><a href="?mod=Seccion&action=edit&id=<?php echo $ValorSecciones['IDSeccion']?>&idlang=1"><?php echo $ValorSecciones['Nombre'];?></a></span>
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

if(!empty($_SESSION[IDClub])):
	$condicion=" and IDClub = " . $_SESSION[IDClub];
endif;

//Secciones Padre
$Secciones=$dbo->all("Seccion","IDPadre = '0' " .$condicion);
while( $RSeccioones = $dbo->fetchArray( $Secciones ) )
	$ArraySecciones[$RSeccioones[IDSeccion]]=$RSeccioones;	
?>

<table class=adminheading>
		<tr>
			<th> 
			<?php echo SIMReg::get( "title" )?> </th>
			
			
		</tr>
</table>

<?php include( "includes/menuclub.php" ); ?>

<table class="adminheading">
	<tbody><tr>
		<th>Seleccione la secci&oacute;n deseada haciendo clic.<span class="title">
		  <br>
          <input type="button" onclick="location.href='?mod=Seccion&action=add'" class="submit" value="Crear Nueva <?php  echo strtoupper( SIMReg::get( "table" ) ); ?>" name="submit2" >
		</span></th>
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
?>