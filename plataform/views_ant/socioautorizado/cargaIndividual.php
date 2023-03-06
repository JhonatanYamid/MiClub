<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
    <div  class="form-group first ">
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento:  </label>
            <div class="col-sm-8">
                <input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="NumeroDocumento" class="col-xs-12 mandatory" title="NumeroDocumento" value="<?php echo $frm["NumeroDocumento"]; ?>" >
            </div>
        </div>
        <?php if(SIMReg::get("club") == 8 || SIMReg::get("club") == 12) {?>
            <div  class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Handicap:  </label>
                <div class="col-sm-8">
                    <input type="number" id="Handicap" name="Handicap" placeholder="Handicap" class="col-xs-12 mandatory" title="Handicap" value="<?php echo $frm["Handicap"]; ?>" >
                </div>
            </div>
        <?php } ?>								
    </div>                          
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
            
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
            </button>
        </div>
    </div>
</form>