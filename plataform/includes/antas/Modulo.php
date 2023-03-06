<?php
//Encapsulando datos globales
SIMReg::setFromStructure( array(
					"title" => "Modulos de Seguridad",
					"table" => "Modulo",
					"key" => "IDModulo",
					"mod" => "Modulo"
) );


//permisos
SIMUtil::verify( 0 , SIMUser::get( "Nivel" ) );


//Para validar los campos del formulario
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
		
		if( !SIMNotify::capture( SIMUtil::valida( $_POST["param"]["Modulo"] , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST["param"]["Modulo"] );
			
			//insertamos los datos
			$id = $dbo->insert( $frm , $table , $key );
			
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id . "&m=insertarexito" );
		}
		else
			print_form( $_POST["param"]["Modulo"] , "insert" , "Agregar Registro" );
	break;
	
	case "edit":
	
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );		
		print_form( $frm , "update" , "Realizar Cambios" );
		
	break ;
	
	case "update" :	
		if( !SIMNotify::capture( SIMUtil::valida( $_POST["param"]["Modulo"] , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST["param"]["Modulo"] );
			
			$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );
			
			$frm = $dbo->fetchById( $table , $key , $id , "array" );
			
			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
			
			print_form( $frm , "update" ,  "Realizar Cambios" );
		}
		else
			print_form( $_POST["param"]["Modulo"] , "update" ,  "Realizar Cambios" );

		
	break;
	
	case "saveModulo" :	

			//los campos al final de las tablas
		    $dbo->deleteById( "CambioModulo" , "IDCambioModulo" , SIMNet::reqInt("IDCambioModulo") );
			
			$frm = SIMUtil::varsLOG( $_POST );			
			
			
			$id = $dbo->insert( $frm , "CambioModulo" , "IDCambioModulo" );
			
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDModulo] . "&m=insertarexito" );

		
	break;
	
	case "editcambioModulo" :	
	
		$idcambio = SIMNet::reqInt( "idcambio" );
		
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );		
		
		$frm["Cambio"] = $dbo->fetchById( "CambioModulo" , "IDCambioModulo" , $idcambio , "array" );
		print_form( $frm , "update" , "Realizar Cambios" );

		
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
		$fieldInt = array( "TRM" );			
		$fieldStr = array ( "Nombre" );		 	
			 		
		$fromjoin = $fieldInt;
			 	
		$wherejoin = $fieldInt;
			 					      	
		$params = SIMUtil::filter( $fieldInt , $fieldStr , $fromjoin , $where_array , $wherejoin );
				
		$sql = " SELECT V.* FROM " . $table . " V " . $params["from"] . $params["where"];
		
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
	<div id="Modulo">
		<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" method="post" enctype="multipart/form-data" class="formvalida">
		<table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">	
						<tr>
							<td> Nombre </td>
							<td><input id="param[Modulo][Nombre]" type="text" size="25" title="Nombre" name="param[Modulo][Nombre]" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" /> </td>
						</tr>
						<tr>
							<td> Nombre del M&oacute;dulo ( Direccion fisica en el programa ) </td>
							<td><input id="param[Modulo][NombreModulo]" type="text" size="25" title="Nombre" name="param[Modulo][NombreModulo]" class="input mandatory" value="<?php echo $frm["NombreModulo"] ?>" /> </td>
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
	<table width="100%" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
				<table class="adminlist">
					<tr>
						<th class="title" colspan="9"><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?></th>
					</tr>
					<tr>
						<th class="texto" colspan="9"><?php echo $result["info"]?></th>
					</tr>
					<tr>
						<th align="center" valign="middle" width="64">Editar</th>
						<th>Nombre</th>
						<th align="center" valign="middle" width="64">Eliminar</th>
					</tr>
	
	<?php
		$dbo =&SIMDB::get();
		while( $r = $dbo->object( $result["result"] ) )
		{
	?>
					<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
						<td align="center" width="64">
							<a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&amp;action=edit&amp;id=" . $r->$key?>"><img src='images/edit.png' border='0'></a>						</td>
						<td><?php echo $r->Nombre?></td>
						<td align="center" width="64">
							<a href="<? echo "?mod=" . SIMReg::get( "mod" ) . "&amp;action=del&amp;id=" . $r->$key?>"><img src='images/trash.png' border='0'></a>						</td>
					</tr>
	<?php 
		}
	?>
					<tr>
						<th class="texto" colspan="9" width="64"><?php echo $result["pages"]?></th>
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
						<td width="131"><input type="text" size="14" value="" name="Nombre" id="Nombre" class="input" /></td>
						<td width="100">&nbsp;</td>
					    <td width="131">&nbsp;</td>
				      <td width="100">&nbsp;</td>
						<td width="131">&nbsp;</td>
					</tr>
					<tr>
						<td width="100">&nbsp;</td>
						<td width="131">&nbsp;</td>
						<td>&nbsp;</td>
						<td><input type="submit" name="buscar" class="submit" value="Buscar"></td>
						<td></td>
						<td><input type="reset" name="submit" class="submit" value="Limpiar Campos"></td>
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