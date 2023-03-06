<?php
include "../../procedures/general_async.php";
$dbo = &SIMDB::get();

$elemento = $_POST["elemento"];
$fecha = $_POST["fecha"];
$club = $_POST["club"];
$socio = $_POST["accionsocio"];
$servicio = $_POST["servicio"];

$week = date("W",strtotime($fecha));
$view .= "<table style='width: 100%;'>";

//recorre cada uno de los dias de la semana de la fecha seleccionada
for($j=1; $j<=7; $j++):
    
    $dayWeek = $j == 7 ? date("Y-m-d",strtotime($dayWeek."+ 1 days")) : date('Y-m-d', strtotime('01/01 +' . ($week - 1) . ' weeks first day +'. $j .'day'));

    $reserva = SIMWebService::get_reservas_servicio($club, $servicio, $dayWeek, $elemento, $socio);
    $idServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = $servicio");

    $today = date('Y-m-d');
    $add = $j == 5 ? "rowspan='2'" : "";

    if(($j%2) != 0 )
        $view .= "<tr>";
        
    if(($j%2) != 0 && $j != 7):
        $view .= "<td $add style='padding: 0 5px 5px; vertical-align: top; width: 50%;'>";
    else:
        $view .= "<td style='padding-bottom: 5px; vertical-align: top; width: 50%;'>";
    endif;

    $height = $j < 6 ? "height:165px;" : "height:65px;";

    $view .= "<div style='background-color: #e8e8e8; color: #787b7e; font-weight: bold; padding: 5px 10px;border-radius: 5px 5px 0px 0px;'>".SIMUtil::tiempo($dayWeek)."</div>
              <div style='background-color: #eff3f8; padding: 5px;border-radius: 0px 0px 5px 5px; overflow: scroll;$height'>";
    
    foreach ($reserva["response"] as $key => $row):        

        $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoEliminarReserva");
        
        //Para el Rancho todos pueden cancelar reserva
        if (
            $Permiso == 1 || SIMUser::get("IDPerfil") <= 2 || SIMUser::get("IDClub") == "3" || SIMUser::get("IDClub") == "12" || SIMUser::get("IDPerfil") == "21" || SIMUser::get("IDPerfil") == "22" || SIMUser::get("IDPerfil") == "30" || SIMUser::get("IDPerfil") == "31"
            || SIMUser::get("IDPerfil") == "27" || SIMUser::get("IDPerfil") == "29" || SIMUser::get("IDPerfil") == "10" || SIMUser::get("IDPerfil") == "23" || SIMUser::get("IDPerfil") == "32"
        ):
            $btn_eliminar = '<a id="detalle_eliminar' . $row["IDReserva"] . '" title="Eliminar Reserva" class="fancybox" href="detalle_reserva_eliminar.php?idr=' . $row["IDReserva"] . '" data-fancybox-type="iframe" ><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
        else:
            $btn_eliminar = '';
        endif;

        if ($row["Cumplida"] == 'N'):
            $color = "#F43125";
        elseif ($row["Cumplida"] == 'P'):
            $color = "#2e49a3";
        elseif ($row["Cumplida"] == 'S'):
            $color = "#31a32f";
        else:
            $color = "#000000";
        endif;

        $IDBeneficiario = $dbo->getFields("ReservaGeneral", "IDSocioBeneficiario", "IDReservaGeneral = '" . $row["IDReserva"] . "'");

        if ($row["IDClub"] == 35): // En puerto penalisa debe ser la casa
            $Accion = $row["Socio"]["Predio"];
        else:
            $Accion = $row["Socio"]["Accion"];
        endif;

        if ($IDBeneficiario):
            $nombre = "Benef. " . $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDBeneficiario . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDBeneficiario . "'");
        else:
            $nombre = $row["Socio"]["Nombre"] . " " . $row["Socio"]["Apellido"];
        endif;

        if ($row["IDClub"] == 44): //PARA EL COUNTRY DE BOGOTA SE DEBE MOSTRAR EL TIPO SOCIO SI ES CANJE.
            if (strtoupper($row[Socio][TipoSocio]) == "CANJE"):
                $nombre .= " (" . $row[Socio][TipoSocio] . ") ";
            endif;
        endif;

        if ($row['Cumplida'] ==  'S'):
            $checkCumplidaSi = "checked";
            $checkCumplidaNo = "";
            $checkCumplidaParcial = "";
            $checkIncumplidaSinSancion = "";
        elseif ($row['Cumplida'] == 'N'):
            $checkCumplidaSi = "";
            $checkCumplidaNo = "checked";
            $checkCumplidaParcial = "";
            $checkIncumplidaSinSancion = "";
        elseif ($row['Cumplida'] == 'I'):
            $checkCumplidaSi = "";
            $checkCumplidaNo = "";
            $checkCumplidaParcial = "";
            $checkIncumplidaSinSancion = "checked";
        elseif ($row['Cumplida'] == 'P'):
            $checkCumplidaSi = "";
            $checkCumplidaNo = "";
            $checkCumplidaParcial = "checked";
            $checkIncumplidaSinSancion = "";
        else:
            $checkCumplidaSi = "";
            $checkCumplidaNo = "";
            $checkCumplidaParcial = "";
            $checkIncumplidaSinSancion = "";
        endif;

        $botonCumplir = "";
        if ($row['Tipo'] != "Automatica"):
            $botonCumplir = '<input type="radio" value="S" title="Cumplida" class="btncambioreservasemana" name="cumplida' . $row["IDReserva"] . '"  campo ="Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkCumplidaSi . '> S ';
            $botonCumplir .= '<input type="radio" value="N" title="Inclumplida" class="btncambioreservasemana" name="cumplida' . $row["IDReserva"] . '"  campo ="Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkCumplidaNo . '> N ';
            $botonCumplir .= '<input type="radio" value="I" title="Incumplida sin sanción" class="btncambioreservasemana" name="cumplida' . $row["IDReserva"] . '"  campo = "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkIncumplidaSinSancion . '> IS ';
            $botonCumplir .= "<div name='msgupdatesemana" . $row["IDReserva"] . "' id='msgupdatesemana" . $row["IDReserva"] . "'></div>";
        else:
            $botonCumplir = "Reserva Automatica";
        endif;

        if ($idServicioMaestro == 15 || $idServicioMaestro == 27 || $idServicioMaestro == 28 || $idServicioMaestro == 30):
            $btn_horario = '&nbsp;&nbsp;<a id="detalle' . $row["IDReserva"] . '"  class="fancybox" title="Ver Detalle" href="detalle_reserva.php?idr=' . $row["IDReserva"] . '&tipo=horario" data-fancybox-type="iframe" ><i class="ace-icon fa fa-tachometer bigger-130"/></a>';
        endif;

        $view .= "<table>
                    <tr style='border-bottom: 3px solid white;font-size: 13px'>
                        <td style='padding: 5px;vertical-align:center; border-right: 2px solid #e8e8e8;'><b>".date("h:ia", strtotime($row["Hora"]))."</b> </td>
                        <td style='padding: 5px;vertical-align:center; border-right: 2px solid #e8e8e8;'><font color='$color'>$Accion-$nombre</font></td>
                        <td style='padding: 5px;vertical-align:center; border-right: 2px solid #e8e8e8;'>$botonCumplir</td>
                        <td style='padding: 5px;vertical-align:center;'>$btn_horario <a id='detalle".$row["IDReserva"]."' class='fancybox' title='Ver Detalle' href='detalle_reserva.php?idr=".$row['IDReserva']."' data-fancybox-type='iframe'><i class='ace-icon fa fa-file-text-o bigger-130'/></a></td>
                        <td style='padding: 5px;vertical-align:center'>$btn_eliminar</td>
                    </tr>
                </table>";

    endforeach;

    $view .= "</div></div>";

    if(($j%2) == 0 || $j == 7):
        $view .= "</tr>";
    endif;
endfor;

$view .= "</table>";

echo $view;
