<form class="form-horizontal formvalida" role="form" method="post" id="frmregistrarCaddie" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-users green"></i>
            Registro de caddies
        </h3>
    </div>

    <div  class="form-group first ">
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Número de Documento </label>

            <div class="col-sm-8">
                <input type="text" id="numeroDocumentoRegistrar" name="numeroDocumentoRegistrar" placeholder="Número de Documento" class="col-xs-12 mandatory" title="número de documento" value="" >
            </div>
        </div>
    </div>

    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
            </button>	
        </div>
    </div>
</form>
