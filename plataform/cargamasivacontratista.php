<?
include("procedures/general.php");
include("procedures/cargamasivacontratista.php");
include("cmp/seo.php");
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
                        <h1> Home <small>
                                <i class="ace-icon fa fa-angle-double-right"></i> <?= $array_clubes[SIMUser::get("club")]["Nombre"] ?> <i class="ace-icon fa fa-angle-double-right"></i> Carga de Invitaciones </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlano" name="frmSocioPlano" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <td colspan="2"> Estructura del Archivo </td>
                                                        </tr>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>*Numero Documento de quien autoriza</td>
                                                        </tr>
                                                        <tr>
                                                            <td>2</td>
                                                            <td>*Fecha Ingreso (yyyy-mm-dd)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>3</td>
                                                            <td>*Fecha Salida (yyyy-mm-dd)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>4</td>
                                                            <td>*Documento Contratista</td>
                                                        </tr>
                                                        <tr>
                                                            <td>5</td>
                                                            <td>*Nombre Contratista</td>
                                                        </tr>
                                                        <tr>
                                                            <td>6</td>
                                                            <td>*Apellido Contratista</td>
                                                        </tr>
                                                        <tr>
                                                            <td>7</td>
                                                            <td>Email</td>
                                                        </tr>
                                                        <tr>
                                                            <td>8</td>
                                                            <td>Telefono</td>
                                                        </tr>
                                                        <tr>
                                                            <td>9</td>
                                                            <td>Tipo Sangre / Empresa</td>
                                                        </tr>
                                                        <tr>
                                                            <td>10</td>
                                                            <td>Placa</td>
                                                        </tr>
                                                        <tr>
                                                            <td>11</td>
                                                            <td>Lugar al que se dirige</td>
                                                        </tr>
                                                        <tr>
                                                            <td>12</td>
                                                            <td>ARL</td>
                                                        </tr>
                                                        <tr>
                                                            <td>13</td>
                                                            <td>EPS</td>
                                                        </tr>
                                                        <tr>
                                                            <td>14</td>
                                                            <td>Hora Incio (hh:mm:ss)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>15</td>
                                                            <td>Hora Final (hh:mm:ss)</td>
                                                        </tr>
                                                        <?php if (SIMUser::get("club") == 18) : ?>
                                                            <tr>
                                                                <td>16</td>
                                                                <td>*Dias (1,2,3..6,0) 1:Lunes,... 0:Domingo</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </table>
                                                </td>
                                                <td valign="top">
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <td>Archivo Excel</td>
                                                            <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                                        else echo $frm["IDClub"];  ?>" />
                                                                <input type="hidden" name="action" id="action" value="cargarplano" />
                                                                <input type="submit" class="submit" value="Cargar">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
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
</body>

</html>