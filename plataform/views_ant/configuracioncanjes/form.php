<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Paisdelclub', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <?php echo SIMHTML::formPopUp("Pais", "Nombre", "Nombre", "IDPais", $frm["IDPais"], "[Seleccione Pais]", "form-control", "title = \"Pais\"") ?>
                                    </div>
                                </div>


                            </div>

                            <div class="form-group first">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'paisesconveniocanje', LANGSESSION); ?>: </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="Paises" name="Paises" placeholder="<?= SIMUtil::get_traduccion('', '', 'paisesconveniocanje', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-pais" title="Paises ">
                                        <br>
                                        <a id="agregar_pais" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_pais" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
                                        <br>
                                        <select name="PaisesConveniosCanjes[]" id="PaisesConveniosCanjes" class="col-xs-8" multiple>
                                            <?php
                                            $item = 1;
                                            $array_paises = explode("|||", $frm["PaisesConvenios"]);


                                            foreach ($array_paises as $id_pais => $datos_paises) :
                                                if (!empty($datos_paises)) {
                                                    $array_datos_paises = explode("-", $datos_paises);
                                                    $item--;
                                                    $IDPais = $array_datos_paises[0];

                                                    if ($IDPais > 0) :
                                                        $nombre_pais = utf8_encode($dbo->getFields("Pais", "Nombre", "IDPais = '" . $IDPais . "'"));
                                                        echo $nombre_pais;
                                            ?>
                                                        <option value="<?php echo  $IDPais . "-" . $nombre_pais; ?>"><?php echo $nombre_pais; ?></option>
                                            <?php
                                                    endif;
                                                }
                                            endforeach; ?>
                                        </select>
                                        <input type="hidden" name="PaisesConvenios" id="PaisesConvenios" value="">
                                    </div>
                                </div>


                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Ciudadesconveniocanje', LANGSESSION); ?>: </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="Ciudades" name="Ciudades" placeholder="<?= SIMUtil::get_traduccion('', '', 'Ciudadesconveniocanje', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-ciudad" title="Ciudades ">
                                        <br>
                                        <a id="agregar_ciudad" href="#"><?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION); ?></a> | <a id="borrar_ciudad" href="#"><?= SIMUtil::get_traduccion('', '', 'Borrar', LANGSESSION); ?></a>
                                        <br>
                                        <select name="CiudadesConveniosCanjes[]" id="CiudadesConveniosCanjes" class="col-xs-8" multiple>
                                            <?php
                                            $item = 1;
                                            $array_ciudades = explode("|||", $frm["CiudadesConvenios"]);


                                            foreach ($array_ciudades as $id_ciudad => $datos_ciudades) :
                                                if (!empty($datos_ciudades)) {
                                                    $array_datos_ciudades = explode("-", $datos_ciudades);
                                                    $item--;
                                                    $IDCiudad = $array_datos_ciudades[0];

                                                    if ($IDCiudad > 0) :
                                                        $nombre_ciudad = utf8_encode($dbo->getFields("Ciudad", "Nombre", "IDCiudad = '" . $IDCiudad . "'"));
                                                        echo $nombre_ciudad;
                                            ?>
                                                        <option value="<?php echo  $IDCiudad . "-" . $nombre_ciudad; ?>"><?php echo $nombre_ciudad; ?></option>
                                            <?php
                                                    endif;
                                                }
                                            endforeach; ?>
                                        </select>
                                        <input type="hidden" name="CiudadesConvenios" id="CiudadesConvenios" value="">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelBotonPais"><?= SIMUtil::get_traduccion('', '', 'Textobotonpais', LANGSESSION); ?></label>
                                    <div class="col-sm-8"><input type="text" id="LabelBotonPais" name="LabelBotonPais" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textobotonpais', LANGSESSION); ?>" value="<?php echo $frm["LabelBotonPais"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="LabelBotonCiudad"><?= SIMUtil::get_traduccion('', '', 'Textobotonciudad', LANGSESSION); ?></label>
                                    <div class="col-sm-8"> <input type="text" id="LabelBotonCiudad" name="LabelBotonCiudad" placeholder="" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'Textobotonciudad', LANGSESSION); ?>" value="<?php echo $frm["LabelBotonCiudad"] ?>" required></div>

                                </div>



                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="PermitePais"><?= SIMUtil::get_traduccion('', '', 'Permitepais', LANGSESSION); ?></label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermitePais"], 'PermitePais', "class='input'") ?>
                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="PermiteCiudad"><?= SIMUtil::get_traduccion('', '', 'Permiteciudad', LANGSESSION); ?></label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteCiudad"], 'PermiteCiudad', "class='input'") ?>
                                    </div>

                                </div>

                            </div>



                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="PermiteCanjeActivo"><?= SIMUtil::get_traduccion('', '', 'Permitequeloscanjesquedenactivosalmomentodellegarlasolicitud', LANGSESSION); ?>?</label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteCanjeActivo"], 'PermiteCanjeActivo', "class='input'") ?>
                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Activo"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activo"], 'Activo', "class='input'") ?>
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
                                            <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
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