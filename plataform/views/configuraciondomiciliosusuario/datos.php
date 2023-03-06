<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>


<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

            <div class="col-sm-8">
                <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Aplica para el restaurante: </label>
            <div class="col-sm-8">
                <select name="IDRestauranteDomicilio" id="IDRestauranteDomicilio">
                    <option value="">Todos</option>
                    <?php
                    $sql_rest_club = string;
                    $sql_rest_club = "SELECT IDRestauranteDomicilio,Nombre FROM RestauranteDomicilio WHERE IDClub = '" . SIMUser::get("club") . "' and Publicar = 'S' and DirigidoA = 'E'  order by Nombre";
                    $qry_rest_club = $dbo->query($sql_rest_club);
                    while ($r_rest = $dbo->fetchArray($qry_rest_club)) : ?>
                        <option value="<?php echo $r_rest["IDRestauranteDomicilio"]; ?>" <?php if ($r_rest["IDRestauranteDomicilio"] == $frm["IDRestauranteDomicilio"]) echo "selected";  ?>><?php echo $r_rest["Nombre"]; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-12">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dias Domicilios</label>

            <div class="col-sm-8">
                <?php
                if (!empty($frm["Dias"])) :
                    $array_dias = explode("|", $frm["Dias"]);
                endif;
                array_pop($array_dias);
                foreach ($Dia_array as $id_dia => $dia) :  ?>
                    <input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if (in_array($id_dia, $array_dias) && $dia != "") echo "checked"; ?>><?php echo $dia; ?>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Inicio Entrega </label>

            <div class="col-sm-8">
                <input type="time" id="HoraInicioEntrega" name="HoraInicioEntrega" placeholder="Hora Inicio Entrega" class="col-xs-12 mandatory" title="Hora Inicio Entrega" value="<?php echo $frm["HoraInicioEntrega"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Fin Entrega </label>

            <div class="col-sm-8">
                <input type="time" id="HoraFinEntrega" name="HoraFinEntrega" placeholder="Hora Fin Entrega" class="col-xs-12 mandatory" title="Hora Fin Entrega" value="<?php echo $frm["HoraFinEntrega"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Inicio Operación </label>

            <div class="col-sm-8">
                <input type="time" id="HoraInicioDomicilios" name="HoraInicioDomicilios" placeholder="Hora Inicio Entrega" class="col-xs-12 mandatory" title="Hora Inicio Entrega" value="<?php echo $frm["HoraInicioDomicilios"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Fin Operación </label>

            <div class="col-sm-8">
                <input type="time" id="HoraFinDomilios" name="HoraFinDomilios" placeholder="Hora Fin Entrega" class="col-xs-12 mandatory" title="Hora Fin Entrega" value="<?php echo $frm["HoraFinDomilios"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo minimo para hecer pedido </label>

            <div class="col-sm-8">
                <input type="number" id="TiempoMinimoPedido" name="TiempoMinimoPedido" placeholder="Tiempo Minimo Pedido" class="col-xs-12 mandatory" title="Tiempo Minimo Pedido" value="<?php echo $frm["TiempoMinimoPedido"]; ?>">minutos
                <br>(Ejemplo: Si es 60 minutos y si el pedido se realiza a las 2pm lo podra recibir minimo a las 3pm)
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Intervalo Entrega Pedido: </label>

            <div class="col-sm-8">
                <input type="text" id="IntervaloEntrega" name="IntervaloEntrega" placeholder="Intervalo Entrega" class="col-xs-12 mandatory" title="Intervalo Entrega" value="<?php echo $frm["IntervaloEntrega"]; ?>">minutos
            </div>
        </div>


    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo minimo para cancelacion pedido </label>

            <div class="col-sm-8">
                <input type="number" id="TiempoMinimoCancelacion" name="TiempoMinimoCancelacion" placeholder="TiempoMinimoCancelacion" class="col-xs-12 mandatory" title="Tiempo Minimo Cancelacion" value="<?php echo $frm["TiempoMinimoCancelacion"]; ?>">minutos
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo Confirmacion Pedido </label>

            <div class="col-sm-8">
                <input type="number" id="TiempoConfirmacion" name="TiempoConfirmacion" placeholder="TiempoConfirmacion" class="col-xs-12 mandatory" title="Tiempo Confirmacion" value="<?php echo $frm["TiempoConfirmacion"]; ?>">minutos
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar Celular? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarCelular"], "SolicitarCelular", "title=\"Solicitar Celular\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Celular </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioCelular"], "ObligatorioCelular", "title=\"Obligatorio Celular\"") ?>
            </div>
        </div>


    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar Propina? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarPropina"], "SolicitarPropina", "title=\"Solicitar Propina\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Propina </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioPropina"], "ObligatorioPropina", "title=\"Obligatorio Propina\"") ?>
            </div>
        </div>


    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje Propina (sobre el total del pedido) </label>

            <div class="col-sm-8">
                <input type="number" id="PorcentajePropina" name="PorcentajePropina" placeholder="Porcentaje Propina" class="col-xs-12 " title="" value="<?php echo $frm["PorcentajePropina"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Propina </label>

            <div class="col-sm-8">
                <input type="text" id="LabelPropina" name="LabelPropina" placeholder="Label Propina" class="col-xs-12 " title="" value="<?php echo $frm["LabelPropina"]; ?>">
            </div>
        </div>


    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar Direccion? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarDireccion"], "SolicitarDireccion", "title=\"Solicitar Celular\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Direccion </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioDireccion"], "ObligatorioDireccion", "title=\"Obligatorio Direccion\"") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dirección predeterminada del Socio? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["DireccionSocio"], "DireccionSocio", "title=\"Direccion predeterminada\"") ?>
            </div>
        </div>

        <!-- <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar pedidos con estado entregado </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PedidosEntregados"], "PedidosEntregados", "title=\"Pedidos entregados\"") ?>
            </div>
        </div> -->
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar Mesa? </label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarMesa"], "SolicitarMesa", "title=\"Solicitar Mesa\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio Mesa </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioMesa"], "ObligatorioMesa", "title=\"Obligatorio Mesa\"") ?>
            </div>
        </div>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar Comentario? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarComentario"], "SolicitarComentario", "title=\"Solicitar Comentario\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio comentario? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioComentario"], "ObligatorioComentario", "title=\"Obligatorio Comentario\"") ?>
            </div>
        </div>

    </div>




    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cobro de Domicilio? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["CobroDomicilio"], "CobroDomicilio", "title=\"Cobro Domicilio\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Domicilio </label>

            <div class="col-sm-8">
                <input type="number" id="ValorDomicilio" name="ValorDomicilio" placeholder="Valor Domicilio" class="col-xs-12 " title="Valor Domicilio" value="<?php echo $frm["ValorDomicilio"]; ?>">
            </div>
        </div>

    </div>







    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cobrar domicilio cuanto total sea menor a $? </label>

            <div class="col-sm-8">
                <input type="number" id="CobroDomicilioMenorA" name="CobroDomicilioMenorA" placeholder="Cobro Domicilio Menor A" class="col-xs-12 " title="CobroDomicilioMenorA" value="<?php echo $frm["CobroDomicilioMenorA"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Domicilio </label>

            <div class="col-sm-8">
                <input type="text" id="TextoDomicilio" name="TextoDomicilio" placeholder="Texto Domicilio" class="col-xs-12 " title="TExto Domicilio" value="<?php echo $frm["TextoDomicilio"]; ?>">
            </div>
        </div>

    </div>
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">

        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Confirmación </label>

            <div class="col-sm-8">
                <textarea id="MensajeConfirmacion" name="MensajeConfirmacion" placeholder="Mensaje Confirmacion" class="col-xs-12 " title="Mensaje Confirmacion" value="<?php echo $frm["MensajeConfirmacion"]; ?>"><?php echo $frm["MensajeConfirmacion"]; ?></textarea>
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Notificacion </label>

            <div class="col-sm-8">
                <input type="text" id="EmailNotificacion" name="EmailNotificacion" placeholder="Email Notificacion" class="col-xs-12 " title="Email Notificacion" value="<?php echo $frm["EmailNotificacion"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedir Forma de pago en Domicilios: </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaFormaPagoDomicilio"], 'SolicitaFormaPagoDomicilio', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Formas de pago informativas permitidas(separada por coma) </label>

            <div class="col-sm-8">
                <input type="text" id="FormaPago" name="FormaPago" placeholder="Abono cuenta, Efectivo, Abono Libreta" class="col-xs-12 " title="FormaPago" value="<?php echo $frm["FormaPago"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pago: </label>
            <div class="col-sm-8">
                <?php
                $sql_tipo_pago_servicio = "Select * From DomicilioTipoPago Where IDConfiguracionDomicilio = '" . SIMNet::reqInt("id") . "'";
                $result_tipo_pago_servicio = $dbo->query($sql_tipo_pago_servicio);
                while ($row_tipo_pago_servicio = $dbo->fetchArray($result_tipo_pago_servicio)) :
                    $array_tipo_pago_servicio[] = $row_tipo_pago_servicio["IDTipoPago"];
                endwhile;
                $sql_tipo_pago = "Select * From TipoPago Where Publicar = 'S'";
                $result_tipo_pago = $dbo->query($sql_tipo_pago);
                while ($row_tipo_pago = $dbo->fetchArray($result_tipo_pago)) : ?>
                    <input type="checkbox" name="IDTipoPago[]" id="IDTipoPago" value="<?php echo $row_tipo_pago["IDTipoPago"]; ?>" <?php if (in_array($row_tipo_pago["IDTipoPago"], $array_tipo_pago_servicio)) echo "checked"; ?>><?php echo $row_tipo_pago["Nombre"]; ?><br>
                <?php endwhile; ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label descripcion domicilios (texto debajo del "Ver mis pedidos") </label>

            <div class="col-sm-8">
                <input type="text" id="LabelDomicilios" name="LabelDomicilios" placeholder="Label Domicilios" class="col-xs-12 " title="Label Domicilios" value="<?php echo $frm["LabelDomicilios"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar decimales en valor de productos? </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarDecimal"], "MostrarDecimal", "title=\"Mostrar Decimal\"") ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedir Fecha en Domicilios: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaFechaDomicilio"], 'SolicitaFechaDomicilio', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedir Hora Domicilio: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaHoraDomicilio"], 'SolicitaHoraDomicilio', "class='input '") ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedido minimo $? </label>

            <div class="col-sm-8">
                <input type="number" id="PedidoMinimo" name="PedidoMinimo" placeholder="Pedido Minimo" class="col-xs-12 " title="Pedido Minimo" value="<?php echo $frm["PedidoMinimo"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pemitir Pedido el mismo dia?: </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PedidoMismoDia"], 'PedidoMismoDia', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar Foto en lista de productos </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarFotoProducto"], 'MostrarFotoProducto', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar pantalla incial con recomendaciones restaurante? (por ejemplo normas de bioseguridad): </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarPantallaTexto"], 'MostrarPantallaTexto', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ip Impresora </label>

            <div class="col-sm-8">
                <input type="text" id="Ipimpresora" name="Ipimpresora" placeholder="Ip impresora" class="col-xs-12 " title="Ip impresora" value="<?php echo $frm["Ipimpresora"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Puerto impresora </label>

            <div class="col-sm-8">
                <input type="text" id="PuertoImpresora" name="PuertoImpresora" placeholder="Puerto Impresora" class="col-xs-12 " title="Puerto Impresora" value="<?php echo $frm["PuertoImpresora"]; ?>">
            </div>
        </div>

    </div>

    <div class="form-group first">
        Texto pantalla incial
        <div class="col-sm-12">
            <?php
            $oCuerpoQr = new FCKeditor("TextoPantalla");
            $oCuerpoQr->BasePath = "js/fckeditor/";
            $oCuerpoQr->Height = 200;
            //$oCuerpo->EnterMode = "p";
            $oCuerpoQr->Value =  $frm["TextoPantalla"];
            $oCuerpoQr->Create();
            ?>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar buscador de productos? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBuscadorProductos"], 'MostrarBuscadorProductos', "class='input mandatory'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>

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

            <input type="hidden" name="DirigidoA" value="E" id="DirigidoA" class="mandatory" title="DirigidoA">
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
            </button>


        </div>
    </div>

</form>