<?php
if ($frm['CampoValidacion'] == 'Rango') {
    $EdadRegistro = explode('|', $frm['Edad']);
    $firstKey = array_key_first($EdadRegistro);
    $lastKey = array_key_last($EdadRegistro);
    $frm['Edad'] = $EdadRegistro[$firstKey + 1] . '-' . $EdadRegistro[$lastKey - 1];
} elseif ($frm['CampoValidacion'] == '>=') {
    $Edad = explode('|', $frm['Edad']);
    $frm['Edad'] = $Edad[1] - 1;
} elseif ($frm['CampoValidacion'] == '<=') {
    $Edad = explode('|', $frm['Edad']);
    $lastKey = array_key_last($Edad);
    $frm['Edad'] = $Edad[$lastKey - 1] + 1;
} elseif ($frm['CampoValidacion'] == '!=') {
    $Edad = explode('|', $frm['Edad']);
    $Edad = array_filter($Edad);
    $min = 1;
    $max = 100;
    for ($i = $min; $i <= $max; $i++) {
        if (!in_array($i, $Edad)) {
            $frm['Edad'] = $i;
        }
    }
} elseif ($frm['CampoValidacion'] == '==') {
    $frm['Edad'] = trim($frm['Edad'], '|');
}
?>

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
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> Nombre </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> Descripcion </label>
                                <div class="col-sm-8"><input type="text" id="Descripcion" name="Descripcion" placeholder="Descripcion" class="col-xs-12" title="Descripcion" value="<?php echo $frm["Descripcion"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Socio </label>
                                <div class="col-sm-8">
                                    <select name="TipoSocio[]" id="TipoSocio" title="Tipo Socio" class="form-control chosen-select mandatory" multiple data-placeholder="[Seleccione Tipo Socio]">
                                        <?php
                                        // $sql_TipoSocio = "SELECT ts.IDTipoSocio,ts.Nombre FROM TipoSocio as ts, ClubTipoSocio as cts WHERE ts.IDTipoSocio=cts.IDTipoSocio and ts.Publicar = 'S' and cts.IDClub = '" . SIMUser::get('club') . "'";
                                        $sql_TipoSocio = "SELECT ts.IDTipoSocio,ts.Nombre FROM TipoSocio as ts WHERE ts.Publicar = 'S'";
                                        $q_TipoSocio = $dbo->query($sql_TipoSocio);
                                        $r_TipoSocio = explode('|', $frm['TipoSocio']);
                                        while ($tiposocio = $dbo->object($q_TipoSocio)) {
                                            if ($frm['TipoSocio'] == '') {
                                                $selected = "";
                                            } elseif (in_array($tiposocio->Nombre, $r_TipoSocio)) {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                        ?>
                                            <option value="<?php echo $tiposocio->Nombre ?>" <?php echo $selected; ?>><?php echo $tiposocio->Nombre ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-info btn-sm chosen-selected" rel="TipoSocio">Seleccionar Todos</button>
                                    <button type="button" class="btn btn-danger btn-sm chosen-deselect" rel="TipoSocio">Borrar Todos</button>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado Civil </label>
                                <div class="col-sm-8">
                                    <select name="EstadoCivil[]" id="EstadoCivil" title="Estado Civil" class="form-control chosen-select mandatory" multiple data-placeholder="[Seleccione Estado Civil]" required>
                                        <?php
                                        $EstadoCivil = SIMResources::$estadoCivil;
                                        $r_EstadoCivil = explode('|', $frm['EstadoCivil']);
                                        foreach ($EstadoCivil as $indice => $valor) {
                                            if ($frm['EstadoCivil'] == '') {
                                                $selected = "";
                                            } elseif (in_array($valor, $r_EstadoCivil)) {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                        ?>
                                            <option value="<?php echo $valor ?>" <?php echo $selected; ?>><?php echo $valor ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-info btn-sm chosen-selected" rel="EstadoCivil">Seleccionar Todos</button>
                                    <button type="button" class="btn btn-danger btn-sm chosen-deselect" rel="EstadoCivil">Borrar Todos</button>

                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Edad </label>
                                <div class="col-sm-4" style="padding-right: 0;">
                                    <select name="CampoValidacion" id="CampoValidacion" class="form-control mandatory" title="Campo Validacion">
                                        <?php
                                        $validacion = SIMResources::$ValidacionReglasNegocio;
                                        foreach ($validacion as $indice => $valor) {
                                            $selected = ($frm['CampoValidacion'] == $indice) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $indice; ?>" <?php echo $selected; ?>><?php echo $valor; ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-sm-4" style="padding-left: 0;">
                                    <input type="text" id="Edad" name="Edad" placeholder="Edad" class="form-control mandatory" title="Edad" value="<?php echo $frm["Edad"]; ?>">
                                    <label class="col-sm-12" for="form-field-1">
                                        Edad &oacute; Rango, (delimitado por guion medio '-').
                                    </label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Parentesco </label>
                                <div class="col-sm-8">
                                    <select name="IDParentesco[]" id="IDParentesco" title="Parentesco" class="form-control chosen-select mandatory" multiple data-placeholder="[Seleccione Parentesco]">
                                        <?php
                                        $sql_Parentesco = &$dbo->All('Parentesco', 'Publicar="S"', 'array');
                                        $r_Parentesco = explode('|', $frm['IDParentesco']);
                                        while ($Parentesco = $dbo->object($sql_Parentesco)) {
                                            if ($frm['IDParentesco'] == '') {
                                                $selected = "";
                                            } elseif (in_array($Parentesco->IDParentesco, $r_Parentesco)) {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                        ?>
                                            <option value="<?php echo $Parentesco->IDParentesco ?>" <?php echo $selected; ?>><?php echo $Parentesco->Nombre ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-info btn-sm chosen-selected" rel="IDParentesco">Seleccionar Todos</button>
                                    <button type="button" class="btn btn-danger btn-sm chosen-deselect" rel="IDParentesco">Borrar Todos</button>

                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="ValorPorcentaje"> Valor Porcentaje Cuota Social</label>
                                <div class="col-sm-8"><input type="text" id="ValorPorcentaje" name="ValorPorcentaje" placeholder="" class="col-xs-12 mandatory" title="Valor Porcentaje" value="<?php echo $frm["ValorPorcentaje"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="ValorPorcentajeIncorporacion"> Valor Porcentaje Cuota Incorporac&oacute;n</label>
                                <div class="col-sm-8"><input type="text" id="ValorPorcentajeIncorporacion" name="ValorPorcentajeIncorporacion" placeholder="" class="col-xs-12 mandatory" title="Valor Porcentaje Incorporación" value="<?php echo $frm["ValorPorcentajeIncorporacion"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Prioridad </label>
                                <div class="col-sm-8">
                                    <select name="Prioridad" id="Prioridad" title="Prioridad" class="form-control mandatory" data-placeholder="[Seleccione Prioridad]">
                                        <?php
                                        $r_Prioridad = SIMResources::$PrioridadCategoria;
                                        foreach ($r_Prioridad as $indice => $Prioridad) {
                                            $selected = ($frm['Prioridad'] == $indice) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $indice ?>" <?= $selected; ?>><?= $Prioridad ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>
                                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                            </div>
                        </div>
                        <div class="form-group first ">

                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                            else echo $frm["IDClub"];  ?>" />
                                    <input type="hidden" name="IDAuxilios" id="IDAuxilios" value="<?php echo $frm["IDAuxilios"]; ?>" />
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
                                    </button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
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