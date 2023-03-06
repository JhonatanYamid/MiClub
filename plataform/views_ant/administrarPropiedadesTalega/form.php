<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>ADMINISTRAR 
            <?php echo strtoupper(SIMReg::get("title")); ?>
        </h4>


    </div>
<?php include( "cmp/footer_scripts.php" ); ?>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-md-12">   
                        <?php
                        include ($view1);
                        ?>
                </div>
            </div>
            

            
            <hr>
            
             <div class="row">
                <div class="col-md-12">   
                        <?php
                        include ($view2);
                        ?>
                </div>
            </div>
            <?php

//include( "cmp/footer_grid.php" );
?>

        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

