<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$ids = SIMNet::req("idservicio");
$idelemento = SIMNet::req("idelemento");
$fecha = SIMNet::req("fecha");

switch ($oper) {

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
                case 'Socio':

                    $idsocio = $dbo->getFields("Socio", "IDSocio", " IDClub = '" . SIMUser::get("IDClub") . "' AND ( Accion = '" . $search_object->data . "' OR NumeroDocumento = '" . $search_object->data . "' OR AccionPadre = '" . $search_object->data . "' ) ");

                    break;

                case 'IDElemento':
                    $IDServicioElemento = $search_object->data;
                    break;

                case 'Fecha':
                    $fecha = $search_object->data;
                    break;
            }
        } //end for

        $str_limit = "";

        break;

    case "searchurl":
        $accion = $_GET["Accion"];
        $idsocio = $accion;

        $fecha = "";

        if (empty($idsocio) && !empty($_GET["fecha"])) {
            $fecha = $_GET["fecha"];
        }

        break;
}

$week = date("W",strtotime($fecha));
$i = 1;
$view .= "<table border='1' style='width: 100%;'>";
//recorre cada uno de los dias de la semana de la fecha seleccionada
for($j=1; $j<=7; $j++):
    
    $dayWeek = $j == 7 ? date("Y-m-d",strtotime($dayWeek."+ 1 days")) : date('Y-m-d', strtotime('01/01 +' . ($week - 1) . ' weeks first day +'. $j .'day'));

    $reserva = SIMWebService::get_reservas_servicio(SIMUser::get("club"), $ids, $dayWeek, $idelemento, $idsocio);
    $idServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = $ids");

    $today = date('Y-m-d');

    if($i == 1):
        $view .= "<tr>";
        $view .= "<td style='padding: 0 5px 5px; vertical-align: top; width: 50%;height: 0;'>";
    else:
        $view .= "<td style='padding-bottom: 5px; vertical-align: top; width: 50%;height: 0;'>";
    endif;

    $view .= "<div style='background-color: grey; color: white; font-weight: bold; padding: 5px;'>".SIMUtil::tiempo($dayWeek)."</div>
              <div style='background-color: #e8e8e8; height:100% !important; padding: 5px;'>";
    
    foreach ($reserva["response"] as $key => $row):        

        $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoEliminarReserva");
        
        //Para el Rancho todos pueden cancelar reserva
        if (
            $Permiso == 1 || SIMUser::get("IDPerfil") <= 2 || SIMUser::get("IDClub") == "3" || SIMUser::get("IDClub") == "12" || SIMUser::get("IDPerfil") == "21" || SIMUser::get("IDPerfil") == "22" || SIMUser::get("IDPerfil") == "30" || SIMUser::get("IDPerfil") == "31"
            || SIMUser::get("IDPerfil") == "27" || SIMUser::get("IDPerfil") == "29" || SIMUser::get("IDPerfil") == "10" || SIMUser::get("IDPerfil") == "23" || SIMUser::get("IDPerfil") == "32"
        ):
            $btn_eliminar = '<a id="detalle_eliminar' . $row["IDReserva"] . '"  class="fancybox" href="detalle_reserva_eliminar.php?idr=' . $row["IDReserva"] . '" data-fancybox-type="iframe" ><i class="ace-icon fa fa-trash-o"/></a>';
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

        // Si la reserva fue tomada para algun beneficiario muestro el nombre del beneficiario
        $IDBeneficiario = $dbo->getFields("ReservaGeneral", "IDSocioBeneficiario", "IDReservaGeneral = '" . $row["IDReserva"] . "'");

        if ($row["IDClub"] == 35): // En puerto penalisa debe ser la casa
            $Accion = $row["Socio"]["Predio"];
        else:
            $Accion = $row["Socio"]["Accion"];
        endif;

        if ($IDBeneficiario):
            $nombre_reserva = "Benef. " . $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDBeneficiario . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDBeneficiario . "'");
        else:
            $nombre_reserva = $row["Socio"]["Nombre"] . " " . $row["Socio"]["Apellido"];
        endif;

        if ($row["IDClub"] == 44): //PARA EL COUNTRY DE BOGOTA SE DEBE MOSTRAR EL TIPO SOCIO SI ES CANJE.
            if (strtoupper($row[Socio][TipoSocio]) == "CANJE"):
                $nombre_reserva .= " (" . $row[Socio][TipoSocio] . ") ";
            endif;
        endif;


        // if ($row[Cumplida] ==  'S'):
        //     $checkCumplidaSi = "checked";
        //     $checkCumplidaNo = "";
        //     $checkCumplidaParcial = "";
        //     $checkIncumplidaSinSancion = "";
        // elseif ($row[Cumplida] == 'N'):
        //     $checkCumplidaSi = "";
        //     $checkCumplidaNo = "checked";
        //     $checkCumplidaParcial = "";
        //     $checkIncumplidaSinSancion = "";
        // elseif ($row[Cumplida] == 'I'):
        //     $checkCumplidaSi = "";
        //     $checkCumplidaNo = "";
        //     $checkCumplidaParcial = "";
        //     $checkIncumplidaSinSancion = "checked";
        // elseif ($row[Cumplida] == 'P'):
        //     $checkCumplidaSi = "";
        //     $checkCumplidaNo = "";
        //     $checkCumplidaParcial = "checked";
        //     $checkIncumplidaSinSancion = "";
        // else:
        //     $checkCumplidaSi = "";
        //     $checkCumplidaNo = "";
        //     $checkCumplidaParcial = "";
        //     $checkIncumplidaSinSancion = "";
        // endif;

        // $botonCumplir = "";
        // if ($row[Tipo] != "Automatica"):
        //     $botonCumplir = '<input type="radio" value="S" class="btncambioreserva" name="cumplida' . $row["IDReserva"] . '"  campo =  "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkCumplidaSi . '>Si ';
        //     $botonCumplir .= '<input type="radio" value="N" class="btncambioreserva" name="cumplida' . $row["IDReserva"] . '"  campo = "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkCumplidaNo . '>No ';
        //     $botonCumplir .= '<input type="radio" value="I" class="btncambioreserva" name="cumplida' . $row["IDReserva"] . '"  campo = "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkIncumplidaSinSancion . '>Incumplida sin sanción ';
        //     $botonCumplir .= '<!--input type="radio" value="P" class="btncambioreserva" name="cumplida' . $row["IDReserva"] . '"  campo = "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkCumplidaParcial . '>Parcial--><br>';
        //     $botonCumplir .= "<div name='msgupdate" . $row["IDReserva"] . "' id='msgupdate" . $row["IDReserva"] . "'></div>";
        // else:
        //     $botonCumplir = "Reserva Automatica";
        // endif;
        //Golf
        if ($idServicioMaestro == 15 || $idServicioMaestro == 27 || $idServicioMaestro == 28 || $idServicioMaestro == 30):
            $btn_horario = '&nbsp;&nbsp;<a id="detalle' . $row["IDReserva"] . '"  class="fancybox" href="detalle_reserva.php?idr=' . $row["IDReserva"] . '&tipo=horario" data-fancybox-type="iframe" ><i class="ace-icon fa  fa-tachometer bigger-130"/></a>';
        endif;


        // "IDReservaGeneral" => "<font color='" . $color_linea . "'>" . $row["IDReserva"] . "</font>",
        //* "Detalle" => "<font color='" . $color_linea . "'>" . '<a id="detalle' . $row["IDReserva"] . '"  class="fancybox" href="detalle_reserva.php?idr=' . $row["IDReserva"] . '" data-fancybox-type="iframe" ><i class="ace-icon fa fa-file-text-o bigger-130"/></a>' . "</font>",
        // "Fecha" => "<font color='" . $color_linea . "'>" . SIMUtil::tiempo($row["Fecha"]) . " " . $Tee . "</font>",
        // "Hora" => "<font color='" . $color_linea . "'>" . $row["Hora"],
        // "Socio" => "<font color='" . $color_linea . "'>" . $nombre_reserva . $row["IDSocioBeneficiario"] . "</font>",
        // "Accion" => "<font color='" . $color_linea . "'>" . $Accion . $btn_horario . "</font>",
        // "IDElemento" => "<font color='" . $color_linea . "'>" . $row["NombreElemento"] . "</font>",
        // "Cumplimiento" => $botonCumplir,
        // "Cancelar" => $btn_eliminar,


        $view .= "<table>
                    <tr>
                        <td style='padding: 5px;'><b>".date("h:ia", strtotime($row["Hora"]))."</b> </td>
                        <td style='padding: 5px;'><font color='$color'>$nombre_reserva $btn_horario</font></td>
                        <td style='padding: 5px;'><a id='detalle".$row["IDReserva"]." class='fancybox' href='detalle_reserva.php?idr='".$row['IDReserva']."' data-fancybox-type='iframe'><i class='ace-icon fa fa-file-text-o'/></a></td>
                        <td style='padding: 5px;'>$btn_eliminar</td>
                    </tr>
                </table>";

    endforeach;

    $view .= "</div></div>";

    if($i == 2 || $j+1 == 7):
        $view .= "</tr>";
        $i = 1;
    else:
        $i++;
    endif;
endfor;

$view .= "</table>";

echo $view;
