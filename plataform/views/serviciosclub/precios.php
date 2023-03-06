
<form class="form-horizontal formvalida" role="form" method="post" id="frm1<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
    <div  class="form-group first ">
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripción del Precio:  </label>
            <div class="col-sm-8">
                <input type="text" id="Nombre" name="Nombre" placeholder="Descripción del Precio" class="col-xs-12 mandatory " title="Descripción del Precio" value="<?php echo $frm["Nombre"]; ?>" >
            </div>
        </div>

        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Precio:  </label>
            <div class="col-sm-8">
                <input type="number" id="Valor" name="Valor" placeholder="Precio" class="col-xs-12 mandatory " title="Precio" value="<?php echo $frm["Valor"]; ?>" >
            </div>
        </div>
    </div>
 
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
            <input type="hidden" name="IDServicio"  id="ID" value="<?php echo $frm[ "IDServicio" ] ?>" />
            <input type="hidden" name="action" id="action" value="<?php if(empty($frm["Nombre"])) echo "insertarprecio"; else echo "actualizarprecio";  ?>"/>
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
            
            <button class="btn btn-info btnEnviar" type="button" rel="frm1<?php echo $script; ?>" >
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
            </button>
        </div>
    </div>
</form>
