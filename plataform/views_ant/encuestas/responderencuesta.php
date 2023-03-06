<?= SIMUtil::get_traduccion('', '', 'RegistrarSocioalaencuesta', LANGSESSION); ?>
<form class="form-horizontal formvalida" role="form" method="post" id="RegistraSocio<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'AccionNombreApellidoNumeroDocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" value="">
                <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">
            </div>
        </div>
    </div>







    <div class="form-group first ">
        <?php
        //Consulto los campos dinamicos

        //$r_campos = &$dbo->all("Pregunta", "IDEncuesta = '" . $frm[$key]  . "'");

        $sql = "SELECT IDPregunta,IDEncuesta,TipoCampo,EtiquetaCampo,Obligatorio,Valores,Publicar FROM Pregunta WHERE IDEncuesta='" . $frm[$key] . "' Order by Orden";
        $r_campos = $dbo->query($sql);
        while ($r = $dbo->object($r_campos)) : ?>
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $r->EtiquetaCampo; ?> </label>
                <div class="col-sm-8">
                    <!-- si la pregunta es de tipo radio-->
                    <?php
                    $radios = explode("|", $r->Valores);
                    for ($i = 0; $i < count($radios); $i++) {
                        if ($r->TipoCampo == "radio") { ?>
                            <input type="radio" name="Campo<?php echo $r->IDPregunta; ?>" id="Campo<?php echo $r->IDPregunta; ?>" value="<?php echo $radios[$i] ?>"><?php echo $radios[$i] ?><br>
                    <?php }
                    } ?>
                    <!--end if ** end for--->

                    <!-- si la pregunta es de tipo text-->
                    <?php if ($r->TipoCampo == "text") { ?>

                        <input type="text" id="Campo<?php echo $r->IDPregunta; ?>" name="Campo<?php echo $r->IDPregunta; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="">
                    <?php } ?>
                    <!--end if--->
                    <!-- si la pregunta es de tipo checkbox-->
                    <?php
                    $checkbox = explode("|", $r->Valores);
                    for ($i = 0; $i < count($radios); $i++) {
                        if ($r->TipoCampo == "checkbox") { ?>

                            <input type="checkbox" name="Campo<?php echo $r->IDPregunta; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" id="Campo<?php echo $r->IDPregunta; ?>" value="<?php echo $checkbox[$i] ?>"> <?php echo $checkbox[$i] ?><br>

                    <?php }
                    } ?>

                    <!-- si la pregunta es de tipo textarea-->
                    <?php

                    if ($r->TipoCampo == "textarea") { ?>

                        <textarea name="Campo<?php echo $r->IDPregunta; ?>" id="Campo<?php echo $r->IDPregunta; ?>" cols="30" rows="10"></textarea>

                    <?php }
                    ?>

                    <!-- si la pregunta es de tipo select-->
                    <?php if ($r->TipoCampo == "select") { ?>
                        <select name="Campo<?php echo $r->IDPregunta; ?>" id="Campo<?php echo $r->IDPregunta; ?>">
                            <option value=""></option>
                            <?php
                            $select = explode("|", $r->Valores);
                            for ($i = 0; $i < count($select); $i++) {
                            ?>

                                <option value="<?php echo $select[$i] ?>"><?php echo $select[$i] ?></option>
                            <?php }
                            ?>
                        </select>
                    <?php } ?>

                    <!-- si la pregunta es de tipo number-->
                    <?php

                    if ($r->TipoCampo == "number") { ?>

                        <input type="number" name="Campo<?php echo $r->IDPregunta; ?>" id="Campo<?php echo $r->IDPregunta; ?>">

                    <?php }
                    ?>

                    <!-- si la pregunta es de tipo date-->
                    <?php

                    if ($r->TipoCampo == "date") { ?>

                        <input type="date" name="Campo<?php echo $r->IDPregunta; ?>" id="Campo<?php echo $r->IDPregunta; ?>">

                    <?php }
                    ?>

                    <!-- si la pregunta es de tipo time-->
                    <?php

                    if ($r->TipoCampo == "time") { ?>

                        <input type="time" name="Campo<?php echo $r->IDPregunta; ?>" id="Campo<?php echo $r->IDPregunta; ?>">


                    <?php }
                    ?>

                    <!-- si la pregunta es de tipo email-->
                    <?php

                    if ($r->TipoCampo == "email") { ?>

                        <input type="email" name="Campo<?php echo $r->IDPregunta; ?>" id="Campo<?php echo $r->IDPregunta; ?>">


                    <?php }
                    ?>

                    <!-- si la pregunta es de tipo rating-->
                    <?php

                    for ($i = 1; $i <= 5; $i++) {
                        if ($r->TipoCampo == "rating") { ?>
                            <input type="radio" name="Campo<?php echo $r->IDPregunta; ?>" id="Campo<?php echo $r->IDPregunta; ?>" value="<?php echo $i ?>"><?php echo $i ?>
                    <?php }
                    } ?>

                    <!-- si la pregunta es de tipo imagen-->
                    <?php

                    if ($r->TipoCampo == "imagen") { ?>


                        <input type="file" name="Campo|<?php echo $r->IDPregunta; ?>" id="Campo|<?php echo $r->IDPregunta; ?>">

                    <?php }
                    ?>

                    <!-- si la pregunta es de tipo titulo-->
                    <?php

                    if ($r->TipoCampo == "titulo") { ?>

                        <input type="text" id="Campo<?php echo $r->IDPregunta; ?>" name="Campo<?php echo $r->IDPregunta; ?>" placeholder="<?php echo $r->EtiquetaCampo; ?>" class="col-xs-12" title="<?php echo $r->EtiquetaCampo; ?>" value="">


                    <?php }
                    ?>



                </div>
            </div>
        <?php endwhile; ?>
    </div>







    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $EditEncuesta[$key] ?>" />
            <input type="hidden" name="IDEncuesta" id="IDEncuesta" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="responderencuesta" />
            <input type="submit" class="submit" id="btnGuardarFormularioSocio" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />



        </div>
    </div>




</form>