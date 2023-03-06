<?php

require_once LIBDIR . "SIMWebServiceReservas.inc.php";
require_once LIBDIR . "SIMWebServiceUsuarios.inc.php";

$hora_actual_sistema_valida = date("H:i:s");
$fecha_actual =  date("Y-m-d");

$fechats = strtotime($fecha_actual);


// Validacion para el club El Rincon (10)
// Si la busqueda es por numero de documento muestre solo a socio pero si es por acción muestro todo el grupo familiar
$TipoBusqueda = "";
if (SIMUser::get('club') == 10) {
    if ($qryString == $datos_invitacion['NumeroDocumento']) {
        $TipoBusqueda = "Documento";
    } elseif ($qryString == $datos_invitacion['Accion']) {
        $TipoBusqueda = "Accion";
    }
}
// Fin Validacion para el club El Rincon (10)

switch (date('w', $fechats)) {
    case 0:
        $dia_txt = "D";
        break;
    case 1:
        $dia_txt = "L";
        break;
    case 2:
        $dia_txt = "M";
        break;
    case 3:
        $dia_txt = "MI";
        break;
    case 4:
        $dia_txt = "J";
        break;
    case 5:
        $dia_txt = "V";
        break;
    case 6:
        $dia_txt = "S";
        break;
}

$socioRoot = str_replace("/", "^", SOCIO_ROOT);
$socioRoot = str_replace("_", "~", $socioRoot);
$foto = str_replace("_", "~", $datos_socio['Foto']);
$VentanaObjetos = $datos_configuracion_club['VentanaObjetos'];
$VentanaVacunacion = $datos_configuracion_club['VentanaVacunacion'];
$CampoFechaNacimiento = $datos_configuracion_club['CampoFechaNacimiento'];
$CampoObservacionGeneral = $datos_configuracion_club['CampoObservacionGeneral'];
$PermitirMultipleAcceso = $datos_configuracion_club['PermitirMultipleAcceso'];

$fotoSocio = "";
if ($EsSocio == "S") {
    $fotoSocio = $socioRoot . $foto;
}
$IDInvitado . SIMUser::get("club") .

    $paramsCarnet = "?foto-persona=asd
				&id_club=" . SIMUser::get("club") . "
				&id_invitacion={$datos_socio['IDSocio']}
				&foto_persona={$fotoSocio}
				&nombre_persona={$datos_invitado['Nombre']} {$datos_invitado['Apellido']}
				&numero_documento={$datos_invitado["NumeroDocumento"]}
				&socio={$datos_socio['Nombre']} {$datos_socio['Apellido']}
				&tipo={$datos_invitacion['TipoInvitacion']}
				&accion={$datos_socio['Accion']}
				&fecha_desde={$datos_invitacion['FechaInicio']}
				&fecha_hasta={$datos_invitacion['FechaFin']}
				&action=imprimir-carnet";

?>
<style>
    #jqGrid_container {
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
        gap: 3%;
    }
</style>
<?
/*
	$url_search = "";
	if( SIMNet::req("action") == "search" )
	{
	$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
	}//end if
	*/
?>

<?php
$sql_borro_ant = $dbo->query("Delete from LogAccesoDiario Where FechaTrCr < '" . date("Y-m-d") . "'");
$array_tipo_contratista = array("4", "19", "20", "21");
if (empty($etiqueta_tipo))
    $etiqueta_tipo = "Socio";

if ($total_resultados >= 1) :
    $sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '" . $id_registro . "' Order by IDLogAcceso Desc Limit 1";
    $result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
    $row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
    // echo '<pre>';
    // print_r($row_log_acceso_ultimo);
    // die();
    $total_log = $dbo->rows($result_log_acceso_ultimo);

    $mecanismoLog = $row_log_acceso_ultimo["Mecanismo"];
    $mecanismoLog = explode(" ", $mecanismoLog);
    $mecanismo = $mecanismoLog[0];
    $placa = $mecanismoLog[1];

    $SalidaLog =  $row_log_acceso_ultimo["Salida"]; ?>
    <div class="widget-box transparent" id="recent-box">

        <?php

        $estado_socio = $dbo->getFields('EstadoSocio', "IDEstadoSocio", " IDEstadoSocio = '" . $datos_socio["IDEstadoSocio"] . "'");
        switch ($estado_socio):
            case "1":
                $estado_socio_color = "color: #4fb852;";
                break;
            case "2":
                $estado_socio_color = "color: #3a4694;";
                break;
            case "3":
                $estado_socio_color = "color: #F10004;";
                break;
            case "4":
                $estado_socio_color = "color: #F10004;";
                break;
            case "6":
                $estado_socio_color = "color: #F10004;";
                break;
            default:
                /* 	$estado_socio_color= "color: #F10004;"; */
                break;

        endswitch;
        ?>
        <div class="widget-header">
            <!--
				<h4 class="widget-title lighter smaller">
				<i class="ace-icon fa fa-users orange"></i>
				<?php echo $modo_busqueda ?>
				<?php echo strtoupper(SIMNet::req("qryString"));



                ?>
				</h4>
				-->



            <!-- 	SocioInvitadoEspecial -->
            <?php
            //$sql_predios = "Select * From SocioAutorizacion Where IDInvitado = '". $datos_invitado['IDInvitado']."' and (FechaInicio >= '".date("Y-m-d")."' or FechaFin >= '".date("Y-m-d")."')   ";
            //$sql_invitacion
            $result_predios = $dbo->query($sql_invitacion/*  $sql_predios */);

            $total_predio_socios = $dbo->rows($result_predios);

            if ($total_predio_socios  >= 2) {



                switch (trim($datos_invitacion["TipoInvitacion"])) {
                    case "InvitadoAcceso":
                        $condicion_log_acceso = " AND Tipo = 'InvitadoAcceso'";

                        break;
                    case "Contratista":
                        $condicion_log_acceso = " AND Tipo = 'Contratista'";


                        break;
                    case "SocioInvitado":
                        $condicion_log_acceso = " AND Tipo = 'SocioInvitado'";
                        break;
                    case "Invitado":
                        $condicion_log_acceso = " AND Tipo = 'InvitadoAcceso'";

                        break;
                    case "Socio":
                        $condicion_log_acceso = " AND Tipo = 'Socio'";


                        break;
                    case "Usuario":
                        $condicion_log_acceso = " AND Tipo = 'Usuario'";

                        break;
                }






                while ($row_predio_socios = $dbo->fetchArray($result_predios)) {

                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_predio_socios["IDSocio"] . "' ", "array");



                    /* VALIDACION DE DIAS AUTORIZADOS */
                    if ($row_predio_socios["Dias"] != '') {
                        $dias = explode(",", $row_predio_socios["Dias"]);

                        if (in_array($dia_txt, $dias)) {
                            $salida = 1;
                        } else {
                            $salida = 0;
                            $msg_dias = "<b style='color: #F10004'>Dia no autorizado por el socio: {$datos_socio["Nombre"]} {$datos_socio["Apellido"]} </b> ";
                        }
                    }



                    /*FIN VALIDACION DE DIAS AUTORIZADOS */





            ?>




                    <b>Tipo:</b> <?php echo $row_predio_socios["TipoInvitacion"]; ?>
                    <?php echo ((SIMUser::get("club") != 10) ? "<b>Fecha Inicio Aut:</b>  {$row_predio_socios["FechaInicio"]}" : ""); ?>
                    <?php echo ((SIMUser::get("club") != 10) ? "<b>Fecha Fin Aut:</b>  {$row_predio_socios["FechaFin"]}" : ""); ?>


                    <?php echo ((SIMUser::get("club") != 10) ? "<b>Fecha Creacion de Aut:</b> {$row_predio_socios["FechaTrCr"]}" : ""); ?>
                    <?php echo ((SIMUser::get("club") != 10) ? "<b>Predio/Apto:</b>  {$datos_socio["Predio"]}" : ""); ?>
                    <b>Accion: </b><?php echo $datos_socio["Accion"]; ?>
                    <b><?php echo $etiqueta_tipo; ?></b> <?php echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?>
                    <b>Tipo Socio:</b> <?php echo $datos_socio["TipoSocio"] ?>
                    <b>Documento de Identidad Socio:</b> <?php echo $datos_socio["NumeroDocumento"] ?>
                    <b style=<?php echo "'$estado_socio_color'" ?>>Estado Socio: </b>
                    <b style=<?php echo "'$estado_socio_color'" ?>>
                        <?php echo $dbo->getFields('EstadoSocio', "Nombre", " IDEstadoSocio = '" . $datos_socio["IDEstadoSocio"] . "'"); ?>
                    </b>

                    <?php if ($datos_socio["TipoSocio"] == 'Canje') : ?>
                        <b>Fecha inicio Canje:</b> <?php echo $datos_socio["FechaInicioCanje"] ?>
                        <b>Fecha final Canje:</b> <?php echo $datos_socio["FechaFinCanje"] ?>

                        <?php
                        if ($datos_socio["FechaFinCanje"] < date("Y-m-d")) {
                        ?>
                            <b>Situación Canje:</b>


                            El socio de tipo Canje ya cumplio con la fecha disponible de canje.

                    <?php

                        }
                    endif;

                    ?>
                    </br>
                    </br>

                <?php
                }
            } else { ?>


                <?php

                if ($datos_invitacion["Dias"] != '') {
                    $dias = explode(",", $datos_invitacion["Dias"]);

                    if (in_array($dia_txt, $dias)) {
                        $salida = 1; ///ejecutas lo que quieres
                    } else {
                        $salida = 0;
                        $msg_dias = "<b style='color: #F10004'>Dia no autorizado por el socio: {$datos_socio["Nombre"]} {$datos_socio["Apellido"]} </b> ";
                    }
                }
                ?>



                <b>Tipo:</b> <?php echo $datos_invitacion["TipoInvitacion"]; ?>
                <?php echo ((SIMUser::get("club") != 10) ? "<b>Fecha Inicio Aut:</b>  {$datos_invitacion["FechaInicio"]}" : ""); ?>
                <?php echo ((SIMUser::get("club") != 10) ? "<b>Fecha Fin Aut:</b>  {$datos_invitacion["FechaFin"]}" : ""); ?>


                <?php echo ((SIMUser::get("club") != 10) ? "<b>Fecha Creacion de Aut:</b> {$datos_invitacion["FechaTrCr"]}" : ""); ?>
                <?php echo ((SIMUser::get("club") != 10) ? "<b>Predio/Apto:</b>  {$datos_socio["Predio"]}" : ""); ?>
                <b>Accion: </b><?php echo $datos_socio["Accion"]; ?>
                <b><?php echo $etiqueta_tipo; ?></b> <?php echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?>
                <b>Tipo Socio:</b> <?php echo $datos_socio["TipoSocio"] ?>
                <b>Documento de Identidad Socio:</b> <?php echo $datos_socio["NumeroDocumento"] ?>
                <b style=<?php echo "'$estado_socio_color'" ?>>Estado Socio: </b>
                <b style=<?php echo "'$estado_socio_color'" ?>>
                    <?php echo $dbo->getFields('EstadoSocio', "Nombre", " IDEstadoSocio = '" . $datos_socio["IDEstadoSocio"] . "'"); ?>
                </b>

                <?php if ($datos_socio["TipoSocio"] == 'Canje') : ?>
                    <b>Fecha inicio Canje:</b> <?php echo $datos_socio["FechaInicioCanje"] ?>
                    <b>Fecha final Canje:</b> <?php echo $datos_socio["FechaFinCanje"] ?>

                    <?php
                    if ($datos_socio["FechaFinCanje"] < date("Y-m-d")) {
                    ?>
                        <b>Situación Canje:</b>


                        El socio de tipo Canje ya cumplio con la fecha disponible de canje.
                <?php
                    }
                endif;
                ?>

            <?php
            }

            ?>


        </div>

        <div class="widget-body">
            <div class="widget-main padding-4">
                <div class="row">
                    <div class="col-xs-12">
                        <div id="jqGrid_container">
                            <table id="simple-table" class="table table-striped table-bordered table-hover" style="width:30%">
                                <tr>
                                    <td>
                                        <table class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td valign="top" width="100">
                                                    <?
                                                    if ($modulo == "Socio") :
                                                        $ruta_foto = SOCIO_ROOT;
                                                        $nombre_foto = "Foto";
                                                        $identificador = $datos_invitado["IDSocio"];
                                                    elseif ($modulo == "Usuario") :
                                                        $ruta_foto = USUARIO_ROOT;
                                                        $nombre_foto = "Foto";
                                                        $identificador = $datos_invitado["IDUsuario"];
                                                    else :
                                                        $ruta_foto = IMGINVITADO_ROOT;
                                                        $nombre_foto = "FotoFile";
                                                        $identificador = $datos_invitado["IDInvitado"];
                                                    endif;

                                                    if (!empty($datos_invitado[$nombre_foto])) {
                                                        echo "<img src='" . $ruta_foto . "$datos_invitado[$nombre_foto]' width='100' height='120'  >";
                                                    } else {
                                                        echo "<img src='assets/images/sinfoto.png' width='100' height='120'> ";
                                                    }
                                                    ?>
                                                    <a class="fancybox" href="../admin/tomarfoto/webcamjquery/foto.php?action=foto&IDRegistro=<?php echo $identificador; ?>&Modulo=<?php echo $modulo; ?>" data-fancybox-type="iframe">
                                                        <i class="ace-icon fa fa-camera bigger-120"></i>
                                                        <span class="bigger-110">Tomar Foto</span>
                                                    </a>




                                                    <!--INICIO VENTANA OBJETOS-->

                                                    <?php if ($VentanaObjetos == 'S') { ?>

                                                        <?php

                                                        $sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '" . $id_registro . "' Order by IDLogAcceso Desc Limit 1";
                                                        $result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
                                                        $row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
                                                        ?>


                                                        <?php if ($row_log_acceso_ultimo['Tipo'] == 'Usuario') {

                                                            $display = 'overflow:scroll;height:200px;display:none';
                                                            $agregar = 'display:none';
                                                        } else {
                                                            $display = 'overflow:scroll;height:200px;';
                                                            $agregar = '';
                                                        }



                                                        $IDSocio = $datos_socio['IDSocio'];
                                                        $IDInvitado = $datos_invitado['IDInvitado'];
                                                        $usuario = $datos_usuario;


                                                        foreach ($usuario as $u)
                                                            $IDUsuario = $u->IDUsuario;
                                                        ?>




                                                        <div style="<?php echo $display ?>">



                                                            <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                                <tr>

                                                                    <th>Tipo Objeto </th>
                                                                    <th>Color</th>
                                                                    <th>Serial</th>
                                                                    <th>Entrada</th>
                                                                    <th>Salida</th>

                                                                </tr>
                                                                <tbody style="" id="listacontactosanunciante">
                                                                    <?php


                                                                    switch (trim($datos_invitacion["TipoInvitacion"])) {
                                                                        case "InvitadoAcceso":
                                                                            if ($IDInvitado != 0) {
                                                                                $r_datosSql = "SELECT *
														FROM AccesoObjeto A
														INNER JOIN TipoObjeto T ON A.IDTipoObjeto=T.IDTipoObjeto
														WHERE  A.IDInvitado='$IDInvitado'";
                                                                            }
                                                                            break;
                                                                        case "Contratista":
                                                                            if ($IDInvitado != 0) {
                                                                                $r_datosSql = "SELECT *
														FROM AccesoObjeto A
														INNER JOIN TipoObjeto T ON A.IDTipoObjeto=T.IDTipoObjeto
														WHERE  A.IDInvitado='$IDInvitado'";
                                                                            }

                                                                            break;
                                                                        case "SocioInvitado":
                                                                            if ($IDInvitado != 0) {
                                                                                $r_datosSql = "SELECT *
														FROM AccesoObjeto A
														INNER JOIN TipoObjeto T ON A.IDTipoObjeto=T.IDTipoObjeto
														WHERE  A.IDInvitado='$IDInvitado'";
                                                                            }
                                                                        case "Invitado":
                                                                            if ($IDInvitado != 0) {
                                                                                $r_datosSql = "SELECT *
														FROM AccesoObjeto A
														INNER JOIN TipoObjeto T ON A.IDTipoObjeto=T.IDTipoObjeto
														WHERE  A.IDInvitado='$IDInvitado'";
                                                                            }
                                                                            break;
                                                                        case "Socio":
                                                                            if ($IDSocio != 0) {

                                                                                $r_datosSql = "SELECT *
														FROM AccesoObjeto A
														INNER JOIN TipoObjeto T ON A.IDTipoObjeto=T.IDTipoObjeto
														WHERE A.IDSocio='$IDSocio'";
                                                                            }

                                                                            break;
                                                                        case "Usuario":
                                                                            if ($IDUsuario != 0) {

                                                                                $r_datosSql = "SELECT *
														FROM AccesoObjeto A
														INNER JOIN TipoObjeto T ON A.IDTipoObjeto=T.IDTipoObjeto
														WHERE  A.IDUsuario='$IDUsuario'";
                                                                            }
                                                                            break;
                                                                    }

                                                                    $r_datosQuery = $dbo->query($r_datosSql);
                                                                    $r_datosm = $dbo->fetch($r_datosQuery);
                                                                    $datosT = $r_datosm;
                                                                    $count_objetos = $dbo->rows($dbo->query($r_datosSql));


                                                                    if ($count_objetos > 1) {

                                                                        $i = 0;



                                                                        foreach ($datosT as $r) {

                                                                            $id_usuario = $r['IDUsuario'];

                                                                            $idAccesoObjeto = $r['IDAccesoObjeto'];



                                                                            $sql_ingreso = "SELECT * FROM LogAccesoObjeto  WHERE IDAccesoObjeto ='$idAccesoObjeto' ORDER BY IDLogAcceso  DESC LIMIT 1";
                                                                            $qry_ingreso = $dbo->query($sql_ingreso);
                                                                            $r_datosc = $dbo->fetch($qry_ingreso);


                                                                            $count_objetos_entrantes = $dbo->rows($dbo->query($sql_ingreso));

                                                                            if ($count_objetos_entrantes < 1) {
                                                                                if ($row_log_acceso_ultimo["Entrada"] != "S") {
                                                                                    $campo_chek_salida = '=';
                                                                                    $campo_chek_entrada = '=';
                                                                                }
                                                                                if ($row_log_acceso_ultimo["Entrada"] != "S") {
                                                                                    $campo_chek_salida = '=';
                                                                                    $campo_chek_entrada = '=';
                                                                                }
                                                                            }




                                                                            if ($row_log_acceso_ultimo["Entrada"] != "S") {
                                                                                $campo_chek_salida = '=';
                                                                                $campo_chek_entrada = '=';
                                                                            }
                                                                            if ($row_log_acceso_ultimo["Entrada"] != "S") {
                                                                                $campo_chek_salida = '=';
                                                                                $campo_chek_entrada = '=';
                                                                            }


                                                                            if ($row_log_acceso_ultimo["Entrada"] == "S") {
                                                                                $campo_chek_salida = 0;
                                                                                $campo_chek_entrada = '=';
                                                                            } else {
                                                                                $campo_chek_entrada = 0;
                                                                                $campo_chek_salida = '=';
                                                                            }





                                                                            if ($r_datosc['Entrada'] == 'S') {
                                                                                $ccheck = 'checked';
                                                                            } else {

                                                                                $ccheck = '';
                                                                            }

                                                                            if ($r_datosc['Salida'] == 'S') {
                                                                                $cchecksalida = 'checked';
                                                                            } else {
                                                                                $cchecksalida = '';
                                                                            }




                                                                    ?>


                                                                            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">


                                                                                <td><?php echo $r['Nombre']; ?></td>
                                                                                <td><?php echo $r['Campo2']; ?></td>
                                                                                <td><?php echo $r['Campo1']; ?></td>


                                                                                <td> <?php echo "<input  type=\"checkbox\"  id=\"accesoobjeto_$i\" name=Campo\"$idAccesoObjeto\" value=\"$idAccesoObjeto\"$ccheck disabled$campo_chek_entrada > "; ?></td>
                                                                                <td> <?php if ($count_objetos >= 1  &&     $count_objetos_entrantes >= 1) echo "<input  type=\"checkbox\"  id=\"salidaobjeto_$i\" name=Campo\"$idAccesoObjeto\" value=\"$idAccesoObjeto\" $cchecksalida disabled$campo_chek_salida>"; ?></td>



                                                                            </tr>
                                                                        <?php
                                                                            $i++;
                                                                        }
                                                                    } else {

                                                                        if ($count_objetos == 0) {

                                                                        ?>


                                                                            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">


                                                                                <td><?php echo     $datosT['Nombre']; ?></td>
                                                                                <td><?php echo     $datosT['Campo2']; ?></td>
                                                                                <td><?php echo     $datosT['Campo1']; ?></td>






                                                                            </tr>
                                                                        <?php
                                                                            $i++;
                                                                        } else {

                                                                            $i = 0;



                                                                            $id_usuario = $datosT['IDUsuario'];

                                                                            $idAccesoObjeto = $datosT['IDAccesoObjeto'];



                                                                            $sql_ingreso = "SELECT * FROM LogAccesoObjeto  WHERE IDAccesoObjeto ='$idAccesoObjeto' ORDER BY IDLogAcceso  DESC LIMIT 1";
                                                                            $qry_ingreso = $dbo->query($sql_ingreso);
                                                                            $r_datosc = $dbo->fetch($qry_ingreso);


                                                                            $count_objetos_entrantes = $dbo->rows($dbo->query($sql_ingreso));

                                                                            if ($count_objetos_entrantes < 1) {
                                                                                if ($row_log_acceso_ultimo["Entrada"] != "S") {
                                                                                    $campo_chek_salida = '=';
                                                                                    $campo_chek_entrada = '=';
                                                                                }
                                                                                if ($row_log_acceso_ultimo["Entrada"] != "S") {
                                                                                    $campo_chek_salida = '=';
                                                                                    $campo_chek_entrada = '=';
                                                                                }
                                                                            }




                                                                            if ($row_log_acceso_ultimo["Entrada"] != "S") {
                                                                                $campo_chek_salida = '=';
                                                                                $campo_chek_entrada = '=';
                                                                            }
                                                                            if ($row_log_acceso_ultimo["Entrada"] != "S") {
                                                                                $campo_chek_salida = '=';
                                                                                $campo_chek_entrada = '=';
                                                                            }


                                                                            if ($row_log_acceso_ultimo["Entrada"] == "S") {
                                                                                $campo_chek_salida = 0;
                                                                                $campo_chek_entrada = '=';
                                                                            } else {
                                                                                $campo_chek_entrada = 0;
                                                                                $campo_chek_salida = '=';
                                                                            }





                                                                            if ($r_datosc['Entrada'] == 'S') {
                                                                                $ccheck = 'checked';
                                                                            } else {

                                                                                $ccheck = '';
                                                                            }

                                                                            if ($r_datosc['Salida'] == 'S') {
                                                                                $cchecksalida = 'checked';
                                                                            } else {
                                                                                $cchecksalida = '';
                                                                            }




                                                                        ?>


                                                                            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">


                                                                                <td><?php echo     $datosT['Nombre']; ?></td>
                                                                                <td><?php echo     $datosT['Campo2']; ?></td>
                                                                                <td><?php echo     $datosT['Campo1']; ?></td>


                                                                                <td> <?php echo "<input  type=\"checkbox\"  id=\"accesoobjeto_$i\" name=Campo\"$idAccesoObjeto\" value=\"$idAccesoObjeto\"$ccheck disabled$campo_chek_entrada > "; ?></td>
                                                                                <td> <?php if ($count_objetos >= 1 &&     $count_objetos_entrantes >= 1) echo "<input  type=\"checkbox\"  id=\"salidaobjeto_$i\" name=Campo\"$idAccesoObjeto\" value=\"$idAccesoObjeto\" $cchecksalida disabled$campo_chek_salida>"; ?></td>



                                                                            </tr>
                                                                    <?php
                                                                            $i++;
                                                                        }
                                                                    }






                                                                    ?>
                                                                </tbody>
                                                            </table>

                                                        </div>


                                                        <!-- FIN VETANA OBJETOS -->


                                                        <!-- fin -->


                                                        <input type="hidden" id="numero_objetos" name="numero_objetos" value="<?php echo $i ?>">
                                                        <a class="fancybox_vehiculo" href="accesoobjetos.php?IDUsuario=<?php echo $IDUsuario ?>&TipoInvitado=<?php echo trim($datos_invitacion["TipoInvitacion"]) ?>&IDSocio=<?php echo $datos_socio['IDSocio'] ?>&IDInvitado=<?php echo $datos_invitado['IDInvitado'] ?>" data-fancybox-type="iframe" style="<?php echo $agregar ?>">
                                                            <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                            <span class="bigger-110">Agregar objeto</span>
                                                        </a>
                                                    <?php } ?>

                                                    <?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial") { ?>
                                                        <a class="fancybox" href="invitadosgeneral.php?action=edit&id=<?= $datos_invitado["IDInvitado"] ?>&refiere=porteria" data-fancybox-type="iframe">
                                                            <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                            <span class="bigger-110">Editar info</span>
                                                        </a>
                                                    <?php } ?>


                                                </td>
                                                <td valign="top">
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <td>&nbsp;
                                                                <?php echo $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]  ?>
                                                            </td>


                                                        </tr>
                                                        <?php if (SIMUser::get('club') == 136 && isset($CarreraXportiva)) { ?>
                                                            <tr>
                                                                <td>&nbsp;
                                                                    <?php echo $CarreraXportiva;  ?>
                                                                </td>
                                                            </tr>
                                                        <?php  } ?>

                                                        <!-- <?php if ($CampoFechaNacimiento == "S"  &&  $datos_invitado["FechaNacimiento"] != '') { ?> -->
                                                        <tr>


                                                            <td style="color: #f00000;">&nbsp;
                                                                <strong>
                                                                    Edad: <?= SIMUtil::Calcular_Edad($datos_invitado["FechaNacimiento"]) ?>
                                                                </strong>
                                                            </td>
                                                        </tr>
                                                        <tr>


                                                            <td>&nbsp;
                                                                Fecha Nacimiento: <?php echo $datos_invitado["FechaNacimiento"] ?>
                                                            </td>
                                                        </tr>
                                                        <!-- 	<?php } ?> -->

                                                        <!-- <?php if (SIMUser::get('club') == 76 && !empty($datos_invitacion['HoraInicio'])) { ?> -->
                                                        <tr>

                                                            <td>
                                                                Hora Ingreso: <b><?= $datos_invitacion['HoraInicio'] ?></b>
                                                                <br>
                                                                Hora Salida: <b><?= $datos_invitacion['HoraFin'] ?></b>
                                                            </td>
                                                        </tr>
                                                        <!-- 	<?php } ?> -->

                                                        <!-- <?php if ($CampoObservacionGeneral == "S"  && $datos_invitado["ObservacionGeneral"] != '') { ?> -->
                                                        <tr>

                                                            <td>&nbsp;
                                                                <?php
                                                                    $CampoObservacionGeneralEncabezado = (SIMUser::get('club') == 88) ? "Observaci&oacute;n general: " : "Club Canje: ";
                                                                    echo $CampoObservacionGeneralEncabezado . $datos_invitado["ObservacionGeneral"]
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <!-- 	<?php } ?> -->
                                                        <?php


                                                        if ($datos_socio["TipoSocio"] == "Niñera") {
                                                        ?>
                                                            <tr>
                                                                <td>&nbsp;
                                                                    <?php
                                                                    echo $datos_invitado["ObservacionGeneral"];
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        } ?>
                                                        <!--
															<tr>
																<td>&nbsp;
																	<?php
                                                                    $tipo_doc = "";
                                                                    $tipo_doc = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                                                                    if (empty($tipo_doc)) :
                                                                        echo "Documento";
                                                                    else :
                                                                        echo $tipo_doc;
                                                                    endif;
                                                                    ?>
																	<?php echo $datos_invitado["NumeroDocumento"];  ?>
																</td>
															</tr>
															-->
                                                        <?php
                                                        if (SIMUser::get("club") != 44) {
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <?php if (SIMUser::get("club") != 10) : ?>
                                                                        &nbsp;Predio/Apto <?php echo $datos_invitado["Predio"];  ?>
                                                                        <?php if (!empty($datos_invitacion["CreadaPor"])) { ?>
                                                                            &nbsp;<br>Autorizado por: <?php echo $datos_invitacion["CreadaPor"];  ?>
                                                                        <?php } ?>

                                                                        <?php
                                                                        if (!empty($datos_invitado["IDTipoInvitado"])) :
                                                                            echo  "<br>&nbsp;" . $dbo->getFields("TipoInvitado", "Nombre", "IDTipoInvitado = '" . $datos_invitado["IDTipoInvitado"] . "'");
                                                                        endif;
                                                                        if (!empty($datos_invitado["IDClasificacionInvitado"])) :
                                                                            echo  " / " . $dbo->getFields("ClasificacionInvitado", "Nombre", "IDClasificacionInvitado = '" . $datos_invitado["IDClasificacionInvitado"] . "'");
                                                                        endif;
                                                                        ?>
                                                                        <br>
                                                                        <?php
                                                                        //ARL
                                                                        //if(empty($datos_invitado["FechaVencimientoArl"]) && in_array($datos_invitado["IDTipoInvitado"],$array_tipo_contratista)):
                                                                        if (empty($datos_invitado["FechaVencimientoArl"]) && ($datos_invitado["IDTipoInvitado"] != "3")) :
                                                                            echo '<span style="color: #F10004">Sin fecha ARL</span>';
                                                                        //elseif(strtotime($datos_invitado["FechaVencimientoArl"])<strtotime(date("Y-m-d"))  && in_array($datos_invitado["IDTipoInvitado"],$array_tipo_contratista)):
                                                                        elseif (strtotime($datos_invitado["FechaVencimientoArl"]) < strtotime(date("Y-m-d")) && (($datos_invitado["IDTipoInvitado"] != "3"))) :
                                                                            echo '<span style="color: #F10004">ARL Vencido</span>';
                                                                        else :
                                                                            echo "<strong>ARL al dia</strong>";
                                                                        endif;
                                                                        if (empty($datos_invitado["FechaVencimientoSOS"]) && ($datos_invitado["IDTipoInvitado"] != "3")) :
                                                                            echo '<br><span style="color: #F10004">Sin fecha SOS</span>';
                                                                        //elseif(strtotime($datos_invitado["FechaVencimientoArl"])<strtotime(date("Y-m-d"))  && in_array($datos_invitado["IDTipoInvitado"],$array_tipo_contratista)):
                                                                        elseif (strtotime($datos_invitado["FechaVencimientoSOS"]) < strtotime(date("Y-m-d")) && (($datos_invitado["IDTipoInvitado"] != "3"))) :
                                                                            echo '<br><span style="color: #F10004">SOS Vencido</span>';
                                                                        else :
                                                                            echo "<br><strong>SOS al dia</strong>";
                                                                        endif;
                                                                        ?>
                                                                        <br>
                                                                        &nbsp;Vehiculo:
                                                                        <?php
                                                                        //Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
                                                                        if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial") :
                                                                            $condicion_vehiculo = " AND IDInvitado = '" . $datos_invitado["IDInvitado"] . "'";
                                                                        elseif ($modulo == "SocioInvitado") :
                                                                            if ((int)$datos_invitacion["IDInvitado"] > 0)
                                                                                $condicion_vehiculo = " AND IDInvitado = '" . $datos_invitacion["IDInvitado"] . "'";
                                                                            else
                                                                                $condicion_vehiculo = " AND IDInvitado = '-1'";
                                                                        elseif ($modulo == "Socio") :
                                                                            $condicion_vehiculo = " AND IDSocio = '" . $datos_invitado["IDSocio"] . "'";
                                                                        elseif ($modulo == "Usuario") :
                                                                            $condicion_vehiculo = " AND IDUSuario = '" . $datos_invitado["IDUsuario"] . "'";
                                                                        else :
                                                                            $condicion_vehiculo = " AND IDSocio = '-1'";
                                                                        endif;
                                                                        ?>
                                                                        <?php
                                                                        //datos vehiculo
                                                                        if ($modulo == "Usuario") {
                                                                            $sql_vehiculo = "Select * From VehiculoUsuario Where 1 " . $condicion_vehiculo;
                                                                        } else {
                                                                            $sql_vehiculo = "Select * From Vehiculo Where 1 " . $condicion_vehiculo;
                                                                        }
                                                                        $result_vehiculo = $dbo->query($sql_vehiculo);
                                                                        $cont_vehiculo = 0;
                                                                        while ($row_vehiculo = $dbo->fetchArray($result_vehiculo)) :
                                                                            $cont_vehiculo++;
                                                                            $array_placa[] = strtoupper($row_vehiculo["Placa"]);
                                                                            echo '<div style="border:1px solid #E9E9E9; margin-top:3px"><strong>Placa: ' . strtoupper($row_vehiculo["Placa"]) . "</strong><br>";
                                                                            if (empty($row_vehiculo["FechaTecnomecanica"])) :
                                                                                echo '<span style="color: #F10004">Sin fecha tecnomecanica</span>';
                                                                            elseif (strtotime($row_vehiculo["FechaTecnomecanica"]) < strtotime(date("Y-m-d"))) :
                                                                                echo '<span style="color: #F10004">Tecnomecanica Vencida</span>';
                                                                            else :
                                                                                echo "Tecnomecanica al dia";
                                                                            endif;
                                                                            echo "<br>";

                                                                            //SOAT
                                                                            if (empty($row_vehiculo["FechaSeguro"])) :
                                                                                echo '<span style="color: #F10004">Sin fecha SOAT</span>';
                                                                            elseif (strtotime($row_vehiculo["FechaSeguro"]) < strtotime(date("Y-m-d"))) :
                                                                                echo '<span style="color: #F10004">SOAT Vencido</span>';
                                                                            else :
                                                                                echo "SOAT al dia";
                                                                            endif;
                                                                            echo "<br>";
                                                                            echo '</div>';
                                                                        endwhile;
                                                                        ?>
                                                                        <br>
                                                                        <!--   &nbsp;Equipos: -->
                                                                        <?php




                                                                        //Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
                                                                        if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial") :
                                                                            $condicion_equipo = " IDInvitado = '" . $datos_invitado["IDInvitado"] . "'";
                                                                        elseif ($modulo == "SocioInvitado") :
                                                                            if ((int)$datos_invitacion["IDInvitado"] > 0)
                                                                                $condicion_equipo = " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "'";
                                                                            else
                                                                                $condicion_equipo = " IDInvitado = '-1'";
                                                                        endif;
                                                                        ?>
                                                                        <?php
                                                                        //datos vehiculo
                                                                        if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioInvitado") :
                                                                            $sql_equipo = "Select * From Equipo Where  " . $condicion_equipo;

                                                                            $result_equipo = $dbo->query($sql_equipo);
                                                                            $cont_equipo = 0;
                                                                            while ($row_equipo = $dbo->fetchArray($result_equipo)) :
                                                                                $cont_equipo++;
                                                                        ?>
                                                                                <div style="border:1px solid #E9E9E9; margin-top:3px"><strong>Equipo Ingresado</strong><br>
                                                                                    <?php
                                                                                    echo SIMResources::$equiposlapradera[$row_equipo['IDTipoEquipo']];
                                                                                    ?>
                                                                                    <br>
                                                                                    Cantidad:
                                                                                    <?php
                                                                                    echo $row_equipo['Cantidad'];
                                                                                    ?>

                                                                                </div>
                                                                        <?php
                                                                            endwhile;
                                                                        endif;
                                                                        echo $OtrosDatos;
                                                                        ?>
                                                                    <?php endif; ?>




                                                                </td>


                                                            </tr>
                                                        <?php
                                                        }
                                                        //Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
                                                        if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial") {
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    Observación Socio: <?php echo $datos_invitacion["ObservacionSocio"];  ?>
                                                                </td>


                                                            </tr>


                                                            <?php




                                                            $hora_actual = strtotime($hora_actual_sistema_valida);
                                                            $hora_fin = strtotime($datos_invitacion["HoraFin"]);
                                                            $hora_inicio = strtotime($datos_invitacion["HoraInicio"]);
                                                            //print_r($datos_invitacion["Dias"]);
                                                            /* VALIDACION DE DIAS AUTORIZADOS */
                                                            /* 	if($datos_invitacion["Dias"] != ''){
																$dias = explode(",",$datos_invitacion["Dias"] );
																foreach ($dias  as $d) {
																
																	$band = false;
																	if ($dia_txt  == $d){
																		$band = true;
																		break;
																		
																	}
															
																}
																
															} */

                                                            /*FIN VALIDACION DE DIAS AUTORIZADOS */






                                                            if ($hora_actual >= $hora_inicio) {


                                                                $horaI = $datos_invitacion["HoraInicio"];
                                                            } else {



                                                                $msg_info_hora = "<font color='#F14823'><b>Atención: Aun no es la hora de ingreso establecida en la invitación</b></font><br>";
                                                                $horaI = "<font color='#F14823'><b>" . $datos_invitacion["HoraInicio"] . "</b></font><br>";
                                                            }

                                                            if ($hora_actual  >  $hora_fin) {

                                                                $msg_info_hora = "<font color='#F14823'><b>Atención: la hora de salida supera la establecida en la invitación</b></font><br>";

                                                                $horaF = "<font color='#F14823'><b>" . $datos_invitacion["HoraFin"] . "</b></font><br>";
                                                            } else {

                                                                $horaF = $datos_invitacion["HoraFin"];
                                                            }
                                                            ?>

                                                            <tr>
                                                                <td>
                                                                    <?php echo $msg_info_hora   ?>
                                                                </td>

                                                            </tr>


                                                            <?php if ($salida == 0) {


                                                            ?>
                                                                <tr>


                                                                    <td>




                                                                        <?php echo $msg_dias;    ?>


                                                                    </td>



                                                                </tr><?php  } ?>

                                                            <tr>
                                                                <td>
                                                                    Hora Inicio: <?php echo $horaI;  ?>


                                                                </td>

                                                            </tr>

                                                            <tr>

                                                                <td>
                                                                    Hora Fin: <?php echo $horaF; ?>
                                                                </td>

                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    Licencia
                                                                    <?php
                                                                    $cont_licencia = 0;
                                                                    $sql_licencia = "Select * From LicenciaInvitado Where IDInvitado = '" . $datos_invitado["IDInvitado"] . "'";
                                                                    $result_licencia = $dbo->query($sql_licencia);
                                                                    while ($row_licencia = $dbo->fetchArray($result_licencia)) :
                                                                        $cont_licencia++;
                                                                        echo "Categoria: " . strtoupper($row_licencia["Categoria"]) . " ";

                                                                        if (empty($row_licencia["FechaVencimiento"])) :
                                                                            echo '<span style="color: #F10004">Sin fecha vencimiento</span>';
                                                                        elseif (strtotime($row_licencia["FechaVencimiento"]) <= strtotime(date("Y-m-d"))) :
                                                                            echo '<span style="color: #F10004">Licencia Vencida</span>';
                                                                        else :
                                                                            echo "Licencia al dia";
                                                                        endif;
                                                                    endwhile;
                                                                    if ($cont_licencia <= 0) :
                                                                        echo '<span style="color: #F10004">Sin Licencia</span>';
                                                                    endif;
                                                                    ?>
                                                                </td>
                                                            </tr>

                                                            <?php
                                                            if ($datos_invitacion["TipoInvitacion"] == "Invitado de Evento") {
                                                            ?>
                                                                <tr>
                                                                    <td>
                                                                        EVENTO: <?php echo $datos_invitado[TipoSangre]; ?>
                                                                    </td>
                                                                </tr>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <?php
                                                //Verifico Cual fue el ultimo movimeinto registrado en el dia para saber si se activa la entrada o la salida
                                                //$sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc Limit 1";
                                                $sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '" . $id_registro . "' Order by IDLogAcceso Desc Limit 1";
                                                $result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
                                                $row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
                                                $total_log = $dbo->rows($result_log_acceso_ultimo);

                                                $mecanismoLog = $row_log_acceso_ultimo["Mecanismo"];
                                                $mecanismoLog = explode(" ", $mecanismoLog);
                                                $mecanismo = $mecanismoLog[0];
                                                $placa = $mecanismoLog[1];

                                                $SalidaLog =  $row_log_acceso_ultimo["Salida"];

                                                ?>

                                                <td colspan="2">
                                                    <label>
                                                        <input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" id="PeatonalTitular" value="Peatonal" <?php echo ((SIMUser::get("club") == 44) ? " checked" : "") ?> />
                                                        <span class="lbl">Peatonal</span>
                                                    </label>
                                                    <?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 52) { ?>
                                                        <label>
                                                            <input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" value="Acompañante Vehiculo" />
                                                            <span class="lbl">Acompañante vehiculo</span>
                                                        </label>
                                                    <?php } ?>
                                                    <?php //if (SIMUser::get("club") == 10 || SIMUser::get("club") == 8) { 
                                                    ?>
                                                    <label>
                                                        <input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" id="VehiculoTitular" value="Vehiculo" <?php echo (((SIMUser::get("club") == 8  || SIMUser::get("club") == 10) && $mecanismo == "Vehiculo" && $SalidaLog = "S") ? " checked" : "") ?> />
                                                        <span class="lbl">Vehiculo</span>
                                                    </label>
                                                    <input type="text" placeholder="Ingrese placa" id="PlacaVehiculoTitular" name="PlacaVehiculo" class="PlacaVehiculo" <?php echo (((SIMUser::get("club") == 8  || SIMUser::get("club") == 10) && $mecanismo == "Vehiculo" && $SalidaLog = "S") ? ' value="' . "$placa"  : "") ?>>
                                                    <?php //} 
                                                    ?>
                                                    <?php
                                                    if (count($array_placa) > 0) :
                                                        foreach ($array_placa as $placa_vehiculo) : ?>
                                                            <label>
                                                                <input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" value="Vehiculo <?php echo $placa_vehiculo; ?>" />
                                                                <span class="lbl"><?php echo $placa_vehiculo; ?></span>
                                                            </label>
                                                    <?php
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                    <!--
														<label>
														<input name="MecanismoEntradaIngreso" type="radio" class="ace tipoentrada" value="OtroVehiculo"/>
														<span class="lbl">Otro Vehiculo</span>
														</label>
														-->
                                                    <span class="lbl">
                                                        <?php
                                                        if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial") :
                                                            $link_otro_vehiculo = "invitadosgeneral.php?action=edit&id=" . $datos_invitado["IDInvitado"] . "&editarinfo=n&tabinvitado=vehiculos";
                                                        elseif ($modulo == "SocioInvitado") :
                                                            $link_otro_vehiculo = "invitadosgeneral.php?action=edit&id=" . $datos_invitacion["IDInvitado"] . "&editarinfo=n&tabinvitado=vehiculos";
                                                        elseif ($modulo == "Usuario") :
                                                            $link_otro_vehiculo = "usuarios.php?action=edit&id=" . $datos_invitacion["IDUsuario"] . "&editarinfo=n&tabsocio=VehiculoUsuarios";
                                                        else :
                                                            $link_otro_vehiculo = "socios.php?action=edit&id=" . $datos_invitado["IDSocio"] . "&editarinfo=n&tabsocio=vehiculos";
                                                        endif;
                                                        ?>




                                                        <!-- INICIO PREDIOS -->

                                                        <?php

                                                        $total_predio_socios = $dbo->rows($result_predios); ?>


                                                        <input type="hidden" id="totalPredioSocios" name="totalPredioSocios" value="<?php echo $total_predio_socios ?>">
                                                        <?php
                                                        if ($total_predio_socios  >= 2) {
                                                        ?>
                                                            <span class="bigger-110" style="font-color:#"> Predio al que se dirige: </span>

                                                            <?php

                                                            $result_predios = $dbo->query($sql_invitacion);
                                                            $contador_predio_s = 0;
                                                            while ($row_predio_socios = $dbo->fetchArray($result_predios)) {
                                                                // print_r($row_predio_socios);
                                                                if (trim($datos_invitacion["TipoInvitacion"]) != 'Usuario' || trim($datos_invitacion["TipoInvitacion"]) != 'Empleado') {

                                                                    switch (trim($datos_invitacion["TipoInvitacion"])) {
                                                                        case "InvitadoAcceso":
                                                                            $regid = $row_predio_socios['IDSocioInvitadoEspecial'];

                                                                            break;
                                                                        case "Contratista":
                                                                            $regid = $row_predio_socios["IDSocioAutorizacion"];;

                                                                            break;
                                                                        case "Contratista Diaria":
                                                                            $regid = $row_predio_socios["IDSocioAutorizacion"];;

                                                                            break;
                                                                        case "SocioInvitado":
                                                                            $regid = $row_predio_socios["IDSocioInvitado"];
                                                                            break;
                                                                        case "Invitado":
                                                                            $regid = $row_predio_socios['IDSocioInvitadoEspecial'];

                                                                            break;
                                                                        case "Socio":
                                                                            // $regid = $row_predio_socios["IDUsuario"];
                                                                            $regid = $row_predio_socios["IDSocio"];

                                                                            break;
                                                                        case "Usuario":
                                                                            // $regid = $row_predio_socios["IDSocio"];

                                                                            break;
                                                                    }
                                                                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_predio_socios["IDSocio"] . "' ", "array");

                                                                    $contador_predio_s++;

                                                                    $socioI = $datos_socio["IDSocio"];
                                                            ?>
                                                                    <label>
                                                                        <input id="PredioIngresoSocio<?php echo $contador_predio_s ?>" name="PredioIngresoSocio" type="radio" class="PredioDirige" value="<?php echo  $row_predio_socios[IDSocioInvitadoEspecial] ?>" />
                                                                        <span class="lbl"><?php echo  $datos_socio['Predio'];  ?></span>
                                                                    </label>
                                                        <?php
                                                                }
                                                            }
                                                        } ?>
                                                        <!-- FIN PREDIOS -->
                                                        <?php
                                                        if (SIMUser::get("club") != 10) :
                                                        ?>
                                                            <a class="fancybox_vehiculo" href="<?php echo $link_otro_vehiculo ?>" data-fancybox-type="iframe">
                                                                <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                                <span class="bigger-110">Agregar Veh&iacute;culo</span>
                                                            </a>
                                                        <? endif; ?>
                                                        <?php
                                                        if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial") :
                                                            $link_equipo = "invitadosgeneral.php?action=edit&id=" . $datos_invitado["IDInvitado"] . "&editarinfo=n&tabinvitado=equipos";
                                                        ?>
                                                            <a class="fancybox_vehiculo" href="<?php echo $link_equipo ?>" data-fancybox-type="iframe">
                                                                <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                                <span class="bigger-110">Agregar Equipo</span>
                                                            </a>
                                                        <?php
                                                        elseif ($modulo == "SocioInvitado") :
                                                            $link_equipo = "invitadosgeneral.php?action=edit&id=" . $datos_invitacion["IDInvitado"] . "&editarinfo=n&tabinvitado=equipos";
                                                        ?>
                                                            <a class="fancybox_vehiculo" href="<?php echo $link_equipo ?>" data-fancybox-type="iframe">
                                                                <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                                <span class="bigger-110">Agregar Equipo</span>
                                                            </a>
                                                        <?php
                                                        endif;
                                                        ?>

                                                        <a class="fancybox_vehiculo" href="accesoinvitado.php?action=imprimir-carnet<? echo $paramsCarnet ?>" data-fancybox-type="iframe">
                                                            <i class="ace-icon fa fa-print bigger-120"></i>
                                                            <span class="bigger-110">Imprimir Carnet</span>
                                                        </a>

                                                    </span>







                                                    <?php


                                                    //Consulto los predios del socio y si tiene los muestre para seleccionar el predio
                                                    $sql_predio_soc = "Select * From Predio Where IDSocio = '" . $datos_socio["IDSocio"] . "' AND IDSocio <> 0";
                                                    $result_predio_soc = $dbo->query($sql_predio_soc);
                                                    $total_predio_soc = $dbo->rows($result_predio_soc);

                                                    if ((int)$total_predio_soc > 0) :
                                                        echo "<br>Predio al que se dirige:";
                                                    endif;
                                                    $contador_predio = 0;
                                                    while ($row_predio_soc = $dbo->fetchArray($result_predio_soc)) :
                                                        $contador_predio++;
                                                    ?>
                                                        <label>
                                                            <input name="PredioIngreso" type="radio" class="ace" value="<?php echo $row_predio_soc["Predio"];  ?>" <?php if ($contador_predio == 1) : ?> checked <?php endif; ?> />
                                                            <span class="lbl"><?php echo $row_predio_soc["Predio"];  ?></span>
                                                        </label>
                                                    <?php
                                                    endwhile;
                                                    ?>
                                                </td>



                                            </tr>



                                            <?php if (!empty($datos_invitado['IDInvitado'])) : ?>
                                                <tr>
                                                    <td>
                                                        <!-- <span>Objetos </span> -->




                                                        <!-- inicio tabla -->
                                                        <!-- <div style="overflow:scroll;height:200px;">
											<table id="simple-table"    class="table table-striped table-bordered table-hover">
   										 <tr>
        
       										<th>Tipo Objeto</th>
        									<th>Color</th>
        									<th>Serial</th>
											<th>Entrada</th>
											<th>Salida</th>
        
    									</tr>
    										<tbody style=""  id="listacontactosanunciante">
       									 <?php







                                                if (!empty($datos_invitado["IDInvitado"])) {


                                                    $IDInvitado = $datos_invitado["IDInvitado"];
                                                }

                                                if (!empty($datos_invitado["IDSocio"])) {

                                                    $IDSocio = $datos_invitado["IDSocio"];
                                                }







                                                $r_datosSql = "SELECT *
											FROM AccesoObjeto A
											INNER JOIN TipoObjeto T ON A.IDTipoObjeto=T.IDTipoObjeto
											WHERE A.IDSocio='$IDSocio'
											AND A.IDInvitado='$IDInvitado'";
                                                $r_datosQuery = $dbo->query($r_datosSql);
                                                $r_datosm = $dbo->fetch($r_datosQuery);
                                                $datosT = $r_datosm;
                                                $count_objetos = $dbo->rows($dbo->query($r_datosSql));

                                                if ($count_objetos > 1) {

                                                    $i = 0;



                                                    foreach ($datosT as $r) {

                                                        $id_usuario = $r['IDUsuario'];

                                                        $idAccesoObjeto = $r['IDAccesoObjeto'];



                                                        $sql_ingreso = "SELECT * FROM LogAccesoObjeto  WHERE IDAccesoObjeto ='$idAccesoObjeto' ORDER BY IDLogAcceso  DESC LIMIT 1";
                                                        $qry_ingreso = $dbo->query($sql_ingreso);
                                                        $r_datosc = $dbo->fetch($qry_ingreso);




                                                        if ($row_log_acceso_ultimo["Entrada"] == "S") {
                                                            $campo_chek_salida = 0;
                                                            $campo_chek_entrada = '=';
                                                        } else {
                                                            $campo_chek_entrada = 0;
                                                            $campo_chek_salida = '=';
                                                        }





                                                        if ($r_datosc['Entrada'] == 'S') {
                                                            $ccheck = 'checked';
                                                        } else {

                                                            $ccheck = '';
                                                        }

                                                        if ($r_datosc['Salida'] == 'S') {
                                                            $cchecksalida = 'checked';
                                                        } else {
                                                            $cchecksalida = '';
                                                        }




                                            ?>
													
								
														<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
			   
				   
																<td><?php echo $r['Nombre']; ?></td>
																<td><?php echo $r['Campo2']; ?></td>
																<td><?php echo $r['Campo1']; ?></td>
															
					
																<td>	<?php echo "<input  type=\"checkbox\"  id=\"accesoobjeto_$i\" name=Campo\"$idAccesoObjeto\" value=\"$idAccesoObjeto\"$ccheck disabled$campo_chek_entrada > "; ?></td>
																<td>	<?php echo "<input  type=\"checkbox\"  id=\"salidaobjeto_$i\" name=Campo\"$idAccesoObjeto\" value=\"$idAccesoObjeto\" $cchecksalida disabled$campo_chek_salida>"; ?></td>
					
				
				
														</tr>
													<?php
                                                        $i++;
                                                    }
                                                } else {
                                                    $i = 0;


                                                    $id_usuario = $r['IDUsuario'];

                                                    $idAccesoObjeto = $r['IDAccesoObjeto'];



                                                    $sql_ingreso = "SELECT * FROM LogAccesoObjeto  WHERE IDAccesoObjeto ='$idAccesoObjeto' ORDER BY IDLogAcceso  DESC LIMIT 1";
                                                    $qry_ingreso = $dbo->query($sql_ingreso);
                                                    $r_datosc = $dbo->fetch($qry_ingreso);




                                                    if ($row_log_acceso_ultimo["Entrada"] == "S") {
                                                        $campo_chek_salida = 0;
                                                        $campo_chek_entrada = '=';
                                                    } else {
                                                        $campo_chek_entrada = 0;
                                                        $campo_chek_salida = '=';
                                                    }





                                                    if ($r_datosc['Entrada'] == 'S') {
                                                        $ccheck = 'checked';
                                                    } else {

                                                        $ccheck = '';
                                                    }

                                                    if ($r_datosc['Salida'] == 'S') {
                                                        $cchecksalida = 'checked';
                                                    } else {
                                                        $cchecksalida = '';
                                                    }




                                                    ?>
													
								
														<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
			   
				   
																<td><?php echo $datosT['Nombre']; ?></td>
																<td><?php echo $datosT['Campo2']; ?></td>
																<td><?php echo $datosT['Campo1']; ?></td>
															
					
															
																<td>	<?php if ($count_objetos >= 1) echo "<input  type=\"checkbox\"  id=\"accesoobjeto_$i\" name=Campo\"$idAccesoObjeto\" value=\"$idAccesoObjeto\"$ccheck disabled$campo_chek_entrada > "; ?></td>
																<td>	<?php if ($count_objetos >= 1) echo "<input  type=\"checkbox\"  id=\"salidaobjeto_$i\" name=Campo\"$idAccesoObjeto\" value=\"$idAccesoObjeto\" $cchecksalida disabled$campo_chek_salida>"; ?></td>
															
				
				
														</tr>
													<?php


                                                }






                                                    ?>
        											</tbody>       
											</table>
											
												</div> -->
                                                        <!-- 	fin tabla -->
                                                        <?php

                                                        ?>


                                                        <!-- 	<input type="hidden" id="numero_objetos" name="numero_objetos" value="<?php echo $i ?>">
												<a class="fancybox_vehiculo" href="accesoobjetos.php?IDSocio=<?php echo $datos_socio['IDSocio'] ?>&IDInvitado=<?php echo $datos_invitado['IDInvitado'] ?>" data-fancybox-type="iframe">
                                                    <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                    <span class="bigger-110">Agregar objeto</span>
                                                </a> -->
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php
                                            if (!empty($datos_socio["AccionPadre"]) || $datos_socio["AccionPadre"] == $datos_socio["Accion"]) :
                                            ?>
                                                <tr>
                                                    <td><button name="CopiarGrupoFamiliar" id="CopiarGrupoFamiliar" class="CopiarGrupoFamiliar">Copiar a grupo familiar</button></td>
                                                </tr>
                                            <?php
                                            endif;
                                            ?>
                                            <tr>
                                                <td width=800px>
                                                    <?php

                                                    // Deshabilitar Validación de Vacuna Covid
                                                    $ActivarValidacionVacunaCovid == false;
                                                    // Fin Deshabilitar Validación de Vacuna Covid

                                                    //Campos personalizados
                                                    $sql_campos = "SELECT * FROM PreguntaAcceso WHERE IDClub = '" . SIMUser::get("club") . "' and Publicar = 'S'  ORDER BY Orden ASC";
                                                    $r_campos = $dbo->query($sql_campos);
                                                    if ($dbo->rows($r_campos) > 0) {
                                                        if ($ActivarValidacionVacunaCovid != false) {
                                                            // inicion vacucnacion
                                                            if ($VentanaVacunacion == 'S') {



                                                                if (!empty($datos_invitado["IDInvitado"])) {
                                                                    $frm_vacuna['IDSocio'] = $datos_invitado["IDInvitado"];
                                                                }

                                                                if (!empty($datos_invitado["IDSocio"])) {
                                                                    $frm_vacuna['IDSocio'] = $datos_invitado["IDSocio"];
                                                                }



                                                                $dbo = &SIMDB::get();

                                                                //$sql1= "SELECT V.*,  S.IDSocio, S.TipoSocio , I.IDInvitado FROM Vacuna V  LEFT JOIN  Socio S ON S.IDSocio=V.IDSocio LEFT JOIN Invitado I ON I.IDInvitado=V.IDInvitado WHERE S.IDSocio=". $frm_vacuna['IDSocio'] ." OR I.IDInvitado=". $frm_vacuna['IDSocio'];																																
                                                                $query = $dbo->query("SELECT V.*,  S.IDSocio, S.TipoSocio , I.IDInvitado FROM Vacuna V  LEFT JOIN  Socio S ON S.IDSocio=V.IDSocio LEFT JOIN Invitado I ON I.IDInvitado=V.IDInvitado WHERE S.IDSocio=" . $frm_vacuna['IDSocio'] . " OR I.IDInvitado=" . $frm_vacuna['IDSocio']);
                                                                $frm_vacuna = $dbo->fetch($query);

                                                                if (!empty($frm_vacuna['IDSocio'])) {
                                                                    $s = 'S';
                                                                } else {

                                                                    $s = 'N';
                                                                }



                                                                $tipo_socio = $frm_vacuna['TipoSocio'];
                                                                if ($s == 'N') {

                                                                    //Dato list marca vacuna
                                                                    $query = $dbo->query("SELECT * FROM VacunaMarca");
                                                                    $marcaVacunas = $dbo->fetch($query);

                                                                    //Dato list entidad vacuna
                                                                    $query = $dbo->query("SELECT IDVacunaEntidad, Nombre FROM VacunaEntidad WHERE IDClub=" . SIMUser::get("club"));
                                                                    $entidadVacunas = $dbo->fetch($query);


                                                                    if (isset($entidadVacunas["IDVacunaEntidad"])) {
                                                                        $entidadVacunas = [$entidadVacunas];
                                                                    }

                                                                    if (empty($marcaVacunas[0]["IDVacunaMarca"])) {
                                                                        $marcaVacunas = [$marcaVacunas];
                                                                    }

                                                                    if (empty($frm_vacuna["IDVacuna"])) {
                                                                        $frm_vacuna['Vacunado'] = 'N';
                                                                    }

                                                                    if ($frm_vacuna["FechaPrimeraDosis"] === "0000-00-00") {
                                                                        $frm_vacuna["FechaPrimeraDosis"] = "";
                                                                    }

                                                                    if ($frm_vacuna["FechaSegundaDosis"] === "0000-00-00") {
                                                                        $frm_vacuna["FechaSegundaDosis"] = "";
                                                                    }

                                                                    if ($frm_vacuna["Vacunado"] != 'S') { ?>
                                                                        <div class="widget-box transparent" id="recent-box">
                                                                            <div class="widget-header">
                                                                                <h4 class="widget-title lighter smaller">
                                                                                    <i class="ace-icon fa fa-users orange"></i> Información de vacuna
                                                                                </h4>
                                                                            </div>
                                                                            <div class="widget-body">
                                                                                <div class="widget-main padding-4">
                                                                                    <div class="row">
                                                                                        <div class="col-xs-12">
                                                                                            <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                                                                                                <div class="form-group first ">
                                                                                                    <div class="col-xs-12 col-sm-6">
                                                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado de vacunación? </label>
                                                                                                        <div class="col-sm-8">
                                                                                                            <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm_vacuna["Vacunado"], 'Vacunado', "class='input mandatory'") ?>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-xs-12 col-sm-6">
                                                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha cita</label>
                                                                                                        <div class="col-sm-8">
                                                                                                            <input type="text" id="FechaCita" name="FechaCita" placeholder="Fecha Cita" class="col-xs-12 calendar" title="Fecha Cita" value="<?php echo $frm_vacuna["FechaCita"]; ?>">
                                                                                                        </div>
                                                                                                    </div>

                                                                                                </div>
                                                                                                <div class="form-group first ">
                                                                                                    <div class="col-xs-12 col-sm-6">
                                                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Marca</label>
                                                                                                        <div class="col-sm-8">
                                                                                                            <select name="IDVacunaMarca" id="IDVacunaMarca" class="form-control">
                                                                                                                <option value="">[Seleccione Marca vacuna]</option>

                                                                                                                <?php foreach ($marcaVacunas as $value) { ?>

                                                                                                                    <option <?php if ($frm_vacuna['IDVacunaMarca'] == $value["IDVacunaMarca"]) {
                                                                                                                                echo " selected ";
                                                                                                                            } ?>value="<?php echo $value["IDVacunaMarca"] ?>"><?php echo $value["Nombre"] ?></option>
                                                                                                                <?php } ?>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="col-xs-12 col-sm-6">
                                                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Lugar primera cita</label>
                                                                                                        <div class="col-sm-8">
                                                                                                            <input type="text" id="LugarCitaPrimera" name="LugarCitaPrimera" placeholder="Lugar primera cita" class="col-xs-12" title="LugarCitaPrimera" value="<?php echo $frm_vacuna["LugarCitaPrimera"]; ?>">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group first ">
                                                                                                    <div class="col-xs-12 col-sm-6">
                                                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Entidad</label>
                                                                                                        <div class="col-sm-8">
                                                                                                            <input type="text" id="Entidad" name="Entidad" placeholder="Entidad" class="col-xs-12" title="Entidad" value="<?php echo $frm_vacuna["Entidad"]; ?>">
                                                                                                        </div>
                                                                                                    </div>


                                                                                                    <div class="col-xs-12 col-sm-6">
                                                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Lugar Vacunación</label>
                                                                                                        <div class="col-sm-8">
                                                                                                            <input type="text" id="Lugar" name="Lugar" placeholder="Lugar vacunación" class="col-xs-12" title="Lugar vacunación" value="<?php echo $frm_vacuna["Lugar"]; ?>">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>


                                                                                                <div class="form-group first ">
                                                                                                    <div class="col-xs-12 col-sm-6">
                                                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha primera dosis</label>
                                                                                                        <div class="col-sm-8">
                                                                                                            <input type="text" id="FechaPrimeraDosis" name="FechaPrimeraDosis" placeholder="Fecha primera dosis" class="col-xs-12 calendar" title="FechaPrimeraDosis" value="<?php echo $frm_vacuna["FechaPrimeraDosis"]; ?>">
                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="col-xs-12 col-sm-6">
                                                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Certificado primera dosis</label>
                                                                                                        <div class="col-sm-8">
                                                                                                            <?php if ($frm_vacuna["ImagenPrimeraDosis"]) { ?>
                                                                                                                <h5>Imagen actual</h5>
                                                                                                                <img src="<?php echo VACUNA_ROOT . $frm_vacuna["ImagenPrimeraDosis"] ?>" width="200">
                                                                                                                <a href="<? echo VACUNA_ROOT . $frm_vacuna["ImagenPrimeraDosis"] ?>" class="ace-icon fa fa-eye">&nbsp;</a>
                                                                                                                <a href="<? echo $script . ".php?action=del-vacuna-image&archivo=" . $frm_vacuna["ImagenPrimeraDosis"] . "&num_img=Primera&id=" . $frm_vacuna["IDVacuna"] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

                                                                                                            <?php } ?>
                                                                                                            <br />
                                                                                                            <br />
                                                                                                            <input type="file" id="ImagenPrimeraDosis" name="ImagenPrimeraDosis" class="col-xs-12" title="ImagenPrimeraDosis" value="<?php echo $frm_vacuna["ImagenPrimeraDosis"]; ?>">
                                                                                                        </div>
                                                                                                    </div>

                                                                                                </div>




                                                                                                <input type="hidden" name="campos_dinamicos[keys]" value="<?php echo $key ?>">

                                                                                                <div class="clearfix form-actions">
                                                                                                    <div class="col-xs-12 text-center">

                                                                                                        <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo SIMUser::get("club") /* $frm["IDClub"] */ ?>" />
                                                                                                        <input type="hidden" name="action" id="action" value="update-vacuna" />
                                                                                                        <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $IDSocio /* $frm["IDSocio"] */ ?>" />
                                                                                                        <input type="hidden" name="IDInvitado" id="IDInvitado" value="<?php echo     $IDInvitado  /* $frm["IDSocio"] */ ?>" />
                                                                                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty(SIMUser::get("club")/* $frm["IDClub"] */)) echo SIMUser::get("club");
                                                                                                                                                                else echo $frm["IDClub"];  ?>" />
                                                                                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                                                                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                                                                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title1") ?>
                                                                                                        </button>
                                                                                                        <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                                                                                        <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                                                                                    </div>
                                                                                                </div>

                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div><!-- /.widget-main -->
                                                                            </div><!-- /.widget-body -->
                                                                        </div><!-- /.widget-box -->
                                                            <?php }
                                                                }
                                                            } ?>
                                                            <!-- fin vacunacion -->
                                                            <table class="table table-striped table-bordered table-hover">


                                                                <?php
                                                                if (!empty($frm_vacuna['IDVacuna'])) {


                                                                ?>


                                                                    <td>

                                                                        <label> Estado de vacunación? </label>


                                                                    </td>

                                                                    <td>

                                                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm_vacuna["Vacunado"], 'Vacunado', "class='input mandatory'") ?>

                                                                    </td>

                                                            <?php
                                                                }
                                                            }
                                                            ?>




                                                            <?php


                                                            while ($r = $dbo->object($r_campos)) {  ?>
                                                                <tr>

                                                                    <td><?php echo $r->EtiquetaCampo; ?></td>
                                                                    <td>
                                                                        <?php
                                                                        switch ($r->TipoCampo) {
                                                                            case "text": ?>
                                                                                <input type="text" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" class="frmcampos" tipocampo="text" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" obligatorio="<?php echo $r->Obligatorio; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" value="" <?php if ($r->Obligatorio == "S") echo "required";  ?>>
                                                                            <?php
                                                                                break;
                                                                            case "textarea": ?>
                                                                                <textarea class="frmcampos" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="textarea" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>"></textarea>
                                                                                <?php
                                                                                break;
                                                                            case "checkbox":
                                                                            case "radio":
                                                                                if (!empty($r->Valores)) {
                                                                                    $array_valores = explode(",", $r->Valores);
                                                                                    $contador = 1;
                                                                                    foreach ($array_valores as $valor) {
                                                                                        if (!empty($valor)) {
                                                                                            if ($r->TipoCampo == "radio") { ?>
                                                                                                <input class="frmcampos" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="radio" type="radio" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" value="<?php echo $valor; ?>" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?> etiqueta=" <?php echo $r->EtiquetaCampo; ?>""><?php echo $valor; ?>
                                                                                            <?php
                                                                                            } else { ?>
                                                                                                <input class="frmcampos" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="checkbox" type="checkbox" name="Campo<?php echo $r->IDPreguntaAcceso . $contador; ?>" value="<?php echo $valor; ?>" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>"><?php echo $valor; ?><br>
                                                                                <?php
                                                                                                $contador++;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                                ?>
                                                                                <?php
                                                                                break;
                                                                                break;
                                                                            case "select":

                                                                                if (!empty($r->Valores)) { ?>
                                                                                    <select class="frmcampos" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="select" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>">
                                                                                        <option value="">Seleccione</option>
                                                                                        <?php
                                                                                        $array_valores = explode(",", $r->Valores);
                                                                                        foreach ($array_valores as $valor) {
                                                                                            if (!empty($valor)) { ?>
                                                                                                <option value="<?php echo $valor; ?>"><?php echo $valor; ?></option>
                                                                                        <?php
                                                                                            }
                                                                                        } ?>
                                                                                    </select>
                                                                                <?php
                                                                                }
                                                                                ?>
                                                                            <?php
                                                                                break;
                                                                            case "number": ?>
                                                                                <input class="frmcampos" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="text" type="number" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>">
                                                                            <?php
                                                                                break;
                                                                            case "date": ?>
                                                                                <input class="frmcampos" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="text" type="date" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>">
                                                                            <?php
                                                                                break;
                                                                            case "time": ?>
                                                                                <input class="frmcampos" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="text" type="number" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>">
                                                                            <?php
                                                                                break;
                                                                            case "email": ?>
                                                                                <input class="frmcampos" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="text" type="email" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" obligatorio="<?php echo $r->Obligatorio; ?>">
                                                                        <?php
                                                                                break;
                                                                            case "titulo":
                                                                                echo "<br>" . $r->Titulo . "<br>";
                                                                                break;
                                                                        }
                                                                        ?>

                                                                    </td>
                                                                </tr>
                                                            <?php
                                                            } ?>
                                                            </table>

                                                        <?php

                                                    } ?>

                                                        <?php
                                                        if ($modulo == "Socio" || $modulo == "SocioInvitado") { ?>
                                                            <table class="table table-striped table-bordered table-hover">
                                                                <tr>
                                                                    <td colspan="3" align="center" bgcolor="#C9F5CF">
                                                                        RESERVAS
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        Fecha
                                                                    </td>
                                                                    <td>
                                                                        Hora
                                                                    </td>
                                                                    <td>
                                                                        Servicio
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                foreach ($resp["response"] as $key => $value) {
                                                                    $FechaReserva = $value["Fecha"];
                                                                    $HoraReserva = $value["Hora"];
                                                                    $Servicio = $value["NombreServicio"];
                                                                    if ($FechaReserva == date("Y-m-d")) { ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?php echo $FechaReserva; ?>
                                                                            </td>
                                                                            <td>
                                                                                <font color='#F14823'><b><?php echo $HoraReserva; ?></b></font>
                                                                            </td>
                                                                            <td>
                                                                                <?php echo $Servicio; ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php
                                                                    }
                                                                }
                                                                foreach ($resp_como_invitado["response"] as $key => $value) {
                                                                    $FechaReserva = $value["Fecha"];
                                                                    $HoraReserva = $value["Hora"];
                                                                    $Servicio = $value["NombreServicio"];
                                                                    if ($FechaReserva == date("Y-m-d")) { ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?php echo $FechaReserva; ?>
                                                                            </td>
                                                                            <td>
                                                                                <font color='#F14823'><b><?php echo $HoraReserva; ?></b></font>
                                                                            </td>
                                                                            <td>
                                                                                <?php echo $Servicio; ?>
                                                                            </td>
                                                                        </tr>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                                <?php if (SIMUser::get("club") == 189) { ?>
                                                                    <tr>
                                                                        <td colspan="2" style="background-color: #059C1C; color:#fff; text-align: center;">
                                                                            CARTERA
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            C&oacute;digo cliente
                                                                        </td>
                                                                        <!-- <td>
																		Monto
																	</td> -->
                                                                        <td>
                                                                            Mensaje
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $CodigoCliente ?></td>
                                                                        <!-- <td><?php //echo $MontoCliente 
                                                                                    ?></td> -->
                                                                        <td><?= $MensajeCartera ?></td>
                                                                    </tr>
                                                                <?php } ?>
                                                                <?php if (SIMUser::get("club") == 156) { ?>
                                                                    <tr>
                                                                        <table class="table table-striped table-bordered table-hover">
                                                                            <tr>
                                                                                <?php
                                                                                if (!empty($RespuestaCarteraPlayaAzul[0])) { ?>
                                                                                    <td colspan="5" style="background-color: #ff0000; color:#fff; text-align: center;">
                                                                                        <b>
                                                                                            <?= $msjCarteraPlayaAzul ?>
                                                                                        </b>
                                                                                    </td>
                                                                                <?php
                                                                                } else { ?>
                                                                                    <td colspan="5" style="background-color: #fff; color:#00bc08; text-align: center;">
                                                                                        <b>
                                                                                            <?= $msjCarteraPlayaAzul ?>
                                                                                        </b>
                                                                                    </td>
                                                                                <?php
                                                                                }
                                                                                ?>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="5" style="background-color: #059C1C; color:#fff; text-align: center;">
                                                                                    <b>
                                                                                        CARTERA
                                                                                    </b>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <b>
                                                                                        Acci&oacute;n
                                                                                    </b>
                                                                                </td>
                                                                                <td>
                                                                                    <b>
                                                                                        Fecha
                                                                                    </b>
                                                                                </td>
                                                                                <td>
                                                                                    <b>
                                                                                        Detalle
                                                                                    </b>
                                                                                </td>
                                                                                <td>
                                                                                    <b>
                                                                                        Comentario
                                                                                    </b>
                                                                                </td>
                                                                                <td>
                                                                                    <b>
                                                                                        Valor
                                                                                    </b>
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                            foreach ($RespuestaCarteraPlayaAzul as $Aporte) {
                                                                                foreach ($Aporte as $DetalleAporte) {
                                                                                    // echo '<pre>';
                                                                                    // var_dump($DetalleAporte);
                                                                            ?>
                                                                                    <tr>
                                                                                        <td><?= $DetalleAporte['Accion'] ?></td>
                                                                                        <td><?= $DetalleAporte['Fecha'] ?></td>
                                                                                        <td><?= $DetalleAporte['Detalle'] ?></td>
                                                                                        <td><?= $DetalleAporte['Comentario'] ?></td>
                                                                                        <td><?= $DetalleAporte['Valor'] ?></td>
                                                                                    </tr>
                                                                                <?php } ?>
                                                                            <?php } ?>
                                                                        </table>
                                                                    </tr>

                                                                <?php } ?>
                                                            </table>
                                                        <?php
                                                        } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <?php
                                                    if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioInvitado") {
                                                        //se validan las fechas de vencimineto de ARL y EPS para La pradera
                                                        if ($datos_invitado["IDClub"] == 16 && $datos_invitado["FechaVencimientoArl"] != '0000-00-00' && $datos_invitado["FechaVencimientoEps"] != '0000-00-00') {
                                                            if ($datos_invitado["FechaVencimientoArl"] < date('Y-m-d') && $datos_invitado["FechaVencimientoEps"] < date('Y-m-d')) {
                                                                $bloqueado = "S";
                                                                $mensaje_bloqueo = "El Contratista tiene la ARL y EPS vencidas, por favor informar";
                                                            } elseif ($datos_invitado["FechaVencimientoEps"] < date('Y-m-d')) {
                                                                $bloqueado = "S";
                                                                $mensaje_bloqueo = "El Contratista tiene la EPS vencida, por favor informar";
                                                            } elseif ($datos_invitado["FechaVencimientoArl"] < date('Y-m-d')) {
                                                                $bloqueado = "S";
                                                                $mensaje_bloqueo = "El Contratista tiene la ARL vencida, por favor informar";
                                                            }
                                                        }

                                                        if ($datos_invitado["IDEstadoInvitado"] == "2" || $datos_invitado["IDEstadoInvitado"] == "3" || ($datos_invitado["IDEstadoInvitado"] == "0" && SIMUser::get("club") == 9)) {
                                                            $bloqueado = "S";
                                                            if ($datos_invitado["IDEstadoInvitado"] == "0" && $modulo == "SocioAutorizacion") {
                                                                $mensaje_bloqueo = "SIN ESTADO.";
                                                            } elseif ($modulo == "SocioInvitadoEspecial" && (int)$datos_invitado["IDEstadoInvitado"] <= 0) { // A los invitados de socio sin estado no los bloqueo
                                                                $mensaje_bloqueo = "";
                                                                $bloqueado = "N";
                                                            } else {
                                                                $mensaje_bloqueo = $datos_invitado["RazonBloqueo"];
                                                            }
                                                        } else {
                                                            $hora_actual = date("Y-m-d H:i:s");
                                                            //Verifico si tiene algun bloqueo temporal
                                                            $sql_observacion_bloqueo = "Select * From ObservacionInvitado Where IDInvitado = '" . $datos_invitado["IDInvitado"] . "' and FechaInicioBloqueo <= CURDATE() AND FechaFinBloqueo >= CURDATE() Order by IDObservacionInvitado Desc";
                                                            $result_observacion_bloqueo = $dbo->query($sql_observacion_bloqueo);
                                                            while ($row_log_acceso = $dbo->fetchArray($result_observacion_bloqueo)) :
                                                                if ($row_log_acceso["HoraInicioBloqueo"] == "00:00:00" && $row_log_acceso["HoraFinBloqueo"] == "00:00:00") :
                                                                    $bloqueado = "S";
                                                                    if (SIMUser::get("club") != 9) :
                                                                        $mensaje_bloqueo = " (" . $row_log_acceso["Observacion"] . ")";
                                                                    endif;

                                                                else :
                                                                    //Verifico si esta en la hora del bloqueo si es el dia actual
                                                                    $hora_inicio_bloqueo = date("Y-m-d") . " " . $row_log_acceso["HoraInicioBloqueo"];
                                                                    $hora_fin_bloqueo = date("Y-m-d") . " " . $row_log_acceso["HoraFinBloqueo"];
                                                                    if (strtotime($hora_actual) >= strtotime($hora_inicio_bloqueo)  && strtotime($hora_actual) <= strtotime($hora_fin_bloqueo)) {
                                                                        $bloqueado = "S";
                                                                        if (SIMUser::get("club") != 9) :
                                                                            $mensaje_bloqueo = " (" . $row_log_acceso["Observacion"] . ")";
                                                                        endif;
                                                                    }
                                                                endif;
                                                            endwhile;
                                                        }
                                                    } else {
                                                        if ($datos_invitado["IDEstadoSocio"] == "2" || $datos_invitado["IDEstadoSocio"] == "3")
                                                            $bloqueado = "S";
                                                        $mensaje_bloqueo = $datos_invitado["RazonBloqueo"];
                                                    }

                                                    // echo $alerta_diagnostico;

                                                    echo $acepta_exo;

                                                    //echo $alerta_edad;
                                                    ?>
                                                    <?php echo $mensaje_alerta ?>
                                                    <label>
                                                        <?php if ($bloqueado <> "S" && empty($alerta_edad_beneficiario)) : ?>

                                                            <?php
                                                            // echo '<pre>';
                                                            // print_r($row_log_acceso_ultimo);
                                                            // die();
                                                            if ($row_log_acceso_ultimo["Entrada"] == "S") :
                                                                $campo_entrada = "disabled";
                                                            else :
                                                                $campo_entrada = "";
                                                            endif;

                                                            if ($row_log_acceso_ultimo["Salida"] == "S" || $total_log == 0) :
                                                                $campo_salida = "disabled";
                                                            else :
                                                                $campo_salida = "";
                                                            endif;

                                                            if ($PermitirMultipleAcceso == 'S' || $PermitirMultipleAcceso == '') {
                                                                $campo_entrada = "";
                                                                $campo_salida = "";
                                                            }
                                                            //$campo_entrada = "";
                                                            //$campo_salida = "";
                                                            ?>
                                                            <input name="Ingreso_" id="Ingreso" class="ace input-lg ace-checkbox-2 ingreso_accesov2" type="checkbox" tipoacceso="unico" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>" <?php echo $campo_entrada; ?> />
                                                            <span class="lbl"><b>INGRESO</b></span>
                                                    </label>
                                                    <label style="padding-left:40px">
                                                        <input name="Salida" id="Salida" class="ace input-lg ace-checkbox-2 salida_accesov2" type="checkbox" tipoacceso="unico" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>" <?php echo $campo_salida; ?> />
                                                        <span class="lbl"><b>SALIDA</b></span>
                                                    <?php else : ?>
                                                        <span style="color: #F10004">BLOQUEADO</span> <?php echo $mensaje_bloqueo; ?>

                                                        <span style="color: #F10004">BLOQUEADO</span> <?php echo $alerta_edad_beneficiario ?>

                                                    <?php endif; ?>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <label for="">Permitir Ingreso/Salida a todos:</label>
                                                    <br>
                                                    <label>
                                                        <?php if ($bloqueado <> "S" && empty($alerta_edad_beneficiario)) : ?>

                                                            <?php
                                                            if ($row_log_acceso_ultimo["Entrada"] == "S") :
                                                                $campo_entrada = "disabled";
                                                            else :
                                                                $campo_entrada = "";
                                                            endif;

                                                            if ($row_log_acceso_ultimo["Salida"] == "S" || $total_log == 0) :
                                                                $campo_salida = "disabled";
                                                            else :
                                                                $campo_salida = "";
                                                            endif;

                                                            if ($PermitirMultipleAcceso == 'S' || $PermitirMultipleAcceso == '') {
                                                                $campo_entrada = "";
                                                                $campo_salida = "";
                                                            }
                                                            //$campo_entrada = "";
                                                            //$campo_salida = "";
                                                            ?>
                                                            <input name="IngresarTodos" id="IngresarTodos" class="ace input-lg ace-checkbox-2 IngresarTodos" type="checkbox" tipoacceso="unico" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>" <?php echo $campo_entrada; ?> />
                                                            <span class="lbl"><b>INGRESAR TODOS</b></span>
                                                    </label>
                                                    <label style="padding-left:40px">
                                                        <input name="SalidaTodos" id="SalidaTodos" class="ace input-lg ace-checkbox-2 SalidaTodos" type="checkbox" tipoacceso="unico" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>" <?php echo $campo_salida; ?> />
                                                        <span class="lbl"><b>SALIDA TODOS</b></span>
                                                    <?php else : ?>
                                                        <span style="color: #F10004">BLOQUEADO</span> <?php echo $mensaje_bloqueo; ?>

                                                        <span style="color: #F10004">BLOQUEADO</span> <?php echo $alerta_edad_beneficiario ?>

                                                    <?php endif; ?>
                                                    </label>
                                                </td>
                                            </tr>
                                            <?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioInvitado") {  ?>
                                                <tr>
                                                    <td colspan="2">Alertas:
                                                        <?php
                                                        echo $datos_invitado["ObservacionGeneral"];
                                                        echo "<br>" . $datos_invitacion["ObservacionSocio"];
                                                        //Consulto el historial de alertas
                                                        $sql_observacion = "Select * From ObservacionInvitado Where IDInvitado = '" . $datos_invitado["IDInvitado"] . "' Order by IDObservacionInvitado Desc";
                                                        $result_observacion = $dbo->query($sql_observacion);
                                                        while ($row_observacion = $dbo->fetchArray($result_observacion)) :
                                                            if (SIMUser::get("club") == 9) {
                                                                include("anotacion.php");
                                                                break;
                                                            } else {
                                                                echo "<br>" . $row_observacion["Observacion"];
                                                                if (!empty($row_observacion["FechaInicioBloqueo"]) && $row_observacion["FechaInicioBloqueo"] != "0000-00-00" && !empty($row_observacion["FechaFinBloqueo"]) && $row_observacion["FechaFinBloqueo"] != "0000-00-00") {
                                                                    echo "<br>Inicio Bloqueo: " . $row_observacion["FechaInicioBloqueo"] . " " . $row_observacion["HoraInicioBloqueo"];
                                                                    echo "<br>Fin Bloqueo: " . $row_observacion["FechaFinBloqueo"] . " " . $row_observacion["HoraFinBloqueo"];
                                                                }
                                                                echo "<br>";
                                                            }
                                                        endwhile;
                                                        //consulto el resto de la info del formulario dinamico
                                                        $sql_otros = "SELECT Valor, EtiquetaCampo
																	FROM SocioAutorizacionOtrosDatos SAOD, CampoFormularioContratista CFC
																	WHERE SAOD.IDCampoFormularioContratista= CFC.IDCampoFormularioContratista and  SAOD.IDSocioAutorizacion = '" . $id_registro . "'";
                                                        $r_otros = $dbo->query($sql_otros);
                                                        while ($row_otros = $dbo->fetchArray($r_otros)) {
                                                            echo "<br><font size='2px'>" . $row_otros["EtiquetaCampo"] . ": " . $row_otros["Valor"] . "</font>";
                                                        }

                                                        //consulto el resto de la info del formulario dinamico de invitados
                                                        $sql_otros = "SELECT Valor, EtiquetaCampo
																	FROM SocioInvitadoEspecialOtrosDatos SAOD, CampoFormularioInvitado CFC
																	WHERE SAOD.IDCampoFormularioInvitado = CFC.IDCampoFormularioInvitado and  SAOD.IDSocioInvitadoEspecial = '" . $id_registro . "'";
                                                        $r_otros = $dbo->query($sql_otros);
                                                        while ($row_otros = $dbo->fetchArray($r_otros)) {
                                                            echo "<br><font size='2px'>" . $row_otros["EtiquetaCampo"] . ": " . $row_otros["Valor"] . "</font>";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            } ?>
                                            <tr>
                                                <td colspan="2">
                                                    <div style="overflow:scroll;height:200px;">
                                                        Entradas/Salidas registradas: <?php echo date("Y-m-d"); ?>:
                                                        <?php
                                                        if ($datos_invitado["SocioAusente"] == "S") {
                                                            echo "<br>" . $mesanje_ausente;
                                                        }
                                                        //Consulto el historial de entradas y salidas del dia


                                                        switch (trim($datos_invitacion["TipoInvitacion"])) {
                                                            case "InvitadoAcceso":
                                                                $condicion_log_acceso = " AND Tipo = 'InvitadoAcceso'";

                                                                break;
                                                            case "Contratista":
                                                                $condicion_log_acceso = " AND Tipo = 'Contratista'";


                                                                break;
                                                            case "SocioInvitado":
                                                                $condicion_log_acceso = " AND Tipo = 'SocioInvitado'";
                                                                break;
                                                            case "Invitado":
                                                                $condicion_log_acceso = " AND (Tipo = 'InvitadoAcceso' or  Tipo = 'SocioInvitado' )";

                                                                break;
                                                            case "Socio":
                                                                $condicion_log_acceso = " AND Tipo = 'Socio'";


                                                                break;
                                                            case "Usuario":
                                                                $condicion_log_acceso = " AND Tipo = 'Usuario'";

                                                                break;
                                                        }

                                                        //print_r($datos_invitacion);
                                                        $sql_log_acceso = "SELECT * FROM LogAcceso WHERE IDClub = " . SIMUser::get("club") . " AND IDInvitacion = '" . $id_registro . "' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '" . date("Y-m-d") . "' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '" . date("Y-m-d") . "')   $condicion_log_acceso Order by IDLogAcceso Desc";

                                                        $result_log_acceso = $dbo->query($sql_log_acceso);
                                                        while ($row_log_acceso = $dbo->fetchArray($result_log_acceso)) :
                                                            if ($row_log_acceso)
                                                                if ($row_log_acceso["Entrada"] == "S") :
                                                                    echo "<br>Entrada: " . substr($row_log_acceso["FechaIngreso"], 11) . " Mecanismo: $row_log_acceso[Mecanismo]";
                                                                elseif ($row_log_acceso["Salida"] == "S") :
                                                                    echo "<br>Salida: " . substr($row_log_acceso["FechaSalida"], 11) . " Mecanismo: $row_log_acceso[Mecanismo]";
                                                                endif;
                                                        endwhile;
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <?php
                                    if ($datos_invitacion["CabezaInvitacion"] == "S") :
                                        if (SIMUser::get('club') == 10 && $TipoBusqueda == "Documento") :

                                        else :
                                            $contadorFamiliar = 0;

                                            while ($datos_grupo_familiar = $dbo->fetchArray($result_grupo)) :
                                                $contador_grupo++;
                                                $bloqueado = "";
                                                if ($nucleo_socio == "1") :
                                                    $datos_invitado_familiar = $datos_grupo_familiar;
                                                    $id_registro = $datos_invitado_familiar["IDSocio"];
                                                    $resp = SIMWebServiceReservas::get_reservas_socio(SIMUser::get("club"), $datos_invitado_familiar["IDSocio"], $Limite, $IDReserva, $IDUsuario);
                                                    $resp_como_invitado = SIMWebServiceReservas::get_reservas_socio_invitado(SIMUser::get("club"), $id_registro, 0, "", $IDUsuario);
                                                    //Verifica diagnostico
                                                    $alerta_diagnostico_fam = "";
                                                    $alerta_edad_fam = "";
                                                    $fecha_hoy = date("Y-m-d") . " 00:00:00";
                                                    $sql_unica = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDSocio='" . $datos_invitado_familiar["IDSocio"] . "' GROUP BY IDSocio ";
                                                    $r_unica = $dbo->query($sql_unica);
                                                    $total_unica = $dbo->rows($r_unica);
                                                    $row_resp_diag = $dbo->fetchArray($r_unica);
                                                    $peso_permitido = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' ");
                                                    if (SIMUser::get("club") != 70) :
                                                        if ($total_unica <= 0) :
                                                            if (SIMUser::get('club') != 10) :
                                                                $alerta_diagnostico_fam = "<font color='#F14823'><b> Atención la persona no ha llenado el diagnostico</b></font><br>";
                                                            endif;
                                                        elseif ($row_resp_diag["Resultado"] > $peso_permitido) :
                                                            $alerta_diagnostico_fam = "<font color='#F14823'><b> El Socio y su grupo familiar No pueden ingresar  </b></font><br>";
                                                        endif;
                                                    else :
                                                        if ($total_unica <= 0) {
                                                            $alerta_diagnostico_fam = "<font color='#F14823' size='4px'><b> Atención la persona no ha aceptado la exoneración de responsabilidades</b></font><br>";
                                                        }
                                                    endif;

                                                    // Observacion general
                                                    $ObservacionGeneralInvitado = $datos_invitado_familiar['ObservacionGeneral'];
                                                    // Fin Observacion general

                                                    // BUSACMOS LOS CLUBES QUE TENGAN CONFIGURACIÓN DE VACUNACIÓN CREADA, ESOS YA TIENEN EL MODULO ACTIVO
                                                    $SQLConfiguracion = "SELECT * FROM ConfiguracionVacunacion2 WHERE IDClub = " . SIMUser::get("club");
                                                    $QRYConfiguracion = $dbo->query($SQLConfiguracion);

                                                    $Campo = "IDSocio";

                                                    $AlertaVacunado = false; //LA ALERTA SIEMPRE EN TRUE Y SE CAMBIA SI ES NECESARIO

                                                    if ($dbo->rows($QRYConfiguracion) > 0) :
                                                        // BUSCAMOS SI ESTA O NO VACUNADO PARA PONER EL AVISO
                                                        $SQLVacuna2 = "SELECT * FROM Vacuna2 WHERE $Campo ='$id_registro'";
                                                        $QRYVacuna2 = $dbo->query($SQLVacuna2);
                                                        if ($dbo->rows($QRYVacuna2) > 0) :
                                                            $AlertaVacunado = false;
                                                        else :
                                                            $SQLVacuna2 = "SELECT * FROM Vacunado WHERE $Campo ='$id_registro'";
                                                            $QRYVacuna2 = $dbo->query($SQLVacuna2);
                                                            $datoVacunado = $dbo->fetchArray($QRYVacuna2);

                                                            if (!empty($datoVacunado[CodigoQrGobierno]) || !empty($datoVacunado[ArchivoVacuna])) :
                                                                $AlertaVacunado = false;
                                                            endif;
                                                        endif;
                                                    else :
                                                        // VERFICAMOS LA VERSION 1 DE VACUANCIÓN
                                                        $SQLConfiguracion = "SELECT * FROM ConfiguracionVacunacion WHERE IDClub = " . SIMUser::get("club");
                                                        $QRYConfiguracion = $dbo->query($SQLConfiguracion);

                                                        if ($dbo->rows($QRYConfiguracion) > 0) :
                                                            // BUSCAMOS SI ESTA O NO VACUNADO PARA PONER EL AVISO
                                                            $SQLVacuna = "SELECT * FROM Vacuna WHERE $Campo ='$id_registro'";
                                                            $QRYVacuna = $dbo->query($SQLVacuna);
                                                            if ($dbo->rows($QRYVacuna) > 0) :
                                                                $AlertaVacunado = false;
                                                            endif;
                                                        endif;
                                                    endif;

                                                    if ($AlertaVacunado) :
                                                        $alerta_diagnostico_fam .= "<font color='#F14823' size='4px'><b> Atención la persona no ha llenado su información de vacunación.</b></font><br>";
                                                    else :
                                                        $parametros_codigo_qr = URLROOT . "PaginaQRVacunacion.php?Fuente=PC&IDClub=" . SIMUser::get("club") . "&$Campo=$id_registro";

                                                        $alerta_diagnostico_fam .= "<font color='#059C1C' size='4px'><b> Datos de vacunación registrados </b></font><br>";
                                                        $alerta_diagnostico_fam .= "<a href = '$parametros_codigo_qr'><font color='#059C1C' size='2px'><b> Ver Información Vacunación </b></font></a><br>";
                                                    endif;

                                                    $alerta_edad_beneficiario = null;
                                                    //Si es el club el rincon valida edad de hijos e hijastros
                                                    if (SIMUser::get("club") == 10 || SIMUser::get("club") == 8) {
                                                        $dia_actua = date("Y-M-D");
                                                        $fecha_nacimiento = $datos_invitado_familiar["FechaNacimiento"];
                                                        $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
                                                        $edadSocio = $edad_diff->format('%y');

                                                        $IDParentesco = $datos_invitado_familiar["IDParentesco"];

                                                        if ((($IDParentesco == 10 || $IDParentesco == 9) && $edadSocio > 25)
                                                            || ($IDParentesco == 8 || $IDParentesco == 7) && $edadSocio > 30
                                                        ) {
                                                            $alerta_edad_beneficiario = "<font color='#F14823'><b> No tiene la edad permitida para tipo su de relación familiar</b></font><br>";
                                                        }
                                                    }

                                                    if (SIMUser::get("club") == 9) {
                                                        $MostrarExo = "S";
                                                        $sql_exo = "SELECT  Valor FROM  Encuesta2Respuesta WHERE IDEncuesta2 = 12 and IDPreguntaEncuesta2 = 44 and IDSocio='" . $id_registro . "' LIMIT 1 ";
                                                        $r_exo = $dbo->query($sql_exo);
                                                        $row_exo = $dbo->fetchArray($r_exo);
                                                        if ($row_exo["Valor"] == "Acepto") {
                                                            $acepta_exo_fam = "<font color='#059C1C'><b>Exoneracion: </b> Aceptada</font><br>";
                                                        } else {
                                                            $acepta_exo_fam = "<font color='#F14823'><b>Exoneracion: </b> No ha diligenciado</font><br>";
                                                        }
                                                    }


                                                    if ($datos_invitado_familiar["FechaNacimiento"] != "0000-00-00") {
                                                        $fecha_nacimiento = $datos_invitado_familiar["FechaNacimiento"];
                                                        $dia_actual = date("Y-m-d");
                                                        $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
                                                        $EdadSocio = $edad_diff->format('%y');
                                                        if ($EdadSocio >= 18 && $EdadSocio <= 70) {
                                                            //$alerta_edad="<font color='#059C1C'><b> Edad correcta</b></font><br>";
                                                        } else {
                                                            //$alerta_edad_fam="<font color='#F14823'><b> No tiene la edad permitida  </b></font><br>";
                                                        }
                                                    } else {
                                                        //$alerta_edad_fam="<font color='#F14823'><b> Sin edad</b></font><br>";
                                                    }
                                                else :
                                                    $id_registro = $datos_grupo_familiar["IDSocioInvitadoEspecial"];
                                                    $datos_invitado_familiar = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_grupo_familiar["IDInvitado"] . "' ", "array");
                                                endif;

                                                //caso especial lagartos no permite a padres o madre mas de 4 veces al mes
                                                if (SIMUser::get("club") == 7 && ($datos_grupo_familiar["IDParentesco"] == "14" || $datos_grupo_familiar["IDParentesco"] == "13" || $datos_socio["IDParentesco"] == "16" || $datos_socio["IDParentesco"] == "17")) {
                                                    $TotalIngresos = SIMWebServiceUsuarios::valida_cantidad_ingresos(SIMUser::get("club"), $datos_grupo_familiar["IDSocio"]);
                                                    if ((int)$TotalIngresos >= (int)$IngresoPermitidoLag) {
                                                        $bloqueado = "S";
                                                        $mensaje_bloqueo = "<font color='#F14823'><b> Atención la persona ha ingresado mas de " . $IngresoPermitidoLag . " veces </b></font><br>";
                                                        $datos_invitado["RazonBloqueo"] = $mensaje_bloqueo;
                                                    }
                                                }
                                                //Fin Caso especial
                                    ?>
                                                <td valign="top">
                                                    <table class="table table-striped table-bordered table-hover" style="width:30%">
                                                        <tr>
                                                            <td valign="top" width="100">
                                                                <?
                                                                if ($modulo == "Socio") :
                                                                    $ruta_foto = SOCIO_ROOT;
                                                                    $nombre_foto = "Foto";
                                                                else :
                                                                    $ruta_foto = IMGINVITADO_ROOT;
                                                                    $nombre_foto = "FotoFile";
                                                                endif;

                                                                if (!empty($datos_invitado_familiar[$nombre_foto])) {
                                                                    echo "<img src='" . $ruta_foto . "$datos_invitado_familiar[$nombre_foto]' width='100' height='120' >";
                                                                } else {
                                                                    echo "<img src='assets/images/sinfoto.png' width='100' height='120' > ";
                                                                }
                                                                $identificador = $datos_invitado_familiar["IDSocio"]
                                                                ?>
                                                                <a class="fancybox" href="../admin/tomarfoto/webcamjquery/foto.php?action=foto&IDRegistro=<?php echo $identificador; ?>&Modulo=<?php echo $modulo; ?>" data-fancybox-type="iframe">
                                                                    <i class="ace-icon fa fa-camera bigger-120"></i>
                                                                    <span class="bigger-110">Tomar Foto</span>
                                                                </a>
                                                                &nbsp;
                                                            </td>
                                                            <td valign="top">
                                                                <table class="table table-striped table-bordered table-hover">
                                                                    <tr>
                                                                        <td>&nbsp;
                                                                            <?php echo $datos_invitado_familiar["Nombre"] . " " . $datos_invitado_familiar["Apellido"]  ?>
                                                                        </td>
                                                                    </tr>

                                                                    <?php if ($fecha_nacimiento != '') { ?>
                                                                        <tr>
                                                                            <td style="color: #f00000;">&nbsp;
                                                                                <strong>
                                                                                    Edad: <?= SIMUtil::Calcular_Edad($fecha_nacimiento) ?>
                                                                                </strong>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>&nbsp;
                                                                                Fecha Nacimiento: <?php echo $fecha_nacimiento ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>

                                                                    <?php if ($CampoObservacionGeneral != '' && $ObservacionGeneralInvitado != '') { ?>
                                                                        <tr>
                                                                            <td>&nbsp;
                                                                                Observaci&oacute;n general: <?php echo $ObservacionGeneralInvitado ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                    <!--<tr>
														<td>&nbsp;
														<?php
                                                        $tipo_doc = "";
                                                        $tipo_doc = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'");
                                                        if (empty($tipo_doc)) :
                                                            echo "Documento";
                                                        else :
                                                            echo $tipo_doc;
                                                        endif;
                                                        ?>
														<?php echo $datos_invitado_familiar["NumeroDocumento"];  ?>
														</td>
														</tr>
													-->
                                                                    <tr>
                                                                        <td>
                                                                            <?php if (SIMUser::get("club") != 10) : ?>
                                                                                <br>
                                                                                <?php
                                                                                //ARL
                                                                                if (empty($datos_invitado["FechaVencimientoArl"])) :
                                                                                    echo '<span style="color: #F10004">Sin fecha ARL</span>';
                                                                                elseif (strtotime($datos_invitado["FechaVencimientoArl"]) < strtotime(date("Y-m-d"))) :
                                                                                    echo '<span style="color: #F10004">ARL Vencido</span>';
                                                                                else :
                                                                                    echo "<strong>ARL al dia</strong>";
                                                                                endif;
                                                                                ?>
                                                                                &nbsp;Vehiculo:
                                                                                <?php
                                                                                //Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
                                                                                if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial") :
                                                                                    $condicion_vehiculo = " AND IDInvitado = '" . $datos_invitado_familiar["IDInvitado"] . "'";
                                                                                elseif ($modulo == "Socio") :
                                                                                    $condicion_vehiculo = " AND IDSocio = '" . $datos_invitado_familiar["IDSocio"] . "'";
                                                                                else :
                                                                                    $condicion_vehiculo = " AND IDSocio = '-1'";
                                                                                endif;
                                                                                ?>
                                                                                <?php
                                                                                unset($array_placa);
                                                                                //datos vehiculo
                                                                                $sql_vehiculo = "Select * From Vehiculo Where 1 " . $condicion_vehiculo;
                                                                                $result_vehiculo = $dbo->query($sql_vehiculo);
                                                                                $cont_vehiculo = 0;
                                                                                while ($row_vehiculo = $dbo->fetchArray($result_vehiculo)) :
                                                                                    $cont_vehiculo++;
                                                                                    $array_placa[] = strtoupper($row_vehiculo["Placa"]);
                                                                                    echo "Placa: " . strtoupper($row_vehiculo["Placa"]) . "<br>";

                                                                                    if (empty($row_vehiculo["FechaTecnomecanica"])) :
                                                                                        echo '<span style="color: #F10004">Sin fecha tecnomecanica</span>';
                                                                                    elseif (strtotime($row_vehiculo["FechaTecnomecanica"]) < strtotime(date("Y-m-d"))) :
                                                                                        echo '<span style="color: #F10004">Tecnomecanica Vencida</span>';
                                                                                    else :
                                                                                        echo "Tecnomecanica al dia";
                                                                                    endif;

                                                                                    echo "<br>";
                                                                                    //SOAT
                                                                                    if (empty($row_vehiculo["FechaSeguro"])) :
                                                                                        echo '<span style="color: #F10004">Sin fecha SOAT</span>';
                                                                                    elseif (strtotime($row_vehiculo["FechaSeguro"]) < strtotime(date("Y-m-d"))) :
                                                                                        echo '<span style="color: #F10004">SOAT Vencido</span>';
                                                                                    else :
                                                                                        echo "SOAT al dia";
                                                                                    endif;
                                                                                endwhile;

                                                                                if (SIMUser::get("club") == 7) {
                                                                                    echo "Observaciones: " . $datos_invitado_familiar["ObservacionGeneral"];
                                                                                }
                                                                                ?>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                                &nbsp;
                                                                <label>
                                                                    <input name="MecanismoEntradaIngreso_<?php echo $contadorFamiliar ?>" id="Peatonal_<?php echo $contadorFamiliar ?>" type="radio" class="ace input-lg Peatonal" value="Peatonal" <?php echo (((SIMUser::get("club") == 44  || SIMUser::get("club") == 10) && $mecanismo == "Peatonal" && $SalidaLog = "S") ? " checked" : "") ?> />
                                                                    <span class="lbl">Peatonal</span>
                                                                </label>
                                                                <?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 52) { ?>
                                                                    <label>
                                                                        <input name="MecanismoEntradaIngreso_<?php echo $contadorFamiliar ?>" type="radio" id="Acompañante_Vehiculo" class="ace input-lg tipoentrada Acompañante_Vehiculo" value="Acompañante Vehiculo" />
                                                                        <span class="lbl">Acompañante vehiculo</span>
                                                                    </label>
                                                                <?php
                                                                } ?>
                                                                <?php //if (SIMUser::get("club") == 10 || SIMUser::get("club") == 8) { 
                                                                ?>
                                                                <label>
                                                                    <input name="MecanismoEntradaIngreso_<?php echo $contadorFamiliar ?>" id="Vehiculo_<?php echo $contadorFamiliar ?>" type="radio" class="ace input-lg tipoentrada Vehiculo" value="Vehiculo" />
                                                                    <span class="lbl">Vehiculo</span>
                                                                </label>
                                                                <input type="text" placeholder="Ingrese placa" id="PlacaVehiculo_<?php echo $contadorFamiliar ?>" name="PlacaVehiculo" class="PlacaVehiculo">
                                                                <?php //} 
                                                                ?>
                                                                <?php
                                                                if (count($array_placa) > 0) :
                                                                    foreach ($array_placa as $placa_vehiculo) : ?>
                                                                        <label>
                                                                            <input name="MecanismoEntradaIngreso_<?php echo $contadorFamiliar ?>" type="radio" class="ace input-lg" value="Vehiculo <?php echo $placa_vehiculo; ?>" />
                                                                            <span class="lbl"><?php echo $placa_vehiculo; ?></span>
                                                                        </label>
                                                                <?php
                                                                    endforeach;
                                                                endif;
                                                                ?>
                                                                <label>
                                                                    <!--input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg" value="OtroVehiculo"/-->
                                                                    <span class="lbl">
                                                                        <?php
                                                                        if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial") :
                                                                            $link_otro_vehiculo = "invitadosgeneral.php?action=edit&id=" . $datos_invitado_familiar["IDInvitado"] . "&editarinfo=n&tabinvitado=vehiculos";
                                                                        else :
                                                                            $link_otro_vehiculo = "socios.php?action=edit&id=" . $datos_invitado_familiar["IDSocio"] . "&editarinfo=n&tabsocio=vehiculos";
                                                                        endif;
                                                                        ?>
                                                                        <?php if (SIMUser::get("club") != 10) : ?>
                                                                            <a class="fancybox_vehiculo" href="<?php echo $link_otro_vehiculo ?>" data-fancybox-type="iframe">
                                                                                <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                                                <span class="bigger-110">Agregar Veh&iacute;culo</span>
                                                                            </a>
                                                                        <?php endif; ?>
                                                                        <a class="fancybox_vehiculo" href="accesoinvitado.php?action=imprimir-carnet<? echo $paramsCarnet ?>" data-fancybox-type="iframe">
                                                                            <i class="ace-icon fa fa-print bigger-120"></i>
                                                                            <span class="bigger-110">Imprimir Carnet</span>
                                                                        </a>
                                                                    </span>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                                <?php
                                                                //Campos personalizados
                                                                $sql_campos = "SELECT * FROM PreguntaAcceso WHERE IDClub = '" . SIMUser::get("club") . "' and Publicar = 'S'  ORDER BY Orden ASC";
                                                                $r_campos = $dbo->query($sql_campos);
                                                                if ($dbo->rows($r_campos) > 0) { ?>

                                                                    <table class="table table-striped table-bordered table-hover">
                                                                        <?php
                                                                        while ($r = $dbo->object($r_campos)) {  ?>
                                                                            <tr>
                                                                                <td><?php echo $r->EtiquetaCampo; ?></td>
                                                                                <td>
                                                                                    <?php
                                                                                    switch ($r->TipoCampo) {
                                                                                        case "text": ?>
                                                                                            <input type="text" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" class="frmcamposfam" tipocampo="text" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" obligatorio="<?php echo $r->Obligatorio; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" value="" <?php if ($r->Obligatorio == "S") echo "required";  ?>>
                                                                                        <?php
                                                                                            break;
                                                                                        case "textarea": ?>
                                                                                            <textarea class="frmcamposfam<?php echo $datos_invitado_familiar["IDSocio"]; ?>" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="textarea" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>"></textarea>
                                                                                            <?php
                                                                                            break;
                                                                                        case "checkbox":
                                                                                        case "radio":
                                                                                            if (!empty($r->Valores)) {
                                                                                                $array_valores = explode(",", $r->Valores);
                                                                                                $contador = 1;
                                                                                                foreach ($array_valores as $valor) {
                                                                                                    if (!empty($valor)) {
                                                                                                        if ($r->TipoCampo == "radio") { ?>
                                                                                                            <input class="frmcamposfam<?php echo $datos_invitado_familiar["IDSocio"]; ?>" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="radio" type="radio" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" value="<?php echo $valor; ?>" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?> etiqueta=" <?php echo $r->EtiquetaCampo; ?>""><?php echo $valor; ?>
                                                                                                        <?php
                                                                                                        } else { ?>
                                                                                                            <input class="frmcamposfam<?php echo $datos_invitado_familiar["IDSocio"]; ?>" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="checkbox" type="checkbox" name="Campo<?php echo $r->IDPreguntaAcceso . $contador; ?>" value="<?php echo $valor; ?>" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>"><?php echo $valor; ?><br>
                                                                                            <?php
                                                                                                            $contador++;
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                            <?php
                                                                                            break;
                                                                                            break;
                                                                                        case "select":
                                                                                            if (!empty($r->Valores)) { ?>
                                                                                                <select class="frmcamposfam<?php echo $datos_invitado_familiar["IDSocio"]; ?>" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="select" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>">
                                                                                                    <?php
                                                                                                    $array_valores = explode(",", $r->Valores);
                                                                                                    foreach ($array_valores as $valor) {
                                                                                                        if (!empty($valor)) { ?>
                                                                                                            <option value="<?php echo $valor; ?>"><?php echo $valor; ?></option>
                                                                                                    <?php
                                                                                                        }
                                                                                                    } ?>
                                                                                                </select>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                        <?php
                                                                                            break;
                                                                                        case "number": ?>
                                                                                            <input class="frmcamposfam<?php echo $datos_invitado_familiar["IDSocio"]; ?>" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="text" type="number" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>">
                                                                                        <?php
                                                                                            break;
                                                                                        case "date": ?>
                                                                                            <input class="frmcamposfam<?php echo $datos_invitado_familiar["IDSocio"]; ?>" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="text" type="date" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>">
                                                                                        <?php
                                                                                            break;
                                                                                        case "time": ?>
                                                                                            <input class="frmcamposfam<?php echo $datos_invitado_familiar["IDSocio"]; ?>" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="text" type="number" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" <?php if ($r->Obligatorio == "S") echo "required";  ?> obligatorio="<?php echo $r->Obligatorio; ?>">
                                                                                        <?php
                                                                                            break;
                                                                                        case "email": ?>
                                                                                            <input class="frmcamposfam<?php echo $datos_invitado_familiar["IDSocio"]; ?>" identificadorpregunta="<?php echo $r->IDPreguntaAcceso; ?>" tipocampo="text" type="email" id="Campo<?php echo $r->IDPreguntaAcceso; ?>" name="Campo<?php echo $r->IDPreguntaAcceso; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" etiqueta="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="" obligatorio="<?php echo $r->Obligatorio; ?>">
                                                                                    <?php
                                                                                            break;
                                                                                        case "titulo":
                                                                                            echo "<br>" . $r->Titulo . "<br>";
                                                                                            break;
                                                                                    }
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                        } ?>
                                                                    </table>
                                                                <?php
                                                                } ?>
                                                                <?php
                                                                if ($modulo == "Socio" || $modulo == "SocioInvitado") { ?>
                                                                    <table class="table table-striped table-bordered table-hover">
                                                                        <tr>
                                                                            <td colspan="3" align="center" bgcolor="#C9F5CF">
                                                                                RESERVAS
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Fecha
                                                                            </td>
                                                                            <td>
                                                                                Hora
                                                                            </td>
                                                                            <td>
                                                                                Servicio
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        foreach ($resp["response"] as $key => $value) {
                                                                            $FechaReserva = $value["Fecha"];
                                                                            $HoraReserva = $value["Hora"];
                                                                            $Servicio = $value["NombreServicio"];
                                                                            if ($FechaReserva == date("Y-m-d")) { ?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <?php echo $FechaReserva; ?>
                                                                                    </td>
                                                                                    <td>
                                                                                        <font color='#F14823'><b><?php echo $HoraReserva; ?></b></font>
                                                                                    </td>
                                                                                    <td>
                                                                                        <?php echo $Servicio; ?>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php
                                                                            }
                                                                        }
                                                                        foreach ($resp_como_invitado["response"] as $key => $value) {
                                                                            $FechaReserva = $value["Fecha"];
                                                                            $HoraReserva = $value["Hora"];
                                                                            $Servicio = $value["NombreServicio"];
                                                                            if ($FechaReserva == date("Y-m-d")) { ?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <?php echo $FechaReserva; ?>
                                                                                    </td>
                                                                                    <td>
                                                                                        <font color='#F14823'><b><?php echo $HoraReserva; ?></b></font>
                                                                                    </td>
                                                                                    <td>
                                                                                        <?php echo $Servicio; ?>
                                                                                    </td>
                                                                                </tr>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </table>
                                                                <?php
                                                                } ?>
                                                                <?php
                                                                if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioInvitado") {
                                                                    if ($datos_invitado["IDEstadoInvitado"] == "2" || $datos_invitado["IDEstadoInvitado"] == "3") {
                                                                        $bloqueado = "S";
                                                                        $mensaje_bloqueo = $datos_invitado["RazonBloqueo"];
                                                                    } else {
                                                                        $hora_actual = date("Y-m-d H:i:s");
                                                                        //Verifico si tiene algun bloqueo temporal
                                                                        $sql_observacion_bloqueo = "Select * From ObservacionInvitado Where IDInvitado = '" . $datos_invitado["IDInvitado"] . "' and FechaInicioBloqueo <= CURDATE() AND FechaFinBloqueo >= CURDATE() Order by IDObservacionInvitado Desc";
                                                                        $result_observacion_bloqueo = $dbo->query($sql_observacion_bloqueo);
                                                                        while ($row_log_acceso = $dbo->fetchArray($result_observacion_bloqueo)) :
                                                                            if ($row_log_acceso["HoraInicioBloqueo"] == "00:00:00" && $row_log_acceso["HoraFinBloqueo"] == "00:00:00") :
                                                                                $bloqueado = "S";
                                                                                if (SIMUser::get("club") != 9) :
                                                                                    $mensaje_bloqueo = " (" . $row_log_acceso["Observacion"] . ")";
                                                                                endif;
                                                                            else :
                                                                                //Verifico si esta en la hora del bloqueo si es el dia actual
                                                                                $hora_inicio_bloqueo = date("Y-m-d") . " " . $row_log_acceso["HoraInicioBloqueo"];
                                                                                $hora_fin_bloqueo = date("Y-m-d") . " " . $row_log_acceso["HoraFinBloqueo"];
                                                                                if (strtotime($hora_actual) >= strtotime($hora_inicio_bloqueo)  && strtotime($hora_actual) <= strtotime($hora_fin_bloqueo)) {
                                                                                    $bloqueado = "S";
                                                                                    if (SIMUser::get("club") != 9) :
                                                                                        $mensaje_bloqueo = " (" . $row_log_acceso["Observacion"] . ")";
                                                                                    endif;
                                                                                }
                                                                            endif;
                                                                        endwhile;
                                                                    }
                                                                } else {
                                                                    if ($datos_invitado_familiar["IDEstadoSocio"] == "2" || $datos_invitado_familiar["IDEstadoSocio"] == "3") {
                                                                        $bloqueado = "S";
                                                                        $mensaje_bloqueo = "por favor comuniquese con administracion.";
                                                                    }
                                                                }
                                                                //Verifico Cual fue el ultimo movimeinto registrado en el dia para saber si se activa la entrada o la salida
                                                                //$sql_log_acceso_ultimo= "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc Limit 1";
                                                                $sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '" . $id_registro . "' Order by IDLogAcceso Desc Limit 1";
                                                                $result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
                                                                $row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
                                                                $total_log = $dbo->rows($result_log_acceso_ultimo);
                                                                if ($row_log_acceso_ultimo["Entrada"] == "S") :
                                                                    $campo_entrada = "disabled";
                                                                else :
                                                                    $campo_entrada = "";
                                                                endif;

                                                                if ($row_log_acceso_ultimo["Salida"] == "S" || $total_log == 0) :
                                                                    $campo_salida = "disabled";
                                                                else :
                                                                    $campo_salida = "";
                                                                endif;

                                                                if ($PermitirMultipleAcceso == 'S' || $PermitirMultipleAcceso == '') {
                                                                    $campo_entrada = "";
                                                                    $campo_salida = "";
                                                                }

                                                                // echo $alerta_diagnostico_fam;
                                                                echo $alerta_edad_fam;
                                                                echo $acepta_exo_fam;

                                                                if ($bloqueado <> 'S' && empty($alerta_edad_beneficiario)) { ?>
                                                                    <label>
                                                                        <input name="Ingreso" id="Ingreso_<?php echo $contadorFamiliar ?>" class="ace input-lg ace-checkbox-2 ingreso_accesov2" type="checkbox" tipoacceso="familia" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>" <?php echo $campo_entrada; ?> />
                                                                        <span class="lbl"><b>INGRESO</b></span>
                                                                    </label>
                                                                    <label>
                                                                        <input name="Salida" id="Salida_<?php echo $contadorFamiliar ?>" class="ace input-lg ace-checkbox-2 salida_accesov2" type="checkbox" tipoacceso="familia" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>" <?php echo $campo_salida; ?> />
                                                                        <span class="lbl"><b>SALIDA</b></span>
                                                                    </label>
                                                                <?php
                                                                } else { ?>
                                                                    <span style="color: #F10004">BLOQUEADO</span> <?php echo $mensaje_bloqueo . ": " . $datos_invitado["RazonBloqueo"]; ?>

                                                                    <span style="color: #F10004">BLOQUEADO</span> <?php echo $alerta_edad_beneficiario ?>

                                                                <?php
                                                                } ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioInvitado") {  ?>
                                                            <tr>
                                                                <td colspan="2">
                                                                    Alertas:
                                                                    <?php
                                                                    //Consulto el historial de alertas
                                                                    $sql_observacion = "Select * From ObservacionInvitado Where IDInvitado = '" . $datos_invitado_familiar["IDInvitado"] . "' Order by IDObservacionInvitado Desc";
                                                                    $result_observacion = $dbo->query($sql_observacion);
                                                                    while ($row_observacion = $dbo->fetchArray($result_observacion)) :
                                                                        if (SIMUser::get("club") == 9) {
                                                                            include("anotacion.php");
                                                                            break;
                                                                        } else {
                                                                            echo $row_observacion["Observacion"];
                                                                            if (!empty($row_observacion["FechaInicioBloqueo"]) && $row_observacion["FechaInicioBloqueo"] != "0000-00-00" && !empty($row_observacion["FechaFinBloqueo"]) && $row_observacion["FechaFinBloqueo"] != "0000-00-00") {
                                                                                echo "<br>Inicio Bloqueo:" . $row_observacion["FechaInicioBloqueo"] . " " . $row_observacion["HoraInicioBloqueo"];
                                                                                echo "<br>Fin Bloqueo:" . $row_observacion["FechaFinBloqueo"] . " " . $row_observacion["HoraFinBloqueo"];
                                                                            }
                                                                            echo "<br>";
                                                                        }
                                                                    endwhile;
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        } ?>
                                                        <tr>
                                                            <td colspan="2">
                                                                Entradas/Salidas registradas: <?php echo date("Y-m-d"); ?>:
                                                                <?php
                                                                if ($datos_invitado["SocioAusente"] == "S") {
                                                                    echo "<br>" . $mesanje_ausente;
                                                                }
                                                                //Consulto el historial de entradas y salidas del dia
                                                                $sql_log_acceso = "Select * From LogAccesoDiario Where IDInvitacion = '" . $id_registro . "' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '" . date("Y-m-d") . "' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '" . date("Y-m-d") . "') Order by IDLogAcceso Desc";
                                                                $result_log_acceso = $dbo->query($sql_log_acceso);

                                                                while ($row_log_acceso = $dbo->fetchArray($result_log_acceso)) :
                                                                    if ($row_log_acceso["Entrada"] == "S") :
                                                                        echo "<br>Entrada: " . substr($row_log_acceso["FechaIngreso"], 11) . " Mecanismo: $row_log_acceso[Mecanismo]";
                                                                    elseif ($row_log_acceso["Salida"] == "S") :
                                                                        echo "<br>Salida: " . substr($row_log_acceso["FechaSalida"], 11) . " Mecanismo: $row_log_acceso[Mecanismo]";
                                                                    endif;
                                                                endwhile;
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <?php
                                                    if ($nucleo_socio == "1") :
                                                        $modulo = "Socio";
                                                        $id_registro = $datos_grupo_familiar["IDSocio"];
                                                    else :
                                                        $modulo = "SocioInvitadoEspecial";
                                                        $id_registro = $datos_grupo_familiar["IDSocioInvitadoEspecial"];
                                                    endif;
                                                    ?>
                                                </td>
                                                <?php
                                                if ($contador_grupo == "3") :
                                                    echo "</tr><tr>";
                                                    $contador_grupo = "-1";
                                                endif;
                                                ?>
                                    <?php
                                                $contadorFamiliar++;
                                            endwhile;
                                        endif;
                                    endif;
                                    ?>
                                </tr>
                            </table>
                        </div>
                        <!-- PAGE CONTENT ENDS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?
elseif ($valida == "si") :
?>
    <span style='color: #F00; font-size:16px'>
        <?php
        echo $mensajevalida;
        ?>
    </span>
    <hr>
<?php
elseif ($_GET["IDTipoBusqueda"] == 2) : ?>
    <span style='color: #000; font-size:16px'>
        Resultados de busqueda por nombre:
    </span>
    <hr>
    <?php
    foreach ($autorizaciones as $autorizacion) {
    ?>
        <br><a href="/plataform/accesoinvitado.php?action=search&qryString=<?php echo $autorizacion['NumeroDocumento'] ?>"><?php echo "{$autorizacion['NumeroDocumento']} - {$autorizacion['Nombre']} {$autorizacion['Apellido']} - Contratista" ?></a>
    <?php
    }
    foreach ($invitados as $invitado) {
    ?>
        <br><a href="/plataform/accesoinvitado.php?action=search&qryString=<?php echo $invitado['NumeroDocumento'] ?>"><?php echo "{$invitado['NumeroDocumento']} - {$invitado['Nombre']} {$invitado['Apellido']} - Invitado General" ?></a>
    <?php
    }
    foreach ($invitadosEspecial as $invitadoEspecial) {
    ?>
        <br><a href="/plataform/accesoinvitado.php?action=search&qryString=<?php echo $invitadoEspecial['NumeroDocumento'] ?>"><?php echo "{$invitadoEspecial['NumeroDocumento']} - {$invitadoEspecial['Nombre']} {$invitadoEspecial['Apellido']} - Invitado" ?></a>
    <?php
    }
    foreach ($usuarios as $usuario) {
    ?>
        <br><a href="/plataform/accesoinvitado.php?action=search&qryString=<?php echo $usuario["NumeroDocumento"] ?>"><?php echo "{$socio['NumeroDocumento']} - {$usuario['Nombre']} - Funcionario" ?></a>
    <?php
    }
    foreach ($socios as $socio) {
    ?>
        <br><a href="/plataform/accesoinvitado.php?action=search&qryString=<?php echo $socio["NumeroDocumento"] ?>"><?php echo "{$socio['NumeroDocumento']} - {$socio['Nombre']} {$socio['Apellido']} - Socio - {$socio['Accion']}" ?></a>
    <?php
    }
    if (empty($autorizaciones) && empty($invitados) && empty($invitadosEspecial) && empty($usuarios) && empty($socios)) :
    ?>
        <span style='color: #F00; font-size:16px'>
            No se encontraron resultados
        </span>
    <?php
    endif;
elseif (count($predios) > 0 && SIMUser::get("club") != 44) :
    ?>
    <span style='color: #F00; font-size:16px'>
        Socio con predio:
    </span>
    <?php
    foreach ($predios as $predio) {
    ?>
        <br><a href="/plataform/accesoinvitado.php?qryString=<?php echo $predio["NumeroDocumento"] ?>&action=search"><?php echo "{$predio['Nombre']} {$predio['Apellido']} - {$predio['Accion']}" ?></a>
    <?php
    }
elseif (!empty(SIMNet::req("qryString"))) :
    if (count($array_proxima_autorizacion) > 0) :
        echo "<span style='color:#063; font-size:16px'; font-weight:bold><br>" . implode("<br>", $array_proxima_autorizacion) . '</span>';
    else : ?>
        <span style='color: #F00; font-size:16px'>
            No se encontraron resultados
        </span>
    <?php
    endif;
elseif (empty(SIMNet::req("qryString"))) :
    ?>
    <span style='color: #F00; font-size:16px'>
        El campo de busqueda no debe estar vacio.
    </span>
<?php

endif; ?>
<script>
    contadorFamiliar = <?php echo $contadorFamiliar ?>
</script>
<?
include("cmp/footer_grid.php");
?>

<script>
    $('.IngresarTodos').click(function() {
        $('.ingreso_accesov2').each(function() {
            $(this).trigger("click");
        });
    });
    $('.SalidaTodos').click(function() {
        $('.salida_accesov2').each(function() {
            $(this).trigger("click");
        });
    });
</script>