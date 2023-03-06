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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <select name="Categoria" id="Categoria" class="form-control">
                                        <option value=""></option>
                                        <?php

                                        $sql_categoria = "Select * From CategoriaEncuestaArbol WHERE Publicar = 'S' Order by Nombre";
                                        $result_categoria = $dbo->query($sql_categoria);
                                        while ($row_categoria = $dbo->fetchArray($result_categoria)) : ?>

                                            <option value="<?php echo $row_categoria["IDCategoriaEncuestaArbol"] ?>" <?php if ($frm_get["Categoria"] == $row_categoria["IDCategoriaEncuestaArbol"]) echo "selected";  ?>><?php echo  $row_categoria["Nombre"] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
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
                            <input type="hidden" name="tab" id="tab" value="historico">
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div><!-- /.widget-main -->
</div><!-- /.widget-body -->
<!-- INICIO TAB DASHBOARD -->
<div class="col-10">
    <div id="jqGrid_container2">
        <!-- <a href="procedures/excel-producto.php?IDClub=<?php echo SIMUser::get("club") . $otos_parametros; ?>"><img src="assets/img/xls.gif"><?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?></a> -->
        <table id="grid-table2"></table>
    </div>
    <div id="grid-pager2"></div>
    <script type="text/javascript">
        var $path_base = "."; //in Ace demo this will be used for editurl parameter
    </script>

    <!-- PAGE CONTENT ENDS -->
</div>
<!-- FIN TAB DASHBOARD-->