<script type="text/javascript">
	/* FUNCION PARA MOSTRAR CARGAR MAS FOTOS */
	 $(document).ready(function(){
	 $("#masfotos").click(function () {
    	  $("#CargarImg").toggle("slow");
    	});
	});
</script>
<?php

include_once("jscript/fckeditor/fckeditor.php"); // FCKEditor
include_once("lib/Busqueda.php");

// Encapsulado de datos globales
SIMReg::setFromStructure(array(
						"title" => $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$_SESSION[IDClub]."'")." :: " . " Galerias",
						"table" => "Galeria",
						"key"  => "IDGaleria",
						"mod" => "Galeria"
						) );

/*						
	 Arreglo empleado en la validacion de campos, contiene como llave el id del campo y como valor el title
 que sera mostrado en caso de que la validacion sea negativa, recuerde que esto solo valida
 campos obligatorios, es decir que no esten vacios. Por lo mismo los siguientes campos no aparecen:
	"Foto" => "Fotografia que acompa&ntilde;a la Galeria", "IDGaleria" => "Identificador",
*/
$array_validacion = array(						
						"Nombre" => "Nombre",
						"Descripcion" => "Descripcion"
						
						);





// extraccion de variables
$table = SIMReg::get( "table" );
$key = SIMReg::get( "key" );
$mod = SIMReg::get( "mod" );

// establecimiento del lenguaje; ponga estas dos lineas en todos los scripts para permitir manejar lenguaje
$idleng = SIMReg::get( "Lenguaje" );
$_POST["param"]["Galeria"]["IDLenguaje"] = $idleng;

// Obtener una referencia al objeto de conexion a la base de datos
$dbo = & SIMDB::get();

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );
if($action=="add")
{
?>
<script type="text/javascript">
	/* FUNCION PARA LOS TABS */
	$(function() {
         $('#ContenedorGaleria').tabs(1,{ disabled: [2,3,4,5] });
    });
</script>
<?	
}//end if
else
{
?>
<script type="text/javascript">
	/* FUNCION PARA LOS TABS */
	$(function() {
         $('#ContenedorGaleria').tabs(<?=$tab?>);
    });
</script>
<?
}//end else


switch (  SIMNet::req("action")  ) 
{
	case "add":
		print_form( "" , "insert" , "Agregra Registro" );
	break;
	
	case "insert":				
		// establecer el arreglo con los datos a validar
		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_validacion ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST );
			
			if( empty( $_FILES["Foto"]["name"] ) ){
				$id = $dbo->insert( $frm , $table , $key );
			}
			else
			{
				$files =  SIMFile::upload( $_FILES["Foto"] , GALERIA_DIR , "IMAGE" );
				if( empty( $files ) )
				{
					SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
					print_form( $frm , "insert" , "Agregar Registro" );
					exit;
				}
				
				$frm["Foto"] = $files[0]["innername"];
				$frm["FotoName"] = $files[0]["innername"];			
				$id = $dbo->insert( $frm , $table , $key );
			}					
			
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id . "&m=insertarexito" );
		}
		else
			print_form( $_POST , "insert" , "Agregar Registro" );
	break;
	
	case "edit":
		$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
		
		
		if( $frm[ $key ] == '' ){
			$frm[ $key ] = SIMNet::reqInt("id");
			print_form($frm, "insert", "Agregar Traduccion");
		}else{
			print_form($frm, "update", "Realizar Cambios");	
		}		
	break;
	
	case "update":
		// establecer el arreglo con los datos a validar
		
	
		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			

			
			$frm =  $_POST ;
			
			if( empty( $_FILES["Foto"]["name"] ) )
			{
				$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"),  array( "Foto" ) );
			}//end if
			else
			{
				$files =  SIMFile::upload( $_FILES["Foto"] , GALERIA_DIR , "IMAGE" );
				if( empty( $files ) )
				{
					SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
					print_form( $frm , "insert" , "Agregar Registro" );
					exit;
				}
				
				$frm["Foto"] = $files[0]["innername"];
				$frm["FotoName"] = $files[0]["innername"];
				$frm["Foto"] = $files[0]["innername"];
				
				
				
				$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );
			}					
			
			$frm = $dbo->fetchById( $table , $key , $id , "array" );
			
			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
			
			print_form( $frm , "update" ,  "Realizar Cambios" );
		}
		else
			print_form( $_POST , "update" ,  "Realizar Cambios" );
	break;
	
	case "del":	
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id"), "array");
		print_form( $frm , "delete" , "Remover Registro");
	break;
	
	case "delete":
		$dbo = & SIMDB::get();
		$dbo->deleteById( $table , $key , SIMNet::reqInt("ID"));		
		SIMHTML::jsRedirect("?mod=" . $mod . "&amp;m=eliminarexito");
	break;
	
	case "delfoto":
				$foto = $_GET['foto'];
				$campo = $_GET['campo'];
				$id = $_GET['id'];
				$filedelete = GALERIA_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."" );
			break;
	

	case "Galeria":
			
			$files =  SIMFile::upload( $_FILES , GALERIA_DIR , "IMAGE" );

			foreach( $files as $llave => $archivo ){
				$sql_foto = "INSERT INTO FotoGaleria ( IDGaleria, Nombre, Foto, FotoSize, FotoType, FechaTrCr, UsuarioTrCr ) VALUES ( '" . $_POST["ID"] . "','" . $archivo["origname"] . "','" . $archivo["innername"] . "','" . $archivo["size"] . "','" . $archivo["type"] . "',NOW(), '" . SIMUser::get( "Nombre" ) . "' )";
				$dbo->query( $sql_foto );
			}//end for

			if( empty( $files ) )
			{
				
				$frm = $dbo->fetchById( $table , $key , $_POST["ID"] , "array" );
				SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
				print_form( $frm , "insert" , "Agregar Registro" );
				exit;
			}//end if
			
			
			echo( "<script type='text/javascript'>location.href='?mod=Galeria&action=edit&id=$_GET[id]&tab=2&m=insertarexito#GaleriaGaleria'</script>" );
	break;
	
	case "delfotogaleria" :
		$foto = $dbo->getFields( "FotoGaleria" , array( "Nombre", "IDFoto", "Foto") , "IDFoto = '$_GET[IDFoto]'");
		$archivo = GALERIA_DIR . "/" .$foto["Foto"];
		unlink( $archivo ); 
		$dbo->query("DELETE FROM FotoGaleria WHERE IDFoto = '$_GET[IDFoto]' ");
		echo( "<script type='text/javascript'>
		alert('Foto (" . $foto["Foto"] . ") Eliminada Correctamente');
		location.href='?mod=Galeria&action=edit&id=$_GET[id]&tab=2#GaleriaGaleria';
		</script>");
    break;
	
	case "InsertarVideoGaleria":
		$frm = SIMUtil::varsLOG( $_POST );
		
		$files =  SIMFile::upload( $_FILES["NombreFile"] , GALERIA_DIR, "IMAGE"  );
		if( empty( $files ) && !empty( $_FILES["NombreFile"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
		$frm["NombreFile"] = $files[0]["name"];
		$frm["File"] = $files[0]["name"];
		
		$id = $dbo->insert( $frm , "GaleriaVideo" , "IDGaleriaVideo" );
		SIMHTML::jsAlert("Registro Exitoso");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDGaleria] ."#GaleriaVideo" );
		exit;
	break;
	
	case "ModificaGaleriaVideo":
		               
				$frm = SIMUtil::varsLOG( $_POST );
				 $files =  SIMFile::upload( $_FILES["NombreFile"] , IMGNOTICIA_DIR , "IMAGE" );
				$frm["File"] =  $files[0]["name"];
				$dbo->update( $frm , "GaleriaVideo" , "IDGaleriaVideo" , $frm[IDGaleriaVideo] );
			   
				SIMHTML::jsAlert("Modificacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDGaleria] ."#GaleriaVideo" );
				exit;
	break;
	
	 case "EliminaGaleriaVideo":
					$id = $dbo->query( "DELETE FROM GaleriaVideo WHERE IDGaleriaVideo   = '".$_GET[IDGaleriaVideo]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "#GaleriaVideo" ); 
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
	$key = SIMReg::get( "key" );
	$table = SIMReg::get( "table" );
	$mod = SIMReg::get( "mod" );
	
	// Obtener referencia BD
	$dbo =& SIMDB::get();
	$URLPopUp = "PopUpSeccion.php";
	// Instanciacion del editor de palabras
  	
	$editorFCK = new FCKeditor( "Cuerpo" , "lib/fckeditor/" , "80%" , 350 , $frm["Cuerpo"] , 'br' ) ;

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
<table class="adminheading">
	<tr>
		<th><?= SIMReg::get("title") ?></th>
	</tr>
</table>

<?php include( "includes/menuclub.php" )?> 

<?	
// imprime el HTML de errores
SIMNotify::each();
include( "includes/tabs.html" );

$mes_sub="Cargar Enlace";
if($newmode != "insert")
{
	/* TRAEMOS TODAS LAS FOTOS */
	$qry_fotos = $dbo->query( "SELECT * FROM FotoGaleria WHERE IDGaleria= $frm[$key] ;" );
	while( $r_fotos = $dbo->fetchArray( $qry_fotos ) )
		$array_fotos[ $r_fotos[IDFoto] ] = $r_fotos;
}
?>


<div id="ContenedorGaleria" style="z-index: 3;">
    <ul>
       <li><a href="#Galeria"><span>Galeria</span></a></li>
       <li><a href="#GaleriaGaleria"><span>Galeria Fotos</span></a></li>
    </ul>
    <div id="Galeria">
        <form name="frm" id="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" class="formvalida">
        <table class="adminform">
            <tr>
                <th>&nbsp;Datos <?php echo $frm["Nombre"] ?></th>
            </tr>
            <tr>
                <td>
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                  <td class="columnafija"> Seccion </td>
                  <td><input type="hidden" id="IDSeccionGaleria" name="IDSeccionGaleria" value="<?php echo $frm["IDSeccionGaleria"];?>">
                    <input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields( "SeccionGaleria" , "Nombre" , "IDSeccionGaleria = '" . $frm["IDSeccionGaleria"] . "'" )?>" readonly>
                    <a href="PopupSeccionGaleria.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;"><img alt="Seccion" src="images/magnifier.png" border="0"></a></td>
                </tr>
                <tr>
                    <td width="100"> Nombre </td>
                    <td><input id="Nombre" type="text" size="50"  name="Nombre" title="Nombre" class="input" value="<?=$frm["Nombre"] ?>" /> Un texto peque&ntilde;o por favor.</td>
                </tr>
                <tr>
                    <td> Descripcion </td>
                    <td>
                    
<textarea rows="5" cols="60" id="Descripcion" name="Descripcion" title="Descripcion" class="input mandatory" ><?php echo $frm["Descripcion"] ?></textarea>                    </td>
                </tr>
                <tr>
                  <td class="columnafija"><? if (!empty($frm[Foto])) {
					echo "<img src='".GALERIA_ROOT."$frm[Foto]' width=55 >";
					?>
                    <a
					href="<? echo "?mod=$mod&action=delfoto&foto=$frm[Foto]&campo=Foto&id=".$frm[$key]; ?>"> <img src='images/trash.png' border='0'></a>
                    <?
				}// END if
				?>
                    Foto </td>
                  <td><input name="Foto" id=file class=""
					title="Foto" type="file" size="25" style="font-size: 10px"></td>
                </tr>
                <tr>
                  <td>Fecha </td>
                  <td><input id="Fecha" type="text" size="10" title="Fecha" name="Fecha" class="input mandatory calendar" value="<?php echo $frm["Fecha"] ?>" readonly /></td>
                </tr>
                <!-- 
                <tr>
                    <td class="columnafija"> Seccion </td>
                    <td><?php //echo formpopup("GaleriaModulo","Nombre","IDGaleriaModulo","IDGaleriaModulo",$frm["IDGaleriaModulo"] , "[Seleccione La Seccion]" , "popup mandatory" , "title = \"Seccion\"" )?> </td>
                </tr>
                -->
                 <tr>
                    <td> Publicar </td>
                    <td><?= SIMHTML::formRadioGroup( SIMResources::$sino , SIMResources::$sino[ substr( $frm[ Publicar ] , 0 , 1 ) ] , "Publicar" ) ?></td>
                 </tr>
                 <tr>
                   <td> Publicar en el Home</td>
                   <td><?= SIMHTML::formRadioGroup( SIMResources::$sino , SIMResources::$sino[ substr( $frm["Home"] , 0 , 1 ) ] , "Home" ) ?></td>
                 </tr>
                 <tr>
                   <td> Galeria Destacada<br /> (primera en la seccion de la galeria)</td>
                   <td><?= SIMHTML::formRadioGroup( SIMResources::$sino , SIMResources::$sino[ substr( $frm["Destacada"] , 0 , 1 ) ] , "Destacada" ) ?></td>
                 </tr>
                 <tr>
                    <td colspan="2" align="center">
                    	
                        
                        				<input type="hidden" name="IDClub" value="<?php if(empty($frm["IDClub"])) echo $_SESSION[IDClub]; else echo $frm["IDClub"];  ?>" />
                        				<input type="hidden" name="ID" id="ID"  value="<?= $frm[ $key ] ?>"  />
				<input type="hidden" name="action"  id="action"  value="<?= $newmode ?>" />

                        
                  
                    <input type="submit" name="submit" value="<?php echo $submit_caption ?>" class="submit" />                    </td>
                </tr>
                </table>
              </td>
            </tr>
        </table>
    </form>
    </div>
    <!--Fin De Galeria--> 
	<div id="GaleriaGaleria">
       <table class="adminform">
            <tr>
                <th>&nbsp;Galeria De <?php echo $frm["Nombre"] ?></th>
            </tr>
            <tr>
                <td>
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td width="283">
                    <input type="button" name="masfotos" value="Agregar Imagenes" id="masfotos" class="submit"/>
                               </td>
					<td >
                    	<table width="100%" style="display:none;" id="CargarImg" border="0">
                        	<form name="frmFoto" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" class="formvalida">
                        	<tr>
                            	
                                    
                                    <?
									$numcols = 2;
									$contador = 1;
                                    for( $i = 1; $i <= 10; $i++ )
									{
                                    ?>	
                                    	<td>
                                        <input type="file" name="fichero_<?=$i?>" id="req" />
                                        </td>
                                    <?
										if( $contador % $numcols == 0 )
										{
											echo "</tr><tr>";
											$contador = 0;
										}//end if
										$contador++;
                                    }//end if
                                    ?>
                                    	
                                    
                             </tr>
                             <tr>
                             	<td colspan="<?=$numcols?>">
                                	<input type="hidden" name="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" value="Galeria" />
                                    <input type="submit" name="submit" value="Cargar Imagenes" class="submit" />
                                </td>
                             </tr>
                             </form>
                          </table>
                    </td>
                </tr>                
                </table>
             	</td>
            </tr>
            <tr>
                    <td>
                    <table width="100%">
					<?
						if($array_fotos!=Null)
						{
							?>
							<tr>
							<? 
							$cont=1;
							$modulo=0;
						    foreach($array_fotos as $clave_fotos => $valor_fotos)
							{
							?>
	                        <td align="center" valign="middle" nowrap class=row<?if(($cont%2)==0) echo "1"; else echo "2";?>>
	                            <? if (!empty($valor_fotos["Foto"]))
								{
	                                $ruta= GALERIA_ROOT . $valor_fotos["Foto"];
	                                $tam = @getimagesize($ruta);
	                                $w=$tam[0]+105;
	                                $h=$tam[1]+130;
	                            ?>
								<a href="javascript:;" onclick="PopupPic('<?=$ruta?>','$w','$w')">
									<img src='<?=$ruta ?>?<?=rand( 1,100 );?>' width="300" border=0>
								</a>
								<a href="<? echo "?mod=$mod&action=delfotogaleria&id=$frm[$key]&IDFoto=$valor_fotos[IDFoto]"?>">
								<img src="images/trash.png" border='0'>
								</a>
								<?
								}// END if
								?>
                                
								<table border="0" cellspacing="4" cellpadding="0">
									<tr>
	                                    <td colspan="2" >
	                                    	<!--
                                            <form action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data">
		                                    	<table>
		                                    		<tr>
		                                    			<td colspan="2">Descripcion: </td>
		                                    		</tr> 
		                                    		<tr>
		                                    			<td colspan="2">
		                                    			<textarea rows="5" cols="30" name="DescripcionFotoImg" class="input"><? echo $valor_fotos["Descripcion"] ?></textarea>
		                                    			<input type="hidden" value="desGaleFoto" name="action" /> <input type="hidden" value="<?php echo $valor_fotos['IDFoto']?>" name="IDFotoGaleria" /> </td>
		                                    		</tr>
		                                    		<tr>
		                                    			<td colspan="2"><input type="submit" class="submit" value="Enviar"  /> </td>
		                                    		</tr> 
		                                    	</table>
	                                    	</form> 
	                                    		-->	
	                                    </td>	                                    
	                                    </tr>
	                                <tr>
	                                <tr>
	                                    <td width=70><b>Nombre:</b></td>
	                                    <td><? echo $valor_fotos["Nombre"] ?></td>
	                                    </tr>
	                                <tr>
	                                    <td width=70><b>Tama&ntilde;o:</b></td>
	                                    <td><? echo $valor_fotos["FotoSize"] ?></td>
	                                </tr>
	                                <tr>
	                                    <td width=70><b>Tipo:</b></td>
	                                    <td><? echo $valor_fotos["FotoType"] ?></td>
	                                </tr>
                                     <tr>
	                                    <td width=70><b>Orden:</b></td>
	                                    <td><input type="number" name="Orden<?php echo $valor_fotos['IDFoto']?>" id="Orden<?php echo $valor_fotos['IDFoto']?>" value="<?php echo $valor_fotos["Orden"] ?>">  </td>
	                                </tr>
                                    <tr>
	                                    <td width=70><b>Texto:</b></td>
	                                    <td><textarea name="Descripcion<?php echo $valor_fotos['IDFoto']?>" id="Descripcion<?php echo $valor_fotos['IDFoto']?>" rows="4" cols="20"><?php echo $valor_fotos["Descripcion"] ?></textarea>
                                        </td>
	                                </tr>
                                    <tr>
	                                    
	                                    <td colspan="2" align="center"><input type="submit" class="submit guardar_fotogaleria" value="Guardar"  alt="<?php echo $valor_fotos['IDFoto']?>"  /></td>
	                                </tr>
								</table>
                               
                                
								<?
								if(($cont%2)==0)
									echo "</td></tr><tr>";
								$cont++;
							}//end for
						}//end if
				
                    ?>
                    	</tr>
                    </table>
					</td>
                </tr>
         </table> 
    </div>
    <!--Fin De Galeria-->  
    <!--Fin Video Galeria--> 
       
</div>
<?php


}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
function list_r($sql = "", $limit = 20){
	$key = SIMReg::get( "key" );
	$table = SIMReg::get("table");
	$mod = SIMReg::get("mod");
	$idleng = SIMReg::get( "Lenguaje" );
	
	if(!empty($_SESSION[IDClub])):
			$condicion = " and IDClub = '".$_SESSION[IDClub]."'";
		endif;
	
	if($sql == ""){
		
		$sql = "SELECT * FROM " . $table . " Where 1 ".$condicion." ORDER BY FechaTrCr DESC";
		}

	//  Creamos el paginador	
	$result =& SIMUtil::createPag( $sql , 20 );	

	
				
?>
<table class=adminheading>
	<tr>
		<th><?= SIMReg::get("title") ?></th>
	</tr>
</table>

<?php include( "includes/menuclub.php" )?> 

<?php
if( $result["rows"] > 0 ){
		SIMNotify::each();
?>	
<table width="100%"  cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td>
			<table class="adminlist" width="100%">
				<tr>
					<th class="title" colspan="6"><?= strtoupper( SIMReg::get("title") ) . ": Listado" ?>
				    <input type="button" onclick="location.href='?mod=Galeria&action=add'" class="submit" value="Crear Nueva <?php  echo strtoupper( SIMReg::get( "table" ) ); ?>" name="submit2" ></th>
				</tr>
				<tr>
					<th align="center" valign="middle" width="64">Editar</th>
					<th>
					<a href="<? echo "?mod=$mod&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit; ?>">Nombre<? if($_GET['order_by']=="Nombre"){?><img src="images/<?=$img?>" border=0><?}?></a>					</th>
					<th>
					<a href="<? echo "?mod=$mod&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit; ?>">Publicar<? if($_GET['order_by']=="Publicar"){?><img src="images/<?=$img?>" border=0><?}?></a>					</th>
					<th align="center" valign="middle" width="64">Eliminar</th>
				</tr>

<?php
	 $dbo = &SIMDB::get();
			    while( $r = $dbo->object( $result["result"] ) ){
				?>
				<tr class="<? SIMUtil::repetition()? 'row0' : 'row1'  ?>">
					<td align="center" width="64">
						<a href='<? echo "?mod=$mod&action=edit&id="; echo $r->$key; ?>'><img src='images/edit.png' border='0'></a>					</td>
					<td><?php echo $r->Nombre;?></td>
    				<td><?php echo $r->Publicar?></td>
					<td align="center" width="64">
						<a href='<? echo "?mod=$mod&action=del&id="; echo $r->$key; ?>'><img src='images/trash.png' border='0'></a>					</td>
				</tr>
<?php
	}//end while
?>
				<tr>
					<th class="texto" colspan="6" width="64"><?= $result["pages"] ?></th>
				</tr>
			</table>
	  </td>
	</tr>
</table>	

<?php		
}
else
	SIMNotify::capture("No se ha encontrado registros","error");
		// imprimir el html de errores
		SIMNotify::each();
}// Enf function list()				

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
function filtrar()
{
	global $dblink , $total_records , $row , $numtoshow , $mod;
?>			
<form name="frm" action='' method="get" class="formvalida">
<table width="100%" align="center" class="adminlist">
        	<tr>
	   		<th align="center" class="title">BUSCAR</th>
	  	</tr>
		<tr>
			<td align="center">
				Filtrar
					<select name="Buscar_por" id="Buscar_por" class="input mandatory" title="Buscar_por">
						<option value=''>Buscar por...</option>
						<option value="IDGaleria">IDGaleria</option>
						<option value="Nombre">Nombre</option>
						<option value="Publicar">Publicar</option>
	    </select>
					<input type="text" size="16" name="QryString" title="Buscar" class="input mandatory"> 
					
					<select name="Ordenar_por" id="Ordenar_por" class="input" title="Ordenar_por">
						<option value='IDGaleria'>Ordenar por </option>
						<option value="IDGaleria">IDGaleria</option>
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
				<input type="hidden" name="mod" value="<?=$mod?>">
				<input type="hidden" name="action" value="list">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
</table>
</form>
<?		
	}//End function filtrar
?>