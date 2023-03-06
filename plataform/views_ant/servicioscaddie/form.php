<div class="widget-box transparent" id="recent-box">
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
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Club Aplica Servicio: </label>
                                    <div class="col-sm-8">

                                        <select name="IDListaClubes" id="IDListaClubes" class="form-control " title="IDListaClubes">
                                            <option value=""></option>
                                            <?php

                                            $sql_listaClubes = "Select IDListaClubes,Nombre From ListaClubes  Where  Publicar = 'S' Order by Nombre";
                                            $result_listaClubes = $dbo->query($sql_listaClubes);
                                            while ($row_listaClubes = $dbo->fetchArray($result_listaClubes)) :

                                            ?>

                                                <option value="<?php echo $row_listaClubes["IDListaClubes"] ?>"><?php echo  $row_listaClubes["Nombre"] ?></option>
                                            <?php endwhile; ?>
                                        </select>




                                        <br>
                                        <a id="agregar_club" href="#">Agregar</a> | <a id="borrar_club" href="#">Borrar</a>
                                        <br>
                                        <select name="ListaClubesCaddies[]" id="ListaClubesCaddies" class="col-xs-8" multiple>
                                            <?php
                                            $item = 1;
                                            $array_ListaClubes = explode("|||", $frm["ClubesAplicaServicio"]);

                                            foreach ($array_ListaClubes as $id_lista_clubes => $datosListaClubes) :

                                                if (!empty($datosListaClubes)) {
                                                    $item--;
                                                    $IDListaClubes = $datosListaClubes;
                                                    if ($IDListaClubes > 0) :
                                                        $nombre_clubes = utf8_encode($dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = '" . $IDListaClubes . "'"));
                                                        echo $nombre_clubes;
                                            ?>
                                                        <option value="<?php echo $IDListaClubes; ?>"><?php echo $nombre_clubes; ?></option>
                                            <?php
                                                    endif;
                                                }
                                            endforeach; ?>
                                        </select>
                                        <input type="hidden" name="ClubesAplicaServicio" id="ClubesAplicaServicio" value="">
                                    </div>
                                </div>





                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Nombre</label>
                                    <div class="col-sm-8"> <input type="text" id="Nombre" name="Nombre" placeholder="" class="form-control mandatory" title="Nombre" value="<?php echo $frm["Nombre"] ?>" required></div>

                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Seleccionar Elemento</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelSeleccionarElemento" name="LabelSeleccionarElemento" placeholder="" class="form-control mandatory" title="Texto Seleccionar Elemento" value="<?php echo $frm["LabelSeleccionarElemento"] ?>" required></div>

                                </div>
                            </div>

                            <div class="form-group first">



                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Texto Seleccionar Elemento</label>
                                    <div class="col-sm-8"> <input type="text" id="LabelSeleccionarElemento" name="LabelSeleccionarElemento" placeholder="" class="form-control mandatory" title="Texto Seleccionar Elemento" value="<?php echo $frm["LabelSeleccionarElemento"] ?>" required></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Numero De Dias</label>
                                    <div class="col-sm-8"> <input type="text" id="NumeroDiasMostrar" name="NumeroDiasMostrar" placeholder="" class="form-control mandatory" title="Numero Dias Mostrar" value="<?php echo $frm["NumeroDiasMostrar"] ?>" required></div>

                                </div>

                            </div>

                            <div class="form-group first">



                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Hora Apertura</label>
                                    <div class="col-sm-8"> <input type="time" id="HoraApertura" name="HoraApertura" placeholder="" class="form-control mandatory" title="Hora Apertura" value="<?php echo $frm["HoraApertura"] ?>" required></div>

                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activo"], 'Activo', "class='input'") ?>
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
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                        </button>
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