 <?

SIMReg::setFromStructure( array(
					"title" => "Cliente",
					"table" => "Cliente",
					"key" => "IDCliente",
					"mod" => "Cliente"
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
			
				$files =  SIMFile::upload( $_FILES["Foto"] , CLIENTE_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto"] = $files[0]["innername"];				
	
				
				$files =  SIMFile::upload( $_FILES["FotoDiseno1"] , CLIENTE_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["FotoDiseno1"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["FotoDiseno1"] = $files[0]["innername"];				
				
				
				
			}//end if				
					
					//insertamos los datos
					$id = $dbo->insert( $frm , $table , $key );
					
					//Actualizo Servicios
					foreach($frm[ServicioCliente] as $id_servicio):
						$sql_interta_servicio=$dbo->query("Insert into ServicioCliente (IDCliente, IDServicio) Values ('".$id."', '".$id_servicio."')");
					endforeach;
					
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
						
					
						$files =  SIMFile::upload( $_FILES["Foto"] , CLIENTE_DIR , "IMAGE" );
						if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
							SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
						
						$frm["Foto"] = $files[0]["innername"];				
						
						
						$files =  SIMFile::upload( $_FILES["FotoDiseno1"] , CLIENTE_DIR , "IMAGE" );
						if( empty( $files ) && !empty( $_FILES["FotoDiseno1"]["name"] ) )
							SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
						$frm["FotoDiseno1"] = $files[0]["innername"];				
						
						$files =  SIMFile::upload( $_FILES["FotoLogoApp"] , CLIENTE_DIR , "IMAGE" );
						if( empty( $files ) && !empty( $_FILES["FotoLogoApp"]["name"] ) )
							SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
						$frm["FotoLogoApp"] = $files[0]["innername"];				
						
						
						
						
						
					}//end if								
					
					$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );
					
					
					//Actualizo Servicios
					$sql_borra_servicios = $dbo->query("Delete From ServicioCliente Where IDCliente  = '".SIMNet::reqInt("id")."'");
					foreach($frm[ServicioCliente] as $id_servicio):
						$sql_interta_servicio=$dbo->query("Insert into ServicioCliente (IDCliente, IDServicio) Values ('".SIMNet::reqInt("id")."', '".$id_servicio."')");
					endforeach;
					
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
			
			
			case "InsertarSocio":
                $frm = SIMUtil::varsLOG( $_POST );
				$frm[IDPais] = $frm[IDPaisSocio];
				$frm[IDDepartamento] = $frm[IDDepartamentoSocio];
				$frm[IDCiudad] = $frm[IDCiudadSocio];
				$frm[Clave] = sha1($frm[Clave]);
				
				$comprobar_correo =$dbo->fetchAll("Socio","(Email = '".$frm[Email]."' or NumeroDocumento = '".$frm[NumeroDocumento]."') and IDCliente = '".$frm[IDCliente]."' ","array");
				if (!empty($comprobar_correo[IDSocio])):
					SIMHTML::jsAlert("Error: Ya existe  el email o el documento en este club, por favor verifique");
	                SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] ."#Socio" );
					exit;
				endif;
				
				//UPLOAD de imagenes
				if(isset($_FILES)){
					$files =  SIMFile::upload( $_FILES["Foto"] , SOCIO_DIR , "IMAGE" );
					if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
						SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
					
					$frm["Foto"] = $files[0]["innername"];
						
				}//end if	
				
						
                $id = $dbo->insert( $frm , "Socio" , "IDSocio" );
				
				//Generar Codigo de barras
				$parametros_codigo_barras = $id."-".$frm[Nombre]."-".$frm[NumeroDocumento];
				$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras($parametros_codigo_barras,$id);
				//actualizo codigo barras
				$update_codigo=$dbo->query("update Socio set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$id."'");
				
                SIMHTML::jsAlert("Registro Exitoso");
                SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] ."#Socio" );
                exit;
			break;
	
			case "ModificaSocio":
                $frm = SIMUtil::varsLOG( $_POST );
				$frm[IDPais] = $frm[IDPaisSocio];
				$frm[IDDepartamento] = $frm[IDDepartamentoSocio];
				$frm[IDCiudad] = $frm[IDCiudadSocio];
				
				if($frm[Clave]!=$frm[ClaveAnt])
					$frm[Clave] = sha1($frm[Clave]);
					
				//Compruebo que no exista el correo				
				if($frm[Email]!=$frm[EmailAnt]):
					$comprobar_correo =$dbo->fetchAll("Socio","(Email = '".$frm[Email]."') and IDCliente = '".$frm[IDCliente]."' ","array");
					if (!empty($comprobar_correo[IDSocio])):
						SIMHTML::jsAlert("Error: Ya existe  el email en este club, por favor verifique");
						SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] ."#Socio" );
						exit;
					endif;	
				endif;	
				
				//Compruebo que no exista el documento
				if($frm[NumeroDocumento]!=$frm[NumeroDocumentoAnt]):
					$comprobar_correo =$dbo->fetchAll("Socio","(NumeroDocumento = '".$frm[NumeroDocumento]."') and IDCliente = '".$frm[IDCliente]."' ","array");
					if (!empty($comprobar_correo[IDSocio])):
						SIMHTML::jsAlert("Error: Ya existe  el numero de documento en este club, por favor verifique");
						SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] ."#Socio" );
						exit;
					endif;	
				endif;	
				
				
				//UPLOAD de imagenes
				if(isset($_FILES)){
					$files =  SIMFile::upload( $_FILES["Foto"] , SOCIO_DIR , "IMAGE" );
					if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
						SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
					
					$frm["Foto"] = $files[0]["innername"];
						
				}//end if	
				
				
				//Generar Codigo de barras
				$parametros_codigo_barras = $frm[IDCliente]."-".$frm[Nombre]."-".$frm[NumeroDocumento];
				$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras($parametros_codigo_barras,$frm[IDCliente]);
				
						
				$dbo->update( $frm , "Socio" , "IDSocio" , $frm[IDSocio] );
                SIMHTML::jsAlert("Modificacion Exitoso");
    			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$frm[ID] ."#Socio" );
                exit;
	        break;	
		
			case "EliminaSocio":
				$id = $dbo->query( "DELETE FROM  Socio WHERE IDSocio   = '".$_GET[IDSocio ]."' LIMIT 1" );
				SIMHTML::jsAlert("Eliminacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."#Socio" );
				exit;
			break;	
			
			case "delfotoSocio":
				$foto = $_GET['foto'];
				$campo = $_GET['campo'];
				$id = $_GET['IDSocio'];
				$filedelete = DISENO_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE Socio SET $campo = '' WHERE IDSocio = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id."" );
			break;		
			
			
			case "InsertarBannerApp":
                $frm = SIMUtil::varsLOG( $_POST );
				
				//UPLOAD de imagenes
				if(isset($_FILES)){
					$files =  SIMFile::upload( $_FILES["Foto1"] , BANNERAPP_DIR , "IMAGE" );
					if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
						SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
					
					$frm["Foto1"] = $files[0]["innername"];
						
				}//end if	
				
						
                $id = $dbo->insert( $frm , "BannerApp" , "IDBannerApp" );
				
                SIMHTML::jsAlert("Registro Exitoso");
                SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] ."#BannerApp" );
                exit;
			break;
	
			case "ModificaBannerApp":
                $frm = SIMUtil::varsLOG( $_POST );
				
				//UPLOAD de imagenes
				if(isset($_FILES)){
					$files =  SIMFile::upload( $_FILES["Foto1"] , BANNERAPP_DIR , "IMAGE" );
					if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
						SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
					
					$frm["Foto1"] = $files[0]["innername"];
						
				}//end if	
						
				$dbo->update( $frm , "BannerApp" , "IDBannerApp" , $frm[IDBannerApp] );
                SIMHTML::jsAlert("Modificacion Exitoso");
    			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$frm[ID] ."#BannerApp" );
                exit;
	        break;	
		
			case "EliminaBannerApp":
				$id = $dbo->query( "DELETE FROM  BannerApp WHERE IDBannerApp   = '".$_GET[IDBannerApp]."' LIMIT 1" );
				SIMHTML::jsAlert("Eliminacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."#BannerApp" );
				exit;
			break;	
			
			
			
			case "delfotoBannerApp":
				$foto = $_GET['foto'];
				$campo = $_GET['campo'];
				$id = $_GET['IDBannerApp'];
				$filedelete = BANNERAPP_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE BannerApp SET $campo = '' WHERE IDBannerApp = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."#BannerApp" );
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
	
	// Servicios
		$qry_servicios = $dbo->all( "Servicio", " IDServicio > 0  ORDER BY Nombre ASC " );
		while( $r_servicio = $dbo->fetchArray( $qry_servicios ) )
			$array_servicio[ $r_servicio["IDServicio"] ] = $r_servicio;
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
	include( "includes/tabs.html" );
	
	?>


<div id="tabsform">

	<ul>
       <li><a href="#Cliente"><span>Cliente</span></a></li>
       <li><a href="#BannerApp"><span>Banner App</span></a></li>
       <li><a href="#Socio"><span>Socios</span></a></li>
			<?php
			 if(!empty($_GET[id])):
                    $sql_servicio_cliente = $dbo->query("Select S.* 
														 From ServicioCliente SC, Servicio S 
														 Where S.iDServicio = SC.IDServicio and IDCliente = '".$_GET[id]."'");
                    while ($r_servicio_cliente = $dbo->fetchArray($sql_servicio_cliente)): 
						$array_tab_servicios [$r_servicio_cliente["Nombre"]] =  $r_servicio_cliente[Script]; 
					?>
                    	
                    <li><a href="#<?php echo $r_servicio_cliente["Nombre"]; ?>"><span><?php echo $r_servicio_cliente["Nombre"]; ?></span></a></li>
                    <?php
                    endwhile;
			   endif;
            ?>		
    </ul>

	<div id="Cliente">
		<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" method="post" enctype="multipart/form-data" class="formvalida">
		<table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">	

			
			          <tr>
			            <td  class="columnafija" >Pais</td>
			            <td><?php echo SIMHTML::formPopUp( "Pais" , "Nombre" , "Nombre" , "IDPais" , $frm["IDPais"] , "[Seleccione el Pais]" , "popup mandatory" , "title = \"Pais\"" )?></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Departamento</td>
			            <td>
                        	<select name="IDDepartamento" id="IDDepartamento" class="popup" required>
                            	<?php if(!empty($frm["IDDepartamento"])):?>
                                  <option value="<?php echo $frm["IDDepartamento"] ?>" selected><?php echo $dbo->getFields( "Departamento" , "Nombre" , "IDDepartamento = '".$frm["IDDepartamento"]."'"); ?></option>
                                <?php endif; ?>
                            	
                            </select>
                        
                          </td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Ciudad</td>
			            <td>
                        	<select name="IDCiudad" id="IDCiudad" class="popup" required>
                            <?php if(!empty($frm["IDCiudad"])):?>
                                  <option value="<?php echo $frm["IDCiudad"] ?>" selected><?php echo $dbo->getFields( "Ciudad" , "Nombre" , "IDCiudad = '".$frm["IDCiudad"]."'"); ?></option>
                                <?php endif; ?>
                            </select>
                        </td>
		              </tr>
			          <tr>
			<td  class="columnafija" > Nombre </td><td><input id=Nombre type=text size=25  name=Nombre class="input mandatory " title="Nombre" value="<?=$frm[Nombre] ?>"> </td>
			</tr>
			          <tr>
			            <td  class="columnafija" >Direccion</td>
			            <td><input id=Direccion type=text size=25  name=Direccion class="input mandatory " title="Direccion" value="<?=$frm[Direccion] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Email</td>
			            <td><input id=Email type=email size=25  name=Email class="input mandatory " title="Email" value="<?=$frm[Email] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Telefono</td>
			            <td><input id=Telefono type=text size=25  name=Telefono class="input mandatory " title="Telefono" value="<?=$frm[Telefono] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Dise&ntilde;o</td>
			            <td><?php echo SIMHTML::formPopUp( "Diseno" , "Nombre" , "Nombre" , "IDDiseno" , $frm["IDDiseno"] , "[Seleccione el Dise&ntilde;o]" , "popup mandatory" , "title = \"Dise&ntilde;o\"" )?></td>
		              </tr>
			<tr>
			  <td  class="columnafija" > Clase Adicional</td>
			  <td><input id=ClaseAdicional type=text size=25  name=ClaseAdicional class="input" title="ClaseAdicional" value="<?=$frm[ClaseAdicional] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija"><? if (!empty($frm[Foto])) {
					echo "<img src='".CLIENTE_ROOT."$frm[Foto]' width=55 >";
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
			  <td class="columnafija"><? if (!empty($frm[FotoDiseno1])) {
					echo "<img src='".CLIENTE_ROOT."$frm[FotoDiseno1]' width=55 >";
					?>
			    <a
					href="<? echo "?mod=$mod&action=delfoto&foto=$frm[FotoDiseno1]&campo=FotoDiseno1&id=".$frm[$key]; ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    Foto Dise&ntilde;o</td>
			  <td><input name="FotoDiseno1" id=file class=""
					title="Foto" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
			<tr>
			  <td class="columnafija"><? if (!empty($frm[FotoLogoApp])) {
					echo "<img src='".CLIENTE_ROOT."$frm[FotoLogoApp]' width=55 >";
					?>
			    <a
					href="<? echo "?mod=$mod&action=delfoto&foto=$frm[FotoLogoApp]&campo=FotoLogoApp&id=".$frm[$key]; ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    Logo Club App</td>
			  <td><input name="FotoLogoApp" id=FotoLogoApp class=""
					title="FotoLogoApp" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
			<tr>
			  <th colspan="2" class="columnafija">Servicios</th>
			  </tr>
			<tr>
			  <td colspan="2" class="columnafija">
              
		              <?php
					  		$sql_servicio_cliente = $dbo->query("Select * From ServicioCliente Where IDCliente = '".$frm[$key]."'");
							while ($r_servicio_cliente = $dbo->fetchArray($sql_servicio_cliente)):
								$array_servicio_cliente[] = $r_servicio_cliente["IDServicio"];
							endwhile;
					?>		
							
              
							<table border="0" width="100%">	
                           <tr>                           	
							   <?php 
							   $contador=0;
                               foreach( $array_servicio as $iddato => $dato ): 
                               	$contador++;
                               ?>
                               <td>
	                           	<input type="checkbox" name="ServicioCliente[]" value="<?php echo $dato[IDServicio]; ?>" <?php if (in_array($dato[IDServicio],$array_servicio_cliente)){ ?> checked="checked" <?php   } ?> /><?php echo $dato[Nombre]; ?>
                               </td>
								   <?php if ($contador==3): 								   
                                   echo "</tr>";
                                   echo "<tr>";
								   $contador=0;
							   endif;
							   ?>
                           <?php endforeach; ?>
                          </tr>  
                          </table>              
              
              </td>
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


	<?
    include( "tabs/cliente/socio.php" );
	include( "tabs/cliente/bannerapp.php" );
	foreach($array_tab_servicios as $nombre_modulo => $nombre_archivo):
		include( "tabs/cliente/".$nombre_archivo );
	endforeach;
	
    //include( "tabs/cliente/servicio.php" );
	?>



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
					<th class=title colspan=8   ><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?></th>
					
				</tr>


<tr>
					<th class=texto colspan=8  ><?php echo $result["info"]?></th>
					
				</tr>
<tr>
<th align=center valign=middle width=64>Editar</th>
				<th>
					Nombre&nbsp;
				</th>
				<th>
					Email&nbsp;
				</th>
				<th>Dise&ntilde;o</th>
					
<th align=center valign=middle width=64>Eliminar</th>
</tr>

<? 
$dbo =&SIMDB::get();
while( $r = $dbo->object( $result["result"] ) )
{
	
?>
  	
<tr class=<? echo SIMUtil::repetition()?'row0':'row1';?>>
<td align=center width=64 ><a href='<?php echo "?mod=" . $mod . "&amp;action=edit&amp;id=" . $r->$key?>'><img src='images/edit.png' border='0'></a></td>
<td nowrap><? echo $r->Nombre ?></td> <td nowrap><? echo $r->Email ?></td>
<td nowrap><? echo $dbo->getFields( "Diseno" , "Nombre" , "IDDiseno = '".$r->IDDiseno."'"); ?></td> 
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

