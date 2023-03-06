<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor
$permite_auxiliares = $dbo->getFields("ServicioMaestro", "PermiteAuxiliar", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
if (empty($permite_auxiliares)) {
    $IDServIni = $dbo->getFields("Servicio", "IDServicioInicial", "IDServicio = '" . $_GET["ids"] . "'");
    if ($IDServIni == 6) {
        $permite_auxiliares = "S";
    }
}


?>
<?
include("cmp/footer_scripts.php");
?>

<div class="widget-box transparent" id="recent-box">
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="accordion" class="accordion-style1 panel-group">
                                <?php
                                //para los corrdinadores que solo puedan ver fechas de cierre
                                if (SIMUser::get("IDPerfil") != 31) {
                                    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoGeneral");
                                    if ($Permiso == 1) :
                                ?>

                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed " data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                                        <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                        &nbsp;General
                                                    </a>
                                                </h4>
                                            </div>

                                            <div class="panel-collapse collapse <?php if ($_GET["tab"] == "configuracion") echo "in"; ?>" id="collapseOne">
                                                <div class="panel-body">
                                                    <?php include("configuracion.php"); ?>
                                                </div>
                                            </div>
                                        </div>



                                    <?php endif; ?>

                                    <?php
                                    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoDisponibilidad");
                                    if ($Permiso == 1) :
                                    ?>

                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                                        <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                        &nbsp;Disponibilidad
                                                    </a>
                                                </h4>
                                            </div>

                                            <div class="panel-collapse collapse <?php if ($_GET["tab"] == "disponibilidad") echo "in"; ?>" id="collapseTwo">
                                                <div class="panel-body">
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <th align="center" valign="middle" width="64">Editar</th>
                                                            <th>Nombre</th>
                                                            <th>Activa</th>
                                                            <th align="center" valign="middle" width="64">Eliminar</th>
                                                            <th align="center" valign="middle" width="64">Copiar</th>
                                                        </tr>
                                                        <tbody id="listacontactosanunciante">
                                                            <?php

                                                            $r_dispo = &$dbo->all("Disponibilidad", "IDServicio = '" . $_GET["ids"]  . "'");

                                                            while ($r = $dbo->object($r_dispo)) {
                                                                $findme   = 'OPCION 2';
                                                                $pos = strpos($r->Nombre, $findme);
                                                                if ($pos === false) {
                                                                    $color_fondo = "";
                                                                } else {
                                                                    $color_fondo = "#59b21b";
                                                                }
                                                            ?>

                                                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                                                                    <td align="center" width="64">
                                                                        <a class="ace-icon glyphicon glyphicon-pencil fancybox" href="disponibilidad_general.php?ids=<?= $_GET[ids] ?>&IDDisponibilidad=<?= $r->IDDisponibilidad ?>" data-fancybox-type="iframe"></a>


                                                                    <td style="color:<?php echo $color_fondo; ?>"><?php echo $r->Nombre; ?> </td>
                                                                    <td style="color:<?php echo $color_fondo; ?>"><?php
                                                                                                                    if ($r->Activo == "S") :
                                                                                                                    //echo "Activa";
                                                                                                                    else :
                                                                                                                    //echo "Desactivada";
                                                                                                                    endif;
                                                                                                                    ?>

                                                                        <?php echo SIMHTML::formPopupArray($opciones = array("S" => "S", "N" => "N"),  $r->Activo, "ActivaSN_" . $r->IDDisponibilidad,  "", "form-control ActivaSNDisponibilidad"); ?>
                                                                        <div name='msgupdate<?php echo $r->IDDisponibilidad; ?>' id='msgupdate<?php echo $r->IDDisponibilidad; ?>'></div>

                                                                    </td>
                                                                    <td align="center" width="64">
                                                                        <a href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaServicioDisponibilidad&ids=<?php echo $frm[$key]; ?>&IDDisponibilidad=<? echo $r->IDDisponibilidad ?>&tab=disponibilidad" class="ace-icon glyphicon glyphicon-remove"></a>
                                                                    </td>

                                                                    <td align="center" width="64">
                                                                        <a href="?mod=<?php echo SIMReg::get("mod") ?>&action=CopiarServicioDisponibilidad&ids=<?php echo $frm[$key]; ?>&IDDisponibilidad=<? echo $r->IDDisponibilidad ?>&tab=disponibilidad" class="ace-icon glyphicon glyphicon-floppy-disk"></a>
                                                                    </td>

                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                        <tr>
                                                            <th class="texto" colspan="14"></th>
                                                        </tr>
                                                    </table>


                                                    <a class="btn btn-primary btn-sm btnModal fancybox" href="disponibilidad_general.php?ids=<?= $_GET[ids] ?>" data-fancybox-type="iframe">
                                                        Crear Disponiblidad
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <?php
                                    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoElementos");
                                    if ($Permiso == 1) :
                                    ?>

                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                                        <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                        &nbsp;Elementos
                                                    </a>
                                                </h4>
                                            </div>

                                            <div class="panel-collapse collapse <?php if ($_GET["tab"] == "elementos") echo "in"; ?>" id="collapseThree">
                                                <div class="panel-body">
                                                    <?php include("elementos.php"); ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <?php
                                    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoTipoReserva");
                                    if ($Permiso == 1) :
                                    ?>
                                        <?php
                                        $permite_tiporeserva = $dbo->getFields("ServicioMaestro", "PermiteTipoReserva", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                                        if ($permite_tiporeserva == "S") :
                                        ?>
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
                                                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                            &nbsp;Tipo Reservas
                                                        </a>
                                                    </h4>
                                                </div>

                                                <div class="panel-collapse collapse <?php if ($_GET["tab"] == "tiporeservas") echo "in"; ?>" id="collapseSix">
                                                    <div class="panel-body">
                                                        <?php include("tiporeserva.php"); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php
                                    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoPreguntas");
                                    if ($Permiso == 1) :
                                    ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseseven">
                                                        <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                        &nbsp;Preguntas reserva
                                                    </a>
                                                </h4>
                                            </div>

                                            <div class="panel-collapse collapse <?php if ($_GET["tab"] == "preguntas") echo "in"; ?>" id="collapseseven">
                                                <div class="panel-body">
                                                    Preguntas que apareceran cuando se este realizando la reserva
                                                    <?php include("preguntareserva.php"); ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                <? } ?>

                                <?php
                                $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoAuxiliares");
                                if ($Permiso == 1) :
                                ?>
                                    <?php
                                    if ($permite_auxiliares == "S") :
                                    ?>

                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
                                                        <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                        &nbsp;Auxiliares (Boleadores)
                                                    </a>
                                                </h4>
                                            </div>

                                            <div class="panel-collapse collapse <?php if ($_GET["tab"] == "auxiliares") echo "in"; ?>" id="collapseFive">
                                                <div class="panel-body">
                                                    <?php include("auxiliares.php"); ?>
                                                    <a class="btn btn-primary btn-sm btnModal fancybox" href="disponibilidad_auxiliar.php?ids=<?= $_GET[ids] ?>" data-fancybox-type="iframe">
                                                        Crear Disponiblidad Auxiliares / Boleadores
                                                    </a>
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <th align="center" valign="middle" width="64">Editar</th>
                                                            <th>Nombre</th>
                                                            <th>Activo</th>
                                                            <th>Orden</th>
                                                            <th align="center" valign="middle" width="64">Eliminar</th>
                                                        </tr>
                                                        <tbody id="listacontactosanunciante">
                                                            <?php

                                                            $r_dispo = &$dbo->all("AuxiliarDisponibilidad", "IDServicio = '" . $_GET["ids"]  . "' ORDER by Orden");


                                                            while ($r = $dbo->object($r_dispo)) {

                                                                if (((int)$r->Orden % 2) == 0)
                                                                    $color_fila = "fff";
                                                                else
                                                                    $color_fila = "e1fede";
                                                            ?>

                                                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>" style="background-color:#<?php echo $color_fila; ?>">
                                                                    <td align="center" width="64">
                                                                        <a class="ace-icon glyphicon glyphicon-pencil fancybox" href="disponibilidad_auxiliar.php?ids=<?= $_GET[ids] ?>&IDAuxiliarDisponibilidad=<?= $r->IDAuxiliarDisponibilidad ?>" data-fancybox-type="iframe"></a>

                                                                    <td><?php echo $r->Nombre; ?></td>
                                                                    <td><?php
                                                                        if ($r->Activo == "S") :
                                                                            echo "Activa";
                                                                        else :
                                                                            echo "Desactivada";
                                                                        endif;
                                                                        ?></td>
                                                                    <td><?php echo $r->Orden; ?></td>
                                                                    <td align="center" width="64">
                                                                        <a href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaAuxiliarDisponibilidad&ids=<?php echo $frm[$key]; ?>&IDAuxiliarDisponibilidad=<? echo $r->IDAuxiliarDisponibilidad ?>&tab=auxiliares" class="ace-icon glyphicon glyphicon-remove"></a>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                                $orden_anterior = $r->Orden;
                                                            }
                                                            ?>
                                                        </tbody>
                                                        <tr>
                                                            <th class="texto" colspan="14"></th>
                                                        </tr>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive1">
                                                        <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                        &nbsp;Fechas de Cierre Auxiliares (Boleadores)
                                                    </a>
                                                </h4>
                                            </div>

                                            <div class="panel-collapse collapse <?php if ($_GET["tab"] == "fechascierreauxiliares") echo "in"; ?>" id="collapseFive1">
                                                <div class="panel-body">
                                                    <?php include("fechascierreauxiliares.php"); ?>
                                                </div>

                                            </div>
                                        </div>

                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php
                                $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoFechasCierre");
                                if ($Permiso == 1) :
                                ?>

                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsefour">
                                                    <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;Fechas de Cierre
                                                </a>
                                            </h4>
                                        </div>

                                        <div class="panel-collapse collapse <?php if ($_GET["tab"] == "fechas") echo "in"; ?>" id="collapsefour">
                                            <div class="panel-body">
                                                <?php include("fechascierreservicio.php"); ?>
                                            </div>
                                        </div>
                                    </div>

                                <?php endif; ?>

                                <?php $permiteReserva = $dbo->getFields("Servicio", "permisoReserva", "IDServicio = '" . $_GET["ids"] . "'");
                                if ($permiteReserva == 'S') {
                                ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#permisos">
                                                    <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;Permisos de Reservar
                                                </a>
                                            </h4>
                                        </div>
                                        <div class="panel-collapse collapse <?php if ($_GET["tab"] == "permisos") echo "in"; ?>" id="permisos">
                                            <div class="panel-body">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#permiteIndividual">
                                                                <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                                &nbsp;Carga Individual
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse <?php if ($_GET["tab1"] == "individual") echo "in"; ?>" id="permiteIndividual">
                                                        <div class="panel-body">
                                                            <?php include("cargaIndividual.php"); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#permiteReserva">
                                                                <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                                &nbsp;Carga Lote
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse <?php if ($_GET["tab"] == "permiteReservar") echo "in"; ?>" id="permiteReserva">
                                                        <div class="panel-body">
                                                            <?php include("permiteReservar.php"); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#lista">
                                                                <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                                &nbsp;Lista
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse <?php if ($_GET["tab1"] == "lista") echo "in"; ?>" id="lista">
                                                        <div class="panel-body">
                                                            <?php
                                                            include("listaPermite.php");
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                $preciosVarios = $dbo->getFields("Servicio", "preciosVarios", "IDServicio = '" . $_GET["ids"] . "'");
                                if ($preciosVarios == 'S') {
                                ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#precios">
                                                    <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;Precios
                                                </a>
                                            </h4>
                                        </div>

                                        <div class="panel-collapse collapse <?php if ($_GET["tab"] == "precios") echo "in"; ?>" id="precios">
                                            <div class="panel-body">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#cargaPrecios">
                                                                <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                                &nbsp;Carga Precios
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse <?php if ($_GET["tab1"] == "cargaPrecios") echo "in"; ?>" id="cargaPrecios">
                                                        <div class="panel-body">
                                                            <?php include("precios.php"); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#listaPrecios">
                                                                <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                                &nbsp;Lista de precios
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse <?php if ($_GET["tab1"] == "listaPrecios") echo "in"; ?>" id="listaPrecios">
                                                        <div class="panel-body">
                                                            <?php
                                                            include("listaPrecios.php");
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>

                                <?php
                                $permite_adicionales = $dbo->getFields("Servicio", "PermiteAdicionarServicios", "IDServicio = '" . $_GET["ids"] . "'");
                                if ($permite_adicionales == "S") {
                                ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed " data-toggle="collapse" data-parent="#accordion" href="#collapseNine">
                                                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;Categorias para servicios adicionales (ejemplo Carritos, Caddies )
                                                </a>
                                            </h4>
                                        </div>

                                        <div class="panel-collapse collapse <?php if ($_GET["tab"] == "categoriaserv") echo "in"; ?>" id="collapseNine">
                                            <div class="panel-body">
                                                <?php include("categorias.php"); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTen">
                                                    <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;Elementos de la categoria
                                                </a>
                                            </h4>
                                        </div>

                                        <div class="panel-collapse collapse <?php if ($_GET["tab"] == "adicionales") echo "in"; ?>" id="collapseTen">
                                            <div class="panel-body">
                                                <?php include("caracteristicaproducto.php"); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>



                                <?php
                                $permite_adicionar_caddies = $dbo->getFields("Servicio", "PermiteAdicionarCaddies", "IDServicio = '" . $_GET["ids"] . "'");
                                if ($permite_adicionar_caddies == "S") {
                                ?>

                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed " data-toggle="collapse" data-parent="#accordion" href="#collapseCaddie">
                                                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;Categoria Caddies
                                                </a>
                                            </h4>
                                        </div>

                                        <div class="panel-collapse collapse <?php if ($_GET["tab"] == "categoriacaddie2") echo "in"; ?>" id="collapseCaddie">
                                            <div class="panel-body">
                                                <?php include("categoriacaddie2.php"); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseCaddie2">
                                                    <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;Caddie
                                                </a>
                                            </h4>
                                        </div>

                                        <div class="panel-collapse collapse <?php if ($_GET["tab"] == "caddie") echo "in"; ?>" id="collapseCaddie2">
                                            <div class="panel-body">
                                                <?php include("caddie2.php"); ?>
                                                <a class="btn btn-primary btn-sm btnModal fancybox" href="disponibilidad_caddie.php?ids=<?= $_GET[ids] ?>" data-fancybox-type="iframe">
                                                    Crear Disponiblidad Caddie
                                                </a>
                                                <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                    <tr>
                                                        <th align="center" valign="middle" width="64">Editar</th>
                                                        <th>Nombre</th>
                                                        <th>Activo</th>
                                                        <th>Orden</th>
                                                        <th align="center" valign="middle" width="64">Eliminar</th>
                                                    </tr>
                                                    <tbody id="listacontactosanunciante">
                                                        <?php

                                                        $r_dispo = &$dbo->all("CaddieDisponibilidad", "IDServicio = '" . $_GET["ids"]  . "' ORDER by Orden");


                                                        while ($r = $dbo->object($r_dispo)) {

                                                            if (((int)$r->Orden % 2) == 0)
                                                                $color_fila = "fff";
                                                            else
                                                                $color_fila = "e1fede";
                                                        ?>

                                                            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>" style="background-color:#<?php echo $color_fila; ?>">
                                                                <td align="center" width="64">
                                                                    <a class="ace-icon glyphicon glyphicon-pencil fancybox" href="disponibilidad_caddie.php?ids=<?= $_GET[ids] ?>&IDCaddieDisponibilidad=<?= $r->IDCaddieDisponibilidad ?>" data-fancybox-type="iframe"></a>

                                                                <td><?php echo $r->Nombre; ?></td>
                                                                <td><?php
                                                                    if ($r->Activo == "S") :
                                                                        echo "Activa";
                                                                    else :
                                                                        echo "Desactivada";
                                                                    endif;
                                                                    ?></td>
                                                                <td><?php echo $r->Orden; ?></td>
                                                                <td align="center" width="64">
                                                                    <a href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaCaddieDisponibilidad&ids=<?php echo $_GET[ids]; ?>&IDCaddieDisponibilidad=<? echo $r->IDCaddieDisponibilidad ?>" class="ace-icon glyphicon glyphicon-remove"></a>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                            $orden_anterior = $r->Orden;
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <tr>
                                                        <th class="texto" colspan="14"></th>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>

                                <?php
                                $permite_sistema_abono = $dbo->getFields("Servicio", "PermiteSistemaAbono", "IDServicio = '" . $_GET["ids"] . "'");
                                if ($permite_sistema_abono == "S") {
                                ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle collapsed " data-toggle="collapse" data-parent="#accordion" href="#collapseporcentajeabono">
                                                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;Porcentaje abono
                                                </a>
                                            </h4>
                                        </div>

                                        <div class="panel-collapse collapse <?php if ($_GET["tab"] == "porcentajeabono") echo "in"; ?>" id="collapseporcentajeabono">
                                            <div class="panel-body">
                                                <?php include("porcentajeabono.php"); ?>
                                            </div>
                                        </div>
                                    </div>



                                <?php } ?>

                            </div>
                        </div><!-- /.col -->
                    </div>
                </div><!-- /.widget-main -->
            </div><!-- /.widget-body -->
        </div>
        <!-- /.widget-box --