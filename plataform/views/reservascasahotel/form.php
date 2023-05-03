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
                                    <label class="col-sm-4 control-label" for="Cedula">Cedula</label>
                                    <div class="col-sm-8"><input type="text" id="Cedula" name="Cedula" placeholder="Cedula" class="form-control" title="Cedula" value="<?php echo $frm["Cedula"] ?>" required></div>
                                </div>
                                <div class="form-group first ">
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mes: </label>

                                        <div class="col-sm-8">
                                            <select name="Mes" id="Mes" onchange="selSemanas()">
                                                <option value="">Seleccione</option>

                                                <?php foreach ($meses['response'] as $key_elemento => $datos_elemento) :

                                                    foreach ($datos_elemento['Meses'] as $key_elemento2 => $datos_elemento2) : ?>
                                                        <option value="<?php echo $datos_elemento['Ano'] . "/" . $datos_elemento2['Mes'] ?>"><?php echo $datos_elemento['Ano'] . " / " . $datos_elemento2['Nombre']; ?></option>
                                                    <?php endforeach; ?>

                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group first ">
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> TipoReserva: </label>

                                        <div class="col-sm-8">

                                            <select name="TipoReserva" id="TipoReserva">
                                                <option value="">Seleccione</option>
                                                <option value="estadia">Estadia</option>
                                                <option value="pasadia">Pasadia</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group first ">
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Semana: </label>

                                        <div class="col-sm-8">

                                            <select name="Semana" id="Semana">
                                                <option value="">Seleccione</option>
                                                <option value="estadia">Semana 1 (Del 29 al 30)</option>
                                                <option value="pasadia">Semana 2 (Del 29 al 30)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>



                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="ServicioLimpieza">Servicio Limpieza</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["ServicioLimpieza"], "ServicioLimpieza", "", "");
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="ServicioNinera">Servicio Ninera</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["ServicioNinera"], "ServicioNinera", "", "");
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="ServicioAcompanante">Servicio Acompanante</label>
                                    <div class="col-sm-8">
                                        <?php
                                        echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["ServicioAcompanante"], "ServicioAcompanante", "", "");
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="Observaciones">Observaciones</label>
                                    <div class="col-sm-8"><input type="text" id="Observaciones" name="Observaciones" placeholder="Observaciones" class="form-control" title="Observaciones" value="<?php echo $frm["Observaciones"] ?>" required></div>
                                </div>
                            </div>


                            <div class="form-group first ">
                                <div class="clearfix form-actions">
                                    <div class="col-xs-12 text-center">
                                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                        <input type="hidden" name="IDUsuario" id="action" value="<?php if (empty($frm["IDUsuario"])) echo SIMUser::get("IDUsuario");
                                                                                                    else echo $frm["IDUsuario"];  ?>" />
                                        <input type="hidden" name="IDSocio" id="action" value="<?php if (empty($frm["IDSocio"])) echo SIMUser::get("IDSocio");
                                                                                                else echo $frm["IDSocio"];  ?>" />
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                else echo $frm["IDClub"];  ?>" />
                                        <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                        </button>

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
<script>
    function selSemanas() {
            // let Mes = $(this).val();
            let Mes = $('#Mes').val();
            alert(Mes);

            jQuery.ajax({
                type: "GET",
                data: {
                    oper: "form",
                    proceso: "ciudades",
                    idDepartamento: idDepartamentoDian,
                    idCiudad: idCiudadDian
                },
                dataType: "html",
                url: "includes/async/socios.async.php",
                success: function(data) {
                    $("#div_IDCiudadDian").html(data);
                }
            });
    }
</script>
<?
include("cmp/footer_scripts.php");
?>