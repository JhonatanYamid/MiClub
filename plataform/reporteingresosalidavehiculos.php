<?
include("procedures/general.php");
include("procedures/reporteingresosalidavehiculos.php");
include("cmp/seo.php");

$frm_get = SIMUtil::makeSafe($_GET);
$url_search = "";
if ($_GET["action"] == "search") {
    $url_search = "?oper=search_url&Accion=" . SIMNet::get("Accion");
} //end if

?>
</head>

<body class="no-skin">
    <?
    if ($_GET["action"] != "add") :
        include("cmp/header.php");
    endif;
    ?>
    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {}
        </script>
        <?
        $menu_invitados = " class=\"active\" ";
        include("cmp/menu.php");
        ?>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs" id="breadcrumbs">
                    <script type="text/javascript">
                        try {
                            ace.settings.check('breadcrumbs', 'fixed')
                        } catch (e) {}
                    </script> <?php include("cmp/breadcrumb.php"); ?>
                </div>
                <div class="page-content">
                    <? SIMNotify::each(); ?>
                    <div class="page-header">
                        <h1> <?php echo strtoupper(SIMReg::get("title")) ?> <small>
                                <i class="ace-icon fa fa-angle-double-right"></i> <?= SIMUtil::tiempo(date("Y-m-d")) ?> </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <div class="widget-body">
                        <div class="widget-main padding-4">
                            <div class="row">
                                <div class="col-xs-12">
                                    <!-- PAGE CONTENT BEGINS -->
                                    <form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">
                                        <div class="col-xs-12 col-sm-12">
                                            <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                <tr>
                                                    <td>Placa</td>
                                                    <td><input type="text" name="Placa" id="Placa" class="form-control"></td>
                                                    <!-- <td>Tipo Vehiculo</td>
                                                    <td><select name="IDTipoVehiculo" id="IDTipoVehiculo" class="form-control">
                                                            <option value="">Seleccione</option>
                                                            <?php
                                                            $sql_tipoVeiculo = "Select * From TipoVehiculo Where 1 ORDER BY Nombre";
                                                            $qry_tipoVeiculo = $dbo->query($sql_tipoVeiculo);
                                                            while ($r_tipoVeiculo = $dbo->fetchArray($qry_tipoVeiculo)) : ?>
                                                                <option value="<?php echo $r_tipoVeiculo["IDTipoVehiculo"]; ?>" <?= ($r_tipoVeiculo["IDTipoVehiculo"] == $frm["IDTipoVehiculo"]) ? "selected" : '';  ?>>
                                                                    <?php echo $r_tipoVeiculo["Nombre"]; ?>
                                                                </option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </td> -->
                                                    <!-- </tr>
                                                <tr> -->
                                                    <td>Usuario Porteria</td>
                                                    <td>
                                                        <select name="IDPortero" id="IDPortero" class="form-control">
                                                            <option value="">Seleccione</option>
                                                            <?php
                                                            $sql_portero = "SELECT IDUsuario, Nombre From Usuario Where `IDPerfil` in (4,25,60,63,115,16) and IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
                                                            $r_portero = $dbo->query($sql_portero);
                                                            while ($row_portero = $dbo->fetchArray($r_portero)) { ?>
                                                                <option value="<?= $row_portero["IDUsuario"]; ?>"><?= $row_portero["Nombre"]; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>Fecha Desde</td>
                                                    <td><span class="col-sm-8">
                                                            <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Ingreso" class="col-xs-12 calendar " title="Fecha Ingreso" value="<?= (!empty($frm_get["FechaInicio"])) ? $frm_get["FechaInicio"] : date("Y-m-d"); ?>">
                                                        </span>
                                                    </td>
                                                    <td>Fecha Hasta</td>
                                                    <td><span class="col-sm-8">
                                                            <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Salida" class="col-xs-12 calendar " title="Fecha Salida" value="<?= (!empty($frm_get["FechaFin"])) ? $frm_get["FechaFin"] : date("Y-m-d"); ?>">
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" align="center">
                                                        <input type="hidden" name="action" value="search">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
                                                                <span class="ace-icon fa fa-search icon-on-right bigger-110"></span> Buscar <?php echo SIMReg::get("title") ?> </button>
                                                        </span>
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-primary btn-sm btnRedirect" rel="<?php echo $script; ?>.php?action=search"> Ver Todos </button>
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