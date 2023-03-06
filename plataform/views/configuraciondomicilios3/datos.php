<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
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
                    <!-- PAGE CONTENT BEGINS -->


                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>
                                </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder=" <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Aplicaparaelrestaurante', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <select name="IDRestauranteDomicilio" id="IDRestauranteDomicilio">
                                        <option value="">Todos</option>
                                        <?php
                                        $sql_rest_club = string;
                                        $sql_rest_club = "SELECT IDRestauranteDomicilio,Nombre FROM RestauranteDomicilio3 WHERE IDClub = '" . SIMUser::get("club") . "' and Publicar = 'S' order by Nombre";
                                        $qry_rest_club = $dbo->query($sql_rest_club);
                                        while ($r_rest = $dbo->fetchArray($qry_rest_club)) : ?>
                                            <option value="<?php echo $r_rest["IDRestauranteDomicilio"]; ?>" <?php if ($r_rest["IDRestauranteDomicilio"] == $frm["IDRestauranteDomicilio"]) echo "selected";  ?>>
                                                <?php echo $r_rest["Nombre"]; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>





                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-12">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DiasDomicilios', LANGSESSION); ?></label>

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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'HoraInicioEntrega', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="time" id="HoraInicioEntrega" name="HoraInicioEntrega" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraInicioEntrega', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraInicioEntrega', LANGSESSION); ?>" value="<?php echo $frm["HoraInicioEntrega"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'HoraFinEntrega', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="time" id="HoraFinEntrega" name="HoraFinEntrega" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraFinEntrega', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraFinEntrega', LANGSESSION); ?>" value="<?php echo $frm["HoraFinEntrega"]; ?>">
                                </div>
                            </div>



                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'HoraInicioOperación', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="time" id="HoraInicioDomicilios" name="HoraInicioDomicilios" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraInicioEntrega', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraInicioEntrega', LANGSESSION); ?>" value="<?php echo $frm["HoraInicioDomicilios"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'HoraFinOperación', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="time" id="HoraFinDomilios" name="HoraFinDomilios" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraFinEntrega', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraFinEntrega', LANGSESSION); ?>" value="<?php echo $frm["HoraFinDomilios"]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tiempominimoparahacerpedido', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="TiempoMinimoPedido" name="TiempoMinimoPedido" placeholder="<?= SIMUtil::get_traduccion('', '', 'TiempoMinimoPedido', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TiempoMinimoPedido', LANGSESSION); ?>" value="<?php echo $frm["TiempoMinimoPedido"]; ?>"><?= SIMUtil::get_traduccion('', '', 'minutos', LANGSESSION); ?>
                                    <br><?= SIMUtil::get_traduccion('', '', '(EjemploSies60minutosysielpedidoserealizaalas2pmlopodrarecibirminimoalas3pm)', LANGSESSION); ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IntervaloEntregaPedido', LANGSESSION); ?>: </label>

                                <div class="col-sm-8">
                                    <input type="text" id="IntervaloEntrega" name="IntervaloEntrega" placeholder="<?= SIMUtil::get_traduccion('', '', 'IntervaloEntrega', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'IntervaloEntrega', LANGSESSION); ?>" value="<?php echo $frm["IntervaloEntrega"]; ?>"><?= SIMUtil::get_traduccion('', '', 'minutos', LANGSESSION); ?>
                                </div>
                            </div>


                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tiempominimoparacancelacionpedido', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="TiempoMinimoCancelacion" name="TiempoMinimoCancelacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'Tiempominimoparacancelacionpedido', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Tiempominimoparacancelacionpedido', LANGSESSION); ?>" value="<?php echo $frm["TiempoMinimoCancelacion"]; ?>"><?= SIMUtil::get_traduccion('', '', 'minutos', LANGSESSION); ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TiempoConfirmacionPedido', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="TiempoConfirmacion" name="TiempoConfirmacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'TiempoConfirmacionPedido', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TiempoConfirmacionPedido', LANGSESSION); ?>" value="<?php echo $frm["TiempoConfirmacion"]; ?>"><?= SIMUtil::get_traduccion('', '', 'minutos', LANGSESSION); ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tiempominimoparacancelacionpedidodespues?(nosevalidatiempoantesdelahoradeentrega)', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(SIMResources::$sinoNum, $frm["TiempoMinimoCancelacionDespues"], "TiempoMinimoCancelacionDespues", "title=\"TiempoMinimoCancelacionDespues\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'SolicitarCelular', LANGSESSION); ?>? </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarCelular"], "SolicitarCelular", "title=\"Solicitar Celular\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ObligatorioCelular', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioCelular"], "ObligatorioCelular", "title=\"Obligatorio Celular\"") ?>
                                </div>
                            </div>


                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SolicitarPropina', LANGSESSION); ?>? </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarPropina"], "SolicitarPropina", "title=\"Solicitar Propina\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ObligatorioPropina', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioPropina"], "ObligatorioPropina", "title=\"Obligatorio Propina\"") ?>
                                </div>
                            </div>


                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PorcentajePropina(sobreeltotaldelpedido)', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="PorcentajePropina" name="PorcentajePropina" placeholder="<?= SIMUtil::get_traduccion('', '', 'PorcentajePropina', LANGSESSION); ?>" class="col-xs-12 " title="" value="<?php echo $frm["PorcentajePropina"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoPropina', LANGSESSION); ?>
                                </label>

                                <div class="col-sm-8">
                                    <input type="text" id="LabelPropina" name="LabelPropina" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoPropina', LANGSESSION); ?>" class="col-xs-12 " title="" value="<?php echo $frm["LabelPropina"]; ?>">
                                </div>
                            </div>
                        </div>


                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SolicitarDireccion', LANGSESSION); ?>? </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarDireccion"], "SolicitarDireccion", "title=\"Solicitar Celular\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ObligatorioDireccion', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioDireccion"], "ObligatorioDireccion", "title=\"Obligatorio Direccion\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DirecciónpredeterminadadelSocio', LANGSESSION); ?>? </label>

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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SolicitarMesa', LANGSESSION); ?>? </label>
                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarMesa"], "SolicitarMesa", "title=\"Solicitar Mesa\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ObligatorioMesa', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioMesa"], "ObligatorioMesa", "title=\"Obligatorio Mesa\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SolicitarComentario', LANGSESSION); ?>? </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SolicitarComentario"], "SolicitarComentario", "title=\"Solicitar Comentario\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Obligatoriocomentario', LANGSESSION); ?>? </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioComentario"], "ObligatorioComentario", "title=\"Obligatorio Comentario\"") ?>
                                </div>
                            </div>

                        </div>




                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CobrodeDomicilio', LANGSESSION); ?>? </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["CobroDomicilio"], "CobroDomicilio", "title=\"Cobro Domicilio\"") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ValorDomicilio', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="ValorDomicilio" name="ValorDomicilio" placeholder="<?= SIMUtil::get_traduccion('', '', 'ValorDomicilio', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'ValorDomicilio', LANGSESSION); ?>" value="<?php echo $frm["ValorDomicilio"]; ?>">
                                </div>
                            </div>

                        </div>





                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Cobrardomiciliocuantototalseamenora$?', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="CobroDomicilioMenorA" name="CobroDomicilioMenorA" placeholder="<?= SIMUtil::get_traduccion('', '', 'Cobrardomiciliocuantototalseamenora$?', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Cobrardomiciliocuantototalseamenora$?', LANGSESSION); ?>" value="<?php echo $frm["CobroDomicilioMenorA"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoDomicilio', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="TextoDomicilio" name="TextoDomicilio" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoDomicilio', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'TextoDomicilio', LANGSESSION); ?>" value="<?php echo $frm["TextoDomicilio"]; ?>">
                                </div>
                            </div>

                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">

                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MensajeConfirmación', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <textarea id="MensajeConfirmacion" name="MensajeConfirmacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'MensajeConfirmación', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'MensajeConfirmación', LANGSESSION); ?>" value="<?php echo $frm["MensajeConfirmacion"]; ?>"><?php echo $frm["MensajeConfirmacion"]; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'EmailNotificacion', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="EmailNotificacion" name="EmailNotificacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'EmailNotificacion', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'EmailNotificacion', LANGSESSION); ?>" value="<?php echo $frm["EmailNotificacion"]; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PedirFormadepagoenDomicilios', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaFormaPagoDomicilio"], 'SolicitaFormaPagoDomicilio', "class='input '") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Formasdepagoinformativaspermitidas(separadaporcoma)', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="FormaPago" name="FormaPago" placeholder="<?= SIMUtil::get_traduccion('', '', 'Abonocuenta,Efectivo,AbonoLibreta', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Formasdepagoinformativaspermitidas(separadaporcoma)', LANGSESSION); ?>" value="<?php echo $frm["FormaPago"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pago', LANGSESSION); ?>:
                                </label>
                                <div class="col-sm-8">
                                    <?php
                                    $sql_tipo_pago_servicio = "Select * From DomicilioTipoPago3 Where IDConfiguracionDomicilio = '" . SIMNet::reqInt("id") . "'";
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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textodescripciondomicilios(textodebajodel"Vermispedidos")', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="LabelDomicilios" name="LabelDomicilios" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoDomicilio', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'TextoDomicilio', LANGSESSION); ?>" value="<?php echo $frm["LabelDomicilios"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrardecimalesenvalordeproductos', LANGSESSION); ?>? </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["MostrarDecimal"], "MostrarDecimal", "title=\"Mostrar Decimal\"") ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PedirFechaenDomicilios', LANGSESSION); ?>: </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaFechaDomicilio"], 'SolicitaFechaDomicilio', "class='input '") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PedirHoraDomicilio', LANGSESSION); ?>: </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaHoraDomicilio"], 'SolicitaHoraDomicilio', "class='input '") ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pedidominimo', LANGSESSION); ?> $? </label>

                                <div class="col-sm-8">
                                    <input type="number" id="PedidoMinimo" name="PedidoMinimo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Pedidominimo', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Pedidominimo', LANGSESSION); ?>" value="<?php echo $frm["PedidoMinimo"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermitirPedidoelmismodia', LANGSESSION); ?>?: </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PedidoMismoDia"], 'PedidoMismoDia', "class='input '") ?>
                                </div>
                            </div>



                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarFotoenlistadeproductos', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarFotoProducto"], 'MostrarFotoProducto', "class='input '") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarpantallainicialconrecomendacionesrestaurante?(porejemplonormasdebioseguridad)', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarPantallaTexto"], 'MostrarPantallaTexto', "class='input '") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IpImpresora', LANGSESSION); ?>
                                </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Ipimpresora" name="Ipimpresora" placeholder="<?= SIMUtil::get_traduccion('', '', 'IpImpresora', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'IpImpresora', LANGSESSION); ?>" value="<?php echo $frm["Ipimpresora"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Puertoimpresora', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="PuertoImpresora" name="PuertoImpresora" placeholder="<?= SIMUtil::get_traduccion('', '', 'Puertoimpresora', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Puertoimpresora', LANGSESSION); ?>" value="<?php echo $frm["PuertoImpresora"]; ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first">
                            <?= SIMUtil::get_traduccion('', '', 'Textopantallaincial', LANGSESSION); ?>
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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Mostrarbuscadordeproductos', LANGSESSION); ?>? </label>
                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBuscadorProductos"], 'MostrarBuscadorProductos', "class='input mandatory'") ?>
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Configuraciónpara', LANGSESSION); ?>: </label>

                                <div class="col-sm-8">
                                    <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cantidad de pedidos en un tiempo esablecido </label>

                                <div class="col-sm-8">
                                    <input type="text" id="NumeroPedidosEnTiempo" name="NumeroPedidosEnTiempo" placeholder="" class="col-xs-12 " title="" value="<?php echo $frm["NumeroPedidosEnTiempo"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo establecido para la cantidad de pedidos limite en minutos </label>

                                <div class="col-sm-8">
                                    <input type="text" id="TiempoValidoParaCantidadPedidos" name="TiempoValidoParaCantidadPedidos" placeholder="" class="col-xs-12 " title="" value="<?php echo $frm["TiempoValidoParaCantidadPedidos"]; ?>">
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Maximodeunidadesporpedido(ej:3solosepermite3delmismoproductoporpedido)', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="CantidadMaximaProducto" name="CantidadMaximaProducto" placeholder="" class="col-xs-12 " title="" value="<?php echo $frm["CantidadMaximaProducto"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Requerir QR Para Ver Menú </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["RequerirQRParaVerMenu"], 'RequerirQRParaVerMenu', "class='input mandatory'") ?>
                                </div>
                            </div>
                        </div>


                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Requerir QR </label>

                                <div class="col-sm-8">
                                    <input type="text" id="TextoRequerirQR" name="TextoRequerirQR" placeholder="" class="col-xs-12 " title="" value="<?php echo $frm["TextoRequerirQR"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Texto Botón Escanear QR</label>

                                <div class="col-sm-8">
                                    <input type="text" id="BotonEscanearQR" name="BotonEscanearQR" placeholder="" class="col-xs-12 " title="" value="<?php echo $frm["BotonEscanearQR"]; ?>">
                                </div>
                            </div>
                        </div>


                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>

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


                            </div>
                        </div>

                    </form>
                </div>
            </div>




        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->