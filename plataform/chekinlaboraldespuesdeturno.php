<?
include("procedures/general.php");
include("procedures/chekinlaboraldespuesdeturno.php");
include("cmp/seo.php");


?>
</head>
<style>
  #map,
  #map2 {
    width: 100%;
    height: 580px;
    box-shadow: 5px 5px 5px #888;
  }
</style>

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
                Checkin Laboral Despues Del Turno
              </small>
            </h1>
          </div><!-- /.page-header -->
          <?php if ($_GET["action"] == "edit") { ?>
            <a class="btn btn-info " href='chekinlaboral.php?action=extras&id=<?php echo $_GET["id"] ?>&type=<?php echo $_GET["type"] ?>'>
              <i class="ace-icon fa fa-arrow-left align-top bigger-125"></i>
              Volver
            </a>
          <?php } ?>

          <div class="widget-body">
            <div class="widget-main padding-4">
              <div class="row">
                <div class="col-xs-12">
                  <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                    <div class="form-group first ">

                      <div class="form-group first ">
                        <?php
                        if ($frm["IDSocio"] > 0) {
                          $idsocio = $frm["IDSocio"];
                        } else if ($frm["IDUsuario"] > 0) {
                          $idusuario = $frm["IDUsuario"];
                        }
                        ?>
                        <div class="col-xs-12 col-sm-6">
                          <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'CheckinParaquien', LANGSESSION); ?> ? </label>
                          <div class="col-md-4">
                            <label class="control-label"><input <?php echo $idsocio != "" ? 'checked' : ""; ?> type="radio" id="personaCheckinSocio" name="personaCheckin" value="1" <?php if ($_GET["action"] == "edit") echo "disabled"; ?>> Socio </label>
                          </div>
                          <div class="col-md-4">
                            <label class="control-label"><input <?php echo $idusuario > 0 ? 'checked' : ""; ?> type="radio" id="personaCheckinUsuario" name="personaCheckin" value="2" <?php if ($_GET["action"] == "edit") echo "disabled"; ?>> Usuario </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group first ">

                        <div class="form-group col-md-6" id="mostrarSocio" style="<?php if ($frm["IDSocio"] >  0) echo "";
                                                                                  else echo "display:none"; ?>">


                          <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>
                          <div class="col-sm-8">

                            <?php
                            $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                            $qry_socio_club = $dbo->query($sql_socio_club);
                            $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                            <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12  autocomplete-ajax" title="número de derecho" value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>" <?php if ($_GET["action"] == "edit") echo "readonly"; ?>>
                            <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="" title="Socio">


                          </div>



                        </div>


                        <div class="form-group col-md-6" id="mostrarUsuario" style="<?php if ($frm["IDUsuario"] > 0) echo "";
                                                                                    else echo "display:none"; ?>">
                          <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?> </label>


                          <div class="col-sm-8">

                            <?php
                            $sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
                            $qry_socio_club = $dbo->query($sql_socio_club);
                            $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                            <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12  autocomplete-ajax-funcionario" title="número de derecho" value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>" <?php if ($_GET["action"] == "edit") echo "readonly"; ?>>
                            <input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" class="" title="Usuario">


                          </div>
                        </div>


                      </div>

                      <div class="form-group first ">
                        <div class="form-group col-md-6">
                          <label class="col-sm-4 control-label" for="Latitud"><?= SIMUtil::get_traduccion('', '', 'LatitudEntrada', LANGSESSION); ?></label>
                          <div class="col-sm-8"><input type="text" id="LatitudEntrada" name="LatitudEntrada" placeholder="<?= SIMUtil::get_traduccion('', '', 'LatitudEntrada', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'LatitudEntrada', LANGSESSION); ?>" value="<?php echo $frm["LatitudEntrada"] ?>" required></div>

                        </div>

                        <div class="form-group col-md-6">
                          <label class="col-sm-4 control-label" for="Casa"><?= SIMUtil::get_traduccion('', '', 'LongitudEntrada', LANGSESSION); ?></label>
                          <div class="col-sm-8"> <input type="text" id="LongitudEntrada" name="LongitudEntrada" placeholder="<?= SIMUtil::get_traduccion('', '', 'LongitudEntrada', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'LongitudEntrada', LANGSESSION); ?>" value="<?php echo $frm["LongitudEntrada"] ?>" required></div>


                        </div>

                      </div>

                      <div class="form-group first ">
                        <div class="form-group col-md-6">
                          <label class="col-sm-4 control-label" for="Latitud"><?= SIMUtil::get_traduccion('', '', 'LatitudSalida', LANGSESSION); ?></label>
                          <div class="col-sm-8"><input type="text" id="LatitudSalida" name="LatitudSalida" placeholder="<?= SIMUtil::get_traduccion('', '', 'LatitudSalida', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'LatitudSalida', LANGSESSION); ?>" value="<?php echo $frm["LatitudSalida"] ?>"></div>

                        </div>

                        <div class="form-group col-md-6">
                          <label class="col-sm-4 control-label" for="Casa"><?= SIMUtil::get_traduccion('', '', 'LongitudSalida', LANGSESSION); ?></label>
                          <div class="col-sm-8"> <input type="text" id="LongitudSalida" name="LongitudSalida" placeholder="<?= SIMUtil::get_traduccion('', '', 'LongitudSalida', LANGSESSION); ?>" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'LongitudSalida', LANGSESSION); ?>" value="<?php echo $frm["LongitudSalida"] ?>"></div>


                        </div>

                      </div>
                      <div class="form-group first ">
                        <div class="col-xs-12 col-sm-6">
                          <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Entrada', LANGSESSION); ?> </label>
                          <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Entrada"], 'Entrada', "class='input mandatory'") ?>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                          <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Salida', LANGSESSION); ?> </label>
                          <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Salida"], 'Salida', "class='input mandatory'") ?>
                        </div>

                      </div>

                      <div class="form-group first ">
                        <div class="form-group col-md-6">
                          <label class="col-sm-4 control-label" for="FechaMovimiento"><?= SIMUtil::get_traduccion('', '', 'FechaMovimientoEntrada', LANGSESSION); ?></label>
                          <div class="col-sm-8">
                            <?php
                            $fechaMovimientoEntrada = explode(" ", $frm["FechaMovimientoEntrada"]);
                            ?>
                            <input type="text" id="FechaEntrada" name="FechaEntrada" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaMovimientoEntrada', LANGSESSION); ?>" class="calendar mandatory" title="<?= SIMUtil::get_traduccion('', '', 'FechaMovimientoEntrada', LANGSESSION); ?>" value="<?php echo $fechaMovimientoEntrada[0]; ?>" required>
                            <input type="time" name="HoraEntrada" id="HoraEntrada" value="<?php echo $fechaMovimientoEntrada[1]; ?>" class="mandatory" title="Hora Entrada" required>
                          </div>

                        </div>

                        <div class="form-group col-md-6">
                          <label class="col-sm-4 control-label" for="FechaMovimiento"><?= SIMUtil::get_traduccion('', '', 'FechaMovimientoSalida', LANGSESSION); ?></label>
                          <div class="col-sm-8">
                            <?php
                            $fechaMovimientoSalida = explode(" ", $frm["FechaMovimientoSalida"]);
                            ?>
                            <input type="text" id="FechaSalida" name="FechaSalida" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaMovimientoSalida', LANGSESSION); ?>" class="calendar mandatory" title="<?= SIMUtil::get_traduccion('', '', 'FechaMovimientoSalida', LANGSESSION); ?>" value="<?php echo $fechaMovimientoSalida[0]; ?>">
                            <input type="time" name="HoraSalida" id="HoraSalida" value="<?php echo $fechaMovimientoSalida[1]; ?>" class="mandatory" title="Hora Salida" required>
                          </div>

                        </div>
                      </div>

                      <div class="form-group first ">
                        <div class="form-group col-md-6">
                          <label class="col-sm-4 control-label" for="Casa">Comentario</label>
                          <div class="col-sm-8">

                            <textarea name="ComentarioRevision" id="ComentarioRevision" cols="30" rows="10"><?php echo $frm["ComentarioRevision"] ?></textarea>

                          </div>


                        </div>
                      </div>

                      <?php if ($_GET["action"] == "edit") { ?>
                        <div class="form-group first ">
                          <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label" for="HoraEntradaEstablecida"><?= SIMUtil::get_traduccion('', '', 'HoraEntradaEstablecida', LANGSESSION); ?></label>
                            <div class="col-sm-8">
                              <input type="time" name="HoraEntradaEstablecida" id="HoraEntradaEstablecida" value="<?php echo $frm["HoraEntradaEstablecida"] ?>" title="Hora Entrada Establecida" class="">
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label" for="HoraSalidaEstablecida"><?= SIMUtil::get_traduccion('', '', 'HoraSalidaEstablecida', LANGSESSION); ?></label>
                            <div class="col-sm-8">
                              <input type="time" name="HoraSalidaEstablecida" id="HoraSalidaEstablecida" value="<?php echo $frm["HoraSalidaEstablecida"] ?>" title="Hora Salida Establecida" class="">
                            </div>
                          </div>
                        </div>




                        <div class="form-group first">
                          <label class="col-sm-4 control-label" for="Casa">
                            <h3><b><?= SIMUtil::get_traduccion('', '', 'UbicacionEntrada', LANGSESSION); ?></b></h3>
                          </label>
                          <div id='map'>
                          </div>

                        </div>

                        <div class="form-group first">
                          <label class="col-sm-4 control-label" for="Casa">
                            <h3><b><?= SIMUtil::get_traduccion('', '', 'UbicacionSalida', LANGSESSION); ?></b></h3>
                          </label>
                          <div id='map2'>
                          </div>

                        </div>

                      <?php } ?>








                      <div class="form-group first ">
                        <div class="clearfix form-actions">
                          <div class="col-xs-12 text-center">
                            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                            <input type="hidden" name="action" id="action" value="<?php if ($_GET["action"] == "edit") {
                                                                                    echo $newmode;
                                                                                  } else {
                                                                                    echo "insert";
                                                                                  }  ?>" />
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                  else echo $frm["IDClub"];  ?>" />
                            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                              <i class="ace-icon fa fa-check bigger-110"></i>
                              <?php if ($_GET["action"] == "edit") {
                                echo SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION);
                              } else {
                                echo "Crear";
                              }  ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                            </button>
                            <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                            <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div><!-- /.widget-main -->
          </div><!-- /.widget-body -->
        </div><!-- /.widget-box -->

        <script>
          //traigo la latitud y longitud de entrada
          let latitudEntrada = document.getElementById("LatitudEntrada").value;
          let longitudEntrada = document.getElementById("LongitudEntrada").value;

          //traigo la latitud y longitud de salida
          let latitudSalida = document.getElementById("LatitudSalida").value;
          let longitudSalida = document.getElementById("LongitudSalida").value;

          //se crea el mapa de ubicacion de entrada
          var map = L.map('map').
          setView([latitudEntrada, longitudEntrada],
            15);

          L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
            maxZoom: 18
          }).addTo(map);

          L.control.scale().addTo(map);
          L.marker([latitudEntrada, longitudEntrada], {
            draggable: true
          }).addTo(map);

          //se crea el mapa de ubicacion de salida
          var map = L.map('map2').
          setView([latitudSalida, longitudSalida],
            15);

          L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
            maxZoom: 18
          }).addTo(map);

          L.control.scale().addTo(map);
          L.marker([latitudSalida, longitudSalida], {
            draggable: true
          }).addTo(map);
        </script>




        <?
        include("cmp/footer_scripts.php");
        ?>