<div class="widget-box transparent" id="recent-box">
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

                        <?php
                        $sql = "SELECT * FROM ContratosClub WHERE IDClub = ".SIMUser::get("club")." ORDER BY IDContratoClub DESC LIMIT 1";
                        $qry = $dbo->query($sql);
                        $datos = $dbo->fetchArray($qry);

                        if(empty($datos)):
                            ?>
                            <div class="form-group first ">
                                <div  class="col-xs-12 col-sm-6">
                                   
                                    <h1> No se han verificado los contratos.</h1>
                                    
                                </div>
                            </div>
                            <?php
                        else:
                            ?>                       
                       
                            <div class="form-group first ">
                                <div  class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Registrado</label>

                                    <div class="col-sm-8">
                                        <input  type="text" size="25" class="input mandatory" disabled value="<?php echo $datos[Nombre] ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div  class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Documento Registrado</label>

                                    <div class="col-sm-8">
                                        <input  type="text" size="25" class="input mandatory" disabled value="<?php echo $datos[NumeroDocumento] ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div  class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Celular Registrado</label>

                                    <div class="col-sm-8">
                                        <input  type="text" size="25" class="input mandatory" disabled value="<?php echo $datos[Celular] ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div  class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha del Registro</label>

                                    <div class="col-sm-8">
                                        <input  type="text" size="25" class="input mandatory" disabled value="<?php echo $datos[Fecha] ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">                            
                                <div class="col-sm-8">
                                    <a href="<?php echo CLUB_ROOT . $datos[Contrato]; ?>" target="_blank">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Ver Contrato licenciamiento</label>
                                    </a>
                                </div>                            
                            </div>
                            <div class="form-group first ">                           
                                <div class="col-sm-8">
                                    <a href="<?php echo CLUB_ROOT . $datos[Oferta]; ?>" target="_blank">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Ver Contrato de transmisión de datos</label>
                                    </a>
                                </div>                            
                            </div>

                            <?php
                        endif;
                        ?>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

