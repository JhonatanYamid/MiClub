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

                    <div class="col-sm-12">
                        <div class="tabbable">
                            <ul class="nav nav-tabs" id="myTab">
                                <li class="<?php if (empty($_GET[tabcorrespondencia])) echo "active"; ?>">
                                    <a data-toggle="tab" href="#home">
                                        <i class="green ace-icon fa fa-gavel bigger-120"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Correspondencia', LANGSESSION); ?>
                                    </a>
                                </li>
                                <?php if (SIMNet::req("action") == "add") : ?>
                                    <li class="<?php if ($_GET[carga] == "carga") echo "active"; ?>">
                                        <a data-toggle="tab" href="#messages">
                                            <i class="green ace-icon fa fa-comments-o bigger-120"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Cargamasivacorrespondencia', LANGSESSION); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <div class="tab-content">
                                <div id="home" class="tab-pane fade <?php if (empty($_GET[tabcorrespondencia])) echo "in active"; ?> ">
                                    <?php include("datos1.php"); ?>
                                </div>

                                <?php if (SIMNet::req("action") == "add") : ?>
                                    <div id="messages" class="tab-pane fade <?php if ($_GET[carga] == "carga") echo "in active"; ?>">
                                        <?php include("cargamasiva.php"); ?>
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
