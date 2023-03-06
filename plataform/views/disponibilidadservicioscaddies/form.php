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
                                <div class="form-group col-md-12">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Dias </label>
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
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Servicio Caddie</label>
                                    <div class="col-sm-8">

                                        <select name="IDServiciosCaddie" id="IDServiciosCaddie">
                                            <option value=""></option>
                                            <?php

                                            $sql_servicios = "Select IDServiciosCaddie,Nombre From ServiciosCaddie  Where Activo = '1' AND IDClub = ".SIMUser::get("club")." Order by IDServiciosCaddie";
                                            $result_servicios = $dbo->query($sql_servicios);
                                            while ($row_servicios = $dbo->fetchArray($result_servicios)) : ?>

                                                <option value="<?php echo $row_servicios["IDServiciosCaddie"] ?>" <?php if ($frm["IDServiciosCaddie"] == $row_servicios["IDServiciosCaddie"]) echo "selected";  ?>><?php echo  $row_servicios["Nombre"] ?></option>
                                            <?php endwhile; ?>
                                        </select>

                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Elemento Servicios Caddie</label>
                                    <div class="col-sm-8">

                                        <select name="IDElementoServiciosCaddies" id="IDElementoServiciosCaddies">
                                            <option value=""></option>
                                            <?php

                                            $sql_servicios = "Select IDElementoServiciosCaddies,Nombre From ElementoServiciosCaddies  Where Activo = '1' Order by IDElementoServiciosCaddies";
                                            $result_servicios = $dbo->query($sql_servicios);
                                            while ($row_servicios = $dbo->fetchArray($result_servicios)) : ?>

                                                <option value="<?php echo $row_servicios["IDElementoServiciosCaddies"] ?>" <?php if ($frm["IDElementoServiciosCaddies"] == $row_servicios["IDElementoServiciosCaddies"]) echo "selected";  ?>><?php echo  $row_servicios["Nombre"] ?></option>
                                            <?php endwhile; ?>
                                        </select>

                                    </div>


                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Nombre</label>
                                    <div class="col-sm-8"> <input type="text" id="Nombre" name="Nombre" placeholder="" class="form-control mandatory" title="Nombre" value="<?php echo $frm["Nombre"] ?>" required></div>


                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Duracion</label>

                                    <div class="col-sm-8">
                                        <input type="number" id="Intervalo" name="Intervalo" placeholder="Duracion" class="col-xs-4 mandatory" title="Duracion" value="<?php echo $frm["Intervalo"] ?>">

                                        <select name="MedicionIntervalo" id="MedicionIntervalo" class="mandatory" title="Medicion Intervalo">
                                            <option value=""></option>
                                            <option value="Minutos" <?php if ($frm["MedicionIntervalo"] == "Minutos") echo "selected";  ?>>Minutos</option>
                                            <option value="Horas" <?php if ($frm["MedicionIntervalo"] == "Horas") echo "selected";  ?>>Horas</option>
                                            <option value="Dias" <?php if ($frm["MedicionIntervalo"] == "Dias") echo "selected";  ?>>Dias</option>
                                            <option value="Meses" <?php if ($frm["MedicionIntervalo"] == "Meses") echo "selected";  ?>>Meses</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Horas Desde</label>
                                    <div class="col-sm-8"> <input type="time" id="HoraDesde" name="HoraDesde" placeholder="" class="form-control mandatory" title="Hora Desde" value="<?php echo $frm["HoraDesde"] ?>" required></div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Horas Hasta</label>
                                    <div class="col-sm-8"> <input type="time" id="HoraHasta" name="HoraHasta" placeholder="" class="form-control mandatory" title="Hora Hasta" value="<?php echo $frm["HoraHasta"] ?>" required></div>


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