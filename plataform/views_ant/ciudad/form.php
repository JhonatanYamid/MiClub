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
                                    <label class="col-sm-4 control-label" for="Nombre">Nombre</label>
                                    <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="" class="form-control" title="Nombre" value="<?php echo $frm["Nombre"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Indicativo">Indicativo</label>
                                    <div class="col-sm-8"><input type="number" id="Indicativo" name="Indicativo" placeholder="" class="form-control" title="Indicativo" value="<?php echo $frm["Indicativo"] ?>" required></div>


                                </div>

                            </div>

                            <div class="form-group first">


                                <div class="form-group col-md-6">

                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pais </label>

                                    <div class="col-sm-8">
                                        <?php echo SIMHTML::formPopUp("Pais", "Nombre", "Nombre", "IDPais", $frm["IDPais"], "[Seleccione Pais]", "form-control", "title = \"Pais\"") ?>
                                    </div>

                                </div>

                                <div class="form-group col-md-6">

                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Departamento </label>

                                    <div class="col-sm-8">
                                        <?php echo SIMHTML::formPopUp("Departamento", "Nombre", "Nombre", "IDDepartamento", $frm["IDDepartamento"], "[Seleccione Departamento]", "form-control", "title = \"Departamento\"", " AND Publicar='S'") ?>
                                    </div>

                                </div>



                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Publicar">Publicar</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input'") ?>
                                    </div>

                                </div>


                            </div>





                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                        </button>
                                        <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
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