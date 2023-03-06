<?
include("procedures/general.php");
include("procedures/reporteingresosalida.php");
include("cmp/seo.php");

$url_search = "";
if ($_GET["action"] == "search") {
    $url_search = "?oper=search_url&Accion=" . SIMNet::get("Accion");
} //end if

$frm_get = SIMUtil::makeSafe($_GET);
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
                                    <form name="frmBusqueda" id="frmBusqueda" action="" method="post" enctype="multipart/form-data">
                                    </form>
                                    <form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">
                                        <div class="col-xs-12 col-sm-12">
                                            <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                <tr>
                                                    <td>Documento</td>
                                                    <td><input type="text" name="Documento" id="Documento" class="form-control"> </td>
                                                    <td>Placa</td>
                                                    <td><input type="text" name="Placa" id="Placa" class="form-control"></td>
                                                    <td>Tipo</td>
                                                    <td><select name="IDTipoInvitado" id="IDTipoInvitado" class="form-control">
                                                            <option value=""></option> <?php
                                                                                        $sql_tipo_lista = "Select * From TipoInvitado Where IDClub = '" . SIMUser::get("club") . "'";
                                                                                        $qry_tipo_lista = $dbo->query($sql_tipo_lista);
                                                                                        while ($r_tipo_lista = $dbo->fetchArray($qry_tipo_lista)) : ?> <option value="<?php echo $r_tipo_lista["IDTipoInvitado"]; ?>" <?php if ($r_tipo_lista["IDTipoInvitado"] == $frm["IDTipoInvitado"]) echo "selected";  ?>><?php echo $r_tipo_lista["Nombre"]; ?></option> <?php endwhile; ?> <option <?php if ("Socio" == $frm["IDTipoInvitado"]) echo "selected";  ?> value="Socio">Socio</option>
                                                            <option <?php if ("Empleado" == $frm["IDTipoInvitado"]) echo "selected";  ?> value="Empleado">Empleado</option>
                                                            <option <?php if ("ContratistaSocio" == $frm["IDTipoInvitado"]) echo "selected";  ?> value="ContratistaSocio">Contratista Socio</option>
                                                            <option <?php if ("InvitadoSocio" == $frm["IDTipoInvitado"]) echo "selected";  ?> value="InvitadoSocio">Invitado Socio</option>
                                                        </select></td>
                                                </tr>
                                                <tr>
                                                    <td>Accion</td>
                                                    <td><input type="text" name="AccionBusqueda" id="Accion" class="form-control"></td>
                                                    <td>Predio</td>
                                                    <td><input type="text" name="PredioBusqueda" id="Predio" class="form-control"></td>
                                                    <td>Tipo Socio</td>
                                                    <td> <?php $sql_tipo_socio = "SELECT TS.IDTipoSocio,Nombre FROM TipoSocio TS, ClubTipoSocio CTS WHERE TS.IDTipoSocio=CTS.IDTipoSocio AND IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
                                                            $result_tipo_socio = $dbo->query($sql_tipo_socio);
                                                            ?> <select name="TipoSocio" id="TipoSocio" class="form-control">
                                                            <option value="">[Seleccione Tipo Socio]</option> <?php
                                                                                                                while ($row_tipo_soc = $dbo->fetchArray($result_tipo_socio)) { ?> <option value="<?php echo $row_tipo_soc["Nombre"];  ?>" <?php if ($frm["TipoSocio"] == $row_tipo_soc["Nombre"]) echo "selected"; ?>><?php echo $row_tipo_soc["Nombre"];  ?></option> <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Usuario Porteria</td>
                                                    <td>
                                                        <select name="IDPortero" id="IDPortero" class="form-control">
                                                            <option value=""></option> <?php
                                                                                        $sql_portero = "SELECT IDUsuario, Nombre From Usuario Where `IDPerfil` in (4,25,60,63,115,16) and IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
                                                                                        $r_portero = $dbo->query($sql_portero);
                                                                                        while ($row_portero = $dbo->fetchArray($r_portero)) { ?> <option value="<?php echo $row_portero["IDUsuario"]; ?>"><?php echo $row_portero["Nombre"]; ?></option> <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>Sede</td>
                                                    <td>
                                                        <select name="IDCursoSede" id="IDCursoSede" class="form-control">
                                                            <option value=""></option>
                                                            <?php
                                                            $sql_sede = "SELECT IDCursoSede, Nombre From CursoSede Where IDClub = '" . SIMUser::get("club") . "' and Publicar = 'S' Order by Nombre";
                                                            $r_sede = $dbo->query($sql_sede);
                                                            while ($row_sede = $dbo->fetchArray($r_sede)) {
                                                                $selected = ($frm_get['TipoUsuario'] == $row['Nombre']) ? 'selected' : '';
                                                            ?>
                                                                <option value="<?php echo $row_sede["IDCursoSede"]; ?>" <?= $selected ?>><?php echo $row_sede["Nombre"]; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>

                                                    <?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 44 || SIMUser::get("club") == 141) : ?>
                                                        <td>Tipo Usuario</td>
                                                        <td>
                                                            <select name="TipoUsuario" id="TipoUsuario" class="form-control">
                                                                <option value="">[Seleccione una opción]</option>
                                                                <?php
                                                                $TipoUsuario = "SELECT * FROM TipoUsuario WHERE 1";
                                                                $qry = $dbo->query($TipoUsuario);
                                                                while ($row = $dbo->fetchArray($qry)) :
                                                                    $selected = ($frm_get['TipoUsuario'] == $row['Nombre']) ? 'selected' : '';
                                                                ?>
                                                                    <option value="<?php echo $row['Nombre']; ?>" <?= $selected ?>><?php echo $row['Nombre']; ?></option>
                                                                <?php endwhile; ?>
                                                            </select>
                                                        </td>
                                                    <?php else : ?>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                    <?php endif; ?>


                                                </tr>
                                                <tr>
                                                    <td>Fecha Desde</td>
                                                    <td><span class="col-sm-8">
                                                            <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Ingreso" class="col-xs-12 calendar " title="Fecha Ingreso" value="<?php if ($frm["FechaInicio"] == "0000-00-00" || $frm["FechaInicio"] == "") echo date("Y-m-d");
                                                                                                                                                                                                        else echo $frm["FechaInicio"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
                                                        </span></td>
                                                    <td>Fecha Hasta</td>
                                                    <td><span class="col-sm-8">
                                                            <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar " title="Fecha Fin" value="<?php if ($frm["FechaFin"] == "0000-00-00" || $frm["FechaFin"] == "") echo date("Y-m-d");
                                                                                                                                                                                            else echo $frm["FechaFin"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
                                                        </span></td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
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