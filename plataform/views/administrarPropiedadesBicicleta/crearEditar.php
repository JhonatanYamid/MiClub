<form class="form-horizontal formvalida" role="form" method="post" id="frmadministrarPropiedadesBicicleta" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-users green"></i>
            Datos Basicos
        </h3>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

            <div class="col-sm-8">
                <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo utf8_encode($frm["Nombre"]); ?>">
            </div>
        </div>
    </div>





    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"]))
                                                                        echo SIMUser::get("club");
                                                                    else
                                                                        echo $frm["IDClub"];
                                                                    ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
            </button>
        </div>
    </div>
</form>