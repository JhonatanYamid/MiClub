<?
include("procedures/general.php");
include("procedures/facturacionsocios.php");
include("cmp/seo.php");
?>
<style>

</style>
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
                        <h1>
                            <a href="<?= $miga_home ?>">Home</a>
                            <small>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                <?= $array_clubes[SIMUser::get("club")]["Nombre"] ?>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                <a href="<?= $script . ".php" ?>"><?php echo SIMReg::get("title") ?></a>
                            </small>

                        </h1>
                    </div><!-- /.page-header -->

                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
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
    //	include( "cmp/footer_scripts.php" );
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