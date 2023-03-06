 <?php
    include(LIBDIR . 'SIMWebServiceVacunacion.inc.php');
    include(LIBDIR . 'SIMWebServiceUsuarios.inc.php');

    $titulo = $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];

    SIMReg::setFromStructure(array(
        "title" => $titulo,
        "table" => "Socio",
        "key" => "IDSocio",
        "mod" => "Socio",
    ));

    function guardar_log_cambio_datos($Tabla, $key, $valor_id, $frm, $IDClub, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
        $datos_campos = $dbo->fieldsOf($Tabla);
        $datos_originales = $dbo->fetchAll($Tabla, " " . $key . " = '" . $valor_id . "' ", "array");
        $array_excluir_campos = array("UsuarioTrCr", "FechaTrCr", "UsuarioTrEd", "FechaTrEd");
        if (count($datos_originales) > 0) :
            foreach ($datos_campos as $nombre_campo) :
                if ($frm[$nombre_campo["Field"]] != $datos_originales[$nombre_campo["Field"]] && $nombre_campo["Field"] != $key && array_key_exists($nombre_campo["Field"], $frm) && !in_array($nombre_campo["Field"], $array_excluir_campos)) :
                    //cuando es diferente insert
                    $sql_log_cambio = "INSERT Into LogCambioDatos (IDClub, Tabla, ValorID, Campo, NuevoDato, IDUsuario, NombreUsuario, Fecha)
												   Values ('" . $IDClub . "','" . $Tabla . "', '" . $valor_id . "','" . $nombre_campo["Field"] . "', '" . $frm[$nombre_campo["Field"]] . "','" . $IDUsuario . "','" . $datos_usuario["Nombre"] . "',NOW())";
                    $dbo->query($sql_log_cambio);
                endif;
            endforeach;
        endif;
    }

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

        $sql_socios = "SELECT IDSocio,NumeroDocumento,Accion FROM Socio WHERE IDClub = '" . $IDClub . "'";
        $r_socios = $dbo->query($sql_socios);
        while ($row_socios = $dbo->fetchArray($r_socios)) {
            $array_socios[$row_socios["NumeroDocumento"]] = $row_socios["IDSocio"];
        }

        $sql_paren = "SELECT IDParentesco,Nombre FROM Parentesco WHERE Publicar='S' ORDER BY IDParentesco ASC";
        $r_paren = $dbo->query($sql_paren);
        while ($row_paren = $dbo->fetchArray($r_paren)) {
            $array_paren[strtoupper($row_paren["Nombre"])] = $row_paren["IDParentesco"];
        }

        $sql_tiposocio = "SELECT * FROM ClubTipoSocio WHERE IDClub = $IDClub";
        $r_tiposocio = $dbo->query($sql_tiposocio);
        while ($row_tiposocio = $dbo->fetchArray($r_tiposocio)) {
            $sqlTipo = "SELECT * FROM TipoSocio WHERE IDTipoSocio = $row_tiposocio[IDTipoSocio]";
            $qryTipo = $dbo->query($sqlTipo);
            $datosTipo = $dbo->fetchArray($qryTipo);

            $array_tipo[strtoupper($datosTipo["Nombre"])] = $datosTipo["Nombre"];
        }

        if ($IDClub == 44) {
            $Update = "UPDATE Socio SET IDEstadoSocio = 2 WHERE IDClub = 44 AND TipoSocio <> 'Invitado' AND TipoSocio <> 'Niñera' AND TipoSocio <> 'Canje'";
            $qry = $dbo->query($Update);
        }

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
            $Accion = $sheet->getCell("A" . $row)->getValue();
            $AccionPadre = $sheet->getCell("B" . $row)->getValue();
            $NumeroDocumento = $sheet->getCell("C" . $row)->getValue();
            $Nombre = $sheet->getCell("D" . $row)->getValue();
            $Apellido = $sheet->getCell("E" . $row)->getValue();
            $Email = $sheet->getCell("F" . $row)->getValue();
            $Telefono = $sheet->getCell("G" . $row)->getValue();
            $Direccion = $sheet->getCell("H" . $row)->getValue();
            $Parentesco = $sheet->getCell("I" . $row)->getValue();
            $FechaNacimiento = $sheet->getCell("J" . $row)->getFormattedValue();
            $Lote = $sheet->getCell("K" . $row)->getValue();
            $Invitaciones = $sheet->getCell("L" . $row)->getValue();
            $Accesos = $sheet->getCell("M" . $row)->getValue();
            $PermiteReservar = $sheet->getCell("N" . $row)->getValue();
            $Estado = $sheet->getCell("O" . $row)->getValue();
            $UsuarioApp = $sheet->getCell("P" . $row)->getValue();
            $ClaveApp = $sheet->getCell("Q" . $row)->getValue();
            $Categoria = $sheet->getCell("R" . $row)->getValue();
            $Celular = $sheet->getCell("S" . $row)->getValue();
            $TipoSocio = $sheet->getCell("T" . $row)->getValue();
            $Ausente = $sheet->getCell("U" . $row)->getValue();
            $TipoAusencia = $sheet->getCell("V" . $row)->getValue();
            $CantidadAusencias = $sheet->getCell("W" . $row)->getValue();
            $FechaInicioAusencia = $sheet->getCell("X" . $row)->getValue();
            $CodigoCarne = $sheet->getCell("Y" . $row)->getValue();
            $FechaInicioCanje = $sheet->getCell("Z" . $row)->getFormattedValue();
            $FechaFinCanje = $sheet->getCell("AA" . $row)->getFormattedValue();
            $ObservacionesGenerales = $sheet->getCell("AB" . $row)->getValue();
            $CodigoDocumento = $sheet->getCell("AC" . $row)->getValue();
            //quitar espacios del documento y el email
            $NumeroDocumento = trim($NumeroDocumento);
            $Email = trim($Email);

            if (!empty($CantidadAusencias)) :
                $update = ", CantidadAusencias = '$CantidadAusencias'";
            endif;

            $pos = strpos($FechaNacimiento, "/");
            if ($pos === false) {
                //echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
            } else {
                $array_nueva_fecha = explode("/", $FechaNacimiento);
                $FechaNacimiento = $array_nueva_fecha[2] . "-" . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
            }

            if (!empty($FechaInicioAusencia)) {
                $pos1 = strpos($FechaInicioAusencia, "/");

                if ($pos1 === false) {
                    //echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
                } else {
                    $array_nueva_fecha1 = explode("/", $FechaInicioAusencia);
                    $FechaInicioAusencia = $array_nueva_fecha1[2] . "-" . $array_nueva_fecha1["0"] . "-" . $array_nueva_fecha1["1"];
                }
            }

            if (!empty($Categoria)) {
                $IDCategoria = $dbo->getFields("Categoria", "IDCategoria", "Nombre = '" . $Categoria . "'");
                $actualiza_categoria = ", IDCategoria = '" . $IDCategoria . "'";
            }

            //para Liga Tenis
            if ($IDClub == 28) :
                $IDCategoria = 0;
                $Lote = ucwords(strtolower($Lote));
                switch ($Lote):
                    case "Parque Nacional":
                        $IDCategoria = 46;
                        break;
                    case "Salitre":
                        $IDCategoria = 45;
                        break;
                    case "Campin":
                        $IDCategoria = 44;
                        break;
                endswitch;
                $actualiza_categoria = ", IDCategoria = '" . $IDCategoria . "'";
            endif;

            if ($Parentesco == "Canje") {
                $TipoSocio = "Canje";
            }

            $IDParentesco = $array_paren[strtoupper($Parentesco)];


            $TipoSocio = $array_tipo[strtoupper($TipoSocio)];

            if (empty($TipoSocio)) {
                $TipoSocio = "Socio";
            }

            //para Risaralda
            if ($IDClub == 85) {
                $FechaInicioInvitado = date("Y-m-d");
                $FechaFinInvitado = $sheet->getCell("S" . $row)->getFormattedValue();
                /*
            if(strtotime($FechaInicioInvitado)<=strtotime($FechaFinInvitado))
            $PermiteReservar =  "S";
            else
            $PermiteReservar =  "N";
             */

                /*
        $FechaActivacion= $sheet->getCell("S".$row)->getFormattedValue();
        if(!empty($FechaActivacion)){
        $FechaInicioInvitado=$FechaActivacion;
        $array_fecha_ini=explode("-",$FechaActivacion);
        $MesIni=$array_fecha_ini["1"];
        if($MesIni==12){
        $sig_year=$array_fecha_ini[0]+1;
        $FechaFinInvitado=$sig_year."-01-01";
        }
        else{
        $sig_mes=$array_fecha_ini[1]+1;
        $FechaFinInvitado=$array_fecha_ini[0]."-".$sig_mes."-01";
        }
        }
         */
            }

            //if(is_numeric($NumeroDocumento) && !empty($NumeroDocumento)){

            if (!empty($NumeroDocumento)) {

                if (
                    strtoupper(trim($Estado)) == "A" || strtoupper(trim($Estado)) == "I" || strtoupper(trim($Estado)) == "MSA" ||
                    strtoupper(trim($Estado)) == "MCA" || strtoupper(trim($Estado)) == "ASC" ||
                    strtoupper(trim($Estado)) == "ACTIVO" || strtoupper(trim($Estado)) == "INACTIVO" ||
                    strtoupper(trim($Estado)) == "MOROSO SIN ACCESO AL APP" || strtoupper(trim($Estado)) == "MOROSO CON ACCESO APP" || strtoupper(trim($Estado)) == "ACTIVO SIN CARGO A SOCIO" ||
                    strtoupper(trim($Estado)) == "SPJ" || strtoupper(trim($Estado)) == "SUSPENDIDO POR JUNTA"
                ) {

                    switch (strtoupper($Estado)) {
                        case "A":
                        case "ACTIVO":
                            $IDEstadoSocio = 1;
                            $cerrarSesion = ", SolicitarCierreSesion = 'N'";
                            break;
                        case "I":
                        case "INACTIVO":
                            $IDEstadoSocio = 2;
                            $cerrarSesion = ", SolicitarCierreSesion = 'S'";
                            break;
                        case "MSA":
                        case "MOROSO SIN ACCESO AL APP":
                            $IDEstadoSocio = 3;
                            $cerrarSesion = ", SolicitarCierreSesion = 'S'";
                            break;
                        case "MCA":
                        case "MOROSO CON ACCESO AL APP":
                            $IDEstadoSocio = 4;
                            $cerrarSesion = ", SolicitarCierreSesion = 'N'";
                            break;
                        case "ASC":
                        case "ACTIVO SIN CARGO A SOCIO":
                            $IDEstadoSocio = 5;
                            $cerrarSesion = ", SolicitarCierreSesion = 'N'";
                            break;

                        case "SPJ":
                        case "SUSPENDIDO POR JUNTA":
                            $IDEstadoSocio = 6;
                            $cerrarSesion = ", SolicitarCierreSesion = 'S'";
                            break;
                    }

                    //Consulto Socio
                    /*
                $sql_socio = "SELECT IDSocio
                From Socio
                Where IDClub = '".$IDClub."' and NumeroDocumento = '".$NumeroDocumento."'";

                $result_socio = $dbo->query($sql_socio);

                if($dbo->rows($result_socio)>0):
                 */

                    $IDSocio = $array_socios[$NumeroDocumento];
                    if ((int) $IDSocio > 0) :
                        //Editar datos Socio

                        $sql_edit_socio = "UPDATE Socio Set Accion = '" . $Accion . "', AccionPadre = '" . $AccionPadre . "', IDParentesco='" . $IDParentesco . "',  Parentesco = '" . $Parentesco . "', Nombre = '" . $Nombre . "', Apellido = '" . $Apellido . "', FechaNacimiento = '" . $FechaNacimiento . "',
										NumeroDocumento = '" . $NumeroDocumento . "', CorreoElectronico = '" . $Email . "', Telefono = '" . $Telefono . "', Direccion='" . $Direccion . "' ,Predio = '" . $Lote . "', IDCategoria = '" . $Categoria . "', UsuarioTrEd = 'Archivo Plano: $nombrearchivo', IDEstadoSocio = '" . $IDEstadoSocio . "',
										NumeroInvitados = '" . $Invitaciones . "',NumeroAccesos = '" . $Accesos . "',PermiteReservar = '" . $PermiteReservar . "', Celular='" . $Celular . "',CodigoDocumento='" . $CodigoDocumento . "',
										TipoSocio='" . $TipoSocio . "', SocioAusente = '$Ausente', TipoAusencia = '$TipoAusencia' $update, FechaInicioAusencia = ' " . $CantidadAusencias . "', FechaTrEd = NOW(),
										FechaInicioInvitado='" . $FechaInicioInvitado . "',FechaFinInvitado='" . $FechaFinInvitado . "', CodigoCarne = '" . $CodigoCarne . "', FechaInicioCanje = '$FechaInicioCanje', FechaFinCanje = '$FechaFinCanje', ObservacionGeneral = '$ObservacionesGenerales' " . $actualiza_categoria . $cerrarSesion . "
										Where IDSocio = '" . $IDSocio . "'";


                        //    if (SIMUser::get("IDPerfil") == 0)

                        /*   echo "UPDATE:" . $sql_edit_socio; */
                        $dbo->query($sql_edit_socio);
                        $numregok++;

                    else :

                        if (!empty($UsuarioApp) && !empty($ClaveApp)) {

                            $ClaveApp = trim($ClaveApp);
                            $UsuarioApp = trim($UsuarioApp);

                            $sql_inserta_socio = "INSERT INTO Socio(IDClub,IDEstadoSocio, IDCategoria, IDParentesco, Accion,AccionPadre, Nombre, Apellido, FechaNacimiento, NumeroDocumento, CorreoElectronico, Email, Clave, Telefono, Celular,Predio,PermiteReservar,NumeroInvitados,NumeroAccesos, TipoSocio, Direccion,FechaInicioInvitado,FechaFinInvitado,UsuarioTrCr, FechaTrCr, SocioAusente, CantidadAusencias, CodigoCarne, FechaInicioCanje, FechaFinCanje, ObservacionGeneral,CodigoDocumento )
                                                    Values ('" . $IDClub . "','" . $IDEstadoSocio . "','" . $IDCategoria . "','" . $IDParentesco . "','" . $Accion . "','" . $AccionPadre . "','" . $Nombre . "','" . $Apellido . "','" . $FechaNacimiento . "','" . $NumeroDocumento . "','" . $Email . "', '" . $UsuarioApp . "',sha1('" . $ClaveApp . "'),'" . $Telefono . "','" . $Celular . "'
                                                    ,'" . $Lote . "','" . $PermiteReservar . "','" . $Invitaciones . "','" . $Accesos . "','" . $TipoSocio . "','" . $Direccion . "','" . $FechaInicioInvitado . "','" . $FechaFinInvitado . "', 'Archivo Plano: " . $nombrearchivo . "',NOW(), '" . $Ausente . "', '" . $CantidadAusencias . "', '" . $CodigoCarne . "', '$FechaInicioCanje', '$FechaFinCanje', '$ObservacionesGenerales','$CodigoDocumento')";

                            //    if (SIMUser::get("IDPerfil") == 0)


                            $dbo->query($sql_inserta_socio);

                            /*  echo "INSERT:" . $sql_inserta_socio;
                            exit; */
                            $numregok++;
                        } else {
                            echo "<br>" . "Falta la columna de usuario y clave a:" . $NumeroDocumento;
                        }

                    endif;
                } else {
                    echo "<br>" . "El Estado  tiene un valor invalido: " . $NumeroDocumento . "Estado: -" . strtoupper($Estado) . "-";
                }
            } else {
                echo "<br>" . "El numero de documento debe ser numerico: " . $NumeroDocumento . " FILA $row";
            }

            $cont++;
        } // end for
        fclose($fp);
        return array("Numregs" => $cont, "RegsOK" => $numregok);
    }

    function get_data_movimiento($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub)
    {

        $dbo = &SIMDB::get();

        $numregok = 0;

        if (!empty($field)) {
            $strfields = "(" . implode(",", $field) . ")";
        }

        if ($fp = fopen($file, "r")) {
            $cont = 0;
            ini_set('auto_detect_line_endings', true);
            if ($IGNORE_FIRTS_ROW) {
                $row = fgets($fp, 4096);
            }

            while (!feof($fp)) {

                $row = fgets($fp, 4096);
                if (!empty($FIELD_TEMINATED)) {
                    if ($FIELD_TEMINATED == "TAB") {
                        $row_data = explode("\t", $row);
                    } else {
                        $row_data = explode($FIELD_TEMINATED, $row);
                    }
                }

                //Relacion de Campos
                $IdentificadorConsumo = (int) $row_data[0];
                $PuntoVenta = utf8_encode($row_data[1]);
                $Producto = utf8_encode($row_data[2]);
                $Cantidad = $row_data[3];
                $ValorProducto = $row_data[4];
                $Fecha = $row_data[5];
                $NumeroFactura = $row_data[6];
                $Propina = $row_data[7];
                $TotalFactura = $row_data[8];
                $Pagador = utf8_encode($row_data[9]);
                $Accion = $row_data[10];

                if (!empty($Accion)) {

                    //Consulto Movimiento
                    $sql_movimiento = "Select *
										  From SocioMovimiento
										  Where IDClub = '" . $IDClub . "' and IdentificadorConsumo = '" . $IdentificadorConsumo . "'";
                    $result_movimiento = $dbo->query($sql_movimiento);

                    if ($dbo->rows($result_movimiento) > 0) :
                        $datos_movimiento = $dbo->fetchArray($result_movimiento);
                        //Editar Movimiento
                        $sql_edit_movimiento = "Update SocioMovimiento Set PuntoVenta = '" . $PuntoVenta . "', Producto = '" . $Producto . "', Cantidad = '" . $Cantidad . "', ValorProducto = '" . $ValorProducto . "', Fecha = '" . $Fecha . "', NumeroFactura = '" . $NumeroFactura . "',
														Propina = '" . $Propina . "', TotalFactura = '" . $TotalFactura . "', Pagador = '" . $TotalFactura . "', Accion = '" . $Accion . "', UsuarioTrEd = 'Archivo Plano: $nombrearchivo',
														FechaTrEd = NOW()
														Where IDClub = '" . $IDClub . "' and IDSocioMovimiento = '" . $datos_movimiento["IDSocioMovimiento"] . "'";
                        $dbo->query($sql_edit_movimiento);
                        $numregok++;

                    else :
                        //Crear Movimiento
                        $sql_movimiento = "Insert Into SocioMovimiento(IDClub,IdentificadorConsumo,PuntoVenta, Producto, Cantidad, ValorProducto, Fecha, NumeroFactura, Propina, TotalFactura, Pagador, Accion, UsuarioTrCr, FechaTrCr)
														  Values ('" . $IDClub . "','" . $IdentificadorConsumo . "','" . $PuntoVenta . "','" . $Producto . "','" . $Cantidad . "','" . $ValorProducto . "','" . $Fecha . "','" . $NumeroFactura . "','" . $Propina . "','" . $TotalFactura . "', '" . $Pagador . "','" . $Accion . "','Archivo Plano: " . $nombrearchivo . "',NOW())";

                        //echo "<br>Crear ";
                        //echo $sql_inserta_socio;
                        //exit;
                        $dbo->query($sql_movimiento);
                        $numregok++;

                    endif;
                } else {
                    echo "<br>" . "El numero de accion esta equivocado: " . $Accion;
                }

                $cont++;
            } // END While
            fclose($fp);
            return array("Numregs" => $cont, "RegsOK" => $numregok);
        } else {
            echo "error open $file";
        }

        return false;
    }

    function get_data_movimiento_extracto($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $BorrarInfoAntigua)
    {

        $dbo = &SIMDB::get();

        $numregok = 0;

        if ($BorrarInfoAntigua == "S") :
            $sql_borra_movimiento = "Delete From SocioExtracto
											  Where IDClub = '" . $IDClub . "'";
            $dbo->query($sql_borra_movimiento);
        endif;

        if ($fp = fopen($file, "r")) {
            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
            $archivo = $file;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            for ($row = 2; $row <= $highestRow; $row++) {
                $Accion = $sheet->getCell("A" . $row)->getValue();
                $Valor = $sheet->getCell("B" . $row)->getValue();
                $Fecha = $sheet->getCell("C" . $row)->getValue();

                if (!empty($Accion)) {
                    //Consulto Movimiento
                    $sql_movimiento = "Select *
										  From SocioExtracto
										  Where IDClub = '" . $IDClub . "' and Accion= '" . $Accion . "' and Fecha = '" . $Fecha . "'";
                    $result_movimiento = $dbo->query($sql_movimiento);

                    if ($dbo->rows($result_movimiento) > 0) :
                        $datos_movimiento = $dbo->fetchArray($result_movimiento);
                        //Editar Movimiento
                        $sql_edit_movimiento = "Update SocioExtracto Set Accion = '" . $Accion . "', Valor = '" . $Valor . "', Fecha = '" . $Fecha . "', UsuarioTrEd = '" . $nombrearchivo . "', FechaTrEd=NOW()
														    Where IDSocioExtracto = '" . $datos_movimiento["IDSocioExtracto"] . "'";
                        $dbo->query($sql_edit_movimiento);
                        $numregok++;

                    else :
                        //Crear Movimiento
                        $sql_movimiento = "Insert Into SocioExtracto(IDClub,Accion,Valor, Fecha, UsuarioTrCr, FechaTrCr)
														  Values ('" . $IDClub . "','" . $Accion . "','" . $Valor . "','" . $Fecha . "','Archivo Plano: " . $nombrearchivo . "',NOW())";
                        $dbo->query($sql_movimiento);
                        $numregok++;
                    endif;
                } else {
                    echo "<br>" . "El numero de accion esta equivocado: " . $Accion;
                }

                $cont++;
            } // END for
            fclose($fp);
            return array("Numregs" => $cont, "RegsOK" => $numregok);
        } else {
            echo "error open $file";
        }

        return false;
    }

    function get_data_movimiento_puntos($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $BorrarInfoAntigua)
    {

        $dbo = &SIMDB::get();

        if ($BorrarInfoAntigua == "S") :
            $sql_borra_movimiento = "Delete From SocioPuntosArrayanes
											  Where IDClub = '" . $IDClub . "'";
            $dbo->query($sql_borra_movimiento);
        endif;

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
            $FechaDesde = $sheet->getCell("A" . $row)->getValue();
            $FechaHasta = $sheet->getCell("B" . $row)->getValue();
            $Accion = $sheet->getCell("C" . $row)->getValue();
            $Apellido = $sheet->getCell("D" . $row)->getValue();
            $Nombre = $sheet->getCell("E" . $row)->getValue();
            $InscripcionCampana = $sheet->getCell("F" . $row)->getValue();
            $Visitas = $sheet->getCell("G" . $row)->getValue();
            $ConsumoRest = $sheet->getCell("H" . $row)->getValue();
            $EscuelaTall = $sheet->getCell("I" . $row)->getValue();
            $InscripcionTorneo = $sheet->getCell("J" . $row)->getValue();
            $EventoSocial = $sheet->getCell("K" . $row)->getValue();
            $TotalPunto = $sheet->getCell("L" . $row)->getValue();
            $Imagen = $sheet->getCell("M" . $row)->getValue();

            if (!empty($FechaDesde) && !empty($FechaHasta) && !empty($Accion) && !empty($TotalPunto)) {

                if (strlen($FechaDesde) == 10 && strlen($FechaHasta) == 10) {

                    //Consulto Socio
                    $sql_socio = "Select *
												  From Socio
												  Where IDClub = '" . $IDClub . "' and Accion = '" . $Accion . "'";
                    $result_socio = $dbo->query($sql_socio);

                    if ($dbo->rows($result_socio) > 0) :
                        $row_socio = $dbo->FetchArray($result_socio);
                        $sql_inserta_punto = "INSERT INTO SocioPuntosArrayanes (IDClub, IDSocio, FechaDesde, FechaHasta, Accion, Apellido, Nombre, InscripcionCampana, Visita, RestauranteDelicatessen, EscuelaTaller, InscripcionTorneo, EventoSocial, TotalPuntos, Imagen, UsuarioTrCr, FechaTrCr)
	                                          VALUES ($IDClub,'" . $row_socio["IDSocio"] . "','" . $FechaDesde . "', '" . $FechaHasta . "', '" . $Accion . "', '" . $Apellido . "', '" . $Nombre . "', '" . $InscripcionCampana . "', '" . $Visitas . "','" . $ConsumoRest . "', '" . $EscuelaTall . "', '" . $InscripcionTorneo . "', '" . $EventoSocial . "',
	                                            '" . $TotalPunto . "', '" . $Imagen . "', '" . $nombrearchivo . "', NOW())";
                        $dbo->query($sql_inserta_punto);
                        $numregok++;
                    else :
                        echo "<br>" . "La membresia no existe en la base: " . $Accion;

                    endif;
                } else {
                    echo "<br>" . "Las fechas tienen un formato invalido: " . $DocumentoInvitado . "Estado: " . $Estado;
                }
            } else {
                echo "<br>" . "Faltan parametros: " . $DocumentoInvitado;
            }
        }

        echo "<br>Carga Archivo";
        exit;

        return false;
    }

    function get_data_movimiento_cuenta($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $BorrarInfoAntigua = "")
    {

        $dbo = &SIMDB::get();
        $numregok = 0;

        if ($BorrarInfoAntigua == "S") :
            $sql_borra_movimiento = "Delete From SocioMovimientoCuenta
											  Where IDClub = '" . $IDClub . "'";

            $dbo->query($sql_borra_movimiento);
        endif;

        require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
        $archivo = $file;
        $inputFileType = PHPExcel_IOFactory::identify($archivo);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($archivo);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $borra_anterior = "S";
        for ($row = 2; $row <= $highestRow; $row++) {
            $fecha_invalida = 1;
            $Codigo = $sheet->getCell("A" . $row)->getValue();
            $Nombre = $sheet->getCell("B" . $row)->getValue();
            $Nit = $sheet->getCell("C" . $row)->getValue();
            $Fecha = $sheet->getCell("D" . $row)->getFormattedValue();
            $array_fecha = explode("/", $Fecha);
            if (strlen($array_fecha[0]) == 1) {
                $array_fecha[0] = "0" . $array_fecha[0];
            }
            if (strlen($array_fecha[1]) == 1) {
                $array_fecha[1] = "0" . $array_fecha[1];
            }
            if (checkdate($array_fecha[0], $array_fecha[1], $array_fecha[2])) {
                $fecha_invalida = 0;
            }

            $Fecha = $array_fecha[2] . "-" . $array_fecha[0] . "-" . $array_fecha["1"];
            $Detalle = $sheet->getCell("E" . $row)->getValue();
            $Cheque = $sheet->getCell("F" . $row)->getValue();
            $Vrcheque = $sheet->getCell("G" . $row)->getValue();
            $Consignado = $sheet->getCell("H" . $row)->getValue();
            $Ctacheq = $sheet->getCell("I" . $row)->getValue();
            $Tipoc = $sheet->getCell("J" . $row)->getValue();
            $Numero = $sheet->getCell("K" . $row)->getValue();
            $Factura = $sheet->getCell("L" . $row)->getValue();
            $Debito = $sheet->getCell("M" . $row)->getValue();
            $Credito = $sheet->getCell("N" . $row)->getValue();
            $Saldo = $sheet->getCell("O" . $row)->getValue();
            $Basereten = $sheet->getCell("P" . $row)->getValue();
            $Porcret = $sheet->getCell("Q" . $row)->getCalculatedValue();
            $Cencosto = $sheet->getCell("R" . $row)->getCalculatedValue();
            $Ivacompras = $sheet->getCell("S" . $row)->getValue();
            $Niif = $sheet->getCell("T" . $row)->getValue();
            $Nom_niif = $sheet->getCell("U" . $row)->getValue();
            $Anulada = $sheet->getCell("V" . $row)->getValue();
            $Registro = $sheet->getCell("W" . $row)->getValue();
            $Regnom = $sheet->getCell("X" . $row)->getValue();

            //Borro lo que tenga este periodo para dejar solo lo del archivo
            if ($borra_anterior == "S" && $fecha_invalida == 0) {
                $sql_borra_movimiento = "Delete From SocioMovimientoCuenta
                          Where IDClub = '" . $IDClub . "' and Fecha='" . $Fecha . "'";
                $dbo->query($sql_borra_movimiento);
                $borra_anterior = "N";
            }

            if (!empty($Nombre) && !empty($Nit) && !empty($Fecha)) {
                if ($fecha_invalida == 0) {



                    if ($IDClub == 71) { //En puerto tranquilo esta columna viene distinta
                        $valor_predio = substr($Nit, 0, 1) . "-" . substr($Nit, 1, 3);
                    } else {
                        $valor_predio = $Nit;
                    }


                    //Consulto Socio
                    $sql_socio = "Select *
      												  From Socio
      												  Where IDClub = '" . $IDClub . "' and Predio like '%" . $valor_predio . "%'";

                    //echo $sql_socio;

                    $result_socio = $dbo->query($sql_socio);

                    if ($dbo->rows($result_socio) > 0) {
                        $row_socio = $dbo->FetchArray($result_socio);

                        $sql_inserta = "INSERT INTO SocioMovimientoCuenta (IDClub, IDSocio, Codigo, Nombre,Nit, Fecha, Detalle, Cheque, Vrcheque, Consignado, Ctacheq, Tipoc, Numero, Factura, Debito, Credito, Saldo, Basereten, Porcret, Cencosto, Ivacompras, Niif, Nom_niif, Anulada, Registro, Regnom, UsuarioTrCr, FechaTrCr)
                          VALUES ($IDClub,'" . $row_socio["IDSocio"] . "','" . $Codigo . "', '" . $Nombre . "', '" . $Nit . "', '" . $Fecha . "', '" . $Detalle . "', '" . $Cheque . "', '" . $Vrcheque . "','" . $Consignado . "', '" . $Ctacheq . "', '" . $Tipoc . "',
                            '" . $Numero . "', '" . $Factura . "', '" . $Debito . "', '" . $Credito . "', '" . $Saldo . "', '" . $Basereten . "', '" . $Porcret . "', '" . $Cencosto . "', '" . $Ivacompras . "', '" . $Niif . "', '" . $Nom_niif . "', '" . $Anulada . "',
                            '" . $Registro . "', '" . $Regnom . "','" . $nombrearchivo . "', NOW())";

                        $dbo->query($sql_inserta);
                    } else {
                        echo "<br>" . "El propietario no existe en la base: " . $valor_predio;
                    }
                } else {
                    echo "<br>" . "Fecha invalida: " . $Nombre . "-" . $Nit . "-" . $Fecha . "-" . $Debito;
                }
            } else {
                echo "<br>" . "Faltan parametros: " . $Nombre . "-" . $Nit . "-" . $Fecha . "-" . $Debito;
            }
        }

        echo "<br>Carga Archivo";
        exit;

        return false;
    }

    function get_data_movimiento_cuotasaldo($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $BorrarInfoAntigua = "")
    {

        $dbo = &SIMDB::get();
        $numregok = 0;

        $sql_socios = "SELECT IDSocio,NumeroDocumento,Accion FROM Socio WHERE IDClub = '" . $IDClub . "'";
        $r_socios = $dbo->query($sql_socios);
        while ($row_socios = $dbo->fetchArray($r_socios)) {
            $array_socios[$row_socios["NumeroDocumento"]] = $row_socios["IDSocio"];
        }

        if ($BorrarInfoAntigua == "S") :
            $sql_borra_movimiento = "Delete From SocioCuotaSaldo
											  Where IDClub = '" . $IDClub . "'";
            $dbo->query($sql_borra_movimiento);
        endif;

        require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
        $archivo = $file;
        $inputFileType = PHPExcel_IOFactory::identify($archivo);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($archivo);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $borra_anterior = "S";
        for ($row = 1; $row <= $highestRow; $row++) {

            if ($row == 1) {
                $TituloSaldo1 = $sheet->getCell("F" . $row)->getValue();
                $TituloSaldo2 = $sheet->getCell("G" . $row)->getValue();
                $TituloDescuento1 = $sheet->getCell("H" . $row)->getValue();
                $TituloDescuento2 = $sheet->getCell("I" . $row)->getValue();
            } else {
                $Apellido = $sheet->getCell("A" . $row)->getValue();
                $Nombre = $sheet->getCell("B" . $row)->getValue();
                $TipoDoc = $sheet->getCell("C" . $row)->getValue();
                $Cedula = $sheet->getCell("D" . $row)->getValue();
                $Cuota = $sheet->getCell("E" . $row)->getValue();
                $Saldo1 = $sheet->getCell("F" . $row)->getValue();
                $Saldo2 = $sheet->getCell("G" . $row)->getValue();
                $Descuento1 = $sheet->getCell("H" . $row)->getValue();
                $Descuento2 = $sheet->getCell("I" . $row)->getValue();

                if (!empty($Nombre) && !empty($Cedula) && !empty($Cuota)) {
                    //Consulto Socio

                    $IDSocio = $array_socios[$Cedula];

                    if (!empty($IDSocio)) {
                        $row_socio = $dbo->FetchArray($result_socio);
                        $sql_inserta = "INSERT INTO SocioCuotaSaldo (IDSocio, IDClub, Nombre, Apellido, TipoDocumento, Documento, Cuota, Saldo1, Saldo2, Descuento1,Descuento2, TituloSaldo1, TituloSaldo2,
                                                      TituloDescuento1,TituloDescuento2, FechaCarga, UsuarioTrCr, FechaTrCr)
                                          VALUES ($IDSocio, $IDClub,'" . $Nombre . "','" . $Apellido . "', '" . $TipoDoc . "', '" . $Cedula . "', '" . $Cuota . "', '" . $Saldo1 . "', '" . $Saldo2 . "', '" . $Descuento1 . "','" . $Descuento2 . "','" . $TituloSaldo1 . "',
                                                  '" . $TituloSaldo2 . "','" . $TituloDescuento1 . "','" . $TituloDescuento2 . "',CURDATE(),'" . $nombrearchivo . "', NOW())";
                        //echo $sql_inserta;
                        //exit;
                        $dbo->query($sql_inserta);
                    } else {
                        echo "<br>" . "El socio no existe en la base: " . $Cedula;
                    }
                } else {
                    echo "<br>" . "Faltan parametros: " . $Nombre . " " . $Apellido . "-" . $Cedula;
                }
            }
        }

        echo "<br>Carga Archivo";
        exit;

        return false;
    }

    function get_data_descuento($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $BorrarInfoAntigua = "")
    {

        $dbo = &SIMDB::get();
        $numregok = 0;

        if ($BorrarInfoAntigua == "S") :
            $sql_borra_movimiento = "Delete From SocioDescuento
											  Where IDClub = '" . $IDClub . "'";
            $dbo->query($sql_borra_movimiento);
        endif;

        require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
        $archivo = $file;
        $inputFileType = PHPExcel_IOFactory::identify($archivo);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($archivo);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $borra_anterior = "S";
        for ($row = 2; $row <= $highestRow; $row++) {
            $fecha_invalida = 1;
            $Codigo = $sheet->getCell("A" . $row)->getValue();
            $Propietario = $sheet->getCell("B" . $row)->getValue();
            $Coeficiente = $sheet->getCell("C" . $row)->getValue();
            $AguaSerena = $sheet->getCell("D" . $row)->getValue();
            $DescuentoSerena = $sheet->getCell("E" . $row)->getValue();
            $ClubHouse = $sheet->getCell("F" . $row)->getValue();
            $DescuentoClubHouse = $sheet->getCell("G" . $row)->getValue();

            //Borro lo que tenga este periodo para dejar solo lo del archivo
            if ($borra_anterior == "S" && $fecha_invalida == 0) {
                $sql_borra_movimiento = "Delete From SocioDescuento
                          Where IDClub = '" . $IDClub . "' and Fecha='" . $Fecha . "'";
                $dbo->query($sql_borra_movimiento);
                $borra_anterior = "N";
            }

            if (!empty($Codigo) && !empty($DescuentoSerena) && !empty($DescuentoClubHouse)) {

                $valor_predio = substr($Nit, 0, 1) . "-" . substr($Nit, 1, 3);

                //Consulto Socio
                $sql_socio = "Select *
      												  From Socio
      												  Where IDClub = '" . $IDClub . "' and Predio like '%" . $valor_predio . "%'";
                $result_socio = $dbo->query($sql_socio);

                if ($dbo->rows($result_socio) > 0) {
                    $row_socio = $dbo->FetchArray($result_socio);

                    $sql_inserta = "INSERT INTO SocioDescuento (IDClub, IDSocio, Codigo, Propietario,Coeficiente, AguaSerena, DescuentoSerena, ClubHouse, DescuentoClubHouse, UsuarioTrCr, FechaTrCr)
                          VALUES ($IDClub,'" . $row_socio["IDSocio"] . "','" . $Codigo . "', '" . $Propietario . "', '" . $Coeficiente . "', '" . $AguaSerena . "', '" . $DescuentoSerena . "', '" . $ClubHouse . "', '" . $DescuentoClubHouse . "','" . $nombrearchivo . "', NOW())";
                    $dbo->query($sql_inserta);
                } else {
                    echo "<br>" . "El propietario no existe en la base: " . $valor_predio;
                }
            } else {
                echo "<br>" . "Faltan parametros: " . $Nombre . "-" . $Nit . "-" . $Fecha . "-" . $Debito;
            }
        }

        echo "<br>Carga Archivo";
        exit;

        return false;
    }

    function get_permite_socio($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $BorrarInfoAntigua = "")
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
        $borra_anterior = "S";
        for ($row = 2; $row <= $highestRow; $row++) {
            $fecha_invalida = 1;
            $Documento = $sheet->getCell("A" . $row)->getValue();
            $Accion = $sheet->getCell("B" . $row)->getValue();
            $PermiteReservar = $sheet->getCell("C" . $row)->getValue();
            $PermiteReservarHotel = $sheet->getCell("D" . $row)->getValue();
            $PermiteDomicilios = $sheet->getCell("E" . $row)->getValue();

            if (!empty($Documento) || !empty($Accion)) {

                //Consulto Socio
                if (!empty($Documento)) {
                    $condicion_buscar = " and NumeroDocumento = '" . $Documento . "'";
                } else {
                    $condicion_buscar = " and Accion = '" . $Accion . "'";
                }

                $sql_socio = "SELECT IDSocio
      												  From Socio
      												  Where IDClub = '" . $IDClub . "' " . $condicion_buscar;
                $result_socio = $dbo->query($sql_socio);

                if ($dbo->rows($result_socio) > 0) {
                    $campo_actualiza = array();
                    $actualizar = "N";
                    if (strtoupper($PermiteReservar) == "S" || strtoupper($PermiteReservar) == "N") {
                        $campo_actualiza[] = " PermiteReservar = '" . $PermiteReservar . "'";
                        $actualizar = "S";
                    }
                    if (strtoupper($PermiteReservarHotel) == "S" || strtoupper($PermiteReservarHotel) == "N") {
                        $campo_actualiza[] = " PermiteReservarHotel = '" . $PermiteReservarHotel . "'";
                        $actualizar = "S";
                    }
                    if (strtoupper($PermiteDomicilios) == "S" || strtoupper($PermiteDomicilios) == "N") {
                        $campo_actualiza[] = " PermiteDomicilios = '" . $PermiteDomicilios . "'";
                        $actualizar = "S";
                    }

                    $row_socio = $dbo->FetchArray($result_socio);

                    if ($actualizar == "S") {

                        if (count($campo_actualiza) > 0) {
                            $datos_actualizar = implode(",", $campo_actualiza);
                        }

                        $sql_actualiza = "UPDATE Socio Set " . $datos_actualizar . "  WHERE IDSocio = '" . $row_socio["IDSocio"] . "'";
                        //echo "<br>" . $sql_actualiza;
                        $dbo->query($sql_actualiza);
                    }
                } else {
                    echo "<br>" . "El socio no existe en la base: " . $Documento . " - " . $Accion;
                }
            } else {
                echo "<br>" . "Faltan parametros: " . $Nombre . "-" . $Nit . "-" . $Fecha . "-" . $Debito;
            }
        }

        echo "<br>Carga Archivo";
        exit;

        return false;
    }

    function get_data_saldo_cartera($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $BorrarInfoAntigua = "")
    {

        $dbo = &SIMDB::get();
        $numregok = 0;

        if ($BorrarInfoAntigua == "S") :
            $sql_borra_movimiento = "Delete From SocioSaldoCartera
											  Where IDClub = '" . $IDClub . "'";
            $dbo->query($sql_borra_movimiento);
        endif;

        require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
        $archivo = $file;
        $inputFileType = PHPExcel_IOFactory::identify($archivo);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($archivo);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $borra_anterior = "S";
        for ($row = 2; $row <= $highestRow; $row++) {
            $fecha_invalida = 1;
            $Codigo = $sheet->getCell("A" . $row)->getValue();
            $Nombre = $sheet->getCell("B" . $row)->getValue();
            $uno_treinta = $sheet->getCell("C" . $row)->getValue();
            $treinta_y_uno_sesenta = $sheet->getCell("D" . $row)->getValue();
            $sesenta_y_uno_noventa = $sheet->getCell("E" . $row)->getValue();
            $mas90 = $sheet->getCell("F" . $row)->getValue();
            $total = $sheet->getCell("G" . $row)->getValue();
            $juridico = $sheet->getCell("H" . $row)->getValue();

            if (!empty($Nombre) && !empty($Codigo)) {

                if ($IDClub == 71) { //En puerto tranquilo esta columna viene distinta
                    $valor_predio = substr($Codigo, 0, 1) . "-" . substr($Codigo, 1, 3);
                } else {
                    $valor_predio = $Nit;
                }

                //Consulto Socio
                $sql_socio = "SELECT IDSocio
      												  From Socio
      												  Where IDClub = '" . $IDClub . "' and Predio like '%" . $valor_predio . "%'";
                $result_socio = $dbo->query($sql_socio);

                if ($dbo->rows($result_socio) > 0) {
                    $row_socio = $dbo->FetchArray($result_socio);
                    $sql_inserta = "INSERT INTO SocioSaldoCartera (`IDClub`, `IDSocio`, `Codigo`, `Nombre`,`1-30`, `31-60`	, `61-90`	, `Mas90`, `Total`, `Juridico`, `UsuarioTrCr`, `FechaTrCr`)
                          VALUES ($IDClub,'" . $row_socio["IDSocio"] . "','" . $Codigo . "', '" . $Nombre . "', '" . $uno_treinta . "', '" . $treinta_y_uno_sesenta . "', '" . $sesenta_y_uno_noventa . "','" . $mas90 . "', '" . $total . "', '" . $juridico . "','" . $nombrearchivo . "', NOW())";
                    $dbo->query($sql_inserta);
                } else {
                    echo "<br>" . "El propietario no existe en la base: " . $valor_predio;
                }
            } else {
                echo "<br>" . "Faltan parametros: " . $Nombre . "-" . $Codigo . "-" . $total;
            }
        }

        echo "<br>Carga Archivo";
        exit;

        return false;
    }

    function get_data_movimiento_dtr($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub, $BorrarInfoAntigua = "")
    {

        $dbo = &SIMDB::get();
        $numregok = 0;

        $sql_socios = "SELECT IDSocio,NumeroDocumento,Accion FROM Socio WHERE IDClub = '" . $IDClub . "'";
        $r_socios = $dbo->query($sql_socios);
        while ($row_socios = $dbo->fetchArray($r_socios)) {
            $array_socios[$row_socios["Accion"]] = $row_socios["IDSocio"];
        }

        if ($BorrarInfoAntigua == "S") :
            $sql_borra_movimiento = "Delete From SocioMovimientoDistrital
									Where IDClub = '" . $IDClub . "'";
            $dbo->query($sql_borra_movimiento);
        endif;

        require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";

        $archivo = $file;
        $inputFileType = PHPExcel_IOFactory::identify($archivo);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($archivo);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $borra_anterior = "S";

        for ($row = 2; $row <= $highestRow; $row++) {

            $Codigo = $sheet->getCell("A" . $row)->getValue();
            $Fecha = $sheet->getCell("B" . $row)->getFormattedValue();
            $Hora = $sheet->getCell("C" . $row)->getFormattedValue();
            $Campo = $sheet->getCell("D" . $row)->getValue();
            $Afiliado = $sheet->getCell("E" . $row)->getValue();
            $TipoTurno = $sheet->getCell("F" . $row)->getValue();
            $Caddie = $sheet->getCell("G" . $row)->getValue();
            $Profesor = $sheet->getCell("H" . $row)->getValue();
            $ValorClases = $sheet->getCell("I" . $row)->getValue();
            $Luz = $sheet->getCell("J" . $row)->getValue();
            $NombreInvitado = $sheet->getCell("K" . $row)->getValue();
            $ValorInvitado = $sheet->getCell("L" . $row)->getValue();
            $Retos = $sheet->getCell("M" . $row)->getValue();
            $Torneos = $sheet->getCell("N" . $row)->getValue();
            $Total = $sheet->getCell("O" . $row)->getValue();
            $TotalMesActual = $sheet->getCell("P" . $row)->getValue();

            // $Total = $TotalMesActual + $TotalMesAnterior;

            if (!empty($Codigo)) {
                $IDSocio = $array_socios[$Codigo];
                if ($IDSocio > 0) {
                    $sql_inserta = "INSERT INTO SocioMovimientoDistrital 
                                (IDClub,
                                IDSocio,
                                CodigoApp,
                                Fecha, 
                                Hora, 
                                Campo, 
                                Afiliado,
                                TipoTurno, 
                                Caddie,
                                Profesor, 
                                ValorClase,
                                Luz,                                
                                NombreInvitado, 
                                ValorInvitado,
                                Retos, 
                                Torneos, 
                                Total, 
                                TotalMesActual,
                                UsuarioTrCr,
                                FechaTrCr)
                                VALUES 
                                ($IDClub, 
                                $IDSocio, 
                                '$Codigo', 
                                '$Fecha', 
                                '$Hora', 
                                '$Campo', 
                                '$Afiliado',
                                '$TipoTurno', 
                                '$Caddie', 
                                '$Profesor', 
                                '$ValorClases', 
                                '$Luz', 
                                '$NombreInvitado', 
                                '$ValorInvitado',
                                '$Retos', 
                                '$Torneos', 
                                '$Total', 
                                '$TotalMesActual', 
                                'Carga Plano',
                                 NOW())";
                    $dbo->query($sql_inserta);
                } else {
                    echo "<br>" . "El socio no existe en la base: " . $Codigo;
                }
            } else {
                echo "<br>" . "Faltan parametros: " . $Codigo;
            }
        }
        echo "<br>Carga Archivo";
        exit;

        return false;
    }

    function get_data_pagospendientes($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub)
    {

        $dbo = &SIMDB::get();
        $fila = ($IGNORE_FIRTS_ROW == 1) ? 2 : 1;

        if ($fp = fopen($file, "r")) {
            $numregok = 0;
            $cont = 0;
            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
            $archivo = $file;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = $fila; $row <= $highestRow; $row++) {
                $Accion = $sheet->getCell("B" . $row)->getFormattedValue();
                $SaldoAnterior = $sheet->getCell("D" . $row)->getValue();
                $TotalPagos = $sheet->getCell("E" . $row)->getValue();
                $TotalCompras = $sheet->getCell("F" . $row)->getValue();
                $CuotaSostenimiento = $sheet->getCell("G" . $row)->getValue();
                $CobroPredial = $sheet->getCell("H" . $row)->getValue();
                $NotasCredito = $sheet->getCell("I" . $row)->getValue();
                $TotalPagar = $sheet->getCell("J" . $row)->getValue();
                $PagueseAntesDe = $sheet->getCell("K" . $row)->getValue();
                $NumeroDocumento = $sheet->getCell("L" . $row)->getValue();

                if (!empty($Accion)) {

                    $sql_socio = "SELECT IDSocio FROM Socio WHERE IDClub = $IDClub AND Accion = '$Accion' AND NumeroDocumento = '$NumeroDocumento'";
                    // echo '<br><br>';
                    $query_socio = $dbo->query($sql_socio);

                    if ($dbo->rows($query_socio) > 0) {
                        $result_socio = $dbo->assoc($query_socio);
                        $IDSocio = $result_socio['IDSocio'];

                        //Consulto Movimiento
                        $sql_pagospendientes = "Select * From SocioPagosPendientes
                        Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "'";

                        $result_pagospendientes = $dbo->query($sql_pagospendientes);
                        $cant = $dbo->rows($result_pagospendientes);
                        if ($dbo->rows($result_pagospendientes) > 0) :
                            $datos_pagospendientes = $dbo->fetchArray($result_pagospendientes);
                            //Editar Movimiento
                            $sql_edit_pagospendientes = "Update SocioPagosPendientes Set SaldoAnterior = '" . $SaldoAnterior . "', TotalPagos = '" . $TotalPagos . "', TotalCompras = '" . $TotalCompras . "', CuotaSostenimiento = '" . $CuotaSostenimiento . "', CobroPredial = '" . $CobroPredial . "', NotasCredito = '" . $NotasCredito . "',
														TotalPagar = '" . $TotalPagar . "', PagueseAntesDe = '" . $PagueseAntesDe . "', UsuarioTrEd = 'Archivo Plano: $nombrearchivo',FechaTrEd = NOW() 
														Where IDClub = '" . $IDClub . "' and IDSocioPagosPendientes = '" . $datos_pagospendientes["IDSocioPagosPendientes"] . "'";
                            $dbo->query($sql_edit_pagospendientes);
                            $numregok++;

                        else :
                            //Crear Movimiento
                            $sql_pagospendientes = "Insert Into SocioPagosPendientes(IDSocio,IDClub,SaldoAnterior,TotalPagos,TotalCompras,CuotaSostenimiento,CobroPredial,NotasCredito,TotalPagar, PagueseAntesDe, UsuarioTrCr, FechaTrCr)
														  Values ('" . $IDSocio . "','" . $IDClub . "','" . $SaldoAnterior . "','" . $TotalPagos . "','" . $TotalCompras . "','" . $CuotaSostenimiento . "','" . $CobroPredial . "','" . $NotasCredito . "','" . $TotalPagar . "', '" . $PagueseAntesDe . "', 'Archivo Plano: " . $nombrearchivo . "',NOW())";


                            $dbo->query($sql_pagospendientes);
                            $numregok++;

                        endif;
                    } else {
                        echo "<br>" . "No se ha encontrado al socio con la accion: " . $Accion;
                    }
                } else {
                    echo "<br>" . "El numero de accion esta equivocado: " . $Accion;
                }
                $cont++;
            } // END For

            return array("Numregs" => $cont, "RegsOK" => $numregok);
        } else {
            echo "error open $file";
        }

        return false;
    }

    function get_data_pagospendientes_PoloClub($nombrearchivo, $file, $IGNORE_FIRTS_ROW, $FIELD_TEMINATED = '', $field = '', $IDClub)
    {

        $dbo = &SIMDB::get();
        // $fila = ($IGNORE_FIRTS_ROW == 1) ? 2 : 1;
        $fila = 2;

        if ($fp = fopen($file, "r")) {
            $numregok = 0;
            $cont = 0;
            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
            $archivo = $file;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            // echo '<pre>';
            // var_dump($sheet);
            // die();
            for ($row = $fila; $row <= $highestRow; $row++) {
                $Accion = $sheet->getCell("A" . $row)->getValue();
                $Fecha = $sheet->getCell("B" . $row)->getValue();

                $timestamp = PHPExcel_Shared_Date::ExcelToPHP($Fecha);
                $timestamp = strtotime("+1 day", $timestamp);
                $Fecha = date("Y-m-d", $timestamp);
                $Documento = $sheet->getCell("C" . $row)->getValue();
                $Valor = $sheet->getCell("D" . $row)->getValue();
                if (!empty($Accion)) {

                    $sql_socio = "SELECT IDSocio FROM Socio WHERE IDClub = $IDClub AND Accion = '$Accion'";
                    // echo '<br><br>';
                    $query_socio = $dbo->query($sql_socio);

                    if ($dbo->rows($query_socio) > 0) {
                        $result_socio = $dbo->assoc($query_socio);
                        $IDSocio = $result_socio['IDSocio'];
                        $hoy = date('Y-m-d');
                        //Consulto Movimiento
                        $sql_pagospendientes = "Select * From SocioPagosPendientesPoloClub
                        Where IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' AND FechaTrCr < '$hoy'";

                        $result_pagospendientes = $dbo->query($sql_pagospendientes);
                        if ($dbo->rows($result_pagospendientes) > 0) :
                            $datos_pagospendientes = $dbo->fetchArray($result_pagospendientes);
                        //Eliminar Movimientos Pasados
                        // $sql_delete_pagospendientes = "DELETE FROM SocioPagosPendientesPoloClub Where IDSocio = '" . $datos_pagospendientes["IDSocio"] . "'";
                        // $dbo->query($sql_delete_pagospendientes);
                        // $numregok++;

                        endif;
                        //Crear Movimiento
                        $sql_pagospendientes = "Insert Into SocioPagosPendientesPoloClub(IDSocio,Accion,Fecha,Documento,Valor,UsuarioTrCr,FechaTrCr) Values ('" . $IDSocio . "','$Accion','$Fecha','$Documento','$Valor', 'Archivo Plano: " . $nombrearchivo . "',NOW())";
                        $dbo->query($sql_pagospendientes);
                        $numregok++;
                    } else {
                        echo "<br>" . "No se ha encontrado al socio con la accion: " . $Accion;
                    }
                } else {
                    echo "<br>" . "El numero de accion esta equivocado: " . $Accion;
                }
                $cont++;
            } // END For

            return array("Numregs" => $cont, "RegsOK" => $numregok);
        } else {
            echo "error open $file";
        }

        return false;
    }

    //extraemos las variables
    $table = SIMReg::get("table");
    $key = SIMReg::get("key");
    $mod = SIMReg::get("mod");


    //Verificar permisos
    //SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

    //creando las notificaciones que llegan en el parametro m de la URL
    SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

    $script = "socios";

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

                $frm[IDPais] = $frm[IDPais];
                $frm[IDClub] = SIMUser::get("club");
                $frm[IDDepartamento] = $frm[IDDepartamento];
                $frm[IDCiudad] = $frm[IDCiudad];
                $ClaveOriginal = $frm[Clave];
                $frm[Clave] = sha1($frm[Clave]);
                $frm[SegundaClave] = sha1($frm[SegundaClave]);

                //quitamos espacio del correo y el documento
                $Email = $frm[Email];
                $NumeroDocumento = $frm[NumeroDocumento];

                $frm[Email] = trim($Email);
                $frm[NumeroDocumento] = trim($NumeroDocumento);

                $comprobar_correo = $dbo->fetchAll("Socio", "(Email = '" . $frm[Email] . "' or NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
                if (!empty($comprobar_correo[IDSocio])) :
                    SIMHTML::jsAlert("Error: Ya existe  el Usuario app o el documento en este club, por favor verifique");
                    SIMHTML::jsRedirect("?mod=" . $mod . "&action=add");
                    exit;
                endif;

                //UPLOAD de imagenes
                if (isset($_FILES)) {
                    foreach ($_FILES as $keyfile => $file) {
                        $files = SIMFile::upload($file, SOCIO_DIR, "IMAGE");
                        if (empty($files) && !empty($file["name"])) {
                            SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                        }

                        $frm[$keyfile] = $files[0]["innername"];
                    }
                    // $files = SIMFile::upload($_FILES["Foto"], SOCIO_DIR, "IMAGE");
                    // if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                    //     SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    // }

                    // $frm["Foto"] = $files[0]["innername"];
                } //end if

                //insertamos los datos

                //ACCION PARA LA INTEGRACION DE LA CLINICA OBYRNE
                if (SIMUser::get("club") == 192) :


                    if (!empty($frm["Genero"])) :
                        if ($frm["Genero"] == "M") :
                            $genero = 1;
                        else :
                            $genero = 2;
                        endif;
                    endif;

                    //TIPO DE DOCUMENTO CLASIFICAR POR ID



                    if ($frm["TipoDocumento"] == "Cédula de ciudadanía") :
                        $tipodocumento = 1;
                    elseif ($frm["TipoDocumento"] == "Pasaporte") :
                        $tipodocumento = 2;
                    elseif ($frm["TipoDocumento"] == "Tarjeta de identidad") :
                        $tipodocumento = 3;
                    elseif ($frm["TipoDocumento"] == "Registro único tributario") :
                        $tipodocumento = 4;
                    elseif ($frm["TipoDocumento"] == "Cédula extranjeria") :
                        $tipodocumento = 5;
                    elseif ($frm["TipoDocumento"] == "Registro civil") :
                        $tipodocumento = 6;
                    elseif ($frm["TipoDocumento"] == "Adulto sin identificación") :
                        $tipodocumento = 7;
                    elseif ($frm["TipoDocumento"] == "Menor sin identificación") :
                        $tipodocumento = 8;
                    elseif ($frm["TipoDocumento"] == "Numero único de identificación") :
                        $tipodocumento = 9;
                    elseif ($frm["TipoDocumento"] == "Numero de identificación tributario") :
                        $tipodocumento = 13;
                    elseif ($frm["TipoDocumento"] == "Código Interno CMB") :
                        $tipodocumento = 14;
                    elseif ($frm["TipoDocumento"] == "Salvoconducto") :
                        $tipodocumento = 15;
                    elseif ($frm["TipoDocumento"] == "Permiso especial de permanencia") :
                        $tipodocumento = 16;
                    elseif ($frm["TipoDocumento"] == "Carnet diplomático") :
                        $tipodocumento = 17;
                    elseif ($frm["TipoDocumento"] == "Certificado de nacido vivo") :
                        $tipodocumento = 23;
                    elseif ($frm["TipoDocumento"] == "Documento de identificación extranjero") :
                        $tipodocumento = 25;
                    elseif ($frm["TipoDocumento"] == "Sin identificación") :
                        $tipodocumento = 26;
                    else :
                        $tipodocumento = "";

                    endif;



                    $nacimiento = date('d-m-Y', strtotime($frm["FechaNacimiento"]));


                    $token = '$2y$10$Zr/k8sePIc1ZdiereVFjKO0Om5aMBuMDFNe4XIFGOS1TwwE8v9tai';
                    //ruta demo, quitar el demo para dejarlo en prod
                    $url = "https://obyrne.pacificmedicalsuite.com/demo/pacific-gbl/api.php/patient/a/1?token=$token";
                    $data = array(
                        "given_1" => "$frm[Nombre]",
                        "given_2" => "",
                        "family_1" => "$frm[Apellido]",
                        "family_2" => "",
                        "ident_value" => "$frm[NumeroDocumento]",
                        "ident_type" => "$tipodocumento",
                        "sex_type" => "$genero",
                        "marital_type" => "$genero",
                        "date_of_birth" => "$nacimiento",
                        "date_of_birthFormat" => "dd/mm/yyyy",
                        "nationality" => "840", //este lo dejo por default por que no tengo el listado de nacionalidades ni paises

                        "patient" => array(
                            "additionals" => array(
                                "21" => "",
                                "22" => "$nacimiento",
                                "22Format" => "dd/mm/yyyy"
                            )
                        ),


                        "phonesPatCr" => [
                            array(
                                "type_phone" => "1", //este lo dejo por default por que no tengo el listado de tipos de tel
                                "no_phone" => "$frm[Telefono]"

                            ),
                            array(
                                "type_phone" => "2", //este lo dejo por default por que no tengo el listado de tipos de tel
                                "no_phone" => "$frm[Celular]"

                            )
                        ],
                        "addressesPatCr" => [array(
                            "country" => "840",
                            "state" => "1190",
                            "city" => "1121",
                            "zone" => "3",
                            "address" => "$frm[Direccion]",
                            "neighbor" => "-1"

                        )],
                        "emailsPatCr" => [array(
                            "email_type" => "1",
                            "email_value" => "$frm[CorreoElectronico]"

                        )]
                    );

                    $encodedData = json_encode($data);
                    $curl = curl_init($url);
                    $data_string = urlencode(json_encode($data));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedData);
                    $result = curl_exec($curl);
                    curl_close($curl);
                    //print_r($encodedData);

                    //print_r($encodedData);
                    $resulfinal = json_decode($result);
                    if ($resulfinal->state == 200) :
                        $frm[IdentificadorExterno] = $resulfinal->id;
                        $id = $dbo->insert($frm, $table, $key);
                    endif;

                else :
                    $id = $dbo->insert($frm, $table, $key);
                endif;

                $IDSocio = $dbo->lastID("IDSocio");
                //Bijao Si se cambia a no aceptado o anulado envio correo
                if ($frm[IDClub] == 32) :
                    $envio_respuesta = SIMUtil::envio_respuesta_registro($id, $frm["IDEstadoSocio"], $frm[Email], $ClaveOriginal);
                endif;

                SIMWebServiceUsuarios::insertar_secciones($IDSocio, $frm["IDClub"]);
                /* 
                //Actualizo Secciones Noticia
                foreach ($frm[SocioSeccion] as $id_seccion) :
                    $sql_interta_seccion = $dbo->query("Insert into SocioSeccion (IDSocio, IDSeccion) Values ('" . SIMNet::reqInt("id") . "', '" . $id_seccion . "')");
                endforeach;

                //Actualizo Secciones Evento
                foreach ($frm[SocioSeccionEvento] as $id_seccion_evento) :
                    $sql_interta_seccion = $dbo->query("Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('" . SIMNet::reqInt("id") . "', '" . $id_seccion_evento . "')");
                endforeach;

                //Actualizo Secciones Galeria
                foreach ($frm[SocioSeccionGaleria] as $id_seccion_galeria) :
                    $sql_interta_seccion = $dbo->query("Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('" . SIMNet::reqInt("id") . "', '" . $id_seccion_galeria . "')");
                endforeach; */

                //Generar Codigo de barras
                //$parametros_codigo_barras = $id."-".$frm[Nombre]."-".$frm[NumeroDocumento];
                //$parametros_codigo_barras = $frm[Accion]."-".$frm[NumeroDocumento];
                $parametros_codigo_barras = $frm[NumeroDocumento];

                if ($frm[IDClub] == 20 || $frm[IDClub] == 1) : //Medellin solo accion, se debe poner como parametro y quitar esta validacion
                    $parametros_codigo_barras = $frm[Accion];
                else :
                    $parametros_codigo_barras = $frm[NumeroDocumento];
                endif;

                if ($frm[IDClub] == 38) : // Club Colombia el doc y el caracter punto y coma
                    $parametros_codigo_barras .= ";";
                endif;

                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $id);
                //actualizo codigo barras
                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $id . "'");

                //Generar Codigo QR
                if ($frm[IDClub] == 34) :
                    $parametros_codigo_qr = $frm[NumeroDocumento];
                else :
                    $configuracionClub = $dbo->getFields('ConfiguracionClub', 'DatosCarne', 'IDClub = ' . SIMUser::get('club'));
                    if ($configuracionClub == 'Contacto') {
                        $codeContents  = 'BEGIN:VCARD' . "\n";
                        $codeContents .= 'VERSION:2.1' . "\n";
                        $codeContents .= 'N:' . $frm['Nombre'] . ' ' . $frm['Apellido'] . "\n";
                        $codeContents .= 'FN:' . $frm['Nombre'] . ' ' . $frm['Apellido'] . "\n";
                        $codeContents .= 'TEL;WORK;VOICE:' . $frm['Telefono'] . "\n";
                        $codeContents .= 'TEL;TYPE=cell:' . $frm['Telefono'] . "\n";
                        $codeContents .= 'EMAIL:' . $frm['CorreoElectronico'] . "\n";
                        $codeContents .= 'END:VCARD';
                    } else {
                        if (!empty($frm['CampoCodigoQR'])) {
                            $codeContents = $frm[$frm['CampoCodigoQR']];
                        } else {
                            $codeContents = $frm['NumeroDocumento'];
                        }
                    }
                    if (SIMUser::get('club') == 206) {
                        $codeContents = $frm['NumeroDocumento'] . $frm['Nombre'] . $frm['Apellido'] . $frm['TipoSocio'];
                    }
                    $parametros_codigo_qr = $codeContents;
                endif;

                $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' Where IDSocio = '" . $id . "'");

                if ($frm[IDClub] == 27) : //Payande
                    $respuesta = SIMWebServiceApp::actualiza_payande($id);
                //print_r($respuesta);
                endif;

                if ($frm[ActulizaCodigoBarras] == 1) :

                    $parametros_codigo_barras = $frm[$frm[CampoCodigoBarras]];

                    $frm[CodigoBarras] = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $id);
                    $SQL = "UPDATE Socio SET CodigoBarras = '$frm[CodigoBarras]' WHERE IDSocio = '$id'";
                    $dbo->query($SQL);

                endif;

                // if ($frm[ActulizaCodigoQR] == 1) :

                //     $parametros_codigo_qr = $frm[$frm[CampoCodigoQR]];
                //     $frm["CodigoQR"] = SIMUtil::generar_carne_qr($id, $parametros_codigo_qr);
                //     $SQL = "UPDATE Socio SET CodigoQR = '$frm[CodigoQR]' WHERE IDSocio = '$id'";
                //     $update_codigo = $dbo->query($SQL);

                // endif;


                SIMHTML::jsRedirect("socios.php?m=insertarexito");
            } else {
                exit;
            }

            break;

        case "update":
            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
                //los campos al final de las tablas
                $frm = SIMUtil::varsLOG($_POST);
                /*  print_r($frm);
                exit; */
                $frm[IDPais] = 0;
                $frm[IDClub] = SIMUser::get("club");
                $frm[IDDepartamento] = 0;
                // $frm[IDCiudad] = 0;
                $frm[IDNacionalidad] = 0;
                $ClaveOriginal = $frm[Clave];

                //quitamos espacio del correo y el documento
                $Email = $frm[Email];
                $NumeroDocumento = $frm[NumeroDocumento];

                $frm[Email] = trim($Email);
                $frm[NumeroDocumento] = trim($NumeroDocumento);

                if ($frm[Clave] != $frm[ClaveAnt]) {
                    $frm[Clave] = sha1($frm[Clave]);
                }

                if ($frm[SegundaClave] != $frm[SegundaClaveAnt]) {
                    $frm[SegundaClave] = sha1($frm[SegundaClave]);
                }

                //Compruebo que no exista el correo
                if ($frm[Email] != trim($frm[EmailAnt])) :
                    $comprobar_correo = $dbo->fetchAll("Socio", "(Email = '" . $frm[Email] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
                    if (!empty($comprobar_correo[IDSocio])) :
                        SIMHTML::jsAlert("Error: Ya existe  el email en este club, por favor verifique");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $frm[ID]);
                        exit;
                    endif;
                endif;

                //Compruebo que no exista el documento
                if ($frm[NumeroDocumento] != trim($frm[NumeroDocumentoAnt])) :
                    $comprobar_correo = $dbo->fetchAll("Socio", "(NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
                    if (!empty($comprobar_correo[IDSocio])) :
                        SIMHTML::jsAlert("Error: Ya existe  el numero de documento en este club, por favor verifique");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $frm[ID]);
                        exit;
                    endif;
                endif;

                //UPLOAD de imagenes
                if (isset($_FILES)) {
                    foreach ($_FILES as $keyfile => $file) {
                        $files = SIMFile::upload($file, SOCIO_DIR, "IMAGE");
                        if (empty($files) && !empty($file["name"])) {
                            SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                        }

                        $frm[$keyfile] = $files[0]["innername"];
                    }
                    // $files = SIMFile::upload($_FILES["Foto"], SOCIO_DIR, "IMAGE");
                    // if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                    //     SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    // }

                    // $frm["Foto"] = $files[0]["innername"];
                } //end if

                //Generar Codigo de barras
                $parametros_codigo_barras = $frm[IDClub] . "-" . $frm[Nombre] . "-" . $frm[NumeroDocumento];
                //$parametros_codigo_barras = $frm[Accion]."-".$frm[NumeroDocumento];
                if ($frm[IDClub] == 20 || $frm[IDClub] == 1) : //Medellin solo accion, se debe poner como parametro y quitar esta validacion
                    $parametros_codigo_barras = $frm[Accion];
                elseif ($frm[IDClub] == 10) :
                    $parametros_codigo_barras = $frm["ObservacionGeneral"];
                else :
                    $parametros_codigo_barras = $frm[NumeroDocumento];
                endif;

                if ($frm[IDClub] == 38) : // Club Colombia el doc y el caracter punto y coma
                    $parametros_codigo_barras .= ";";
                endif;

                if ($frm[IDClub] == 34) : // rancho sanf f
                    $alto_barras = "80";
                endif;

                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $frm[IDClub], $alto_barras);

                //Generar Codigo QR
                if ($frm[IDClub] == 34) :
                    $parametros_codigo_qr = $frm[NumeroDocumento];
                /*  elseif ($frm[IDClub] == 88) :
                    $parametros_codigo_qr = "https://www.miclubapp.com/plataform/reportereservassocio.php?action=search&NumeroDocumento=" . $frm["NumeroDocumento"];
              */
                // elseif ($frm[IDClub] == 10) :
                //     $parametros_codigo_qr = $frm["ObservacionGeneral"];
                else :
                    $configuracionClub = $dbo->getFields('ConfiguracionClub', 'DatosCarne', 'IDClub = ' . SIMUser::get('club'));
                    if ($configuracionClub == 'Contacto') {
                        $codeContents  = 'BEGIN:VCARD' . "\n";
                        $codeContents .= 'VERSION:2.1' . "\n";
                        $codeContents .= 'N:' . $frm['Nombre'] . ' ' . $frm['Apellido'] . "\n";
                        $codeContents .= 'FN:' . $frm['Nombre'] . ' ' . $frm['Apellido'] . "\n";
                        $codeContents .= 'TEL;WORK;VOICE:' . $frm['Telefono'] . "\n";
                        $codeContents .= 'TEL;TYPE=cell:' . $frm['Telefono'] . "\n";
                        $codeContents .= 'EMAIL:' . $frm['CorreoElectronico'] . "\n";
                        $codeContents .= 'END:VCARD';
                    } else {
                        if (!empty($frm['CampoCodigoQR'])) {
                            $codeContents = $frm[$frm['CampoCodigoQR']];
                        } else {
                            $codeContents = $frm['NumeroDocumento'];
                        }
                    }
                    if (SIMUser::get('club') == 206) {
                        $codeContents = $frm['NumeroDocumento'] . $frm['Nombre'] . $frm['Apellido'] . $frm['TipoSocio'];
                    }

                    $parametros_codigo_qr = $codeContents;
                endif;
                $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);

                if ($frm[IDClub] == 9) {
                    $parametros_codigo_qr = $frm["Accion"] . "\r\n";
                    //$frm["CodigoQR2"]=SIMUtil::generar_carne_qr($frm[IDSocio],$parametros_codigo_qr);
                }

                //antes de actualizar guardo el log de cambio que se hizo en el socio
                $identificador_club = $frm[IDClub];
                $log_cambio_tabla = guardar_log_cambio_datos($table, $key, SIMNet::reqInt("id"), $frm, $identificador_club, SIMUser::get("IDUsuario"));
                //fin guardar log

                //Bijao Si se cambia a no aceptado o anulado envio correo
                if ($frm["IDEstadoSocio"] != $frm["IDEstadoSocioAnt"] && $frm[IDClub] == 32) :
                    $envio_respuesta = SIMUtil::envio_respuesta_registro(SIMNet::reqInt("id"), $frm["IDEstadoSocio"], $frm[Email], $ClaveOriginal);
                endif;

                //Para btcc se inactiva todo el grupo familiar cuando no tien permiso de reservar
                if ($frm["IDClub"] == 72 || $frm["IDClub"] == 8) {
                    if (!empty($frm["AccionPadre"])) {
                        $inactiva_nucleo = "UPDATE Socio SET PermiteReservar = '" . $frm["PermiteReservar"] . "' WHERE  IDClub = '" . $frm["IDClub"] . "' and (AccionPadre = '" . $frm["AccionPadre"] . "' or Accion = '" . $frm["AccionPadre"] . "') ";
                        $dbo->query($inactiva_nucleo);
                    } elseif (!empty($frm["Accion"])) {
                        //$inactiva_nucleo="UPDATE Socio SET PermiteReservar = '".$frm["PermiteReservar"]."' WHERE  IDClub = '".$frm["IDClub"]."' and AccionPadre = '".$frm["Accion"]."'";
                        //$dbo->query($inactiva_nucleo);
                    }
                    //echo $inactiva_nucleo;
                    //exit;
                }

                //Verifica si el id de la categoria cambia, en ese caso guarda en el campo ModificaCategoria = 'Admin' para identificar que el administrador realiza el cambio 
                $idCatAnt = $dbo->getFields("Socio", "IDCategoria", "IDSocio = " . SIMNet::reqInt("id"));
                if ($frm['IDCategoria'] != $idCatAnt)
                    $frm['ModificaCategoria'] = 'Admin';

                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

                if ($frm[IDClub] == 27) : //Payande
                    $respuesta = SIMWebServiceApp::actualiza_payande(SIMNet::reqInt("id"));
                //print_r($respuesta);
                endif;

                if ($frm[ActulizaCodigoBarras] == 1) :
                    $parametros_codigo_barras = $frm[$frm[CampoCodigoBarras]];

                    $frm[CodigoBarras] = SIMUtil::generar_codigo_barras($parametros_codigo_barras, SIMNet::reqInt("id"));
                    $SQL = "UPDATE Socio SET CodigoBarras = '$frm[CodigoBarras]' WHERE IDSocio = '" . SIMNet::reqInt("id") . "'";
                    $dbo->query($SQL);

                endif;

                // if ($frm[ActulizaCodigoQR] == 1) :
                //     die('entro');

                //     $parametros_codigo_qr = $frm[$frm[CampoCodigoQR]];

                //     $frm["CodigoQR"] = SIMUtil::generar_carne_qr(SIMNet::reqInt("id"), $parametros_codigo_qr);
                //     $SQL = "UPDATE Socio SET CodigoQR = '$frm[CodigoQR]' WHERE IDSocio = '" . SIMNet::reqInt("id") . "'";
                //     $update_codigo = $dbo->query($SQL);

                // endif;

                //Modifica los registros de corredores
                $SQL_triatlon = "UPDATE RegistroCorredor SET NumeroDocumento = '" . $frm['NumeroDocumento'] . "', Nombre = '" . $frm['Nombre'] . "', Apellido = '" . $frm['Apellido'] . "',Email = '" . $frm['CorreoElectronico'] . "'  WHERE IDSocio = '" . SIMNet::reqInt("id") . "'";
                $update_triatlon = $dbo->query($SQL_triatlon);


                /*
            //Actualizo Secciones Noticia
            $sql_borra_seccion = $dbo->query("Delete From SocioSeccion Where IDSocio  = '".SIMNet::reqInt("id")."'");
            foreach($frm[SocioSeccion] as $id_seccion):
            $sql_interta_seccion=$dbo->query("Insert into SocioSeccion (IDSocio, IDSeccion) Values ('".SIMNet::reqInt("id")."', '".$id_seccion."')");
            endforeach;

            //Actualizo Secciones Evento
            $sql_borra_seccion_evento = $dbo->query("Delete From SocioSeccionEvento Where IDSocio  = '".SIMNet::reqInt("id")."'");
            foreach($frm[SocioSeccionEvento] as $id_seccion_evento):
            $sql_interta_seccion=$dbo->query("Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('".SIMNet::reqInt("id")."', '".$id_seccion_evento."')");
            endforeach;

            //Actualizo Secciones Galeria
            $sql_borra_seccion_galeria = $dbo->query("Delete From SocioSeccionGaleria Where IDSocio  = '".SIMNet::reqInt("id")."'");
            foreach($frm[SocioSeccionGaleria] as $id_seccion_galeria):
            $sql_interta_seccion=$dbo->query("Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('".SIMNet::reqInt("id")."', '".$id_seccion_galeria."')");
            endforeach;
             */

                //enviar correo a los socios que activaron
                $NotificarCorreoSocio = $dbo->getFields("ConfiguracionClub", "EnviarCorreoSocio", "IDClub = '" . $frm["IDClub"] . "'");



                if ($NotificarCorreoSocio == "S") {

                    if ($frm["IDEstadoSocio"] == 1) {
                        $Asunto = "Confirmación información ingreso app";
                        $Mensaje = "Queda confirmado su usuario y contraseña para el ingreso al app<br><br> Usuario:  $frm[NumeroDocumento] <br> Contraseña: $frm[NumeroDocumento]";

                        SIMUtil::envia_correo_general($frm["IDClub"], $frm["CorreoElectronico"], $Mensaje, $Asunto);
                    }
                }

                //inactivar los clasificados del socio
                if ($frm["IDEstadoSocio"] == 2) {
                    $sql_inactivar_clasificados = "UPDATE Clasificado SET IDEstadoClasificado='4' WHERE IDSocio='" . SIMNet::reqInt("id") . "'";
                    $dbo->query($sql_inactivar_clasificados);
                }

                $frm = $dbo->fetchById($table, $key, $id, "array");
                SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
                SIMHTML::jsAlert("Registro Guardado Correctamente");
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
            } else {
                exit;
            }

            break;

        case "edit":

            $club = 195;
            //MASCOTAS
            $sql_preguntas3 = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
					FROM Encuesta E
					JOIN Pregunta P ON P.IDEncuesta = E.IDEncuesta
					WHERE E.IDClub =$club
					AND E.IDEncuesta =731
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

            $result3 = $dbo->query($sql_preguntas3);
            $numPregunta3 = 1;
            $array_preguntas3 = array();
            $array_NoPregunta3 = array();
            while ($rowPregunta3 = $dbo->fetchArray($result3)) {

                $array_preguntas3[$rowPregunta3["IDPregunta"]] = str_replace(",", "/", $rowPregunta3["EtiquetaCampo"]);
                $array_NoPregunta3[$rowPregunta3["IDPregunta"]] = "_" . $rowPregunta3["IDPregunta"];
            }

            //PERSONA_CONTACTO
            $sql_preguntas4 = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
					FROM Encuesta E
					JOIN Pregunta P ON P.IDEncuesta = E.IDEncuesta
					WHERE E.IDClub =$club
					AND E.IDEncuesta =829
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

            $result4 = $dbo->query($sql_preguntas4);
            $numPregunta4 = 1;
            $array_preguntas4 = array();
            $array_NoPregunta4 = array();
            while ($rowPregunta4 = $dbo->fetchArray($result4)) {

                $array_preguntas4[$rowPregunta4["IDPregunta"]] = str_replace(",", "/", $rowPregunta4["EtiquetaCampo"]);
                $array_NoPregunta4[$rowPregunta4["IDPregunta"]] = "_" . $rowPregunta4["IDPregunta"];
            }

            //EMPLEADOS
            $sql_preguntas1 = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
					FROM Encuesta E
					JOIN Pregunta P ON P.IDEncuesta = E.IDEncuesta
					WHERE E.IDClub =$club
					AND E.IDEncuesta =729
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

            $result1 = $dbo->query($sql_preguntas1);
            $numPregunta = 1;
            $array_preguntas1 = array();
            $array_NoPregunta1 = array();
            while ($rowPregunta1 = $dbo->fetchArray($result1)) {

                $array_preguntas1[$rowPregunta1["IDPregunta"]] = str_replace(",", "/", $rowPregunta1["EtiquetaCampo"]);
                $array_NoPregunta1[$rowPregunta1["IDPregunta"]] = "_" . $rowPregunta1["IDPregunta"];
            }

            //VEHICULOS
            $sql_preguntas2 = " SELECT P.IDPregunta,P.EtiquetaCampo,P.Orden
					FROM Encuesta E
					JOIN Pregunta P ON P.IDEncuesta = E.IDEncuesta
					WHERE E.IDClub =$club
					AND E.IDEncuesta =730
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

            $result2 = $dbo->query($sql_preguntas2);
            $numPregunta = 1;
            $array_preguntas2 = array();
            $array_NoPregunta2 = array();
            while ($rowPregunta2 = $dbo->fetchArray($result2)) {

                $array_preguntas2[$rowPregunta2["IDPregunta"]] = str_replace(",", "/", $rowPregunta2["EtiquetaCampo"]);
                $array_NoPregunta2[$rowPregunta2["IDPregunta"]] = "_" . $rowPregunta2["IDPregunta"];
            }





            $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
            $view = "views/" . $script . "/form.php";
            $newmode = "update";
            $titulo_accion = "Actualizar";

            break;

        case "delfoto":
            $foto = $_GET['foto'];
            $campo = $_GET['campo'];
            $id = $_GET['id'];
            $filedelete = SOCIO_DIR . $foto;
            unlink($filedelete);
            $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
            SIMHTML::jsAlert("Imagen Eliminada Correctamente");
            SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
            break;

        case "search":
            $view = "views/" . $script . "/list.php";
            break;

        case "InsertarVehiculo":
            $frm = SIMUtil::varsLOG($_POST);
            //Verifico que no exista la placa
            $placa = $dbo->getFields("Vehiculo", "Placa", "Placa = '" . $frm[Placa] . "' and IDSocio = '" . $frm["ID"] . "'");
            if (empty($placa)) :
                $id = $dbo->insert($frm, "Vehiculo", "IDVehiculo");

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio =  $frm[ID]");

                if ($frm[IDClub] == 72 && $datos_socio[Accion] == $datos_socio[AccionPadre]) :

                    // PARA BTC DEBEMOS CREAR EL VEHICULO A TODO EL CIRCULO FAMILIAR
                    $SQLNucleo = "SELECT IDSocio FROM Socio WHERE AccionPadre = $datos_socio[AccionPadre] AND IDSocio <> $frm[ID] AND IDClub = 72";
                    $QRYNucleo = $dbo->query($SQLNucleo);

                    while ($socios = $dbo->fetchArray($QRYNucleo)) :
                        $frm[IDSocio] = $socios[IDSocio];
                        $id = $dbo->insert($frm, "Vehiculo", "IDVehiculo");
                    endwhile;
                endif;

                SIMHTML::jsAlert("Registro Exitoso");
            else :
                SIMHTML::jsAlert("La placa ya existe por favor verifique");
            endif;
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=vehiculos&id=" . $frm[ID]);
            exit;
            break;

        case "ModificaVehiculo":
            $frm = SIMUtil::varsLOG($_POST);
            $dbo->update($frm, "Vehiculo", "IDVehiculo", $frm[IDVehiculo]);
            SIMHTML::jsAlert("Modificacion Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=vehiculos&id=" . $frm[ID]);
            exit;
            break;

        case "EliminaVehiculo":
            $id = $dbo->query("DELETE FROM Vehiculo WHERE IDVehiculo   = '" . $_GET[IDVehiculo] . "' LIMIT 1");
            SIMHTML::jsAlert("Eliminacion Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=vehiculos&id=" . $_GET["id"]);
            exit;
            break;
        case "InsertGestionCartera":
            $frm = SIMUtil::varsLOG($_POST);
            //Verifico que no exista la categoria
            $id = $dbo->insert($frm, "GestionCartera", "IDGestionCartera");
            SIMHTML::jsAlert("Registro Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=gestioncartera&id=" . $frm["IDSocio"]);
            exit;
            break;
        case "Exportar":
            $frm = SIMUtil::varsLOG($_POST);

            //Fecha Ultimo Backup
            $FechaBackup = '2018-05-30';

            $where = '';
            
            if($frm['Activa'] === "1"){
                $where = "AND gc.IDSocio = '" . $frm["IDSocio"] . "'";
            }

            $sql = "Select gc.*,CONCAT(s.nombre,' ', s.apellido) as NombreSocio From GestionCartera gc join Socio as s on s.IDSocio = gc.IDSocio Where gc.FechaRegistro between  '" . 
            $frm["FechaInicio"] . "' AND '" . $frm["FechaFin"] . "' AND gc.IDClub = '" . $frm["IDClub"] . "' ".$where;
            $nombre = "Reservas" . date("Y_m_d H:i:s");
            $qry = $dbo->query($sql);
            $Num = $dbo->rows($qry);

            $html = "";
            $html .= "<table width='100%' border='1'>";
            $html .= "<tr>";
            $html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
            $html .= "</tr>";
            $html .= "<tr>";
            $html .= "<th>SOCIO</th>";
            $html .= "<th>OBSERVACION</th>";
            $html .= "<th>FECHA REGISTRO</th>";
            $html .= "<th>USUARIO</th>";
            $html .= "</tr>";


            while ($row = $dbo->fetchArray($qry, $a)) {

                $html .= "<tr>";
                $html .= "<td>" . $row['NombreSocio'] . "</td>";
                $html .= "<td>" . $row['Observacion'] . "</td>";
                $html .= "<td>" . $row['FechaRegistro'] . "</td>";
                $html .= "<td>" . $row['UsuarioTrEd'] . "</td>";
                $html .= "</tr>";
            }

            $html .= "</table>";
            $filename = "libros.xls";

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=" . $filename . "");


            echo $html;
            exit;
            die;
            //Verifico que no exista la categoria
            $id = $dbo->insert($frm, "GestionCartera", "IDGestionCartera");
            SIMHTML::jsAlert("Registro Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=gestioncartera&id=" . $frm["IDSocio"]);
            exit;
            break;
        case "InsertarLicenciaSocio":
            $frm = SIMUtil::varsLOG($_POST);
            //Verifico que no exista la categoria
            $placa = $dbo->getFields("LicenciaSocio", "Categoria", "Categoria = '" . $frm[Categoria] . "' and IDSocio = '" . $frm["ID"] . "'");
            if (empty($placa)) :
                $id = $dbo->insert($frm, "LicenciaSocio", "IDLicenciaSocio");
                SIMHTML::jsAlert("Registro Exitoso");
            else :
                SIMHTML::jsAlert("La categoria ya existe por favor verifique");
            endif;
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=licencias&id=" . $frm[ID]);
            exit;
            break;

        case "ModificaLicenciaSocio":
            $frm = SIMUtil::varsLOG($_POST);
            $dbo->update($frm, "LicenciaSocio", "IDLicenciaSocio", $frm[IDLicenciaSocio]);
            SIMHTML::jsAlert("Modificacion Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=licencias&id=" . $frm[ID]);
            exit;
            break;

        case "EliminaLicenciaSocio":
            $id = $dbo->query("DELETE FROM LicenciaSocio WHERE IDLicenciaSocio   = '" . $_GET[IDLicenciaSocio] . "' LIMIT 1");
            SIMHTML::jsAlert("Eliminacion Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=licencias&id=" . $_GET["id"]);
            exit;
            break;

        case "InsertarPredio":
            $frm = SIMUtil::varsLOG($_POST);
            $id = $dbo->insert($frm, "Predio", "IDPredio");
            SIMHTML::jsAlert("Registro Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=predios&id=" . $frm[ID]);
            exit;
            break;

        case "ModificaPredio":
            $frm = SIMUtil::varsLOG($_POST);
            $dbo->update($frm, "Predio", "IDPredio", $frm[IDPredio]);
            SIMHTML::jsAlert("Modificacion Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=predios&id=" . $frm[ID]);
            exit;
            break;

        case "EliminaPredio":
            $id = $dbo->query("DELETE FROM Predio WHERE IDPredio   = '" . $_GET[IDPredio] . "' LIMIT 1");
            SIMHTML::jsAlert("Eliminacion Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=predios&id=" . $_GET["id"]);
            exit;
            break;

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

        case "cargarmovimiento":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_movimiento($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;

        case "cargarextracto":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_movimiento_extracto($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], $_POST['BorrarInfoAntigua']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;

        case "cargarpuntos":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_movimiento_puntos($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], $_POST['BorrarInfoAntigua']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;

        case "cargarmovimientocuenta":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_movimiento_cuenta($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], $_POST['BorrarInfoAntigua']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;

        case "cargarcuotasaldo":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_movimiento_cuotasaldo($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], $_POST['BorrarInfoAntigua']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;

        case "cargarsaldocartera":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_saldo_cartera($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], $_POST['BorrarInfoAntigua']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;

        case "cargarmovimientodtr":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_movimiento_dtr($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], $_POST['BorrarInfoAntigua']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;

        case "cargardescuento":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;
            $result = get_data_descuento($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], $_POST['BorrarInfoAntigua']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;

        case "cargarpermitesocio":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;
            $result = get_permite_socio($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub'], $_POST['BorrarInfoAntigua']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;
        case "cargarpagospendientes":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_pagospendientes($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub']);

            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;
        case "cargarpagospendientespoloclub":
            $time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES);
            if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data_pagospendientes_PoloClub($nombre_archivo, SOCIOPLANO_DIR . $nombre_archivo, $_POST['IGNORELINE'], $_POST['FIELD_TEMINATED'], $_POST['field'], $_POST['IDClub']);

            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } // if($result["Numregs"] > 0){

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);
            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
            break;
        case "inactivarsocio":
            $fechainactivar = date("Y-m-01");
            $sql_inactiva = "UPDATE Socio Set PermiteReservar='" . $_POST["PermiteReservar"] . "', UsuarioTrEd='plano usuario permite reservar',FechaTrEd=NOW() WHERE IDClub = '" . $_POST["IDClub"] . "' and PagadoHasta <= '" . $fechainactivar . "'";
            $dbo->query($sql_inactiva);
            SIMHTML::jsAlert("Socios actualizados con permitir reservar en No con exito");
            SIMHTML::jsRedirect($script . ".php?action=search");
            break;

        case "inactivarhotel":
            $sql_inactiva = "UPDATE Socio Set PermiteReservarHotel='" . $_POST["PermiteReservarHotel"] . "', UsuarioTrEd='plano usuario permite hotel',FechaTrEd=NOW() WHERE IDClub = '" . $_POST["IDClub"] . "'";
            $dbo->query($sql_inactiva);
            SIMHTML::jsAlert("Socios actualizados con permitir reservar hotel en No con exito");
            SIMHTML::jsRedirect($script . ".php?action=search");
            break;

        case "inactivardomicilios":
            $sql_inactiva = "UPDATE Socio Set PermiteDomicilios='" . $_POST["PermiteDomicilios"] . "', UsuarioTrEd='plano usuario permite domicilio',FechaTrEd=NOW() WHERE IDClub = '" . $_POST["IDClub"] . "'";
            $dbo->query($sql_inactiva);
            SIMHTML::jsAlert("Socios actualizados con permitir hacer domicilios en No con exito");
            SIMHTML::jsRedirect($script . ".php?action=search");
            break;

        case "crearbarras":
            $sql_socio = "Select * From Socio Where IDClub = '" . $_POST["IDClub"] . "' and CodigoBarras = '' ";
            $result_socio = $dbo->query($sql_socio);
            while ($row_socio = $dbo->fetchArray($result_socio)) {
                $parametros_codigo_barras = $row_socio["NumeroDocumento"];
                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $row_socio["IDSocio"], $alto_barras);
                //actualizo codigo barras
                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $row_socio["IDSocio"] . "' AND IDClub = '" . SIMUser::get('club') . "'");
            }
            SIMHTML::jsAlert("Socios actualizados codigo de barras");
            SIMHTML::jsRedirect($script . ".php?action=search");
            break;



        case "cartera_bacata":

            $frm = SIMUtil::varsLOG($_POST);
            $file = $_FILES['file']['tmp_name'];
            $IDClub = SIMUser::get("club");
            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
            $archivo = $file;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $query1 = $dbo->query("TRUNCATE TABLE CarteraBacata");
            for ($row = 3; $row <= $highestRow; $row++) {

                $fecha = $sheet->getCell("A" . $row)->getFormattedValue();
                $factura = $sheet->getCell("B" . $row)->getValue();
                $documento = $sheet->getCell("C" . $row)->getValue();
                $nombre = $sheet->getCell("D" . $row)->getValue();
                $cantidad = $sheet->getCell("E" . $row)->getValue();
                $producto = $sheet->getCell("F" . $row)->getValue();
                $total_neto = $sheet->getCell("G" . $row)->getValue();
                $iva = $sheet->getCell("H" . $row)->getValue();
                $total = $sheet->getCell("I" . $row)->getValue();
                $forma_pago = $sheet->getCell("J" . $row)->getValue();

                $timestamp = strtotime($fecha);
                $fecha = date("Y-m-d", $timestamp);

                $query = $dbo->query("SELECT IDSocio FROM Socio WHERE NumeroDocumento='$documento' AND IDClub='$IDClub'");

                while ($row_socios = mysqli_fetch_array($query)) {
                    $ID = $row_socios["IDSocio"];
                }



                if (!empty($documento)) {
                    $sql_insertar = $dbo->query("INSERT INTO `CarteraBacata`(  `IDClub`, `IDSocio`, `NumeroDocumento`, `Nombre`, `Factura`,`Fecha`, `Cantidad`, `Producto`, `Total_Neto`, `Iva`, `Total`, `FormaPago`) VALUES ( '$IDClub',' $ID','$documento ','$nombre','$factura','$fecha','$cantidad','$producto','$total_neto','$iva','$total','$forma_pago')");
                    $ID = 0;
                }
                $cont++;
            } // END for


            SIMHTML::jsAlert("Subida Exitosa");
            SIMHTML::jsRedirect($script . ".php");
            break;


        case "cartera_cabo_tortuga":

            $frm = SIMUtil::varsLOG($_POST);
            $file = $_FILES['file']['tmp_name'];
            $IDClub = SIMUser::get("club");



            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
            $archivo = $file;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $query1 = $dbo->query("TRUNCATE TABLE CarteraCaboTortuga");
            for ($row = 5; $row <= $highestRow; $row++) {
                $Cliente = $sheet->getCell("A" . $row)->getValue();
                $ValorTotal = $sheet->getCell("B" . $row)->getValue();
                $PorVencer = $sheet->getCell("C" . $row)->getValue();
                $a30 = $sheet->getCell("D" . $row)->getValue();
                $a60 = $sheet->getCell("E" . $row)->getValue();
                $a90 = $sheet->getCell("F" . $row)->getValue();
                $mas90 = $sheet->getCell("G" . $row)->getValue();



                $accion = explode("-", $Cliente);
                $accion[0]; // sacamos el accion
                $accion = explode(" ", $accion[0]); //quitamos el espacio que queda
                $accion = $accion[0] . "-1"; //le ponemos el accion titular -1

                $query = $dbo->query("SELECT IDSocio FROM Socio WHERE Accion='$accion' AND IDClub='$IDClub'");

                while ($row_socios = mysqli_fetch_array($query)) {
                    $ID = $row_socios["IDSocio"];
                }


                if (empty($query)) {

                    $accion1 = str_replace("-1", "", $accion);


                    $query = $dbo->query("SELECT IDSocio FROM Socio WHERE Accion='$accion1' AND IDClub='$IDClub'");

                    while ($row_socios = mysqli_fetch_array($query)) {
                        $ID = $row_socios["IDSocio"];
                    }
                }

                if (!empty($Cliente)) {

                    $sql_insertar = $dbo->query("INSERT INTO `CarteraCaboTortuga`(`IDClub`, `IDSocio`, `Cliente`, `ValorTotal`, `PorVencer`, `1A30`, `31A60`, `61A90`, `MasDe90`) VALUES ( '$IDClub',' $ID','$Cliente ','$ValorTotal','$PorVencer','$a30','$a60','$a90','$mas90' )");
                    $ID = 0;
                }





                $cont++;
            } // END for

            $query = $dbo->query("SELECT Cliente FROM CarteraCaboTortuga WHERE IDSocio='0' AND IDClub='$IDClub'");

            $row_cartera = $dbo->fetchArray($query);


            while ($row_socios = mysqli_fetch_array($query)) {
                $data[] = $row_socios;
            }



            if (json_encode($data) == '[]') {
                SIMHTML::jsAlert("Subida Exitosa");
                SIMHTML::jsRedirect($script . ".php");
            } else {

                SIMHTML::jsAlert("Algunos usuarios no se encontraron en la base de datos, por favor verificar.");

                $query = $dbo->query("SELECT Cliente FROM CarteraCaboTortuga WHERE IDSocio='0' AND IDClub='$IDClub'");



                echo "Estos usuarios no se encontraron en la base de datos, por favor verificar" ?> <br><br> <?
                                                                                                                while ($row_socios = mysqli_fetch_array($query)) {

                                                                                                                    echo  $Cliente = $row_socios["Cliente"];  ?> <br> <?

                                                                                                                                                                    }
                                                                                                                                                                    exit;
                                                                                                                                                                }




                                                                                                                                                                break;



                                                                                                                                                            case "cartera_vencida":






                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $file = $_FILES['file']['tmp_name'];
                                                                                                                                                                $IDClub = SIMUser::get("club");



                                                                                                                                                                require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
                                                                                                                                                                $archivo = $file;
                                                                                                                                                                $inputFileType = PHPExcel_IOFactory::identify($archivo);
                                                                                                                                                                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                                                                                                                                                                $objPHPExcel = $objReader->load($archivo);
                                                                                                                                                                $sheet = $objPHPExcel->getSheet(0);
                                                                                                                                                                $highestRow = $sheet->getHighestRow();
                                                                                                                                                                $highestColumn = $sheet->getHighestColumn();

                                                                                                                                                                $query1 = $dbo->query("TRUNCATE TABLE CarteraVencida");
                                                                                                                                                                for ($row = 2; $row <= $highestRow; $row++) {

                                                                                                                                                                    $id_tercero = $sheet->getCell("A" . $row)->getValue();
                                                                                                                                                                    $nombre_tercero = $sheet->getCell("B" . $row)->getValue();
                                                                                                                                                                    $accion = $sheet->getCell("C" . $row)->getValue();
                                                                                                                                                                    $fecha = $sheet->getCell("D" . $row)->getFormattedValue();
                                                                                                                                                                    $factura = $sheet->getCell("E" . $row)->getValue();
                                                                                                                                                                    $valor = $sheet->getCell("F" . $row)->getValue();



                                                                                                                                                                    $timestamp = strtotime($fecha);
                                                                                                                                                                    $fecha = date("Y-m-d", $timestamp);

                                                                                                                                                                    $query = $dbo->query("SELECT IDSocio FROM Socio WHERE NumeroDocumento='$id_tercero' AND IDClub='$IDClub'");

                                                                                                                                                                    while ($row_socios = mysqli_fetch_array($query)) {
                                                                                                                                                                        $ID = $row_socios["IDSocio"];
                                                                                                                                                                    }



                                                                                                                                                                    if (!empty($id_tercero)) {

                                                                                                                                                                        $sql_insertar = $dbo->query("INSERT INTO `CarteraVencida`(  `IDClub`, `IDSocio`, `Accion`, `IDTercero`, `NombreTercero`, `Factura`,`Fecha`, `Valor`) VALUES ( '$IDClub',' $ID','$accion ','$id_tercero','$nombre_tercero','$factura','$fecha','$valor')");
                                                                                                                                                                        $ID = 0;
                                                                                                                                                                    }   /*
                
                
                 
                 
                echo $accion; echo "--";
                echo $id_tercero;  echo "--";
                echo $nombre_tercero; echo "--";
                echo $factura; echo "--";
                echo $nombre_tipo_cartera; echo "--";
                echo $tc; echo "--";
                echo $fecha; echo "--";
                echo $fecha_pago; echo "--";
                echo $valor; echo "--";
                echo $dias; echo "--";
                echo $vendedor; echo "--";
                echo $nombre_vendedor; echo "--";
                echo $ciudad_residencia; echo "--";
                echo $tel_recidencia; echo "--";
                echo $direccion_recidencia; echo "--";
                echo $zona; echo "--";
                echo $region; echo "--";
                echo $ciudad_correspondencia; echo "--";
                echo $tel_correspondencia; echo "--";
                echo $direccion_correspondencia; echo "--";
                echo $celular; echo "--";
                */



                                                                                                                                                                    $cont++;
                                                                                                                                                                } // END for

                                                                                                                                                                SIMHTML::jsAlert("Subida Exitosa");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php");
                                                                                                                                                                break;


                                                                                                                                                            case "crearqr":
                                                                                                                                                                $configuracionClub = $dbo->getFields('ConfiguracionClub', 'DatosCarne', 'IDClub = ' . SIMUser::get('club'));
                                                                                                                                                                $sql_socio = "Select * From Socio Where IDClub = '" . $_POST["IDClub"] . "' and CodigoQR=''";
                                                                                                                                                                $result_socio = $dbo->query($sql_socio);
                                                                                                                                                                while ($row_socio = $dbo->fetchArray($result_socio)) :
                                                                                                                                                                    if ($configuracionClub == 'Contacto') {
                                                                                                                                                                        $codeContents  = 'BEGIN:VCARD' . "\n";
                                                                                                                                                                        $codeContents .= 'VERSION:2.1' . "\n";
                                                                                                                                                                        $codeContents .= 'N:' . $row_socio['Nombre'] . ' ' . $row_socio['Apellido'] . "\n";
                                                                                                                                                                        $codeContents .= 'FN:' . $row_socio['Nombre'] . ' ' . $row_socio['Apellido'] . "\n";
                                                                                                                                                                        $codeContents .= 'TEL;WORK;VOICE:' . $row_socio['Telefono'] . "\n";
                                                                                                                                                                        $codeContents .= 'TEL;TYPE=cell:' . $row_socio['Telefono'] . "\n";
                                                                                                                                                                        $codeContents .= 'EMAIL:' . $row_socio['CorreoElectronico'] . "\n";
                                                                                                                                                                        $codeContents .= 'END:VCARD';
                                                                                                                                                                    } else if ($configuracionClub == 'Codigocarne') {
                                                                                                                                                                        $codeContents = $row_socio['CodigoCarne'] . "\n";
                                                                                                                                                                    } else if ($configuracionClub == 'AccionPadre') {
                                                                                                                                                                        $codeContents = $row_socio['AccionPadre'] . "\n";
                                                                                                                                                                    } else if ($configuracionClub == 'Accion') {
                                                                                                                                                                        $codeContents = $row_socio['Accion'] . "\n";
                                                                                                                                                                    } else {
                                                                                                                                                                        $codeContents = $row_socio['NumeroDocumento'] . "\n";
                                                                                                                                                                    }
                                                                                                                                                                    if (SIMUser::get('club') == 206) {
                                                                                                                                                                        $codeContents = $row_socio['NumeroDocumento'] . $row_socio['Nombre'] . $row_socio['Apellido'] . $row_socio['TipoSocio'];
                                                                                                                                                                    }

                                                                                                                                                                    $parametros_codigo_qr = $codeContents;
                                                                                                                                                                    $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($row_socio[IDSocio], $parametros_codigo_qr);
                                                                                                                                                                    $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $row_socio["IDSocio"] . "' AND IDClub = '" . SIMUser::get('club') . "'");
                                                                                                                                                                endwhile;
                                                                                                                                                                SIMHTML::jsAlert("Se creo codigo qr a todos los socios.");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=search");
                                                                                                                                                                break;

                                                                                                                                                            case "actualizarqr":
                                                                                                                                                                $configuracionClub = $dbo->getFields('ConfiguracionClub', 'DatosCarne', 'IDClub = ' . SIMUser::get('club'));
                                                                                                                                                                $sql_socio = "Select * From Socio Where IDClub = '" . $_POST["IDClub"] . "'";
                                                                                                                                                                $result_socio = $dbo->query($sql_socio);
                                                                                                                                                                while ($row_socio = $dbo->fetchArray($result_socio)) :
                                                                                                                                                                    if ($configuracionClub == 'Contacto') {
                                                                                                                                                                        $codeContents  = 'BEGIN:VCARD' . "\n";
                                                                                                                                                                        $codeContents .= 'VERSION:2.1' . "\n";
                                                                                                                                                                        $codeContents .= 'N:' . $row_socio['Nombre'] . ' ' . $row_socio['Apellido'] . "\n";
                                                                                                                                                                        $codeContents .= 'FN:' . $row_socio['Nombre'] . ' ' . $row_socio['Apellido'] . "\n";
                                                                                                                                                                        $codeContents .= 'TEL;WORK;VOICE:' . $row_socio['Telefono'] . "\n";
                                                                                                                                                                        $codeContents .= 'TEL;TYPE=cell:' . $row_socio['Telefono'] . "\n";
                                                                                                                                                                        $codeContents .= 'EMAIL:' . $row_socio['CorreoElectronico'] . "\n";
                                                                                                                                                                        $codeContents .= 'ORG:' . ($row_socio['Empresa']) . "\n";
                                                                                                                                                                        $codeContents .= 'TITLE:' .  ($row_socio['CargoSocio']) . "\n";
                                                                                                                                                                        $codeContents .= 'END:VCARD';
                                                                                                                                                                    } else if ($configuracionClub == 'Codigocarne') {
                                                                                                                                                                        $codeContents = $row_socio['CodigoCarne'] . "\n";
                                                                                                                                                                    } else if ($configuracionClub == 'AccionPadre') {
                                                                                                                                                                        $codeContents = $row_socio['AccionPadre'] . "\n";
                                                                                                                                                                    } else if ($configuracionClub == 'Accion') {
                                                                                                                                                                        $codeContents = $row_socio['Accion'] . "\n";
                                                                                                                                                                    } else {
                                                                                                                                                                        $codeContents = $row_socio['NumeroDocumento'] . "\n";
                                                                                                                                                                    }
                                                                                                                                                                    if (SIMUser::get('club') == 206) {
                                                                                                                                                                        // $codeContents = $row_socio['NumeroDocumento'] . $row_socio['Nombre'] . $row_socio['Apellido'] . $row_socio['TipoSocio'];
                                                                                                                                                                        $codeContents = $row_socio['NumeroDocumento'];
                                                                                                                                                                    }
                                                                                                                                                                    $parametros_codigo_qr = $codeContents;
                                                                                                                                                                    $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($row_socio[IDSocio], $parametros_codigo_qr);
                                                                                                                                                                    $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $row_socio["IDSocio"] . "' AND IDClub = '" . SIMUser::get('club') . "'");
                                                                                                                                                                endwhile;
                                                                                                                                                                SIMHTML::jsAlert("Se actualizo codigo qr a todos los socios.");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=search");
                                                                                                                                                                break;

                                                                                                                                                            case "actualizarclave":
                                                                                                                                                                $sql_inactiva = "UPDATE Socio Set CambioClave='" . $_POST["CambiarClave"] . "', UsuarioTrEd='admin modulo socio plano clave',FechaTrEd=NOW() WHERE IDClub = '" . $_POST["IDClub"] . "'";
                                                                                                                                                                $dbo->query($sql_inactiva);
                                                                                                                                                                SIMHTML::jsAlert("Socios actualizados con cambiar clave");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=search");
                                                                                                                                                                break;

                                                                                                                                                            case "actualizarperfil":
                                                                                                                                                                $sql_inactiva = "UPDATE Socio Set SolicitaEditarPerfil='" . $_POST["EditaPerfil"] . "', UsuarioTrEd='admin modulo socio plano perfil',FechaTrEd=NOW() WHERE IDClub = '" . $_POST["IDClub"] . "'";
                                                                                                                                                                $dbo->query($sql_inactiva);
                                                                                                                                                                SIMHTML::jsAlert("Socios actualizados con editar perfil");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=search");
                                                                                                                                                                break;

                                                                                                                                                            case "actualizarperfilfoto":
                                                                                                                                                                $sql_inactiva = "UPDATE Socio Set SolicitarCambioFotoPerfil='" . $_POST["EditaPerfilFoto"] . "', UsuarioTrEd='admin modulo socio plano perfil',FechaTrEd=NOW() WHERE IDClub = '" . $_POST["IDClub"] . "'";
                                                                                                                                                                $dbo->query($sql_inactiva);
                                                                                                                                                                SIMHTML::jsAlert("Socios actualizados con editar foto");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=search");
                                                                                                                                                                break;

                                                                                                                                                            case "update-vacuna":
                                                                                                                                                                //los campos al final de las tablas
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);

                                                                                                                                                                $dbo = &SIMDB::get();
                                                                                                                                                                $query = $dbo->query("SELECT V.IDVacuna FROM Socio S LEFT JOIN Vacuna V ON S.IDSocio=V.IDSocio WHERE S.IDSocio=" . $frm['IDSocio']);
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

                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&id={$frm['IDSocio']}");
                                                                                                                                                                break;
                                                                                                                                                            case "update-vacuna2":
                                                                                                                                                                //los campos al final de las tablas

                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $dbo = &SIMDB::get();

                                                                                                                                                                $frm['IDVacuna'] = $frm['IDVacuna'][0];
                                                                                                                                                                $frm['IDDosis'] = $frm['IDDosis'][0];
                                                                                                                                                                $r_campos = &$dbo->all("CampoVacunacion", "IDClub = '" . SIMUser::get('club')  . "' AND Publicar = 'S'");
                                                                                                                                                                $frm['IDVacunaMarca'] = $dbo->getFields('VacunaMarca', 'IDVacunaMarca', 'Nombre ="' . $frm['Marca'] . '"');
                                                                                                                                                                $response = array();
                                                                                                                                                                while ($r = $dbo->object($r_campos)) {
                                                                                                                                                                    if ($_POST["Campo|" . $r->IDCampoVacunacion] != '') {
                                                                                                                                                                        $array_dinamicos["IDCampo"] = $r->IDCampoVacunacion;
                                                                                                                                                                        $array_dinamicos["Valor"] = $_POST["Campo|" . $r->IDCampoVacunacion];
                                                                                                                                                                        array_push($response, $array_dinamicos);
                                                                                                                                                                    }
                                                                                                                                                                }

                                                                                                                                                                $ValoresFormulario = json_encode($response);
                                                                                                                                                                $UsuarioCrea = "ADMIN " . SIMUser::get("IDUsuario");


                                                                                                                                                                $respuesta = SIMWebServiceVacunacion::set_vacunacionv2($frm['IDClub'], $frm['IDSocio'], $frm['IDUsuario'], $frm['IDVacunaMarca'], $frm['Lugar'], $frm['Marca'], $frm['EntidadDosis'], $frm['FechaCitaVacuna'], '', $_FILES, '', $ValoresFormulario, $frm['IDDosis'], '');



                                                                                                                                                                if ($respuesta['success']) {
                                                                                                                                                                    SIMHTML::jsAlert($respuesta['message']);
                                                                                                                                                                    SIMHTML::jsRedirect($script . ".php?action=edit&id={$frm['IDSocio']}&tabsocio=vacuna2");
                                                                                                                                                                }
                                                                                                                                                                break;

                                                                                                                                                            case "update-Archivo-vacunado":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $dbo = &SIMDB::get();

                                                                                                                                                                $datosUser = $dbo->fetchAll("Usuario", " IDUsuario = '" . SIMUser::get('IDUsuario') . "' ", "array");
                                                                                                                                                                $DatosVacunado = $dbo->fetchAll("Vacunado", "IDSocio = '" . $frm['IDSocio'] . "'", "array");
                                                                                                                                                                $deseoVacuna = (!empty($frm['DeseoVacuna'])) ? $frm['DeseoVacuna'] : 'si';

                                                                                                                                                                // SI NO RESPONDIO LA PREGUNTA DE DESEAR ESTAR VACUNADO DAMOS POR ECHO DE QUE SI LO DESEA
                                                                                                                                                                if (empty($DatosVacunado)) :
                                                                                                                                                                    $sql = "INSERT INTO Vacunado (IDSocio, DeseoVacuna, UsuarioTrCr, FechaTrCr) VALUES (" . $frm['IDSocio'] . ",'" . $deseoVacuna . "','" . $datosUser['Nombre'] . "',NOW())";
                                                                                                                                                                    $dbo->query($sql);
                                                                                                                                                                    $Vacunado = $dbo->lastID();
                                                                                                                                                                else :
                                                                                                                                                                    $Vacunado = $DatosVacunado['IDVacunado'];
                                                                                                                                                                endif;

                                                                                                                                                                if ($_FILES['CertificadoDigital']['name'] != '') {
                                                                                                                                                                    //Valido el pseo del archivo
                                                                                                                                                                    $tamano_archivo = $_FILES["CertificadoDigital"]['size'];
                                                                                                                                                                    if ($tamano_archivo >= 6000000) {
                                                                                                                                                                        SIMHTML::jsAlert("El archivo supera el limite de peso permitido de 6 megas, por favor verifique");
                                                                                                                                                                        SIMHTML::jsRedirect("?action=edit&id=" . $frm['IDSocio'] . "&tabsocio=vacuna2");
                                                                                                                                                                        return $respuesta;
                                                                                                                                                                    }
                                                                                                                                                                    //UPLOAD de imagenes
                                                                                                                                                                    $files = SIMFile::upload($_FILES['CertificadoDigital'], VACUNA_DIR, "");
                                                                                                                                                                    if (empty($files) && !empty($_FILES["CertificadoDigital"]["name"])) :
                                                                                                                                                                        SIMHTML::jsAlert("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.");
                                                                                                                                                                        SIMHTML::jsRedirect("?action=edit&id=" . $frm['IDSocio'] . "&tabsocio=vacuna2");
                                                                                                                                                                        return $respuesta;
                                                                                                                                                                    endif;
                                                                                                                                                                    $Archivo = $files[0]["innername"];


                                                                                                                                                                    // ACTUALIZAMOS EL ARCHIVO
                                                                                                                                                                    $ActulizarArchivo = "UPDATE Vacunado SET ArchivoVacuna='" . $Archivo . "'  WHERE IDVacunado = $Vacunado";
                                                                                                                                                                    $dbo->query($ActulizarArchivo);
                                                                                                                                                                }

                                                                                                                                                                $ActulizarArchivo = "UPDATE Vacunado SET DeseoVacuna = '" . $deseoVacuna . "', FechaTrEd = NOW(), UsuarioTrEd = '" . $datosUser['Nombre'] . "'  WHERE IDVacunado = $Vacunado";
                                                                                                                                                                if ($dbo->query($ActulizarArchivo)) {
                                                                                                                                                                    SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                    SIMHTML::jsRedirect("?action=edit&id=" . $frm['IDSocio'] . "&tabsocio=vacuna2");
                                                                                                                                                                }
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

                                                                                                                                                            case "del-vacuna2-image":
                                                                                                                                                                $archivo = $_GET['archivo'];
                                                                                                                                                                $numImagen = $_GET['num_img'];
                                                                                                                                                                $campo = $_GET['campo'];
                                                                                                                                                                $id = $_GET['id'];
                                                                                                                                                                $idSocio = $_GET['IDSocio'];
                                                                                                                                                                $filedelete = VACUNA_DIR . $archivo;
                                                                                                                                                                unlink($filedelete);
                                                                                                                                                                $queryUpdate = "UPDATE Vacuna2 SET Certificado=NULL WHERE IDVacuna=$id";
                                                                                                                                                                $dbo->query($queryUpdate);
                                                                                                                                                                SIMHTML::jsAlert("Imagen Eliminada Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $idSocio . "&tabsocio=vacuna2");
                                                                                                                                                                break;
                                                                                                                                                            case "del-archivo-vacunado":
                                                                                                                                                                $archivo = $_GET['archivo'];
                                                                                                                                                                $numImagen = $_GET['num_img'];
                                                                                                                                                                $campo = $_GET['campo'];
                                                                                                                                                                $id = $_GET['id'];
                                                                                                                                                                $idSocio = $_GET['IDSocio'];
                                                                                                                                                                $filedelete = VACUNA_DIR . $archivo;
                                                                                                                                                                unlink($filedelete);
                                                                                                                                                                $queryUpdate = "UPDATE Vacunado SET ArchivoVacuna=NULL WHERE IDVacunado=$id";
                                                                                                                                                                $dbo->query($queryUpdate);
                                                                                                                                                                SIMHTML::jsAlert("Archivo Eliminado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $idSocio . "&tabsocio=vacuna2");
                                                                                                                                                                break;

                                                                                                                                                            case "del-vacunaCampoVacuncion2-image":
                                                                                                                                                                $archivo = $_GET['archivo'];
                                                                                                                                                                $numImagen = $_GET['num_img'];
                                                                                                                                                                $campo = $_GET['campo'];
                                                                                                                                                                $id = $_GET['id'];
                                                                                                                                                                $idSocio = $_GET['IDSocio'];
                                                                                                                                                                $filedelete = VACUNA_DIR . $archivo;
                                                                                                                                                                unlink($filedelete);
                                                                                                                                                                $queryUpdate = "UPDATE VacunaCampoVacunacion2 SET Valor=NULL WHERE IDVacuna=$id AND IDCampoVacunacion = $campo";
                                                                                                                                                                $dbo->query($queryUpdate);
                                                                                                                                                                SIMHTML::jsAlert("Imagen Eliminada Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $idSocio . "&tabsocio=vacuna2");
                                                                                                                                                                break;

                                                                                                                                                            case "InsertarMascota":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $id = $dbo->insert($frm, 'Mascota', 'IDMascota');

                                                                                                                                                                //UPLOAD de imagenes
                                                                                                                                                                if (isset($_FILES)) {

                                                                                                                                                                    if (!empty($_FILES['Foto']['name'])) {
                                                                                                                                                                        $files = SIMFile::upload($_FILES["Foto"], SOCIO_DIR, "IMAGE");
                                                                                                                                                                        if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                                                                                                                                                                            SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                                                                                                                                                                        }

                                                                                                                                                                        $frm["Foto"] = $files[0]["innername"];
                                                                                                                                                                    }

                                                                                                                                                                    if (!empty($_FILES['FotoVacuna']['name'])) {
                                                                                                                                                                        $files = SIMFile::upload($_FILES["FotoVacuna"], SOCIO_DIR, "IMAGE");
                                                                                                                                                                        if (empty($files) && !empty($_FILES["FotoVacuna"]["name"])) {
                                                                                                                                                                            SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                                                                                                                                                                        }

                                                                                                                                                                        $frm["FotoVacuna"] = $files[0]["innername"];
                                                                                                                                                                    }
                                                                                                                                                                } //end if

                                                                                                                                                                $id = $dbo->update($frm, 'Mascota', 'IDMascota', $id);

                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=mascotas&id={$frm['IDSocio']}");
                                                                                                                                                                break;

                                                                                                                                                            case "ModificarMascota":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);

                                                                                                                                                                //UPLOAD de imagenes
                                                                                                                                                                if (isset($_FILES)) {

                                                                                                                                                                    if (!empty($_FILES['Foto']['name'])) {
                                                                                                                                                                        $files = SIMFile::upload($_FILES["Foto"], SOCIO_DIR, "IMAGE");
                                                                                                                                                                        if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                                                                                                                                                                            SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                                                                                                                                                                        }

                                                                                                                                                                        $frm["Foto"] = $files[0]["innername"];
                                                                                                                                                                    }

                                                                                                                                                                    if (!empty($_FILES['FotoVacuna']['name'])) {
                                                                                                                                                                        $files = SIMFile::upload($_FILES["FotoVacuna"], SOCIO_DIR, "IMAGE");
                                                                                                                                                                        if (empty($files) && !empty($_FILES["FotoVacuna"]["name"])) {
                                                                                                                                                                            SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                                                                                                                                                                        }

                                                                                                                                                                        $frm["FotoVacuna"] = $files[0]["innername"];
                                                                                                                                                                    }
                                                                                                                                                                }

                                                                                                                                                                $id = $dbo->update($frm, 'Mascota', 'IDMascota', $frm['IDMascota']);

                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=mascotas&id={$frm['IDSocio']}");
                                                                                                                                                                break;

                                                                                                                                                            case "DelMascotaFoto":
                                                                                                                                                                $archivo = $_GET['archivo'];
                                                                                                                                                                $campo = $_GET['campo'];
                                                                                                                                                                $id = $_GET['id'];
                                                                                                                                                                $idSocio = $_GET['IDSocio'];
                                                                                                                                                                $filedelete = SOCIO_DIR . $archivo;
                                                                                                                                                                unlink($filedelete);
                                                                                                                                                                $queryUpdate = "UPDATE Mascota SET Foto$campo" . "=NULL WHERE IDMascota=$id";
                                                                                                                                                                $dbo->query($queryUpdate);
                                                                                                                                                                SIMHTML::jsAlert("Imagen Eliminada Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $idSocio . "&IDMascota=$id&tabsocio=mascotas");
                                                                                                                                                                break;

                                                                                                                                                            case "EliminarMascota":
                                                                                                                                                                $consult = $dbo->query("SELECT Foto, FotoVacuna FROM Mascota WHERE IDMascota=$id");
                                                                                                                                                                $consult = $dbo->fetch($consult);

                                                                                                                                                                unlink(SOCIO_DIR . $consult["Foto"]);
                                                                                                                                                                unlink(SOCIO_DIR . $consult["FotoVacuna"]);

                                                                                                                                                                $id = $_GET["id"];
                                                                                                                                                                $idSocio = $_GET["IDSocio"];
                                                                                                                                                                $dbo->query("DELETE FROM Mascota WHERE IDMascota=$id");

                                                                                                                                                                SIMHTML::jsAlert("Registro Eliminado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?" . "action=edit&id=" . $idSocio . "" . "&tabsocio=mascotas");
                                                                                                                                                                break;

                                                                                                                                                            case "InsertarSocioAusente":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $id = $dbo->insert($frm, 'SocioAusente', 'IDSocioAusente');
                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=ausente&id={$frm['IDSocio']}");
                                                                                                                                                                break;

                                                                                                                                                            case "ModificarSocioAusente":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $id = $dbo->update($frm, 'SocioAusente', 'IDSocioAusente', $frm['IDSocioAusente']);

                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=ausente&id={$frm['IDSocio']}");
                                                                                                                                                                break;

                                                                                                                                                            case "EliminarSocioAusente":

                                                                                                                                                                $id = $_GET["id"];
                                                                                                                                                                $idSocio = $_GET["IDSocio"];
                                                                                                                                                                $dbo->query("DELETE FROM SocioAusente WHERE IDSocioAusente=$id");

                                                                                                                                                                SIMHTML::jsAlert("Registro Eliminado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?" . "action=edit&id=" . $idSocio . "" . "&tabsocio=ausente");
                                                                                                                                                                break;

                                                                                                                                                            case "InsertarSocioHabitacion":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);

                                                                                                                                                                $frm[Noches] = $frm[NumeroFracciones] * 44;
                                                                                                                                                                $frm[Estadias] = $frm[NumeroFracciones] * 6;

                                                                                                                                                                $id = $dbo->insert($frm, 'SocioHabitacion', 'IDSocioHabitacion');
                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=habitaciones&id={$frm['IDSocio']}");
                                                                                                                                                                break;

                                                                                                                                                            case "ModificaSocioHabitacion":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $id = $dbo->update($frm, 'SocioHabitacion', 'IDSocioHabitacion', $frm['IDSocioHabitacion']);

                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=habitaciones&id={$frm['IDSocio']}");
                                                                                                                                                                break;

                                                                                                                                                            case "EliminaSocioHabitacion":

                                                                                                                                                                $id = $_GET["IDSocioHabitacion"];
                                                                                                                                                                $idSocio = $_GET["id"];
                                                                                                                                                                $dbo->query("DELETE FROM SocioHabitacion WHERE IDSocioHabitacion=$id");

                                                                                                                                                                SIMHTML::jsAlert("Registro Eliminado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?" . "action=edit&id=" . $idSocio . "" . "&tabsocio=habitaciones");
                                                                                                                                                                break;

                                                                                                                                                            case "InsertarTicketDescuento":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $id = $dbo->insert($frm, 'TicketDescuento', 'IDTicketDescuento');

                                                                                                                                                                $Servicio = explode("|||", $frm[SeleccionServicios]);

                                                                                                                                                                foreach ($Servicio as $Servicio) :
                                                                                                                                                                    $Datos = explode("-", $Servicio);
                                                                                                                                                                    if ($Datos[1] > 0) :
                                                                                                                                                                        $insert = "INSERT INTO  TicketDescuentoServicio (IDTicketDescuento, IDServicio) VALUES ($id,$Datos[1])";
                                                                                                                                                                        $dbo->query($insert);
                                                                                                                                                                    endif;
                                                                                                                                                                endforeach;

                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=habitaciones&id={$frm['IDSocio']}");
                                                                                                                                                                break;

                                                                                                                                                            case "ModificaTicketDescuento":
                                                                                                                                                                $frm = SIMUtil::varsLOG($_POST);
                                                                                                                                                                $id = $dbo->update($frm, 'TicketDescuento', 'IDTicketDescuento', $frm['IDTicketDescuento']);

                                                                                                                                                                $Servicio = explode("|||", $frm[SeleccionServicios]);

                                                                                                                                                                foreach ($Servicio as $Servicio) :
                                                                                                                                                                    $Datos = explode("-", $Servicio);
                                                                                                                                                                    if ($Datos[1] > 0) :
                                                                                                                                                                        $filedelete = "DELETE FROM  TicketDescuentoServicio WHERE IDServicio = $Datos[1] AND IDTicketDescuento = $frm[IDTicketDescuento]";
                                                                                                                                                                        $dbo->query($delete);

                                                                                                                                                                        $insert = "INSERT INTO  TicketDescuentoServicio (IDTicketDescuento, IDServicio) VALUES ($id,$Datos[1])";
                                                                                                                                                                        $dbo->query($insert);
                                                                                                                                                                    endif;
                                                                                                                                                                endforeach;

                                                                                                                                                                SIMHTML::jsAlert("Registro Guardado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?action=edit&tabsocio=habitaciones&id={$frm['IDSocio']}");
                                                                                                                                                                break;

                                                                                                                                                            case "EliminaTicketDescuento":

                                                                                                                                                                $id = $_GET["IDTicketDescuento"];
                                                                                                                                                                $idSocio = $_GET["id"];
                                                                                                                                                                $dbo->query("DELETE FROM TicketDescuento WHERE IDTicketDescuento=$id");
                                                                                                                                                                $dbo->query("DELETE FROM TicketDescuentoServicio WHERE IDTicketDescuento=$id");

                                                                                                                                                                SIMHTML::jsAlert("Registro Eliminado Correctamente");
                                                                                                                                                                SIMHTML::jsRedirect($script . ".php?" . "action=edit&id=" . $idSocio . "" . "&tabsocio=tickets");
                                                                                                                                                                break;


                                                                                                                                                            default:
                                                                                                                                                                $view = "views/" . $script . "/form.php";
                                                                                                                                                                $newmode = "insert";
                                                                                                                                                                $titulo_accion = "Crear";
                                                                                                                                                                break;
                                                                                                                                                        } // End switch

                                                                                                                                                        if (empty($view)) {
                                                                                                                                                            $view = "views/" . $script . "/form.php";
                                                                                                                                                        }

                                                                                                                                                                        ?>