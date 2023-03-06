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
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario </label>
                                    <div class="col-sm-8"> <?php
                                        $sql_socio_club = "Select Nombre From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
                                        $qry_socio_club = $dbo->query($sql_socio_club);
                                        $r_socio = $dbo->fetchArray($qry_socio_club); ?> <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax-funcionario" title="número de derecho" value="<?php echo utf8_decode($r_socio["Nombre"] . " " . $r_socio["Apellido"]) ?>">
                                        <input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" class="mandatory" title="Usuario">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Servicio Caddie</label>
                                    <div class="col-sm-8">
                                        <select name="IDServiciosCaddie" id="IDServiciosCaddie">
                                            <option value="">[SELECCION UNA OPCION]</option> <?php

                                            $sql_servicios = "Select IDServiciosCaddie,Nombre From ServiciosCaddie  Where Activo = '1' AND IDClub = ".SIMUser::get("club")." Order by IDServiciosCaddie";
                                            $result_servicios = $dbo->query($sql_servicios);
                                            while ($row_servicios = $dbo->fetchArray($result_servicios)) : ?> <option value="<?php echo $row_servicios["IDServiciosCaddie"] ?>" <?php if ($frm["IDServiciosCaddie"] == $row_servicios["IDServiciosCaddie"]) echo "selected";  ?>><?php echo  $row_servicios["Nombre"] ?></option> <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Categoria</label>
                                    <div class="col-sm-8">
                                        <select name=" IDCategoriasEcaddie" id="IDCategoriasEcaddie">
                                            <option value="">[SELECCION UNA OPCION]</option> <?php

                                            $sql_servicios = "Select * From  CategoriasEcaddie  Where Activa = '1' AND IDClub = ".SIMUser::get("club")." Order by IDServiciosCaddie";
                                            $result_servicios = $dbo->query($sql_servicios);
                                            while ($row_servicios = $dbo->fetchArray($result_servicios)) : ?> <option value="<?php echo $row_servicios["IDCategoriasEcaddie"] ?>" <?php if ($frm["IDCategoriasEcaddie"] == $row_servicios["IDCategoriasEcaddie"]) echo "selected";  ?>><?php echo  $row_servicios["Nombre"] ?></option> <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Nombre</label>
                                    <div class="col-sm-8"> <input type="text" id="Nombre" name="Nombre" placeholder="" class="form-control mandatory" title="Nombre" value="<?php echo $frm["Nombre"] ?>" required></div>
                                </div>
                            </div>
                            <div class="form-group first">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Descripcion</label>
                                    <div class="col-sm-8">
                                        <textarea name="Descripcion" id="Descripcion" cols="30" rows="10" class="form-control mandatory"><?php echo $frm["Descripcion"]; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Valor Caddie</label>
                                    <div class="col-sm-8"> 
                                        <input type="text" id="Valor" name="Valor" placeholder="" class="form-control mandatory" title="Valor" value="<?php echo $frm["Valor"] ?>" required>
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