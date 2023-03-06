<?php
$sqlTiemposRespuesta = "Select Tiempos.Nombre, PreguntaTiempos.TipoCampo, PreguntaTiempos.EtiquetaCampo, PreguntaTiempos.Valores, TiemposRespuesta.Valor 
from TiemposSolicitud, Tiempos, PreguntaTtiempos, TiemposRespuesta
where 
TiemposSolicitud.IDTiempos = Tiempos.IDTiempos
AND TiemposRespuesta.IDTiemposSolicitud= TiemposSolicitud.IDTiemposSolicitud
AND TiemposRespuesta.IDPreguntaTiempos = PreguntaTiempos.IDPreguntaTiempos
AND TiemposSolicitud.IDTiemposSolicitud = " . $frm['IDTiemposSolicitud'];
$queryTiemposRespuesta = $dbo->query($sqlTiemposRespuesta);

$DateAndTime = date('Y-m-d h:i:s', time());

?>
<style>
    a {
        white-space: nowrap;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis !important;
    }
</style>

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

                            <div class="col-xs-12 col-sm-6 first">
                                <?php

                                // Validar si es un Usuario o un Socio
                                if ($row["IDUsuario"] > 0) {
                                    $sqlUsuario = "SELECT * FROM Usuario WHERE IDUsuario = " . $frm['IDUsuario'];
                                    $queryUsuario = $dbo->query($sqlUsuario);
                                    $rowUser = $dbo->assoc($queryUsuario);
                                } else {
                                    $sqlSocio = "SELECT * FROM Socio WHERE IDSocio = " . $frm['IDSocio'];
                                    $querySocio = $dbo->query($sqlSocio);
                                    $rowUser = $dbo->assoc($querySocio);
                                }

                                ?>
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> Usuario </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Usuario" name="Usuario" placeholder="Usuario" class="col-xs-12 mandatory" title="Usuario" value="<?php echo $rowUser["Nombre"]; ?>" readonly="readonly">
                                    <input type="hidden" id="IDUsuario" name="IDUsuario" value="<?php echo $IDUser = $rowUser["IDUsuario"] > 0 ? $rowUser["IDUsuario"] : 0; ?>">
                                    <input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $IDUser = $rowUser["IDSocio"] > 0 ? $rowUser["IDSocio"] : 0; ?>">
                                </div>


                            </div>

                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="FechaTrCr"> Fecha Solicitud </label>
                                <div class="col-sm-8"><input type="text" id="FechaTrCr" name="FechaTrCr" placeholder="Fecha Solicitud" class="col-xs-12 mandatory" title="FechaTrCr" value="<?php echo $frm["FechaTrCr"]; ?>" readonly="readonly"></div>
                            </div>

                            <?php
                            while ($rowsTiemposRespuesta = $dbo->fetchArray($queryTiemposRespuesta)) {
                            ?>
                                <div class="col-xs-12 col-sm-6 first">
                                    <?php
                                    switch ($rowsTiemposRespuesta['TipoCampo']) {
                                        case 'textarea':
                                    ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <textarea id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" readonly="readonly"> <?php echo $rowsTiemposRespuesta["Valor"]; ?> </textarea>
                                            </div>
                                        <?php
                                            break;
                                        case 'radio':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'checkbox':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'select':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'number':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="number" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'date':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="date" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'time':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="time" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'email':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="email" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'rating':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <i class="fa fa-star"> <?php echo $rowsTiemposRespuesta['Valor'] ?> </i>
                                            </div>
                                        <?php
                                            break;
                                        case 'imagen':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">

                                                <? if (!empty($rowsTiemposRespuesta['Valor'])) { ?>
                                                    <a target="_blank" href="<?php echo PQR_ROOT . $rowsTiemposRespuesta['Valor'] ?>">
                                                        <?php //echo mb_strimwidth($rowsAuxiliosRespuesta['Valor'], 0, 45, '...'); 
                                                        ?>
                                                        Ver archivo
                                                    </a>
                                                <?
                                                } // END if
                                                ?>
                                            </div>
                                        <?php
                                            break;

                                        case 'imagenarchivo':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">

                                                <? if (!empty($rowsTiemposRespuesta['Valor'])) { ?>
                                                    <a target="_blank" href="<?php echo PQR_ROOT . $rowsTiemposRespuesta['Valor'] ?>">
                                                        <?php //echo mb_strimwidth($rowsAuxiliosRespuesta['Valor'], 0, 45, '...'); 
                                                        ?>
                                                        Ver archivo
                                                    </a>
                                                <?
                                                } // END if
                                                ?>
                                            </div>
                                        <?php
                                            break;

                                        case 'titulo
                                        ':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;

                                        default:
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsTiemposRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 mandatory" title="Valor" value="<?php echo $rowsTiemposRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                    <?php
                                            break;
                                    }
                                    ?>
                                </div>
                            <?php
                            }
                            ?>

                        </div>
                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-check-circle green"></i>
                                Aprobar/Rechazar
                            </h3>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <?php
                                // Obtener nombre del EstadoSolicitud
                               $EstadosTiempo = SIMResources::$EstadoTiempo;
                                ?>
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado</label>
                                <div class="col-sm-8">
                                    <select id="IDEstado" name="IDEstado" class="col-xs-12 mandatory" title="Estado" onchange="TipoRechazo(this.value)">
                                        <?php
                                        foreach ($EstadosTiempo as $key => $Estado) {
                                        ?>
                                            <option value="<?php echo $key ?>" <?php echo $frm['IDEstado'] == $key ? 'selected' : ''; ?>><?php echo $Estado ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Comentarios"> Comentarios </label>
                                <div class="col-sm-8">
                                    <textarea id="Comentarios" name="Comentarios" class="col-xs-12 mandatory" title="Comentarios"><?php echo $frm["Comentarios"]; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">



                            <div class="col-xs-12 col-sm-6" id="TipoRechazo" style="display: none;">
                                <?php
                                // Obtener nombre del AuxiliosRechazo
                                $sqlTiemposRechazo = "SELECT * FROM TiemposRechazo";
                                $queryTiemposRechazo = $dbo->query($sqlTiemposRechazo);
                                ?>
                                <label class="col-sm-4 control-label no-padding-right" for="AuxiliosRechazo"> Tipo Rechazo </label>
                                <div class="col-sm-8">
                                    <select id="IDTiemposRechazo" name="IDTiemposRechazo" class="col-xs-12 mandatory" title="Tipo Rechazo">
                                        <option value="0">[Seleccione Tipo Rechazo]</option>
                                        <?php
                                        while ($rowTiemposRechazo = $dbo->fetchArray($queryTiemposRechazo)) {
                                        ?>
                                            <option value="<?php echo $rowTiemposRechazo['IDTiemposRechazo'] ?>" <?php echo $rowTiemposRechazo['IDTiemposRechazo'] == $frm['IDTiemposRechazo'] ? 'selected' : '' ?>><?php echo $rowTiemposRechazo['Nombre'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <?php
                                $sql_id_tiempos = $string;
                                $sql_id_tiempos = "Select * From Tiempos Where IDClub = '" . SIMUser::get("club") . "' ";
                                $sql_id_tiempos = $dbo->query($sql_id_tiempos);
                                while ($r_area = $dbo->fetchArray($sql_id_tiempos)) : ?>
                                    <!-- <option value="<?php echo $r_area["IDTiempos"]; ?>" <?php if ($r_area["IDTiempos"] == $frm["IDTiempos"]) echo "selected";  ?>><?php echo $r_area["IDTiempos"]; ?></option>-->
                                <?php
                                endwhile;    ?>

                            </div>


                        </div>


                        <div class="form-group first ">

                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                            else echo $frm["IDClub"];  ?>" />
                                    <input type="hidden" name="IDTiempos" id="IDTiempos" value="<?php echo $frm["IDTiempos"]; ?>" />
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                    </button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" id="FechaRevision" name="FechaRevision" placeholder="Fecha Revision" class="col-xs-12 mandatory" title="FechaRevision" value="<?php echo $DateAndTime; ?>">

                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->
<script>
    function TipoRechazo(i) {
        if (i == 2) {
            document.getElementById('TipoRechazo').style.display = 'block'
        } else {
            document.getElementById('TipoRechazo').style.display = 'none'
        }
    }
    var estado = document.getElementById('IDEstado').value
    window.load = TipoRechazo(estado);
</script>
<?
include("cmp/footer_scripts.php");
?>