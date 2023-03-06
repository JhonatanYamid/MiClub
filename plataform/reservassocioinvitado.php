<?
include("procedures/general.php");
include("procedures/reservassocioinvitado.php");
include("cmp/seo.php");
?>
</head>

<body class="no-skin">
    <?
    include("cmp/header.php");
    ?>
    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {}
        </script>
        <?
        $menu_home = " class=\"active\" ";
        include("cmp/menu.php");
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
                                            <h3 class="widget-title lighter smaller">
                                                Reporte que genera las reservas de todos los socios donde la haya realizado el socio o donde haya sido invitado.
                                            </h3>
                                        </div>
                                        <div class="widget-header">
                                            <h4 class="widget-title lighter smaller">
                                                <i class="ace-icon fa fa-users orange"></i>Consultar Reservas
                                            </h4>



                                        </div>

                                        <div>



                                            <form name="frmexportareserva" id="frmexportareserva" method="post" enctype="multipart/form-data" action="procedures/excel-reservassocioinvitado.php">

                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>

                                                    <div class="col-sm-8">
                                                        <input type="date" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 " title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>

                                                    <div class="col-sm-8">
                                                        <input type="date" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 " title="fecha fin" value="<?php echo date("Y-m-d") ?>">
                                                    </div>
                                                </div>

                                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                                                <br><br>
                                                <br><br>

                                                <input class="btn btn-info" type="submit" name="expbnr" id="expbnr" value="Exportar">

                                                <!--     <button class="btn btn-info btnEnviar" type="submit" rel="frm">
                                                    <i class="ace-icon fa fa-cloud-download bigger-110"></i>
                                                    Exportar
                                                </button> -->


                                            </form>
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-4">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <!-- PAGE CONTENT BEGINS -->

                                                    </div>
                                                </div>
                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->
                                    <?
                                    include($view);
                                    ?>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->

        <?
        include("cmp/footer.php");
        ?>
        <script>
            $('#gritter-center').on(ace.click_event, function() {
                $.gritter.add({
                    title: 'This is a centered notification',
                    text: 'Just add a "gritter-center" class_name to your $.gritter.add or globally to $.gritter.options.class_name',
                    class_name: 'gritter-info gritter-center' + (!$('#gritter-light').get(0).checked ? ' gritter-light' : '')
                });

                return false;
            });
        </script>
    </div><!-- /.main-container -->
</body>

</html>