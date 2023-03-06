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

                            <div class="form-group first ">
                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">
                                        <div class="col-sm-8">
                                            <!--
                                          <select name = "IDSocio" id="IDSocio" <?php if ($_GET["action"] != "add") echo "disabled"; ?>>
										    <option value=""></option>
										    <?php
                                            $sql_socio_club = "Select * From Socio Where IDClub = '" . SIMUser::get("club") . "' Order by Apellido Asc";
                                            $qry_socio_club = $dbo->query($sql_socio_club);
                                            while ($r_socio = $dbo->fetchArray($qry_socio_club)) : ?>
										    <option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if ($r_socio["IDSocio"] == $frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]); ?></option>
										    <?php
                                            endwhile;    ?>
									      </select>
                                          -->
                                            <?php
                                            $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocioPrestamo"] . "'";
                                            $qry_socio_club = $dbo->query($sql_socio_club);
                                            $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                                            <input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Apellido"] . " " . $r_socio["Nombre"] ?>">
                                            <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Lugar Entrega </label>

                                    <div class="col-sm-8">
                                        <select name="IDLugarObjetosPrestados" id="IDLugarObjetosPrestados">
                                            <option value=""></option>
                                            <?php
                                            $sql_lugar = "Select IDLugarObjetosPrestados,Nombre From LugarObjetosPrestados Where IDClub = '" . SIMUser::get("club") . "'";
                                            $qry_lugar = $dbo->query($sql_lugar);
                                            while ($r_lugar = $dbo->fetchArray($qry_lugar)) : ?>
                                                <option value="<?php echo $r_lugar["IDLugarObjetosPrestados"]; ?>" <?php if ($r_lugar["IDLugarObjetosPrestados"] == $frm["IDLugarObjetosPrestados"]) echo "selected";  ?>><?php echo $r_lugar["Nombre"]; ?></option>
                                            <?php
                                            endwhile;    ?>
                                        </select>
                                    </div>
                                </div>



                            </div>

                            <div class="form-group first ">


                                <div class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CategoriaObjetosPrestados', LANGSESSION); ?> </label>

                                    <div class="col-sm-8">

                                        <select name="IDCategoriaObjetosPrestados" id="IDCategoriaObjetosPrestados">
                                            <option value=""></option>
                                            <?php
                                            $sql_categoria = "Select IDCategoriaObjetosPrestados,NombreCategoriaObjeto From CategoriaObjetosPrestados Where IDClub = '" . SIMUser::get("club") . "'";
                                            $qry_categoria = $dbo->query($sql_categoria);
                                            while ($r_categoria = $dbo->fetchArray($qry_categoria)) : ?>
                                                <option value="<?php echo $r_categoria["IDCategoriaObjetosPrestados"]; ?>" <?php if ($r_categoria["IDCategoriaObjetosPrestados"] == $frm["IDCategoriaObjetosPrestados"]) echo "selected";  ?>><?php echo $r_categoria["NombreCategoriaObjeto"]; ?></option>
                                            <?php
                                            endwhile;    ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label" for="form-field-1">Cantidad prestada</label>
                                    <div class="col-sm-8"><input type="number" id="CantidadPrestada" name="CantidadPrestada" placeholder="" class="form-control mandatory" title="Cantidad Prestada" value="<?php echo $frm["CantidadPrestada"] ?>"></div>

                                </div>
                            </div>
                            <div class="form-group first ">


                            </div>

                            <?php if ($_GET["action"] == "edit") { ?>
                                <div class="form-group first ">
                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label" for="form-field-1">Cantidad pendiente</label>
                                        <div class="col-sm-8"><input type="number" id="CantidadPendiente" name="CantidadPendiente" placeholder="" class="form-control " title="Cantidad Pendiente" value="<?php echo $frm["CantidadPendiente"] ?>"></div>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label" for="form-field-1">Cantidad entregada</label>
                                        <div class="col-sm-8"><input type="number" id="CantidadEntregada" name="CantidadEntregada" placeholder="" class="form-control " title="Cantidad Entregada" value="<?php echo $frm["CantidadEntregada"] ?>"></div>

                                    </div>
                                </div>

                                <div class="form-group first ">
                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></label>
                                        <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="" class="form-control " title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"] ?>"></div>

                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>

                                        <div class="col-sm-8">
                                            <option value=""></option>
                                            <select name="Estado" id="Estado">

                                                <?php $Estados = SIMResources::$estado_objeto_prestado;
                                                foreach ($Estados as $key => $estado) {


                                                ?>
                                                    <option value="<?php echo $key; ?>" <?php if ($key == $frm["Estado"]) echo "selected";  ?>><?php echo $estado; ?></option>

                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>


                                </div>
                                <div class="form-group first ">

                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario que entrega </label>

                                        <div class="col-sm-8">
                                            <div class="col-sm-8">

                                                <?php
                                                $sql_usuario_club = "Select Nombre From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
                                                $qry_usuario_club = $dbo->query($sql_usuario_club);
                                                $r_usuario = $dbo->fetchArray($qry_usuario_club); ?>

                                                <input type="text" id="IDUsuario" name="IDUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 mandatory " title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo  $r_usuario["Nombre"] ?>">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario que recibe</label>

                                        <div class="col-sm-8">
                                            <div class="col-sm-8">

                                                <?php
                                                $sql_usuario_club = "Select Nombre From Usuario Where IDUsuario = '" . $frm["IDUsuarioRecibe"] . "'";
                                                $qry_usuario_club = $dbo->query($sql_usuario_club);
                                                $r_usuario = $dbo->fetchArray($qry_usuario_club); ?>

                                                <input type="text" id="IDUsuarioRecibe" name="IDUsuarioRecibe" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 mandatory " title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo  $r_usuario["Nombre"] ?>">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>






                            <?php if ($_GET["action"] == "add") { ?>
                                <div class="form-group first ">
                                    <div class="clearfix form-actions">
                                        <div class="col-xs-12 text-center">
                                            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                            <input type="hidden" name="AplicaPara" id="AplicaPara" value="S" />
                                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                    else echo $frm["IDClub"];  ?>" />


                                            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                                <i class="ace-icon fa fa-check bigger-110"></i>
                                                <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                                            </button>

                                            <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                            <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
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