<?
include("procedures/general.php");
include("procedures/reportetalonerasocio.php");
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



                    <?
                    SIMNotify::each();
                    ?>


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
                                                <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'Filtrar', LANGSESSION); ?>
                                            </h4>


                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-4">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <!-- PAGE CONTENT BEGINS -->


                                                        <form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

                                                            <div class="col-xs-12 col-sm-12">



                                                                <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                                    <tr>
                                                                        <td>Numero Documento </td>
                                                                        <td>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" title="IDSocio">
                                                                            </div>
                                                                        </td>
                                                                        <td><?= SIMUtil::get_traduccion('', '', 'Desde', LANGSESSION); ?></td>
                                                                        <td>
                                                                            <input type="text" id="FechaDesde" name="FechaDesde" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaDesde', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaDesde', LANGSESSION); ?>" value="<?php echo $frm["FechaDesde"] ?>">
                                                                        </td>

                                                                        <td><?= SIMUtil::get_traduccion('', '', 'Hasta', LANGSESSION); ?></td>
                                                                        <td>
                                                                            <input type="text" id="FechaHasta" name="FechaHasta" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaHasta', LANGSESSION); ?>" class="col-xs-12 calendar" title="Fecha Hasta" value="<?php echo $frm["FechaHasta"] ?>">
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td colspan="6" align="center">

                                                                            <input type="hidden" name="action" id="action" value="search">
                                                                            <span class="input-group-btn">

                                                                                <button type="button" class="btn btn-purple btn-sm btnEnviar" id="send" rel="frmfrmBuscar">
                                                                                    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                                                                    <?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?>
                                                                                </button>


                                                                            </span>


                                                                        </td>
                                                                    </tr>
                                                                </table>


                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>




                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->






                                </div><!-- /.col -->


                            </div><!-- /.row -->

                            <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->

        <?
        include("cmp/footer_scripts.php");

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




            document.getElementById('send').addEventListener('click', (e) => {
                $('#action').val('Exportar');
            })
        </script>



    </div><!-- /.main-container -->


</body>

</html>