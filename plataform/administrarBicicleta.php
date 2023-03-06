<?php
include("procedures/general.php");
include("procedures/administrarBicicleta.php");
include("cmp/seo.php");
?>
</head>

<body class="no-skin">


    <?php
    include("cmp/header.php");
    ?>


    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {}
        </script>

        <?php
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

                    <div class="page-content">


                        <?php
                        SIMNotify::each();

                        //						if( $view <> "views/".$script."/form.php" )
                        //						{
                        ?>
                        <div class="ace-settings-container" id="ace-settings-container">

                            <button class="btn btn-danger btnRedirect" rel="<?php echo $script ?>.php?action=add">
                                <i class="ace-icon fa fa-file align-top bigger-125"></i>
                                Nueva <?php echo SIMReg::get("title") ?>
                            </button>


                        </div>
                        <?php
                        //}//end if
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
                                                    <i class="ace-icon fa fa-users orange"></i>CONSULTAR
                                                    <?php //echo $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"]; 
                                                    ?>
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

                                                                        <input type="text" name="qryString" class="form-control search-query " placeholder="Ingrese el nombre del caddie">
                                                                        <input type="hidden" name="action" value="search">
                                                                        <span class="input-group-btn">

                                                                            <button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
                                                                                <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                                                                Buscar Parametro bicicleta
                                                                            </button>
                                                                        </span>
                                                                        <span class="input-group-btn">

                                                                            <button type="button" class="btn btn-primary btn-sm btnRedirect" rel="administrarBicicleta.php?action=search">
                                                                                Ver Todos
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

            <?php
            include("cmp/footer.php");
            ?>
        </div><!-- /.main-container -->

        <script type="text/javascript">
            $("#fechaInicio").datepicker({
                autoclose: true,
                startDate: Date(),
                format: 'yyyy-mm-dd',
            }).on("changeDate", function(selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#fechaFin').datepicker('setStartDate', minDate);
            });

            $("#fechaFin").datepicker({
                autoclose: true,
                startDate: Date(),
                format: 'yyyy-mm-dd',
            }).on("changeDate", function(selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#fechaInicio').datepicker('setEndDate', minDate);
            });


            $(".btnSorteo").on("click", function(e) {
                var fechaInicio = $("#fechaInicio").val();
                var fechaFin = $("#fechaFin").val();

                if (fechaInicio != "" && fechaFin != "") {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        'async': true,
                        url: "includes/async/acciones.async.php",
                        data: {
                            action: "comprobarFechas",
                            fechaInicio: fechaInicio,
                            fechaFin: fechaInicio
                        },
                        success: function(data) {
                            if (data == 1) {
                                if (confirm("Ya existe un sorteo entre estas fechas, ¿está seguro que desea registrar un nuevo sorteo?")) {
                                    document.getElementById('frmSorteo').submit();
                                }
                            } else
                                document.getElementById('frmSorteo').submit();
                        }
                    });

                } else {
                    alert("debe seleccionar la fecha de inicio y fecha de fin para continuar");
                }

            });
        </script>

</body>

</html>