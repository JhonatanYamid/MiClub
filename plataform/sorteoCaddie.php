<?php
include( "procedures/general.php" );
include( "procedures/sorteoCaddie.php" );
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



  <div class="ace-settings-container" id="ace-settings-container">

                           <button class="btn btn-primary" onclick="location='administracionCaddie.php'">
                                <i class="ace-icon fa fa-preview align-top bigger-125"></i>
                                Volver
                            </button>

                        </div>


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
                                                <i class="ace-icon fa fa-users orange"></i>REALIZAR
                                                <?php echo strtoupper(SIMReg::get("title")); ?>
                                            </h4>
                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-4">


                                                <form class="form-horizontal formvalida" role="form" method="post" id="frmSorteo" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                                                    <input type="hidden" name="action" id="action" value="realizarSorteo" />

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="group-control">
                                                                <label>Fecha inicio</label>
                                                                <input type="text" name="fechaInicio" id="fechaInicio" class="form-control calendario" readonly="" value="<?php echo $frm['fechaInicio']; ?>" placeholder="Ingrese la fecha de inicio">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="group-control">
                                                                <label>Fecha inicio</label>
                                                                <input type="text" name="fechaFin" id="fechaFin" class="form-control calendario"  readonly="" value="<?php echo $frm['fechaFin']; ?>" placeholder="Ingrese la fecha de fin">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="group-control">
                                                                <br><p></p>
                                                                <button type="button" class="btn btn-primary btn-sm btnSorteo" rel="sorteoCaddie.php?action=realizarSorteo">
                                                                    Realizar sorteo
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>


                                            </div><!-- /.widget-main -->
                                        </div><!-- /.widget-body -->
                                    </div><!-- /.widget-box -->


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

    <script type="text/javascript">


        $("#fechaInicio").datepicker({
            autoclose: true,
            startDate: Date(),
            format: 'yyyy-mm-dd',
        }).on("changeDate", function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#fechaFin').datepicker('setStartDate', minDate);
        });

        $("#fechaFin").datepicker({
            autoclose: true,
            startDate: Date(),
            format: 'yyyy-mm-dd',
        }).on("changeDate", function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#fechaInicio').datepicker('setEndDate', minDate);
        });


        $(".btnSorteo").on("click", function (e)
        {
            var fechaInicio = $("#fechaInicio").val();
            var fechaFin = $("#fechaFin").val();

            if (fechaInicio != "" && fechaFin != "")
            {
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                    'async': true,
                    url: "includes/async/acciones.async.php",
                    data: {action: "comprobarFechas", fechaInicio: fechaInicio, fechaFin: fechaInicio},
                    success: function (data) {
                        if (data == 1)
                        {
                            if (confirm("Ya existe un sorteo entre estas fechas, ¿está seguro que desea registrar un nuevo sorteo?"))
                            {
                                document.getElementById('frmSorteo').submit();
                            }
                        } else
                            document.getElementById('frmSorteo').submit();
                    }
                });

            } else
            {
                alert("debe seleccionar la fecha de inicio y fecha de fin para continuar");
            }

        });





    </script>

</body>
</html>
