<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$ids = SIMNet::req("idservicio");
$idelemento = SIMNet::req("idelemento");
$fecha = SIMNet::req("fecha");

//Si es coordinador tenemos que mirar que servicio se trae primero
//si es un usuario normal se traen las horas
if (empty($fecha)) {
    $fecha = date("Y-m-d");
}

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true") {
    $oper = "search";
}

switch ($oper) {

    case "del":

        break;

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
} //end switch

$reserva = SIMWebService::get_reservas_servicio(SIMUser::get("club"), $ids, $fecha, $idelemento, $idsocio);
$id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $ids . "'");

$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
foreach ($reserva["response"] as $key => $row) {


    $responce->rows[$i]['id'] = $row["IDReserva"];

    if (!empty($row["Tee"])) {
        $Tee = " - " . $row["Tee"];
    } else {
        $Tee = "";
    }

    $btn_eliminar = string;



    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoEliminarReserva");
    //Para el Rancho todos pueden cancelar reserva
    if (
        $Permiso == 1 || SIMUser::get("IDPerfil") <= 2 || SIMUser::get("IDClub") == "3" || SIMUser::get("IDClub") == "12" || SIMUser::get("IDPerfil") == "21" || SIMUser::get("IDPerfil") == "22" || SIMUser::get("IDPerfil") == "30" || SIMUser::get("IDPerfil") == "31"
        || SIMUser::get("IDPerfil") == "27" || SIMUser::get("IDPerfil") == "29" || SIMUser::get("IDPerfil") == "10" || SIMUser::get("IDPerfil") == "23" || SIMUser::get("IDPerfil") == "32"
    ) :
        $btn_eliminar = '<a id="detalle_eliminar' . $row["IDReserva"] . '"  class="fancybox" href="detalle_reserva_eliminar.php?idr=' . $row["IDReserva"] . '" data-fancybox-type="iframe" ><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
    else :
        $btn_eliminar = '';
    endif;

    if ($row["Cumplida"] == 'N') {
        $color_linea = "#F43125";
    } elseif ($row["Cumplida"] == 'P') {
        $color_linea = "#2e49a3";
    } elseif ($row["Cumplida"] == 'S') {
        $color_linea = "#31a32f";
    } else {
        $color_linea = "#000000";
    }

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";

    $contador++;

    if ($contador <= 500) :

        if ($origen != "mobile") {

            // Si la reserva fue tomada para algun beneficiario muestro el nombre del beneficiario
            $IDBeneficiario = $dbo->getFields("ReservaGeneral", "IDSocioBeneficiario", "IDReservaGeneral = '" . $row["IDReserva"] . "'");

            // Observaciones de las reservas 
            $Observaciones = $dbo->getFields("ReservaGeneral", "Observaciones", "IDReservaGeneral = '" . $row["IDReserva"] . "'");

            //BLOQUEO ZEUS PARA EL SAN ANDRES
            $bloqueoZeus = $dbo->getFields("Socio", "BloqueoZeus", "IDSocio = '" . $row["IDSocio"] . "'");
            $motivoBloqueoZeus = $dbo->getFields("Socio", "MotivoBloqueoZeus", "IDSocio = '" . $row["IDSocio"] . "'");
            if (!empty($bloqueoZeus)) {
                $Observaciones .= "  Bloqueo:" . $bloqueoZeus . " Motivo:" . $motivoBloqueoZeus;
            }

            if ($row["IDClub"] == 35) // En puerto penalisa debe ser la casa
            {
                $Accion = $row["Socio"]["Predio"];
            } else {
                $Accion = $row["Socio"]["Accion"];
            }

            if ($IDBeneficiario) :
                $nombre_reserva = "Benef. " . $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDBeneficiario . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDBeneficiario . "'");
            else :
                $nombre_reserva = $row["Socio"]["Nombre"] . " " . $row["Socio"]["Apellido"];
            endif;

            if ($row["IDClub"] == 44) : //PARA EL COUNTRY DE BOGOTA SE DEBE MOSTRAR EL TIPO SOCIO SI ES CANJE.
                if (strtoupper($row[Socio][TipoSocio]) == "CANJE") :
                    $nombre_reserva .= " (" . $row[Socio][TipoSocio] . ") ";
                endif;
            endif;

            /*
            if($row["IDClub"]==37 && $row["IDServicio"]==3575):  // especial polo club se debe actualizar cancha y equipo
            $cancha_reserva=$dbo->getFields( "ReservaGeneral" , "Cancha" , "IDReservaGeneral = '".$row["IDReserva"]."'" );
            $equipo_reserva=$dbo->getFields( "ReservaGeneral" , "Equipo" , "IDReservaGeneral = '".$row["IDReserva"]."'" );
            $QuintoJugador=$dbo->getFields( "ReservaGeneral" , "QuintoJugador" , "IDReservaGeneral = '".$row["IDReserva"]."'" );
            $Handicap = "<br><b>Handicap:</b>" . $dbo->getFields( "Socio" , "Handicap" , "IDSocio = '".$row["IDSocio"]."'" );
            $canchas=SIMHTML::formPopupArray( SIMResources::$canchas_polo  ,  $cancha_reserva , "Cancha_".$row["IDSocio"]."_".$row["IDReserva"] ,  "Seleccione cancha" , "form-control canchapolo"  );
            $equipo=SIMHTML::formPopupArray( SIMResources::$equipos_polo  ,  $equipo_reserva , "Equipo_".$row["IDSocio"]."_".$row["IDReserva"] ,  "Seleccione equipo" , "form-control equipopolo"  );
            if($QuintoJugador=="S")
            $checked_quinto="checked";
            else
            $checked_quinto="";

            $quinto_jugador="5 jug? <input type='checkbox' id='checkquintojugador_".$row["IDSocio"]."_".$row["IDReserva"]."' name='checkquintojugador_".$row["IDSocio"]."_".$row["IDReserva"]."' class='form-control quinto_jugador' ".$checked_quinto.">";
            $div_mensaje="<div name='msgcancha' id='msgcancha".$row["IDReserva"]."'></div>";
            $bloque_cancha_equipo=$Handicap.$canchas.$equipo.$quinto_jugador.$div_mensaje;
            endif;
             */

            if ($row[Cumplida] ==  'S') :
                $checkCumplidaSi = "checked";
                $checkCumplidaNo = "";
                $checkCumplidaParcial = "";
                $checkIncumplidaSinSancion = "";
            elseif ($row[Cumplida] == 'N') :
                $checkCumplidaSi = "";
                $checkCumplidaNo = "checked";
                $checkCumplidaParcial = "";
                $checkIncumplidaSinSancion = "";
            elseif ($row[Cumplida] == 'I') :
                $checkCumplidaSi = "";
                $checkCumplidaNo = "";
                $checkCumplidaParcial = "";
                $checkIncumplidaSinSancion = "checked";
            elseif ($row[Cumplida] == 'P') :
                $checkCumplidaSi = "";
                $checkCumplidaNo = "";
                $checkCumplidaParcial = "checked";
                $checkIncumplidaSinSancion = "";
            else :
                $checkCumplidaSi = "";
                $checkCumplidaNo = "";
                $checkCumplidaParcial = "";
                $checkIncumplidaSinSancion = "";
            endif;

            $botonCumplir = "";
            if ($row[Tipo] != "Automatica") :
                $botonCumplir = '<input type="radio" value="S" class="btncambioreserva" name="cumplida' . $row["IDReserva"] . '"  campo =  "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkCumplidaSi . '>Si ';
                $botonCumplir .= '<input type="radio" value="N" class="btncambioreserva" name="cumplida' . $row["IDReserva"] . '"  campo = "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkCumplidaNo . '>No ';
                $botonCumplir .= '<input type="radio" value="I" class="btncambioreserva" name="cumplida' . $row["IDReserva"] . '"  campo = "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkIncumplidaSinSancion . '>Incumplida sin sanción ';
                $botonCumplir .= '<!--input type="radio" value="P" class="btncambioreserva" name="cumplida' . $row["IDReserva"] . '"  campo = "Cumplida" IDReservaGeneral="' . $row["IDReserva"] . '" ' . $checkCumplidaParcial . '>Parcial--><br>';
                $botonCumplir .= "<div name='msgupdate" . $row["IDReserva"] . "' id='msgupdate" . $row["IDReserva"] . "'></div>";
            else :
                $botonCumplir = "Reserva Automatica";
            endif;
            //Golf
            if ($id_servicio_maestro == 15 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28 || $id_servicio_maestro == 30) {
                $btn_horario = '&nbsp;&nbsp;<a id="detalle' . $row["IDReserva"] . '"  class="fancybox" href="detalle_reserva.php?idr=' . $row["IDReserva"] . '&tipo=horario" data-fancybox-type="iframe" ><i class="ace-icon fa  fa-tachometer bigger-130"/></a>';
            }
            $responce->rows[$i]['cell'] = array(
                "IDReservaGeneral" => "<font color='" . $color_linea . "'>" . $row["IDReserva"] . "</font>",
                "Detalle" => "<font color='" . $color_linea . "'>" . '<a id="detalle' . $row["IDReserva"] . '"  class="fancybox" href="detalle_reserva.php?idr=' . $row["IDReserva"] . '" data-fancybox-type="iframe" ><i class="ace-icon fa fa-file-text-o bigger-130"/></a>' . "</font>",
                "Fecha" => "<font color='" . $color_linea . "'>" . SIMUtil::tiempo($row["Fecha"]) . " " . $Tee . "</font>",
                "Hora" => "<font color='" . $color_linea . "'>" . $row["Hora"],
                "Socio" => "<font color='" . $color_linea . "'>" . $nombre_reserva . $row["IDSocioBeneficiario"] . "</font>",
                "Accion" => "<font color='" . $color_linea . "'>" . $Accion . $btn_horario . "</font>",
                "IDElemento" => "<font color='" . $color_linea . "'>" . $row["NombreElemento"] . "</font>",
                "Cumplimiento" => $botonCumplir,
                "Observaciones" => $Observaciones,
                "Cancelar" => $btn_eliminar,
            );
        }

    endif;

    $i++;
}

echo json_encode($responce);
