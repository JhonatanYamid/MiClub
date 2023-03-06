<div class="widget-box transparent" id="recent-box">
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-glass green"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguraciondeServicios', LANGSESSION); ?>
                            </h3>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-12">


                                <?php

                                // Consulto los servicios disponibles al usuario

                                $sql_servicio_club = $dbo->query("select * from ServicioClub where IDClub = '" . SIMUser::get("club") . "' and Activo = 'S'");
                                while ($r_servicio_club = $dbo->object($sql_servicio_club)) {
                                    $servicio_club[] = $r_servicio_club->IDServicioMaestro;
                                }

                                $arrayop = array();
                                ?>


                                <table id="simple-table" class="table table-striped table-bordered table-hover">
                                    <tr>
                                        <th><?= SIMUtil::get_traduccion('', '', 'Servicio', LANGSESSION); ?></th>
                                        <th><?= SIMUtil::get_traduccion('', '', 'Titulo', LANGSESSION); ?></th>
                                        <th><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></th>
                                    </tr>
                                    <tbody id="listacontactosanunciante">
                                        <?php
                                        $sql_servicio_club = "SELECT * FROM ServicioClub WHERE IDClub = '" . SIMUser::get("club") . "' AND Activo = 'S'";
                                        $r_servicio_club = $dbo->query($sql_servicio_club);
                                        while ($row_servicio_club = $dbo->fetchArray($r_servicio_club)) {
                                            $array_servicio_club[$row_servicio_club["IDServicioMaestro"]] = $row_servicio_club;
                                        }

                                        $r_servicioclub = &$dbo->all("ServicioMaestro", "Publicar = 'S' Order by Nombre");

                                        while ($r = $dbo->object($r_servicioclub)) {
                                            if ($array_servicio_club[$r->IDServicioMaestro]["Activo"] == 'S') {
                                        ?>
                                                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">

                                                    <input type="hidden" name="IDServicioMaestro<?php echo $r->IDServicioMaestro; ?>" id="IDServicioMaestro<?php echo $r->IDServicioMaestro; ?>" value="<?php echo $array_servicio_club[$r->IDServicioMaestro]["Activo"]; ?>">


                                                    <td>
                                                        <?php
                                                        echo $r->Nombre;
                                                        if (!empty($r->Descripcion))
                                                            echo  " (" . $r->Descripcion . ")";
                                                        ?>
                                                    </td>
                                                    <td><input id=TituloServicio<?php echo $r->IDServicioMaestro; ?> type=text size=25 name=TituloServicio<?php echo $r->IDServicioMaestro; ?> class="col-xs-12" title="Titulo Servicio" value="<?php echo $array_servicio_club[$r->IDServicioMaestro]["TituloServicio"]; ?>"></td>
                                                    <td>
                                                        <input id=OrdenServicio<?php echo $r->IDServicioMaestro; ?> type=text size=25 name=OrdenServicio<?php echo $r->IDServicioMaestro; ?> class="col-xs-12" title="Orden" value="<?php echo $array_servicio_club[$r->IDServicioMaestro]["Orden"]; ?>">
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                    <tr>
                                        <th class="texto" colspan="14"></th>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-sitemap green"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguraciondeModulos', LANGSESSION); ?>
                            </h3>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-12">
                                <?php
                                // Consulto los modulos disponibles del club
                                $sql_modulo_club = $dbo->query("select * from ClubModulo where IDClub = '" . SIMUser::get("club") . "' and Activo = 'S'");
                                while ($r_modulo_club = $dbo->object($sql_modulo_club)) {
                                    $modulo_club[] = $r_modulo_club->IDModulo;
                                }

                                // Consulto los modulos disponibles del club
                                $sql_modulo = $dbo->query("select * from Modulo where 1");
                                while ($r_modulo = $dbo->object($sql_modulo)) {
                                    $modulo_datos[$r_modulo->IDModulo] = $r_modulo->Nombre;
                                }
                                ?>
                                <table id="simple-table" class="table table-striped table-bordered table-hover">
                                    <tr>
                                        <th><?= SIMUtil::get_traduccion('', '', 'Modulo', LANGSESSION); ?></th>
                                        <th><?= SIMUtil::get_traduccion('', '', 'TituloClub', LANGSESSION); ?></th>
                                        <th><?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?></th>
                                        <th><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?></th>
                                        <th><?= SIMUtil::get_traduccion('', '', 'Ubicacion', LANGSESSION); ?></th>
                                    </tr>
                                    <tbody id="listacontactosanunciante">
                                        <?php

                                        $r_modulo = &$dbo->all("ClubModulo", "IDClub = '" . SIMUser::get("club") . "' AND Activo = 'S'");

                                        while ($r = $dbo->object($r_modulo)) {
                                        ?>

                                            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">

                                                <input type="hidden" name="IDModulo<?php echo $r->IDModulo; ?>" id="IDModulo<?php echo $r->IDModulo; ?>" value="<?php echo $r->Activo; ?>">

                                                <td><?php echo $modulo_datos[$r->IDModulo]; ?></td>
                                                <td>
                                                    <input id=Titulo<?php echo $r->IDModulo; ?> type=text size=25 name=Titulo<?php echo $r->IDModulo; ?> class="col-xs-12" title="Titulo" value="<?= $r->Titulo ?>" placeholder="Titulo Menu Central">
                                                    <input id=TituloLateral<?php echo $r->IDModulo; ?> type=text size=25 name=TituloLateral<?php echo $r->IDModulo; ?> class="col-xs-12" title="Titulo Lateral" value="<?= $r->TituloLateral ?>" placeholder="Titulo menu lateral">
                                                </td>
                                                <td>
                                                    <? if (!empty($r->Icono)) {
                                                        echo "<img src='" . MODULO_ROOT . "$r->Icono' width=55 >";
                                                    ?>
                                                        <a href="<? echo $script . " .php?action=delfotomodulo&foto=$r->Icono&campo=Icono&idclubmodulo=" . $r->IDClubModulo; ?>&id=<?php echo SIMUser::get("club"); ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                    <?
                                                    } // END if
                                                    ?>
                                                    <input name="Icono<?php echo $r->IDModulo; ?>" id=Icono<?php echo $r->IDModulo; ?> class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                                    <input type="hidden" name="ImagenOriginal<?php echo $r->IDModulo; ?>" id="ImagenOriginal<?php echo $r->IDModulo; ?>" value="<?php echo $r->Icono; ?>">

                                                    <?php
                                                    if (SIMUser::get("club") == "51") {
                                                        if (!empty($r->IconoLateral)) {
                                                            echo "<img src='" . MODULO_ROOT . "$r->IconoLateral' width=55 >";
                                                    ?>
                                                            <a href="<? echo $script . " .php?action=delfotomodulo&foto=$r->IconoLateral&campo=IconoLateral&idclubmodulo=" . $r->IDClubModulo; ?>&id=<?php echo SIMUser::get("club"); ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                        <?
                                                        } // END if
                                                        ?>
                                                        <br>Icono lateral<input name="IconoLateral<?php echo $r->IDModulo; ?>" id=IconoLateral<?php echo $r->IDModulo; ?> class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                                        <input type="hidden" name="ImagenOriginalLateral<?php echo $r->IDModulo; ?>" id="ImagenOriginalLateral<?php echo $r->IDModulo; ?>" value="<?php echo $r->IconoLateral; ?>">
                                                    <?php } ?>
                                                </td>



                                                <td><input id=Orden<?php echo $r->IDModulo; ?> type=text size=25 name=Orden<?php echo $r->IDModulo; ?> class="col-xs-12" title="Orden" value="<?= $r->Orden ?>"></td>
                                                <td>
                                                    <?php
                                                    unset($ubicacion_modulo);
                                                    if (!empty($r->Ubicacion)) :
                                                        $ubicacion_modulo = explode("|", $r->Ubicacion);
                                                    endif;
                                                    ?>

                                                    <input type="checkbox" name="UbicacionModulo<?php echo $r->IDModulo; ?>[]" <?php if (in_array("Lateral", $ubicacion_modulo)) echo "checked"; ?> value="Lateral"> Menu Lateral app
                                                    <br><input type="checkbox" name="UbicacionModulo<?php echo $r->IDModulo; ?>[]" <?php if (in_array("Central", $ubicacion_modulo)) echo "checked"; ?> value="Central">Menu central app
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                    <tr>
                                        <th class="texto" colspan="16"></th>
                                    </tr>
                                </table>


                            </div>



                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                                </button>


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