 <style>
    #map {
        width: 100%;
        height: 580px;
        box-shadow: 5px 5px 5px #888;
    }
</style>

<?php
    if($frm['IDUsuario'])
        $nmUsuario = $dbo->getFields("Usuario", "Nombre", "IDUsuario = ".$frm['IDUsuario']);
?>

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
                                <div class="col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>: </label>
                                    <div class="col-sm-8 limpiar">
                                        <input type="text" id="Buscar" name="Buscar" placeholder="<?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> class="col-xs-12 autocomplete-ajax-funcionario-laboralUsuario" value="<?= $nmUsuario ?>" />
                                        <input type="hidden" name="IDUsuario" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?>"  id="IDUsuario" value="<?php echo $frm['IDUsuario'] ?>" />
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="FechaMovimiento"><?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>:</label>
                                    <div class="col-sm-8"><input type="date" id="Fecha" name="Fecha" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" value="<?php echo $frm["FechaEntrada"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Entrada', LANGSESSION); ?>:</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm['Entrada'], 'Entrada', "class='input mandatory'") ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="FechaMovimiento"><?= SIMUtil::get_traduccion('', '', 'Fechaentrada', LANGSESSION); ?>:</label>
                                    <div class="col-sm-8"><input type="datetime-local" id="FechaEntrada" name="FechaEntrada" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechaentrada', LANGSESSION); ?>:" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Fechaentrada', LANGSESSION); ?>" value="<?php echo $frm["FechaEntrada"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Salida', LANGSESSION); ?>:</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm['Salida'], 'Salida', "class='input mandatory'") ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="FechaMovimiento"><?= SIMUtil::get_traduccion('', '', 'Fechasalida', LANGSESSION); ?>:</label>
                                    <div class="col-sm-8"><input type="datetime-local" id="FechaSalida" name="FechaSalida" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechasalida', LANGSESSION); ?>:" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Fechasalida', LANGSESSION); ?>:" value="<?php echo $frm["FechaSalida"] ?>" required></div>
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