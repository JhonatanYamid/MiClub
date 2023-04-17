<?
include("procedures/general.php");
include("procedures/consumoalimentoscasinosocio.php");
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
              <?= SIMUtil::get_traduccion('', '', 'Home', LANGSESSION); ?>
              <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <?= $array_clubes[SIMUser::get("club")]["Nombre"] ?>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Carga de Empleados Alimentos Casino Socio
              </small>
            </h1>
          </div><!-- /.page-header -->

          <div class="row">
            <div class="col-xs-12">
              <!-- PAGE CONTENT BEGINS -->


              <div class="row">
                <div class="col-sm-12">




                  <form class="form-horizontal formvalida" role="form" method="post" id="frmUsuarioPlano" name="frmUsuarioPlano" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                        <td>
                          <table id="simple-table" class="table table-striped table-bordered table-hover">
                            <tr>
                              <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
                            </tr>

                            <tr>
                              <td>1</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></td>
                            </tr>
                            <tr>
                              <td>3</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'Desayuno', LANGSESSION); ?>(S/N)</td>
                            </tr>
                            <tr>
                              <td>4</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'Almuerzo', LANGSESSION); ?>(S/N)</td>
                            </tr>
                            <tr>
                              <td>5</td>
                              <td>Comida(S/N)</td>
                            </tr>
                            <tr>
                              <td>6</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'Cena', LANGSESSION); ?>(S/N)</td>
                            </tr>
                            <tr>
                              <td>7</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>(2021-11-01)</td>
                            </tr>
                            <tr>
                              <td>8</td>
                              <td><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>(2021-11-30)</td>
                            </tr>

                          </table>
                        </td>
                        <td valign="top">
                          <table id="simple-table" class="table table-striped table-bordered table-hover">
                            <tr>
                              <td><?= SIMUtil::get_traduccion('', '', 'ArchivoExcel', LANGSESSION); ?></td>
                              <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                            </tr>
                            <!--
      <tr>
        <td>Separador de campo</td>
        <td>
        <select name="FIELD_TEMINATED" id="FIELD_TEMINATED" class="form-control" size="1">
            <option value="TAB">Tabulador</option>
            <option value=",">Coma (,)</option>
            <option value="|">Pie (|)</option>
        </select>
        </td>
      </tr>
      <tr>
        <td>Encabezados en la primera Fila?</td>
        <td>
         Si
<input type="radio" name="IGNORELINE" value="1" border="0"/>
No
<input type="radio" name="IGNORELINE" value="0" checked="" border="0"/>

        </td>
      </tr>
		-->
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