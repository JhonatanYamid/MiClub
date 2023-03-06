<?

SIMReg::setFromStructure(array(
  "title" => "familiaresvacunacion",
  "table" => "FamiliaresVacunacion",
  "key" => "IDFamiliaresvacunacion",
  "mod" => "FamiliaresVacunacion"
));


$script = "familiaresvacunacion";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

function calculaedad($fechanacimiento)
{
  list($ano, $mes, $dia) = explode("-", $fechanacimiento);
  $ano_diferencia  = date("Y") - $ano;
  $mes_diferencia = date("m") - $mes;
  $dia_diferencia   = date("d") - $dia;
  if ($dia_diferencia < 0 || $mes_diferencia < 0)
    $ano_diferencia--;
  return $ano_diferencia;
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

      //Validar los datos
      for ($i <= 1; $i <= 5; $i++) {

        if ($i == 1)
          $indice_validar = "";

        if ($frm["Parentesco" . $indice_validar] == "Conyuge") {
          $total_conyugue++;
        }
        $inserta = "N";
        if (empty($frm["TipoDocumento" . $indice_validar]) || empty($frm["NumeroDocumento" . $indice_validar])) {
          $frm["TipoDocumento" . $indice_validar] = "";
          $frm["NumeroDocumento" . $indice_validar] = "";
          $frm["Nombres" . $indice_validar] = "";
          $frm["Apellidos" . $indice_validar] = "";
          $frm["FechaDeNacimiento" . $indice_validar] = "";
          $frm["Edad" . $indice_validar] = "";
          $frm["Eps" . $indice_validar] = "";
          $frm["Parentesco" . $indice_validar] = "";
          $frm["CiudadDondeReside" . $indice_validar] = "";
          $frm["CorreoElectronico" . $indice_validar] = "";
          $frm["Celular" . $i] = "";
          $inserta = "N";
          $error = "Los datos no estan completos";
        } else {
          if (
            empty($frm["TipoDocumento" . $indice_validar]) || empty($frm["NumeroDocumento" . $indice_validar])
            || empty($frm["Nombres" . $indice_validar]) || empty($frm["Apellidos" . $indice_validar])
            || empty($frm["FechaDeNacimiento" . $indice_validar]) || empty($frm["Eps" . $indice_validar])
            || empty($frm["Parentesco" . $indice_validar]) || empty($frm["CiudadDondeReside" . $indice_validar])
            || empty($frm["CorreoElectronico" . $indice_validar]) || empty($frm["Celular" . $indice_validar])
          ) {
            $error = "Error: Debe completar todos los datos";
          } else {
            $inserta = "S";
            $edad_persona = calculaedad($frm["FechaDeNacimiento" . $indice_validar]);
            $frm["Edad" . $indice_validar] = $edad_persona;
          }
          //Calculo edad

        }
        /*
              if($total_conyugue>1){
                $error="Error: Solo puede registrar 1 Conyuge";
              }*/
      }



      //insertamos los datos
      if ($inserta == "S") {
        $id = $dbo->insert($frm, $table, $key);
        $mensaje_resultado = "Registro guardado correctamente";
      }
      /* else{
              SIMHTML::jsAlert($error);
            }*/
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


      $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

      $frm = $dbo->fetchById($table, $key, $id, "array");

      SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
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
  default:
    $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
  $view = "views/" . $script . "/form.php";
