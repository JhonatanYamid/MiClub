<?php
//Encapsulando datos globales
SIMReg::setFromStructure( array(
					"title" => "Usuarios del Sistema",
					"table" => "Usuario",
					"key" => "IDUsuario",
					"mod" => "Usuario"
) );

//permisos
//SIMUtil::verify( 0 , SIMUser::get( "Nivel" ) );


//Para validar los campos del formulario
$array_valida = array(
	"Nombre" => "Nombre",
	"Telefono" => "Tel&eacute;fono",
	"User" => "Nombre de Usuario",
	"Email" => "Email",
	"Autorizado" => "Autorizado"

);

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
		$_POST["param"]["usuario"]["IDClub"] = SIMNet::post( "IDClub" ); 
		if( !SIMNotify::capture( SIMUtil::valida( $_POST["param"]["usuario"] , $array_valida ) , "error" ) )
		{			
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST["param"]["usuario"] );
			
			//Validamos que el usuario no exista
			$SqlUsuario = "SELECT * FROM Usuario WHERE User = '" . $frm["User"]  ."'";
			$QryUsuario = $dbo -> query( $SqlUsuario );
			$NumUsuario = $dbo -> rows( $QryUsuario );
			if( $NumUsuario > 0 )
			{
				SIMNotify::capture( "El Usuario <strong>" . $frm["User"] . "</strong> ya Existe" , "error" );
				$_POST["param"]["usuario"]["User"] = "";
				print_form( $_POST["param"]["usuario"] , "insert" , "Agregar Registro" );
				exit;
			}
			
			if( $frm["Password"] <> $frm["RePassword"] )
			{
				SIMNotify::capture( "La contrase&ntilde;a y su confirmaci&oacute;n deben ser iguales" , "error" );
				print_form( $_POST["param"]["usuario"] , "insert" , "Agregar Registro" );
				exit;
			}
				
			//insertamos los datos
			$id = SIMUser::insert( $frm , $table , $key , "Password" );
			
			if(count($_POST["PerfilUsuario"])>0){	
				$borro_perfil_usuarios=$dbo->query("Delete from  UsuarioPerfil Where IDUsuario = '".$id."'");	
				foreach($_POST["PerfilUsuario"] as $valor_perfil){
					$inserta_perfil=$dbo->query("Insert into UsuarioPerfil (IDUsuario, IDPerfil) Values ('".$id."','".$valor_perfil."')");					
				}
			}
			
			if(count($_POST["UsuarioServicio"])>0){	
				foreach($_POST["UsuarioServicio"] as $valor_perfil){
					$inserta_perfil=$dbo->query("Insert into UsuarioServicio (IDUsuario, IDServicio) Values ('".$id."','".$valor_perfil."')");					
				}
			}
			
			
			if(count($_POST["UsuarioServicioElemento"])>0){	
				foreach($_POST["UsuarioServicioElemento"] as $valor_perfil){
					$inserta_perfil=$dbo->query("Insert into UsuarioServicioElemento (IDUsuario, IDServicioElemento) Values ('".$id."','".$valor_perfil."')");					
				}
			}
			
			SIMUser::update( $frm , $table , $key , $id , "Password" , array( "Password" ) );
			
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id . "&m=insertarexito" );
		}
		else
			print_form( $_POST["param"]["usuario"] , "insert" , "Agregar Registro" );
	break;
	
	case "edit":
	
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );		
		print_form( $frm , "update" , "Realizar Cambios" );
		
	break ;
	
	case "update" :	
		 
		$_POST["param"]["usuario"]["IDClub"] = SIMNet::post( "IDClub" ); 
		if( !SIMNotify::capture( SIMUtil::valida( $_POST["param"]["usuario"] , $array_valida ) , "error" ) )
		{			
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST["param"]["usuario"] );
			
			//Validamos que el usuario no exista
			$SqlUsuario = "SELECT * FROM Usuario WHERE User = '" . $frm["User"]  ."' AND IDUsuario <> '" . $_GET["id"] ."'";
			$QryUsuario = $dbo -> query( $SqlUsuario );
			$NumUsuario = $dbo -> rows( $QryUsuario );
			if( $NumUsuario > 0 )
			{
				SIMNotify::capture( "El Usuario <strong>" . $frm["User"] . "</strong> ya Existe" , "error" );
				$_POST["param"]["usuario"]["User"] = "";
				print_form( $_POST["param"]["usuario"] , "insert" , "Agregar Registro" );
				exit;
			}
			
			if( $frm["Password"] <> $frm["RePassword"] )
			{
				SIMNotify::capture( "La contrase&ntilde;a y su confirmaci&oacute;n deben ser iguales" , "error" );
				print_form( $_POST["param"]["usuario"] , "update" , "Agregar Registro" );
				exit;
			}
			
			
			if(count($_POST["PerfilUsuario"])>0){	
				$borro_perfil_usuarios=$dbo->query("Delete from  UsuarioPerfil Where IDUsuario = '".$_GET["id"]."'");	
				foreach($_POST["PerfilUsuario"] as $valor_perfil){
					$inserta_perfil=$dbo->query("Insert into UsuarioPerfil (IDUsuario, IDPerfil) Values ('".$_GET["id"]."','".$valor_perfil."')");					
				}
			}
			
			if(count($_POST["UsuarioServicio"])>0){	
				$borro_perfil_usuarios=$dbo->query("Delete from  UsuarioServicio Where IDUsuario = '".$_GET["id"]."'");	
				foreach($_POST["UsuarioServicio"] as $valor_perfil){
					$inserta_perfil=$dbo->query("Insert into UsuarioServicio (IDUsuario, IDServicio) Values ('".$_GET["id"]."','".$valor_perfil."')");					
				}
			}
			
			if(count($_POST["UsuarioServicioElemento"])>0){	
				$borro_perfil_usuarios=$dbo->query("Delete from  UsuarioServicioElemento Where IDUsuario = '".$_GET["id"]."'");	
				foreach($_POST["UsuarioServicioElemento"] as $valor_perfil){
					$inserta_perfil=$dbo->query("Insert into UsuarioServicioElemento (IDUsuario, IDServicioElemento) Values ('".$_GET["id"]."','".$valor_perfil."')");					
				}
			}
			
			
			
			$id = SIMUser::update( $frm , $table , $key , SIMNet::reqInt("id") , "Password" , array( "Password" ) );
			
			$frm = $dbo->fetchById( $table , $key , $id , "array" );
			
			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
			
			print_form( $frm , "update" ,  "Realizar Cambios" );
		}
		else
			print_form( $_POST["param"]["usuario"] , "update" ,  "Realizar Cambios" );
		
	break;
	
	case "del":
		
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
		
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
		$fieldInt = array();
				
		$fieldStr = array ( "Nombre", "User" , "Email" );		 	
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
<table class="adminheading">
	<tr>
		<th><?php echo SIMReg::get( "title" )?></th>
	</tr>
</table>
<?php
//imprime el HTML de errores
SIMNotify::each();

?>
<div id="tabsform">
	<div id="usuario">
		<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" method="post" enctype="multipart/form-data" class="formvalida">
		<table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
						  <td>Club</td>
						  <td><?php echo SIMHTML::formPopUp( "Club" , "Nombre" , "Nombre" , "IDClub" , $frm["IDClub"] , "[Seleccione el Club]" , "popup mandatory" , "title = \"Club\"" )?></td>
					  </tr>
						<tr>
							<td> Nombre </td>
							<td><input id="param[usuario][Nombre]" type="text" size="40" title="Nombre" name="param[usuario][Nombre]" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" /> </td>
						</tr>
						<tr>
							<td> Tel&eacute;fono </td>
							<td><input id="param[usuario][Telefono]" type="text" size="15" title="Tel&eacute;fono" name="param[usuario][Telefono]" class="input mandatory" value="<?php echo $frm["Telefono"] ?>" /> </td>
						</tr>
						<tr>
							<td> Email </td>
							<td><input id="param[usuario][Email]" type="text" size="25" title="Email" name="param[usuario][Email]" class="input mandatory" value="<?php echo $frm["Email"] ?>" /> </td>
						</tr>
						<tr>
							<td> Nombre de Usuario </td>
							<td><input id="param[usuario][User]" type="text" size="20" title="Nombre de Usuario" name="param[usuario][User]" class="input mandatory" value="<?php echo $frm["User"] ?>" /> </td>
						</tr>
						<tr>
							<td> Contrase&ntilde;a </td>
							<td><input id="param[usuario][Password]" type="password" size="20" title="Contrase&ntilde;a" name="param[usuario][Password]" class="input" value="" /> </td>
						</tr>
						<tr>
							<td> Confirme la Contrase&ntilde;a </td>
							<td><input id="param[usuario][RePassword]" type="password" size="20" title="Repetir Contrase&ntilde;a" name="param[usuario][RePassword]" class="input" value="" /> </td>
						</tr>
						<tr>
							<td> Autorizado</td>
							<td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["Autorizado"] , "param[usuario][Autorizado]" , "title=\"Autorizado\"" )?> </td>
						</tr>
						<tr>
						  <td>Perfil</td>
						  <td><?php 
						  
						  // Consulto los perfiles del usuario
						  $sql_perfil=$dbo->query("select * from UsuarioPerfil where IDUsuario = '".$frm[IDUsuario]."'");
						  while($r_perfil=$dbo->object($sql_perfil)){
							  $perfil[]=$r_perfil->IDPerfil;
						  }
						  
						  
						  $arrayop = array();
						  // consulto las subsecciones
						  $query_perfiles=$dbo->query("Select * from Perfil Where IDPerfil >0  Order by Nombre");
						  while($r=$dbo->object($query_perfiles)){
								$arrayop[$r->Nombre]=$r->IDPerfil;  
						  }
						  
						  
						  echo SIMHTML::formCheckGroup( $arrayop , $perfil , "PerfilUsuario[]") ?>
			  
			 
			 </td>
					  </tr>
						<tr>
						  <td>Servicios</td>
						  <td><?php 
						  
						  // Consulto los servicios disponibles al usuario
						  $sql_servicio=$dbo->query("select * from UsuarioServicio where IDUsuario = '".$frm[IDUsuario]."'");
						  while($r_servicio=$dbo->object($sql_servicio)){
							  $servicio[]=$r_servicio->IDServicio;
						  }
						  
						  
						  $arrayop = array();
						  // consulto las subsecciones
						  $query_servicios=$dbo->query("Select * from Servicio Where Publicar = 'S' Order by Nombre");
						  while($r=$dbo->object($query_servicios)){
								$arrayservicio[$r->Nombre]=$r->IDServicio;  
						  }
						  
						  
						  echo SIMHTML::formCheckGroup( $arrayservicio , $servicio , "UsuarioServicio[]") ?></td>
					  </tr>
						<tr>
						  <td>Elemento</td>
						  <td><?php 
						  
						  // Consulto los servicios disponibles al usuario
						  $sql_elemento=$dbo->query("select * from UsuarioServicioElemento where IDUsuario = '".$frm[IDUsuario]."'");
						  while($r_elemento=$dbo->object($sql_elemento)){
							  $elemento[]=$r_elemento->IDServicioElemento;
						  }
						  
						  
						  $arrayop = array();
						  // consulto las subsecciones
						  $query_elemento=$dbo->query("Select * from ServicioElemento Where Publicar = 'S' Order by Nombre");
						  while($r=$dbo->object($query_elemento)){
								$arrayelemento[$r->Nombre]=$r->IDServicioElemento;  
						  }
						  
						  
						  echo SIMHTML::formCheckGroup( $arrayelemento , $elemento , "UsuarioServicioElemento[]") ?></td>
					  </tr>
						<tr>
							<td colspan="2" align="center">
								<input type="submit" name="submit" value="<?php echo $submit_caption ?>" class="submit" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
				
		<input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
		<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
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
	$mod = SIMReg::get( "table" );
	
	if( empty( $sql ) )
	 	$sql =  "SELECT " . $table . ".* , Pais.Nombre AS Pais FROM " . $table . " LEFT JOIN Pais ON " . $table . ".IDPais = Pais.IDPais ORDER BY " . $key;
	 	
 	$result =& SIMUtil::createPag( $sql , 50 );	
	
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
	<table width="100%" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
				<table class="adminlist">
					<tr>
						<th class="title" colspan="10"><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?>
                        <input type="button" onclick="location.href='?mod=Usuario&action=add'" class="submit" value="Crear Nuevo <?php  echo strtoupper( SIMReg::get( "table" ) ); ?>" name="submit2" >
                        </th>
					</tr>
					<tr>
						<th class="texto" colspan="10"><?php echo $result["info"]?></th>
					</tr>
					<tr>
						<th align="center" valign="middle" width="64">Editar</th>
						<th>Club</th>
						<th>Nombre</th>
						<th>Email</th>
						<th>Usuario</th>
						<th>Autorizado</th>
						<th align="center" valign="middle" width="64">Eliminar</th>
					</tr>
	
	<?php
		$dbo =&SIMDB::get();
		while( $r = $dbo->object( $result["result"] ) )
		{
	?>
					<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
						<td align="center" width="64">
							<a href="<?php echo "?mod=" . $mod . "&amp;action=edit&amp;id=" . $r->$key?>"><img src='images/edit.png' border='0'></a>
						</td>
						<td><?php echo $dbo->getFields( "Club" , "Nombre" , "IDClub = '" . $r->IDClub . "'" ) ?></td>
						<td><?php echo $r->Nombre?></td>
						<td><?php echo $r->Email?></td>
						<td><?php echo $r->User?></td>
						<td><?php echo SIMResources::$sino[ $r->Autorizado ]?></td>
						<td align="center" width="64">
							<a href="<? echo "?mod=" . $mod . "&amp;action=del&amp;id=" . $r->$key?>"><img src='images/trash.png' border='0'></a>
						</td>
					</tr>
	<?php 
		}
	?>
					<tr>
						<th class="texto" colspan="10" width="64"><?php echo $result["pages"]?></th>
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
						<td width="100">Nombre</td>
						<td width="131"><input type="text" size="14" value="<?php echo SIMNet::get( "Nombre" )?>" name="Nombre" id="Nombre" class="input" /></td>
						<td width="100">Email</td>
						<td width="131"><input type="text" size="14" value="<?php echo SIMNet::get( "Email" )?>" name="Email" id="Email" class="input" /></td>
						<td width="100">Usuario</td>
						<td width="131"><input type="text" size="14" value="<?php echo SIMNet::get( "User" )?>" name="User" id="User" class="input" /></td>
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