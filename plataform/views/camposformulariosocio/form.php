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
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label " for="Nombre"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>: </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="IDGruposFormularioSocio"><?= SIMUtil::get_traduccion('', '', 'Grupo', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <?php
                                    $sqlGrupo = "SELECT IDGruposFormularioSocio,Nombre FROM GruposFormularioSocio ORDER BY Orden";
                                    $resultGrupo = $dbo->query($sqlGrupo);
                                    ?>
                                    <select name="IDGruposFormularioSocio" id="IDGruposFormularioSocio" class="form-control">
                                        <?php while ($rowGrupo = $dbo->fetchArray($resultGrupo)) { ?>
                                            <option value="<?php echo $rowGrupo["IDGruposFormularioSocio"];  ?>" <?php if ($frm["IDGruposFormularioSocio"] == $rowGrupo["IDGruposFormularioSocio"]) echo "selected"; ?>><?php echo $rowGrupo["Nombre"];  ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="CampoKey"><?= SIMUtil::get_traduccion('', '', 'Campoenlatabla', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <input type="text" id="CK" name="CK" placeholder="<?= SIMUtil::get_traduccion('', '', 'Campoenlatabla', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-columna" title="<?= SIMUtil::get_traduccion('', '', 'Campoenlatabla', LANGSESSION); ?>" value="<?php echo $frm["CampoKey"]; ?>">
                                    <input type="hidden" id="CampoKey" name="CampoKey" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Campoenlatabla', LANGSESSION); ?>" value="<?php echo $frm["CampoKey"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label " for="Clase"><?= SIMUtil::get_traduccion('', '', 'Clase(Separarconunespaciosiesmasdeunaclase)', LANGSESSION); ?>: </label>
                                <div class="col-sm-8"><input type="text" id="Clase" name="Clase" placeholder="<?= SIMUtil::get_traduccion('', '', 'Clase', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Clase', LANGSESSION); ?>" value="<?php echo $frm["Clase"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Tipo"><?= SIMUtil::get_traduccion('', '', 'Tipodecampo', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <select name="Tipo" id="Tipo" class="mandatory">
                                        <?
                                        $options = array(
                                            "Texto en una línea" => "text",
                                            "Texto en párrafo" => "textarea",
                                            "Múltiples opciones" => "radio",
                                            "Casillas de verificación" => "checkbox",
                                            "Menú desplegable" => "select",
                                            "Número" => "number",
                                            "Fecha" => "date",
                                            "Hora" => "time",
                                            "Correo electrónico" => "email",
                                            "Contraseña" => "password",
                                            "Imagen" => "file"
                                        );
                                        foreach ($options as $key => $val) {
                                            if ($frm["Tipo"] == $val) {
                                                echo '<option value="' . $val . '" selected="selected">' . $key . '</option>';
                                            } else {
                                                echo '<option value="' . $val . '">' . $key . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 txtCampo" style="display:none">
                                <label class="col-sm-4 control-label " for="TxtCampo"><?= SIMUtil::get_traduccion('', '', 'Textodentrodelcampo', LANGSESSION); ?>: </label>
                                <div class="col-sm-8"><input type="text" id="TxtCampo" name="TxtCampo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Textodentrodelcampo', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Textodentrodelcampo', LANGSESSION); ?>" value="<?php echo $frm["TxtCampo"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first">
                            <div class="col-xs-12 col-sm-6 opciones" style="display:none">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Opcionesdeseleccion', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <?
                                    $arrOpciones = array('', 'Desde texto', 'Desde base de datos', 'Desde consulta en base de datos', 'Desde Clase');
                                    foreach ($arrOpciones as $key => $val) {
                                        $chk = ($key == $frm["TipoOpciones"]) ? " checked" : "";
                                        $hidden = ($key == 0) ? 'style="display:none"' : "" ?>
                                        <label class="radiogroup">
                                            <input type="radio" name="TipoOpciones" id="TipoOpciones" value="<? echo $key ?>" <? echo $hidden ?> <? echo $chk ?>> <? echo $val ?>
                                        </label>
                                        <br>
                                    <? } ?>
                                </div>
                            </div>
                            <div id="contentOpciones">
                                <div class="col-xs-12 col-sm-6 opSeleccion" style="display:none">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Opcionesdeseleccion(Separelasopcionesconelsiguienteformato"nom1=val1|nom2=val2")', LANGSESSION); ?> </label>
                                    <div class="col-sm-8">
                                        <textarea id="OpcionesSeleccion" name="OpcionesSeleccion" cols="10" rows="5" class="col-xs-12"><?php echo $frm["OpcionesSeleccion"]; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6 opConsulta" style="display:none">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'ConsultaSQL(Laconsultadebedarcomoresultadodosvalores,elnombreyelvalordelaopcionaguardar)', LANGSESSION); ?> </label>
                                    <div class="col-sm-8">
                                        <textarea id="ConsultaBD" name="ConsultaBD" cols="10" rows="5" class="col-xs-12"><?php echo $frm["ConsultaBD"]; ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group first opClase" style="display:none">
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Opcionesdesdeclase', LANGSESSION); ?>: </label>
                                        <div class="col-sm-8"><input type="text" id="OpcionesClase" name="OpcionesClase" placeholder="<?= SIMUtil::get_traduccion('', '', 'Opcionesdesdeclase', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Opcionesdesdeclase', LANGSESSION); ?>" value="<?php echo $frm["OpcionesClase"]; ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first opCrear" style="display:none">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Nombredelatabla', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <input type="text" id="NT" name="NT" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nombredelatabla', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-tabla" title="<?= SIMUtil::get_traduccion('', '', 'Nombredelatabla', LANGSESSION); ?>" value="<?php echo $frm["NombreTabla"]; ?>">
                                    <input type="hidden" name="NombreTabla" value="<?php echo $frm["NombreTabla"]; ?>" id="NombreTabla">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Nombredelcampodelaetiquetaamostrar', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <input type="text" id="CN" name="CN" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nombredelcampodelaetiquetaamostrar', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-columna" title="<?= SIMUtil::get_traduccion('', '', 'Nombredelcampodelaetiquetaamostrar', LANGSESSION); ?>" value="<?php echo $frm["CampoName"]; ?>">
                                    <input type="hidden" name="CampoName" value="<?php echo $frm["CampoName"]; ?>" id="CampoName">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first opCrear" style="display:none">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Nombredelcampodelvaloralseleccionar', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <input type="text" id="CV" name="CV" placeholder="<?= SIMUtil::get_traduccion('', '', 'Nombredelcampodelvaloralseleccionar', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-columna" title="<?= SIMUtil::get_traduccion('', '', 'Nombredelcampodelvaloralseleccionar', LANGSESSION); ?>" value="<?php echo $frm["CampoValue"]; ?>">
                                    <input type="hidden" name="CampoValue" value="<?php echo $frm["CampoValue"]; ?>" id="CampoValue">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Condicion', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Condicion" name="Condicion" placeholder="<?= SIMUtil::get_traduccion('', '', 'Condicion', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Condicion', LANGSESSION); ?>" value="<?php echo $frm["Condicion"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'AgregarFuncion', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["AgregarFuncion"], 'AgregarFuncion', "class='input'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 funciones" style="display:none">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Llamarlafuncioncon', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <select name="TipoFuncion" id="TipoFuncion">
                                        <?
                                        $options = array("Change", "Click", "KeyUp");
                                        foreach ($options as $key => $val) {
                                            $key++;
                                            if ($frm["TipoFuncion"] == $key) {
                                                echo '<option value="' . $key . '" selected="selected">' . $val . '</option>';
                                            } else {
                                                echo '<option value="' . $key . '">' . $val . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first funciones" style="display:none">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Funcion', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <textarea id="Funcion" name="Funcion" cols="10" rows="5" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Funcion', LANGSESSION); ?>"><?php echo $frm["Funcion"]; ?></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Agregaridentificadorenlaclase', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["IdentificadorClase"], 'IdentificadorClase', "class='input'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group first">
                            <div class="col-xs-12 col-sm-6 idClase" style="display:none">
                                <label class="col-sm-4 control-label" for="NombreIdClase"><?= SIMUtil::get_traduccion('', '', 'Identificadorenlaclase', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <input type="text" id="NombreIdClase" name="NombreIdClase" placeholder="<?= SIMUtil::get_traduccion('', '', 'Identificadorenlaclase', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Identificadorenlaclase', LANGSESSION); ?>" value="<?php echo $frm["NombreIdClase"]; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
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
                                    <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                                </button>
                                <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?php
include("list");
include("cmp/footer_scripts.php");
?>

<script type="text/javascript">
    var arrClass = ['', $(".opSeleccion"), $(".opCrear"), $(".opConsulta"), $(".opClase")];
    var TipoOpciones = $("input[name='TipoOpciones']:checked").val();
    var Tipo = $("#Tipo").find('option:selected').attr("value");
    var AgregarFuncion = $('input[name="AgregarFuncion"]:checked').val();
    var IdClaseFuncion = $("input[name='IdentificadorClase']:checked").val();


    if (Tipo != 'radio' && Tipo != 'checkbox' && Tipo != 'time' && Tipo != 'file') {
        $(".txtCampo").show("slow");
    }

    if (TipoOpciones != null && TipoOpciones != 0) {
        $data = arrClass[TipoOpciones];

        $(".opciones").show("slow");
        $data.show("slow");
    }
    if (AgregarFuncion && AgregarFuncion == 'S') {
        $(".funciones").show("slow");
    }

    if (IdClaseFuncion && IdClaseFuncion == 'S') {
        $(".idClase").show("slow");
    }

    //muestra u oculta el div de opciones de seleccion dependiendo el tipo de campo                                             
    $("#Tipo").change(function() {
        var val = $(this).val();

        if (val == 'radio' || val == 'checkbox' || val == 'select') {
            $(".opciones").show("slow");
        } else {
            $(".opciones").hide("slow");
        }

        if (val == 'radio' || val == 'checkbox' || val == 'time' || val == 'file') {
            $(".txtCampo").hide("slow");
        } else {
            $(".txtCampo").show("slow");
        }

        arrClass.forEach((data, index) => {
            if (index != '') {
                $data = data;
                $data.hide("slow");
            }
        });

        $("#contentOpciones input, #contentOpciones textarea, .opCrear input, #TxtCampo").each(function() {
            $(this).val("");
        });
        $("input[name='TipoOpciones'][value=0]").prop('checked', true);
    });

    $("input[name='TipoOpciones']").change(function() {

        var valor = $(this).val();

        arrClass.forEach((data, index) => {
            if (index != '') {
                $data = data;

                if (valor == index) {
                    $data.show("slow");
                } else {
                    $data.hide("slow");
                }
            }
        });

        $("#contentOpciones input, #contentOpciones textarea, .opCrear input").each(function() {
            $(this).val("");
        });
    });

    $("input[name='CK']").on("keyup", function() {
        if ($(this).val() == "") {
            $('#CampoKey').val("");
        }
    });

    $("input[name='NT']").on("keyup", function() {
        if ($(this).val() == "") {
            $('#NombreTabla').val("");
        }
    });

    $("input[name='CN']").on("keyup", function() {
        if ($(this).val() == "") {
            $('#CampoName').val("");
        }
    });

    $("input[name='CV']").on("keyup", function() {
        if ($(this).val() == "") {
            $('#CampoValue').val("");
        }
    });

    $("input[name='AgregarFuncion']").change(function() {
        $('#TipoFuncion').val('0');
        $('#Funcion').val('');
        $('#NombreIdClase').val('');
        $("input[name='IdentificadorClase'][value='N']").prop('checked', true);

        if ($(this).val() == 'S') {
            $(".funciones").show("slow");
        } else if ($(this).val() == 'N') {
            $(".funciones").hide("slow");
            $(".idClase").hide("slow");
        }
    });

    $("input[name='IdentificadorClase']").change(function() {
        $('#NombreIdClase').val('');

        if ($(this).val() == 'S') {
            $(".idClase").show("slow");
        } else if ($(this).val() == 'N') {
            $(".idClase").hide("slow");
        }
    });
</script>