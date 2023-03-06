<?
include("procedures/general.php");
include("procedures/cargamasivacartabase.php");
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
            <h1>
              Home
              <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <?= $array_clubes[SIMUser::get("club")]["Nombre"] ?>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <?= SIMUtil::get_traduccion('', '', 'CargaBaseCartera', LANGSESSION); ?>
              </small>
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
                              <td colspan="2"><?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                            </tr>
                            <tr>
                              <td>1</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'Numero', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'Nombres', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>3</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'Carta', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>4</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'PorVencer', LANGSESSION); ?> 30</td>
                            </tr>
                            <tr>
                              <td>5</td>
                              <td>30 <?= SIMUtil::get_traduccion('', '', 'Días', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>6</td>
                              <td>60 <?= SIMUtil::get_traduccion('', '', 'Días', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>7</td>
                              <td>90 <?= SIMUtil::get_traduccion('', '', 'Días', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>8</td>
                              <td>120 <?= SIMUtil::get_traduccion('', '', 'Días', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>9</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'mas', LANGSESSION); ?> 120</td>
                            </tr>
                            <tr>
                              <td>10</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'SaldoVencido', LANGSESSION); ?> 60 <?= SIMUtil::get_traduccion('', '', 'Días', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>11</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'General', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>12</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'FechaAbono', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>13</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'AbonoActual', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>14</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'NuevoSaldo', LANGSESSION); ?></td>
                            </tr>
                          </table>
                        </td>
                        <td valign="top">
                          <table id="simple-table" class="table table-striped table-bordered table-hover">
                            <tr>
                              <td><?= SIMUtil::get_traduccion('', '', 'ArchivoExcel', LANGSESSION); ?></td>
                              <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                            </tr>


                            <tr>
                              <td colspan="2">
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                      else echo $frm["IDClub"];  ?>" />
                                <input type="hidden" name="action" id="action" value="cargarplano" />
                                <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

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