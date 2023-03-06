Registrar Socio a la encuesta
<form class="form-horizontal formvalida" role="form" method="post" id="RegistraSocio<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>
            <div class="col-sm-8">
                <input type="text" id="Accion" name="Accion" placeholder="Accion Nombre Apellido Numero Documento" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" value="">
                <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
            </div>
        </div>
    </div>

    <!--  <?php
            $IDSocio = $frm["IDSocio"];
            $IDEncuesta = $_GET["id"];
            $IDClub = SIMUser::get("club");


            $sql = "SELECT UnaporSocio FROM Encuesta WHERE IDEncuesta= '$IDEncuesta' ";
            // echo $sql;

            $query = $dbo->query($sql);
            $encuesta = $dbo->fetchArray($query);
            $frm1 = $encuesta;

            if ($frm1["UnaporSocio"] == 'S') {

                $sql1 = "SELECT IDSocio FROM EncuestaRespuesta WHERE IDEncuesta= '$IDEncuesta' AND IDSocio ='$IDSocio' ";
                echo $sql1;
                $query1 = $dbo->query($sql1);
                $encuestarespuesta = $dbo->fetchArray($query1);
                $frm2 = $encuestarespuesta;
                if ($frm2["IDSocio"] <> "") {
                    // echo "<script>alert('Solo puede realizar la encuesta 1 vez');'</script>";
                }
            }


            ?> -->


    <!--
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
         $("#IDSocio").change(function() {
            IDSocio1 = Number(document.getElementById("IDSocio").value);
            alert("id es:" + IDSocio1);
        }) 
        $.ajax
    </script>-->


    <div class="form-group first ">
        <?php
        //Consulto los campos dinamicos

        $r_campos = &$dbo->all("Pregunta", "IDEncuesta = '" . $frm[$key]  . "'");
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
            <input type="submit" class="submit" id="btnGuardarFormularioSocio" value="Guardar">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club") ?>" />



        </div>
    </div>




</form>
<!-- 
<br><a href="procedures/excel-responderencuesta.php?IDEncuesta=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif">Exportar</a>
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>

        <th>Encuesta</th>
        <th>Socio</th>

        <th>Valor</th>

        <th>Fecha registro</th>

        <?php
        //Consulto los campos dinamicos
        $r_campos = &$dbo->all("Pregunta", "IDEncuesta = '" . $frm[$key]  . "'");
        while ($r = $dbo->object($r_campos)) :
            $array_campos[] = $r->IDPregunta;    ?>
            <th><?php echo $r->EtiquetaCampo; ?></th>
        <?php endwhile; ?>
        <th>Eliminar</th>


    </tr>
    <tbody id="listacontactosanunciante">
        <?php
        $r_datos = &$dbo->all("EncuestaRespuesta", "IDEncuesta = '" . $frm[$key]  . "'");
        while ($r = $dbo->object($r_datos)) : ?>
            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                <td><?php echo $dbo->getFields("Encuesta", "Nombre", "IDEncuesta = '" . $r->IDEncuesta . "'"); ?></td>
                <td><?php echo utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r->IDSocio . "'") . "" . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r->IDSocio . "'"));
                    // $corresoc = $dbo->getFields("Socio", "CorreoElectronico", "IDSocio = '" . $r->IDSocio . "'");
                    if (!empty($corresoc))
                        echo "(" . $corresoc . ")";
                    ?></td>

                <td><?php echo $r->Valor; ?></td>

                <td><?php echo $r->FechaTrCr; ?></td>
                <?php
                //Consulto los campos dinamicos
                $r_campos = &$dbo->all("EncuestaRespuesta", "IDEncuesta = '" . $r->IDEncuesta  . "'");
                while ($rdatos = $dbo->object($r_campos)) :
                    $array_otros_datos[$rdatos->IDEncuestaRespuesta][$rdatos->IDPregunta] =  $rdatos->Valor;
                endwhile;

                if (count($array_campos) > 0) :
                    foreach ($array_campos as $id_campo) : ?>
                        <td>&nbsp;<?php echo $array_otros_datos[$r->IDEncuestaRespuesta][$id_campo]; ?></td>
                <?php endforeach;
                endif; ?>

                <td align="center"><a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaRegistro&id=<?php echo $r->IDEncuesta; ?>&IDEncuestaRespuesta=<? echo $r->IDEncuestaRespuesta ?>&tabencuesta=responderencuesta"></a></td>

            </tr>
        <?php endwhile; ?>

    </tbody>
    <tr>
        <th class="texto" colspan="16"></th>
    </tr>
</table> -->