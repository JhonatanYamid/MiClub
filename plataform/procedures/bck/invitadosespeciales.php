<?php
//Si es coordinador tenemos que mirar que servicio se trae primero
//si es un usuario normal se traen las horas
$fecha = date("Y-m-d");
if( !empty( $_POST["fecha"] ) )
	$fecha = $_POST["fecha"];



SIMReg::setFromStructure( array(
						"title" => "Invitados Especiales",
						"table" => "SocioInvitadoEspecial",
						"key" => "IDSocioInvitadoEspecial",
						"mod" => "SocioInvitadoEspecial"
	) );


	$script = "invitadosespeciales";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	//Verificar permisos
	//SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );



	switch ( SIMNet::req( "action" ) ) {

		case "add" :
			$view = "views/".$script."/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
		break;

		case "insert" :
		/*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST );


			$array_datos = array();
			for($cont_invitado=1;$cont_invitado<=($frm["NumeroInvitados"]-1);$cont_invitado++):
				$campo_tipodoc = "IDTipoDocumento".$cont_invitado;
				$campo_nombre = "Nombre".$cont_invitado;
				$campo_documento = "NumeroDocumento".$cont_invitado;
				$campo_apellido = "Apellido".$cont_invitado;
				$campo_email = "Email".$cont_invitado;
				$campo_fechanac = "FechaNacimiento".$cont_invitado;
				$campo_telefono = "Telefono".$cont_invitado;
				$campo_placa = "Placa1";
				$campo_tipoinvitado = "TipoInvitado".$cont_invitado;
				$campo_cabeza = "CabezaInvitacion".$cont_invitado;
				if(!empty($frm[$campo_nombre]) && !empty($frm[$campo_documento])):
					$array_datos_invitado["IDTipoDocumento"]=$frm[$campo_tipodoc];
					$array_datos_invitado["NumeroDocumento"]=$frm[$campo_documento];
					$array_datos_invitado["Nombre"]=$frm[$campo_nombre];
					$array_datos_invitado["Apellido"]=$frm[$campo_apellido];
					$array_datos_invitado["Email"]=$frm[$campo_email];
					$array_datos_invitado["TipoInvitado"]=$frm[$campo_tipoinvitado];
					$array_datos_invitado["Placa"]=$frm[$campo_placa];
					$array_datos_invitado["CabezaInvitacion"]=$frm[$campo_cabeza];
					array_push($array_datos, $array_datos_invitado);
				endif;
			endfor;
			$DatosInvitado = json_encode($array_datos);



			if($frm["NumeroInvitados"]>0):


				//Servicio de invitados
					$respuesta = SIMWebService::set_autorizacion_invitado($frm["IDClub"],$frm["IDSocio"],$frm["FechaInicio"],$frm["FechaFin"],$DatosInvitado,SIMUser::get("IDUsuario"),$val,$val);
					

						if( $respuesta >= 1 )
						{
							//bien

							SIMNotify::capture( $respuesta["message"] , "info alert-success" );
							//SIMNotify::capture( "La invitacion se ha creado correctamente" , "info alert-success" );

						}//end if
						else
						{
							//paila
							SIMNotify::capture( $respuesta_error , "error alert-danger" );
						}//end else

			else:
				SIMNotify::capture( "Faltan parametros" , "error alert-danger" );
			endif;
		}
		else
			exit;
		break;

		case "edit":

		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
		$view = "views/".$script."/form.php";
		$newmode = "update";
		$titulo_accion = "Actualizar";
		break ;

		case "registraingreso":
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
		$view = "views/".$script."/form.php";
		$newmode = "updateingreso";
		$titulo_accion = "Registrar Ingreso";

		break ;

		case "editinfo":
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
		//Consulto los invitados del grupo
		$sql_grupo = "Select * From " . $table . " Where IDPadre ='".$frm["IDInvitado"]."' or IDInvitado = '".$frm["IDInvitado"]."' and FechaInicio = '".$frm["FechaInicio"]."' and FechaFin = '".$frm["FechaFin"]."' Order By 	IDSocioInvitadoEspecial";
		$result_grupo = $dbo->query($sql_grupo);
		$contador_grupo=1;
		while($row_grupo = $dbo->fetchArray($result_grupo)):
			$array_datos_invitados[$contador_grupo] = $row_grupo;
			$contador_grupo++;
		endwhile;
		$view = "views/".$script."/form.php";
		$newmode = "updateinfo";
		$titulo_accion = "Actualizar Datos";

		break ;

		case "editobservacion":
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
		$view = "views/".$script."/form.php";
		$newmode = "updateobservacion";
		$titulo_accion = "Actualizar Datos";

		break ;


		case "update" :

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST);

			print_r($frm);
			exit;




			$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id"),"" );

			$frm = $dbo->fetchById( $table , $key , $id , "array" );

			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );
			SIMHTML::jsAlert("Registro Guardado Correctamente");
			SIMHTML::jsRedirect( $script.".php?action=edit&id=".SIMNet::reqInt("id") );
		}
		else
			exit;
		break;


		case "updateingreso" :

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST);

			$sql_ingreso = "Update ".$table." Set Estado = 'I', FechaIngresoClub = NOW(), Observaciones = '".$frm["Observaciones"]."' Where ".$key." = ".$id;
			$qry_ingreso = $dbo->query($sql_ingreso);

			SIMNotify::capture( "Se realizo el ingreso satisfactoriamente" , "info" );
			SIMHTML::jsAlert("Se realizo el ingreso satisfactoriamente");
			SIMHTML::jsRedirect( $script.".php?action=add");
		}
		else
			exit;

		case "updateinfo" :

	if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST );


			$array_datos = array();
			for($cont_invitado=1;$cont_invitado<=($frm["NumeroInvitados"]-1);$cont_invitado++):
				$campo_tipodoc = "IDTipoDocumento".$cont_invitado;
				$campo_nombre = "Nombre".$cont_invitado;
				$campo_documento = "NumeroDocumento".$cont_invitado;
				$campo_apellido = "Apellido".$cont_invitado;
				$campo_email = "Email".$cont_invitado;
				$campo_fechanac = "FechaNacimiento".$cont_invitado;
				$campo_telefono = "Telefono".$cont_invitado;
				$campo_placa = "Placa1";
				$campo_tipoinvitado = "TipoInvitado".$cont_invitado;
				$campo_cabeza = "CabezaInvitacion".$cont_invitado;
				if(!empty($frm[$campo_nombre]) && !empty($frm[$campo_documento])):
					$array_datos_invitado["IDTipoDocumento"]=$frm[$campo_tipodoc];
					$array_datos_invitado["NumeroDocumento"]=$frm[$campo_documento];
					$array_datos_invitado["Nombre"]=$frm[$campo_nombre];
					$array_datos_invitado["Apellido"]=$frm[$campo_apellido];
					$array_datos_invitado["Email"]=$frm[$campo_email];
					$array_datos_invitado["TipoInvitado"]=$frm[$campo_tipoinvitado];
					$array_datos_invitado["Placa"]=$frm[$campo_placa];
					$array_datos_invitado["CabezaInvitacion"]=$frm[$campo_cabeza];
					array_push($array_datos, $array_datos_invitado);
				endif;
			endfor;
			$DatosInvitado = json_encode($array_datos);

			if($frm["NumeroInvitados"]>0):
				//Servicio de invitados
				 $respuesta = SIMWebService::set_autorizacion_invitado_update($frm["IDClub"],$frm["IDSocio"],$frm["ID"],$frm["FechaInicio"],$frm["FechaFin"],$DatosInvitado);
				 //print_r($respuesta);


				if( !$respuesta["message"] )
				{
					//bien
					SIMHTML::jsAlert("La invitacion se ha modificado correctamente");
					SIMNotify::capture( "La invitacion se ha modificado correctamente" , "info alert-success" );
					SIMHTML::jsRedirect( $script.".php?action=add");
				}//end if
				else
				{
					//paila
					SIMHTML::jsAlert($respuesta["message"]);
					SIMNotify::capture( $respuesta["message"] , "error alert-danger" );
					SIMHTML::jsRedirect( $script.".php?action=add");
				}//end else

			else:
				SIMHTML::jsAlert("Faltan parametros");
				SIMNotify::capture( "Faltan parametros" , "error alert-danger" );
			endif;
		}
		else
			exit;
		break;


		case "updateobservacion" :

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST);

			$sql_edit = "Update ".$table." Set Observaciones = '".$frm["Observaciones"]."' Where ".$key." = ".$id;
			$qry_edit = $dbo->query($sql_edit);

			SIMNotify::capture( "Se ingreso la observacion satisfactoriamente" , "info" );
			SIMHTML::jsAlert("Se ingreso la observacion satisfactoriamente");
			SIMHTML::jsRedirect( $script.".php?action=add");
		}
		else
			exit;
		break;


		case "search" :
			$view = "views/".$script."/list.php";
		break;

		case "DelImgNot":
			$campo = $_GET['cam'];
			if($campo=="SWF"){
				$doceliminar = SWFEvento_DIR.$dbo->getFields( "Evento" , "$campo" , "IDEvento = '" . $_GET[id] . "'" );
				unlink($doceliminar);
				$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert("SWF eliminado Correctamente");
			}else{
				$doceliminar = IMGEVENTO_DIR.$dbo->getFields( "Evento" , "$campo" , "IDEvento = '" . $_GET[id] . "'" );
				unlink($doceliminar);
				$dbo->query("UPDATE Evento SET $campo = '' WHERE IDEvento = $_GET[id] LIMIT 1 ;");
				SIMHTML::jsAlert("Imagen Eliminada Correctamente");
			}
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id] );
			exit;
	break;


	default:
		if($_GET["permiso"]!="l")
			$view = "views/".$script."/list.php";
		else
			$view = "views/".$script."/listlectura.php";





	} // End switch


	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
