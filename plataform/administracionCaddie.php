<?php
include( "procedures/general.php" );
include( "procedures/administrarCaddie.php" );
include( "cmp/seo.php" );
?>
</head>

<body class="no-skin">


    <?php
    include( "cmp/header.php" );
    ?>


    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {
            }
        </script>

        <?php
        $menu_home = " class=\"active\" ";
        include( "cmp/menu.php" );
        ?>

        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs" id="breadcrumbs">
                    <script type="text/javascript">
                        try {
                            ace.settings.check('breadcrumbs', 'fixed')
                        } catch (e) {
                        }
                    </script>


                    <?php include("cmp/breadcrumb.php"); ?>




                </div>
                <div class="page-content">
                  

                    <div class="page-header">
                        <?php include("cmp/migapan.php"); ?>

                    </div><!-- /.page-header -->

                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="widget-box transparent" id="recent-box">
                                       


<div class="row">
    <div class="col-md-4" style="height: 300px;border: #CCC 1px solid;padding:140px;text-align: center;">
        <a href="registrarCaddie.php" >Registrar caddie</a>
    </div>
    <div class="col-md-4" style="height: 300px;border: #CCC 1px solid;padding:140px;text-align: center;">
        <a href="sorteoCaddie.php" >Sorteo Caddie</a>
    </div>
    <div class="col-md-4" style="height: 300px;border: #CCC 1px solid;padding:140px;text-align: center;">
        <a href="caddiesDisponibles.php" >Caddies disponibles</a>
    </div>
</div>

                                      
                                    </div>
                                  
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->

        <?php
        include("cmp/footer.php");
        ?>
    </div><!-- /.main-container -->


</body>
</html>
