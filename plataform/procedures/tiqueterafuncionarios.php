<?

SIMReg::setFromStructure(array(
    "title" => "TiqueteraFuncionarios",
    "table" => "TiqueteraFuncionarios",
    "key" => "IDTiqueteraFuncionarios",
    "mod" => "Socio"
));


$script = "tiqueterafuncionarios";

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
        
        $NumeroDocumento =  $sheet->getCell("A" . $row)->getValue();
        $IDTaloneraFunc =  $sheet->getCell("B" . $row)->getValue();
        $FechaInicio = $sheet->getCell("C" . $row)->getValue();
        $FechaFin =  utf8_decode($sheet->getCell("D" . $row)->getValue());
        $CantidadEntradas =  utf8_decode($sheet->getCell("E" . $row)->getValue());


        //if(is_numeric($NumeroDocumento) && !empty($NumeroDocumento)){
        if (!empty($NumeroDocumento)) {

            //Consulto Socio
            $sql_socio = "SELECT NumeroDocumento From TiqueteraFuncionarios Where IDClub = '" . $IDClub . "' and NumeroDocumento = '" .  $NumeroDocumento . "' Order By NumeroDocumento DESC Limit 1";


            $result_socio = $dbo->query($sql_socio);

            if ($dbo->rows($result_socio) > 0) :
                //Editar datos Socio

                $sql_edit_socio = "UPDATE TiqueteraFuncionarios Set IDTaloneraFunc = '" . $IDTaloneraFunc . "', FechaInicio = '" . $FechaInicio . "', 	FechaTrEd = '" . date('Y-m-d') . "', FechaFin = '" . $FechaFin . "', CantidadEntradas = '". $CantidadEntradas .   "' Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'";


                //echo "<br>Editar";
                //echo "<br>" . $sql_edit_socio;
                //exit;
                $dbo->query($sql_edit_socio);
                $numregok++;

            else :

                if (!empty($NumeroDocumento)) {
                    //Crear Socio
                    $sql_inserta_socio = "INSERT INTO TiqueteraFuncionarios(IDClub,NumeroDocumento,FechaInicio,FechaFin,CantidadEntradas, IDTaloneraFunc, FechaTrEd) Values ('" . $IDClub . "','" . $NumeroDocumento . "','" . $FechaInicio . "','" . $FechaFin . "','" . $CantidadEntradas . "','"  . $IDTaloneraFunc . "','" . $FechaTrEd . "')";
                    //echo "<br>Crear ";
                    // echo "<br>" . $sql_inserta_socio;

                    $dbo->query($sql_inserta_socio);
                    $numregok++;
                } else {
                    echo "<br>" . "Falta la columna de usuario y clave a:" . $NumeroDocumento;
                }

            endif;
        } else {
            echo "<br>" . "El numero de documento debe ser numerico: " . $NumeroDocumento;
        }


        $cont++;
    } // end for
    fclose($fp);
    return array("Numregs" => $cont, "RegsOK" => $numregok);
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
            $frm['IDUsuario'] = SIMUser::get("IDUsuario");
            $frm['FechaRegistro'] = date('Y-m-d');

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
            $id = $dbo->insert($frm, "CambiosTiqueteraFuncionario", $key);
            $frm = $dbo->fetchById($table, $key, $id, "array");


            //insertamos los datos
           
           
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;

    case "cargarplano":
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
