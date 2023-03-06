<?

require(dirname(__FILE__) . "/../../admin/config.inc.php");
require(LIBDIR . "SIMServicioReserva.inc.php");
SIMUtil::cache();
session_start();

$dia_semana = date("N");

/*
if($_GET[IDClub]==26 && ($dia_semana=="6" || $dia_semana =="7") && (int)date("H")>=14 ):
	$_GET["ids"]=1484;
endif;

if($_GET[IDClub]==26 && ($dia_semana=="6" || $dia_semana =="7") && ($_GET["ids"]=="1490" || $_GET["ids"]=="2109" || $_GET["ids"]=="") ):
	$_GET["ids"]=4350;
endif;
if($_GET[IDClub]==26 && $dia_semana!="6" && $dia_semana !="7" && empty($_GET["ids"])):
	$_GET["ids"]=2109;
endif;
*/



if ($_GET["ids"] == 2719)
    $_GET["ids"] = 2719;

if ($_GET[IDClub] == 26 && ($dia_semana == "6" || $dia_semana == "7") && (int)date("H") >= 14) :
    $_GET["ids"] = 2719;
//$_GET["ids"]=17286;
endif;

if ($_GET[IDClub] == 26 && ($dia_semana == "6" || $dia_semana == "7") && ($_GET["ids"] == "1490" || $_GET["ids"] == "2109" || $_GET["ids"] == "")) :
    $_GET["ids"] = 2719;
//$_GET["ids"]=17286;
endif;

if ($_GET[IDClub] == 26 && $dia_semana != "6" && $dia_semana != "7" && empty($_GET["ids"])) :
    $_GET["ids"] = 2719;
//$_GET["ids"]=17286;
endif;

if ($_GET[IDClub] == 52) :
    $_GET["ids"] = 5177;
endif;

if ($_GET[IDClub] == 23) :
    $_GET["ids"] = 5681;
endif;

if ($_GET[IDClub] == 10) :
    $_GET["ids"] = 13526;
endif;

if ($_GET[IDClub] == 78 && empty($_GET["ids"])) :
    $_GET["ids"] = 10061;
endif;

if ($_GET[IDClub] == 106 && empty($_GET["ids"])) :
    $_GET["ids"] = 18686;
endif;

switch ($_GET["ids"]):
        //Esui
    case "2109":
    case "1490":
        $servicios_relacionados = array("2109", "1490");
        break;
    case "4350":
    case "5039":
    case "1484":
        $servicios_relacionados = array("4350", "5039", "1484");
        break;
    case "10061":
    case "9990":
        $servicios_relacionados = array("9990", "10061");
        break;
    case "2719":
    case "2720":
        $servicios_relacionados = array("2720", "2719");
        break;
endswitch;

//traer datos del registro
$datos_club = $dbo->fetchById("Club", "IDClub", $_GET["IDClub"], "array");

//$response_elementos = SIMWebService::get_elementos( $_GET["IDClub"],"", $_GET["ids"] );
$response_elementos = SIMServicioReserva::get_busqueda_elementos($_GET["IDClub"], $Tag, $IDUsuario, $IDSocio, $_GET["ids"], $IDClubAsociado);


$elementos[$_GET["ids"]] = $response_elementos["response"];
$ids = $_GET["ids"];

foreach ($elementos[$ids] as $key_elemento => $datos_elemento) {

    $horas = SIMWebService::get_disponiblidad_elemento_servicio($_GET["IDClub"], $ids, date("Y-m-d"), $datos_elemento["IDElemento"], "S", "", "", "", "");


    unset($array_datos_elemento);
    //Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
    $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $ids . "'");
    if ($id_servicio_maestro == 15) : //15 = Golf
        foreach ($horas["response"]["0"]["Disponibilidad"] as $key_horas => $todashoras) {
            foreach ($todashoras as $key_todahora => $datos_horas) {
                //print_r($datos_horas);
                if ($datos_horas["IDElemento"] == $datos_elemento["IDElemento"]) :
                    //echo "<br>" . $datos_horas["IDElemento"];
                    $array_datos_elemento[][] = $datos_horas;
                    $array_horas[$datos_horas["IDElemento"]]  = $array_datos_elemento;
                endif;
            }
        } //end for

    else :
        foreach ($horas["response"] as $key_horas => $datos_horas) {
            $array_horas[$datos_elemento["IDElemento"]]  = $datos_horas["Disponibilidad"];
        } //end for

    endif;
} //end for


$logo_club = CLUB_ROOT . $dbo->getFields("Club", "FotoDiseno1", "IDClub = '" . $_GET["IDClub"] . "'");
$ruta_logo_club = CLUB_DIR . $dbo->getFields("Club", "FotoDiseno1", "IDClub = '" . $_GET["IDClub"] . "'");

$id_servicio = $_GET["ids"];
if (count($servicios_relacionados) > 1) :
    $clave = array_search($id_servicio, $servicios_relacionados);
    $siguiente_clave = (int)$clave + 1;
    $siguiente_servicio = $servicios_relacionados[$siguiente_clave];
    if (!empty($siguiente_servicio)) :
        $id_servicio = $siguiente_servicio;
        header("Refresh: 30; URL='pantallagolflagartos.php?action=new&ids=" . $siguiente_servicio . "&IDClub=" . $_GET[IDClub] . "'");
    else :
        $id_servicio = $servicios_relacionados[0];
        header("Refresh: 30; URL='pantallagolflagartos.php?action=new&ids=" . $id_servicio . "&IDClub=" . $_GET[IDClub] . "'");
    endif;
else :
//header("Refresh: 30; URL='pantallapub.php?action=new&ids=$id_servicio'");

endif;


$id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $_GET["ids"] . "'");
$servicio_maestro = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
$servicio_personalizado =  $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . $_GET["IDClub"] . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_maestro . "'");


$colortv =  $dbo->getFields("Servicio", "ColorTv", "IDClub = '" . $_GET["IDClub"] . "'  and IDServicio = '" . $_GET["ids"] . "'");
if (empty($colortv)) :
    $colortv = "#333";
else :
    $color_personalizado = "S";
endif;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reservas</title>
    <link href='https://fonts.googleapis.com/css?family=Raleway:200,400,300,700,500,600' rel='stylesheet' type='text/css'>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="dist/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <script src="assets/js/ie-emulation-modes-warning.js"></script>



    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        .banner {}

        .header {
            height: 50px;
            padding: 5px;
            width: 100%;
        }

        /*
Generic Styling, for Desktops/Laptops
*/
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /**Forzamos a que las filas tenga el mismo ancho**/
            width: 100%;
            /*El ancho que necesitemos*/
        }

        /* Zebra striping */
        tr:nth-of-type(odd) {
            background: #eee;
        }

        th {
            background: <?php echo $colortv; ?>;
            color: white;
            font-weight: bold;
            text-align: center;
            /* para cambiar tamaño de letra */
            font-family: 'Arial', serif;
            font-size: 20px !important;
        }

        td,
        th {
            padding: 6px;
            border: 1px solid #ccc;
            text-align: center;
            font-size: <?php if ($_GET["IDClub"] == 34) echo "16px";
                        else echo "12px"; ?>;
            margin: 0;
            word-wrap: break-word;
            /*Si el contenido supera el tamano, adiciona a una nueve linea**/
            font-weight: bold;
            /* para cambiar tamaño de letra */
            font-size: 16px !important;

        }

        thead {}


        .cheader {
            background-color: #428BCA;
            color: #FFFFFF;
        }
    </style>

</head>

<body>
    <table class="fixed">
        <thead>
            <tr>
                <th colspan="<?php
                                $columnas = (int)count($elementos[$ids]);
                                if ($columnas >= 10) :
                                    $columnas_titulo = $columnas - 3;
                                else :
                                    $columnas_titulo = $columnas;
                                endif;

                                echo $columnas_titulo;
                                ?>
                     ">
                    <h3 class="fecha" style="font-weight:bold; font-family: 'Arial', serif; font-size:40px !important">Reservas: <? echo $servicio_personalizado . " " . strtolower(SIMUtil::tiempo(date("Y-m-d"))) ?></h3>
                </th>
                <th align="center" style="background-color:<?php if ($color_personalizado == "S") echo $colortv;
                                                            else echo "#FFFCFC"; ?>;" <?php if ($columnas >= 10) : $columna_ultima = ((int)count($elementos[$ids]) - $columnas_titulo) + 1;
                                                                                            echo "colspan = '" . $columna_ultima . "' ";
                                                                                        endif;  ?>>
                    <?php
                    $tamano = getimagesize($ruta_logo_club);
                    $ancho = $tamano[0];              //Ancho
                    $alto = $tamano[1];
                    if ($ancho > 155) :
                        $tamano_logo = 'width="155" height="80"';
                    endif;
                    ?>
                    <img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $tamano_logo; ?> />
                </th>
            </tr>
            <tr>
                <th>Hora</th>
                <?
                $contador_elemento = 1;
                $parametro_desde = 1;
                $parametro_hasta = count($elementos[$ids]);
                foreach ($elementos[$ids] as $key_elemento => $datos_elemento) {
                ?>
                    <?php
                    if ($contador_elemento >= $parametro_desde && $contador_elemento <= $parametro_hasta) : // mostrar solo los seis primeros 
                    ?>
                        <th width="400px">
                            <?= $datos_elemento["Nombre"] ?>
                        </th>
                    <?php
                    endif; ?>
                <?
                    $contador_elemento++;
                } //end for
                ?>
            </tr>
        </thead>
        <tbody>
            <?
            $horaactual = strtotime(date("H:i:s"));

            foreach ($array_horas as $idelemento => $datos_elemento)
                foreach ($datos_elemento as $key_todahora => $datos_horas)
                    foreach ($datos_horas as $key_horas => $info_disponibilidad) {
                        $horamostrar = strtotime($info_disponibilidad["Hora"]);
                        //if( $horamostrar >= $horaactual || $fecha <> date("Y-m-d") )
                        //{
                        //print_r($info_disponibilidad[]);
                        if (empty($info_disponibilidad["Tee"]))
                            $ConTee = "N";
                        else
                            $ConTee = "S";

                        $mostrar_disponibilidad[$info_disponibilidad["Hora"]][$idelemento][$info_disponibilidad["Tee"]] = $info_disponibilidad;
                        //}
                    } //end for
            //mosrar información
            ksort($mostrar_disponibilidad);
            foreach ($mostrar_disponibilidad as $hora => $datos_disponibilidad) {
                $contador_elemento = 1;
            ?>
                <tr>
                    <td style="text-align:right !important">
                        <?php
                        $dia_fecha = date('w', strtotime(date("Y-m-d")));
                        //Hora desde
                        $sql_dispo_elemento_gral = "Select HoraDesde,IDDisponibilidad From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '" . $_GET["ids"] . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $idelement . "|%' Order By HoraDesde ASC Limit 1";
                        $qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
                        $datos_detalle_disponibilidad = $dbo->fetchArray($qry_dispo_elemento_gral);
                        $datos_detalle_disponibilidad["IDDisponibilidad"];
                        if (empty($intervalo_horas)) :
                            $datos_disponibilidad_actual = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $datos_detalle_disponibilidad["IDDisponibilidad"] . "' ", "array");
                            $intervalo_horas = $datos_disponibilidad_actual["Intervalo"]; //minutos
                        endif;

                        echo $hora_inicio = substr($hora, 0, 5);

                        $hora_hasta = strtotime("+" . $intervalo_horas . " minute", strtotime($hora_inicio));

                        //echo  " - " . date("H:i ", $hora_hasta);
                        ?>
                    </td>
                    <?
                    foreach ($elementos[$ids] as $key_elemento => $datos_elemento) {
                    ?>
                        <?php
                        if ($contador_elemento >= $parametro_desde && $contador_elemento <= $parametro_hasta) : // mostrar solo los seis primeros 
                        ?>
                            <td width="400px">
                                <?php
                                //print_r($datos_disponibilidad);
                                if ($ConTee == "S")
                                    $hasta = 2;
                                else
                                    $hasta = 1;
                                for ($i = 1; $i <= $hasta; $i++) :
                                    echo "&nbsp;";
                                    if ($i == 1)
                                        $Tee = "Tee1";
                                    else
                                        $Tee = "Tee10";

                                    if ($ConTee == "N")
                                        $Tee = "";

                                    //print_r($datos_disponibilidad["1391"]["Tee10"]);

                                    if ($_GET["IDClub"] == "9") :
                                        $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["IDSocio"] . "'");
                                        echo $accion_socio . "-";
                                    endif;

                                    $nombre_tomo_reserva = "";
                                    //if($hora_inicio=="12:50" && $datos_elemento["IDElemento"]==98)
                                    //print_r($datos_disponibilidad);

                                    $reservado = $datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["IDSocio"];
                                    //Verifico si el club se configuro para mostrar el nombre del socio o para mostrar un texto personalizado, para funcionarios si se muestra el nombre
                                    $MostrarReserva = $dbo->getFields("Club", "MostrarReserva", "IDClub = '" . $_GET["IDClub"] . "'");
                                    $MostrarReservaS = $dbo->getFields("Servicio", "MostrarReserva", "IDServicio = '" . $_GET["ids"] . "'");

                                    if ($MostrarReservaS == "Pesonalizado") :
                                        $MostrarReserva == "Pesonalizado";
                                        $LabelPersonalizado = $dbo->getFields("Servicio", "LabelPersonalizado", "IDServicio = '" . $_GET["ids"] . "'");
                                    elseif ($MostrarReservaS == "NombreSocio") :
                                        $MostrarReserva = "";
                                    elseif ($MostrarReserva == "Pesonalizado") :
                                        $MostrarReserva = $dbo->getFields("Club", "LabelPersonalizado", "IDClub = '" . $_GET["IDClub"] . "'");
                                    endif;

                                    $id_benef = "";
                                    $id_inv = "";

                                    if ($MostrarReserva == "Pesonalizado" && !empty($reservado)) :

                                        $nombre_tomo_reserva = utf8_encode($LabelPersonalizado);
                                        $nombre_tomo_reserva .= "  " . $datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["Tee"];
                                    else :
                                        if ((int)$datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["IDSocioBeneficiario"] > 0 && empty($datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["Socio"])) : // Si la reserva es para un beneficiario
                                            $id_benef = $datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["IDSocioBeneficiario"];
                                            $nombre_tomo_reserva =   "Benef. " . $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $id_benef . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $id_benef . "'");
                                        elseif ((int)$datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["IDInvitadoBeneficiario"] > 0 && empty($datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["Socio"])) : // Si la reserva es para un invitado
                                            $id_inv = $datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["IDInvitadoBeneficiario"];
                                            $nombre_tomo_reserva =   "Inv. " . $dbo->getFields("ReservaGeneralInvitado", "Nombre", "IDReservaGeneralInvitado = '" . $id_inv . "'");
                                        else : // es para el socio
                                            $nombre_tomo_reserva =  $datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["Socio"];
                                        endif;

                                        if (!empty($reservado)) :
                                            $nombre_tomo_reserva .= "  " . $datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["Tee"];
                                        endif;

                                    endif;

                                    if ($datos_disponibilidad_actual["Cupos"] > 1) {
                                        // consulto quien esta en esa reserva
                                        $sql_rese = "SELECT NombreSocio, IDReservaGeneral, NombreBeneficiario FROM ReservaGeneral WHERE Fecha='" . date("Y-m-d") . "' and Hora = '" . $hora_inicio . "' and IDServicioElemento = '" . $datos_elemento["IDElemento"] . "' ";
                                        $r_rese = $dbo->query($sql_rese);
                                        while ($row_rese = $dbo->fetchArray($r_rese)) {

                                            if (!empty($row_rese[NombreBeneficiario])) {
                                                $NombreReservaM = "Benef. " . $row_rese["NombreBeneficiario"];

                                                // PARA APONSENTOS SE DEBE PONER EL NOMBRE DEL CABALLO.
                                                $sql_otro_dato = "SELECT Valor From ReservaGeneralCampo Where IDReservaGeneral = '" . $row_rese["IDReservaGeneral"] . "' AND IDServicioCampo = 57 ";
                                                // echo $row_rese["IDReservaGeneral"];
                                                $result_otro_dato = $dbo->query($sql_otro_dato);
                                                $datos = $dbo->fetchArray($result_otro_dato);
                                                if (!empty($datos)) {
                                                    $NombreReservaM .= "<br>CABALLO: " . $datos["Valor"];
                                                }
                                            } else {
                                                $NombreReservaM = $row_rese["NombreSocio"];

                                                // PARA APONSENTOS SE DEBE PONER EL NOMBRE DEL CABALLO.
                                                $sql_otro_dato = "SELECT Valor From ReservaGeneralCampo Where IDReservaGeneral = '" . $row_rese["IDReservaGeneral"] . "' AND IDServicioCampo = 57 ";
                                                // echo $row_rese["IDReservaGeneral"];
                                                $result_otro_dato = $dbo->query($sql_otro_dato);
                                                $datos = $dbo->fetchArray($result_otro_dato);
                                                if (!empty($datos)) {
                                                    $NombreReservaM .= "<br>CABALLO: " . $datos["Valor"];
                                                }
                                            }
                                            $nombre_tomo_reserva .= "<br>" . $NombreReservaM;
                                        }
                                    }


                                    echo $nombre_tomo_reserva;
                                    //echo $datos_disponibilidad[ $datos_elemento["IDElemento"] ][ "Socio" ];
                                    //Consulto Invitados
                                    if (!empty($datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["IDReserva"])) :
                                        $datos_invitado = "";
                                        $sql_invitado = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $datos_disponibilidad[$datos_elemento["IDElemento"]][$Tee]["IDReserva"] . "'";
                                        $result_invitado = $dbo->query($sql_invitado);
                                        while ($row_invitado = $dbo->fetchArray($result_invitado)) :
                                            if (!empty($row_invitado["IDSocio"]))
                                                $tipo_invitado = "Socio: ";
                                            else
                                                $tipo_invitado = "Externo: ";

                                            $nom_invitado =     $row_invitado["Nombre"];

                                            if (!empty($row_invitado["IDSocio"]) && empty($row_invitado["Nombre"])) :
                                                $nom_invitado = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row_invitado["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row_invitado["IDSocio"] . "'");
                                            endif;
                                            echo  ", " . ucwords($nom_invitado) . "<br>";
                                        endwhile;
                                    //Consulto Boleador escogido
                                    //$id_auxiliar = $dbo->getFields( "ReservaGeneral" , "IDAuxiliar" , "IDReservaGeneral = '" . $datos_disponibilidad[ $datos_elemento["IDElemento"] ][ "IDReserva" ] . "'" );
                                    //if((int)$id_auxiliar>0)
                                    //echo  " - " .  $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $id_auxiliar . "'" );

                                    endif;

                                endfor;
                                ?>
                            </td>
                        <?php
                        endif; ?>
                    <?
                        $contador_elemento++;
                    } //end for
                    ?>
                </tr>
            <?
            } //end for
            ?>
        </tbody>
    </table>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script src="jquery-scrolltofixed-min.js" type="text/javascript"></script>

    <script src="assets/js/goheadfixed.js" type="text/javascript"></script>


    <script>
        jQuery(document).ready(function() {

            <?php if (count($elementos[$ids]) >= 1) :    ?>
                setTimeout("location.href='pantallagolflagartos.php?ids=<?php echo $ids ?>&pagina=<?php echo $proxima_pagina; ?>&action=new&id=<?php echo rand(0, 10000); ?>&IDClub=<?php echo $_GET[IDClub]; ?>'", 40000);
            <?php endif; ?>

            $("html, body").animate({
                scrollTop: $(document).height()
            }, 90000);
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 90000);
            }, 90000);


            setInterval(function() {
                // 50000 - it will take 4 secound in total from the top of the page to the bottom
                $("html, body").animate({
                    scrollTop: $(document).height()
                }, 90000);
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 90000);
                }, 90000);

                location.reload();


            }, 100000);


            $('.header').scrollToFixed();
            //$('.header').bind('fixed.ScrollToFixed', function() { $(this).css('color', 'red'); });
            //$('.header').bind('unfixed.ScrollToFixed', function() { $(this).css('color', ''); });

            $('.footer').scrollToFixed({
                bottom: 0,
                limit: $('.footer').offset().top,
                preFixed: function() {
                    $(this).css('color', 'blue');
                },
                postFixed: function() {
                    $(this).css('color', '');
                },
            });

            // Order matters here because we are dependent on the state of the footer above for
            // our limit.  The footer must be set first; otherwise, we will not be in the right
            // position on a window refresh, if the limit is supposed to be invoked.
            $('#summary').scrollToFixed({
                marginTop: $('.header').outerHeight(true) + 10,
                limit: function() {
                    var limit = $('.footer').offset().top - $('#summary').outerHeight(true) - 10;
                    return limit;
                },
                minWidth: 1000,
                zIndex: 999,
                fixed: function() {},
                dontCheckForPositionFixedSupport: true
            });

            $('#summary').bind('unfixed.ScrollToFixed', function() {
                if (window.console) console.log('summary preUnfixed');
            });
            $('#summary').bind('unfixed.ScrollToFixed', function() {
                if (window.console) console.log('summary unfixed');
                $(this).css('color', '');
                $('.header').trigger('unfixed.ScrollToFixed');
            });
            $('#summary').bind('fixed.ScrollToFixed', function() {
                if (window.console) console.log('summary fixed');
                $(this).css('color', 'red');
                $('.header').trigger('fixed.ScrollToFixed');
            });


        });
    </script>

</body>

</html>