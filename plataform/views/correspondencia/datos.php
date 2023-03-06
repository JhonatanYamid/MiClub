<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

            <div class="col-sm-8">
                <?php
                $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                $qry_socio_club = $dbo->query($sql_socio_club);
                $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                <input type="text" id="Accion" name="Accion" placeholder="casa, nombre" class="col-xs-12 mandatory autocomplete-ajax" title="socio" value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
                <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

            <div class="col-sm-8">

                <select name="IDTipoCorrespondencia" id="IDTipoCorrespondencia" class="form-control mandatory" title="Tipo">
                    <option value=""></option>
                    <?php
                    $sql_cat_corresp = string;
                    $sql_cat_corresp = "Select * From TipoCorrespondencia Where IDClub = '" . SIMUser::get("club") . "' order by Nombre";
                    $qry_cat_corresp = $dbo->query($sql_cat_corresp);
                    while ($r_cat_corresp = $dbo->fetchArray($qry_cat_corresp)) : ?>
                        <option value="<?php echo $r_cat_corresp["IDTipoCorrespondencia"]; ?>" <?php if ($r_cat_corresp["IDTipoCorrespondencia"] == $frm["IDTipoCorrespondencia"]) echo "selected";  ?>><?php echo $r_cat_corresp["Nombre"]; ?></option>
                    <?php
                    endwhile;    ?>
                </select>

            </div>
        </div>


    </div>







    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario Crea </label>

            <div class="col-sm-8">

                <select name="IDUsuarioCrea" id="IDUsuarioCrea" class="form-control mandatory" title="Usuario Crea">
                    <option value=""></option>
                    <?php
                    $sql_usu = "Select * From Usuario Where IDClub = '" . SIMUser::get("club") . "' order by Nombre";
                    $qry_cat_corresp = $dbo->query($sql_usu);
                    while ($r_usu = $dbo->fetchArray($qry_cat_corresp)) : ?>
                        <option value="<?php echo $r_usu["IDUsuario"]; ?>" <?php if ($r_usu["IDUsuario"] == $frm["IDUsuarioCrea"]) echo "selected";  ?>><?php echo $r_usu["Nombre"]; ?></option>
                    <?php
                    endwhile;    ?>
                </select>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario Entrega </label>

            <div class="col-sm-8">

                <select name="IDUsuarioEntrega" id="IDUsuarioEntrega" class="form-control " title="Usuario Entrega">
                    <option value=""></option>
                    <?php
                    $sql_usu = "Select * From Usuario Where IDClub = '" . SIMUser::get("club") . "' order by Nombre";
                    $qry_cat_corresp = $dbo->query($sql_usu);
                    while ($r_usu = $dbo->fetchArray($qry_cat_corresp)) : ?>
                        <option value="<?php echo $r_usu["IDUsuario"]; ?>" <?php if ($r_usu["IDUsuario"] == $frm["IDUsuarioEntrega"]) echo "selected";  ?>><?php echo $r_usu["Nombre"]; ?></option>
                    <?php
                    endwhile;    ?>
                </select>

            </div>
        </div>

    </div>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Vivienda </label>

            <div class="col-sm-8">

                <input type="text" id="Vivienda" name="Vivienda" placeholder="Vivienda" class="col-xs-12 mandatory" title="Vivienda" value="<?php echo utf8_encode($frm["Vivienda"]); ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Destinatario </label>

            <div class="col-sm-8">

                <input type="text" id="Destinatario" name="Destinatario" placeholder="Destinatario" class="col-xs-12 mandatory" title="Destinatario" value="<?php echo utf8_encode($frm["Destinatario"]); ?>">

            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Recepcion </label>
            <?php $fechaRecepcion = explode(" ", $frm["FechaRecepcion"]) ?>
            <div class="col-sm-4">

                <input type="date" id="FechaRecepcion" name="FechaRecepcion" placeholder="FechaRecepcion" class="col-xs-12 calendar" title="Fecha Recepcion" value="<?php echo $fechaRecepcion[0] ?>">

            </div>
            <div class="col-sm-4">
                <input type="time" name="HoraFechaRecepcion" id="HoraFechaRecepcion" value="<?php echo $fechaRecepcion[1] ?>" required>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Entrega </label>
            <?php $fechaEntrega = explode(" ", $frm["FechaEntrega"]) ?>
            <div class="col-sm-4">
                <input type="date" id="FechaEntrega" name="FechaEntrega" placeholder="FechaEntrega" class="col-xs-12 calendar" title="Fecha Entrega" value="<?php echo $fechaEntrega[0] ?>">
            </div>

            <div class="col-sm-4">
                <input type="time" name="HoraFechaEntrega" id="HoraFechaEntrega" value="<?php echo $fechaEntrega[1] ?>" required>
            </div>
        </div>

    </div>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Entregado A </label>

            <div class="col-sm-8">
                <input type="text" id="EntregadoA" name="EntregadoA" placeholder="Entregado A" class="col-xs-12 " title="Entregado A" value="<?php echo utf8_encode($frm["EntregadoA"]); ?>">
            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Archivo </label>
            <div class="col-sm-8">

                <? if (!empty($frm[Archivo])) { ?>
                    <a target="_blank" href="<?php echo CORRESPONDENCIA_ROOT . $frm[Archivo] ?>"><?php echo $frm[Archivo]; ?></a>
                    <a href="<? echo $script . " .php?action=delfoto&doc=$frm[Archivo]&campo=Archivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="Archivo" id=file class="" title="Archivo" type="file" size="25" style="font-size: 10px">
            </div>
        </div>




    </div>


    <div class="form-group first ">






        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado </label>

            <div class="col-sm-8">

                <?php echo SIMHTML::formPopUp("CorrespondenciaEstado", "Nombre", "Nombre", "IDCorrespondenciaEstado", $frm["IDCorrespondenciaEstado"], "[Seleccione el estado]", "form-control", "title = \"Estado\"") ?>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones </label>

            <div class="col-sm-8">

                <textarea name="Observaciones" id="Observaciones" cols="30" rows="10"><?php echo $frm["Observaciones"] ?></textarea>

            </div>
        </div>


    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Firma </label>
            <input name="FotoFirma" id=file class="" title="FotoFirma" type="file" size="25" style="font-size: 10px">
            <div class="col-sm-8">
                <? if (!empty($frm["FotoFirma"])) {
                    echo "<img src='" . CORRESPONDENCIA_ROOT . $frm["FotoFirma"] . "' width='300px' height='300px' >";
                ?>
                    <a href="<? echo $script . ".php?action=delfoto&foto=$frm[FotoFirma]&campo=FotoFirma&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
            </div>
        </div>
    </div>

    </div>
    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Solo para los servicios Publicos
        </h3>
    </div>


    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero de Cliente </label>

            <div class="col-sm-8">

                <input type="text" id="NumeroCliente" name="NumeroCliente" placeholder="Numero de Cliente" class="col-xs-12" title="NumeroCliente" value="<?php echo utf8_encode($frm["NumeroCliente"]); ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Medidor </label>

            <div class="col-sm-8">

                <input type="text" id="Medidor" name="Medidor" placeholder="Medidor" class="col-xs-12" title="Medidor" value="<?php echo utf8_encode($frm["Medidor"]); ?>">

            </div>
        </div>

    </div>




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


        </div>
    </div>

</form>