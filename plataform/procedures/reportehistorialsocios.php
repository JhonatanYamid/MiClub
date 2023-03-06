 <?php
    // $tipoReporte = empty($_SESSION["TipoRepDiagnostico"]) ? "" : $_SESSION["TipoRepDiagnostico"];
    $tipoReporte = 'Socio';
    SIMReg::setFromStructure(array(
        "title" => "DashBoard Historial Socios",
        "table" => "CuotasSociales",
        "key" => "IDDiagnostico",
        "mod" => "Historial Socios",

    ));
    $script = "reportehistorialsocios";

    //extraemos las variables
    $table = SIMReg::get("table");
    $key = SIMReg::get("key");
    $mod = SIMReg::get("mod");

    //Verificar permisos
    SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

    $idClub = SIMReg::get("club");

    // if (empty($tipoReporte) || empty($idClub)) {
    //     $view = "views/" . $script . "/form.php";
    //     //  exit;
    // }

    if (!empty($tipoReporte) && !empty($idClub)) {
        $view = "views/" . $script . "/list.php";
        if ($tipoReporte == "Socio") {




            $sql_estadosTipoEmpleado = "SELECT COUNT(S.TipoSocio) AS TotalTipoSocio,S.TipoSocio
                                     FROM Socio as S
                                     WHERE S.IDClub = '" . SIMReg::get("club") . "'
                                     AND S.IDEstadoSocio = 1
                                     GROUP BY S.TipoSocio";

            $r_TipoEmpleado = $dbo->query($sql_estadosTipoEmpleado);

            while ($r = $dbo->fetchArray($r_TipoEmpleado)) {
                $r["TipoSocio"] = ($r["TipoSocio"] == '') ? 'Otros' : $r["TipoSocio"];
                $array_dataTipo[$r["TipoSocio"]] = $r["TipoSocio"] . "|" . $r["TotalTipoSocio"];
            }

            $sql_Categoria = "SELECT COUNT(C.IDCategoria) AS TotalCategoria,C.Nombre
                                     FROM Socio as S,Categoria as C
                                     WHERE S.IDCategoria=C.IDCategoria AND S.IDClub = '" . SIMReg::get("club") . "'
                                     AND S.IDEstadoSocio = 1
                                     GROUP BY S.IDCategoria";
            $r_Categoria = $dbo->query($sql_Categoria);
            while ($r = $dbo->fetchArray($r_Categoria)) {
                $array_dataCategoria[$r["Nombre"]] = $r["Nombre"] . "|" . $r["TotalCategoria"];
            }

            $sql_Parentesco = "SELECT COUNT(S.IDParentesco) AS TotalParentesco,P.Nombre
                                     FROM Socio as S, Parentesco as P
                                     WHERE S.IDParentesco=P.IDParentesco AND S.IDClub = '" . SIMReg::get("club") . "'
                                     AND S.IDEstadoSocio = 1
                                     GROUP BY S.IDParentesco";
            $r_Parentesco = $dbo->query($sql_Parentesco);
            while ($r = $dbo->fetchArray($r_Parentesco)) {
                $array_dataParentesco[$r["Nombre"]] = $r["Nombre"] . "|" . $r["TotalParentesco"];
            }
            $sql_EstadoCivil = "SELECT COUNT(S.EstadoCivil) AS TotalEstadoCivil, S.EstadoCivil
                                     FROM Socio as S
                                     WHERE S.IDClub = '" . SIMReg::get("club") . "'
                                     AND S.IDEstadoSocio = 1
                                     GROUP BY S.EstadoCivil";
            $r_EstadoCivil = $dbo->query($sql_EstadoCivil);
            while ($r = $dbo->fetchArray($r_EstadoCivil)) {
                $r["EstadoCivil"] = ($r["EstadoCivil"] == '') ? 'Otros' : $r["EstadoCivil"];
                $array_dataEstadoCivil[$r["EstadoCivil"]] = $r["EstadoCivil"] . "|" . $r["TotalEstadoCivil"];
            }

            $sql_formaPago = "SELECT SUM(D.MontoPago) AS TotalMetodoPago ,D.MetodoPago FROM HistorialCuotasSociales as H, DetalleHistorialCuotasSociales as D WHERE H.IDHistorialCuotasSociales=D.IDHistorialCuotasSociales AND H.IDClub = '" . SIMUser::get('club') . "' GROUP BY D.MetodoPago";
            $r_formaPago = $dbo->query($sql_formaPago);

            while ($r = $dbo->fetchArray($r_formaPago)) {
                $array_dataFormaPago[$r["MetodoPago"]] = $r["MetodoPago"] . "|" . $r["TotalMetodoPago"];
            }

            $sql_MetodoPago = "SELECT COUNT(D.MetodoPago) AS TotalMetodoPago ,D.MetodoPago FROM HistorialCuotasSociales as H, DetalleHistorialCuotasSociales as D WHERE H.IDHistorialCuotasSociales=D.IDHistorialCuotasSociales AND H.IDClub = '" . SIMUser::get('club') . "' GROUP BY D.MetodoPago";
            $r_MetodoPago = $dbo->query($sql_MetodoPago);

            while ($r = $dbo->fetchArray($r_MetodoPago)) {
                $array_dataMetodoPago[$r["MetodoPago"]] = $r["MetodoPago"] . "|" . $r["TotalMetodoPago"];
            }

            // $sql_Pagos = "SELECT COUNT(H.IDHistorialCuotasSociales) AS Total FROM HistorialCuotasSociales as H WHERE H.Estado = 'Pagado' AND H.IDClub = '" . SIMUser::get('club') . "'";
            $sql_Pagos = "SELECT COUNT(H.IDHistorialCuotasSociales) AS Total FROM HistorialCuotasSociales as H WHERE H.Estado = 'Pagado' AND H.IDClub = '" . SIMUser::get('club') . "'";
            $r_Pagos = $dbo->query($sql_Pagos);
            $cant_Pagos = $dbo->assoc($r_Pagos);

            $sql_CuotasSociales = "SELECT COUNT(H.IDHistorialCuotasSociales) AS Total FROM HistorialCuotasSociales as H WHERE H.IDClub = '" . SIMUser::get('club') . "'";
            $r_CuotasSociales = $dbo->query($sql_CuotasSociales);
            $cant_CuotasSociales = $dbo->assoc($r_CuotasSociales);

            // $sql_MesPago = "SELECT COUNT(H.IDHistorialCuotasSociales) as TotalMesPago, concat(Date_format(H.FechaTrEd,'%m')) as mes FROM HistorialCuotasSociales as H WHERE H.IDClub = '" . SIMUser::get('club') . "' AND H.Estado = 'Pagado' GROUP BY 2";
            $sql_MesPago = "SELECT COUNT(H.IDHistorialCuotasSociales) as TotalMesPago, concat(Date_format(H.FechaTrEd,'%m')) as mes FROM HistorialCuotasSociales as H WHERE H.IDClub = '" . SIMUser::get('club') . "' GROUP BY 2";
            $r_MesPago = $dbo->query($sql_MesPago);

            while ($r = $dbo->fetchArray($r_MesPago)) {
                $array_dataMesPago[$r["mes"]] = $r["mes"] . "|" . $r["TotalMesPago"];
            }

            //  $view = "views/".$script."/list.php";
        }
    } // End if !empty($tipoReporte)

    ?>
