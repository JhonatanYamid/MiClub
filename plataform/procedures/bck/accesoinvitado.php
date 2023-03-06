<?

 SIMReg::setFromStructure( array(
           "title" => "Acceso Invitados",
           "title1" => "Registrar vacuna",
           "table" => "SocioInvitado",
           "key" => "IDSocioInvitado",
           "mod" => "SocioInvitado",

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

$IngresoPermitidoLag=4;


 switch ( SIMNet::req( "action" ) ) {

   case "add" :
     $view = "views/".$script."/form.php";
     $newmode = "insert";
     $titulo_accion = "Crear";
   break;
   case "update-vacuna":
    //los campos al final de las tablas
    $frm = SIMUtil::varsLOG($_POST);

    $dbo = &SIMDB::get();
    //$sql1 ="SELECT V.*, I.IDInvitado FROM Vacuna V LEFT JOIN Invitado I ON I.IDInvitado=V.IDInvitado WHERE  I.IDInvitado=". $frm['IDInvitado'];
    $query = $dbo->query("SELECT V.*, I.IDInvitado FROM Vacuna V LEFT JOIN Invitado I ON I.IDInvitado=V.IDInvitado WHERE I.IDInvitado=". $frm['IDInvitado']);
    $consult = $dbo->fetch($query);

    if (empty($consult['IDVacuna'])) {
        $id = $dbo->insert($frm, 'Vacuna', 'IDVacuna');
    } else {
        $id = $dbo->update($frm, 'Vacuna', 'IDVacuna', $consult['IDVacuna']);
    }

    for ($i = 0; $i < $frm["campos_dinamicos"]["keys"]; $i++) {
        $frm_dinamico = [];
        $frm_dinamico["Valor"] = $frm["campos_dinamicos"]["Valor_" . $i];
        $frm_dinamico["Dosis"] = $frm["campos_dinamicos"]["Dosis_" . $i];
        $frm_dinamico["IDSocio"] = $frm["campos_dinamicos"]["IDSocio_" . $i];
        $frm_dinamico["IDCampoVacunacion"] = $frm["campos_dinamicos"]["IDCampoVacunacion_" . $i];
        $frm_dinamico["IDVacunaCampoVacunacion"] = $frm["campos_dinamicos"]["IDVacunaCampoVacunacion_" . $i];

        $frm_dinamico = SIMUtil::varsLOG($frm_dinamico);

        if ($frm_dinamico["IDVacunaCampoVacunacion"] == null && $frm_dinamico["IDVacunaCampoVacunacion"] == '') {
            $id = $dbo->insert($frm_dinamico, 'VacunaCampoVacunacion', 'IDVacunaCampoVacunacion');
        } else {
            $id = $dbo->update($frm_dinamico, 'VacunaCampoVacunacion', 'IDVacunaCampoVacunacion', $frm_dinamico["IDVacunaCampoVacunacion"]);
        }
    }

    //UPLOAD de imagenes
    if (isset($_FILES)) {

        if (!empty($_FILES['ImagenPrimeraDosis']['name'])) {
            $files = SIMFile::upload($_FILES["ImagenPrimeraDosis"], VACUNA_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
            }

            $frm["ImagenPrimeraDosis"] = $files[0]["innername"];
        }

        if (!empty($_FILES['ImagenSegundaDosis']['name'])) {
            $files = SIMFile::upload($_FILES["ImagenSegundaDosis"], VACUNA_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["Foto1"]["name"])) {
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
            }

            $frm["ImagenSegundaDosis"] = $files[0]["innername"];
        }
    } //end if

    $id = $dbo->update($frm, 'Vacuna', 'IDVacuna', $id);

    SIMHTML::jsAlert("Registro  Correctamente");
    SIMHTML::jsRedirect($script . ".php?action=edit&id={$frm['IDInvitado']}");
    break;

case "del-vacuna-image":
    $archivo = $_GET['archivo'];
    $numImagen = $_GET['num_img'];
    $campo = $_GET['campo'];
    $id = $_GET['id'];
    $idSocio = $_GET['IDSocio'];
    $filedelete = VACUNA_DIR . $archivo;
    unlink($filedelete);
    $queryUpdate = "UPDATE Vacuna SET Imagen$numImagen" . "Dosis=NULL WHERE IDVacuna=$id";
    $dbo->query($queryUpdate);
    SIMHTML::jsAlert("Imagen Eliminada Correctamente");
    SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $idSocio . "");
    break;
   case "search" :

    $qryString = str_replace(".","",SIMNet::req("qryString"));
    $qryString = str_replace(",","",$qryString);

    //Verificar si la busqueda es por nombre
    if($_GET["IDTipoBusqueda"]==2){

      include("./procedures/accesoinvitadobusqueda.php");
      $view = "views/".$script."/list.php";
      break;
    }

    //Verificar si el predio buscado tiene mas de 1 dueño
    $prediosSql = "SELECT S.Nombre, S.Apellido, S.NumeroDocumento, S.Accion
        FROM Predio P
        INNER JOIN Socio S ON P.IDSocio=S.IDSocio
        WHERE P.Predio='$qryString'";
    $predioQuery = $dbo->query($prediosSql);
    $predios = $dbo->fetch($predioQuery);

    if(!empty($predios) && isset($predios["Nombre"])){
      $predios = [$predios];
    }

	  //var_dump($predios);

    if(!empty($predios) && count($predios) > 0){
      $view = "views/".$script."/list.php";
      break;
    }


     //realizo busquedas
     //Guardo el Log de la busqueda
     //$sql_log_peticion =$dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('".SIMUser::get("club")."','".SIMUser::get("IDUsuario")."','".SIMNet::req("qryString")."','Consulta',NOW())");
     $EsSocio="N";
     $sql_soc="SELECT IDSocio FROM Socio Where NumeroDocumento = '".SIMNet::req("qryString")."' and IDClub = '".SIMUser::get("club")."' and IDEstadoSocio = 1 Limit 1";
     $r_soc=$dbo->query($sql_soc);
     if($dbo->rows($r_soc)>0){
       $EsSocio="S";
     }




     //BUSQUEDA INVITADOS ACCESOS

       $qryString = str_replace(".","",SIMNet::req("qryString"));
       $qryString = str_replace(",","",$qryString);
       if ($qryString) {
         // si es solo numeros en un numero de documento
        $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and ( I.NumeroDocumento = '".(int)$qryString."' or I.NumeroDocumento = '".$qryString."' ) and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub = '".SIMUser::get("club")."' Order By IDSocioInvitadoEspecial";
         $modo_busqueda = "DOCUMENTO";

       } else {
         //seguramente es una placa
         //Consulto en invitaciones accesos
         $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub = '".SIMUser::get("club")."' Order By IDSocioInvitadoEspecial";
         $modo_busqueda = "PLACA";
       }




         $configuracion_club = "Select * From ConfiguracionClub C Where IDClub = '".SIMUser::get("club")."' ";
         $result_configuracion_club =$dbo->query($configuracion_club);
         $datos_configuracion_club =$dbo->fetchArray( $result_configuracion_club);
       
       
         $result_invitacion = $dbo->query($sql_invitacion);
         $total_resultados = $dbo->rows($result_invitacion);
         $datos_invitacion = $dbo->fetchArray($result_invitacion);
         $datos_invitacion["TipoInvitacion"] = "Invitado ";
         $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
         $datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );

         if($datos_invitacion[IDClub] == 44 && !empty($datos_invitado[TipoSangre]))
          $datos_invitacion["TipoInvitacion"] = "Invitado de Evento";

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
         if ($qryString) {
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

             if(!empty($id_funcionario)){
               $condi_func= " or IDUsuario='".$id_funcionario."' ";
             }

             $sql_unica="SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '".$fecha_hoy."' and ( (NumeroDocumento='".$qryString."' or NumeroDocumento='".(int)$qryString."') ".$condi_func." ) Group by IDusuario ";

             $r_unica=$dbo->query($sql_unica);
             $total_unica=$dbo->rows($r_unica);
             $row_resp_diag=$dbo->fetchArray($r_unica);
             $peso_permitido=$dbo->getFields( "Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' " );
             $alerta_diagnostico = "";
              if(SIMUser::get("club") != 70):
                if($total_unica<=0){
                  $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona no ha llenado el diagnostico</b></font>";
                }
                elseif($row_resp_diag["Resultado"]>=$peso_permitido){
                  $alerta_diagnostico="<font color='#F14823' size='4px' ><b> Atención la persona se debe comunicar con salud ocupacional. ". "</b></font><br>";
                }
                else{
                  $alerta_diagnostico="<font color='#059C1C'><b> Diagnostico correcto  </b></font><br>";
                }
              else:
                if($total_unica<=0){
                  $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona no ha aceptado la exoneración de responsabilidades</b></font><br>";
                }
              endif;


             foreach(SIMResources::$EstadoZeus as $id_tipo => $tipo):

              if($id_tipo == $datos_socio["IDEstadoZeus"])
              {
                $alerta_diagnostico .= "<font color='#059C1C'><b> ".$tipo."  </b></font><br>";
              }

            endforeach;
           }


       endif;
     //FIN BUSQUEDA CONTRATISTA

     //BUSQUEDA INVITADOS GENERAL
       if($total_resultados<=0):
         if ($qryString) {
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

           //consultar la fecha mayor de los invitados no residentes
           $documentoInvitadoNoResidente = $datos_invitacion["NumeroDocumento"];
           $sql_fechamayor = "SELECT MAX(FechaIngreso) as maximafecha FROM SocioInvitado WHERE NumeroDocumento = '$documentoInvitadoNoResidente' and IDClub = '".SIMUser::get("club")."'  Limit 1";
           $query = $dbo->query($sql_fechamayor);
           $invitadonoresidente = $dbo->fetchArray($query);
           $datos_noresidentes = $invitadonoresidente;
           $datos_invitacion["FechaFin"] = $datos_noresidentes["maximafecha"];

           if(!empty($datos_invitacion["IDSocioInvitado"])){
             $sql_otros="SELECT IDCampoFormularioInvitado,Valor
                        FROM InvitadosOtrosDatos
                        WHERE IDInvitacion = '".$datos_invitacion["IDSocioInvitado"]."'";
             $r_otros=$dbo->query($sql_otros);
             while($row_otros_datos=$dbo->fetchArray($r_otros)){
               $campo_otro = $dbo->getFields( "CampoFormularioInvitado", "EtiquetaCampo", "IDCampoFormularioInvitado = '" . $row_otros_datos["IDCampoFormularioInvitado"] . "' " );
               $OtrosDatos.="<br>".$campo_otro."=".$row_otros_datos["Valor"];
             }
           }


           if($total_resultados>0){
           //Verifica diagnostico
           $fecha_hoy=date("Y-m-d") . " 00:00:00";
           $sql_unica="SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '".$fecha_hoy."' and (NumeroDocumento='".$qryString."' or NumeroDocumento='".(int)$qryString."') Group by IDusuario ";
           $r_unica=$dbo->query($sql_unica);
           $total_unica=$dbo->rows($r_unica);
           $row_resp_diag=$dbo->fetchArray($r_unica);
           $peso_permitido=$dbo->getFields( "Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' " );
           $alerta_diagnostico = "";
            if(SIMUser::get("club") != 70):
              if($total_unica<=0){
                $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención: la persona no ha llenado el diagnostico</b></font>";
              }
              elseif($row_resp_diag["Resultado"]>=$peso_permitido){
                $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona se debe comunicar con salud ocupacional!  </b></font><br>";
              }
              else{
                $alerta_diagnostico="<font color='#059C1C'><b> Diagnostico correcto  </b></font><br>";
              }
            else:
              if($total_unica<=0){
                $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona no ha aceptado la exoneración de responsabilidades</b></font><br>";
              }
            endif;


           foreach(SIMResources::$EstadoZeus as $id_tipo => $tipo):

            if($id_tipo == $datos_socio["IDEstadoZeus"])
            {
              $alerta_diagnostico .= "<font color='#059C1C'><b> ".$tipo."  </b></font><br>";
            }

          endforeach;
         }


       endif;
     //FIN BUSQUEDA CONTRATISTA

     //BUSQUEDA USUARIO FUNCIONARIO
      //para lagartos y my solo pueden ingresar los que tengan invitacion
      //if($total_resultados<=0 && SIMUser::get("club") != 7):
      if($total_resultados<=0 && SIMUser::get("club") != 70000 &&  SIMUser::get("club") != 9 ):
           $sql_invitacion = "Select * From Usuario Where (NumeroDocumento = '".$qryString."' or NumeroDocumento = '".(int)$qryString."' ) and  Activo = 'S' and IDClub = '".SIMUser::get("club")."'
                              UNION
                              Select U.* From Usuario U,VehiculoUsuario VU Where U.IDUsuario=VU.IDUsuario and VU.Placa = '".$qryString."' and U.Activo  = 'S'  and IDClub = '".SIMUser::get("club")."' ";
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
             $alerta_diagnostico = "";
              if(SIMUser::get("club") != 70):
                if($total_unica<=0){
                  $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona no ha llenado el diagnostico.</b></font><br>";
                }
                elseif($row_resp_diag["Resultado"]>=$peso_permitido){
                  $alerta_diagnostico="<font color='#F14823' size='4px'  ><b> Atención la persona se debe comunicar con salud ocupacional  </b></font><br>";
                }
                else{
                  $alerta_diagnostico="<font color='#059C1C'><b> Diagnostico correcto  </b></font><br>";
                }
              else:
                if($total_unica<=0){
                  $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona no ha aceptado la exoneración de responsabilidades</b></font><br>";
                }
              endif;

              if((strtotime($datos_socio[FechaFinContrato]) < strtotime(date("y-m-d")) && $datos_socio[FechaFinContrato] != "0000-00-00"))
              {
                $alerta_diagnostico .= "<font color='#F14823' size='2px'><b> Atención el empleado tiene el contrato vencido</b></font><br>";
              }

           }

       endif;
     //FIN BUSQUEDA USUARIO FUNCIONARIO




     //BUSQUEDA SOCIO
     if($total_resultados<=0 || $EsSocio=="S"):

      if(SIMUser::get("club") == 70)
          $secuencia = "-00";
        else
          $secuencia = "";

         if (ctype_digit($qryString)) {
             // si es solo numeros en un numero de documento
             $sql_invitacion = "Select * From Socio Where (NumeroDocumento = '".$qryString."' or NumeroDocumento = '".(int)$qryString."' or Accion = '".$qryString."' or Accion = '".$qryString.$secuencia."' or NumeroDerecho = '".$qryString."' or CodigoCarne = '".$qryString."'  ) and IDClub = '".SIMUser::get("club")."' AND IDEstadoSocio <> 2 Order by AccionPadre ASC";
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


         $sql_invitacion = "SELECT *
                            FROM Socio WHERE (Accion = '".$qryString."' or Accion = '".$qryString.$secuencia."'  or NumeroDerecho = '".$qryString."' or CodigoCarne = '".$qryString."' or  Predio like '".$qryString."'or Email = '".$qryString."' or NumeroDocumento = '".$qryString."') and IDClub = '".SIMUser::get("club")."'AND IDEstadoSocio <> 2
                            UNION
                            SELECT S.* FROM Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = '".$qryString.$secuencia."' AND IDEstadoSocio <> 2
                            UNION
                            SELECT S.* FROM Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '".$qryString."' and IDClub = '".SIMUser::get("club")."' AND IDEstadoSocio <> 2
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

            if(SIMUser::get("club") == 44 && $datos_socio["SocioAusente"] == "S")
            {
              if($datos_socio["CantidadAusencias"] >= 30)
                $mesanje_ausente = "<font color='#F14823'><b> El socio es ausente y ya sumo mas de 30 entradas en el año. </b></font><br>";
              else
                $mesanje_ausente = "El socio es ausente y  lleva: ".$datos_socio["CantidadAusencias"]." entradas en el año.";

            }


             //caso especial lagartos no permite a padres o madre mas de 4 veces al mes
             if(SIMUser::get("club")==7 && ($datos_invitacion["IDParentesco"]=="14" || $datos_invitacion["IDParentesco"]=="13" || $datos_socio["IDParentesco"]=="16" || $datos_socio["IDParentesco"]=="17")){
                $TotalIngresos=SIMWebService::valida_cantidad_ingresos(SIMUser::get("club"),$datos_invitacion["IDSocio"]);
                if((int)$TotalIngresos>=(int)$IngresoPermitidoLag){
                  $bloqueado="S";
                  $mensaje_bloqueo="<font color='#F14823'><b> Atención la persona ha ingresado mas de ".$IngresoPermitidoLag." veces </b></font><br>";
                  $datos_invitado["RazonBloqueo"]=$mensaje_bloqueo;
                }
             }
             //Fin Caso especial

              //Caso especial para el club el ricon para tarjetas rojas y negras
              if(SIMUser::get("club")==10 || SIMUser::get("club")==8){
                //var_dump($datos_invitacion["NumeroDocumento"]);
                $tarjetaNegra = $dbo->getFields("ListaNegraApp", "Razon", "NumeroDocumento = '". $datos_invitacion["NumeroDocumento"] ."' and IDClub = '". SIMUser::get("club") ."'");

				$mensaje_bloqueo = $tarjetaNegra["Razon"];

                if(!empty($tarjetaNegra))
                {
                  $bloqueado="S";
                  $mensaje_bloqueo="<font color='#F14823'><b> Atención la persona esta en lista negra: </b></font>$tarjetaNegra<br>";
                  $datos_invitado["RazonBloqueo"]=$mensaje_bloqueo;
                }

                $tarjetaRoja = $dbo->getFields("ListaRojaApp", "Razon", "NumeroDocumento = '". $datos_invitacion["NumeroDocumento"] ."' and IDClub = '". SIMUser::get("club") ."'");
                $mensaje_bloqueo = $tarjetaRoja["Razon"];
                if(!empty($tarjetaRoja))
                {
                  $bloqueado="S";
                  $mensaje_bloqueo="<font color='#F14823'><b> Atención la persona esta en lista roja: </b>$tarjetaRoja</font><br>";
                  $datos_invitado["RazonBloqueo"]=$mensaje_bloqueo;
                }

                $tarjetaAmarilla = $dbo->getFields("ListaAmarillaApp", "Razon", "NumeroDocumento = '". $datos_invitacion["NumeroDocumento"] ."' and IDClub = '". SIMUser::get("club") ."'");
                $mensaje_bloqueo = $tarjetaRoja["Razon"];
                if(!empty($tarjetaAmarilla))
                {
                  $mensaje_alerta="<font color='#F14823'><b> Atención la persona esta en lista amarilla: </b></font>$tarjetaAmarilla<br>";
                  $datos_invitado["RazonBloqueo"]=$mensaje_bloqueo;
                }

              }

              //Si es el club el rincon valida edad de hijos e hijastros
              if(SIMUser::get("club")==10 || SIMUser::get("club")==8){
                $dia_actua = date("Y-M-D");
                $fecha_nacimiento = $datos_invitado["FechaNacimiento"];
                $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
                $edadSocio=$edad_diff->format('%y');

                $IDParentesco = $datos_invitado["IDParentesco"];

                if((($IDParentesco==10 || $IDParentesco==9) && $edadSocio>25)
                    || ($IDParentesco==8 || $IDParentesco==7) && $edadSocio>30){
                  $alerta_edad_beneficiario="<font color='#F14823'><b> No tiene la edad permitida para tipo su de relación familiar</b></font><br>";

                }
              }

              //Verifica diagnostico
              //El club del rincon no hay que validar diagnostico
              if(SIMUser::get("club")!=10 && SIMUser::get("club")!=8){
                $fecha_hoy=date("Y-m-d") . " 00:00:00";
                $sql_unica="SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '".$fecha_hoy."' and IDSocio='".$id_registro."' GROUP BY IDSocio ";
                $r_unica=$dbo->query($sql_unica);
                $total_unica=$dbo->rows($r_unica);
                $row_resp_diag=$dbo->fetchArray($r_unica);
                $peso_permitido=$dbo->getFields( "Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' " );
                $alerta_diagnostico = "";

                if(SIMUser::get("club") != 70):
                  if($total_unica<=0){
                    $alerta_diagnostico="<font color='#F14823'><b> Atención la persona no ha llenado el diagnostico!</b></font><br>";
                  }
                  elseif($row_resp_diag["Resultado"]>=$peso_permitido){
                    $alerta_diagnostico="<font color='#F14823' size='4px'><b> El Socio y su grupo familiar No pueden ingresar  </b></font><br>";
                  }
                  else{
                    $alerta_diagnostico="<font color='#059C1C'><b> Diagnostico correcto  </b></font><br>";
                  }
                else:
                  if($total_unica<=0){
                    $alerta_diagnostico="<font color='#F14823' size='4px'><b> Atención la persona no ha aceptado la exoneración de responsabilidades</b></font><br>";
                  }

                endif;
              }

              //Fin Verifica diagnostico

              foreach(SIMResources::$EstadoZeus as $id_tipo => $tipo):

                if($id_tipo == $datos_socio["IDEstadoZeus"] && SIMUser::get("club") == 70)
                {
                  $alerta_diagnostico .= "<font color='#059C1C'><b> ".$tipo."  </b></font><br>";
                }

              endforeach;

              if(SIMUser::get("club") == 70)
              {
                $estado = $dbo->getFields("EstadoSocio","Nombre","IDEstadoSocio = $datos_socio[IDEstadoSocio]");
                $alerta_diagnostico .= "<font color='#059C1C'><b> Estado Socio: ".$estado."  </b></font><br>";
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
            $resp_como_invitado=SIMWebServiceApp::get_reservas_socio_invitado(SIMUser::get("club"),$datos_socio["IDSocio"],0,"",1);
           }


           if(SIMUser::get("club")==7){
             $OtrosDatos.="<br><b>Observacion:</b>"."=".$datos_socio["ObservacionGeneral"];
           }

           //Consulto grupo Familiar
           if (empty($datos_socio["AccionPadre"]) || $datos_socio["AccionPadre"] == $datos_socio["Accion"] || !empty($datos_socio["AccionPadre"])): // Es Cabeza
             $nucleo_socio = 1;

             //Consulto grupo Familiar
            if(!empty($datos_socio["AccionPadre"])){
              $nucleo_socio = 1;
              $condicion_nucleo = " and (AccionPadre = '".$datos_socio["AccionPadre"]."')";
              $datos_invitacion["CabezaInvitacion"]="S";
              $response_nucleo = array();
              $sql_grupo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras, NumeroDocumento, ObservacionGeneral,TipoSocio,Categoria,Parentesco,FechaNacimiento FROM Socio WHERE IDClub = '".SIMUser::get("club")."' and IDSocio <> '".$datos_socio["IDSocio"]."' and IDEstadoSocio = 1 " . $condicion_nucleo;
              $result_grupo = $dbo->query($sql_grupo);
            }
            else{
              if (empty($datos_socio["AccionPadre"]) || $datos_socio["AccionPadre"] == $datos_socio["Accion"]): // Es Cabeza
                $nucleo_socio = 1;
                $condicion_nucleo = " and (AccionPadre = '".$datos_socio["Accion"]."')";
                $datos_invitacion["CabezaInvitacion"]="S";
                $response_nucleo = array();
                $sql_grupo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras, NumeroDocumento, ObservacionGeneral,TipoSocio,Categoria,Parentesco,FechaNacimiento FROM Socio WHERE IDClub = '".SIMUser::get("club")."' and IDSocio <> '".$datos_socio["IDSocio"]."' and IDEstadoSocio = 1 " . $condicion_nucleo;
                $result_grupo = $dbo->query($sql_grupo);
              endif;
            }

            $valida = "no";
            if($datos_socio[IDEstadoSocio] == 3 && $datos_socio[IDClub] != 70){
              $total_resultados = 0;
              $valida="si";
              $mensajevalida = "EL SOCIO SE ENCUENTRA MOROSO";

              if($datos_socio[IDClub] == 44)
                $mensajevalida = "El estado de derecho está suspendido por mora 90 días";
            }

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

     //PREDIOS SOCIO INVITACIONES

    /*  $sql_predios = "Select IDSocio From SocioInvitadoEspecial Where IDInvitado = '". $datos_invitado['IDInvitado']."' and (FechaInicio >= '".date("Y-m-d")."' or FechaFin >= '".date("Y-m-d")."')   ";

     $result_predios= $dbo->query( $sql_predios); */
 /*    $row_result_predios = $dbo->fetchArray( $result_predios );  */
   


     $view = "views/".$script."/list.php";
   break;

    case "imprimir-carnet":
      $view = "views/".$script."/carnetselector.php";
    break;

   default:
     $view = "views/".$script."/list.php";





 } // End switch



 if( empty( $view ) )
   $view = "views/".$script."/form.php";


?>
