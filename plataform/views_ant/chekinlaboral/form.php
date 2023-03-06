<style>
    #map,
    #map2 {
        width: 100%;
        height: 580px;
        box-shadow: 5px 5px 5px #888;
    }
</style>
<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="form-group first ">
                                <?php
                                if ($frm["IDSocio"] > 0) {
                                    $idsocio = $frm["IDSocio"];
                                } else if ($frm["IDUsuario"] > 0) {
                                    $idusuario = $frm["IDUsuario"];
                                }
                                ?>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Checkin Para quien ? </label>
                                    <div class="col-md-4">
                                        <label class="control-label"><input <?php echo $idsocio != "" ? 'checked' : ""; ?> type="radio" id="personaCheckinSocio" name="personaCheckin" value="1" <?php if ($_GET["action"] != "add") echo "disabled"; ?>> Socio </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label"><input <?php echo $idusuario > 0 ? 'checked' : ""; ?> type="radio" id="personaCheckinUsuario" name="personaCheckin" value="2" <?php if ($_GET["action"] != "add") echo "disabled"; ?>> Usuario </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6" id="mostrarSocio" style="<?php if ($frm["IDSocio"] >  0) echo "";
                                                                                            else echo "display:none"; ?>">


                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>
                                    <div class="col-sm-8">

                                        <?php
                                        $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                                        $qry_socio_club = $dbo->query($sql_socio_club);
                                        $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                                        <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12  autocomplete-ajax" title="número de derecho" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
                                        <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="" title="Socio">


                                    </div>



                                </div>


                                <div class="form-group col-md-6" id="mostrarUsuario" style="<?php if ($frm["IDUsuario"] > 0) echo "";
                                                                                            else echo "display:none"; ?>">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario </label>


                                    <div class="col-sm-8">

                                        <?php
                                        $sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
                                        $qry_socio_club = $dbo->query($sql_socio_club);
                                        $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                                        <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12  autocomplete-ajax-funcionario" title="número de derecho" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
                                        <input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" class="" title="Usuario">


                                    </div>
                                </div>


                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Latitud">Latitud entrada</label>
                                    <div class="col-sm-8"><input type="text" id="LatitudEntrada" name="LatitudEntrada" placeholder="Latitud Entrada" class="form-control" title="Latitud Entrada" value="<?php echo $frm["LatitudEntrada"] ?>" required <?php if ($_GET["action"] != "add") echo "readonly"; ?>></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Casa">Longitud entrada</label>
                                    <div class="col-sm-8"> <input type="text" id="LongitudEntrada" name="LongitudEntrada" placeholder="Longitud entrada" class="form-control" title="Longitud Entrada" value="<?php echo $frm["LongitudEntrada"] ?>" required <?php if ($_GET["action"] != "add") echo "readonly"; ?>></div>


                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Latitud">Latitud salida</label>
                                    <div class="col-sm-8"><input type="text" id="LatitudSalida" name="LatitudSalida" placeholder="Latitud Salida" class="form-control" title="Latitud Salida" value="<?php echo $frm["LatitudSalida"] ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?>></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Casa">Longitud salida</label>
                                    <div class="col-sm-8"> <input type="text" id="LongitudSalida" name="LongitudSalida" placeholder="Longitud Salida" class="form-control" title="Longitud Salida" value="<?php echo $frm["LongitudSalida"] ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?>></div>


                                </div>

                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Entrada </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Entrada"], 'Entrada', "class='input mandatory'") ?>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Salida </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Salida"], 'Salida', "class='input '") ?>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="FechaMovimiento">Fecha Movimiento Entrada</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $fechaMovimientoEntrada = explode(" ", $frm["FechaMovimientoEntrada"]);
                                        ?>
                                        <input type="text" id="FechaEntrada" name="FechaEntrada" placeholder="Fecha Movimiento Entrada" class="calendar" title="Fecha Movimiento Entrada" value="<?php echo $fechaMovimientoEntrada[0]; ?>" required>
                                        <input type="time" name="HoraEntrada" id="HoraEntrada" value="<?php echo $fechaMovimientoEntrada[1]; ?>" required>
                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="FechaMovimiento">Fecha Movimiento Salida</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $fechaMovimientoSalida = explode(" ", $frm["FechaMovimientoSalida"]);
                                        ?>
                                        <input type="text" id="FechaSalida" name="FechaSalida" placeholder="Fecha Movimiento Salida" class="calendar" title="FechaMovimientoSalida" value="<?php echo $fechaMovimientoSalida[0]; ?>">
                                        <input type="time" name="HoraSalida" id="HoraSalida" value="<?php echo $fechaMovimientoSalida[1]; ?>">
                                    </div>

                                </div>
                            </div>



                            <?php if ($_GET["action"] <> "add") { ?>

                                <div class="form-group first">
                                    <label class="col-sm-4 control-label" for="Casa">
                                        <h3><b>Ubicacion Entrada</b></h3>
                                    </label>
                                    <div id='map'>
                                    </div>

                                </div>

                                <div class="form-group first">
                                    <label class="col-sm-4 control-label" for="Casa">
                                        <h3><b>Ubicacion Salida</b></h3>
                                    </label>
                                    <div id='map2'>
                                    </div>

                                </div>
                            <?php } ?>





                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                        </button>
                                        <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<script>
    //traigo la latitud y longitud de entrada
    let latitudEntrada = document.getElementById("LatitudEntrada").value;
    let longitudEntrada = document.getElementById("LongitudEntrada").value;

    //traigo la latitud y longitud de salida
    let latitudSalida = document.getElementById("LatitudSalida").value;
    let longitudSalida = document.getElementById("LongitudSalida").value;

    //se crea el mapa de ubicacion de entrada
    var map = L.map('map').
    setView([latitudEntrada, longitudEntrada],
        15);

    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
        maxZoom: 18
    }).addTo(map);

    L.control.scale().addTo(map);
    L.marker([latitudEntrada, longitudEntrada], {
        draggable: true
    }).addTo(map);

    //se crea el mapa de ubicacion de salida
    var map = L.map('map2').
    setView([latitudSalida, longitudSalida],
        15);

    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
        maxZoom: 18
    }).addTo(map);

    L.control.scale().addTo(map);
    L.marker([latitudSalida, longitudSalida], {
        draggable: true
    }).addTo(map);
</script>

<?
include("cmp/footer_scripts.php");
?>