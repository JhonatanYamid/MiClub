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
                    <!-- PAGE CONTENT BEGINS -->


                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>" <?php if (SIMUser::get("IDPerfil") == 86) echo "readonly"; ?>>
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> " <?php if (SIMUser::get("IDPerfil") == 86) echo "readonly"; ?>><?php echo $frm["Descripcion"]; ?></textarea>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'precio', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="Precio" name="Precio" placeholder="<?= SIMUtil::get_traduccion('', '', 'precio', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'precio', LANGSESSION); ?>" value="<?php echo $frm["Precio"]; ?>" <?php if (SIMUser::get("IDPerfil") == 86) echo "readonly"; ?>>
                                </div>
                            </div>



                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 1 </label>
                                <?php if (SIMUser::get("IDPerfil") != 86) { ?>
                                    <input name="Foto1" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
                                <?php } ?>
                                <div class="col-sm-8">
                                    <? if (!empty($frm["Foto1"])) {
                                        echo "<img src='" . IMGPRODUCTO_ROOT . $frm["Foto1"] . "' height='300px' width='300px' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 2 </label>
                                <?php if (SIMUser::get("IDPerfil") != 86) { ?>
                                    <input name="Foto2" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 2" type="file" size="25" style="font-size: 10px">
                                <?php } ?>
                                <div class="col-sm-8">
                                    <? if (!empty($frm["Foto2"])) {
                                        echo "<img src='" . IMGPRODUCTO_ROOT . $frm["Foto2"] . "' height='300px' width='300px' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto2&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 3 </label>
                                <?php if (SIMUser::get("IDPerfil") != 86) { ?>
                                    <input name="Foto3" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 3" type="file" size="25" style="font-size: 10px">
                                <?php } ?>
                                <div class="col-sm-8">
                                    <? if (!empty($frm["Foto3"])) {
                                        echo "<img src='" . IMGPRODUCTO_ROOT . $frm["Foto3"] . "' height='300px' width='300px' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto3]&campo=Foto3&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 4 </label>
                                <?php if (SIMUser::get("IDPerfil") != 86) { ?>
                                    <input name="Foto4" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 4" type="file" size="25" style="font-size: 10px">
                                <?php } ?>
                                <div class="col-sm-8">
                                    <? if (!empty($frm["Foto4"])) {
                                        echo "<img src='" . IMGPRODUCTO_ROOT . $frm["Foto4"] . "' height='300px' width='300px'>";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto4]&campo=Foto4&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 5 </label>
                                <?php if (SIMUser::get("IDPerfil") != 86) { ?>
                                    <input name="Foto5" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 5" type="file" size="25" style="font-size: 10px">
                                <?php } ?>
                                <div class="col-sm-8">
                                    <? if (!empty($frm["Foto5"])) {
                                        echo "<img src='" . IMGPRODUCTO_ROOT . $frm["Foto5"] . "' height='300px' width='300px' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto5]&campo=Foto5&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 6 </label>
                                <?php if (SIMUser::get("IDPerfil") != 86) { ?>
                                    <input name="Foto6" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 6" type="file" size="25" style="font-size: 10px">
                                <?php } ?>
                                <div class="col-sm-8">
                                    <? if (!empty($frm["Foto6"])) {
                                        echo "<img src='" . IMGPRODUCTO_ROOT . $frm["Foto6"] . "' height='300px' width='300px' >";
                                    ?>
                                        <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto6]&campo=Foto6&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Existencias', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="Existencias" name="Existencias" placeholder="<?= SIMUtil::get_traduccion('', '', 'Existencias', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Existencias', LANGSESSION); ?>" value="<?php echo $frm["Existencias"]; ?>">
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Proveedor', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Proveedor" name="Proveedor" placeholder="<?= SIMUtil::get_traduccion('', '', 'Proveedor', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Proveedor', LANGSESSION); ?>" value="<?php echo $frm["Proveedor"]; ?>" <?php if (SIMUser::get("IDPerfil") == 86) echo "readonly"; ?>>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Permitecomentariosalpedir', LANGSESSION); ?>? </label>

                                <div class="col-sm-8">
                                    <?php if (SIMUser::get("IDPerfil") != 86) { ?>
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteComentarios"], 'PermiteComentarios', "class='input mandatory'") ?>
                                    <?php }
                                      ?>
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" value="<?php echo $frm["Orden"]; ?>" <?php if (SIMUser::get("IDPerfil") == 86) echo "readonly"; ?>>
                                </div>
                            </div>



                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Horainicioproductodisponible', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="time" id="HoraInicioDisponible" name="HoraInicioDisponible" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraInicioEntrega', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraInicioEntrega', LANGSESSION); ?>" value="<?php echo $frm["HoraInicioDisponible"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Horafinproductodisponible', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="time" id="HoraFinDisponible" name="HoraFinDisponible" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraFinEntrega', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraFinEntrega', LANGSESSION); ?>" value="<?php echo $frm["HoraFinDisponible"]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-12">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DiasDomicilios', LANGSESSION); ?></label>

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

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="IDProductoExterno" name="IDProductoExterno" placeholder="<?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'codigo', LANGSESSION); ?>" value="<?php echo $frm["IDProductoExterno"]; ?>">
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <?php if (SIMUser::get("IDPerfil") != 86) { ?>
                                        <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                    <?php } ?>
                                </div>
                            </div>

                        </div>

                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-globe green"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>
                            </h3>
                        </div>


                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-12">


                                <div class="col-sm-12">

                                    <?php
                                    // Consulto las categorias disponibles del club
                                    $sql_categoria_producto = $dbo->query("select * from ProductoCategoria4 where IDProducto = '" . $frm["IDProducto"] . "'");
                                    while ($r_categoria_producto = $dbo->object($sql_categoria_producto)) {
                                        $categoria_producto[] = $r_categoria_producto->IDCategoriaProducto;
                                    }
                                    $arrayop = array();
                                    // consulto los modulos
                                    $query_categoria = $dbo->query("Select * from CategoriaProducto4 Where IDClub = '" . SIMUser::get("club") . "' Order by Nombre");
                                    while ($r = $dbo->object($query_categoria)) {
                                        $nombre_categoria = $r->Nombre;
                                        $arraycategorias[$nombre_categoria] = $r->IDCategoriaProducto;
                                    }
                                    echo SIMHTML::formCheckGroup($arraycategorias, $categoria_producto, "CategoriaProducto[]", "&nbsp;"); ?>


                                </div>
                            </div>
                        </div>



                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-globe green"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Caracteristicas', LANGSESSION); ?>
                            </h3>
                        </div>


                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-12">


                                <div class="col-sm-12">

                                    <?php
                                    // Consulto las categorias disponibles del club
                                    $sql_carac_producto = $dbo->query("SELECT * from ProductoCaracteristica where IDProducto = '" . $frm["IDProducto"] . "'");
                                    while ($r_carac_producto = $dbo->object($sql_carac_producto)) {
                                        $carac_producto[] = $r_carac_producto->IDCaracteristicaProducto;
                                    }

                                    $arrayop = array();
                                    // consulto los modulos

                                    $query_carac = "SELECT CP.*,PP.Nombre as Categoria
																		FROM CaracteristicaProducto CP, PropiedadProducto PP
																		WHERE CP.IDPropiedadProducto = PP.IDPropiedadProducto
																		And CP.IDClub = '" . SIMUser::get("club")  . "' and PP.Version = 4
																		ORDER BY CP.Orden ASC,PP.Nombre,CP.Nombre ";
                                    ?>

                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                        <tr>
                                            <td>
                                                <?php
                                                $Nombre_cat = "";
                                                $contador_prop = 0;
                                                $r_carac = $dbo->query($query_carac);
                                                while ($r = $dbo->object($r_carac)) {

                                                    if ($Nombre_cat != $r->Categoria) {
                                                        echo "</td><td>";
                                                        echo "<b>" . $r->Categoria . "</b>" . "<br>";
                                                        $Nombre_cat = $r->Categoria;
                                                        $contador_prop++;
                                                        //echo "</td><td>";
                                                    }
                                                    if (in_array($r->IDCaracteristicaProducto, $carac_producto)) {
                                                        $seleccionado = "checked";
                                                    } else {
                                                        $seleccionado = "";
                                                    }
                                                    echo "<input type='checkbox' name='CaracteristicaProducto[]' id='CaracteristicaProducto[]' value='" . $r->IDCaracteristicaProducto . "' " . $seleccionado . ">&nbsp;" . $nombre_categoria = $r->Nombre . " Valor: $" . $r->Valor;
                                                    echo "<br>";
                                                }
                                                ?>


                                        </tr>
                                    </table>



                                </div>
                            </div>
                        </div>





                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                        else echo $frm["IDClub"];  ?>" />
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