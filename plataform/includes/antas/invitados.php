 <?

SIMReg::setFromStructure( array(
					"title" => $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$_SESSION[IDClub]."'")." :: " . "Socio",
					"table" => "Socio",
					"key" => "IDSocio",
					"mod" => "Socio"
) );


//para validar los campos del formulario
$array_valida = array(  
	 "Nombre" => "Nombre",  "IDClub" => "IDClub", "NumeroDocumento" => "NumeroDocumento"	, "Genero" => "Genero"
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
					
					$frm[IDPais] = $frm[IDPais];
					$frm[IDDepartamento] = $frm[IDDepartamento];
					$frm[IDCiudad] = $frm[IDCiudad];
					$frm[Clave] = sha1($frm[Clave]);
					
					$comprobar_correo =$dbo->fetchAll("Socio","(Email = '".$frm[Email]."' or NumeroDocumento = '".$frm[NumeroDocumento]."') and IDClub = '".$frm[IDClub]."' ","array");
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
							
					
					//insertamos los datos
					$id = $dbo->insert( $frm , $table , $key );
					
					
					//Actualizo Secciones Noticia
					foreach($frm[SocioSeccion] as $id_seccion):
						$sql_interta_seccion=$dbo->query("Insert into SocioSeccion (IDSocio, IDSeccion) Values ('".SIMNet::reqInt("id")."', '".$id_seccion."')");
					endforeach;
					
					//Actualizo Secciones Evento
					foreach($frm[SocioSeccionEvento] as $id_seccion_evento):
						$sql_interta_seccion=$dbo->query("Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('".SIMNet::reqInt("id")."', '".$id_seccion_evento."')");
					endforeach;
					
					//Actualizo Secciones Galeria
					foreach($frm[SocioSeccionGaleria] as $id_seccion_galeria):
						$sql_interta_seccion=$dbo->query("Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('".SIMNet::reqInt("id")."', '".$id_seccion_galeria."')");
					endforeach;
					
					//Generar Codigo de barras
					$parametros_codigo_barras = $id."-".$frm[Nombre]."-".$frm[NumeroDocumento];
					$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras($parametros_codigo_barras,$id);
					//actualizo codigo barras
					$update_codigo=$dbo->query("update Socio set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$id."'");
					
						
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
					
					$frm[IDPais] = $frm[IDPais];
					$frm[IDDepartamento] = $frm[IDDepartamento];
					$frm[IDCiudad] = $frm[IDCiudad];
					
					if($frm[Clave]!=$frm[ClaveAnt])
						$frm[Clave] = sha1($frm[Clave]);
						
					//Compruebo que no exista el correo				
					if($frm[Email]!=$frm[EmailAnt]):
						$comprobar_correo =$dbo->fetchAll("Socio","(Email = '".$frm[Email]."') and IDClub = '".$frm[IDClub]."' ","array");
						if (!empty($comprobar_correo[IDSocio])):
							SIMHTML::jsAlert("Error: Ya existe  el email en este club, por favor verifique");
							SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID]);
							exit;
						endif;	
					endif;		
					
					//Compruebo que no exista el documento
					if($frm[NumeroDocumento]!=$frm[NumeroDocumentoAnt]):
						$comprobar_correo =$dbo->fetchAll("Socio","(NumeroDocumento = '".$frm[NumeroDocumento]."') and IDClub = '".$frm[IDClub]."' ","array");
						if (!empty($comprobar_correo[IDSocio])):
							SIMHTML::jsAlert("Error: Ya existe  el numero de documento en este club, por favor verifique");
							SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] );
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
					$parametros_codigo_barras = $frm[IDClub]."-".$frm[Nombre]."-".$frm[NumeroDocumento];
					$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras($parametros_codigo_barras,$frm[IDClub]);
					
					$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );
					
					
					//Actualizo Secciones Noticia
					$sql_borra_seccion = $dbo->query("Delete From SocioSeccion Where IDSocio  = '".SIMNet::reqInt("id")."'");
					foreach($frm[SocioSeccion] as $id_seccion):
						$sql_interta_seccion=$dbo->query("Insert into SocioSeccion (IDSocio, IDSeccion) Values ('".SIMNet::reqInt("id")."', '".$id_seccion."')");
					endforeach;
					
					//Actualizo Secciones Evento
					$sql_borra_seccion_evento = $dbo->query("Delete From SocioSeccionEvento Where IDSocio  = '".SIMNet::reqInt("id")."'");
					foreach($frm[SocioSeccionEvento] as $id_seccion_evento):
						$sql_interta_seccion=$dbo->query("Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('".SIMNet::reqInt("id")."', '".$id_seccion_evento."')");
					endforeach;
					
					//Actualizo Secciones Galeria
					$sql_borra_seccion_galeria = $dbo->query("Delete From SocioSeccionGaleria Where IDSocio  = '".SIMNet::reqInt("id")."'");
					foreach($frm[SocioSeccionGaleria] as $id_seccion_galeria):
						$sql_interta_seccion=$dbo->query("Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('".SIMNet::reqInt("id")."', '".$id_seccion_galeria."')");
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
				$filedelete = SOCIO_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id."" );
			break;
			
			
			case "list" :
				$where_array = array();
				
				if(!empty($_SESSION[IDClub])):
					$_GET[IDClub] = $_SESSION[IDClub];
				endif;
				
				$fieldInt = array("IDClub");
						
				$fieldStr = array ( "Nombre", "Apellido", "Accion" , "AccionPadre", "Email" );		 	
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
	
		// Secciones
		$qry_secciones_club = $dbo->all( "Seccion", " IDClub =  '".$_SESSION[IDClub]."'  ORDER BY Nombre ASC " );
		while( $r_seccion_club = $dbo->fetchArray( $qry_secciones_club ) )
			$array_seccion_club[ $r_seccion_club["IDSeccion"] ] = $r_seccion_club;
			
		// Secciones Evento
		$qry_secciones_club_evento = $dbo->all( "SeccionEvento", " IDClub =  '".$_SESSION[IDClub]."'  ORDER BY Nombre ASC " );
		while( $r_seccion_club_evento = $dbo->fetchArray( $qry_secciones_club_evento ) )
			$array_seccion_club_evento[ $r_seccion_club_evento["IDSeccionEvento"] ] = $r_seccion_club_evento;
			
		// Secciones Galeria
		$qry_secciones_club_galeria = $dbo->all( "SeccionGaleria", " IDClub =  '".$_SESSION[IDClub]."'  ORDER BY Nombre ASC " );
		while( $r_seccion_club_galeria = $dbo->fetchArray( $qry_secciones_club_galeria ) )
			$array_seccion_club_galeria[ $r_seccion_club_galeria["IDSeccionGaleria"] ] = $r_seccion_club_galeria;	
			

?>


<table class=adminheading>
		<tr>
			<th> 
			<?php echo SIMReg::get( "title" )?> </th>
			
			
		</tr>
</table>

<?php include( "includes/menuclub.php" )?> 

<?php
    //imprime el HTML de errores
    SIMNotify::each();
    include( "includes/tabs.html" );
	
        
        if ($newmode != "update") {			
            echo "  <script>
			  $(function() {
				
				$( '#tabsform' ).tabs( { disabled: [1, 2] } );
			  });
			  </script>
			";
        }
        ?>	
<?
//imprime el HTML de errores
SIMNotify::each();
include( "includes/tabs.html" );
?>





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
					<th class=title colspan=9   ><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?>
				    </th>
					
				</tr>


<tr>
					<th class=texto colspan=9  ><?php echo $result["info"]?></th>
					
				</tr>
<tr>
				<th>
					Socio&nbsp;
				</th>
				<th>Número de documento invitado</th>
				<th>
					Nombre de invitado&nbsp;
				</th>
				<th>Fecha de ingreso</th>
					
<th>Registrar ingreso</th>
</tr>

<? 
$dbo =&SIMDB::get();
while( $r = $dbo->object( $result["result"] ) )
{
	
?>
  	
<tr class=<? echo SIMUtil::repetition()?'row0':'row1';?>>
<td nowrap><? echo $r->Nombre ?></td>
<td nowrap><? echo $r->Accion ?></td> 
<td nowrap><? echo $r->Email ?></td>
<td nowrap><?php 
							  if ($r->AccionPadre=="")
							  	echo "Socio";
							  else
							  	echo "Beneficiario";	
							  
							  ?></td> 
<td ></td></tr>
<? } // END for
?>
<tr>
					<th class=texto colspan=6 nowrap  ><?php echo $result["pages"]?></th>
					
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
						<td width="100">Número de documento socio</td>
						<td width="131"><input type="text" size="14" value="" name="" id="" class="input" /></td>
						<td width="100">Número de documento invitado</td>
						<td width="131"><input type="text" size="14" value="" name="" id="" class="input" /></td>
						
					</tr>
					
					<tr>
						<td width="100"></td>	
						<td width="131"></td>
						<td width="100"></td>
						<td width="131"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="buscar" class="submit" value="Buscar"></td>						
						<td colspan="2" align="center"><input type="reset" name="submit" class="submit" value="Limpiar Campos"></td>
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

