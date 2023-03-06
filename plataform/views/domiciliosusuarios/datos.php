<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <input type="text" id="Numero" name="Numero" placeholder="<?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?>" value="<?php echo $frm["Numero"]; ?>" readonly>
            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
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

                <?php

                if ($_GET["action"] == "add") :
                    $frm["IDUsuario"] = SIMUser::get("IDUsuario");
                endif;


                $sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
                $qry_socio_club = $dbo->query($sql_socio_club);
                $r_socio = $dbo->fetchArray($qry_socio_club); ?>
                <!-- 	<?php echo $sql_socio_club . "<br>";
                            print_r($frm); ?> -->

                <input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Nombre"] ?>">
                <input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" class="mandatory" title="Usuario">
            </div>

        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <select name="IDEstadoDomicilio" id="IDEstadoDomicilio">
                    <option value=""></option>
                    <?php
                    $sql_estadodom_club = "Select * From EstadoDomicilio Where IDClub = '" . SIMUser::get("club") . "'";
                    $qry_estadodom_club = $dbo->query($sql_estadodom_club);
                    while ($r_estadodom = $dbo->fetchArray($qry_estadodom_club)) : ?>
                        <option value="<?php echo $r_estadodom["IDEstadoDomicilio"]; ?>" <?php if ($r_estadodom["IDEstadoDomicilio"] == $frm["IDEstadoDomicilio"]) echo "selected";  ?>><?php echo $r_estadodom["Nombre"]; ?></option>
                    <?php
                    endwhile;    ?>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fecha/HoraEntrega', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <input type="text" id="HoraEntrega" name="HoraEntrega" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraEntrega', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraEntrega', LANGSESSION); ?>" value="<?php echo $frm["HoraEntrega"]; ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?>>
            </div>
        </div>



    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Restaurante', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <select name="IDRestauranteDomicilio" id="IDRestauranteDomicilio" <?php if ($_GET['action'] == 'edit') echo "disabled" ?>>
                    <option value=""></option>
                    <?php
                    $sql_restaurante_club = "Select IDRestauranteDomicilio,Nombre From RestauranteDomicilio Where IDClub = '" . SIMUser::get("club") . "' AND Publicar='S'";
                    $qry_restaurante_club = $dbo->query($sql_restaurante_club);
                    while ($r_restaurante = $dbo->fetchArray($qry_restaurante_club)) : ?>
                        <option value="<?php echo $r_restaurante["IDRestauranteDomicilio"]; ?>" <?php if ($r_restaurante["IDRestauranteDomicilio"] == $frm["IDRestauranteDomicilio"]) echo "selected "; ?>><?php echo $r_restaurante["Nombre"]; ?></option>
                    <?php
                    endwhile;    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ComentarioSocio', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <textarea id="ComentariosSocio" name="ComentariosSocio" cols="10" rows="5" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'ComentarioSocio', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?>><?php echo $frm["ComentariosSocio"]; ?></textarea>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ComentarioClub', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <textarea id="ComentariosClub" name="ComentariosClub" cols="10" rows="5" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'ComentarioClub', LANGSESSION); ?>"><?php echo $frm["ComentariosClub"]; ?></textarea>

            </div>
        </div>

    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CelularSocio', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <input type="text" id="Celular" name="Celular" placeholder="<?= SIMUtil::get_traduccion('', '', 'CelularSocio', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'CelularSocio', LANGSESSION); ?>" value="<?php echo $frm["Celular"]; ?>" readonly>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <textarea id="Direccion" name="Direccion" cols="3" rows="2" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?>><?php echo $frm["Direccion"]; ?></textarea>
            </div>
        </div>



    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NotificaraSocio', LANGSESSION); ?>? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FormaPago', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <!--  <?php echo $frm["FormaPago"];
                        if ($frm["IDTipoPago"] > 0)
                            echo $dbo->getFields("TipoPago", "Nombre", "IDTipoPago = '" . $frm["IDTipoPago"] . "'");
                        echo "<br>" . $dbo->getFields("PagoCredibanco", "errorMessage", "NumeroFactura = '" . $frm["IDDomicilio"] . "'")

                        ?> -->

                <?php echo "forma pago" . $frm["FormaPago"];
                if ($frm["IDTipoPago"] > 0) {
                    echo  $dbo->getFields("TipoPago", "Nombre", "IDTipoPago = '" . $frm["IDTipoPago"] . "'");
                    echo "<br>" . $dbo->getFields("PagoCredibanco", "errorMessage", "NumeroFactura = '" . $frm["IDDomicilio"] . "'");
                }


                $datos_pagoCredibanco = $dbo->fetchAll("PagoCredibanco", " reserved12 = '" . $frm["IDDomicilio"] . "' ", "array");
                if ($datos_pagoCredibanco["Modulo"] == "Domicilio" && ($frm[IDClub] == 8 || $frm[IDClub] == 16)) {

                    $TipoCrediBanco = $datos_pagoCredibanco["xmlResponse"];
                    $data = json_decode($TipoCrediBanco, true);

                    echo "<br>";
                    echo  $data["paymentWay"];
                }

                //   echo "<br>" . "Error:" . $dbo->getFields("PagoCredibanco", "errorMessage", "NumeroFactura = '" . $frm["IDDomicilio"] . "'")

                ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mesa', LANGSESSION); ?>: </label>

            <div class="col-sm-8">
                <?php echo $frm["NumeroMesa"]; ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Otrosdatos', LANGSESSION); ?>: </label>

            <div class="col-sm-8">
                <?php
                //Si se solicita otros campos al momento de reservar muestro los valores
                $sql_otro_dato = "SELECT * From DomicilioCampo Where IDDomicilio = '" . $frm["IDDomicilio"] . "'";
                $result_otro_dato = $dbo->query($sql_otro_dato);
                while ($row_otro_dato = $dbo->fetchArray($result_otro_dato)) :
                ?>
                    <tr>
                        <td><?php echo $dbo->getFields("DomicilioPregunta", "Nombre", "IDDomicilioPregunta = '" . $row_otro_dato["IDDomicilioPregunta"] . "' AND Version='1'"); ?></td>
                        <td><?php echo $row_otro_dato["Valor"]; ?></td>
                    </tr>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Propina', LANGSESSION); ?>: </label>

            <div class="col-sm-8">
                <?php
                if ($frm["Propina"] == "S")
                    echo "Si";
                else
                    echo "No";
                ?>
            </div>
        </div>
    </div>






    <?php
    $IPImpresora = $dbo->getFields("Club", "IPImpresora", "IDClub = '" . $frm["IDClub"] . "'");
    if (empty(!$IPImpresora) && $newmode != "add") {
    ?>
        <div class="form-group first ">

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ImprimirPedido', LANGSESSION); ?> </label>

                <div class="col-sm-8">
                    <input type="button" name="btnimprimir" id="btnimprimir" value="Imprimir" onclick="window.location='domicilios.php?action=edit&opc=imprimir&id=<?php echo $frm["IDDomicilio"] ?>'">
                </div>
            </div>
        </div>
    <?php } ?>




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


        </div>
    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-glass green"></i>
            <?= SIMUtil::get_traduccion('', '', 'DetallePedido', LANGSESSION); ?>
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-12">


            <?php

            // Consulto los servicios disponibles al usuario

            $sql_detalle_pedido = $dbo->query("select * from DomicilioDetalle where IDDomicilio = '" . $frm[IDDomicilio] . "'");



            ?>


            <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>

                    <th><?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?></th>
                    <th><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></th>
                    <th><?= SIMUtil::get_traduccion('', '', 'Cantidad', LANGSESSION); ?></th>
                    <th><?= SIMUtil::get_traduccion('', '', 'Caracteristicas', LANGSESSION); ?></th>
                    <th><?= SIMUtil::get_traduccion('', '', 'Comentario', LANGSESSION); ?></th>
                    <th><?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?></th>
                    <th><?= SIMUtil::get_traduccion('', '', 'ValorUnitario', LANGSESSION); ?></th>
                    <th><?= SIMUtil::get_traduccion('', '', 'Total', LANGSESSION); ?></th>
                    <th><?= SIMUtil::get_traduccion('', '', 'CambiarEstado', LANGSESSION); ?></th>
                </tr>
                <tbody id="listacontactosanunciante">
                    <?php


                    while ($r_detalle_pedido = $dbo->object($sql_detalle_pedido)) {
                    ?>
                        <!--  saco el total de la compra para sacar la propina del club los lagartos -->
                        <?php
                        $totalcompra += $r_detalle_pedido->Total;

                        ?>

                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                            <td aling="center">
                                <?
                                $fotoprod = $dbo->getFields("Producto", "Foto1", "IDProducto = '" . $r_detalle_pedido->IDProducto . "'");
                                if (!empty($fotoprod)) {
                                    echo "<img src='" . IMGPRODUCTO_ROOT . $fotoprod . "' width='150' height='150' >";
                                } // END if
                                ?>
                            </td>
                            <td><?php
                                $datos_producto = $dbo->fetchAll("Producto", " IDProducto = '" . $r_detalle_pedido->IDProducto . "' ", "array");
                                echo $datos_producto["Nombre"];
                                if (!empty($datos_producto["Descripcion"]))
                                    echo "<br>" . $datos_producto["Descripcion"];
                                ?>
                            </td>
                            <td>
                                <?php echo number_format($r_detalle_pedido->Cantidad, 0, ",", "."); ?>
                            </td>
                            <td>
                                <?php
                                $sql_carac = "SELECT DC.*,PP.Nombre as Categoria, CP.Nombre as Caracteristica
										FROM DomicilioCaracteristica DC, CaracteristicaProducto CP, PropiedadProducto PP
										WHERE  DC.IDCaracteristicaProducto = CP.IDCaracteristicaProducto and PP.IDPropiedadProducto = DC.IDPropiedadProducto and
										IDDomicilio = '$frm[IDDomicilio]' and IDProducto = '$r_detalle_pedido->IDProducto'
                                        AND (DC.IDDomicilioDetalle = $r_detalle_pedido->IDDomicilioDetalle OR (DC.IDDomicilioDetalle = 0 AND DC.IDDomicilioCaracteristica < 15772))
										ORDER BY PP.Nombre";

                                $r_carac = $dbo->query($sql_carac);
                                while ($row_carac = $dbo->FetchArray($r_carac)) {
                                    echo $row_carac["Categoria"] . " : " . $row_carac["Caracteristica"] . "<br>";
                                }

                                ?>
                            </td>
                            <td>
                                <?php echo $r_detalle_pedido->Comentario; ?>
                            </td>
                            <td><?php
                                $datos_producto = $dbo->fetchAll("Producto", " IDProducto = '" . $r_detalle_pedido->IDProducto . "' ", "array");
                                echo $datos_producto["IDProductoExterno"];

                                ?>
                            </td>
                            <td>$
                                <?php echo number_format($r_detalle_pedido->ValorUnitario, 0, ",", "."); ?>
                            </td>
                            <td>$
                                <?php echo number_format($r_detalle_pedido->Total, 0, ",", "."); ?>
                            </td>
                            <td>
                                <?php echo SIMHTML::formPopUp("EstadoProducto", "Descripcion", "IDEstadoProducto", "IDEstadoProducto", $r_detalle_pedido->IDEstadoProducto, "[Seleccione el Estado]", "form-control CambiaEstadoProducto", "title = \"Estado Producto\"  producto = \"$r_detalle_pedido->IDProducto\" pedido = \"$frm[IDDomicilio]\" IDClub = \"$frm[IDClub]\" Version = \"1\" ") ?>
                                <div id="msgupdate<?php echo $r_detalle_pedido->IDProducto; ?>"></div>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <!--   Si el club es lagartos idclub=7 saco la propina del total comprado que es 10% -->
                <?php
                if (($frm[IDClub] == 8 || $frm[IDClub] == 7) && $frm["Propina"] == "S") { ?>
                    <tr>
                        <td align="center" colspan="13">Propina:

                            <?php
                            $propina = $totalcompra * 0.10;

                            echo number_format($propina, 0, ",", ".");

                            ?>
                        </td>


                    </tr>

                <?php  } ?>
                <tr>
                    <th class="texto" colspan="13"></th>
                </tr>
            </table>


        </div>




    </div>





</form>