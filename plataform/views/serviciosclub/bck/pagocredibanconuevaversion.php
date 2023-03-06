<form class="form-horizontal formvalida" role="form" method="post" name="frmcredibanconuevaversion" id="frmcredibanconuevaversion" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

<?php
    $EditPagoCredibanco = $dbo->fetchAll("CredibancoNuevaVersionServicio", " IDServicio = '$frm[$key]' ", "array");
?>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario API </label>
            <div class="col-sm-8">
                <input type="text" id="UsuarioApiCredibanco" name="UsuarioApiCredibanco" placeholder="Usuario Api" class="col-xs-12" title="UsuarioApiCredibanco" value="<?php echo $EditPagoCredibanco["UsuarioApiCredibanco"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Contraseña API </label>
            <div class="col-sm-8">
                <input type="password" id="PassApiCredibanco" name="PassApiCredibanco" placeholder="Contaseña Api" class="col-xs-12" title="PassApiCredibanco" value="<?php echo $EditPagoCredibanco["PassApiCredibanco"]; ?>">
            </div>
        </div>
    </div>
   
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="credibanconuevaversion" />
            <button class="btn btn-info btnEnviar" type="button" rel="frmcredibanconuevaversion">
                <i class="ace-icon fa fa-check bigger-110"></i> Actuliza datos Pago </button>
        </div>
    </div>
</form>