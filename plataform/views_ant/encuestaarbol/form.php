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


                    <div class="col-sm-12">
                        <div class="tabbable">
                            <ul class="nav nav-tabs" id="myTab">
                                <li class="<?php if (empty($_GET[tabencuesta])) echo "active"; ?>">
                                    <a data-toggle="tab" href="#home">
                                        <i class="green ace-icon fa fa-home bigger-120"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'DatosGenerales', LANGSESSION); ?>
                                    </a>
                                </li>

                                <?php if (SIMNet::req("action") == "edit") : ?>
                                    <li class="<?php if ($_GET[tabencuesta] == "formulario") echo "active"; ?>">
                                        <a data-toggle="tab" href="#messages">
                                            <i class="green ace-icon fa fa-check-circle bigger-120"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Preguntas', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="<?php if ($_GET[tabencuesta] == "notificacionlocal") echo "active"; ?>">
                                        <a data-toggle="tab" href="#notificacion">
                                            <i class="green ace-icon fa fa-bell bigger-120"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Notificaciones', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="<?php if ($_GET[tabencuesta] == "registros") echo "active"; ?>">
                                        <a data-toggle="tab" href="#invitaciones">
                                            <i class="green ace-icon fa fa-ticket  bigger-120"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Respuestas', LANGSESSION); ?>
                                        </a>
                                    </li>

                                    <!-- <li class="<?php if ($_GET[tabencuesta] == "funcionarios") echo "active"; ?>">
                                    <a data-toggle="tab" href="#funcionarios">
                                        <i class="green ace-icon fa fa-ticket  bigger-120"></i>
                                        Respuestas Funcionario
                                    </a>
                                </li> -->

                                <?php endif; ?>

                            </ul>

                            <div class="tab-content">
                                <div id="home" class="tab-pane fade <?php if (empty($_GET[tabencuesta])) echo "in active"; ?> ">
                                    <?php include("encuesta.php"); ?>
                                </div>

                                <?php if (SIMNet::req("action") == "edit") : ?>

                                    <div id="messages" class="tab-pane fade <?php if ($_GET[tabencuesta] == "formulario") echo "in active"; ?>">
                                        <?php include("preguntas.php"); ?>
                                    </div>
                                    <div id="notificacion" class="tab-pane fade <?php if ($_GET[tabencuesta] == "notificacionlocal") echo "in active"; ?>">
                                        <?php include("notificacioneslocales.php"); ?>
                                    </div>
                                    <div id="invitaciones" class="tab-pane fade <?php if ($_GET[tabencuesta] == "registros") echo "in active"; ?>">
                                        <?php include("registro.php"); ?>
                                    </div>
                                    <div id="funcionarios" class="tab-pane fade <?php if ($_GET[tabencuesta] == "funcionarios") echo "in active"; ?>">
                                        <?php include("registrofuncionario.php"); ?>
                                    </div>

                                <?php endif; ?>

                            </div>
                        </div>
                    </div>



                </div>
            </div>




        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>