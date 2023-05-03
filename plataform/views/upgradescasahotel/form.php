<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="form-group first ">

                               
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="NombreMejora">Nombre Mejora</label>
                                    <div class="col-sm-8"><input type="text" id="NombreMejora" name="NombreMejora" placeholder="Nombre Mejora" class="form-control" title="NombreMejora" value="<?php echo $frm["NombreMejora"] ?>" required></div>

                                </div>

                               
                            </div>


                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDUsuario" id="action" value="<?php if (empty($frm["IDUsuario"])) echo SIMUser::get("IDUsuario");
                                                                                                    else echo $frm["IDUsuario"];  ?>" />
                                        <input type="hidden" name="IDSocio" id="action" value="<?php if (empty($frm["IDSocio"])) echo SIMUser::get("IDSocio");
                                                                                                else echo $frm["IDSocio"];  ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>