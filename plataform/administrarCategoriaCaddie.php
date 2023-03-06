<?php
/* ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL); */
include( "procedures/general.php" );
include( "procedures/administrarCategoriaCaddie.php" );
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


                    <?php
                    SIMNotify::each();
                    ?>
                    <!--						
                    
                                        
                    
                                        <div class="page-header">
<?php include("cmp/migapan.php"); ?>
                    
                                        </div><!-- /.page-header -->

                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="widget-box transparent" id="recent-box">
                                        <div class="widget-header">
                                            <h4 class="widget-title lighter smaller">
                                                <i class="ace-icon fa fa-users orange"></i>CONSULTAR 
<?php //echo $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];  ?>
                                            </h4>


                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-4">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <!-- PAGE CONTENT BEGINS -->
                                                        <form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

                                                            <div class="col-xs-12 col-sm-8">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon">
                                                                        <i class="ace-icon fa fa-check"></i>
                                                                    </span>

                                                                    <input type="text" name="qryString" class="form-control search-query "  placeholder="Ingrese el nombre de la categoría">
                                                                    <input type="hidden" name="action" value="search">
                                                                    <span class="input-group-btn">

                                                                        <button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
                                                                            <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                                                            Buscar Categoría
                                                                        </button>
                                                                    </span>
                                                                    <span class="input-group-btn">

                                                                        <button type="button" class="btn btn-primary btn-sm btnRedirect" rel="administrarCategoriaCaddie.php?action=search">
                                                                            Ver Todas
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>

                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div>
                                    <!-- /.widget-box -->
<?php
include( $view );
?>
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
