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
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Caddie </label>

                                    <div class="col-sm-8">
                                        <select name="IDUsuario" id="IDUsuario" class="mandatory" required>
                                            <option value=""></option>
                                            <?php
                                            $sql_caddie = "Select IDUsuario,Nombre,IDCaddiesEcaddie From CaddiesEcaddie Where IDClub = '" . SIMUser::get("club") . "'";
                                            $qry_caddie = $dbo->query($sql_caddie);
                                            while ($r_caddie = $dbo->fetchArray($qry_caddie)) :

                                            ?>
                                                <option value="<?php echo $r_caddie["IDUsuario"]; ?>" <?php if ($r_caddie["IDCaddiesEcaddie"] == $frm["IDCaddiesEcaddie"]) echo "selected";  ?>><?php echo $r_caddie["Nombre"]; ?></option>
                                            <?php
                                            endwhile;    ?>
                                        </select>
                                    </div>
                                </div>



                                <div class="form-group col-md-6">
                                    <?php
                                    $TurboCaddieActivado = $dbo->getFields("CaddiesEcaddie", "TurboCaddieActivado", "IDCaddiesEcaddie = '" . $frm[IDCaddiesEcaddie] . "'");

                                    ?>
                                    <label class="col-sm-4 control-label" for="TurboCaddieActivado">Turbo Caddie</label>
                                    <div class="col-sm-2"> <input type="checkbox" id="TurboCaddieActivado" name="TurboCaddieActivado" placeholder="" class="form-control" title="Turbo Caddie Activado" value="S" <?php if ($TurboCaddieActivado == "S") echo "checked"; ?>></div>


                                </div>

                            </div>
                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="IDClubSeleccion">Selecciona el club</label>
                                    <div class="col-sm-8">
                                        <select name="IDClubSeleccion" id="IDClubSeleccion" class="mandatory" required>
                                            <option value=""></option>
                                            <?php
                                            $IDConfiguracionCaddies = $dbo->getFields("ConfiguracionCaddies", "IDConfiguracionCaddies", "IDClub = '" . SIMUser::get("club") . "'");
                                            $sql_lista_clubes = "Select IDListaClubes From ClubesCaddies Where IDConfiguracionCaddies = '" . $IDConfiguracionCaddies . "'";
                                            $qry_lista_clubes = $dbo->query($sql_lista_clubes);
                                            while ($r_lista_clubes = $dbo->fetchArray($qry_lista_clubes)) : ?>
                                                <option value="<?php echo $r_lista_clubes["IDListaClubes"]; ?>" <?php if ($r_lista_clubes["IDListaClubes"] == $frm["IDClubSeleccion"]) echo "selected";  ?>><?php echo $dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = $r_lista_clubes[IDListaClubes]"); ?></option>
                                            <?php
                                            endwhile;    ?>
                                        </select>
                                    </div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Días</label>

                                    <div class="col-sm-8">
                                        <?php
                                        //print_r($frm["Dias"]);
                                        if (!empty($frm["Dias"])) :
                                            $array_dias = explode(",", $frm["Dias"]);
                                        endif;
                                        //print_r($array_dias);
                                        //array_pop($array_dias);

                                        foreach ($Dia_array as $id_dia => $dia) :

                                        ?>
                                            <input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if (in_array($id_dia, $array_dias) && $dia != "") echo "checked"; ?>><?php echo $dia; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> </label>

                                    <div class="col-sm-8">
                                        <td colspan="2">
                                            <table width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td width="44%"><span class="columnafija">Hora Inicial </span></td>
                                                        <td width="56%"><span class="columnafija">Hora Final </span></td>
                                                    </tr>
                                                    <?php
                                                    $contador_hora_guardada = 1;
                                                    $sql_servicio_disponibilidad = $dbo->query("Select * From DisponibilidiadCaddiesEcaddie Where IDCaddiesEcaddie = '" . $frm[IDCaddiesEcaddie] . "'");

                                                    while ($r_disponibilidad = $dbo->fetchArray($sql_servicio_disponibilidad)) :
                                                        // $array_intervalo[$contador_hora_guardada] = $r_disponibilidad["Intervalo"];
                                                        $array_desde[$contador_hora_guardada] = $r_disponibilidad["HoraDesde"];
                                                        $array_hasta[$contador_hora_guardada] = $r_disponibilidad["HoraHasta"];
                                                        $contador_hora_guardada++;
                                                    endwhile;

                                                    for ($contador_horas = 1; $contador_horas <= 5; $contador_horas++) : ?>
                                                        <tr>

                                                            <td><input type="time" name="HoraDesde<?= $contador_horas ?>" id="HoraDesde<?= $contador_horas ?>" class="input <?php if ($contador_horas == 1) echo "mandatory"; ?>" title="Hora desde" value="<?php echo $array_desde[$contador_horas] ?>"></td>
                                                            <td><input type="time" name="HoraHasta<?= $contador_horas ?>" id="HoraHasta<?= $contador_horas ?>" class="input <?php if ($contador_horas == 1) echo "mandatory"; ?>" title="Hora hasta" value="<?php echo $array_hasta[$contador_horas] ?>" onchange="validahorasdisponibilidadcaddie(<?= $contador_horas ?>)"></td>
                                                        </tr>
                                                    <?php endfor; ?>

                                                </tbody>
                                            </table>
                                        </td>


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
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
     function validahoras(contador_horas) {
        var HoraDesde = document.getElementById("HoraDesde" + contador_horas).value;
        var HoraHasta = document.getElementById("HoraHasta" + contador_horas).value;

        if (HoraHasta <= HoraDesde) {
            alert("La hora final debe ser mayor a la hora inicio");
            document.getElementById("HoraHasta" + contador_horas).value = "";
            document.getElementById("HoraHasta" + contador_horas).focus();
            return false;
        }

        return true;
    } 
</script> -->
<?
include("cmp/footer_scripts.php");
?>