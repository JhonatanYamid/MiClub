<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>

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
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pregunta', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Pregunta" name="Pregunta" placeholder="<?= SIMUtil::get_traduccion('', '', 'Pregunta', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Pregunta"]; ?>">
                                </div>
                            </div>


                            <!-- <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Respuesta </label>

                                <div class="col-sm-8">
                                    <textarea id="Respuesta" name="Respuesta" cols="10" rows="5" class="col-xs-12 mandatory" title="Respuesta"><?php echo $frm["Respuesta"]; ?></textarea>
                                </div>
                            </div> -->

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Respuesta', LANGSESSION); ?></label>

                                <div class="col-sm-8">

                                    <?php
                                    $oCuerpo = new FCKeditor("Respuesta");
                                    $oCuerpo->BasePath = "js/fckeditor/";
                                    $oCuerpo->Height = 300;
                                    $oCuerpo->Width = 300;
                                    //$oCuerpo->EnterMode = "p";
                                    $oCuerpo->Value =  $frm["Respuesta"];
                                    $oCuerpo->Create();
                                    ?>

                                </div>
                            </div>

                        </div>






                        <div class="form-group first ">


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12" title="Orden" value="<?php echo $frm["Orden"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">


                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'VotosFueUtil', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <? echo $frm["VotosUtil"]; ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'VotosnofueUtil', LANGSESSION); ?></label>

                                <div class="col-sm-8">
                                    <? echo $frm["VotosNoUtil"]; ?>
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
                                    $sql_categoria_producto = $dbo->query("select * from FaqCategoria where IDFaq = '" . $frm["IDFaq"] . "'");
                                    while ($r_categoria_producto = $dbo->object($sql_categoria_producto)) {
                                        $categoria_producto[] = $r_categoria_producto->IDCategoriaFaq;
                                    }
                                    $arrayop = array();
                                    // consulto los modulos
                                    $query_categoria = $dbo->query("Select * from CategoriaFaq Where IDClub = '" . SIMUser::get("club") . "' Order by Nombre");
                                    while ($r = $dbo->object($query_categoria)) {

                                        $nombre_categoria = $r->Nombre;
                                        $arraycategorias[$nombre_categoria] = $r->IDCategoriaFaq;
                                    }
                                    echo SIMHTML::formCheckGroup($arraycategorias, $categoria_producto, "CategoriaFaq[]", "&nbsp;"); ?>


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