<div class="widget-box transparent" id="recent-box"></div>
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clubes: </label>
                                    <div class="col-sm-8">
                                        <select name="IDListaClubes" id="IDListaClubes" class="form-control " title="IDListaClubes">
                                            <option value=""></option> <?php

                                            $sql_listaClubes = "Select IDListaClubes,Nombre From ListaClubes  Where  Publicar = 'S' Order by Nombre";
                                            $result_listaClubes = $dbo->query($sql_listaClubes);
                                            while ($row_listaClubes = $dbo->fetchArray($result_listaClubes)) :

                                            ?> <option value="<?php echo $row_listaClubes["IDListaClubes"] ?>"><?php echo  $row_listaClubes["Nombre"] ?></option> <?php endwhile; ?>
                                        </select>
                                        <br>
                                        <a id="agregar_club" href="#">Agregar</a> | <a id="borrar_club" href="#">Borrar</a>
                                        <br>
                                        <select name="ListaClubesCaddies[]" id="ListaClubesCaddies" class="col-xs-8" multiple> <?php
                                            $item = 1;

                                            $sql_listaClubesCaddies = "Select IDListaClubes From ClubesCaddies  Where  IDConfiguracionCaddies ='" . $_GET["id"] . "'  Order by IDClubesCaddies";
                                            $result_listaClubesCaddies = $dbo->query($sql_listaClubesCaddies);



                                            while ($row_listaClubesCaddies = $dbo->fetchArray($result_listaClubesCaddies)) :
                                                if (!empty($row_listaClubesCaddies["IDListaClubes"])) {
                                                    //$array_datos_invitados = explode("-", $datos_invitado);
                                                    $item--;
                                                    $IDListaClubes = $row_listaClubesCaddies["IDListaClubes"];
                                                    if ($IDListaClubes > 0) :
                                                        $nombre_clubes = utf8_encode($dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = '" . $IDListaClubes . "'"));
                                                        echo $nombre_clubes;
                                            ?> <option value="<?php echo  $row_listaClubesCaddies["IDListaClubes"]; ?>"><?php echo $nombre_clubes; ?></option> <?php
                                                    endif;
                                                }
                                            endwhile; ?> </select>
                                        <input type="hidden" name="IDListaClub" id="IDListaClub" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Cantidad Caddies Especial</label>
                                    <div class="col-sm-8"><input type="number" id="CantidadCaddiesEspecial" name="CantidadCaddiesEspecial" placeholder="" class="form-control mandatory" title="Cantidad Caddies Especial" value="<?php echo $frm["CantidadCaddiesEspecial"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto De Espera</label>
                                    <div class="col-sm-8"> <input type="text" id="TextoEspera" name="TextoEspera" placeholder="" class="form-control mandatory" title="TextoEspera" value="<?php echo $frm["TextoEspera"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Tiempo maximo de espera por parte del socio en minutos</label>
                                    <div class="col-sm-8"> <input type="text" id="TiempoEsperaSolicitud" name="TiempoEsperaSolicitud" placeholder="" class="form-control mandatory" title="TiempoEsperaSolicitud" value="<?php echo $frm["TiempoEsperaSolicitud"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Explicacion Seleccion Caddie</label>
                                    <div class="col-sm-8"><input type="text" id="LabelExplicacionSeleccionCaddie" name="LabelExplicacionSeleccionCaddie" placeholder="" class="form-control mandatory" title="Texto Explicacion Seleccion Caddie" value="<?php echo $frm["LabelExplicacionSeleccionCaddie"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Seleccion Servicio</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelSeleccionServicio" name="LabelSeleccionServicio" placeholder="" class="form-control mandatory" title="Texto Seleccion Servicio" value="<?php echo $frm["LabelSeleccionServicio"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Mis Reservas Caddies</label>
                                    <div class="col-sm-8"><input type="text" id="LabelMisReservasCaddies" name="LabelMisReservasCaddies" placeholder="" class="form-control mandatory" title="Texto Mis Reservas Caddies" value="<?php echo $frm["LabelMisReservasCaddies"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Encabezado Buscador Caddie</label>
                                    <div class="col-sm-8"> <input type="text" id="TextoHeaderBuscadorCaddie" name="TextoHeaderBuscadorCaddie" placeholder="" class="form-control mandatory" title="Texto Encabezado Buscador Caddie" value="<?php echo $frm["TextoHeaderBuscadorCaddie"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Carrito Compra</label>
                                    <div class="col-sm-8"><input type="text" id="LabelCarritoCompra" name="LabelCarritoCompra" placeholder="" class="form-control mandatory" title="Texto Carrito Compra" value="<?php echo $frm["LabelCarritoCompra"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Mi Disponibilidad Empleado</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelMiDisponibilidadEmpleado" name="LabelMiDisponibilidadEmpleado" placeholder="" class="form-control mandatory" title="Texto Mi Disponibilidad Empleado" value="<?php echo $frm["LabelMiDisponibilidadEmpleado"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Introduccion Agenda Caddie</label>
                                    <div class="col-sm-8"><input type="text" id="LabelIntroduccionAgendaCaddie" name="LabelIntroduccionAgendaCaddie" placeholder="" class="form-control mandatory" title="Texto Introduccion Agenda Caddie" value="<?php echo $frm["LabelIntroduccionAgendaCaddie"] ?>" required></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Mensaje Resultado Pago Completo</label>
                                    <div class="col-sm-8"> <input type="text" id="MensajeResultadoPagoCompleto" name="MensajeResultadoPagoCompleto" placeholder="" class="form-control mandatory" title="Mensaje Resultado Pago Completo" value="<?php echo $frm["MensajeResultadoPagoCompleto"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto para seleccionar la categoria del caddie a buscar</label>
                                    <div class="col-sm-8"><input type="text" id="LabelSeleccionCategoriaCaddie" name="LabelSeleccionCategoriaCaddie" placeholder="" class="form-control mandatory" title="Texto Introduccion Agenda Caddie" value="<?php echo $frm["LabelSeleccionCategoriaCaddie"] ?>"></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Resumen</label>
                                    <div class="col-sm-8"><input type="text" id="TextoResumen" name="TextoResumen" placeholder="" class="form-control mandatory" title="Texto Resumen" value="<?php echo $frm["TextoResumen"] ?>" required></div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Espera </label>
                                    <input name="ImagenEspera" id=ImagenEspera class="" title="Imagen Espera" type="file" size="25" style="font-size: 10px">
                                    <div class="col-sm-8">
                                        <? if (!empty($frm["ImagenEspera"])) {
                                            echo "<img src='" . CADDIE_ROOT . $frm["ImagenEspera"] . "' height='300px' width='300px' >";
                                        ?>
                                        <a href="<? echo $script . " .php?action=delfoto&foto=$frm[ImagenEspera]&campo=ImagenEspera&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                        <?
                                        } // END if
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Dias Disponibles Agendar Empleado:</label>
                                    <div class="col-sm-8"> <?php
                                        if (!empty($frm["DiasDisponiblesAgendarEmpleado"])) :
                                            $array_dias = explode(",", $frm["DiasDisponiblesAgendarEmpleado"]);
                                        endif;
                                        array_pop($array_dias);

                                        foreach ($Dia_array as $id_dia => $dia) :  ?> <input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if (in_array($id_dia, $array_dias) && $dia != "") echo "checked"; ?>><?php echo $dia; ?> <?php endforeach; ?> </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pago: </label>
                                    <div class="col-sm-8"> <?php
                                        $sql_tipo_pago_servicio = "Select IDTipoPago From ConfiguracionCaddiesTipoPago Where IDConfiguracionCaddies = '" . SIMNet::reqInt("id") . "'";

                                        $result_tipo_pago_servicio = $dbo->query($sql_tipo_pago_servicio);
                                        while ($row_tipo_pago_servicio = $dbo->fetchArray($result_tipo_pago_servicio)) :
                                            $array_tipo_pago_servicio[] = $row_tipo_pago_servicio["IDTipoPago"];

                                        endwhile;
                                        $sql_tipo_pago = "Select IDTipoPago,Nombre From TipoPago Where Publicar = 'S'";
                                        $result_tipo_pago = $dbo->query($sql_tipo_pago);
                                        while ($row_tipo_pago = $dbo->fetchArray($result_tipo_pago)) : ?> <input type="checkbox" name="IDTipoPago[]" id="IDTipoPago" value="<?php echo $row_tipo_pago["IDTipoPago"]; ?>" <?php if (in_array($row_tipo_pago["IDTipoPago"], $array_tipo_pago_servicio)) echo "checked"; ?>><?php echo $row_tipo_pago["Nombre"]; ?><br> <?php endwhile; ?> </div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Contactar por WhatsApp </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteContactoWhatsapp"], 'PermiteContactoWhatsapp', "class='input'") ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto boton contacto por WhatsApp </label>
                                    <div class="col-sm-8">
                                        <input type="text" id="LabelBotonWhatsapp" name="LabelBotonWhatsapp" placeholder="" class="form-control " title="LabelBotonWhatsapp" value="<?php echo $frm["LabelBotonWhatsapp"] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Seleccionar Club </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteSeleccionarClub"], 'PermiteSeleccionarClub', "class='input mandatory'") ?>
                                </div>
                                <!--Nuevas opciones para e-caddies -->
                                
                                   <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pagar Primero E-caddie </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermitePagarPrimeroCaddie"], 'PermitePagarPrimeroCaddie', "class='input'") ?>
                                </div>
                                
                                 <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Si primero se debe pagar el caddie digite el valor a cobrar</label>
                                    <div class="col-sm-8"> <input type="number" id="ValorGeneral" name="ValorGeneral" placeholder="" class="form-control  " title="TiempoEsperaSolicitud" value="<?php echo $frm["ValorGeneral"] ?>" required></div>
                                </div>
                                
                                
                                
                                   <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Caddie Especifico </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarCaddieEspecifico"], 'OcultarCaddieEspecifico', "class='input'") ?>
                                </div>
                                
                                
                                   <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Turbo Caddie </label>
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarTurboCaddie"], 'OcultarTurboCaddie', "class='input'") ?>
                                </div>
                                
                                <!-- fin -->
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>
                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Publicar"], 'Publicar', "class='input'") ?>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i> <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?> </button>
                                        <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->
<?
include("cmp/footer_scripts.php");
?>
