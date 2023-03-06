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
                    <form class="form-horizontal formvalida" role="form" method="post" name="frm<?php echo $script; ?>" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">

                            <div class="form-group first ">
                                <!--   <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dirigido A </label>

                                    <div class="col-sm-8">
                                        <?php /* echo SIMHTML::formRadioGroup(array_flip(SIMResources::$dirigidoa), $frm["DirigidoA"], "DirigidoA", "title=\"DirigidoA\"") */ ?>

                                    </div>

                                </div> -->

                                <!--  <input type="hidden" name="DirigidoA" id="DirigidoA" value="S" /> -->


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="NombreTalonera">Nombre Talonera</label>
                                    <div class="col-sm-8"> <input type="text" id="NombreTalonera" name="NombreTalonera" placeholder="Nombre Talonera" class="form-control mandatory " title="NombreTalonera" value="<?php echo $frm["NombreTalonera"] ?>"></div>


                                </div>
                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="DescripcionTalonera">DescripcionTalonera</label>
                                    <div class="col-sm-8"><input type="text" id="DescripcionTalonera" name="DescripcionTalonera" placeholder="Descripcion Talonera" class="form-control mandatory" title="DescripcionTalonera" value="<?php echo $frm["DescripcionTalonera"] ?>"></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="ValorSocio">Valor Socio</label>
                                    <div class="col-sm-8"> <input type="text" id="ValorSocio" name="ValorSocio" placeholder="Valor Socio" class="form-control mandatory " title="ValorSocio" value="<?php echo $frm["ValorSocio"] ?>"></div>

                                </div>

                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="ValorUsuario">Valor Usuario</label>
                                    <div class="col-sm-8"> <input type="text" id="ValorUsuario" name="ValorUsuario" placeholder="Valor Usuario" class="form-control mandatory" title="ValorUsuario" value="<?php echo $frm["ValorUsuario"] ?>"></div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="ValorGrupoFamiliar">Valor Grupo Familiar</label>
                                    <div class="col-sm-8"> <input type="text" id="ValorGrupoFamiliar" name="ValorGrupoFamiliar" placeholder="Valor Grupo Familiar" class="form-control mandatory" title="ValorGrupoFamiliar" value="<?php echo $frm["ValorGrupoFamiliar"] ?>"></div>

                                </div>

                            </div>

                            <div class="form-group first">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="ValorPorMiembro">Valor Por Miembro</label>
                                    <div class="col-sm-8"> <input type="text" id="ValorPorMiembro" name="ValorPorMiembro" placeholder="Valor Por Miembro" class="form-control mandatory" title="ValorPorMiembro" value="<?php echo $frm["ValorPorMiembro"] ?>"></div>

                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Cantidad Entradas </label>
                                    <div class="col-sm-8"> <input type="text" id="CantidadEntradas" name="CantidadEntradas" placeholder="Cantidad Entradas " class="form-control mandatory" title="CantidadEntradas" value="<?php echo $frm["CantidadEntradas"] ?>"></div>

                                </div>
                            </div>

                            <div class="form-group first">

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label" for="Duracion">Duracion</label>

                                    <div class="col-sm-8">
                                        <input type="number" id="Duracion" name="Duracion" placeholder="Duracion" class="col-xs-4 mandatory" title="Duracion" value="<?php echo $frm["Duracion"] ?>">

                                        <select name="MedicionDuracion" id="MedicionDuracion" class="mandatory" title="Duracion">
                                            <option value=""></option>
                                            <option value="Minutos" <?php if ($frm["MedicionDuracion"] == "Minutos") echo "selected";  ?>>Minutos</option>
                                            <option value="Horas" <?php if ($frm["MedicionDuracion"] == "Horas") echo "selected";  ?>>Horas</option>
                                            <option value="Dias" <?php if ($frm["MedicionDuracion"] == "Dias") echo "selected";  ?>>Dias</option>
                                            <option value="Meses" <?php if ($frm["MedicionDuracion"] == "Meses") echo "selected";  ?>>Meses</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Servicio </label>
                                    <div class="col-sm-7">
                                        <select name="IDServicio" id="IDServicio" class="form-control" title="Servicio">
                                            <option value=""></option>
                                            <?php

                                            $sql_servicios = "Select SC.* From ServicioClub SC Where SC.IDClub = '" . SIMUser::get("club") . "' and SC.Activo = 'S' Order by TituloServicio";
                                            $result_servicios = $dbo->query($sql_servicios);
                                            while ($row_servicios = $dbo->fetchArray($result_servicios)) :

                                                $IDServicio = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '" . $row_servicios["IDServicioMaestro"] . "' and IDClub = '" . SIMUser::get("club") . "' ");

                                                if (!empty($row_servicios["TituloServicio"]))
                                                    $nombre_servicio = $row_servicios["TituloServicio"];
                                                else
                                                    $nombre_servicio = utf8_encode($dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $row_servicios["IDServicioMaestro"] . "'")); ?>

                                                <option value="<?php echo $IDServicio ?>" <?php if ($frm["IDServicio"] == $IDServicio) echo "selected";  ?>><?php echo  $nombre_servicio ?></option>
                                            <?php endwhile; ?>
                                        </select>

                                        <br>
                                        <a id="agregar_servicioclub" href="#">Agregar Servicio</a> | <a id="borrar_servicio" href="#">Borrar Servicio</a>
                                        <br>
                                        <select name="SocioServicioDatos[]" id="SocioServicio" class="col-xs-8 mandatory" multiple>
                                            <?php
                                            $SQLServiciosPermisos = "SELECT * FROM TaloneraServicios WHERE IDTalonera = $_GET[id]";
                                            $QRYServiciosPermisos = $dbo->query($SQLServiciosPermisos);
                                            while($DatoServicios = $dbo->fetchArray($QRYServiciosPermisos)):
                                
                                                $IDServicioMaestro = $dbo->getFields("Servicio","IDServicioMaestro","IDServicio = $DatoServicios[IDServicio]");
                                                $NombreServicio = $dbo->getFields("ServicioClub","TituloServicio","IDServicioMaestro = $IDServicioMaestro AND IDClub = " . SIMUser::get("club"));
                                                if(empty($NombreServicio))
                                                    $NombreServicio = $dbo->getFields("ServicioMaestro","Nombre","IDServicioMaestro = $IDServicioMaestro");

                                                ?>
                                                    <option value="<?php echo $DatoServicios[IDServicio]; ?>"><?php echo $NombreServicio; ?></option>
                                                <?php                       
                                            endwhile;
                                            ?>
                                        </select>
                                        <input type="hidden" name="SeleccionServicios" id="SeleccionServicios" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Talonera monedero </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["TaloneraMonedero"], 'TaloneraMonedero', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="CantidadEntradas">Saldo talonera monedero </label>
                                    <div class="col-sm-8"> <input type="text" id="SaldoTaloneraMonedero" name="SaldoTaloneraMonedero" placeholder="Saldo Talonera Monedero " class="form-control " title="Saldo Talonera Monedero" value="<?php echo $frm["SaldoTaloneraMonedero"] ?>"></div>

                                </div>


                            </div>
                            <div class="form-group first">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Todos los servicios </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["TodosLosServicios"], 'TodosLosServicios', "class='input'") ?>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Activa </label>

                                    <div class="col-sm-8">
                                        <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activa"], 'Activa', "class='input'") ?>
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