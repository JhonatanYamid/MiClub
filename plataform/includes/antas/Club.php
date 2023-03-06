 <?

SIMReg::setFromStructure( array(
					"title" => "Club",
					"table" => "Club",
					"key" => "IDClub",
					"mod" => "Club"
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
			
				$files =  SIMFile::upload( $_FILES["Foto"] , CLUB_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto"] = $files[0]["innername"];				
	
				
				$files =  SIMFile::upload( $_FILES["FotoDiseno1"] , CLUB_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["FotoDiseno1"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["FotoDiseno1"] = $files[0]["innername"];				
				
				
				
			}//end if				
					
					//insertamos los datos
					$id = $dbo->insert( $frm , $table , $key );
					
					//Actualizo Servicios
					foreach($frm[ServicioClub] as $id_servicio):
						$sql_interta_servicio=$dbo->query("Insert into ServicioClub (IDClub, IDServicio) Values ('".$id."', '".$id_servicio."')");
					endforeach;
					
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id . "&m=insertarexito" );
				}
				else
					print_form( $_POST , "insert" , "Agregar Registro" );
			break;
			
			case "edit":
			
				$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );		
				$_SESSION[IDClub] = SIMNet::reqInt("id");
				print_form( $frm , "update" , "Realizar Cambios" );
				
			break ;
			
			case "update" :	
				if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					
						//UPLOAD de imagenes
						if(isset($_FILES)){
							$files =  SIMFile::upload( $_FILES["Foto"] , CLUB_DIR , "IMAGE" );
							if( empty( $files ) && !empty( $_FILES["Foto"]["name"] ) )
								SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
							
							$frm["Foto"] = $files[0]["innername"];				
							
							
							$files =  SIMFile::upload( $_FILES["FotoDiseno1"] , CLUB_DIR , "IMAGE" );
							if( empty( $files ) && !empty( $_FILES["FotoDiseno1"]["name"] ) )
								SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
							$frm["FotoDiseno1"] = $files[0]["innername"];				
							
							$files =  SIMFile::upload( $_FILES["FotoLogoApp"] , CLUB_DIR , "IMAGE" );
							if( empty( $files ) && !empty( $_FILES["FotoLogoApp"]["name"] ) )
								SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
							$frm["FotoLogoApp"] = $files[0]["innername"];				
							
						
						
						
						
					}//end if								
					
					$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );
					
					
					//Actualizo Servicios
					$sql_borra_servicios = $dbo->query("Delete From ServicioClub Where IDClub  = '".SIMNet::reqInt("id")."'");
					foreach($frm[ServicioClub] as $id_servicio):
						$sql_interta_servicio=$dbo->query("Insert into ServicioClub (IDClub, IDServicio) Values ('".SIMNet::reqInt("id")."', '".$id_servicio."')");
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
			
			
			
			case "InsertarFechaCierre":
				$frm = SIMUtil::varsLOG( $_POST );
			
				$id = $dbo->insert( $frm , "ClubFechaCierre" , "IDClubFechaCierre" );
				SIMHTML::jsAlert("Registro Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDClub] ."#FechaCierre" );
				exit;
			break;
			
			case "ModificaFechaCierre":
						$frm = SIMUtil::varsLOG( $_POST );
			
						$dbo->update( $frm , "ClubFechaCierre" , "IDClubFechaCierre" , $frm[IDClubFechaCierre] );
					   
						SIMHTML::jsAlert("Modificacion Exitoso");
						SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDClub] ."#FechaCierre" );
						exit;
			break;
			
			 case "EliminaFechaCierre":
							$id = $dbo->query( "DELETE FROM ClubFechaCierre WHERE IDClubFechaCierre   = '".$_GET[IDClubFechaCierre]."' LIMIT 1" );
							SIMHTML::jsAlert("Eliminacion Exitoso");
							SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "#FechaCierre" ); 
							exit;
			break;
			
			
			
			case "list" :
				$where_array = array();
				$fieldInt = array();
						
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


 <?php //include( "includes/menuclub.php" )?> 
 
<?
	//imprime el HTML de errores
	SIMNotify::each();
	include( "includes/tabs.html" );
	
	?>


<div id="tabsform">

	<ul>
       <li><a href="#Club"><span>Club</span></a></li>
       <li><a href="#FechaCierre"><span>Fechas Cierre</span></a></li>
    </ul>

	<div id="Club">
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
			  <td  class="columnafija" >Color 1</td>
			  <td><input name="Color1" type="color" value="<?php if (empty($frm["Color1"])) { echo "#FFFFFF"; } else{ echo $frm["Color1"]; }    ?>" /></td>
			  </tr>
			<tr>
			  <td  class="columnafija" >Color 2</td>
			  <td><input name="Color2" type="color" value="<?php if (empty($frm["Color2"])) { echo "#FFFFFF"; } else{ echo $frm["Color2"]; }    ?>" /></td>
			  </tr>
			<tr>
			  <td class="columnafija"><? if (!empty($frm[Foto])) {
					echo "<img src='".CLUB_ROOT."$frm[Foto]' width=55 >";
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
					echo "<img src='".CLUB_ROOT."$frm[FotoDiseno1]' width=55 >";
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
					echo "<img src='".CLUB_ROOT."$frm[FotoLogoApp]' width=55 >";
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
			  <td class="columnafija">Email Notificaciones</td>
			  <td><input id=EmailNotificaciones type=text size=25  name=EmailNotificaciones class="input" title="EmailNotificaciones" value="<?=$frm[EmailNotificaciones] ?>"></td>
			  </tr>
			<tr>
			  <th colspan="2" class="columnafija">Administrador General</th>
			  </tr>
			<tr>
			  <td class="columnafija">Nombre de Usuario Administrador General:</td>
			  <td><input id=NombreAdministrador type=text size=25  name=NombreAdministrador class="input" title="Nombre Administrador" value="<?=$frm[NombreAdministrador] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Cargo Dentro del Club:</td>
			  <td><input id=CargoAdministrador type=text size=25  name=CargoAdministrador class="input" title="Cargo Administrador" value="<?=$frm[CargoAdministrador] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Email</td>
			  <td><input id=EmailAdministrador type=text size=25  name=EmailAdministrador class="input" title="Email Administrador" value="<?=$frm[EmailAdministrador] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Teléfono:</td>
			  <td><input id=TelefonoAdministrador type=text size=25  name=TelefonoAdministrador class="input" title="TelefonoAdministrador" value="<?=$frm[TelefonoAdministrador] ?>"></td>
			  </tr>
            
			<tr>
			  <td colspan=2 align=center>
			    
			    <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			    <input type="button" onclick="location.href='?mod=Club'" class="submit" value="Regresar" name="submit">
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


<div id="FechaCierre">
	<?php include("tabs/club/fechacierre.php" ) ?>
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
    
 <?php //include( "includes/menuclub.php" )?>     
    
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
                    <input type="button" onclick="location.href='?mod=Club&action=add'" class="submit" value="Crear Nuevo <?php  echo strtoupper( SIMReg::get( "title" ) ); ?>" name="submit" >
                    
                    </th>
					
				</tr>


<tr>
					<th class=texto colspan=8  ><?php echo $result["info"]?></th>
					
				</tr>
<tr>
<th align=center valign=middle width=64>Ver</th>
				<th>
					Nombre&nbsp;
				</th>
				<th>
					Email&nbsp;
				</th>
				<th>Dise&ntilde;o</th>
					
<th align=center valign=middle width=64>Ver reservas</th>
</tr>

<? 
$dbo =&SIMDB::get();
while( $r = $dbo->object( $result["result"] ) )
{
	
?>
  	
<tr class=<? echo SIMUtil::repetition()?'row0':'row1';?>>
<td align=center width=64 ><a href='<?php echo "?mod=HomeClub&amp;action=edit&amp;id=" . $r->$key?>'>Ver</a></td>
<td nowrap><? echo $r->Nombre ?></td> <td nowrap><? echo $r->Email ?></td>
<td nowrap><? echo $dbo->getFields( "Diseno" , "Nombre" , "IDDiseno = '".$r->IDDiseno."'"); ?></td> 
<td align=center width=64 >Ver reservas</td></tr>
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
						<td width="100">Email</td>
						<td width="131"><input type="text" size="14" value="<?php echo SIMNet::get( "Email" )?>" name="Email" id="Email" class="input" /></td>
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

