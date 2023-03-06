<form class="form-horizontal formvalida" role="form" method="post" id="EditPregunta<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <?php
    $action = "InsertarPregunta";
    if ($_GET["IDIngresosPreguntas"]) {
        $EditPregunta = $dbo->fetchAll("IngresosPreguntas", " IDIngresosPreguntas = '" . $_GET["IDIngresosPreguntas"] . "' ", "array");
        $action = "ModificaPregunta";
        if ($EditPregunta['OrigenDatos'] == 'Tablas') { ?>
            <style>
                .selecttabla,
                .ContentOrigenDatos {
                    display: block;
                }

                .opcionesrespuesta {
                    display: none;
                }
            </style>
        <?php
        }
        ?>

        <input type="hidden" name="IDIngresosPreguntas" id="IDIngresosPreguntas" value="<?php echo $EditPregunta["IDIngresosPreguntas"] ?>" />
    <?php
    }
    ?>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pregunta', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <input type="text" id="Nombre" name="EtiquetaCampo" placeholder="<?= SIMUtil::get_traduccion('', '', 'EtiquetaCampo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Etiqueta Campo" value="<?php echo $EditPregunta["EtiquetaCampo"]; ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tipoderespuesta', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <select class="form-control TipoCampoIngresos" id="TipoCampo" name="TipoCampo">
                    <optgroup label="Estándar">
                        <option value="text" <?php if ($EditPregunta["TipoCampo"] == "text") echo "selected"; ?>>Texto en una línea</option>
                        <option value="textarea" <?php if ($EditPregunta["TipoCampo"] == "textarea") echo "selected"; ?>>Texto en párrafo</option>
                        <option value="radio" <?php if ($EditPregunta["TipoCampo"] == "radio") echo "selected"; ?>>Múltiples opciones</option>
                        <option value="checkbox" <?php if ($EditPregunta["TipoCampo"] == "checkbox") echo "selected"; ?>>Casillas de verificación</option>
                        <option value="select" <?php if ($EditPregunta["TipoCampo"] == "select") echo "selected"; ?>>Menú desplegable</option>
                        <option value="number" <?php if ($EditPregunta["TipoCampo"] == "number") echo "selected"; ?>>Número</option>
                        <!--<option value="page">Page Break</option>-->
                    </optgroup>
                    <optgroup label="Elegantes">
                        <option value="date" <?php if ($EditPregunta["TipoCampo"] == "date") echo "selected"; ?>>Fecha</option>
                        <option value="time" <?php if ($EditPregunta["TipoCampo"] == "time") echo "selected"; ?>>Hora</option>
                        <option value="email" <?php if ($EditPregunta["TipoCampo"] == "email") echo "selected"; ?>>Correo electrónico</option>
                        <option value="rating" <?php if ($EditPregunta["TipoCampo"] == "rating") echo "selected"; ?>>Estrella</option>
                        <option value="imagen" <?php if ($EditPregunta["TipoCampo"] == "imagen") echo "selected"; ?>>Imagen</option>
                        <option value="imagenarchivo" <?php if ($EditPregunta["TipoCampo"] == "imagenarchivo") echo "selected"; ?>>Imagen o Archivo</option>
                    </optgroup>
                    <optgroup label="Titulo">
                        <option value="titulo" <?php if ($EditPregunta["TipoCampo"] == "titulo") echo "selected"; ?>>Titulo</option>

                </select>
            </div>
        </div>

    </div>
    <div class="form-group first ContentOrigenDatos ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'origendedatos', LANGSESSION); ?> </label>
            <?php $OpcionesDatos = array("Tablas" => "Tablas Luker", "Manual" => "Manual"); ?>
            <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip($OpcionesDatos), $EditPregunta['OrigenDatos'], 'OrigenDatos', "class='input mandatory OrigenDatos'") ?></div>

        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6 opcionesrespuesta">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Opcionesderespuesta(separadosporcoma)opie(|)', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $EditPregunta["Valores"]; ?></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 selecttabla">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'vistasluker', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <select name="VistaLuker" id="VistaLuker" class="col-xs-12 mandatory" title="Vistas Luker">[]
                    <option value='vlk_clase_viv_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_clase_viv_atg') ? 'selected' : ''; ?>>Clase vivienda</option>
                    <option value='VLK_ESTCIV_ATG' <?= ($EditPregunta['VistaLuker'] == 'VLK_ESTCIV_ATG') ? 'selected' : ''; ?>>Estado Civil</option>
                    <option value='VLK_EST_NIVEL_ATG' <?= ($EditPregunta['VistaLuker'] == 'VLK_EST_NIVEL_ATG') ? 'selected' : ''; ?>>Nivel Estudios</option>
                    <option value='vlk_instituciones_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_instituciones_atg') ? 'selected' : ''; ?>>Instituciones</option>
                    <option value='vlk_loc_bogo_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_loc_bogo_atg') ? 'selected' : ''; ?>>Localidad Bogota</option>
                    <option value='vlk_nivacademico_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_nivacademico_atg') ? 'selected' : ''; ?>>Nivel Academico</option>
                    <option value='vlk_paises_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_paises_atg') ? 'selected' : ''; ?>>Paises</option>
                    <option value='vlk_depto_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_depto_atg') ? 'selected' : ''; ?>>Departamento</option>
                    <option value='vlk_ciudad_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_ciudad_atg') ? 'selected' : ''; ?>>Ciudad</option>
                    <option value='vlk_profesiones_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_profesiones_atg') ? 'selected' : ''; ?>>Profesiones</option>
                    <option value='vlk_relac_fam' <?= ($EditPregunta['VistaLuker'] == 'vlk_relac_fam') ? 'selected' : ''; ?>>Parentesco</option>
                    <option value='vlk_rol_fam_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_rol_fam_atg') ? 'selected' : ''; ?>>Rol Familiar</option>
                    <option value='vlk_tipo_viv_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_tipo_viv_atg') ? 'selected' : ''; ?>>Tipo Vivienda</option>
                    <option value='vlk_tip_doc_ident' <?= ($EditPregunta['VistaLuker'] == 'vlk_tip_doc_ident') ? 'selected' : ''; ?>>Tipo Documento Identidad</option>
                    <option value='vlk_zona_viv_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_zona_viv_atg') ? 'selected' : ''; ?>>Zona Vivienda</option>
                    <option value='vlk_deportes_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_deportes_atg') ? 'selected' : ''; ?>>Deportes</option>
                    <option value='vlk_idiomas_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_idiomas_atg') ? 'selected' : ''; ?>>Idiomas</option>
                    <option value='vlk_causa_retiro' <?= ($EditPregunta['VistaLuker'] == 'vlk_causa_retiro') ? 'selected' : ''; ?>>Causa Retiro</option>
                    <option value='vlk_eps_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_eps_atg') ? 'selected' : ''; ?>>EPS</option>
                    <option value='vlk_afp_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_afp_atg') ? 'selected' : ''; ?>>Fondo Pensiones</option>
                    <option value='vlk_fces_atg' <?= ($EditPregunta['VistaLuker'] == 'vlk_fces_atg') ? 'selected' : ''; ?>>Fondo cesantias</option>
                </select>
            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <input type="number" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Orden" value="<?php echo $EditPregunta["Orden"]; ?>">
            </div>
        </div>


    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?> </label>

            <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPregunta["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?></div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPregunta["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
            </div>

        </div>
    </div>
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $EditPregunta[$key] ?>" />
            <input type="hidden" name="IDIngresos" id="IDIngresos" value="<?php echo $frm[$key] ?>" />
            <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditPregunta[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
        </div>
    </div>
</form>
<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></th>
        <th><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?></th>
        <th align="center" valign="middle" width="64"><?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?></th>
    </tr>
    <tbody id="listacontactosanunciante">
        <?php

        $r_documento = &$dbo->all("IngresosPreguntas", "IDIngresos = '" . $frm[$key]  . "' ORDER BY Orden ASC");
        while ($r = $dbo->object($r_documento)) {
        ?>

            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                <td align="center" width="64">
                    <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDIngresosPreguntas=" . $r->IDIngresosPreguntas ?>&tabencuesta=formulario" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $r->EtiquetaCampo; ?></td>
                <td><?php echo $r->TipoCampo; ?></td>
                <td><?php echo $r->Obligatorio; ?></td>
                <td><?php echo $r->Orden; ?></td>
                <td><?php echo $r->Publicar; ?></td>
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaPregunta&id=<?php echo $r->IDIngresos; ?>&IDIngresosPreguntas=<? echo $r->IDIngresosPreguntas ?>&tabIngresosPreguntas=formulario"></a>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
    <tr>
        <th class="texto" colspan="16"></th>
    </tr>
</table>