<?
	/* Script para el envío de notificaciones a los socios que tienen reserva en un servicio */
	include( "procedures/general.php" );	
	include( "cmp/seo.php" );
?>
</head>

<body class="no-skin">
    <div class="main-container" id="main-container">
        <script type="text/javascript">
        try {
            ace.settings.check('main-container', 'fixed')
        } catch (e) {}
        </script>
        <div class="main-content">
            <div class="main-content-inner">


                <div class="page-content">
                    <?
						SIMNotify::each();						
						?>
                    <div class="page-header">
                        <h1>
                            Home
                            <small>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                <?=$array_clubes[ SIMUser::get("club") ]["Nombre"] ?>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                Buscar Invitados <?=$datos_servicio[$ids]["Nombre"] ?>
                            </small>
                        </h1>
                    </div><!-- /.page-header -->

                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="col-xs-12 col-sm-8">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="ace-icon fa fa-check"></i>
                                            </span>

                                            <input type="text" name="invitado" id="invitado" class="form-control"
                                                placeholder="Ingrese el nombre del invitado">
                                            <span class="input-group-btn">
                                                <button type="button" id="buscarinvitado" class="btn btn-purple btn-sm" fecha="<?php echo $_GET["Fecha"]?>"
                                                    rel="<?php echo $_GET["Servicio"]?>">
                                                    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                                    Buscar Invitado
                                                </button>
                                            </span>
                                        </div>
                                        <br>
                                        <br>
                                        <div id="DatosInvitado">

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

        <?
				include("cmp/footer_scripts.php");
			?>
    </div><!-- /.main-container -->


</body>

</html>