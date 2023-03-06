<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO 
            <?php echo strtoupper(SIMReg::get("title")); ?>
        </h4>


    </div>

    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->


                    <div class="col-sm-12">
                        <?php
                                    if ($_GET["editarinfo"] != "n"): //condicion para cuando en porteria se ingresa a esta pantalla
                                        include ("crearEditar.php");
                                    endif;
                                    ?>
                    </div>

                </div>
            </div>


        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include( "cmp/footer_scripts.php" );
?>