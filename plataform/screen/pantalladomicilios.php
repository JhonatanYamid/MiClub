<?

  include( "../procedures/general.php" );
  $fecha_hoy=date("Y-m-d") . " 00:00:00";
  //$fecha_hoy="2019-01-01 00:00:00";
  $sql_pedido="SELECT *
               FROM Domicilio D, Socio S
               WHERE D.IDSocio=S.IDSocio and D.IDClub = '".SIMUser::get("club")."' and HoraEntrega >= '".$fecha_hoy."'
               and IDEstadoDomicilio = 1
               ORDER BY HoraEntrega ASC";
  $r_pedido=$dbo->query($sql_pedido);

  $logo_club = CLUB_ROOT.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" );
  $ruta_logo_club = CLUB_DIR.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" );


	$colortv="#333";

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="refresh" content="30" />
    <title>Domicilios</title>
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
					 $columnas = 3;
                     echo $columnas_titulo;
					 ?>
                     ">
                <h3 class="fecha">PEDIDOS: <? echo SIMUtil::tiempo( date( "Y-m-d" ) ) ?></h3>
                </th>
                <th align="center" style="background-color:<?php if($color_personalizado=="S") echo $colortv; else echo "#FFFCFC"; ?>;" <?php if($columnas>=10): $columna_ultima = ((int)count($elementos[$ids])-$columnas_titulo)+1; echo "colspan = '".$columna_ultima."' "; endif;  ?>>
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
                <th>Hora</th>
                <th>Socio</th>
                <th>Pedido</th>
                <th>Comentario</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>

            <?php while($row_pedido = $dbo->fetchArray($r_pedido)){ ?>
              <tr>
                <td style="text-align:center !important"><?php echo substr($row_pedido["HoraEntrega"],10); ?></td>
                <td style="text-align:left !important"><?php echo $row_pedido["Nombre"] . " " . $row_pedido["Apellido"] . " - " .$row_pedido["Accion"]; ?></td>
                <td style="text-align:left !important">
                  <?php

                   $sql_detalle_pedido=$dbo->query("SELECT * from DomicilioDetalle where IDDomicilio = '".$row_pedido[IDDomicilio]."'");
                  ?>


                  <table style="width:100% !important" >
                                  <tr>

                                          <th style="width:30%">Nombre</th>
                                          <th style="width:30%">Cantidad</th>
                                          <th style="width:30%">Comentario</th>


                                  </tr>
                                  <tbody id="listacontactosanunciante">
                                  <?php


                                          while($r_detalle_pedido=$dbo->object($sql_detalle_pedido))
                                          {
                                  ?>

                                  <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">

                                          <td><?php echo $dbo->getFields( "Producto" , "Nombre" , "IDProducto = '" . $r_detalle_pedido->IDProducto."'" ); ?></td>
                                          <td>
                                          <?php echo number_format($r_detalle_pedido->Cantidad,0,",","."); ?>
                                          </td>
                                          <td>
                                          <?php echo $r_detalle_pedido->Comentario; ?>
                                          </td>

                                    </tr>
                                  <?php
                                  }
                                  ?>
                                  </tbody>
                                  <tr>
                                          <th class="texto" colspan="13"></th>
                                  </tr>
                          </table>

                </td>
                <td style="text-align:center !important"><?php echo $row_pedido["ComentariosSocio"]; ?></td>
                <td style="text-align:center !important"><?php echo $dbo->getFields( "EstadoDomicilio" , "Nombre" , "IDEstadoDomicilio = '" . $row_pedido["IDEstadoDomicilio"] . "'" ); ?></td>
              </tr>
            <?php  }  ?>

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

			<?php if(count($elementos[$ids])>=1):	?>
				setTimeout("location.href='pantallav2.php?ids=<?php echo $ids?>&pagina=<?php echo $proxima_pagina; ?>&action=new&id=<?php echo rand(0,10000);?>'", 20000);
			<?php endif; ?>

          $("html, body").animate({ scrollTop: $(document).height() }, 300000);
          setTimeout(function() {
             $('html, body').animate({scrollTop:0}, 300000);
          },300000);


          setInterval(function(){
               // 50000 - it will take 4 secound in total from the top of the page to the bottom
              $("html, body").animate({ scrollTop: $(document).height() }, 300000);
              setTimeout(function() {
                 $('html, body').animate({scrollTop:0}, 90000);
              },300000);

              location.reload();


          },900000);


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
