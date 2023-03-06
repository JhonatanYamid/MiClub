<form class="form-horizontal formvalida" role="form" method="post" id="EditPregunta<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <?php
    $action = "InsertarPregunta";
    if ($_GET[IDCampoHotel]) {
        $EditPregunta = $dbo->fetchAll("CampoHotelPasadia", " IDCampoHotel = '" . $_GET["IDCampoHotel"] . "' ", "array");
        $action = "ModificaPregunta";
    ?>
        <input type="hidden" name="IDCampoHotel" id="IDCampoHotel" value="<?php echo $EditPregunta[IDCampoHotel] ?>" />
    <?php
    }
    ?>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
            <div class="col-sm-8">
                <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $EditPregunta["Nombre"]; ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de respuesta </label>

            <div class="col-sm-8">
                <select class="form-control" id="Tipo" name="Tipo">
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
                    </optgroup>
                    <optgroup label="Titulo">
                        <option value="titulo" <?php if ($EditPregunta["TipoCampo"] == "titulo") echo "selected"; ?>>Titulo</option>

                </select>
            </div>
        </div>

    </div>





    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Opciones de respuesta (separados por coma) o pie (|)</label>

            <div class="col-sm-8">
                <textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $EditPregunta["Valores"]; ?></textarea>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Orden </label>

            <div class="col-sm-8">
                <input type="number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $EditPregunta["Orden"]; ?>">
            </div>
        </div>


    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio </label>

            <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $EditPregunta["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?></div>
        </div>
    </div>








    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $EditPregunta[$key] ?>" />

            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <input type="submit" class="submit" value="Guardar">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>" />
            <input type="hidden" name="IDTipoReservaHotel" id="IDTipoReservaHotel" value="<?php echo $_GET[id] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />


        </div>
    </div>




</form>










<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Obligatorio</th>
        <th>Orden</th>

        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante">
        <?php

        $r_documento = &$dbo->all("CampoHotelPasadia", "IDClub = '" . SIMUser::get("club")  . "'");

        while ($r = $dbo->object($r_documento)) {
        ?>

            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                <td align="center" width="64">
                    <a href="<?php echo $script . ".php" . "?action=edit&id=" . $frm[$key] . "&IDCampoHotel=" . $r->IDCampoHotel ?>&tabtiporeservahotel=messages" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $r->Nombre; ?></td>
                <td><?php echo $r->Tipo; ?></td>
                <td><?php echo $r->Obligatorio; ?></td>
                <td><?php echo $r->Orden; ?></td>

                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaPregunta&id=<?php echo $_GET[id]; ?>&IDCampoHotel=<? echo $r->IDCampoHotel ?>&tabtiporeservahotel=messages"></a>
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