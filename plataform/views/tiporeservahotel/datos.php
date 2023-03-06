<!-- PAGE CONTENT BEGINS -->


<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



    <div class="form-group first ">
        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label" for="form-field-1">Tipo</label>
            <div class="select col-sm-8">
                <select name="Tipo" id="Tipo" class="form-control mandatory" required>
                    <option value=""></option>
                    <option value="Estadia" <?php if ($frm["Tipo"] == "Estadia") echo "selected"; ?>>Estadia</option>
                    <option value="Pasadia" <?php if ($frm["Tipo"] == "Pasadia") echo "selected"; ?>>Pasadia</option>

                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label" for="form-field-1">Texto para escoger el tipo de reserva</label>
            <div class="col-sm-8"> <input type="text" id="LabelTipo" name="LabelTipo" placeholder="" class="form-control mandatory" title="LabelTipo" value="<?php echo $frm["LabelTipo"] ?>" required></div>


        </div>


    </div>
    <div id="mostrarPasadia" style="<?php if ($frm["Tipo"] == "Pasadia") echo "";
                                    else echo "display:none"; ?> ">
        <div class="form-group first">

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label" for="form-field-1">Capacidad pasadia</label>
                <div class="col-sm-8"> <input type="number" id="CapacidadPasadia" name="CapacidadPasadia" placeholder="" class="form-control" title="Capacidad Pasadia" value="<?php echo $frm["CapacidadPasadia"] ?>" required></div>

            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label" for="form-field-1">Texto boton acompañante pasadia</label>
                <div class="col-sm-8"> <input type="text" id="LabelBotonAcompanantePasadia" name="LabelBotonAcompanantePasadia" placeholder="" class="form-control" title="Texto Boton Acompanante Pasadia" value="<?php echo $frm["LabelBotonAcompanantePasadia"] ?>" required></div>

            </div>


        </div>

        <div class="form-group first">

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label" for="form-field-1">Texto Dueño reserva pasadia</label>
                <div class="col-sm-8"> <input type="text" id="LabelDuenoReservaPasadia" name="LabelDuenoReservaPasadia" placeholder="" class="form-control" title="Texto Dueno Reserva Pasadia" value="<?php echo $frm["LabelDuenoReservaPasadia"] ?>" required></div>

            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label" for="form-field-1">Texto Dueño invitado pasadia</label>
                <div class="col-sm-8"> <input type="text" id="LabelDuenoInvitadoPasadia" name="LabelDuenoInvitadoPasadia" placeholder="" class="form-control" title="Texto Dueno Invitado Pasadia" value="<?php echo $frm["LabelDuenoInvitadoPasadia"] ?>" required></div>

            </div>
        </div>




        <div class="form-group first">


            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Invitado externo socio pasadia </label>

                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "$frm[InvitadoExternoSocioPasadia]", "InvitadoExternoSocioPasadia", "title=\"InvitadoExternoSocioPasadia\"") ?></div>
            </div>

        </div>

        <div class="form-group first">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Adicional pasadia </label>

                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "$frm[AdicionalPasadia]", "AdicionalPasadia", "title=\"AdicionalPasadia\"") ?></div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Observaciones pasadia </label>

                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "$frm[ObservacionesPasadia]", "ObservacionesPasadia", "title=\"ObservacionesPasadia\"") ?></div>
            </div>
        </div>



        <div class="form-group first">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Boton adicional pasadia </label>

                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "$frm[BotonAdicionalPasadia]", "BotonAdicionalPasadia", "title=\"BotonAdicionalPasadia\"") ?></div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Boton niñera pasadia </label>

                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "$frm[BotonNineraPasadia]", "BotonNineraPasadia", "title=\"BotonNineraPasadia\"") ?></div>
            </div>
        </div>

        <div class="form-group first">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Boton corral pasadia </label>

                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "$frm[BotonCorralPasadia]", "BotonCorralPasadia", "title=\"BotonCorralPasadia\"") ?></div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Boton invitado pasadia </label>

                <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "$frm[BotonInvitadoPasadia]", "BotonInvitadoPasadia", "title=\"BotonInvitadoPasadia\"") ?></div>
            </div>
        </div>

        <div class="form-group first ">

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Formulario Personalizado cuando el dueño es invitado? (si se marca Si diligenciar que datos preguntar si se marca no se osrrar por defecto "Externo o Socio Club") </label>

                <div class="col-sm-8">
                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["FormularioInvitadoPasadia"], 'FormularioInvitadoPasadia', "class='input'") ?>
                    <input type="text" id="CamposInvitadoPasadia" name="CamposInvitadoPasadia" placeholder="Cedula,Nombre" class="col-xs-12 " title="CamposInvitadoPasadia" value="<?php if (empty($frm["CamposInvitadoPasadia"])) {
                                                                                                                                                                                        echo "Cedula,Nombre";
                                                                                                                                                                                    } else {
                                                                                                                                                                                        echo $frm["CamposInvitadoPasadia"];
                                                                                                                                                                                    }; ?>">
                </div>
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