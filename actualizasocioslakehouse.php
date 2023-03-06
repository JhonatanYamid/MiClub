 
<?php
 
include("admin/config.inc.php");

$dbo = &SIMDB::get();
 

    require LIBDIR  . "SIMWebServiceZeus.inc.php";

        
	//Base Produccion
	$urlendpoint="http://200.1.126.78:1080/wsZeusGenerico/ServiceWS.asmx?WSDL";
	$usuariuozeus="userpos2";
	$clavezeus="userpos2022";
	$Identificacion="";
	$Tipo_Identificacion="";
	$Folio="";

	$Accion="";
	$Secuencia="";
 $Token=SIMWebServiceZeus::obtener_token_club_curl($urlendpoint,$usuariuozeus,$clavezeus);
	echo $Token;
	 
	echo "<br>";
	 
	 if(!empty($Token)){
		$datos_huesped=SIMWebServiceZeus::consultaconsumoshuesped($urlendpoint, $Token, $Identificacion, $Tipo_Identificacion, $Folio);
		 echo "<BR> <BR> RESPUESTA DEL SERVICIO HUESPED: "; print_r($datos_huesped);
		}	
		
 
if(!empty($Token)){
	$Accion="";
	$Identificacion="";
		$datos_usuario=SIMWebServiceZeus::estado_socio($urlendpoint,$Token,$Identificacion,$Accion,$Secuencia);
 
		print_r($datos_usuario);
		}
		
		/*
if ($IDClub == 233) {
 
              if ($EstadoSocio == "A") :
                $estado_socio = 1;
                
              if($bloqueo=="S"):
               $Permite_Reservar="N";
               $estado_socio = 6;
               endif;
               
            else :
                $estado_socio = 2;
            endif;
            
                $con_array = "N";
                $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' and Accion = '" . $Accion . "'";
                $result_socio = $dbo->query($sql_socio);
                $row_socio = $dbo->fetchArray($result_socio);
            }

 
 require(dirname(__FILE__) . "/admin/config.inc.php");
	$IDClub = 233;

	//Base Produccion
	$urlendpoint="http://200.1.126.78:1080/wsZeusGenerico/ServiceWS.asmx?WSDL";
	$usuariuozeus="userclubes2";
	$clavezeus="userclubes2022";

	$Accion="";
	$Secuencia="";
	 $Token=SIMWebServiceZeus::obtener_token_club_curl($urlendpoint,$usuariuozeus,$clavezeus);
	echo  $Token;
	 
	
	if(!empty($Token)){
	$Accion="02017382";
	$Identificacion="";
		$datos_usuario=SIMWebServiceZeus::estado_socio($urlendpoint,$Token,$Identificacion,$Accion,$Secuencia);
		
		echo "resp".$datos_usuario;
		
		
		foreach ($datos_usuario as $resp_usuario ){
			$actualizar="S";
			if(!empty($resp_usuario->identificacion)){
				if($resp_usuario->estado=="A"){
					$EstadoSocio="A";
					$array_activo[]=$resp_usuario->identificacion;
				}
				else{
					$EstadoSocio="I";
					$array_inactivo[]=$resp_usuario->identificacion;
				}

				//$Accion=trim($resp_usuario->accion).trim($resp_usuario->secuencia);
				$Accion=trim($resp_usuario->accion);
				$AccionPadre=trim($resp_usuario->accion);
				$Parentesco="";
				$Genero="";
				$Nombre=$resp_usuario->nombrecompleto;
				$Apellido="";
				$FechaNacimiento="";
				$NumeroDocumento=$resp_usuario->identificacion;
				$CorreoElectronico=$resp_usuario->item->email;
				$Telefono=$resp_usuario->telefonos;
				$Celular="";
				$Direccion=$resp_usuario->direccion;
				$TipoSocio="Socio";
				$InvitacionesPermitidasMes="100";
				$UsuarioApp=$resp_usuario->identificacion;
				$Predio="";
				$Categoria="";
				$CodigoCarne="";
				echo "<br>ACC " . $Accion . " ACCP" .  $AccionPadre . " NOM" . $Nombre . " DOC" . $NumeroDocumento . " CORRE" . $CorreoElectronico . " TEL" . $Telefono . " DIRECC" . $Direccion . " USUAPP" . $UsuarioApp . " Estado:".$EstadoSocio;
				if($EstadoSocio=="I"){
						if(in_array($NumeroDocumento,$array_activo))
							$actualizar="S";
						else
							$actualizar="N";
				}

				//if($actualizar=="S")
					//$resp=SIMWebServiceApp::set_socio($IDClub,$Accion,$AccionPadre,$Parentesco,$Genero,$Nombre,$Apellido,$FechaNacimiento,$NumeroDocumento,$CorreoElectronico,$Telefono,$Celular,$Direccion,$TipoSocio,$EstadoSocio,$InvitacionesPermitidasMes,$UsuarioApp,$Predio,$Categoria,"S",$CodigoCarne);
					
					 $inserta_socio = "INSERT INTO Socio (IDClub, IDSocioSistemaExterno, IdentificadorExterno, IDEstadoSocio, Accion, AccionPadre, IDParentesco, Parentesco, TipoSocio, IDCategoria, Genero, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, Telefono, Celular, FechaNacimiento, UsuarioTrCr, FechaTrCr, NumeroInvitados, NumeroAccesos, PermiteReservar,IDPermisoServicio, CambioClave,FotoActualizadaSocio,Predio,CodigoCarne)
											  Values ('" . $IDClub . "','" . $IDSocioSistemaExterno . "','" . $IDSocioSistemaExterno . "','" . $estado_socio . "','" . $Accion . "','" . $AccionPadre . "','" . $Parentesco . "','" . $Parentesco . "','" . $TipoSocio . "','" . $id_categoria . "','" . $Genero . "','" . trim($Nombre) . "','" . $Apellido . "','" . $NumeroDocumento . "','" . $UsuarioApp . "','" . $clave_socio . "','" . $CorreoElectronico . "',
												'" . $Telefono . "','" . $Celular . "','" . $FechaNacimiento . "','Cron',NOW(),'$InvitacionesPermitidasMes','$InvitacionesPermitidasMes','" . $Permite_Reservar . "', '" . $PermiteServicio . "', '" . $CambiarClave . "','S','" . $Predio . "','" . $CodigoCarne . "')";
												

				//print_r($resp);

			}
		}
	}
	else{
		echo "No se pudo obtener token";
	}
	echo "<br>Finalizado";
	exit;
?>*/
