<?
include("procedures/general.php");
include("procedures/listaclubcanje.php");
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



                    <?php
                    SIMNotify::each();

                    if ($view <> "views/" . $script . "/form.php") {
                    ?>

                    <?
                    } //end if
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


                                        <div class="widget-body">
                                            <div class="widget-main padding-4">
                                                <div class="row">
                                                    <div class="col-xs-12">

                                                    </div>
                                                </div>
                                                <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                                                    <div class="widget-header widget-header-large">
                                                        <h3 class="widget-title grey lighter">
                                                            <i class="ace-icon fa fa-globe green"></i>
                                                            <?= SIMUtil::get_traduccion('', '', 'Clubesconconvenioparacanjes', LANGSESSION); ?>
                                                        </h3>
                                                    </div>

                                                    <div class="form-group first ">

                                                        <div class="col-xs-12 col-sm-12">


                                                            <div class="col-sm-12">

                                                                <?php
                                                                // Consulto del listado de clubes del club
                                                                $sql_clubes_canje = $dbo->query("select * from ListaDetalleClubCanje where IDClub = '" . SIMUser::get("club") . "'");


                                                                while ($r_club_canje = $dbo->object($sql_clubes_canje)) {
                                                                    $club_canje[] = $r_club_canje->IDListaClubes;
                                                                    $club_canje_correo[$r_club_canje->IDListaClubes] = $r_club_canje->CorreoNotificacion;
                                                                    $club_canje_mensaje[$r_club_canje->IDListaClubes] = $r_club_canje->MensajeAlCrearCanje;
                                                                    $MaximoDias[$r_club_canje->IDListaClubes] = $r_club_canje->MaximoDias;
                                                                }
                                                                $arrayop = array();
                                                                // consulto los clubes
                                                                $query_listaclubes = $dbo->query("Select * from ListaClubes Where Publicar = 'S' Order by Nombre");
                                                                while ($r = $dbo->object($query_listaclubes)) {
                                                                    $nombre_club = utf8_encode($r->Nombre) . "(" . $dbo->getFields("Pais", "Nombre", "IDPais = '" . $r->IDPais . "'") . ")";
                                                                    $arrayclubes[$nombre_club] = $r->IDListaClubes;
                                                                }



                                                                echo formCheckGroup_especial($arrayclubes, $club_canje, "ClubCanje[]", "&nbsp;", "", $club_canje_correo, $club_canje_mensaje, $MaximoDias);
                                                                ?>

                                                                <?php
                                                                function formCheckGroup_especial($options, $selection, $name, $sep = "", $attrs = "", $club_canje_correo, $club_canje_mensaje, $MaximoDias)
                                                                {



                                                                    $checkgroup = "";

                                                                    $checkgroup = "<table id='simple-table' class='table table-striped table-bordered table-hover'><tr><td>";
                                                                    $columnas = 0;
                                                                    foreach ($options as $key => $val) {
                                                                        $columnas++;
                                                                        $checkgroup .= "<label class=\"checkgroup\"><input type=\"checkbox\" name=\"ClubCanje[" . $val . "]" . "\" id=\"" . $name . "\" value=\"" . $val . "\" " . $attrs;


                                                                        if (!empty($selection))
                                                                            $checkgroup .= (in_array($val, $selection)) ? " checked" : "";

                                                                        $checkgroup .= "> " . utf8_decode($key);
                                                                        $checkgroup .= "</label>" . $sep;


                                                                        $checkgroup .= "</td>";

                                                                        if ($columnas == 4) :
                                                                            $checkgroup .= "</tr><tr><td>";
                                                                            $columnas = 0;
                                                                        else :
                                                                            $checkgroup .= "<td>";
                                                                        endif;
                                                                    }
                                                                    $checkgroup .= "</tr></table>";

                                                                    return $checkgroup;
                                                                }
                                                                ?>



                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="clearfix form-actions">
                                                        <div class="col-xs-12 text-center">
                                                            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                                            <input type="hidden" name="action" id="action" value="update" />
                                                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                                    else echo $frm["IDClub"];  ?>" />


                                                            <input style="height: 40px;" class="btn btn-info btnEnviar" type="submit" rel="frmofertacandidatos" id="enviarcandidatos" name="enviarcandidatos" value="Guardar">



                                                        </div>
                                                    </div>

                                                </form>



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