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

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

                                <div class="col-sm-6">

                                    <?php
                                    $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                                    $qry_socio_club = $dbo->query($sql_socio_club);
                                    $r_socio = $dbo->fetchArray($qry_socio_club);
                                    if (!empty($frm["IDSocio"])) {
                                        $label_accion = " Accion: " . $r_socio["Accion"];
                                        if ($frm[IDClub] == 35)
                                            $label_accion = " Casa: " . $r_socio["Predio"];
                                    }
                                    ?>

                                    <input type="text" id="Accion" name="Accion" placeholder="Accion Nombre Apellido Numero Documento" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Apellido"] . " " . $r_socio["Nombre"] . $label_accion ?>">
                                    Busqueda por: Accion, Nombre, Apellido, Numero Documento
                                    <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">

                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre completo del visitante</label>
                                <div class="col-sm-8"><input type="text" id="NombreVisitante" name="NombreVisitante" placeholder="Nombre Visitante" class="col-xs-12 mandatory" title="NombreVisitante" value="<?php echo $frm["NombreVisitante"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Parentesco</label>
                                <div class="col-sm-8"><input type="text" id="Parentesco" name="Parentesco" placeholder="Parentesco" class="col-xs-12 mandatory" title="Parentesco" value="<?php echo $frm["Parentesco"]; ?>"></div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha de inicio</label>
                                <div class="col-sm-8"><input type="date" id="FechaDeInicio" name="FechaDeInicio" class="col-xs-12 mandatory" title="FechaDeInicio" value="<?php echo $frm["FechaDeInicio"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha final</label>
                                <div class="col-sm-8"><input type="date" id="FechaFinal" name="FechaFinal" class="col-xs-12 mandatory" title="FechaFinal" value="<?php echo $frm["FechaFinal"]; ?>"></div>
                            </div><br><br><br><br>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Ciudad de procedencia del visitante</label>
                                <div class="col-sm-8"><input type="text" id="CiudadDeProcedenciaDelVisitante" name="CiudadDeProcedenciaDelVisitante" placeholder="CiudadDeProcedenciaDelVisitante" class="col-xs-12 mandatory" title="CiudadDeProcedenciaDelVisitante" value="<?php echo $frm["CiudadDeProcedenciaDelVisitante"]; ?>"></div>
                            </div>

                        </div>

                        <div class="form-group first ">


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
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>