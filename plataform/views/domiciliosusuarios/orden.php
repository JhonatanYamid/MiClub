<?php
// Consulto los restaurantes disponibles del usuario
$sql_usuario_restaurante = $dbo->query("select * from UsuarioRestaurante where IDUsuario = '" . SIMUser::get("IDUsuario") . "'");

while ($r_usuario_restaurante = $dbo->object($sql_usuario_restaurante)) {
    $usuario_restaurante[] = $r_usuario_restaurante->IDRestauranteDomicilio;
}
if (count($usuario_restaurante) > 0) {
    $id_usuario_restaurante = implode(",", $usuario_restaurante);
    $condicionRestaurante = " AND  IDRestauranteDomicilio in(" . $id_usuario_restaurante . ") ";
}
?>
<form class="form-horizontal formvalida" role="form" method="post" id="frmOrden" name="frmOrden" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <div class="form-group first ">

        <div class="form-group first ">

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Funcionario </label>

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

                    <?php

                    if ($_GET["action"] == "add") :
                        $frm["IDUsuario"] = SIMUser::get("IDUsuario");
                    endif;


                    $sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
                    $qry_socio_club = $dbo->query($sql_socio_club);
                    $r_socio = $dbo->fetchArray($qry_socio_club); ?>
                    <!-- 	<?php echo $sql_socio_club . "<br>";
                                print_r($frm); ?> -->

                    <input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="Número de documento" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="número de documento" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Nombre"] ?>">
                    <input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" class="mandatory" title="Usuario">
                </div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Restaurante: </label>
                <div class="col-sm-8">

                    <select name="IDRestauranteDomicilio" id="IDRestauranteDomicilio" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Restaurante', LANGSESSION); ?>">
                        <option value="">Seleccion Restaurante</option>
                        <?php
                        $sql_grupos = "Select * From RestauranteDomicilio Where IDClub = '" . SIMUser::get("club") . "' $condicionRestaurante";
                        $result_grupos = $dbo->query($sql_grupos);
                        while ($row_grupos = $dbo->fetchArray($result_grupos)) : ?>
                            <option value="<?php echo $row_grupos["IDRestauranteDomicilio"]; ?>"><?php echo $row_grupos["Nombre"]; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group first">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?></label>
                <div class="col-sm-8">
                    <input type="text" id="FechaEntrega" name="FechaEntrega" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?> value=" <?php echo $frm["FechaEntrega"] ?>">
                </div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Horadeentrega', LANGSESSION); ?></label>
                <div class="col-sm-3">
                    <select name="HoraEntrega" id="HoraEntrega" class="form-control mandatory" title="HoraEntrega">
                        <option value=""></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group first ">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Comentario Usuario</label>

                <div class="col-sm-8">
                    <textarea id="ComentariosSocio" name="ComentariosSocio" cols="10" rows="5" class="col-xs-12" title="Comentarios Socio"><?php echo $frm["ComentariosSocio"]; ?></textarea>
                </div>
            </div>
        </div>
        <div class="form-group first ">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Celular Usuario</label>
                <div class="col-sm-8">
                    <input type="text" id="Celular" name="Celular" placeholder="Celular" class="col-xs-12" title="Celular" value="<?php echo $frm["Celular"]; ?>">
                </div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Direccion </label>
                <div class="col-sm-8">
                    <textarea id="Direccion" name="Direccion" cols="3" rows="2" class="col-xs-12" title="Direccion"><?php echo $frm["Direccion"]; ?></textarea>
                </div>
            </div>
        </div>
        <div class="form-group first ">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mesa: </label>
                <div class="col-sm-8">
                    <input type="text" id="Mesa" name="Mesa" placeholder="Mesa" class="col-xs-12" title="Mesa" value="<?php echo $frm["Mesa"]; ?>">
                </div>
            </div>

        </div>

        <?php
        $sql = "SELECT IDCategoriaProducto, Nombre FROM CategoriaProducto WHERE IDClub = '" . SIMUser::get("club") . "' $condicionRestaurante ORDER BY Orden ASC";
        $query = $dbo->query($sql);
        while ($row = $dbo->fetchArray($query)) {
        ?>
            <div class="widget-header widget-header-large">
                <h3 class="widget-title grey lighter">
                    <i class="ace-icon fa fa-shopping-cart green"></i>
                    Categoria : <?php echo $row["Nombre"]; ?>
                </h3>
            </div>

            <div class="form-group first ">
                <?php

                $checkgroup = "";
                $checkgroup = "<table id='simple-table' class='table table-striped table-bordered table-hover'>
                                    <tr>
                                        <td>";
                $columnas = 0;

                $sqlProductos = "SELECT P.IDProducto, P.Nombre FROM Producto P, CategoriaProducto CP, ProductoCategoria PC WHERE CP.IDCategoriaProducto = " . $row['IDCategoriaProducto'] . " AND PC.IDCategoriaProducto = CP.IDCategoriaProducto AND PC.IDProducto = P.IDProducto";
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
                <input type="hidden" name="Version" id="Version" value="1" />
                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                        else echo $frm["IDClub"];  ?>" />
                <button class="btn btn-info btnEnviar" type="button" rel="frmOrden">
                    <i class="ace-icon fa fa-check bigger-110"></i>
                    Crear Orden
                </button>
            </div>
        </div>

</form>