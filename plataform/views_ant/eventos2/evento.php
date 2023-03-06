<!-- PAGE CONTENT BEGINS -->


<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Seccion', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input type="hidden" id="IDSeccionEvento2" name="IDSeccionEvento2" value="<?php echo $frm["IDSeccionEvento2"]; ?>">
                <input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields("SeccionEvento2", "Nombre", "IDSeccionEvento2 = '" . $frm["IDSeccionEvento2"] . "'") ?>" readonly>
                <a href="PopupSeccionEvento2.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;" class="ace-icon glyphicon glyphicon-search"></a>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Eventopara', LANGSESSION); ?>: </label>

            <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>
                <?php
                if (SIMUser::get("club") == "36") {
                    echo "<br>Tipo:";
                    echo SIMHTML::formPopupArray(SIMResources::$tipo_socio,  $frm["TipoSocio"], "TipoSocio",  "Seleccione tipo", "form-control");
                } ?>
            </div>
        </div>



    </div>


    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Titular', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input id="Titular" type="text" size="25" title="Titular" name="Titular" class="input mandatory" value="<?php echo $frm["Titular"] ?>" />
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Introduccion', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <textarea rows="5" cols="50" id="Introduccion" name="Introduccion" class="input"><?php echo $frm["Introduccion"] ?></textarea>
            </div>
        </div>



    </div>



    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Lugar', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input id="Lugar" type="text" size="25" title="Lugar" name="Lugar" class="input mandatory" value="<?php echo $frm["Lugar"] ?>" />
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Hora', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input id="Hora" type="time" size="25" title="Hora" name="Hora" class="input" value="<?php echo $frm["Hora"] ?>" />
            </div>
        </div>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaEvento', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input type="text" id="FechaEvento" name="FechaEvento" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaEvento', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha Evento" value="<?php echo $frm["FechaEvento"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaFinEvento', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input type="text" id="FechaFinEvento" name="FechaFinEvento" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFinEvento', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha Fin Evento" value="<?php echo $frm["FechaFinEvento"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DescripcionValor', LANGSESSION); ?>$</label>

            <div class="col-sm-8">
                <textarea rows="5" cols="50" id="Valor" name="Valor" class="input"><?php echo $frm["Valor"] ?></textarea>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EmailContacto', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input id="EmailContacto" type="text" size="25" title="Email Contacto" name="EmailContacto" class="input mandatory" value="<?php echo $frm["EmailContacto"] ?>" />
            </div>
        </div>

    </div>






    <div class="form-group first">


        <?= SIMUtil::get_traduccion('', '', 'Cuerpo', LANGSESSION); ?>

        <div class="col-sm-12">
            <?php
            $oCuerpo = new FCKeditor("Cuerpo");
            $oCuerpo->BasePath = "js/fckeditor/";
            $oCuerpo->Height = 400;
            //$oCuerpo->EnterMode = "p";
            $oCuerpo->Value =  $frm["Cuerpo"];
            $oCuerpo->Create();
            ?>
        </div>


    </div>


    <!--
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cuerpo para Email</label>


											<?php
                                            $oCuerpo = new FCKeditor("CuerpoEmail");
                                            $oCuerpo->BasePath = "js/fckeditor/";
                                            $oCuerpo->Height = 400;
                                            //$oCuerpo->EnterMode = "p";
                                            $oCuerpo->Value =  $frm["CuerpoEmail"];
                                            $oCuerpo->Create();
                                            ?>

								</div>
                                -->




    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EnviarNotificaciónaSocios', LANGSESSION); ?> ? </label>

            <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?></div>
        </div>



    </div>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Publicar"], "Publicar", "title=\"Publicar\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input id="Orden" type="text" size="25" title="Orden" name="Orden" class="input mandatory" value="<?php echo $frm["Orden"] ?>" />
            </div>
        </div>

    </div>


    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicioPublicacion', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaFinPublicacion', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha Fin" value="<?php echo $frm["FechaFin"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pdfadjunto', LANGSESSION); ?> 1 </label>

            <div class="col-sm-8">
                <?php
                $ruta_adjunto1file = string;
                if ($frm["Adjunto1File"]) {

                    if (strstr(strtolower($frm["Adjunto1File"]), "http://"))
                        $ruta_adjunto1file = $frm["Adjunto1File"];
                    else
                        $ruta_adjunto1file = IMGEVENTO_ROOT . $frm["Adjunto1File"];
                ?>
                    <a target="_blank" href="<?php echo $ruta_adjunto1file; ?>"><?php echo $frm["Adjunto1File"] ?></a>
                    <a href="<? echo $script . ".php?action=DelDocNot&cam=Adjunto1File&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?php
                } else {
                ?>
                    <input type="file" name="Adjunto1Documento" id="Adjunto1Documento" class="popup" title="Noticia Documento">
                <?php
                }
                ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Pdfadjunto', LANGSESSION); ?> 2 </label>

            <div class="col-sm-8">
                <?php
                if ($frm["Adjunto2File"]) {

                    if (strstr(strtolower($frm["Adjunto2File"]), "http://"))
                        $ruta_adjunto2file = $frm["Adjunto2File"];
                    else
                        $ruta_adjunto2file = IMGEVENTO_ROOT . $frm["Adjunto2File"];
                ?>
                    <a target="_blank" href="<?php echo $ruta_adjunto1file; ?>"><?php echo $frm["Adjunto2File"] ?></a>
                    <a href="<? echo $script . ".php?action=DelDocNot&cam=Adjunto2File&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?php
                } else {
                ?>
                    <input type="file" name="Adjunto2Documento" id="Adjunto2Documento" class="popup" title="Noticia Documento">
                <?php
                }
                ?>

            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ImagenEvento', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <?php
                if ($frm["EventoFile"]) {
                    if (strstr(strtolower($frm["EventoFile"]), "http://"))
                        $ruta_notfile = $frm["EventoFile"];
                    else
                        $ruta_notfile = IMGEVENTO_ROOT . $frm["EventoFile"];

                ?>
                    <img alt="<?php echo $frm["EventoFile"] ?>" src="<?php echo $ruta_notfile; ?>">
                    <a href="<? echo $script . ".php?action=DelImgNot&cam=EventoFile&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?php
                } else {
                ?>
                    <input type="file" name="EventoImagen" id="EventoImagen" class="popup" title="Evento Imagen">
                <?php
                }
                ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitiralsocioeliminarsedelevento', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(SIMResources::$sinoNum, $frm["PermiteEliminarSocio"], "PermiteEliminarSocio", "title=\"PermiteEliminarSocio\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mensajenopermiteeliminar', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <textarea rows="5" cols="50" id="MensajeNoEliminarSocio" name="MensajeNoEliminarSocio" class="input"><?php echo $frm["MensajeNoEliminarSocio"] ?></textarea>
            </div>
        </div>

    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-credit-card green"></i>
            <?= SIMUtil::get_traduccion('', '', 'ParametrosInscripcion', LANGSESSION); ?>
        </h3>
    </div>

    <div class="form-group first">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteInscripcionporApp', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["InscripcionApp"], "InscripcionApp", "title=\"InscripcionApp\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pagoinscripcion', LANGSESSION); ?>: </label>
            <div class="col-sm-8">
                <?php
                $sql_tipo_pago_servicio = "Select * From EventoTipoPago2 Where IDEvento2 = '" . SIMNet::reqInt("id") . "'";
                $result_tipo_pago_servicio = $dbo->query($sql_tipo_pago_servicio);
                while ($row_tipo_pago_servicio = $dbo->fetchArray($result_tipo_pago_servicio)) :
                    $array_tipo_pago_servicio[] = $row_tipo_pago_servicio["IDTipoPago"];
                endwhile;
                $sql_tipo_pago = "Select * From TipoPago Where Publicar = 'S'";
                $result_tipo_pago = $dbo->query($sql_tipo_pago);
                while ($row_tipo_pago = $dbo->fetchArray($result_tipo_pago)) : ?>
                    <input type="checkbox" name="IDTipoPago[]" id="IDTipoPago" value="<?php echo $row_tipo_pago["IDTipoPago"]; ?>" <?php if (in_array($row_tipo_pago["IDTipoPago"], $array_tipo_pago_servicio)) echo "checked"; ?>><?php echo $row_tipo_pago["Nombre"]; ?><br>
                <?php endwhile; ?>
            </div>
        </div>


    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mensajepagoreserva', LANGSESSION); ?></label>
            <div class="col-sm-8">
                <input id=MensajePagoInscripcion type=text size=25 name=MensajePagoInscripcion class="input" title="Mensaje Pago Inscripcion" value="<?= $frm["MensajePagoInscripcion"] ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Valorinscripcion', LANGSESSION); ?></label>
            <div class="col-sm-8">
                $<input id=ValorInscripcion type=number size=25 name=ValorInscripcion class="input" title="Valor Inscripcion" value="<?= $frm["ValorInscripcion"] ?>">

            </div>
        </div>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Maximodeparticipantes', LANGSESSION); ?>:</label>
            <div class="col-sm-8">
                <input id="MaximoParticipantes" type="number" size=25 name="MaximoParticipantes" class="input mandatory" title="Maximo Participantes" value="<?= $frm["MaximoParticipantes"] ?>">

            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Emailnotificacioninscripcion', LANGSESSION); ?>:</label>
            <div class="col-sm-8">
                <input id="EmailNotificacionInscripcion" type=text size=25 name="EmailNotificacionInscripcion" class="input" title="Email notificacion" value="<?= $frm["EmailNotificacionInscripcion"] ?>">

            </div>
        </div>

    </div>

    <div class="form-group first">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitirinscripcionmasdeunavezalmismosocio', LANGSESSION); ?>? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteRepetir"], "PermiteRepetir", "title=\"PermiteRepetir\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitirinscripcioneshastalasiguientefecha/hora', LANGSESSION); ?>: </label>

            <div class="col-sm-8">
                <input type="text" id="FechaLimiteInscripcion" name="FechaLimiteInscripcion" placeholder="Fecha Limite Inscripcion" class="col-xs-12 calendar" title="Fecha Limite Inscripcion" value="<?php echo $frm["FechaLimiteInscripcion"] ?>">
                <input id="HoraLimiteInscripcion" type="time" size="25" title="Hora" name="HoraLimiteInscripcion" class="input" value="<?php echo $frm["HoraLimiteInscripcion"] ?>" />
            </div>
        </div>
    </div>





    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <input type="hidden" name="ModuloActual" id="ModuloActual" value="<?php echo SIMReg::get("title"); ?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                    else echo $frm["IDClub"];  ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm">
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
            </button>


        </div>
    </div>

</form>