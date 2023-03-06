<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
    <div  class="form-group first ">
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento:  </label>
            <div class="col-sm-8">
                <input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="Numero Documento" class="col-xs-12 mandatory autocomplete-ajax" title="Numero Documento" value="<?php echo $frm["NumeroDocumento"]; ?>" >
                <input type="hidden" name="IDSocio" value="" id="IDSocio" class="mandatory" title="Socio">
                <input type="hidden" name="NumeroDoc" value="" id="NumeroDoc" class="mandatory" title="Socio">
            </div>
        </div>
        <?php if(/* SIMReg::get("club") == 8 || */ (SIMReg::get("club") == 12  && $_GET["ids"] == 144 )) {?>
            <div  class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Handicap:  </label>
                <div class="col-sm-8">
                    <input type="number" id="Handicap" name="Handicap" placeholder="Handicap" class="col-xs-12 mandatory" title="Handicap" value="<?php echo $frm["Handicap"]; ?>" >
                </div>
            </div>
        <?php } ?>
       
            <div  class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Tipo Reserva:  </label>
                <div class="col-sm-8">
                    <select name="IDTipoReserva" id="IDtipoReserva" class="habilita_elemento">
                        <option value="">Seleccione</option>
                            <?php
                                $sql_tipo_reserva = "Select * From ServicioTipoReserva Where IDServicio = '".$_GET["ids"]."'";
                                $r_tipo_reserva = $dbo->query($sql_tipo_reserva);

                                while($row_tipo_reserva = $dbo->fetchArray($r_tipo_reserva)):                                    
                                    ?>
                                        <option value="<?php echo $row_tipo_reserva["IDServicioTipoReserva"]; ?>"><?php echo $row_tipo_reserva["Nombre"]; ?></option>
                                    <?php 
                                endwhile; 
                            ?> 
                    </select>     
                </div>
            </div>
        									
    </div>                          
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
            <input type="hidden" name="action" id="action" value="cargaindividual"/>
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
            
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
            </button>
        </div>
    </div>
</form>
