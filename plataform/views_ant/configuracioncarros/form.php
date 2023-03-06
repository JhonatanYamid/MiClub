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
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Crear Disponibilidad</label>
                                    <div class="col-sm-8"><input type="text" id="LabelCrearDisponibilidad" name="LabelCrearDisponibilidad" placeholder="" class="form-control mandatory" title="Texto Crear Disponibilidad" value="<?php echo $frm["LabelCrearDisponibilidad"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Escribir Direccion</label>
                                    <div class="col-sm-8"><input type="text" id="LabelEscribirDireccion" name="LabelEscribirDireccion" placeholder="" class="form-control mandatory" title="Texto Escribir Direccion" value="<?php echo $frm["LabelEscribirDireccion"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Calificacion</label>
                                    <div class="col-sm-8"><input type="text" id="TextoCalificacion" name="TextoCalificacion" placeholder="" class="form-control mandatory" title="Texto Calificacion" value="<?php echo $frm["TextoCalificacion"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Mis Viajes</label>
                                    <div class="col-sm-8"><input type="text" id="LabelMisViajes" name="LabelMisViajes" placeholder="" class="form-control mandatory" title="Texto Mis Viajes" value="<?php echo $frm["LabelMisViajes"] ?>" required></div>

                                </div>


                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Desde Club</label>
                                    <div class="col-sm-8"><input type="text" id="LabelDesdeClub" name="LabelDesdeClub" placeholder="" class="form-control mandatory" title="Texto Desde Club" value="<?php echo $frm["LabelDesdeClub"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Hacia Club</label>
                                    <div class="col-sm-8"><input type="text" id="LabelHaciaClub" name="LabelHaciaClub" placeholder="" class="form-control mandatory" title="Texto Hacia Club	" value="<?php echo $frm["LabelHaciaClub"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto LLamar Al Conductor</label>
                                    <div class="col-sm-8"><input type="text" id="LabelLLamarConductor" name="LabelLLamarConductor" placeholder="" class="form-control mandatory" title="Texto LLamar Al Conductor" value="<?php echo $frm["LabelLLamarConductor"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Telefono</label>
                                    <div class="col-sm-8"><input type="text" id="LabelTelefono" name="LabelTelefono" placeholder="" class="form-control mandatory" title="Texto Telefono" value="<?php echo $frm["LabelTelefono"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Introduccion Mapa</label>
                                    <div class="col-sm-8"><input type="text" id="LabelIntroduccionMapa" name="LabelIntroduccionMapa" placeholder="" class="form-control mandatory" title="Texto Introduccion Mapa" value="<?php echo $frm["LabelIntroduccionMapa"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Descripcion</label>
                                    <div class="col-sm-8"><input type="text" id="LabelDescripcion" name="LabelDescripcion" placeholder="Recorrido" class="form-control mandatory" title="Texto Descripcion" value="<?php echo $frm["LabelDescripcion"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Mis Publicaciones</label>
                                    <div class="col-sm-8"><input type="text" id="LabelMisPublicaciones" name="LabelMisPublicaciones" placeholder="" class="form-control mandatory" title="Texto Mis Publicaciones" value="<?php echo $frm["LabelMisPublicaciones"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Mis Solicitudes</label>
                                    <div class="col-sm-8"><input type="text" id="LabelMisSolicitudes" name="LabelMisSolicitudes" placeholder="" class="form-control mandatory" title="Texto Mis Solicitudes" value="<?php echo $frm["LabelMisSolicitudes"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Reutilizar Ruta</label>
                                    <div class="col-sm-8"><input type="text" id="LabelReusarRuta" name="LabelReusarRuta" placeholder="" class="form-control mandatory" title="Texto Reutilizar Ruta" value="<?php echo $frm["LabelReusarRuta"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Cancelar Ruta</label>
                                    <div class="col-sm-8"><input type="text" id="LabelCancelarRuta" name="LabelCancelarRuta" placeholder="" class="form-control mandatory" title="Texto Cancelar Ruta" value="<?php echo $frm["LabelCancelarRuta"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Cancelar Solicitud</label>
                                    <div class="col-sm-8"><input type="text" id="LabelCancelarSolicitud" name="LabelCancelarSolicitud" placeholder="" class="form-control mandatory" title="Texto Cancelar Solicitud" value="<?php echo $frm["LabelCancelarSolicitud"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Latitud</label>
                                    <div class="col-sm-8"><input type="text" id="Latitud" name="Latitud" placeholder="" class="form-control mandatory" title="Latitud" value="<?php echo $frm["Latitud"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Longitud</label>
                                    <div class="col-sm-8"><input type="text" id="Longitud" name="Longitud" placeholder="" class="form-control mandatory" title="Longitud" value="<?php echo $frm["Longitud"] ?>" required></div>

                                </div>


                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Mis Viajes </label>
                                    <input name="IconoMisViajes" id=file class="" title="IconoMisViajes" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoMisViajes"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoMisViajes"] . "' height'300px' width='300px'>";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoMisViajes]&campo=IconoMisViajes&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Desde Club </label>
                                    <input name="IconoDesdeClub" id=file class="" title="IconoDesdeClub" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoDesdeClub"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoDesdeClub"] . "'height'300px' width='300px' >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoDesdeClub]&campo=IconoDesdeClub&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Icono Hacia Club </label>
                                    <input name="IconoHaciaClub" id=file class="" title="IconoHaciaClub" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoHaciaClub"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoHaciaClub"] . "' height'300px' width='300px' >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoHaciaClub]&campo=IconoHaciaClub&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Icono Crear Disponibilidad </label>
                                    <input name="IconoCrearDisponibilidad" id=IconoCrearDisponibilidad class="" title="IconoCrearDisponibilidad" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["IconoCrearDisponibilidad"])) {
                                            echo "<img src='" . CARPOL_ROOT . $frm["IconoCrearDisponibilidad"] . "' height'300px' width='300px' >";
                                        ?>
                                            <a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoCrearDisponibilidad]&campo=IconoCrearDisponibilidad&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Modelo </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteModelo"], 'PermiteModelo', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Color</label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteColor"], 'PermiteColor', "class='input'") ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Reusar </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteReusar"], 'PermiteReusar', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Calificar</label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteCalificar"], 'PermiteCalificar', "class='input'") ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Cancelar </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteCancelar"], 'PermiteCancelar', "class='input'") ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Agregar Telefono </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteAgregarTelefono"], 'PermiteAgregarTelefono', "class='input'") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Agregar Valor </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteAgregarValor"], 'PermiteAgregarValor', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Descripcion </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteDescripcion"], 'PermiteDescripcion', "class='input'") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Placa </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermitePlaca"], 'PermitePlaca', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Permite Marca</label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermiteMarca"], 'PermiteMarca', "class='input'") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Activo </label>

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