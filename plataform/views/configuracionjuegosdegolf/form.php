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
                                    <label class="col-sm-4 control-label" for="IconoJugar">Icono Jugar</label>
                                    <div class="col-sm-8"><input type="text" id="IconoJugar" name="IconoJugar" placeholder="IconoJugar" class="form-control" title="IconoJugar" value="<?php echo $frm["IconoJugar"] ?>" required></div>

                                </div>

                                <div class=" form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="OcultaJugar">Ocultar Jugar</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["OcultaJugar"], "OcultaJugar", "", "");
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="TextoJugar">Texto Jugar</label>
                                    <div class="col-sm-8"><input type="text" id="TextoJugar" name="TextoJugar" placeholder="TextoJugar" class="form-control" title="TextoJugar" value="<?php echo $frm["TextoJugar"] ?>" required></div>

                                </div>


                                <label class="col-sm-4 control-label" for="OcultarHandicap">OcultarHandicap</label>
                                <div class="col-sm-8">
                                    <?php
                                    echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["OcultarHandicap"], "OcultarHandicap", "", "");
                                    ?>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="IconoHandicap">IconoHandicap</label>
                                <div class="col-sm-8"><input type="text" id="IconoHandicap" name="IconoHandicap" placeholder="IconoHandicap" class="form-control" title="IconoHandicap" value="<?php echo $frm["IconoHandicap"] ?>" required></div>

                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label" for="TextoHandicap">TextoHandicap</label>
                                <div class="col-sm-8"><input type="text" id="TextoHandicap" name="TextoHandicap" placeholder="TextoHandicap" class="form-control" title="TextoHandicap" value="<?php echo $frm["TextoHandicap"] ?>" required></div>

                            </div>


                            <label class="col-sm-4 control-label" for="OcultarGrupos">OcultarGrupos</label>
                            <div class="col-sm-8">
                                <?php
                                echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["OcultarGrupos"], "OcultarGrupos", "", "");
                                ?>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label" for="IconoGrupos">IconoGrupos</label>
                            <div class="col-sm-8"><input type="text" id="IconoGrupos" name="IconoGrupos" placeholder="IconoGrupos" class="form-control" title="IconoGrupos" value="<?php echo $frm["IconoGrupos"] ?>" required></div>

                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label" for="TextoGrupos">TextoGrupos</label>
                            <div class="col-sm-8"><input type="text" id="TextoGrupos" name="TextoGrupos" placeholder="TextoGrupos" class="form-control" title="TextoGrupos" value="<?php echo $frm["TextoGrupos"] ?>" required></div>

                        </div>








                        <label class="col-sm-4 control-label" for="OcultarJuegos">OcultarJuegos</label>
                        <div class="col-sm-8">
                            <?php
                            echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["OcultarJuegos"], "OcultarJuegos", "", "");
                            ?>
                        </div>
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label" for="IconoJuegos">IconoJuegos</label>
                    <div class="col-sm-8"><input type="text" id="IconoJuegos" name="IconoJuegos" placeholder="IconoJuegos" class="form-control" title="IconoJuegos" value="<?php echo $frm["IconoJuegos"] ?>" required></div>

                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label" for="TextoJuegos">TextoJuegos</label>
                    <div class="col-sm-8"><input type="text" id="TextoJuegos" name="TextoJuegos" placeholder="TextoJuegos" class="form-control" title="TextoJuegos" value="<?php echo $frm["TextoJuegos"] ?>" required></div>

                </div>


                <label class="col-sm-4 control-label" for="OcultarTelefono">OcultarTelefono</label>
                <div class="col-sm-8">
                    <?php
                    echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["OcultarTelefono"], "OcultarTelefono", "", "");
                    ?>
                </div>
            </div>
            <label class="col-sm-4 control-label" for="OcultarEmail">OcultarEmail</label>
            <div class="col-sm-8">
                <?php
                echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["OcultarEmail"], "OcultarEmail", "", "");
                ?>
            </div>
        </div>
        <label class="col-sm-4 control-label" for="OcultarIdentificacion">OcultarIdentificacion</label>
        <div class="col-sm-8">
            <?php
            echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["OcultarIdentificacion"], "OcultarIdentificacion", "", "");
            ?>
        </div>
    </div><br> <br> <br>







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