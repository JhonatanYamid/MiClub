<?
include("../procedures/general.php");
include("../procedures/reserva.php");
$logo_club = CLUB_ROOT . $dbo->getFields("Club", "FotoDiseno1", "IDClub = '" . SIMUser::get("club") . "'");
$ruta_logo_club = CLUB_DIR . $dbo->getFields("Club", "FotoDiseno1", "IDClub = '" . SIMUser::get("club") . "'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>reservas</title>
  <link href='https://fonts.googleapis.com/css?family=Raleway:200,400,300,700,500,600' rel='stylesheet' type='text/css'>
  <!-- Bootstrap core CSS -->
  <link href="dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap theme -->
  <link href="dist/css/bootstrap-theme.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="theme.css" rel="stylesheet">

  <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
  <!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

  <script src="assets/js/ie-emulation-modes-warning.js"></script>

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  <style>
    .banner {
      height: 250px;
      padding: 5px;
      width: 100%;
    }

    .header {
      height: 50px;
      padding: 5px;
      width: 100%;
    }
  </style>

</head>

<body role="document">

  <div class="banner">

    <div class="container" role="main">
      <div class="">
        <?php
        $tamano = getimagesize($ruta_logo_club);
        $ancho = $tamano[0];              //Ancho
        $alto = $tamano[1];
        if ($ancho > 300) :
          $tamano_logo = 'width="200" height="200"';
        endif;

        ?>

        <div class="pull-right"><img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $tamano_logo; ?> /></div>
      </div>
      <div class="circle">
        <!--
            <img src="assets/img/iconos-esqui.png" />
            -->
      </div>
    </div>

    <div class="page-header">
      <div class="row">
        <div class="col-lg-2">&nbsp;</div>
        <div class="col-lg-4">
          <h3>Reservas</h3>
        </div>
        <div class="col-lg-6">
          <h3 class="fecha"><? echo SIMUtil::tiempo(date("Y-m-d")) ?></h3>
        </div>
      </div>
    </div>
  </div>


  <div class="header">
    <div class="container" role="main">
      <div class="row">
        <div class="col-md-12">
          <table class="table">
            <thead>
              <tr>
                <th>Hora</th>
               
                <?
                $contador_elemento = 1;

                if (empty($_GET["pagina"]) || $_GET["pagina"] = 0) :
                  $desde = 1;
                  $hasta = 7;
                  $proxima_pagina = 1;
                else :
                  $desde = 7;
                  $hasta = 12;
                  $proxima_pagina = 0;
                endif;


                if (count($elementos[$ids]) >= 7) :
                  $parametro_desde = $desde;
                  $parametro_hasta = $hasta;
                else :
                  $parametro_desde = 1;
                  $parametro_hasta = count($elementos[$ids]);
                endif;
                foreach ($elementos[$ids] as $key_elemento => $datos_elemento) {
                ?>

                  <?php if ($contador_elemento >= $parametro_desde && $contador_elemento <= $parametro_hasta) : // mostrar solo los seis primeros 
                  ?>
                    <th width="400px">
                      <?= $datos_elemento["Nombre"] ?>
                    </th>
                  <?php endif; ?>

                <?
                  $contador_elemento++;
                } //end for
                ?>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container" role="main">
      <div class="row">
        <div class="col-md-12">
          <table class="table">

            <tbody>
              <?
              $horaactual = strtotime(date("H:i:s"));


              foreach ($array_horas as $idelemento => $datos_elemento)
                foreach ($datos_elemento as $key_todahora => $datos_horas)
                  foreach ($datos_horas as $key_horas => $info_disponibilidad) {
                    $horamostrar = strtotime($info_disponibilidad["Hora"]);
                    //if( $horamostrar >= $horaactual || $fecha <> date("Y-m-d") )
                    //{
                    //print_r($info_disponibilidad);
                    $mostrar_disponibilidad[$info_disponibilidad["Hora"]][$idelemento] = $info_disponibilidad;

                    //}

                  } //end for

              //mosrar información
              ksort($mostrar_disponibilidad);
              foreach ($mostrar_disponibilidad as $hora => $datos_disponibilidad) {
                $contador_elemento = 1;
              ?>
                <tr>
                  <td>
                    <?php
                    $dia_fecha = date('w', strtotime(date("Y-m-d")));
                    //Hora desde
                    $sql_dispo_elemento_gral = "Select HoraDesde,IDDisponibilidad From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '" . $_GET["ids"] . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $idelement . "|%' Order By HoraDesde ASC Limit 1";
                    $qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
                    $datos_detalle_disponibilidad = $dbo->fetchArray($qry_dispo_elemento_gral);
                    $datos_detalle_disponibilidad["IDDisponibilidad"];
                    if (empty($intervalo_horas)) :
                      $datos_disponibilidad_actual = $dbo->fetchAll("Disponibilidad", " IDDisponibilidad = '" . $datos_detalle_disponibilidad["IDDisponibilidad"] . "' ", "array");
                      $intervalo_horas = $datos_disponibilidad_actual["Intervalo"]; //minutos
                    endif;

                    echo $hora_inicio = substr($hora, 0, 5);

                 /*    $hora_hasta = strtotime("+" . $intervalo_horas . " minute", strtotime($hora_inicio));
                    echo  " - " . date("H:i ", $hora_hasta); */

                    //hora par
                  /*   $sql_horapar = "Select HoraDesde,HoraPar,IDDisponibilidad From ServicioDisponibilidad Where IDDisponibilidad='" .  $datos_detalle_disponibilidad["IDDisponibilidad"] . "' and IDServicio = '" . $_GET["ids"] . "' and   IDDia like '%" . $dia_fecha . "|%' and IDServicioElemento like '%" . $idelement . "|%' AND HoraDesde <='" . $hora_inicio . "' AND  HoraHasta >='" . $hora_inicio . "'   AND Activo='S'";
                    $qry_horapar = $dbo->query($sql_horapar);
                    $datos_hora_par = $dbo->fetchArray($qry_horapar);
 */
                    ?></td>
                 <!--  <td>
                    <?php
                   // echo $datos_hora_par["HoraPar"];
                    ?>
                  </td> -->
                  <?

                  foreach ($elementos[$ids] as $key_elemento => $datos_elemento) {

                  ?>


                    <?php if ($contador_elemento >= $parametro_desde && $contador_elemento <= $parametro_hasta) : // mostrar solo los seis primeros 
                    ?>
                      <td width="400px">
                        <?php
                        if (SIMUser::get("club") == "9") :
                          $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $datos_disponibilidad[$datos_elemento["IDElemento"]]["IDSocio"] . "'");
                          echo $accion_socio . "-";
                        endif;
                        echo $datos_disponibilidad[$datos_elemento["IDElemento"]]["Socio"];
                        //Consulto Invitados						
                        if (!empty($datos_disponibilidad[$datos_elemento["IDElemento"]]["IDReserva"])) :
                          $datos_invitado = "";
                          $sql_invitado = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $datos_disponibilidad[$datos_elemento["IDElemento"]]["IDReserva"] . "'";
                          $result_invitado = $dbo->query($sql_invitado);
                          while ($row_invitado = $dbo->fetchArray($result_invitado)) :
                            if (empty($row_invitado["IDSocio"]))
                              $tipo_invitado = "Socio: ";
                            else
                              $tipo_invitado = "Externo: ";

                            $nom_invitado =   $row_invitado["Nombre"];

                            if (!empty($row_invitado["IDSocio"]) && empty($row_invitado["Nombre"])) :
                              $nom_invitado = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_disponibilidad[$datos_elemento["IDElemento"]]["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_disponibilidad[$datos_elemento["IDElemento"]]["IDSocio"] . "'");
                            endif;
                            echo  "," . strtoupper($nom_invitado) . "<br>";
                          endwhile;
                        //Consulto Boleador escogido							
                        //$id_auxiliar = $dbo->getFields( "ReservaGeneral" , "IDAuxiliar" , "IDReservaGeneral = '" . $datos_disponibilidad[ $datos_elemento["IDElemento"] ][ "IDReserva" ] . "'" );
                        //if((int)$id_auxiliar>0)
                        //echo  " - " .  $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $id_auxiliar . "'" );									

                        endif;

                        ?></td>
                    <?php endif; ?>


                  <?
                    $contador_elemento++;
                  } //end for
                  ?>
                </tr>
              <?
              } //end for
              ?>

            </tbody>
          </table>
        </div>

      </div>


    </div>


  </div> <!-- /container -->


  <!-- Bootstrap core JavaScript
    ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="../../dist/js/bootstrap.min.js"></script>
  <script src="../../assets/js/docs.min.js"></script>
  <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>

  <script src="jquery-scrolltofixed-min.js" type="text/javascript"></script>




  <script>
    jQuery(document).ready(function() {

      <?php if (count($elementos[$ids]) > 7) :  ?>
        setTimeout("location.href='index.php?ids=<?php echo $ids ?>&pagina=<?php echo $proxima_pagina; ?>&action=new'", 20000);
      <?php endif; ?>

      $("html, body").animate({
        scrollTop: $(document).height()
      }, 50000);
      setTimeout(function() {
        $('html, body').animate({
          scrollTop: 0
        }, 50000);
      }, 50000);


      setInterval(function() {
        // 50000 - it will take 4 secound in total from the top of the page to the bottom
        $("html, body").animate({
          scrollTop: $(document).height()
        }, 50000);
        setTimeout(function() {
          $('html, body').animate({
            scrollTop: 0
          }, 50000);
        }, 50000);

        location.reload();


      }, 100000);


      $('.header').scrollToFixed();
      //$('.header').bind('fixed.ScrollToFixed', function() { $(this).css('color', 'red'); });
      //$('.header').bind('unfixed.ScrollToFixed', function() { $(this).css('color', ''); });

      $('.footer').scrollToFixed({
        bottom: 0,
        limit: $('.footer').offset().top,
        preFixed: function() {
          $(this).css('color', 'blue');
        },
        postFixed: function() {
          $(this).css('color', '');
        },
      });

      // Order matters here because we are dependent on the state of the footer above for
      // our limit.  The footer must be set first; otherwise, we will not be in the right
      // position on a window refresh, if the limit is supposed to be invoked.
      $('#summary').scrollToFixed({
        marginTop: $('.header').outerHeight(true) + 10,
        limit: function() {
          var limit = $('.footer').offset().top - $('#summary').outerHeight(true) - 10;
          return limit;
        },
        minWidth: 1000,
        zIndex: 999,
        fixed: function() {},
        dontCheckForPositionFixedSupport: true
      });

      $('#summary').bind('unfixed.ScrollToFixed', function() {
        if (window.console) console.log('summary preUnfixed');
      });
      $('#summary').bind('unfixed.ScrollToFixed', function() {
        if (window.console) console.log('summary unfixed');
        $(this).css('color', '');
        $('.header').trigger('unfixed.ScrollToFixed');
      });
      $('#summary').bind('fixed.ScrollToFixed', function() {
        if (window.console) console.log('summary fixed');
        $(this).css('color', 'red');
        $('.header').trigger('fixed.ScrollToFixed');
      });


    });
  </script>


</body>

</html>