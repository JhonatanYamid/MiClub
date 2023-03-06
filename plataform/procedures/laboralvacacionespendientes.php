<?

SIMReg::setFromStructure(array(
    "title" => "Laboralvacacionespendientes",
    "table" => "LaboralVacacionesPendientes",
    "key" => "IDLaboralVacacionesPendientes",
    "mod" => "Socio"
));


$script = "laboralvacacionespendientes";

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
        echo "File : " . $file['file']['name'] . "... ";
        echo "Size :" . $file['file']['size'] . " Bytes ... ";
        echo "Status : Transfer Ok ...<br>";
        return $nuevo_nombre;
    } else {
        echo "error";
    }
}

function get_data($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub)
{

    $dbo = &SIMDB::get();
    $HoyFechaHora = date("Y-m-d H:i:s");
    $numregok = 0;
    require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
    $archivo = $file;
    $inputFileType = PHPExcel_IOFactory::identify($archivo);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($archivo);
    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();

    $highestColumn = $sheet->getHighestColumn();
    for ($row = 2; $row <= $highestRow; $row++) {
        $Cedula =  $sheet->getCell("A" . $row)->getValue();
        $Periodo =  $sheet->getCell("B" . $row)->getValue();
        $Dias = $sheet->getCell("C" . $row)->getValue();
        $DiasCompensatorio =  utf8_decode($sheet->getCell("D" . $row)->getValue());
        $SabadoLaboral =  utf8_decode($sheet->getCell("E" . $row)->getValue());
        $IDSocio = 0;
        $IDUsuario = 0;

        //if(is_numeric($NumeroDocumento) && !empty($NumeroDocumento)){
        if (!empty($Cedula)) {
            if ($IDClub == 20) {
                $IDUsuario = $dbo->getFields("Usuario", "IDUsuario", "NumeroDocumento='" . $Cedula . "'");
            } else {
                $IDUsuario = $dbo->getFields("Usuario", "IDUsuario", "NumeroDocumento='" . $Cedula . "'");
                $IDSocio = $dbo->getFields("Socio", "IDSocio", "NumeroDocumento='" . $Cedula . "'");
            }

            if ($IDUsuario > 0) {
                //Consulto Usuario
                $sql_socio = "SELECT IDUsuario
                From LaboralVacacionesPendientes
                Where IDClub = '" . $IDClub . "' and IDUsuario = '" . $IDUsuario . "' Limit 1";
            } else  if ($IDSocio > 0) {
                //Consulto Socio
                $sql_socio = "SELECT IDSocio
                     From LaboralVacacionesPendientes
                    Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' Limit 1";
            }




            $result_socio = $dbo->query($sql_socio);

            if ($dbo->rows($result_socio) > 0  && ($IDSocio > 0 || $IDUsuario > 0)) :

                if ($IDSocio > 0) {
                    //Editar datos Socio
                    $sql_edit_socio = "UPDATE LaboralVacacionesPendientes Set Periodo = '" . $Periodo . "', Dias = '" . $Dias . "', DiasCompensatorio = '" . $DiasCompensatorio . "', SabadoLaboral ='" . $SabadoLaboral . "' ,UsuarioTrCr = '" . SIMUser::get("Nombre") . "', FechaTrCr = '" . $HoyFechaHora . "'" .   " Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                } else if ($IDUsuario > 0) {

                    //Editar datos Usuario
                    $sql_edit_socio = "UPDATE LaboralVacacionesPendientes Set Periodo = '" . $Periodo . "', Dias = '" . $Dias . "', DiasCompensatorio = '" . $DiasCompensatorio . "', SabadoLaboral ='" . $SabadoLaboral . "', UsuarioTrCr = '" . SIMUser::get("Nombre") . "', FechaTrCr = '" . $HoyFechaHora . "'" .   " Where IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'";
                }

                //echo "<br>Editar";
                // echo "<br>" . $sql_edit_socio;
                //exit;
                $dbo->query($sql_edit_socio);
                $numregok++;

            else :



                if (!empty($Cedula) && ($IDSocio > 0 || $IDUsuario > 0)) {
                    //los campos al final de las tablas
                    /*    $frm = SIMUtil::varsLOG($_POST);
                    $UsuarioTrCr = $frm["UsuarioTrCr"]; */


                    //Crear laboral vacaciones pendientes socio
                    if ($IDSocio > 0) {
                        $sql_inserta_socio = "INSERT INTO LaboralVacacionesPendientes(IDClub,IDSocio,Periodo,Dias,DiasCompensatorio,SabadoLaboral,UsuarioTrCr,FechaTrCr)
                        Values ('" . $IDClub . "','" . $IDSocio . "','" . $Periodo . "','" . $Dias . "','" . $DiasCompensatorio . "','$SabadoLaboral','" . SIMUser::get("Nombre") . "','" . $HoyFechaHora . "')";
                    } else
                        //Crear laboral vacaciones pendientes usario
                        if ($IDUsuario > 0) {
                            $sql_inserta_socio = "INSERT INTO LaboralVacacionesPendientes(IDClub,IDUsuario,Periodo,Dias,DiasCompensatorio,SabadoLaboral,UsuarioTrCr,FechaTrCr)
                        Values ('" . $IDClub . "','" . $IDUsuario . "','" . $Periodo . "','" . $Dias . "','" . $DiasCompensatorio . "','$SabadoLaboral','" . SIMUser::get("Nombre") .  "','" . $HoyFechaHora . "')";
                        }

                    //echo "<br>Crear ";
                    // echo "<br>" . $sql_inserta_socio;

                    $dbo->query($sql_inserta_socio);
                    $numregok++;
                } else {
                    echo "<br>" . "Cedula no existe o el campo esta vacio :" . $Cedula;
                }

            endif;
        } else {
            echo "<br>" . "El numero de documento debe ser numerico: " . $Cedula;
        }


        $cont++;
    } // end for
    fclose($fp);
    return array("Numregs" => $cont, "RegsOK" => $numregok, "NoRegistros" => $contNoRegistro);
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

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . $id);

            //SIMHTML::jsRedirect($script . ".php");
            // SIMHTML::jsRedirect($script . ".php");
            //+ print_r($frm)
        } else


            exit;

        break;


    case "edit":

        $id = $frm["IDEntreLomasCenso"];
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":
        /*   print_r($_POST);
        exit;*/

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

    case "cargamasivalaboralvacacionespendientes":
        /*   print_r($_POST);
        exit; */
        $time_start = SIMUtil::getmicrotime();
        $nombre_archivo = copiar_archivo($_POST, $_FILES);
        if ($nombre_archivo == "error") :
            echo "Error Transfiriendo Archivo";
            exit;
        endif;

        if ((int)$_POST["IDClub"] <= 0) {
            echo "Debe seleccionar un club";
            exit;
        }



        $result = get_data($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub']);
        if ($result["Numregs"] > 0) {
            echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
        } // if($result["Numregs"] > 0){


        $time_end = SIMUtil::getmicrotime();
        $time = $time_end - $time_start;
        $time = number_format($time, 3);
        SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
        exit;
        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
