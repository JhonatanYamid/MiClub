<?php
$frm_get =  SIMUtil::makeSafe($_GET);
$AuxiliosInfinitoSolicitud = $dbo->fetchAll('AuxiliosInfinitoSolicitud', 'IDAuxiliosInfinitoSolicitud = ' . $frm_get['id'], 'array');
$AuxiliosInfinito = $dbo->fetchAll('AuxiliosInfinito', 'IDAuxiliosInfinito = ' . $AuxiliosInfinitoSolicitud['IDAuxiliosInfinito'], 'array');

// Validar si es un Usuario o un Socio
if ($AuxiliosInfinitoSolicitud["IDUsuario"] > 0) {
    $sqlUsuario = "SELECT * FROM Usuario WHERE IDUsuario = " . $AuxiliosInfinitoSolicitud['IDUsuario'];
    $queryUsuario = $dbo->query($sqlUsuario);
    $rowUser = $dbo->assoc($queryUsuario);
    $user = "Empleado";
} else {
    $sqlSocio = "SELECT * FROM Socio WHERE IDSocio = " . $AuxiliosInfinitoSolicitud['IDSocio'];
    $querySocio = $dbo->query($sqlSocio);
    $rowUser = $dbo->assoc($querySocio);
    $user = "Socio";
}

$sqlAuxiliosRespuesta = "Select AuxiliosInfinitoSolicitud.IDSocio,AuxiliosInfinitoSolicitud.IDUsuario,AuxiliosInfinito.Nombre, PreguntaAuxiliosInfinito.TipoCampo, PreguntaAuxiliosInfinito.EtiquetaCampo, PreguntaAuxiliosInfinito.Obligatorio, PreguntaAuxiliosInfinito.Valores, AuxiliosInfinitoRespuesta.Valor
from AuxiliosInfinitoSolicitud, AuxiliosInfinito, PreguntaAuxiliosInfinito, AuxiliosInfinitoRespuesta
where
AuxiliosInfinitoSolicitud.IDAuxiliosInfinito = AuxiliosInfinito.IDAuxiliosInfinito
AND AuxiliosInfinitoRespuesta.IDAuxiliosInfinitoSolicitud= AuxiliosInfinitoSolicitud.IDAuxiliosInfinitoSolicitud
AND AuxiliosInfinitoRespuesta.IDPreguntaAuxiliosInfinito = PreguntaAuxiliosInfinito.IDPreguntaAuxiliosInfinito
AND AuxiliosInfinitoSolicitud.IDAuxiliosInfinitoSolicitud = " . $frm_get['id'];
$queryAuxiliosRespuesta = $dbo->query($sqlAuxiliosRespuesta);

$DateAndTime = date('Y-m-d h:i:s', time());

$NumeroDocumento = $rowUser['NumeroDocumento'];
$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . SIMUser::get('IDUsuario') . "' ", "array");

$sqlJefe = "SELECT COUNT(IDAuxiliosInfinitoSolicitud) as Total FROM AuxiliosInfinitoSolicitud ais LEFT JOIN Usuario u ON ais.IDUsuario=u.IDUsuario LEFT JOIN Socio s ON ais.IDSocio=s.IDSocio WHERE ais.IDClub = " . SIMUser::get('club') . " AND (s.DocumentoJefe = " . $datos_usuario['NumeroDocumento'] . " OR u.DocumentoJefe = " . $datos_usuario['NumeroDocumento'] . ")";

$queryJefe = $dbo->query($sqlJefe);
$resultJefe = $dbo->assoc($queryJefe);


if ($resultJefe['Total'] > 0) {
    $Perfil = 1;
} else {
    $Perfil = 0;
}
// Cambiar club para luker

$arrIDClubLuker = [95, 96, 97, 98, 122, 169];
if (in_array(SIMUser::get('club'), $arrIDClubLuker)) {
    $vista = "Licencia";
} else {
    $vista = "Auxilio";
}

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
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="AuxiliosInfinito"> <?= $vista ?> </label>
                                <div class="col-sm-8"><input type="text" id="AuxiliosInfinito" name="AuxiliosInfinito" placeholder="Fecha Solicitud" class="col-xs-12 mandatory" title="AuxiliosInfinito" value="<?php echo $AuxiliosInfinito["Nombre"]; ?>" readonly="readonly"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="Usuario"> <?= $user ?> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="Usuario" name="Usuario" placeholder="Usuario" class="col-xs-12 mandatory" title="Usuario" value="<?php echo $rowUser["Nombre"]; ?>" readonly="readonly">
                                    <input type="hidden" id="IDUsuario" name="IDUsuario" value="<?php echo $IDUser = $rowUser["IDUsuario"] > 0 ? $rowUser["IDUsuario"] : 0; ?>">
                                    <input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $IDUser = $rowUser["IDSocio"] > 0 ? $rowUser["IDSocio"] : 0; ?>">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 first">
                                <label class="col-sm-4 control-label no-padding-right" for="FechaTrCr"> <?= SIMUtil::get_traduccion('', '', 'Fechasolicitud', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="FechaTrCr" name="FechaTrCr" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechasolicitud', LANGSESSION); ?>" class="col-xs-12 mandatory" title="FechaTrCr" value="<?php echo $AuxiliosInfinitoSolicitud["FechaTrCr"]; ?>" readonly="readonly"></div>
                            </div>

                            <?php
                            while ($rowsAuxiliosRespuesta = $dbo->fetchArray($queryAuxiliosRespuesta)) {
                                $mandatory = ($rowsAuxiliosRespuesta['Obligatorio'] == 'S') ? "mandatory" : "";
                            ?>
                                <div class="col-xs-12 col-sm-6 first">
                                    <?php
                                    switch ($rowsAuxiliosRespuesta['TipoCampo']) {
                                        case 'textarea':
                                    ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <textarea id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" readonly="readonly"> <?php echo $rowsAuxiliosRespuesta["Valor"]; ?> </textarea>
                                            </div>
                                        <?php
                                            break;
                                        case 'radio':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'checkbox':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'select':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'number':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="number" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'date':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="date" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'time':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="time" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'email':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="email" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;
                                        case 'rating':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <i class="fa fa-star"> <?php echo $rowsAuxiliosRespuesta['Valor'] ?> </i>
                                            </div>
                                        <?php
                                            break;
                                        case 'imagen':
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">

                                                <? if (!empty($rowsAuxiliosRespuesta['Valor'])) { ?>
                                                    <a target="_blank" href="<?php echo PQR_ROOT . $rowsAuxiliosRespuesta['Valor'] ?>">
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
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">

                                                <? if (!empty($rowsAuxiliosRespuesta['Valor'])) { ?>
                                                    <a target="_blank" href="<?php echo PQR_ROOT . $rowsAuxiliosRespuesta['Valor'] ?>">
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
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
                                            </div>
                                        <?php
                                            break;

                                        default:
                                        ?>
                                            <label class="col-sm-4 control-label no-padding-right" for="Valor"> <?php echo $rowsAuxiliosRespuesta['EtiquetaCampo']; ?> </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 <?= $mandatory; ?>" title="Valor" value="<?php echo $rowsAuxiliosRespuesta["Valor"]; ?>" readonly="readonly">
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
                                <?= SIMUtil::get_traduccion('', '', 'Aprobar/Rechazar', LANGSESSION); ?>
                            </h3>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <?php
                                // Obtener nombre del EstadoSolicitud
                                $EstadosAuxilio = SIMResources::$EstadoAuxilio;
                                ?>
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?></label>
                                <div class="col-sm-8">
                                    <select id="IDEstado" name="IDEstado" class="col-xs-12 mandatory" title="Estado" onchange="TipoRechazo(this.value)">
                                        <?php
                                        foreach ($EstadosAuxilio as $key => $Estado) {
                                            // $disabled = ($key == 4) ?  ' disabled ' : '';
                                        ?>
                                            <option value="<?= $key ?>" <?= $frm['IDEstado'] == $key ? ' selected ' : ''; ?>>
                                                <?php echo $Estado ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <?php if ($Perfil == 1) { ?>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Comentarios"> <?= SIMUtil::get_traduccion('', '', 'Comentarios', LANGSESSION); ?> </label>
                                    <div class="col-sm-8">
                                        <textarea id="Comentarios" name="Comentarios" class="col-xs-12" title="Comentarios"><?= $frm["Comentarios"]; ?></textarea>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Comentarios"> <?= SIMUtil::get_traduccion('', '', 'Comentarios', LANGSESSION); ?> </label>
                                    <div class="col-sm-8">
                                        <textarea id="Comentarios" name="Comentarios" class="col-xs-12" title="Comentarios" disabled><?= $frm["Comentarios"]; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Comentario', LANGSESSION); ?>: </label>
                                    <div class="col-sm-8">
                                        <textarea id="ComentarioAprobador" name="ComentarioAprobador" cols="10" rows="5" class="col-xs-12" title="Comentario Aprobador" <?= ($_GET["action"] != "edit") ? " disabled" : ''; ?>><?php echo $frm["ComentarioAprobador"]; ?></textarea>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="form-group first ">



                            <div class="col-xs-12 col-sm-6" id="TipoRechazo" style="display: none;">
                                <?php
                                // Obtener nombre del AuxiliosRechazo
                                $sqlAuxiliosRechazo = "SELECT * FROM AuxiliosInfinitoRechazo";
                                $queryAuxiliosRechazo = $dbo->query($sqlAuxiliosRechazo);
                                ?>
                                <label class="col-sm-4 control-label no-padding-right" for="AuxiliosRechazo"> Tipo Rechazo </label>
                                <div class="col-sm-8">
                                    <select id="IDAuxiliosInfinitoRechazo" name="IDAuxiliosInfinitoRechazo" class="col-xs-12 mandatory" title="Tipo Rechazo">
                                        <option value="0">[Seleccione Tipo Rechazo]</option>
                                        <?php
                                        while ($rowAuxiliosRechazo = $dbo->fetchArray($queryAuxiliosRechazo)) {
                                        ?>
                                            <option value="<?php echo $rowAuxiliosRechazo['IDAuxiliosInfinitoRechazo'] ?>" <?php echo $rowAuxiliosRechazo['IDAuxiliosInfinitoRechazo'] == $frm['IDAuxiliosInfinitoRechazo'] ? 'selected' : '' ?>><?php echo $rowAuxiliosRechazo['Nombre'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <?php
                                $sql_id_auxilios = $string;
                                $sql_id_auxilios = "Select * From AuxiliosInfinito Where IDClub = '" . SIMUser::get("club") . "' ";
                                $sql_id_auxilios = $dbo->query($sql_id_auxilios);
                                while ($r_area = $dbo->fetchArray($sql_id_auxilios)) : ?>
                                    <!-- <option value="<?php echo $r_area["IDAuxiliosInfinito"]; ?>" <?php if ($r_area["IDAuxiliosInfinito"] == $frm["IDAIDAuxiliosInfinito"]) echo "selected";  ?>><?php echo $r_area["IDAuxilios"]; ?></option>-->
                                <?php
                                endwhile;    ?>

                            </div>


                        </div>


                        <div class="form-group first ">

                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?= $frm['IDAuxiliosInfinitoSolicitud'] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                            else echo $frm["IDClub"];  ?>" />
                                    <input type="hidden" name="IDAuxiliosInfinito" id="IDAuxiliosInfinito" value="<?php echo $frm["IDAuxiliosInfinito"]; ?>" />
                                    <input type="hidden" name="IDModulo" id="IDModulo" value="<?php echo SIMReg::get("IDModulo"); ?>" />
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                                    </button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm['IDAuxiliosInfinitoSolicitud'] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm['IDAuxiliosInfinitoSolicitud'] ?>" />
                                    <input type="hidden" id="FechaRevision" name="FechaRevision" placeholder="Fecha Revision" class="col-xs-12 mandatory" title="FechaRevision" value="<?php echo $DateAndTime; ?>">
                                    <input type="hidden" name="Perfil" id="Perfil" value="<?= $Perfil ?>" />

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