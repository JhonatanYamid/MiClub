 <?

	SIMReg::setFromStructure( array(
						"title" => "Reserva Hotel",
						"table" => "ReservaHotel",
						"key" => "IDReserva",
						"mod" => "Hotel"
	) );


	$script = "reservashotel";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	require_once LIBDIR . "SIMWebServiceHotel.inc.php";
	//Verificar permisos
	SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );


	switch ( SIMNet::req( "action" ) ) {

		case "add" :
			$view = "views/".$script."/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
      if(!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"])){
          $resultado_habitacion=SIMWebServiceHotel::get_valida_fecha( $_GET["IDClub"], $_GET["FechaInicio"], $_GET["FechaFin"],"S","");
      }


		break;

		case "insert" :

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );


          //$invitados = explode("\r",$frm["SocioInvitado"]);
    			$invitados = explode("|||",$frm["InvitadoSeleccion"]);
    			if(count($invitados)>0):
    				foreach($invitados as $nom_invitado):
    					$array_datos = explode("-",$nom_invitado);
    					if($array_datos[0]=="socio"): // socio club
    						$datos_invitado[]["IDSocio"] = trim($array_datos[1]);
    					elseif($array_datos[0]=="externo"): // invitado externo
                //como viene solo el nombre le pongo un documento ficticio
                $doc=rand(100000,90000000);
                $IDInvitado=$dbo->getFields( "ReservaHotelInvitado" , "IDReservaHotelInvitado" , "NumeroDocumento = '".$doc."'" );
                if((int)$IDInvitado>0){
                  $update_inv="UPDATE ReservaHotelInvitado SET IDSocio = '".$frm["IDSocio"]."' , Nombre = '".trim($array_datos[1])."' WHERE  IDReservaHotelInvitado = '".$IDInvitado."' ";
                  $dbo->query($update_inv);
                  $id_invitado_h = $IDInvitado;
                }
                else{
                  $inserta_inv="INSERT INTO ReservaHotelInvitado (IDSocio, TipoAsistente, NumeroDocumento, Nombre )
                               VALUES ('".$frm["IDSocio"]."','invitado','".$doc."', '".trim($array_datos[1])."')";
                  $dbo->query($inserta_inv);
                  $id_invitado_h = $dbo->lastID();
                }
                $datos_invitado[]["IDInvitado"] = $id_invitado_h;
    						//$datos_invitado[]["Nombre"] = trim($array_datos[1]);
    					endif;
    				endforeach;
    				$array_invitados = json_encode($datos_invitado);
    			else:
    				$array_invitados = "";
    			endif;

					 //insertamos los datos
		       //$id = $dbo->insert( $frm , $table , $key );

           $frm["Estado"]="enfirme";
           if($frm["Estado"]!="enfirme"){
             	//SIMHTML::jsAlert("Atencion: Solo con el estado Enfirme la reserva queda confirmada, d elo contrario solo queda separada y otro socio puede tomarla ");
           }

           $IDTemporadaAlta=SIMWebServiceHotel::verifica_temporada($frm["FechaInicio"],$frm["IDClub"]);
           if($IDTemporadaAlta>0)	:
   						$TemporadaActual = "Alta";
   						//datos de temporada alta
   						$TemporadaAlta = $dbo->query( "SELECT * FROM TemporadaAlta WHERE IDTemporadaAlta = '".$IDTemporadaAlta."'" );
   						$ArrayTemporadaAlta = $dbo->fetchArray( $TemporadaAlta );
   				else:
   						$TemporadaActual = "Baja";
   				endif;

           $respuesta = SIMWebServiceHotel::set_reserva($frm["IDClub"],$frm["IDSocio"],"","",$frm["IDHabitacion"],$IDPromocion,$IDTemporadaAlta,$TemporadaActual,$frm["CabezaReserva"],$frm["Estado"],$frm["FechaInicio"],$frm["FechaFin"],$frm["Ninera"],$frm["Corral"],$frm["IVA"]
           ,$frm["NumeroPersonas"],$frm["Adicional"],$frm["Pagado"],$frm["FechaReserva"],$array_invitados,"S",SIMUser::get( "IDUsuario" ),$frm["NombreDuenoReserva"],$frm["DocumentoDuenoReserva"],$frm["EmailDuenoReserva"]);

           $IDReserva=$respuesta["response"]["0"]["IDReserva"];

          /*
          $invitados = explode("|||",$frm["InvitadoSeleccion"]);
     			if(count($invitados)>0):
     				foreach($invitados as $nom_invitado):
     					$array_datos = explode("-",$nom_invitado);
     					if($array_datos[0]=="socio"): // socio club
     						$inserta_socio =  "INSERT INTO ReservaHotelDetalleInvitado (IDReservaHotel, IDReservaHotelInvitado, IDSocioInvitado,TipoInvitado)
     															Values ('".$IDReserva."','','".$array_datos[1]."', 'Socio')";
     						$dbo->query($inserta_socio);

     					elseif($array_datos[0]=="externo"): // invitado externo
                   $sql_nvo_inv="INSERT INTO ReservaHotelInvitado (IDSocio,TipoAsistente,NumeroDocumento,Nombre) VALUES ('".$frm["IDSocio"]."','Invitado','".$frm["IDSocio"]."','".$array_datos[1]."')";
                   $dbo->query($sql_nvo_inv);
                   $id_invitado = $dbo->lastID();
     							$inserta_externo = "INSERT INTO ReservaHotelDetalleInvitado (IDReservaHotel, IDReservaHotelInvitado, IDSocioInvitado,TipoInvitado)
     								  Values ('".$IDReserva."','".$id_invitado."', '','Externo')";
     							$dbo->query($inserta_externo);
     					endif;
     				endforeach;
     				$respuesta["success"] = "1";
     			endif;
          */

				//var_dump($frm["campos_dinamicos"]["keys"]);
				//Campos dinamicos
				for($i=0; $i<$frm["campos_dinamicos"]["keys"]; $i++){
					$frm_dinamico = [];
					$frm_dinamico["Valor"] = $frm["campos_dinamicos"]["Valor_".$i];
					$frm_dinamico["IDSocio"] = $frm["IDSocio"];
					$frm_dinamico["IDUsuario"] = SIMUser::get("IDUsuario");
					$frm_dinamico["IDCampoHotel"] = $frm["campos_dinamicos"]["IDCampoHotel_".$i];
					$frm_dinamico["IDHotelCampoHotel"] = $frm["campos_dinamicos"]["IDHotelCampoHotel_".$i];
					$frm_dinamico["IDReserva"] = $IDReserva;

					$frm_dinamico = SIMUtil::varsLOG( $frm_dinamico );

					if($frm_dinamico["IDHotelCampoHotel"] == null && $frm_dinamico["IDHotelCampoHotel"]==''){
						$id = $dbo->insert($frm_dinamico, 'HotelCampoHotel', 'IDHotelCampoHotel');
					}else{
						$id = $dbo->update($frm_dinamico, 'HotelCampoHotel', 'IDHotelCampoHotel', $frm_dinamico["IDHotelCampoHotel"]);
					}
				}

					SIMHTML::jsAlert($respuesta["message"]);
					SIMHTML::jsRedirect( $script.".php?action=add" );
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

		case "search" :
			$view = "views/".$script."/list.php";
		break;


    case "updateduenoinvitadohotel":
      $frm = SIMUtil::varsLOG( $_POST );
      if(!empty($frm["IDReservaHotel"]) && !empty($frm["DocumentoDuenoReserva"]) && !empty($frm["NombreDuenoReserva"])):
       $ObservacionCambio = "El Usuario " . SIMUser::get("Nombre") . " cambio el dueno de la reserva que era originalmente";
       $update_reserva="Update ReservaHotel Set Pagado = '".$frm["Pagado"]."', DocumentoDuenoReserva = '".$frm["DocumentoDuenoReserva"]."',NombreDuenoReserva = '".$frm["NombreDuenoReserva"]."',EmailDuenoReserva = '".$frm["EmailDuenoReserva"]."', Observaciones= '".$ObservacionCambio."',UsuarioTrEd='".SIMUser::get("Nombre")." ". $ObservacionCambio ."', FechaTrEd = NOW() Where IDReserva = '".$frm["IDReservaHotel"]."'";
       $dbo->query($update_reserva);
       if($frm["CorreoPago"]=="S"){
          SIMUtil::envia_correo_pago_reserva($frm);
       }
       SIMNotify::capture( "Datos actualizados con exito" , "info alert-success" );
    elseif($frm["CabezaReserva"]=="Socio"):
      $ObservacionCambio = "El Usuario " . SIMUser::get("Nombre") . " cambio el estado pagado a " . $frm["Pagado"];

      if($frm["Pagado"]=="S"){
        $actualiza_estado=", Estado = 'enfirme'";

      $update_reserva="Update ReservaHotel Set Pagado = '".$frm["Pagado"]."', DocumentoDuenoReserva = '".$frm["DocumentoDuenoReserva"]."', Observaciones= '".$ObservacionCambio."',UsuarioTrEd='".SIMUser::get("Nombre")." ". $ObservacionCambio ."', FechaTrEd = NOW() ".$actualiza_estado." Where IDReserva = '".$frm["IDReservaHotel"]."'";
      $dbo->query($update_reserva);
   $fecha1= ($frm["FechaInicio"]);
$fecha2= ($frm["FechaFin"]);

$dias = (strtotime($fecha1)-strtotime($fecha2))/86400;
$dias = abs($dias);
$dias = floor($dias); 
 $sql_socio = "SELECT * FROM ReservaHotel WHERE IDReserva='".$frm["IDReservaHotel"]."'";
                $result_socio = $dbo->query($sql_socio);
                $row_socio = $dbo->fetchArray($result_socio);
               $idsocio= $row_socio["IDSocio"];
               $idhabitacion= $row_socio["IDHabitacion"];
               
                $noches = "SELECT * FROM SocioHabitacion WHERE IDHabitacion=$idhabitacion and  IDSocio=$idsocio ";
                $cantidadnoches = $dbo->query($noches);
                $cantidad = $dbo->fetchArray($cantidadnoches);
               $numeronoches= $cantidad["Noches"];
               if($numeronoches>0 and $dias<=$numeronoches){
 $total=($numeronoches-$dias);
 }else{
 $total=$numeronoches;
 }
 

      $actualizarnoches = "UPDATE SocioHabitacion SET  Noches=$total WHERE IDHabitacion='$idhabitacion' and IDSocio='$idsocio'";
                            $qryHabitaciones = $dbo->query($actualizarnoches);
      
      SIMNotify::capture( "Pago actualizado con exito" , "info alert-success" );
    }else{
        SIMNotify::capture( "Falta datos, por favor verifique" , "info alert-danger" );
        }
     endif;
    break;

      case 'updateinvitadohotel':

  			$frm = SIMUtil::varsLOG( $_POST );
  			//$invitados = explode("\r",$frm["SocioInvitado"]);
  			//$invitados=$frm["SocioInvitado"];
  			// print_r($frm);
  			// exit;
  			 if($frm["IDSocioOrig"]!=$frm["IDSocio"] && $frm["IDSocio"]>0):
  				//Actualizo el socio dueño de la reserva
  				$ObservacionCambio = "El Usuario " . SIMUser::get("Nombre") . " cambio el dueno de la reserva que era originalmente " . $frm["IDSocioOrig"];
  				$update_reserva="Update ReservaHotel Set IDSocio = '".$frm["IDSocio"]."', Observaciones= '".$ObservacionCambio."',UsuarioTrEd='".SIMUser::get("Nombre")." ". $ObservacionCambio ."', FechaTrEd = NOW() Where IDReserva = '".$frm["IDReservaHotel"]."'";
  				$dbo->query($update_reserva);
  			endif;

        $invitados = explode("|||",$frm["InvitadoSeleccion"]);
  			if(count($invitados)>0):
  				// Borro invitados
  				$sql_invidado_reserva_del = "Delete From ReservaHotelDetalleInvitado Where IDReservaHotel = '".$frm["IDReservaHotel"]."'";
  				$dbo->query( $sql_invidado_reserva_del);
  				foreach($invitados as $nom_invitado):
  					$array_datos = explode("-",$nom_invitado);
  					if($array_datos[0]=="socio"): // socio club
  						$inserta_socio =  "INSERT INTO ReservaHotelDetalleInvitado (IDReservaHotel, IDReservaHotelInvitado, IDSocioInvitado,TipoInvitado)
  															Values ('".$frm["IDReservaHotel"]."','','".$array_datos[1]."', 'Socio')";
  						$dbo->query($inserta_socio);

  					elseif($array_datos[0]=="externo"): // invitado externo
						$sql_nvo_inv="INSERT INTO ReservaHotelInvitado (IDSocio,TipoAsistente,NumeroDocumento,Nombre) VALUES ('".$frm["IDSocio"]."','Invitado','".$frm["IDSocio"]."','".$array_datos[1]."')";
						$dbo->query($sql_nvo_inv);
						$id_invitado = $dbo->lastID();
									$inserta_externo = "INSERT INTO ReservaHotelDetalleInvitado (IDReservaHotel, IDReservaHotelInvitado, IDSocioInvitado,TipoInvitado)
										Values ('".$frm[IDReservaHotel]."','".$id_invitado."', '','Externo')";
  							$dbo->query($inserta_externo);
  					endif;
  				endforeach;
  				$respuesta["success"] = "1";
  			endif;
  			if( $respuesta["success"] == "1" )
  			{
  				//bien
          SIMNotify::capture( "Invitados modificados correctamente" , "info alert-success" );
  			}//end if
  			else
  			{
          //paila
  				SIMNotify::capture( "Se produjo un error al guardar"  , "error alert-danger" );
  			}//end else
  		break;



		default:
			$view = "views/".$script."/list.php";
	} // End switch


  if (!empty($_GET["idr"])):
    $detalle_reserva = $dbo->fetchAll( "ReservaHotel", " IDReserva = '" . $_GET["idr"] . "' ", "array" );
    $sql_invitado_reserva = "Select * From ReservaHotelDetalleInvitado Where IDReservaHotel = '".$_GET["idr"]."'";
    $qry_invitado_reserva = $dbo->query($sql_invitado_reserva);
    while($row_invitado_reserva = $dbo->fetchArray($qry_invitado_reserva)):
      $array_invitados[$row_invitado_reserva["IDReservaHotelDetalleInvitado"]] = $row_invitado_reserva;
    endwhile;
  endif;



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
