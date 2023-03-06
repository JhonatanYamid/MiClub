<?

 SIMReg::setFromStructure( array(
           "title" => "Acceso Invitados",
           "table" => "SocioInvitado",
           "key" => "IDSocioInvitado",
           "mod" => "SocioInvitado"
 ) );


 $script = "accesoinvitado";

 //extraemos las variables
 $table = SIMReg::get( "table" );
 $key = SIMReg::get( "key" );
 $mod = SIMReg::get( "mod" );


 //Verificar permisos
 SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

 //creando las notificaciones que llegan en el parametro m de la URL
 SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );




 switch ( SIMNet::req( "action" ) ) {

   case "add" :
     $view = "views/".$script."/form.php";
     $newmode = "insert";
     $titulo_accion = "Crear";
   break;

   case "search" :
     //realizo busquedas
     //Guardo el Log de la busqueda
     //$sql_log_peticion =$dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('".SIMUser::get("club")."','".SIMUser::get("IDUsuario")."','".SIMNet::req("qryString")."','Consulta',NOW())");


     //BUSQUEDA INVITADOS ACCESOS
       $qryString = str_replace(".","",SIMNet::req("qryString"));
       $qryString = str_replace(",","",$qryString);
       if (ctype_digit($qryString)) {
         // si es solo numeros en un numero de documento
         $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and ( I.NumeroDocumento = '".(int)$qryString."' or I.NumeroDocumento = '".$qryString."' ) and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub = '".SIMUser::get("club")."' Order By IDSocioInvitadoEspecial";
         $modo_busqueda = "DOCUMENTO";
       } else {
         //seguramente es una placa
         //Consulto en invitaciones accesos
         $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub = '".SIMUser::get("club")."' Order By IDSocioInvitadoEspecial";
         $modo_busqueda = "PLACA";
       }


         $result_invitacion = $dbo->query($sql_invitacion);
         $total_resultados = $dbo->rows($result_invitacion);
         $datos_invitacion = $dbo->fetchArray($result_invitacion);
         $datos_invitacion["TipoInvitacion"] = "Invitado " . $datos_invitacion["TipoInvitacion"];
         $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
         $datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );

         if($datos_invitacion["Ingreso"]=="N"){
           $accion_acceso = "ingreso";
           $label_accion_acceso = "Ingres&oacute;";
         }
         elseif($datos_invitacion["Salida"]=="N")	{
           $accion_acceso	= "salio";
           $label_accion_acceso	= "Sali&oacute;";
         }
         $modulo="SocioInvitadoEspecial";
         $id_registro = $datos_invitacion["IDSocioInvitadoEspecial"];

         //Consulto grupo Familiar
         if($datos_invitacion["CabezaInvitacion"]=="S"):
           $sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '".$datos_invitacion["IDSocioInvitadoEspecial"]."'";
           $result_grupo = $dbo->query($sql_grupo);
         endif;
     //FIN BUSQUEDA INVITADOS ACCESOS

     //BUSQUEDA CONTRATISTA
       if($total_resultados<=0):
         if (ctype_digit($qryString)) {
             // si es solo numeros en un numero de documento
             $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and (I.NumeroDocumento = '".(int)$qryString."' or I.NumeroDocumento = '".$qryString."') and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub = '".SIMUser::get("club")."'";
             $modo_busqueda = "DOCUMENTO";
         } else {
           //seguramente es una placa
           //Consulto en invitaciones accesos
           /*
           $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub = '".SIMUser::get("club")."'
                               UNION Select SA.* From SocioAutorizacion SA Where Predio = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub = '".SIMUser::get("club")."'";
           */
           $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub = '".SIMUser::get("club")."'";
           $modo_busqueda = "PLACA";
         }


           $result_invitacion = $dbo->query($sql_invitacion);
           $total_resultados = $dbo->rows($result_invitacion);
           $datos_invitacion = $dbo->fetchArray($result_invitacion);

           if($datos_invitacion["Ingreso"]=="N"){
             $accion_acceso = "ingreso";
             $label_accion_acceso = "Ingres&oacute;";
           }
           elseif($datos_invitacion["Salida"]=="N")	{
             $accion_acceso	= "salio";
             $label_accion_acceso	= "Sali&oacute;";
           }

           $datos_invitacion["TipoInvitacion"] = "Contratista " . $datos_invitacion["TipoAutorizacion"];
           if($datos_invitacion["UsuarioTrCr"]>0){
              $datos_invitacion["CreadaPor"] = $dbo->getFields( "Usuario", "Nombre", "IDUsuario = '" . $datos_invitacion["UsuarioTrCr"] . "' " );
           }
           else{
             $datos_invitacion["CreadaPor"] = $datos_invitacion["UsuarioTrCr"];
           }


           $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
           $datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );
           $modulo="SocioAutorizacion";
           $id_registro = $datos_invitacion["IDSocioAutorizacion"];

           if($total_resultados>0){
            //Verifico si la cedula es de un funcionario
              $id_funcionario = $dbo->getFields( "Usuario", "IDUsuario", "NumeroDocumento = '" . $datos_invitado["NumeroDocumento"] . "' " );
             //Verifica diagnostico
             $fecha_hoy=date("Y-m-d") . " 00:00:00";
             $sql_unica="SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '".$fecha_hoy."' and IDUsuario='".$id_funcionario."' Group by IDusuario ";
             $r_unica=$dbo->query($sql_unica);
             $total_unica=$dbo->rows($r_unica);
             $row_resp_diag=$dbo->fetchArray($r_unica);
             $peso_permitido=$dbo->getFields( "Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' " );
             if($total_unica<=0){
               $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona no ha llenado el diagnostico</b></font>";
             }
             elseif($row_resp_diag["Resultado"]>$peso_permitido){
               $alerta_diagnostico="<font color='#F14823' size='4px' ><b> Atención la persona se debe comunicar con salud ocupacional  </b></font><br>";
             }
           }


       endif;
     //FIN BUSQUEDA CONTRATISTA

     //BUSQUEDA INVITADOS GENERAL
       if($total_resultados<=0):
         if (ctype_digit($qryString)) {
           $sql_invitacion = "Select SI.* From SocioInvitado SI Where (SI.NumeroDocumento = '".(int)$qryString."' or SI.NumeroDocumento = '".$qryString."' ) and FechaIngreso = '".date("Y-m-d")."' and IDClub = '".SIMUser::get("club")."'";
         }else{
           $sql_invitacion = "SELECT SI.*
                              From SocioInvitado SI, Invitado I, Vehiculo V
                              Where SI.IDInvitado = I.IDInvitado and I.IDInvitado = V.IDInvitado and V.Placa = '".$qryString."'
                              and FechaIngreso = '".date("Y-m-d")."' AND  SI.IDClub = '".SIMUser::get("club")."'";
           $modo_busqueda = "PLACA";

         }


           // si es solo numeros en un numero de documento
           //$sql_invitacion = "Select SI.* From SocioInvitado SI Where (SI.NumeroDocumento = '".(int)$qryString."' or SI.NumeroDocumento = '".$qryString."' ) and FechaIngreso = '".date("Y-m-d")."' and IDClub = '".SIMUser::get("club")."'";
           $modo_busqueda = "DOCUMENTO";
           $result_invitacion = $dbo->query($sql_invitacion);
           $total_resultados = $dbo->rows($result_invitacion);
           $datos_invitacion = $dbo->fetchArray($result_invitacion);
           $datos_invitacion["TipoInvitacion"] = "Invitado ";
           $datos_invitado_otro = $dbo->fetchAll( "Invitado", " NumeroDocumento = '" . $datos_invitacion["NumeroDocumento"] . "' and IDClub = '".SIMUser::get("club")."'  Limit 1", "array" );
           $datos_invitado["IDInvitado"] = $datos_invitado_otro["IDInvitado"];
           $datos_invitacion["IDInvitado"]=$datos_invitado_otro["IDInvitado"];
           $datos_invitado["FotoFile"] = $datos_invitado_otro["FotoFile"];
           $datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
           $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
           $datos_invitado["Nombre"]  = $datos_invitacion["Nombre"];
           $datos_invitado["NumeroDocumento"] = $datos_invitacion["NumeroDocumento"];
           $datos_invitado["IDTipoDocumento"] = "2"; // le pongo cc a todos ya que en el modulo no se solicita este dato
           $modulo="SocioInvitado";
           $id_registro = $datos_invitacion["IDSocioInvitado"];
           if($datos_invitacion["Estado"]=="P"){
             $accion_acceso = "ingreso";
             $label_accion_acceso = "Ingres&oacute;";
           }

           $sql_otros="SELECT IDCampoFormularioInvitado,Valor
                      FROM InvitadosOtrosDatos
                      WHERE IDInvitacion = '".$datos_invitacion["IDSocioInvitado"]."'";
           $r_otros=$dbo->query($sql_otros);
           while($row_otros_datos=$dbo->fetchArray($r_otros)){
             $campo_otro = $dbo->getFields( "CampoFormularioInvitado", "EtiquetaCampo", "IDCampoFormularioInvitado = '" . $row_otros_datos["IDCampoFormularioInvitado"] . "' " );
             $OtrosDatos.="<br>".$campo_otro."=".$row_otros_datos["Valor"];
           }

           if($total_resultados>0){
           //Verifica diagnostico
           $fecha_hoy=date("Y-m-d") . " 00:00:00";
           $sql_unica="SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '".$fecha_hoy."' and NumeroDocumento='".$qryString."' Group by IDusuario ";
           $r_unica=$dbo->query($sql_unica);
           $total_unica=$dbo->rows($r_unica);
           $row_resp_diag=$dbo->fetchArray($r_unica);
           $peso_permitido=$dbo->getFields( "Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' " );
           if($total_unica<=0){
             $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención: la persona no ha llenado el diagnostico</b></font>";
           }
           elseif($row_resp_diag["Resultado"]>$peso_permitido){
             $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona se debe comunicar con salud ocupacional  </b></font><br>";
           }
           else{
             $alerta_diagnostico="<font color='#059C1C'><b> Diagnostico correcto  </b></font><br>";
           }
         }


       endif;
     //FIN BUSQUEDA CONTRATISTA




     //BUSQUEDA USUARIO FUNCIONARIO
     //para lagartos solo pueden ingresar los que tengan invitacion
     if($total_resultados<=0 && SIMUser::get("club") != 7):
           $sql_invitacion = "Select * From Usuario Where (NumeroDocumento = '".$qryString."' or NumeroDocumento = '".(int)$qryString."' ) and IDClub = '".SIMUser::get("club")."'";
           $modo_busqueda = "DOCUMENTO";

           $result_invitacion = $dbo->query($sql_invitacion);
           $total_resultados = $dbo->rows($result_invitacion);
           $datos_invitacion = $dbo->fetchArray($result_invitacion);
           $datos_invitacion["TipoInvitacion"] = "Usuario";
           $datos_socio = $dbo->fetchAll( "Usuario", " IDUsuario = '" . $datos_invitacion["IDUsuario"] . "' ", "array" );
           $datos_invitado = $datos_socio;
           $modulo="Usuario";
           $id_registro = $datos_invitacion["IDUsuario"];

           if($total_resultados>0){
             //Verifica diagnostico
             $fecha_hoy=date("Y-m-d") . " 00:00:00";
             $sql_unica="SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '".$fecha_hoy."' and IDUsuario='".$id_registro."' Group by IDusuario ";
             $r_unica=$dbo->query($sql_unica);
             $total_unica=$dbo->rows($r_unica);
             $row_resp_diag=$dbo->fetchArray($r_unica);
             $peso_permitido=$dbo->getFields( "Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' " );
             $etiqueta_tipo="Funcionario";
             if($total_unica<=0){
               $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona no ha llenado el diagnostico.</b></font><br>";
             }
             elseif($row_resp_diag["Resultado"]>$peso_permitido){
               $alerta_diagnostico="<font color='#F14823' size='4px'  ><b> Atención la persona se debe comunicar con salud ocupacional  </b></font><br>";
             }
             else{
               $alerta_diagnostico="<font color='#059C1C'><b> Diagnostico correcto  </b></font><br>";
             }
           }

       endif;
     //FIN BUSQUEDA USUARIO FUNCIONARIO




     //BUSQUEDA SOCIO
     if($total_resultados<=0):

         if (ctype_digit($qryString)) {
             // si es solo numeros en un numero de documento
             $sql_invitacion = "Select * From Socio Where (NumeroDocumento = '".$qryString."' or NumeroDocumento = '".(int)$qryString."' or Accion = '".$qryString."' or NumeroDerecho = '".$qryString."'  ) and IDClub = '".SIMUser::get("club")."'";
             $modo_busqueda = "DOCUMENTO";
         } else {
           //seguramente es una placa	o una accion
           //Consulto las placas de vehiculos de socios
           /*
           $sql_invitacion = "Select * From Socio Where (Accion = '".$qryString."' or NumeroDerecho = '".$qryString."') and IDClub = '".SIMUser::get("club")."'
                     UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '".$qryString."' and IDClub = '".SIMUser::get("club")."'
                     UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''";
           */

           /*
           $sql_invitacion = "Select * From Socio Where (Accion = '".$qryString."' or NumeroDerecho = '".$qryString."' or NumeroDocumento = '".$qryString."' or Predio like '".$qryString."') and IDClub = '".SIMUser::get("club")."'
                      UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''
                     UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''  order by IDSocio ASC";
         */

         $sql_invitacion = "SELECT * FROM Socio WHERE (Accion = '".$qryString."' or NumeroDerecho = '".$qryString."' or Predio like '".$qryString."'or Email = '".$qryString."') and IDClub = '".SIMUser::get("club")."'
                            UNION
                            SELECT S.* FROM Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''
                            UNION
                            SELECT S.* FROM Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''
                            ORDER BY IDSocio ASC";



         }

           $result_invitacion = $dbo->query($sql_invitacion);
           $total_resultados = $dbo->rows($result_invitacion);
           $datos_invitacion = $dbo->fetchArray($result_invitacion);
           $datos_invitacion["TipoInvitacion"] = "Socio Club";
           $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
           $datos_invitado = $datos_socio;
           $modulo="Socio";
           $id_registro = $datos_invitacion["IDSocio"];

           if($total_resultados>0){
             //Verifica diagnostico
             $fecha_hoy=date("Y-m-d") . " 00:00:00";
             $sql_unica="SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '".$fecha_hoy."' and IDSocio='".$id_registro."' GROUP BY IDSocio ";
             $r_unica=$dbo->query($sql_unica);
             $total_unica=$dbo->rows($r_unica);
             $row_resp_diag=$dbo->fetchArray($r_unica);
             $peso_permitido=$dbo->getFields( "Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' " );
             if($total_unica<=0){
               $alerta_diagnostico="<font color='#F14823'><b> Atención la persona no ha llenado el diagnostico!</b></font><br>";
             }
             elseif($row_resp_diag["Resultado"]>$peso_permitido){
               $alerta_diagnostico="<font color='#F14823' size='4px'><b> El Socio y su grupo familiar No pueden ingresar  </b></font><br>";
             }
             else{
               $alerta_diagnostico="<font color='#059C1C'><b> Diagnostico correcto  </b></font><br>";
             }

             //Valido si solo permite reservar por edades
         	  if($datos_invitacion["FechaNacimiento"]!="0000-00-00"){

         	      $fecha_nacimiento = $datos_invitacion["FechaNacimiento"];
           	  	$dia_actual = date("Y-m-d");
           	  	$edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
           	  	$EdadSocio=$edad_diff->format('%y');
           	    if($EdadSocio>=18 && $EdadSocio <= 70){
                  $alerta_edad="<font color='#059C1C'><b> Edad correcta</b></font><br>";
           	      $edadpermitida=="S";
           	    }
           	    else{
                  $alerta_edad="<font color='#F14823'><b> No tiene la edad permitida</b></font><br>";
           	    }
         	  }
            else{
              $alerta_edad="<font color='#F14823'><b> Sin edad</b></font><br>";
            }

            //Reservas
            $resp=SIMWebService::get_reservas_socio( SIMUser::get("club"), $datos_socio["IDSocio"], $Limite, $IDReserva, 1);
            $resp_como_invitado=SIMWebServiceApp::get_reservas_socio_invitado(SIMUser::get("club"),$datos_socio["IDSocio"],0);
           }


           //Consulto grupo Familiar
           if (empty($datos_socio["AccionPadre"]) || $datos_socio["AccionPadre"] == $datos_socio["Accion"]): // Es Cabeza
             $nucleo_socio = 1;
             $condicion_nucleo = " and AccionPadre = '".$datos_socio["Accion"]."'";
             $datos_invitacion["CabezaInvitacion"]="S";
             $response_nucleo = array();
             $sql_grupo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras, NumeroDocumento FROM Socio WHERE IDClub = '".SIMUser::get("club")."' and IDSocio <> '".$datos_socio["IDSocio"]."' " . $condicion_nucleo;
             $result_grupo = $dbo->query($sql_grupo);
           endif;

       endif;
     //FIN BUSQUEDA SOCIO

     // Si no lo encuntra busco si tiene alguna autorización para otro dia

     if($total_resultados<=0):

       $datos_inv_prox = $dbo->fetchAll( "Invitado", " NumeroDocumento = '" . $qryString . "' ", "array" );
       if((int)$datos_inv_prox["IDInvitado"]>0):
         //Autorizaciones para otro dia
           $sql_auto_post = "Select * From SocioAutorizacion Where IDInvitado = '".$datos_inv_prox["IDInvitado"]."' and FechaInicio > '".date("Y-m-d")."'";
           $result_auto_post = $dbo->query($sql_auto_post);
           if($dbo->rows($result_auto_post)>0):
             $row_auto_post = $dbo->fetchArray($result_auto_post);
             $array_proxima_autorizacion [] = $datos_inv_prox["Nombre"] . " " . $datos_inv_prox["Apellido"] . " tiene una autorizacion para el " .  $row_auto_post["FechaInicio"];
           endif;
         //Invitaciones para otro dia
           $sql_inv_post = "Select * From SocioInvitadoEspecial Where IDInvitado = '".$datos_inv_prox["IDInvitado"]."' and FechaInicio > '".date("Y-m-d")."'";
           $result_inv_post = $dbo->query($sql_inv_post);
           if($dbo->rows($result_inv_post)>0):
             $row_inv_post = $dbo->fetchArray($result_inv_post);
             $array_proxima_autorizacion [] = $datos_inv_prox["Nombre"] . " " . $datos_inv_prox["Apellido"] . " tiene una invitacion para el " .  $row_inv_post["FechaInicio"];
           endif;
       endif;
     endif;





     $view = "views/".$script."/list.php";
   break;

   default:
     $view = "views/".$script."/list.php";





 } // End switch



 if( empty( $view ) )
   $view = "views/".$script."/form.php";


?>
