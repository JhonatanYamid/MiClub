<form class="form-horizontal formvalida" role="form" method="post" id="InsertGestionCartera<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>
            <div class="col-sm-8">
                <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>
            <div class="col-sm-8">
                <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d") ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descargar por Socio </label>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> ¿Exportar solo este socio? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activa"], 'Activa', "class='input'") ?>
            </div>
        </div>
    </div>






    <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
    <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $_GET['id'] ?>" />
    <input type="hidden" name="action" id="action" value="<?php echo 'Exportar' ?>" />
    <button class="btn btn-info btnEnviar" type="submit" rel="frm<?php echo $script; ?>">
        <i class="ace-icon fa fa-cloud-download bigger-110"></i> Exportar
    </button>
</form>