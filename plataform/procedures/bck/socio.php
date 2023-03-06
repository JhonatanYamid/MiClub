 <?php
    include(LIBDIR . 'SIMWebServiceVacunacion.inc.php');

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

        $sql_paren = "SELECT IDParentesco,Nombre FROM Parentesco WHERE Publicar='S'";
        $r_paren = $dbo->query($sql_paren);
        while ($row_paren = $dbo->fetchArray($r_paren)) {
            $array_paren[strtoupper($row_paren["Nombre"])] = $row_paren["IDParentesco"];
        }

        if ($IDClub == 44) {
            $Update = "UPDATE Socio SET IDEstadoSocio = 2 WHERE IDClub = 44 AND TipoSocio <> 'Invitado' AND TipoSocio <> 'Niñera'";
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
            $CantidadAusencias = $sheet->getCell("V" . $row)->getValue();
            $FechaInicioAusencia = $sheet->getCell("W" . $row)->getValue();
            $CodigoCarne = $sheet->getCell("X" . $row)->getValue();

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
                    strtoupper(trim($Estado)) == "MOROSO SIN ACCESO AL APP" || strtoupper(trim($Estado)) == "MOROSO CON ACCESO APP" || strtoupper(trim($Estado)) == "ACTIVO SIN CARGO A SOCIO"
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
										NumeroInvitados = '" . $Invitaciones . "',NumeroAccesos = '" . $Accesos . "',PermiteReservar = '" . $PermiteReservar . "', Celular='" . $Celular . "',
										TipoSocio='" . $TipoSocio . "', SocioAusente = ' " . $Ausente . "', CantidadAusencias = ' " . $CantidadAusencias . "', FechaInicioAusencia = ' " . $CantidadAusencias . "', FechaTrEd = NOW(),
										FechaInicioInvitado='" . $FechaInicioInvitado . "',FechaFinInvitado='" . $FechaFinInvitado . "', CodigoCarne = '" . $CodigoCarne . "' " . $actualiza_categoria . $cerrarSesion . "
										Where IDSocio = '" . $IDSocio . "'";

                        //echo "<br>Editar";
                        //echo "<br>" . $sql_edit_socio;
                        //exit;
                        $dbo->query($sql_edit_socio);
                        $numregok++;

                    else :

                        if (!empty($UsuarioApp) && !empty($ClaveApp)) {
                            //Crear Socio
                            $sql_inserta_socio = "INSERT INTO Socio(IDClub,IDEstadoSocio, IDCategoria, IDParentesco, Accion,AccionPadre, Nombre, Apellido, FechaNacimiento, NumeroDocumento, CorreoElectronico, Email, Clave, Telefono, Celular,Predio,PermiteReservar,NumeroInvitados,NumeroAccesos, TipoSocio, Direccion,FechaInicioInvitado,FechaFinInvitado,UsuarioTrCr, FechaTrCr, SocioAusente, CantidadAusencias, CodigoCarne )
	                                Values ('" . $IDClub . "','" . $IDEstadoSocio . "','" . $IDCategoria . "','" . $IDParentesco . "','" . $Accion . "','" . $AccionPadre . "','" . $Nombre . "','" . $Apellido . "','" . $FechaNacimiento . "','" . $NumeroDocumento . "','" . $Email . "', '" . $UsuarioApp . "',sha1('" . $ClaveApp . "'),'" . $Telefono . "','" . $Celular . "'
	                                ,'" . $Lote . "','" . $PermiteReservar . "','" . $Invitaciones . "','" . $Accesos . "','" . $TipoSocio . "','" . $Direccion . "','" . $FechaInicioInvitado . "','" . $FechaFinInvitado . "', 'Archivo Plano: " . $nombrearchivo . "',NOW(), '" . $Ausente . "', '" . $CantidadAusencias . "', '" . $CodigoCarne . "')";
                            //echo "<br>Crear ";
                            //echo "<br>" . $sql_inserta_socio;
                            //exit;
                            $dbo->query($sql_inserta_socio);
                            $numregok++;
                        } else {
                            echo "<br>" . "Falta la columna de usuario y clave a:" . $NumeroDocumento;
                        }

                    endif;
                } else {
                    echo "<br>" . "El Estado  tiene un valor invalido: " . $NumeroDocumento . "Estado: -" . strtoupper($Estado) . "-";
                }
            } else {
                echo "<br>" . "El numero de documento debe ser numerico: " . $NumeroDocumento;
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

                if (!empty($Accion)) {

                    $sql_socio = "SELECT IDSocio FROM Socio WHERE IDClub = " . $IDClub . " AND Accion = '" . $Accion . "'";
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

                $comprobar_correo = $dbo->fetchAll("Socio", "(Email = '" . $frm[Email] . "' or NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
                if (!empty($comprobar_correo[IDSocio])) :
                    SIMHTML::jsAlert("Error: Ya existe  el email o el documento en este club, por favor verifique");
                    SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $frm[ID] . "#Socio");
                    exit;
                endif;

                //UPLOAD de imagenes
                if (isset($_FILES)) {
                    $files = SIMFile::upload($_FILES["Foto"], SOCIO_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    }

                    $frm["Foto"] = $files[0]["innername"];
                } //end if

                //insertamos los datos
                $id = $dbo->insert($frm, $table, $key);

                //Bijao Si se cambia a no aceptado o anulado envio correo
                if ($frm[IDClub] == 32) :
                    $envio_respuesta = SIMUtil::envio_respuesta_registro($id, $frm["IDEstadoSocio"], $frm[Email], $ClaveOriginal);
                endif;

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
                endforeach;

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
                        $codeContents = $frm['NumeroDocumento'] . "\n";
                    }
                    $parametros_codigo_qr = $codeContents;
                endif;

                $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' Where IDSocio = '" . $id . "'");

                if ($frm[IDClub] == 27) : //Payande
                    $respuesta = SIMWebServiceApp::actualiza_payande($id);
                //print_r($respuesta);
                endif;

                SIMHTML::jsRedirect("socios.php?m=insertarexito");
            } else {
                exit;
            }

            break;

        case "update":
            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
                //los campos al final de las tablas
                $frm = SIMUtil::varsLOG($_POST);

                $frm[IDPais] = 0;
                $frm[IDClub] = SIMUser::get("club");
                $frm[IDDepartamento] = 0;
                $frm[IDCiudad] = 0;
                $frm[IDNacionalidad] = 0;
                $ClaveOriginal = $frm[Clave];

                if ($frm[Clave] != $frm[ClaveAnt]) {
                    $frm[Clave] = sha1($frm[Clave]);
                }

                if ($frm[SegundaClave] != $frm[SegundaClaveAnt]) {
                    $frm[SegundaClave] = sha1($frm[SegundaClave]);
                }

                //Compruebo que no exista el correo
                if ($frm[Email] != $frm[EmailAnt]) :
                    $comprobar_correo = $dbo->fetchAll("Socio", "(Email = '" . $frm[Email] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
                    if (!empty($comprobar_correo[IDSocio])) :
                        SIMHTML::jsAlert("Error: Ya existe  el email en este club, por favor verifique");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $frm[ID]);
                        exit;
                    endif;
                endif;

                //Compruebo que no exista el documento
                if ($frm[NumeroDocumento] != $frm[NumeroDocumentoAnt]) :
                    $comprobar_correo = $dbo->fetchAll("Socio", "(NumeroDocumento = '" . $frm[NumeroDocumento] . "') and IDClub = '" . $frm[IDClub] . "' ", "array");
                    if (!empty($comprobar_correo[IDSocio])) :
                        SIMHTML::jsAlert("Error: Ya existe  el numero de documento en este club, por favor verifique");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $frm[ID]);
                        exit;
                    endif;
                endif;

                //UPLOAD de imagenes
                if (isset($_FILES)) {
                    $files = SIMFile::upload($_FILES["Foto"], SOCIO_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    }

                    $frm["Foto"] = $files[0]["innername"];
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
                elseif ($frm[IDClub] == 88) :
                    $parametros_codigo_qr = "https://www.miclubapp.com/plataform/reportereservassocio.php?action=search&NumeroDocumento=" . $frm["NumeroDocumento"];
                elseif ($frm[IDClub] == 10) :
                    $parametros_codigo_qr = $frm["ObservacionGeneral"];
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
                        $codeContents = $frm['NumeroDocumento'] . "\n";
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

                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

                if ($frm[IDClub] == 27) : //Payande
                    $respuesta = SIMWebServiceApp::actualiza_payande(SIMNet::reqInt("id"));
                //print_r($respuesta);
                endif;

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

                $frm = $dbo->fetchById($table, $key, $id, "array");
                SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
                SIMHTML::jsAlert("Registro Guardado Correctamente");
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
            } else {
                exit;
            }

            break;

        case "edit":
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
            $view = "views/socios/list.php";
            break;

        case "InsertarVehiculo":
            $frm = SIMUtil::varsLOG($_POST);
            //Verifico que no exista la placa
            $placa = $dbo->getFields("Vehiculo", "Placa", "Placa = '" . $frm[Placa] . "' and IDSocio = '" . $frm["ID"] . "'");
            if (empty($placa)) :
                $id = $dbo->insert($frm, "Vehiculo", "IDVehiculo");
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
                } else {
                    $codeContents = $row_socio['NumeroDocumento'] . "\n";
                }
                $parametros_codigo_qr = $codeContents;
                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($row_socio[IDSocio], $parametros_codigo_qr);
                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $row_socio["IDSocio"] . "' AND IDClub = '" . SIMUser::get('club') . "'");
            endwhile;
            SIMHTML::jsAlert("Socios actualizados con permitir hacer domicilios en No con exito");
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
                    $codeContents .= 'END:VCARD';
                } else {
                    $codeContents = $row_socio['NumeroDocumento'] . "\n";
                }
                $parametros_codigo_qr = $codeContents;
                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($row_socio[IDSocio], $parametros_codigo_qr);
                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $row_socio["IDSocio"] . "' AND IDClub = '" . SIMUser::get('club') . "'");
            endwhile;
            SIMHTML::jsAlert("Socios actualizados con permitir hacer domicilios en No con exito");
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

        default:
            $newmode = "insert";
            $titulo_accion = "Crear";
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
    } // End switch

    if (empty($view)) {
        $view = "views/socios/form.php";
    }

    ?>
