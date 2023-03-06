 <?php
    $tipoReporte = 'Socio';
    SIMReg::setFromStructure(array(
        "title" => "Reporte encuesta arbol",
        "table" => "CuotasSociales",
        "key" => "IDDiagnostico",
        "mod" => "Historial Socios",

    ));
    $script = "reporteencuestaarbol1";

    //extraemos las variables
    $table = SIMReg::get("table");
    $key = SIMReg::get("key");
    $mod = SIMReg::get("mod");

    $frm = SIMUtil::varsLOG($_POST);
    $frm_get = SIMUtil::varsLOG($_GET);
    // echo '<pre>';
    // var_dump($frm_get);
    // die();
    //Verificar permisos
    SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

    $IDClub = SIMReg::get("club");

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

                SIMHTML::jsAlert("Registro Guardado Correctamente");
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

                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

                $frm = $dbo->fetchById($table, $key, $id, "array");

                SIMHTML::jsAlert("Registro Guardado Correctamente");
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
            } else
                exit;

            break;

        case "search":
            $IDEncuestaArbol = 9;
            $where = "";
            $where_filtro = "";
            $sqlGraficas = "SELECT C.Nombre as Ciudad,S.IDSocio, U.IDUsuario, CONCAT( S.Nombre, ' ',S.Apellido) AS NombreSocio, U.Nombre AS NombreFuncionario,
					P.IDPreguntaEncuestaArbol,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,CONCAT(DATE(ER.FechaTrCr),' ',HOUR(ER.FechaTrCr),':',MINUTE(ER.FechaTrCr)) AS FechaRespuesta ,ER.IDEncuestaArbolOpcionesRespuesta,S.IDAreaSocio
					FROM EncuestaArbol E
					JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
					JOIN EncuestaArbolRespuesta ER ON ER.IDPreguntaEncuestaArbol = P.IDPreguntaEncuestaArbol
					LEFT JOIN Socio S ON ER.IDSocio = S.IDSocio
					LEFT JOIN Usuario U ON ER.IDSocio = U.IDUsuario
					LEFT JOIN AreaSocio A ON S.IDAreaSocio = A.IDAreaSocio
					LEFT JOIN Ciudad C ON S.IDCiudad = C.IDCiudad
                    LEFT JOIN CategoriaEncuestaArbol CA ON P.IDCategoriaEncuestaArbol=CA.IDCategoriaEncuestaArbol
                    $where
					AND E.IDEncuestaArbol = " . $IDEncuestaArbol . "
					AND P.Publicar = 'S'
                    $where_filtro
					ORDER BY ER.IDEncuestaArbolRespuesta DESC";

            $q_Graficas = $dbo->query($sqlGraficas);

            $arr_graficas = array();
            while ($row = $dbo->fetchArray($q_Graficas)) {
                $arr_Respuesta = explode(',', $row['Valor']);
                $arr_opciones = array();

                if (count($arr_Respuesta) > 1) {
                    foreach ($arr_Respuesta as $Respuesta) {
                        $Respuesta = trim($Respuesta);
                        $arr_graficas[$row['IDPreguntaEncuestaArbol']][$Respuesta]++;
                    }
                } else {
                    $arr_graficas[$row['IDPreguntaEncuestaArbol']][trim($row['Valor'])]++;
                }
            }
            $view = "views/" . $script . "/list.php";
            break;


        default:
            $view = "views/" . $script . "/list.php";
    } // End switch


    if (empty($view))
        $view = "views/" . $script . "/form.php";


    // if (!empty($tipoReporte) && !empty($IDClub)) {
    //     $view = "views/" . $script . "/list.php";
    //     if ($tipoReporte == "Socio") {




    //         $sql_estadosTipoEmpleado = "SELECT COUNT(S.TipoSocio) AS TotalTipoSocio,S.TipoSocio
    //                                  FROM Socio as S
    //                                  WHERE S.IDClub = '" . SIMReg::get("club") . "'
    //                                  AND S.IDEstadoSocio = 1
    //                                  GROUP BY S.TipoSocio";

    //         $r_TipoEmpleado = $dbo->query($sql_estadosTipoEmpleado);

    //         while ($r = $dbo->fetchArray($r_TipoEmpleado)) {
    //             $r["TipoSocio"] = ($r["TipoSocio"] == '') ? 'Otros' : $r["TipoSocio"];
    //             $array_dataTipo[$r["TipoSocio"]] = $r["TipoSocio"] . "|" . $r["TotalTipoSocio"];
    //         }

    //         $sql_Categoria = "SELECT COUNT(C.IDCategoria) AS TotalCategoria,C.Nombre
    //                                  FROM Socio as S,Categoria as C
    //                                  WHERE S.IDCategoria=C.IDCategoria AND S.IDClub = '" . SIMReg::get("club") . "'
    //                                  AND S.IDEstadoSocio = 1
    //                                  GROUP BY S.IDCategoria";
    //         $r_Categoria = $dbo->query($sql_Categoria);
    //         while ($r = $dbo->fetchArray($r_Categoria)) {
    //             $array_dataCategoria[$r["Nombre"]] = $r["Nombre"] . "|" . $r["TotalCategoria"];
    //         }

    //         $sql_Parentesco = "SELECT COUNT(S.IDParentesco) AS TotalParentesco,P.Nombre
    //                                  FROM Socio as S, Parentesco as P
    //                                  WHERE S.IDParentesco=P.IDParentesco AND S.IDClub = '" . SIMReg::get("club") . "'
    //                                  AND S.IDEstadoSocio = 1
    //                                  GROUP BY S.IDParentesco";
    //         $r_Parentesco = $dbo->query($sql_Parentesco);
    //         while ($r = $dbo->fetchArray($r_Parentesco)) {
    //             $array_dataParentesco[$r["Nombre"]] = $r["Nombre"] . "|" . $r["TotalParentesco"];
    //         }
    //         $sql_EstadoCivil = "SELECT COUNT(S.EstadoCivil) AS TotalEstadoCivil, S.EstadoCivil
    //                                  FROM Socio as S
    //                                  WHERE S.IDClub = '" . SIMReg::get("club") . "'
    //                                  AND S.IDEstadoSocio = 1
    //                                  GROUP BY S.EstadoCivil";
    //         $r_EstadoCivil = $dbo->query($sql_EstadoCivil);
    //         while ($r = $dbo->fetchArray($r_EstadoCivil)) {
    //             $r["EstadoCivil"] = ($r["EstadoCivil"] == '') ? 'Otros' : $r["EstadoCivil"];
    //             $array_dataEstadoCivil[$r["EstadoCivil"]] = $r["EstadoCivil"] . "|" . $r["TotalEstadoCivil"];
    //         }

    //         $sql_formaPago = "SELECT SUM(D.MontoPago) AS TotalMetodoPago ,D.MetodoPago FROM HistorialCuotasSociales as H, DetalleHistorialCuotasSociales as D WHERE H.IDHistorialCuotasSociales=D.IDHistorialCuotasSociales AND H.IDClub = '" . SIMUser::get('club') . "' GROUP BY D.MetodoPago";
    //         $r_formaPago = $dbo->query($sql_formaPago);

    //         while ($r = $dbo->fetchArray($r_formaPago)) {
    //             $array_dataFormaPago[$r["MetodoPago"]] = $r["MetodoPago"] . "|" . $r["TotalMetodoPago"];
    //         }

    //         $sql_MetodoPago = "SELECT COUNT(D.MetodoPago) AS TotalMetodoPago ,D.MetodoPago FROM HistorialCuotasSociales as H, DetalleHistorialCuotasSociales as D WHERE H.IDHistorialCuotasSociales=D.IDHistorialCuotasSociales AND H.IDClub = '" . SIMUser::get('club') . "' GROUP BY D.MetodoPago";
    //         $r_MetodoPago = $dbo->query($sql_MetodoPago);

    //         while ($r = $dbo->fetchArray($r_MetodoPago)) {
    //             $array_dataMetodoPago[$r["MetodoPago"]] = $r["MetodoPago"] . "|" . $r["TotalMetodoPago"];
    //         }

    //         // $sql_Pagos = "SELECT COUNT(H.IDHistorialCuotasSociales) AS Total FROM HistorialCuotasSociales as H WHERE H.Estado = 'Pagado' AND H.IDClub = '" . SIMUser::get('club') . "'";
    //         $sql_Pagos = "SELECT COUNT(H.IDHistorialCuotasSociales) AS Total FROM HistorialCuotasSociales as H WHERE H.Estado = 'Pagado' AND H.IDClub = '" . SIMUser::get('club') . "'";
    //         $r_Pagos = $dbo->query($sql_Pagos);
    //         $cant_Pagos = $dbo->assoc($r_Pagos);

    //         $sql_CuotasSociales = "SELECT COUNT(H.IDHistorialCuotasSociales) AS Total FROM HistorialCuotasSociales as H WHERE H.IDClub = '" . SIMUser::get('club') . "'";
    //         $r_CuotasSociales = $dbo->query($sql_CuotasSociales);
    //         $cant_CuotasSociales = $dbo->assoc($r_CuotasSociales);

    //         // $sql_MesPago = "SELECT COUNT(H.IDHistorialCuotasSociales) as TotalMesPago, concat(Date_format(H.FechaTrEd,'%m')) as mes FROM HistorialCuotasSociales as H WHERE H.IDClub = '" . SIMUser::get('club') . "' AND H.Estado = 'Pagado' GROUP BY 2";
    //         $sql_MesPago = "SELECT COUNT(H.IDHistorialCuotasSociales) as TotalMesPago, concat(Date_format(H.FechaTrEd,'%m')) as mes FROM HistorialCuotasSociales as H WHERE H.IDClub = '" . SIMUser::get('club') . "' GROUP BY 2";
    //         $r_MesPago = $dbo->query($sql_MesPago);

    //         while ($r = $dbo->fetchArray($r_MesPago)) {
    //             $array_dataMesPago[$r["mes"]] = $r["mes"] . "|" . $r["TotalMesPago"];
    //         }

    //         //  $view = "views/".$script."/list.php";
    //     }
    // } // End if !empty($tipoReporte)

    ?>
