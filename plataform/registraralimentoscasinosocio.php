<?
include("procedures/general.php");
include("procedures/registraralimentoscasinosocio.php");
include("cmp/seo.php");
$logo_club = CLUB_ROOT . $dbo->getFields("Club", "FotoDiseno1", "IDClub = '" . SIMUser::get("club") . "'");
$logo_ruta = CLUB_DIR . $dbo->getFields("Club", "FotoDiseno1", "IDClub = '" . SIMUser::get("club") . "'");
?>
</head>

<body class="no-skin" onLoad="document.frmfrmBuscar.qryString.focus();">

    <div id="navbar" class="navbar navbar-default">

        <div class="navbar-container" id="navbar-container">


            <div class="navbar-header pull-left">
                <a href="index.php" class="navbar-brand"><img src="assets/img/logo-interno.png" /></a>
            </div>

            <div class="navbar-buttons navbar-header pull-right" role="navigation">
                <div style="background-color:#FFFFFF">
                    <?
                    $imagen = getimagesize($logo_ruta);    //Sacamos la información
                    $ancho = $imagen[0];              //Ancho
                    $alto = $imagen[1];               //Alto
                    //echo "Ancho: $ancho<br>";
                    //echo "Alto: $alto";
                    if ($ancho > 100 || $alto > 100) :
                        $ancho_alto = 'width="100" height="50"';
                    endif;
                    ?>
                    <img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $ancho_alto; ?> />
                </div>
            </div>



        </div><!-- /.navbar-container -->
    </div>


    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {}
        </script>


        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs" id="breadcrumbs">
                    <?php include("cmp/breadcrumb.php"); ?>


                </div>

                <div class="page-content">
                    <?
                    SIMNotify::each();
                    ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->


                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="widget-box transparent" id="recent-box">
                                        <div class="widget-header">
                                            <h4 class="widget-title lighter smaller">
                                                <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'IngreseCedula', LANGSESSION); ?>
                                                <!--  <?php if (SIMUser::get("club") != 9) {
                                                            //calcular ocupacion
                                                            $sql_entra = "SELECT count(IDLogAcceso) as TotalEntra FROM `LogAccesoDiario` WHERE IDClub = '" . SIMUser::get("club") . "' and Entrada = 'S'";
                                                            $r_entra = $dbo->query($sql_entra);
                                                            $row_entra = $dbo->fetchArray($r_entra);
                                                            $sql_sale = "SELECT count(IDLogAcceso) as TotalSale FROM `LogAccesoDiario` WHERE IDClub = " . SIMUser::get("club") . " and Salida = 'S'";
                                                            $r_sale = $dbo->query($sql_sale);
                                                            $row_sale = $dbo->fetchArray($r_sale);
                                                            $ocupacion = (int)$row_entra["TotalEntra"] - (int)$row_sale["TotalSale"];
                                                        ?> -->
                                                <!--   <div style="float:right">
                                                    <?php echo "Ocupación aprox: <span style='color:#E41926'> " . $ocupacion . "</span> personas"; ?>
                                                    <div>
                                                    <? } ?> -->



                                            </h4>


                                        </div>

                                        <div class="widget-body">
                                            <div class="widget-main padding-4">
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <!-- BUSQUEDA ACCESO -->
                                                        <form class="form-horizontal formvalida" id="frmfrmBuscarAlimentosCasino" name="frmfrmBuscarAlimentosCasino" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="GET">

                                                            <div id="busqueda_pantalla_acceso" class="col-xs-12 col-sm-8">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon">
                                                                        <i class="ace-icon fa fa-check"></i>
                                                                    </span>
                                                                    <input type="text" name="qryString" id="busqueda_acceso" placeholder="<?= SIMUtil::get_traduccion('', '', 'IngreseCedula', LANGSESSION); ?>" class="form-control search-query busqueda_acceso" autofocus>
                                                                    <input type="hidden" name="action" value="search">
                                                                    <input type="hidden" name="IDTipoBusqueda" id="IDTipoBusqueda" value="1">
                                                                    <!--  <span class="input-group-btn">
                                                                        <select name="IDTipoBusqueda" id="IDTipoBusqueda" class="form-control">
                                                                            <option value="1">Cedula</option>
                                                                            <option value="2">Busqueda Nombre</option>
                                                                            <option value="3">Busqueda por placa (Dar salida)</option>
                                                                        </select>
                                                                    </span>-->
                                                                    <span class="input-group-btn">
                                                                        <button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscarAlimentosCasino">
                                                                            <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                                                            <?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <hr>
                                                        <hr>
                                                        <!-- PAGE CONTENT BEGINS -->
                                                    </div>
                                                    <div class="col-xs-6" style="font-size: 20px;" align="center">
                                                        <br>
                                                        <?php
                                                        $fechaActual = date('Y-m-d');
                                                        $sql_entra = "SELECT * FROM `LogRegistroAlimentoSocio` WHERE IDClub = '" . SIMUser::get("club") . "' ORDER BY IDLogRegistroAlimentoSocio DESC LIMIT 10";
                                                        $r_entra = $dbo->query($sql_entra);
                                                        $row_entra = $dbo->fetch($r_entra);

                                                        foreach ($row_entra as $j => $i) {
                                                            if ($i['FechaRegistro'] != date('Y-m-d')) {
                                                                $borra = "DELETE FROM LogRegistroAlimentoSocio WHERE FechaRegistro <> '" . date('Y-m-d') . "'";
                                                                $dbo->query($borra);
                                                            }
                                                            $cedula = $i['Cedula'];
                                                            if (strpos($i['Mensaje'], 'Alimento registrado correctamente para') !== false) {
                                                                $Foto = $dbo->getFields("Socio", "Foto", "NumeroDocumento = '" . $cedula . "' AND IDClub = '" . SIMUser::get("club") . "'");
                                                                // $sql_user = "SELECT Foto FROM `Socio` WHERE IDClub = '" . SIMUser::get("club") . "' AND NumeroDocumento = '$cedula' ORDER BY IDLogRegistroAlimentoSocio DESC LIMIT 1";
                                                                // $r_user = $dbo->query($sql_user);
                                                                // $row_user = $dbo->fetch($r_user);
                                                                $foto = "<img src='" . SOCIO_ROOT . "$Foto' width=55 style='border-radius: 50%;margin-right:10px' >";
                                                                $color = '#d4edda';
                                                            } else {
                                                                $foto = "<div style='color:white; font-size:20px; background:#c8afb2;font-weight:600; border-radius: 100%;width:55px;height:55px;text-align:center;line-height:55px;'>X</div>";
                                                                $color = '#f8d7da';
                                                            }
                                                            if ($j == 0) {
                                                                echo "<div class='row' style='color:#0c5460; font-size:27px;background:$color; padding:5px;padding-left:10px;border-radius: 5px;font-weight:600'><div class='col-xs-2'>" .$foto."</div><div class='col-xs-10'>". $i['Mensaje'] . "</div></div>";
                                                            } else {
                                                                echo "<div class='row' style='color:#0c5460; font-size:20px;background:$color; padding:3px; padding-left:10px;margin-top:5px;border-radius: 5px;font-weight:600'><div class='col-xs-2'>" .$foto."</div><div class='col-xs-10'>". $i['Mensaje'] . "</div></div>";
                                                            }
                                                        }
                                                        ?>
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

    </div><!-- /.main-container -->

    <script>
        $("#IDTipoBusqueda").change(function() {
            if ($("#IDTipoBusqueda").val() == 1) {
                $("#busqueda_acceso").attr("placeholder", "Cedula");
                $("#busqueda_nombre").val("");
            } else if ($("#IDTipoBusqueda").val() == 2) {
                $("#busqueda_acceso").attr("placeholder", "Busqueda Invitado por Nombre");
                $("#busqueda_nombre").val("true");
            } else if ($("#IDTipoBusqueda").val() == 3) {
                $("#busqueda_acceso").attr("placeholder", "Busqueda por placa (Dar salida)");
            }
        });
    </script>
</body>

</html>