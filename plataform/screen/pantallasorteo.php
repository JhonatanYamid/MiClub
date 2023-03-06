<?

  include( "../procedures/general.php" );

  header("Refresh: 60; URL='pantallasorteo.php?a=".rand(1,1000));

  $sql = "SELECT scd.IDCategoriaCaddie, cc.nombre AS categoria, c.nombre, c.apellido,c.Codigo, "
          . "c.numeroDocumento, scd.estado, c.IDCaddie, s.IDSorteoCaddie, scd.IDSorteoCaddieDetalle, "
          . "t.codigo AS codigoTalega "
          . "from SorteoCaddie s "
          . "INNER JOIN ( SELECT MAX(IDSorteoCaddie) AS maximoId "
          . "             FROM SorteoCaddie "
          . "             WHERE IDClub = " . SIMUser::get("club") . " "
          . "             AND DATE_FORMAT(NOW(),'%Y-%m-%d') BETWEEN fechaInicio AND fechaFin "
          . "             ) usor ON(s.IDSorteoCaddie = usor.maximoId) "
          . "INNER JOIN SorteoCaddieDetalle scd ON(s.IDSorteoCaddie = scd.IDSorteoCaddie) "
          . "INNER JOIN Caddie c ON(scd.IDCaddie = c.IDCaddie) "
          . "INNER JOIN CategoriaCaddie cc ON(scd.IDCategoriaCaddie = cc.IDCategoriaCaddie) "
          . "LEFT JOIN Talega t ON(scd.IDTalega = t.IDTalega) "
          . "WHERE s.IDClub = " . SIMUser::get("club") . " $condicion "
          . "ORDER BY cc.orden, scd.orden ASC ";

  $result = $dbo->query($sql);
  while ($row = $dbo->fetchArray($result)) {
      $array_caddie[$row["IDCategoriaCaddie"]][] = $row;
  }

  $logo_club = CLUB_ROOT.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" );
  $ruta_logo_club = CLUB_DIR.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" );

	$colortv="#333";

  $consulto_categorias="SELECT * FROM CategoriaCaddie WHERE IDClub = '".SIMUser::get("club")."'";
  $result_cat=$dbo->query($consulto_categorias);
  while($row_cat=$dbo->fetchArray($result_cat)){
      if(count($array_caddie[$row_cat["IDCategoriaCaddie"]])>0){
        $array_categorias[]=$row_cat;
      }
  }
  $cantidad_categorias=count($array_categorias);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Reservas</title>
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
}
.header {
    height: 50px;
    padding: 5px;
    width: 100%;
}

/*
Generic Styling, for Desktops/Laptops
*/
table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;/**Forzamos a que las filas tenga el mismo ancho**/
    width: 100%; /*El ancho que necesitemos*/
}
/* Zebra striping */
tr:nth-of-type(odd) {
  background: #eee;
}
th {
  background: <?php echo $colortv; ?>;
  color: white;
  font-weight: bold;
  text-align:center;
}
td, th {
  padding: 6px;
  border: 1px solid #ccc;
  text-align: center;
  font-size:<?php if(SIMUser::get("club")==34) echo "16px"; else echo "12px"; ?>;
  margin: 0;
  word-wrap: break-word;/*Si el contenido supera el tamano, adiciona a una nueve linea**/
  font-weight: bold;

}

thead{
}


.cheader {
    background-color: #428BCA;
	color: #FFFFFF;
}

</style>

  </head>
  <body>
    <table class="fixed">
    	<thead>
        <tr>
          <th colspan="<?php
					 echo (int)($cantidad_categorias-1);
					 ?>
                     ">
                <h3 class="fecha">SORTEO CADDIES: <? echo $servicio_personalizado . " " . SIMUtil::tiempo( date( "Y-m-d" ) ) ?></h3>
                </th>
                <th align="center" style="background-color:<?php if($color_personalizado=="S") echo $colortv; else echo "#FFFCFC"; ?>;" >
                <?php
				$tamano = getimagesize($ruta_logo_club);
					$ancho = $tamano[0];              //Ancho
					$alto = $tamano[1];
				if($ancho>155):
					$tamano_logo = 'width="155" height="80"';
				endif;
				?>
                	<img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $tamano_logo; ?> />
                </th>
            </tr>
            <tr>
                <?php
                foreach($array_categorias as $datos_categoria){ ?>
                    <th><?php echo $datos_categoria["nombre"];?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                foreach($array_categorias as $datos_categoria){ ?>
                    <td valign="top">
                      <table>
                        <tr style="color:#FFF">
                          <td bgcolor="#4a6451">Posicion</td>
                          <td bgcolor="#4a6451">Nombre - Codigo</td>
                          <td bgcolor="#4a6451">Estado</td>
                        </tr>
                      <?php
                      $contador=1;
                      foreach($array_caddie[$datos_categoria["IDCategoriaCaddie"]] as $datos_caddie){
                        ?>
                            <tr>
                              <td><?php echo $contador; ?></td>
                              <td><?php echo $datos_caddie["nombre"] .  " " . $datos_caddie["apellido"] . " - " . $datos_caddie["Codigo"]; ?></td>
                              <td>
                                <?php
                                switch($datos_caddie["estado"]){
                                  case "1";
                                    echo '<span style="color:#092dc1">Disponible</span>';
                                  break;
                                  case "2";
                                    echo '<span style="color:#f41f42">En campo</span>';
                                  break;
                                  case "3";
                                    echo 'Inactivo';
                                  break;
                                }
                                ?>
                                </td>
                            </tr>
                      <?
                      $contador++;
                    } ?>
                      </table>
                  </td>
                <?php } ?>

            </tr>
      </tbody>
    </table>



     <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script src="jquery-scrolltofixed-min.js" type="text/javascript"></script>

	 <script src="assets/js/goheadfixed.js" type="text/javascript"></script>


    <script>
        jQuery( document ).ready(function(){



      $("html, body").animate({ scrollTop: $(document).height() }, 80000);
      setTimeout(function() {
         $('html, body').animate({scrollTop:0}, 80000);
      },80000);





          $('.header').scrollToFixed();
          //$('.header').bind('fixed.ScrollToFixed', function() { $(this).css('color', 'red'); });
          //$('.header').bind('unfixed.ScrollToFixed', function() { $(this).css('color', ''); });

          $('.footer').scrollToFixed( {
              bottom: 0,
              limit: $('.footer').offset().top,
              preFixed: function() { $(this).css('color', 'blue'); },
              postFixed: function() { $(this).css('color', ''); },
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
              fixed: function() {  },
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
