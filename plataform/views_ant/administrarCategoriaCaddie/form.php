<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR NUEVA CATEGORÍA
            <?php //echo strtoupper(SIMReg::get("title")); ?>
        </h4>


    </div>

    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <!-- PAGE CONTENT BEGINS -->


                <div class="col-md-12">
                    <?php
                    include ($view1);
                    ?>
                </div>
            </div>
            <?php
            include( "cmp/footer_scripts.php" );
            ?>                   
            <hr>

            <div class="row">
                <div class="col-dms-12">
                    <?php
                    include ($view2);
                    ?>
                </div>

            </div>


        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

