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
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoArchivo', LANGSESSION); ?></label>
                                <div class="col-sm-8"> <?php //echo SIMHTML::formPopUp( "TipoArchivo" , "Nombre" , "Nombre" , "IDTipoArchivo" , $frm["IDTipoArchivo"] , "[Seleccione el Tipo de Archivo]" , "popup mandatory" , "title = \"IDTipo Archivo\"" )
									?>
                                    <!--
                                             <select name = "IDTipoArchivo" id="IDTipoArchivo" class="form-control">
                                        		<option value=""></option>
                                             	<?php
													$sql_tipoarch_club = "Select * From TipoArchivo  Where  IDClub = '8'";
													$qry_tipoarchivo_club = $dbo->query($sql_tipoarch_club);
													while ($r_tipoarch = $dbo->fetchArray($qry_tipoarchivo_club)) : ?>
													<option value="<?php echo $r_tipoarch["IDTipoArchivo"]; ?>" <?php if ($r_tipoarch["IDTipoArchivo"] == $frm["IDTipoArchivo"]) echo "selected";  ?>><?php echo $r_tipoarch["Nombre"]; ?></option>
												<?php endwhile;  ?>
                                        	</select>
                                            -->
                                    <select name="IDTipoArchivoInfinito" id="IDTipoArchivoInfinito" class="form-control">
                                        <option value=""></option> <?php
										$sql_tipoarch_club = "Select * From TipoArchivoInfinito  Where  IDClub = '" . SIMUser::get("club") . "' AND IDModulo='" . $_GET["IDModulo"] . "'";
										$qry_tipoarchivo_club = $dbo->query($sql_tipoarch_club);
										while ($r_tipoarch = $dbo->fetchArray($qry_tipoarchivo_club)) : ?> <option value="<?php echo $r_tipoarch["IDTipoArchivoInfinito"]; ?>" <?php if ($r_tipoarch["IDTipoArchivoInfinito"] == $frm["IDTipoArchivoInfinito"]) echo "selected";  ?>><?php echo $r_tipoarch["Nombre"]; ?></option> <?php endwhile;  ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Servicio', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <select name="IDServicio" id="IDServicio">
                                        <option value=""></option> <?php
										$sql_servicio_club = "Select S.IDServicio, SM.Nombre From Servicio S, ServicioMaestro SM Where S.IDServicioMaestro = SM.IDServicioMaestro and IDClub = '" . SIMUser::get("club") . "'";
										$qry_servicio_club = $dbo->query($sql_servicio_club);
										while ($r_servicio = $dbo->fetchArray($qry_servicio_club)) : ?> <option value="<?php echo $r_servicio["IDServicio"]; ?>" <?php if ($r_servicio["IDServicio"] == $frm["IDServicio"]) echo "selected";  ?>><?php echo $r_servicio["Nombre"]; ?></option> <?php
										endwhile;    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Titulo', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Subtitulo', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Subtitular" name="Subtitular" placeholder="<?= SIMUtil::get_traduccion('', '', 'Subtitular', LANGSESSION); ?>" class="col-xs-12" title="Subtitular" value="<?php echo $frm["Subtitular"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Fecha" name="Fecha" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fecha', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha" value="<?php echo $frm["Fecha"] ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? if (!empty($frm[Archivo1])) { ?>
                                    <a target="_blank" href="<?php echo DOCUMENTO_ROOT . $frm[Archivo1] ?>"><?php echo $frm[Archivo1]; ?></a>
                                    <a href="<? echo $script . " .php?action=delDoc&doc=$frm[Archivo1]&campo=Archivo1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
									} // END if
									?>
                                    <input name="Archivo1" id=file class="" title="Archivo1" type="file" size="25" style="font-size: 10px">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?> </label>
                                <input name="Icono" id=file class="" title="Icono" type="file" size="25" style="font-size: 10px">
                                <div class="col-sm-8">
                                    <? if (!empty($frm[Icono])) {
										echo "<img src='" . DOCUMENTO_ROOT . "$frm[Icono]' >";
									?>
                                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[Icono]&campo=Icono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
									} // END if
									?>
                                </div>
                            </div>
                        </div>
						<div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar PDF para android dentro de la aplicación </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarPDFInternoAndroid"], 'MostrarPDFInternoAndroid', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="number" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 " title="Orden" value="<?php echo $frm["Orden"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EnviarNotificación', LANGSESSION); ?> ? </label>
                                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?></div>
                            </div>
                        </div>
                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                <input type="hidden" name="ModuloActual" id="ModuloActual" value="<?php echo SIMReg::get("title"); ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
                                <input type="hidden" name="IDModulo" id="IDModulo" value="<?php echo $_GET["IDModulo"] ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i> <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?> </button>
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