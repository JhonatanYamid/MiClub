 <?php

    $titulo = "Invitado";

    SIMReg::setFromStructure(array(
        "title" => "Autorizaciones ",
        "table" => "SocioAutorizacion",
        "key" => "IDSocioAutorizacion",
        "mod" => "SocioAutorizacion",
    ));

    $script = "autorizaciones";

    //extraemos las variables
    $table = SIMReg::get("table");
    $key = SIMReg::get("key");
    $mod = SIMReg::get("mod");

    require(LIBDIR . "SIMWebServiceAccesos.inc.php");

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

            //Relacion de Campos
            $CedulaAutoriza = $sheet->getCell("A" . $row)->getValue();
            $FechaIngreso = $sheet->getCell("B" . $row)->getFormattedValue();
            $FechaSalida = $sheet->getCell("C" . $row)->getFormattedValue();
            $DocumentoInvitado = $sheet->getCell("D" . $row)->getValue();
            $Nombre = $sheet->getCell("E" . $row)->getValue();
            $Apellido = $sheet->getCell("F" . $row)->getValue();
            $Email = $sheet->getCell("G" . $row)->getValue();
            $Telefono = $sheet->getCell("H" . $row)->getValue();
            $TipoSangre = $sheet->getCell("I" . $row)->getValue();
            $Placa = $sheet->getCell("J" . $row)->getValue();
            $Predio = $sheet->getCell("K" . $row)->getValue();
            $Arl = $sheet->getCell("L" . $row)->getValue();
            $Eps = $sheet->getCell("M" . $row)->getValue();
            $HoraInicio = $sheet->getCell("N" . $row)->getValue();
            $HoraSalida = $sheet->getCell("O" . $row)->getValue();
            if (SIMUser::get('club') == 18) {
                $CheckDias = $sheet->getCell("P" . $row)->getValue();
            }
            $TipoAutorizacion = "Invitacion";

            //if(is_numeric($CedulaAutoriza) && is_numeric($DocumentoInvitado) && !empty($CedulaAutoriza) && !empty($FechaIngreso) && !empty($FechaSalida) && !empty($Nombre) && !empty($Apellido) ){
            if (is_numeric($CedulaAutoriza) && !empty($DocumentoInvitado) && !empty($CedulaAutoriza) && !empty($FechaIngreso) && !empty($FechaSalida) && !empty($Nombre) && !empty($Apellido)) {

                if (strlen($FechaIngreso) == 10 && strlen($FechaSalida) == 10) {

                    //Consulto Socio
                    $sql_socio = "Select *
										  From Socio
										  Where IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $CedulaAutoriza . "'";
                    $result_socio = $dbo->query($sql_socio);

                    if ($dbo->rows($result_socio) > 0) :

                        $row_datos_socio = $dbo->fetchArray($result_socio);
                        //Crear autorizacion
                        $IDUsuario = SIMUser::get("IDUsuario");
                        $respuesta = SIMWebServiceAccesos::set_autorizacion_contratista($IDClub, $row_datos_socio["IDSocio"], $TipoAutorizacion, $FechaIngreso, $FechaSalida, "1", $DocumentoInvitado, $Nombre, $Apellido, $Email, $Placa, "S", $HoraInicio, $HoraSalida, $Observaciones, $IDUsuario, $Telefono, $FechaNacimiento, $TipoSangre, $Predio, $Arl, $Eps, $ObservacionSocio, "", "", "", $CheckDias);
                        print_r($respuesta["message"]);
                        echo "<br><br>";
                        $numregok++;
                    else :
                        echo "<br>" . "La cedula de quien invita no existe en la base: " . $CedulaAutoriza;

                    endif;
                } else {
                    echo "<br>" . "Las fechas tienen un formato invalido: " . $FechaIngreso . " " . $DocumentoInvitado . "Estado: " . $Estado;
                }
            } else {
                echo "<br>" . "El numero de documento debe ser numerico o falta nombre apellido: " . $DocumentoInvitado;
            }

            $cont++;
        } // END While
        fclose($fp);
        return array("Numregs" => $cont, "RegsOK" => $numregok);

        return false;
    }

    //extraemos las variables
    $table = SIMReg::get("table");
    $key = SIMReg::get("key");
    $mod = SIMReg::get("mod");

    //Verificar permisos
    /* SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil")); */

    //creando las notificaciones que llegan en el parametro m de la URL
    SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

    switch (SIMNet::req("action")) {

        case "cargarplano":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

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
    } // End switch

    ?>
