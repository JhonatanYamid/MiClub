<?

SIMReg::setFromStructure(array(
  "title" => "GrupodeSocios",
  "table" => "GrupoReconocimiento",
  "key" => "IDGrupoReconocimiento",
  "mod" => "GrupoReconocimiento"
));


$script = "gruporeconocimiento";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);


function copiar_archivo(&$frm, $file)
{
  $filedir = SOCIOPLANO_DIR;
  $nuevo_nombre = rand(0, 1000000) . "_" . date("Y-m-d") . "_" . $file['file']['name'];

  if (copy($file['file']['tmp_name'], "$filedir/" . $nuevo_nombre)) {
    //echo "File : ".$file['file']['name']."... ";
    //echo "Size :".$file['file']['size']." Bytes ... ";
    //echo "Status : Transfer Ok ...<br>";
    return $nuevo_nombre;
  } else {
    return "error";
  }
}


function get_data_accion($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $IDGrupoReconocimiento)
{

  $dbo = &SIMDB::get();

  $numregok = 0;

  if (!empty($field))
    $strfields = "(" . implode(",", $field) . ")";

  if ($fp = fopen($file, "r")) {
    $cont = 0;
    ini_set('auto_detect_line_endings', true);
    if ($IGNORE_FIRTS_ROW)
      $row = fgets($fp, 4096);

    while (!feof($fp)) {


      $row = fgets($fp, 4096);

      //Relacion de Campos
      $Accion = trim($row);


      if (!empty($Accion)) {



        //Consulto Que exista el socio
        $sql_socio = "Select IDSocio
                     From Socio
                     Where IDClub = '" . $IDClub . "' and (Accion= '" . $Accion . "' or NumeroDocumento = '" . $Accion . "')";

        $result_socio = $dbo->query($sql_socio);

        if ($dbo->rows($result_socio) > 0) :
          $datos_socio = $dbo->fetchArray($result_socio);
          $array_id_socio[] = $datos_socio["IDSocio"];
          $numregok++;

        else :
          $array_socio_no_existe[] = "El siguiente numero de accion no existe: " . $Accion;
          $numregfail++;
        endif;
      } else {
        echo "<br>" . "El numero de accion esta equivocado: " . $Accion;
      }

      $cont++;
    } // END While
    fclose($fp);


    $array_resultado["Exitosos"] = $numregok;
    $array_resultado["NoExitosos"] = $numregfail;
    $array_resultado["ReporteNoExitoso"] = $array_socio_no_existe;

    if (count($array_id_socio) > 0) :
      $id_socios = implode("|||", $array_id_socio);
      $sql_grupo = "Update GrupoSocio Set IDAsociado = '" . $id_socios . "' Where IDGrupoReconocimiento = '" . $IDGrupoReconocimiento . "'";
      $dbo->query($sql_grupo);
    endif;

    return $array_resultado;
  } else
    echo "error open $file";
}


switch (SIMNet::req("action")) {

  case "add":
    $view = "views/" . $script . "/form.php";
    $newmode = "insert";
    $titulo_accion = "Crear";
    break;

  case "insert":

    if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
      //los campos al final de las tablas
      $frm = SIMUtil::varsLOG($_POST);
      //insertamos los datos
      $id = $dbo->insert($frm, $table, $key);

      $invitados = explode("|||", $frm["InvitadoSeleccion"]);
      if (count($invitados) > 0) :
        foreach ($invitados as $nom_invitado) :
          $array_datos = explode("-", $nom_invitado);
          if ($array_datos[0] == "socio") : // socio club
            $insert_persona = "INSERT INTO GrupoReconocimientoSocio (IDGrupoReconocimiento,IDSocio) VALUES ('" . $id . "','" . $array_datos[1] . "')";
            $dbo->query($insert_persona);
          endif;
        endforeach;
      endif;

      if (count($datos_invitado) > 0) :
        $frm["IDAsociado"] = implode("|||", $datos_invitado);
      endif;



      SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
      SIMHTML::jsRedirect($script . ".php");
    } else
      exit;

    break;


  case "edit":
    $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
    $view = "views/" . $script . "/form.php";
    $newmode = "update";
    $titulo_accion = "Actualizar";




    break;

  case "update":

    if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
      //los campos al final de las tablas
      $frm = SIMUtil::varsLOG($_POST);
      $sql_persona_grupo = "SELECT IDSocio From GrupoReconocimientoSocio WHERE IDGrupoReconocimiento = '" . SIMNet::reqInt("id") . "'";
      $r_persona_grupo = $dbo->query($sql_persona_grupo);
      while ($row_persona = $dbo->fetchArray($r_persona_grupo)) {
        $array_persona[] = $row_persona["IDSocio"];
      }
      $array_personas_final = array();
      $invitados = explode("|||", $frm["InvitadoSeleccion"]);
      if (count($invitados) > 0) :
        foreach ($invitados as $nom_invitado) :
          $array_datos = explode("-", $nom_invitado);
          $array_personas_final[] = $array_datos[1];
          if ($array_datos[0] == "socio") : // socio club
            //verifico si existe
            if (!in_array($array_datos[1], $array_persona)) {
              $insert_persona = "INSERT INTO GrupoReconocimientoSocio (IDGrupoReconocimiento,IDSocio) VALUES ('" . SIMNet::reqInt("id") . "','" . $array_datos[1] . "')";
              $dbo->query($insert_persona);
            }
          endif;
        endforeach;
      endif;
      //Borro los que no pertenecen al grupo
      foreach ($array_persona as $id_persona) {
        if (!in_array($id_persona, $array_personas_final)) {
          $sql_borra = "DELETE FROM GrupoReconocimientoSocio Where IDGrupoReconocimiento = '" . SIMNet::reqInt("id") . "' and IDSocio = '" . $id_persona . "'";
          $dbo->query($sql_borra);
        }
      }

      if (count($datos_invitado) > 0) :
        $frm["IDAsociado"] = implode("|||", $datos_invitado);
      endif;


      $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

      $frm = $dbo->fetchById($table, $key, $id, "array");


      //Archivo

      if (!empty($_FILES["file"]["name"])) :

        $time_start = SIMUtil::getmicrotime();
        $nombre_archivo = copiar_archivo($_POST, $_FILES);
        if ($nombre_archivo == "error") :
          echo "Error Transfiriendo Archivo";
          exit;
        endif;

        $array_resultado = get_data_accion($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], SIMNet::reqInt("id"));
        if ((int)$array_resultado["NoExitosos"] > 0) :
          $mensaje_carga = "<br>" . SIMUtil::get_traduccion('', '', 'Registrosexitosos', LANGSESSION) . ":" . $array_resultado["Exitosos"];
          $mensaje_carga .= "<br>" . SIMUtil::get_traduccion('', '', 'Nosepuedoingresar', LANGSESSION) . ":" . $array_resultado["NoExitosos"];
          if (count($array_resultado["ReporteNoExitoso"]) > 0) :
            foreach ($array_resultado["ReporteNoExitoso"] as $mensaje) :
              $mensaje_carga .= $mensaje;
            endforeach;
          endif;

          $time_end = SIMUtil::getmicrotime();
          $time = $time_end - $time_start;
          $time = number_format($time, 3);
        endif;
      endif;



      if ((int)$array_resultado["NoExitosos"] <= 0) :
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
      else :
        SIMHTML::jsAlert($mensaje_carga);
      endif;
      SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
    } else
      exit;

    break;

  case "search":
    $view = "views/" . $script . "/list.php";
    break;

  case "delfoto":
    $foto = $_GET['foto'];
    $campo = $_GET['campo'];
    $id = $_GET['id'];
    $filedelete = SERVICIO_DIR . $foto;
    unlink($filedelete);
    $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
    SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
    SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
    break;


  case "delfoto":
    $foto = $_GET['foto'];
    $campo = $_GET['campo'];
    $id = $_GET['id'];
    $filedelete = BANNERAPP_DIR . $foto;
    unlink($filedelete);
    $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
    SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
    SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
    break;



  default:
    $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
  $view = "views/" . $script . "/form.php";
