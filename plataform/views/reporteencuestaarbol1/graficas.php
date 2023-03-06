<div class="widget-body">
    <div class="widget-main padding-4">
        <div class="row">
            <div class="col-12">
                <!-- PAGE CONTENT BEGINS -->
                <form class="form-horizontal formvalida" id="frmfrmBuscar2" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-3">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'fecha', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaHistorico" name="FechaHistorico" placeholder="<?= SIMUtil::get_traduccion('', '', 'fecha', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'fecha', LANGSESSION); ?>" value="<?php echo $frm_get["FechaHistorico"]; ?>">
                                </div>
                            </div>
                            <!--<div class="col-xs-12 col-sm-3">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'fechafin', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechafin', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'fechafin', LANGSESSION); ?>" value="<?php echo $frm_get["FechaFin"]; ?>">
                                </div>
                            </div> -->

                            <div class="col-xs-12 col-sm-3">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar2">
                                        <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                        <?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION) ?> <?php echo SIMReg::get("title") ?>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group first ">
                            <input type="hidden" name="oper" id="oper" value="searchurl">
                            <input type="hidden" name="action" id="action" value="search">
                            <input type="hidden" name="tab" id="tab" value="graficas">
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div><!-- /.widget-main -->
</div><!-- /.widget-body -->
<!-- INICIO TAB DASHBOARD -->
<div class="col-10">
    <div class="">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-pie-chart green"></i>
            Graficas
        </h3>
    </div>
    <div class="col-md-12">
        <?php
        foreach ($arr_graficas as $Pregunta => $valores) {
        ?>
            <div class="col-md-4">
                <div id="container">
                    <div id="<?= $Pregunta ?>" style="width: 500px; height: 500px;"></div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load("current", {
            packages: ["corechart"]
        });
        google.charts.load("current", {
            packages: ['bar']
        });
        <?php
        ksort($arr_graficas);
        foreach ($arr_graficas as $Pregunta => $valores) {
            $PreguntaEncuesta = $dbo->getFields('PreguntaEncuestaArbol', 'EtiquetaCampo', "IDPreguntaEncuestaArbol=$Pregunta");
        ?>

            google.charts.setOnLoadCallback(chart<?= $Pregunta ?>);
            // google.charts.setOnLoadCallback(drawChartTipoSocio);

            <?php
            $data_chart = '';
            foreach ($valores as $opcion => $valor) {
                $data_chart .= "['" . $opcion . "'," . $valor . "],";
            }
            ?>

            // function drawChartTipoSocio() {
            function chart<?= $Pregunta ?>() {
                var data = google.visualization.arrayToDataTable([
                    ['Task', 'Tipo socio'],
                    <?php echo $data_chart; ?>
                ]);
                var options = {
                    width: 700,
                    height: 500,
                    title: '<?= $PreguntaEncuesta ?>',
                    pieHole: 0.4,
                };
                var chart = new google.visualization.PieChart(document.getElementById('<?= $Pregunta ?>'));
                chart.draw(data, options);
            }

        <?php
        }
        ?>
    </script>