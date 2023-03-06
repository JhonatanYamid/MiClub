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
        } catch (e) {}
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
                    } catch (e) {}
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
                                        <div class="widget-header">
                                            <h4 class="widget-title lighter smaller">
                                                <i class="ace-icon fa fa-users orange"></i>EXPORTAR ASIGNACIÓN Caddies
                                            </h4>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main padding-4">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <!-- PAGE CONTENT BEGINS -->
                                                        <form class="form-horizontal formvalida" role="form" method="get" name="frm1" id="frm1" action="procedures/excel-caddies.php" enctype="multipart/form-data">
                                                            <h3>Descargar historico de asignacion de caddies</h3>
                                                            <div class="form-group first ">
                                                                <div class="col-xs-12 col-sm-6">
                                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>

                                                                    <div class="col-sm-8">
                                                                        <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xs-12 col-sm-6">
                                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>

                                                                    <div class="col-sm-8">
                                                                        <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d") ?>">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                                                            <input type="hidden" name="Tipo"  value="Historico">

                                                            <button class="btn btn-info btnEnviar" type="button" rel="frm1">
                                                                <i class="ace-icon fa fa-cloud-download bigger-110"></i>
                                                                Exportar
                                                            </button>
                                                        </form>

                                                        <form class="form-horizontal formvalida" role="form" method="get" name="frm2" id="frm2" action="procedures/excel-caddies.php" enctype="multipart/form-data">
                                                            <h3>Descargar caddies en sorteo para las fechas</h3>
                                                            <div class="form-group first ">
                                                                <div class="col-xs-12 col-sm-6">
                                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>

                                                                    <div class="col-sm-8">
                                                                        <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xs-12 col-sm-6">
                                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>

                                                                    <div class="col-sm-8">
                                                                        <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d") ?>">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                                                            <input type="hidden" name="Tipo"  value="Sorteo">

                                                            <button class="btn btn-info btnEnviar" type="button" rel="frm2">
                                                                <i class="ace-icon fa fa-cloud-download bigger-110"></i>
                                                                Exportar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div>
                                    <!-- /.widget-box -->                                  
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->

        <?php
        include("cmp/footer_scripts.php");
        ?>
    </div><!-- /.main-container -->


</body>

</html>