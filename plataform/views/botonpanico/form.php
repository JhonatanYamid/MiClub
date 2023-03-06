<style>
    #map {
        width: 100%;
        height: 580px;
        box-shadow: 5px 5px 5px #888;
    }
</style>

<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="form-group first ">
                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="Latitud"><?= SIMUtil::get_traduccion('', '', 'Latitud', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="Latitud" name="Latitud" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Latitud', LANGSESSION); ?>" value="<?php echo $frm["Latitud"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="Casa"><?= SIMUtil::get_traduccion('', '', 'Longitud', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="Longitud" name="Longitud" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Longitud', LANGSESSION); ?>" value="<?php echo $frm["Longitud"] ?>" required></div>


                                </div>

                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-4">
                                    <?php
                                    $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                                    $qry_socio_club = $dbo->query($sql_socio_club);
                                    $r_socio = $dbo->fetchArray($qry_socio_club);

                                    ?>
                                    <label class="col-sm-4 control-label" for="Nombre"><?= SIMUtil::get_traduccion('', '', 'NombreSocio', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="Accion" name="Accion" class="col-xs-12 mandatory autocomplete-ajax" title="<?= SIMUtil::get_traduccion('', '', 'NombreSocio', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Nombre"] . " " . $r_socio["Apellido"]  ?>"></div>

                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-sm-4 control-label" for="Predio"><?= SIMUtil::get_traduccion('', '', 'Predio', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="Predio" name="Predio" class="col-xs-12 mandatory autocomplete-ajax" title="<?= SIMUtil::get_traduccion('', '', 'Predio', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Predio"];  ?>"></div>
                                </div>
                            </div>


                            <div class="form-group first">
                                <div id='map'>
                                </div>

                            </div>


                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
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
    //traigo la latitud y longitud
    let latitud = document.getElementById("Latitud").value;
    let longitud = document.getElementById("Longitud").value;

    //se crea el mapa
    var map = L.map('map').
    setView([latitud, longitud],
        15);

    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
        maxZoom: 18
    }).addTo(map);

    L.control.scale().addTo(map);
    L.marker([latitud, longitud], {
        draggable: true
    }).addTo(map);
</script>

<?
include("cmp/footer_scripts.php");
?>