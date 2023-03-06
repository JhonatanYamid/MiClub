<form class="form-horizontal formvalida" role="form" method="post" id="frmOrden" name="frmOrden" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <div class="form-group first ">
        <div class="col-sm-8">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Funcionario', LANGSESSION); ?> </label>
            <div class="col-sm-8">
                <!--
                                          <select name = "IDSocio" id="IDSocio" <?php if ($_GET["action"] != "add") echo "disabled"; ?>>
										    <option value=""></option>
										    <?php
                                            $sql_socio_club = "Select * From Usuario Where IDClub = '" . SIMUser::get("club") . "' Order by Nombre Asc";
                                            $qry_socio_club = $dbo->query($sql_socio_club);
                                            while ($r_socio = $dbo->fetchArray($qry_socio_club)) : ?>
										    <option value="<?php echo $r_socio["IDUsuario"]; ?>" <?php if ($r_socio["IDUsuario"] == $frm["IDUsuario"]) echo "selected";  ?>><?php echo $r_socio["Nombre"]; ?></option>
										    <?php
                                            endwhile;    ?>
									      </select>
                                          -->
                <!-- 
                <?php

                if ($_GET["action"] == "add") :
                    $frm["IDUsuarioCreacion"] = SIMUser::get("IDUsuario");
                endif;


                $sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuarioCreacion"] . "'";
                $qry_socio_club = $dbo->query($sql_socio_club);
                $r_socio = $dbo->fetchArray($qry_socio_club); ?> -->


                <!-- 
                <input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="Número de documento" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="número de documento" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Nombre"] ?>"> 
                <input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuarioCreacion"]; ?>" id="IDUsuario" class="mandatory" title="Usuario">-->

                <?php

                if ($_GET["action"] == "add") :
                    $frm["IDUsuario"] = SIMUser::get("IDUsuario");
                endif;


                $sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
                $qry_socio_club = $dbo->query($sql_socio_club);
                $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                <input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Nombre"] ?>">
                <input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>">

            </div>
        </div>

        <div class="form-group first">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Horadeentrega', LANGSESSION); ?></label>
                <div class="col-sm-8">
                    <input type="time" name="HoraEntrega" id="HoraEntrega" class="input" title="<?= SIMUtil::get_traduccion('', '', 'Horadeentrega', LANGSESSION); ?>" value="<?php echo $frm["HoraEntrega"] ?>">
                </div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?></label>
                <div class="col-sm-8">
                    <input type="text" id="FechaEntrega" name="FechaEntrega" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?> value=" <?php echo $frm["FechaEntrega"] ?>">
                </div>
            </div>
        </div>

        <div class="form-group first ">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ComentarioUsuario', LANGSESSION); ?></label>

                <div class="col-sm-8">
                    <textarea id="ComentariosSocio" name="ComentariosSocio" cols="10" rows="5" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'ComentarioUsuario', LANGSESSION); ?>"><?php echo $frm["ComentariosSocio"]; ?></textarea>
                </div>
            </div>
        </div>
        <div class="form-group first ">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CelularUsuario', LANGSESSION); ?></label>
                <div class="col-sm-8">
                    <input type="text" id="Celular" name="Celular" placeholder="<?= SIMUtil::get_traduccion('', '', 'CelularUsuario', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'CelularUsuario', LANGSESSION); ?>" value="<?php echo $frm["Celular"]; ?>">
                </div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?> </label>
                <div class="col-sm-8">
                    <textarea id="Direccion" name="Direccion" cols="3" rows="2" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?>"><?php echo $frm["Direccion"]; ?></textarea>
                </div>
            </div>
        </div>
        <div class="form-group first ">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mesa', LANGSESSION); ?>: </label>
                <div class="col-sm-8">
                    <input type="text" id="Mesa" name="Mesa" placeholder="<?= SIMUtil::get_traduccion('', '', 'Mesa', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Mesa', LANGSESSION); ?>" value="<?php echo $frm["Mesa"]; ?>">
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Restaurante', LANGSESSION); ?>: </label>
                <div class="col-sm-8">

                    <select name="IDRestauranteDomicilio" id="IDRestauranteDomicilio" class="form-control">
                        <option value=""><?= SIMUtil::get_traduccion('', '', 'SeleccionRestaurante', LANGSESSION); ?></option>
                        <?php
                        $sql_grupos = "Select * From RestauranteDomicilio2 Where IDClub = '" . SIMUser::get("club") . "'";
                        $result_grupos = $dbo->query($sql_grupos);
                        while ($row_grupos = $dbo->fetchArray($result_grupos)) : ?>
                            <option value="<?php echo $row_grupos["IDRestauranteDomicilio"]; ?>"><?php echo $row_grupos["Nombre"]; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>

        <?php
        $sql = "SELECT IDCategoriaProducto, Nombre FROM CategoriaProducto2 WHERE IDClub = '" . SIMUser::get("club") . "' ORDER BY Orden ASC";
        $query = $dbo->query($sql);
        while ($row = $dbo->fetchArray($query)) {
        ?>
            <div class="widget-header widget-header-large">
                <h3 class="widget-title grey lighter">
                    <i class="ace-icon fa fa-shopping-cart green"></i>
                    <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?> : <?php echo $row["Nombre"]; ?>
                </h3>
            </div>

            <div class="form-group first ">
                <?php

                $checkgroup = "";
                $checkgroup = "<table id='simple-table' class='table table-striped table-bordered table-hover'>
                                    <tr>
                                        <td>";
                $columnas = 0;

                $sqlProductos = "SELECT P.IDProducto, P.Nombre FROM Producto2 P, CategoriaProducto2 CP, ProductoCategoria PC WHERE CP.IDCategoriaProducto = " . $row['IDCategoriaProducto'] . " AND PC.IDCategoriaProducto = CP.IDCategoriaProducto AND PC.IDProducto = P.IDProducto";
                $queryProductos = $dbo->query($sqlProductos);

                /* echo $sqlProductos; */

                while ($rowProductos = $dbo->fetchArray($queryProductos)) {
                    $columnas++;
                    $checkgroup .= "<label class=\"checkgroup\">
                                        <input type=\"checkbox\" name=\"Producto[" . $rowProductos['IDProducto'] . "]\" id=\"Producto[" . $rowProductos['IDProducto'] . "]\" value=\"" . $rowProductos['IDProducto'] . "\" ";
                    $checkgroup .= "> " . $rowProductos['Nombre'];
                    $checkgroup .= "
                                    </label>" . "&nbsp;";

                    $checkgroup .= "<br><label class=\"checkgroup\">
                                            <input type=\"text\" name=\"Cantidad[" . $rowProductos['IDProducto'] . "]\" id=\"Cantidad[" . $rowProductos['IDProducto'] . "]\" placeholder=\"Cantidad\" > " . "
                                        </label>";
                    $checkgroup .= "<br><label class=\"checkgroup\">
                                            <input type=\"text\" name=\"Cometario[" . $rowProductos['IDProducto'] . "]\" id=\"Comentario[" . $rowProductos['IDProducto'] . "]\" placeholder=\"Comentario\" > " . "
                                        </label>";

                    $checkgroup .= "</td>";

                    if ($columnas == 4) :
                        $checkgroup .= "</tr><tr><td>";
                        $columnas = 0;
                    else :
                        $checkgroup .= "<td>";
                    endif;
                }

                $checkgroup .= "</tr></table>";
                echo $checkgroup;
                ?>
            </div>
        <?php
        }
        ?>
        <div class="clearfix form-actions">
            <div class="col-xs-12 text-center">
                <input type="hidden" name="action" id="action" value="CrearOrdenCompleta" />
                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                        else echo $frm["IDClub"];  ?>" />
                <button class="btn btn-info btnEnviar" type="button" rel="frmOrden">
                    <i class="ace-icon fa fa-check bigger-110"></i>
                    <?= SIMUtil::get_traduccion('', '', 'CrearOrden', LANGSESSION); ?>
                </button>
            </div>
        </div>

</form>