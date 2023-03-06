<?
include("procedures/general.php");
include("procedures/reportehistorialsocios.php");
include("cmp/seo.php");
?>
<style>
    .bs-example {
        font-family: sans-serif;
        position: relative;
        margin: 100px;
    }

    .typeahead,
    .tt-query,
    .tt-hint {
        border: 2px solid #CCCCCC;
        border-radius: 8px;
        font-size: 14px !important;
        /* Set input font size */
        height: 30px;
        line-height: 22px;
        outline: medium none;
        padding: 8px 12px;
        width: 396px;
    }

    .typeahead {
        background-color: #FFFFFF;
    }

    .typeahead:focus {
        border: 2px solid #0097CF;
    }

    .tt-query {
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    }

    .tt-hint {
        color: #999999;
    }

    .tt-menu {
        background-color: #FFFFFF;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        margin-top: 12px;
        padding: 8px 0;
        width: 422px;
    }

    .tt-suggestion {
        font-size: 13px;
        /* Set suggestion dropdown font size */
        padding: 3px 20px;
        line-height: 16px;
    }

    .tt-suggestion:hover {
        cursor: pointer;
        background-color: #0097CF;
        color: #FFFFFF;
    }

    .tt-suggestion p {
        margin: 0;
    }
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