 <?

SIMReg::setFromStructure( array(
					"title" => "Servicio",
					"table" => "Servicio",
					"key" => "IDServicio",
					"mod" => "Servicio"
) );


//para validar los campos del formulario
$array_valida = array(  
	 "IntervaloHora" => "IntervaloHora" 	
); 


//extraemos las variables
$table = SIMReg::get( "table" );
$key = SIMReg::get( "key" );
$mod = SIMReg::get( "mod" );
$dbo =& SIMDB::get();


//verificar si el servicio ya tiene alguna configuracion
$idservicio_club = $dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro = '".$_GET[IDServicioMaestro]."' and IDClub = '".$_SESSION[IDClub]."' "); 
if(empty($idservicio_club) && empty($_GET["id"])):
	$_GET["action"]="add";
else:
	if(empty($_GET["action"]))
	$_GET["action"]="edit";
	if(empty($_GET["id"]))	
		$_GET["id"]=$idservicio_club;
endif;

	
//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );	

if(SIMNet::req( "action" )=="add")
{
?>
<script type="text/javascript">
	/* FUNCION PARA LOS TABS */
	$(function() {
         $('#ContenedorServicio').tabs(1,{ disabled: [2,3,4,5] });
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
         $('#ContenedorServicio').tabs(<?=$tab?>);
    });
</script>
<?
}//end else



		switch ( SIMNet::req( "action" ) ) {
			case "add" :
				print_form( "" , "insert" , "Agregar Registro" );
			break;
			
			case "insert" :	
				if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					
					
					
				if( empty( $_FILES["Icono"]["name"] ) ){
					$id = $dbo->insert( $frm , $table , $key );
				}
				else
				{
					$files =  SIMFile::upload( $_FILES["Icono"] , SERVICIO_DIR , "IMAGE" );
					if( empty( $files ) )
					{
						SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
						print_form( $frm , "insert" , "Agregar Registro" );
						exit;
					}
					
					$frm["Icono"] = $files[0]["innername"];
					$frm["IconoName"] = $files[0]["innername"];			
					$id = $dbo->insert( $frm , $table , $key );
				}			
					
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
					
					
					
			if( empty( $_FILES["Icono"]["name"] ) )
			{
				$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"),  array( "Icono" ) );
			}//end if
			else
			{
				$files =  SIMFile::upload( $_FILES["Icono"] , SERVICIO_DIR , "IMAGE" );
				if( empty( $files ) )
				{
					SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
					print_form( $frm , "insert" , "Agregar Registro" );
					exit;
				}
				
				$frm["Icono"] = $files[0]["innername"];
				$frm["IconoName"] = $files[0]["innername"];
				
				
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
				$filedelete = SERVICIO_DIR.$foto;
				unlink($filedelete);
				$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."" );
			break;
			
			case "list" :
				$where_array = array();
				if(!empty($_SESSION[IDClub])):
					$_GET[IDClub] = $_SESSION[IDClub];
				endif;
				
				$fieldInt = array("IDClub");
						
				$fieldStr = array ( "Nombre");		 	
				$listjoin = array();
				$fromjoin = array();
					 
				$wherejoin = array();
												
				$params = SIMUtil::filter( $fieldInt , $fieldStr , $fromjoin , $listjoin , $where_array , $wherejoin );
						
				$sql = " SELECT " . $params["fields"] . " FROM " . $table . " V " . $params["from"] . $params["where"];
				
				list_r( $sql );
			break;
			
	
	case "InsertarServicioDisponibilidad":
		$frm = SIMUtil::varsLOG( $_POST );
	
		foreach($frm["IDDia"] as $Dia_seleccion):
			$array_dia []= $Dia_seleccion;
		endforeach;
		
		if(count($array_dia)>0):
			$id_dia=implode("|",$array_dia) . "|";;
		endif;
		$frm["IDDia"]=$id_dia;
		
		//Elementos 
		foreach($frm["IDServicioElemento"] as $IDServicioElemnto):
			$array_servicio_elemento []= $IDServicioElemnto;
		endforeach;
		if(count($array_servicio_elemento)>0):
			$ID_Servicio_Elemento=implode("|",$array_servicio_elemento). "|";
		endif;
		$frm["IDServicioElemento"]=$ID_Servicio_Elemento;
	
		$id = $dbo->insert( $frm , "ServicioDisponibilidad" , "IDServicioDisponibilidad" );
		SIMHTML::jsAlert("Registro Exitoso");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioDisponibilidad" );
		exit;
	break;
	
	case "ModificaServicioDisponibilidad":
				$frm = SIMUtil::varsLOG( $_POST );
				
				foreach($frm["IDDia"] as $Dia_seleccion):
					$array_dia []= $Dia_seleccion;
				endforeach;
				
				if(count($array_dia)>0):
					$id_dia=implode("|",$array_dia) . "|";
				endif;
				$frm["IDDia"]=$id_dia;
				
				//Elementos 
				foreach($frm["IDServicioElemento"] as $IDServicioElemnto):
					$array_servicio_elemento []= $IDServicioElemnto;
				endforeach;
				if(count($array_servicio_elemento)>0):
					$ID_Servicio_Elemento=implode("|",$array_servicio_elemento). "|";
				endif;
				$frm["IDServicioElemento"]=$ID_Servicio_Elemento;
				
				
				$dbo->update( $frm , "ServicioDisponibilidad" , "IDServicioDisponibilidad" , $frm[IDServicioDisponibilidad] );
			   
				SIMHTML::jsAlert("Modificacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioDisponibilidad" );
				exit;
	break;
	
	 case "EliminaServicioDisponibilidad":
					$id = $dbo->query( "DELETE FROM ServicioDisponibilidad WHERE IDServicioDisponibilidad   = '".$_GET[IDServicioDisponibilidad]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "#ServicioDisponibilidad" ); 
					exit;
	break;
	
	case "InsertarDisponibilidadElemento":
		$frm = SIMUtil::varsLOG( $_POST );
		$id = $dbo->insert( $frm , "ElementoDisponibilidad" , "IDElementoDisponibilidad" );
		SIMHTML::jsAlert("Registro Exitoso");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioElemento" );
		exit;
	break;
	
	case "ModificaDisponibilidadElemento":
				$frm = SIMUtil::varsLOG( $_POST );
	
				$dbo->update( $frm , "ElementoDisponibilidad" , "IDElementoDisponibilidad" , $frm[IDElementoDisponibilidad] );
			   
				SIMHTML::jsAlert("Modificacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioElemento" );
				exit;
	break;
	
	 case "EliminaDisponibilidadElemento":
			$id = $dbo->query( "DELETE FROM ElementoDisponibilidad WHERE IDElementoDisponibilidad   = '".$_GET[IDElementoDisponibilidad]."' LIMIT 1" );
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "&IDServicioElemento=".$_GET[IDServicioElemento]."#ServicioElemento" ); 
			exit;
break;
	
			
			
	case "InsertarServicioCampo":
		$frm = SIMUtil::varsLOG( $_POST );
	
		$id = $dbo->insert( $frm , "ServicioCampo" , "IDServicioCampo" );
		SIMHTML::jsAlert("Registro Exitoso");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioCampos" );
		exit;
	break;
	
	case "ModificaServicioCampo":
				$frm = SIMUtil::varsLOG( $_POST );
	
				$dbo->update( $frm , "ServicioCampo" , "IDServicioCampo" , $frm[IDServicioCampo] );
			   
				SIMHTML::jsAlert("Modificacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioCampos" );
				exit;
	break;
	
	 case "EliminaServicioCampo":
					$id = $dbo->query( "DELETE FROM ServicioCampo WHERE IDServicioCampo   = '".$_GET[IDServicioCampo]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "#ServicioCampos" ); 
					exit;
	break;
	
		
	case "InsertarServicioCierre":
		$frm = SIMUtil::varsLOG( $_POST );
	
		$id = $dbo->insert( $frm , "ServicioCierre" , "IDServicioCierre" );
		SIMHTML::jsAlert("Registro Exitoso");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioCierre" );
		exit;
	break;
	
	case "ModificaServicioCierre":
				$frm = SIMUtil::varsLOG( $_POST );
	
				$dbo->update( $frm , "ServicioCierre" , "IDServicioCierre" , $frm[IDServicioCierre] );
			   
				SIMHTML::jsAlert("Modificacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioCierre" );
				exit;
	break;
	
	 case "EliminaServicioCierre":
					$id = $dbo->query( "DELETE FROM ServicioCierre WHERE IDServicioCierre   = '".$_GET[IDServicioCierre]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "#ServicioCierre" ); 
					exit;
	break;
	
			
			
	case "InsertarServicioElemento":
		$frm = SIMUtil::varsLOG( $_POST );
	
		$id = $dbo->insert( $frm , "ServicioElemento" , "IDServicioElemento" );
		$ultimo_id = $dbo->lastID();
		
		
		$frm["IDServicioElemento"]	= $ultimo_id;
		$id = $dbo->insert( $frm , "ElementoDisponibilidad" , "IDElementoDisponibilidad" );
		
		SIMHTML::jsAlert("Registro Exitoso");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioElemento" );
		exit;
	break;
	
	case "ModificaServicioElemento":
				$frm = SIMUtil::varsLOG( $_POST );
	
				$dbo->update( $frm , "ServicioElemento" , "IDServicioElemento" , $frm[IDServicioElemento] );
				
				
				$id = $dbo->insert( $frm , "ElementoDisponibilidad" , "IDElementoDisponibilidad" );
				
			   
				SIMHTML::jsAlert("Modificacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."&IDServicioElemento=".$frm[IDServicioElemento]."#ServicioElemento" );
				exit;
	break;
	
	 case "EliminaServicioElemento":
					$id = $dbo->query( "DELETE FROM ServicioElemento WHERE IDServicioElemento   = '".$_GET[IDServicioElemento]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "#ServicioElemento" ); 
					exit;
	break;
	
	case "InsertarServicioReserva":
		$frm = SIMUtil::varsLOG( $_POST );
	
		$id = $dbo->insert( $frm , "ReservaGeneral" , "IDReservaGeneral" );
		SIMHTML::jsAlert("Registro Exitoso");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioReserva" );
		exit;
	break;
	
	case "ModificaServicioReserva":
				$frm = SIMUtil::varsLOG( $_POST );
	
				$dbo->update( $frm , "ReservaGeneral" , "IDReservaGeneral" , $frm[IDReservaGeneral] );
			   
				SIMHTML::jsAlert("Modificacion Exitoso");
				SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[IDServicio] ."#ServicioReserva" );
				exit;
	break;
	
	 case "EliminaServicioReserva":
					$id = $dbo->query( "DELETE FROM ReservaGeneral WHERE IDReservaGeneral   = '".$_GET[IDReservaGeneral]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "#ServicioReserva" ); 
					exit;
	break;
	
	 case "EliminaInvitadoReserva":
					$id = $dbo->query( "DELETE FROM ReservaGeneralInvitado WHERE IDReservaGeneralInvitado   = '".$_GET[IDReservaGeneralInvitado]."' LIMIT 1" );
					SIMHTML::jsAlert("Eliminacion Exitoso");
					SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $_GET[id] . "#ServicioReserva" ); 
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
	Global $Dia_array;	
	$dbo =& SIMDB::get();
	$key = SIMReg::get( "key" );
	$table = SIMReg::get( "table" );
	$mod = SIMReg::get( "mod" );
?>


<table class=adminheading>
		<tr>
			<th> 
			<?php echo SIMReg::get( "title" )?>:: <?php echo $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$_GET[IDServicioMaestro]."'"); ?> :: Configuraci&oacute;n </th>
			
			
		</tr>
</table>

<?php include( "includes/menuclub.php" )?>


<?
//imprime el HTML de errores
SIMNotify::each();
include( "includes/tabs.html" );
?>


<div id="ContenedorServicio" style="z-index: 1;">
    <ul>
       <li><a href="#Servicio"><span>Configuraci&oacute;n</span></a></li>
       <li><a href="#ServicioDisponibilidad"><span>Disponibilidad General</span></a></li>
       <li><a href="#ServicioCampos"><span>Campos Personalizados</span></a></li>
       <li><a href="#ServicioElemento"><span>Elementos</span></a></li>
       <li><a href="#ServicioCierre"><span>Fechas de Cierre Servicio</span></a></li>
       <!-- <li><a href="#ServicioReserva"><span>Reservas</span></a></li> -->
    </ul>
    <div id="Servicio">
		<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" method="post" enctype="multipart/form-data" class="formvalida">
		<table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">	

			
			<tr>
			<td  class="columnafija" > Nombre </td>
            <td><?php echo $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$frm[IDServicioMaestro]."'"); ?></td>
			</tr>
			<tr>
			  <td class="columnafija"><? if (!empty($frm[Icono])) {
					echo "<img src='".SERVICIO_ROOT."$frm[Icono]' width=55 >";
					?>
			    <a
					href="<? echo "?mod=$mod&action=delfoto&foto=$frm[Icono]&campo=Icono&id=".$frm[$key]; ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    Icono </td>
			  <td><input name="Icono" id=file class=""
					title="Icono" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Texto Legal</td>
			  <td><textarea rows="5" cols="60" id="TextoLegal" title="Texto Legal" name="TextoLegal" class="input"><?php echo $frm["TextoLegal"] ?></textarea></td>
			  </tr>
			<tr>
			  <td class="columnafija">Dias de Anticipacion para reservar<br>
              (Si es 0 se puede reservar apartir del mismo d&iacute;a)
              </td>
			  <td><input id=DiasAnticipacion type=number size=25  name=DiasAnticipacion class="input mandatory " title="DiasAnticipacion" value="<?=$frm[DiasAnticipacion] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Dias maximo para hacer reservacion<br>
               (Ej: 2 = Maximo  se puede reservar dos dias adelante de la fecha actual)</td>
			  <td><input id=DiasMaximoReserva type=number size=25  name=DiasMaximoReserva class="input mandatory " title="DiasMaximoReserva" value="<?=$frm[DiasMaximoReserva] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Maximo de personas por turno</td>
			  <td><input id=MaximoPersonaTurno type=number size=25  name=MaximoPersonaTurno class="input mandatory " title="Maximo Persona Turno" value="<?=$frm[MaximoPersonaTurno] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Hora Inicial Servicio</td>
			  <td><input type="time" name="HoraDesde" id="HoraDesde" class="input" title="Hora desde" value="<?php echo $frm[HoraDesde] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Hora Final Servicio</td>
			  <td><input type="time" name="HoraHasta" id="HoraHasta" class="input" title="Hora hasta" value="<?php echo $frm[HoraHasta] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Intervalo de turnos</td>
			  <td>
              <input id=IntervaloHora type=number size=25  name=IntervaloHora class="input mandatory " title="IntervaloHora" value="<?=$frm[IntervaloHora] ?>">
              
              </td>
			  </tr>
			<tr>
			  <td class="columnafija">Numero invitados Club</td>
			  <td><input id=NumeroInvitadoClub type=text size=25  name=NumeroInvitadoClub class="input mandatory " title="Numero Invitados Club" value="<?=$frm[NumeroInvitadoClub] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Numero invitados Externo</td>
			  <td><input id=NumeroInvitadoExterno type=text size=25  name=NumeroInvitadoExterno class="input mandatory " title="Numero Invitados Externo" value="<?=$frm[NumeroInvitadoExterno] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Horas maximo para cancelar reserva</td>
			  <td><input id=HoraCancelacion type=text size=25  name=HoraCancelacion class="input mandatory " title="Horas cancelacion" value="<?=$frm[HoraCancelacion] ?>"></td>
			  </tr>
			<tr>
			  <td class="columnafija">Tiempo limite para confirmar reserva en app</td>
			  <td><input id=MinutosReserva type=text size=25  name=MinutosReserva class="input mandatory " title="Minutos Reserva" value="<?=$frm[MinutosReserva] ?>">
			    minutos</td>
			  </tr>
            
			<tr>
			  <td colspan=2 align=center>
			    
			    <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			    <input type="button" onclick="location.href='?mod=HomeClub&action=edit&id=<?php echo $_SESSION[IDClub]?>'" class="submit" value="Cancelar" name="submit">
			    <input type=hidden name=ID value="<? echo $frm[$key] ?>">
			    <input type=hidden name=action value=<?=$newmode?>>
			    <input type=hidden name="IDServicioMaestro" id="IDServicioMaestro" value="<?php if(!empty($frm["IDServicioMaestro"])) echo $frm["IDServicioMaestro"]; else echo $_GET["IDServicioMaestro"]; ?>">
			    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo $_SESSION[IDClub]; else echo $frm["IDClub"];  ?>" />
			    </td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
</form>

			
</div>

<div id="ServicioDisponibilidad">
	<?php include("tabs/servicio/disponibilidad.php" ) ?>
</div>
<div id="ServicioCampos">
	<?php include("tabs/servicio/campos.php" ) ?>
</div>

<div id="ServicioElemento">
	<?php include("tabs/servicio/elemento.php" ) ?>
</div>

<div id="ServicioCierre">
	<?php include("tabs/servicio/fechacierre.php" ) ?>
</div>

<div id="ServicioReserva">
	<?php //include("tabs/servicio/reserva.php" ) ?>
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
					Publicar&nbsp;
				</th>
					
<th align=center valign=middle width=64>Eliminar</th>
</tr>

<? 
$dbo =&SIMDB::get();
while( $r = $dbo->object( $result["result"] ) )
{
	
?>
  	
<tr class=<?echo SIMUtil::repetition()?'row0':'row1';?>>
<td align=center width=64 ><a href='<?php echo "?mod=" . $mod . "&amp;action=edit&amp;id=" . $r->$key?>'><img src='images/edit.png' border='0'></a></td>
<td nowrap><? echo $r->Nombre ?></td> <td nowrap><? echo $r->Publicar ?></td> 
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