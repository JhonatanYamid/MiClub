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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de respuesta </label>

                                <div class="col-sm-8">
                                    <select class="form-control" id="Tipo" name="Tipo">
                                        <optgroup label="Estándar">
                                            <option value="text" <?php if ($frm["Tipo"] == "text") echo "selected"; ?>>Texto en una línea</option>
                                            <option value="textarea" <?php if ($frm["Tipo"] == "textarea") echo "selected"; ?>>Texto en párrafo</option>
                                            <option value="radio" <?php if ($frm["Tipo"] == "radio") echo "selected"; ?>>Múltiples opciones</option>
                                            <option value="checkbox" <?php if ($frm["Tipo"] == "checkbox") echo "selected"; ?>>Casillas de verificación</option>
                                            <option value="select" <?php if ($frm["Tipo"] == "select") echo "selected"; ?>>Menú desplegable</option>
                                            <option value="number" <?php if ($frm["Tipo"] == "number") echo "selected"; ?>>Número</option>
                                            <option value="rating" <?php if ($frm["Tipo"] == "rating") echo "selected"; ?>>Estrellas</option>
                                            <!--<option value="page">Page Break</option>-->
                                        </optgroup>
                                        <optgroup label="Elegantes">
                                            <option value="date" <?php if ($frm["Tipo"] == "date") echo "selected"; ?>>Fecha</option>
                                            <option value="time" <?php if ($frm["Tipo"] == "time") echo "selected"; ?>>Hora</option>
                                            <option value="email" <?php if ($frm["Tipo"] == "email") echo "selected"; ?>>Correo electrónico</option>
                                        </optgroup>
                                        <optgroup label="Titulo">
                                            <option value="titulo" <?php if ($frm["Tipo"] == "titulo") echo "selected"; ?>>Titulo</option>

                                    </select>
                                </div>
                            </div>
                        </div>



                        <div class="form-group first ">


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Opciones de respuesta (separados por coma)</label>

                                <div class="col-sm-8">
                                    <textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Valores"><?php echo $frm["Valores"]; ?></textarea>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Orden </label>

                                <div class="col-sm-8">
                                    <input type="number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $frm["Orden"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio </label>

                                <div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Obligatorio"], 'Obligatorio', "class='input mandatory'") ?></div>
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