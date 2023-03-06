<div class="widget-body">
    <div class="widget-main padding-4">
        <div class="row">
            <div class="col-12">
                <!-- PAGE CONTENT BEGINS -->
                <form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-3">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'fechainicio', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechainicio', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'fechainicio', LANGSESSION); ?>" value="<?php echo $frm_get["FechaInicio"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-3">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'fechafin', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechafin', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'fechafin', LANGSESSION); ?>" value="<?php echo $frm_get["FechaFin"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-3">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
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
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div><!-- /.widget-main -->
</div><!-- /.widget-body -->
<!-- INICIO TAB DASHBOARD -->
<div class="col-10">
    <div id="jqGrid_container">
        <!-- <a href="procedures/excel-producto.php?IDClub=<?php echo SIMUser::get("club") . $otos_parametros; ?>"><img src="assets/img/xls.gif"><?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?></a> -->
        <table id="grid-table"></table>
    </div>
    <div id="grid-pager"></div>
    <script type="text/javascript">
        var $path_base = "."; //in Ace demo this will be used for editurl parameter
    </script>

    <!-- PAGE CONTENT ENDS -->
</div>
<!-- FIN TAB DASHBOARD-->