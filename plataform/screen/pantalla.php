<?
  include( "../procedures/general.php" );
  include( "../procedures/reserva.php" );
  
  
  
  $logo_club = CLUB_ROOT.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" ); 
  $ruta_logo_club = CLUB_DIR.$dbo->getFields( "Club" , "FotoDiseno1" , "IDClub = '".SIMUser::get("club")."'" ); 
  
  
  switch($_GET["ids"]):
  	case "273":
	case "217":
	case "32":
		$servicios_relacionados = array("273","217","32");
	break;
	//Vestir Caballero
	case "125":
	case "180":
	case "228":
		$servicios_relacionados = array("125","180","228");
	break;
	//Vestir Dama
	case "181":
	case "352":	
		$servicios_relacionados = array("181","352");
	break;
	default:	
		$servicios_relacionados = array($_GET["ids"]);
  endswitch;
  
 // $servicios_relacionados = array("178","351","32");
  //$servicios_relacionados = array("129");
  
  $id_servicio = $_GET["ids"];
  
  if(count($servicios_relacionados)>1):
  	$clave = array_search($id_servicio, $servicios_relacionados);
	$siguiente_clave = (int)$clave+1;
	$siguiente_servicio = $servicios_relacionados[$siguiente_clave];
	if(!empty($siguiente_servicio)):
		$id_servicio = $siguiente_servicio;
		header("Refresh: 40; URL='pantalla.php?ids=$id_servicio'");
	else:
		$id_servicio = $servicios_relacionados[0];	
		header("Refresh: 40; URL='pantalla.php?ids=$id_servicio'");
	endif;
		 
	//echo $id_servicio;
	
  endif;
  
  
  //$id_servicio = $_GET["ids"];
  
 	 $dia_fecha= date('w', strtotime(date("Y-m-d")));
	  //Consulto los elementos de los servicios
	  $sql_elemento = "Select * From ServicioElemento Where IDServicio = '".$id_servicio."' Order By Orden";
	  $result_elemento = $dbo->query($sql_elemento);	  
	  while($row_elemento = $dbo->fetchArray($result_elemento)):
	  	// Verifico si el dia de hoy tiene disponibilidad el elemento
		$sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDServicio = '".$id_servicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$row_elemento["IDServicioElemento"]."|%'";
		$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
		if( $dbo->rows( $qry_dispo_elemento_gral ) > 0 ){	
		
				 //Verifico si tene disponibilidad  general el elemento				
						if(empty($hora_inicio_mostrar)):
							
							//Hora desde
							$sql_dispo_elemento_gral = "Select HoraDesde,IDDisponibilidad From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$id_servicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$row_reserva["IDServicioElemento"]."|%' Order By HoraDesde ASC Limit 1";
							$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
							$datos_detalle_disponibilidad = $dbo->fetchArray($qry_dispo_elemento_gral);						
							$hora_inicio_mostrar=$datos_detalle_disponibilidad["HoraDesde"];
							//Hora hasta
							$sql_dispo_max = "Select HoraHasta From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$id_servicio."' and   IDDia like '%".$dia_semana."|%'  Order By HoraHasta DESC Limit 1";
							$qry_dispo_max= $dbo->query($sql_dispo_max);
							$r_dispo_max = $dbo->fetchArray($qry_dispo_max);
							$hora_fin_mostrar = $r_dispo_max["HoraHasta"];	
							
							$datos_disponibilidad = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $datos_detalle_disponibilidad["IDDisponibilidad"] . "' ", "array" );
							$intervalo_horas = $datos_disponibilidad["Intervalo"]; //minutos				
							
						endif;	
					
		
				
				$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , " IDServicio  = '" . $row_elemento["IDServicio"]  . "'" );
				$nombre_servicio_maestro = $dbo->getFields( "ServicioMaestro" , "Nombre" , " IDServicioMaestro  = '" . $id_servicio_maestro  . "'" );
				$array_encabezado []= $nombre_servicio_maestro . "|" . $row_elemento["Nombre"] . "|" . $row_elemento["IDServicioElemento"] . "|". $id_servicio;
				//Consulto qlas reservas dl dia del elemento
				$sql_reserva = "Select * From ReservaGeneral Where IDServicioElemento = '".$row_elemento["IDServicioElemento"]."' and Fecha = '".date("Y-m-d")."' and IDEstadoReserva = 1";
				$result_reserva = $dbo->query($sql_reserva);
				
				while($row_reserva = $dbo->fetchArray($result_reserva)):
				
					$nombre_socio_reserva = $dbo->getFields( "Socio" , "Nombre" , " IDSocio  = '" . $row_reserva["IDSocio"]  . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , " IDSocio  = '" . $row_reserva["IDSocio"]  . "'" );
					
					
					$array_nombre_socio = explode(" ",$nombre_socio_reserva);
					if(count($array_nombre_socio)>=3):
						$nombre_socio_pantalla = $array_nombre_socio[0] . " " . $array_nombre_socio[2];
					elseif(count($array_nombre_socio)<=2):	
						$nombre_socio_pantalla = $array_nombre_socio[0] . " " . $array_nombre_socio[1];
					endif;
					
					
					$accion_socio_reserva = $dbo->getFields( "Socio" , "Accion" , " IDSocio  = '" . $row_reserva["IDSocio"]  . "'" );
					//$array_elemento_reserva[$row_reserva["IDServicioElemento"]][$row_reserva["Hora"]] = $nombre_socio_pantalla . "<br>" . $accion_socio_reserva;
					$array_elemento_reserva[$row_reserva["IDServicioElemento"]][$row_reserva["Hora"]] = $nombre_socio_reserva;
					//verifico cuantos bloques de 15 ocupa esta reserva
					
					
					
						
						
		endwhile;
		
		} // En if rows
		
		
	  endwhile;
	
  
//  print_r($array_elemento_reserva);
 // exit;
  
  
  
  	
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
  background: #333; 
  color: white; 
  font-weight: bold; 
  text-align:center;
}
td, th { 
  padding: 6px; 
  border: 1px solid #ccc; 
  text-align: center; 
  font-size:12px;
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
					 $columnas = (int)count($array_encabezado);
                     echo $columnas;
					 ?>
                     ">
                <h3 class="fecha">Reservas: <? echo SIMUtil::tiempo( date( "Y-m-d" ) ) ?></h3>
                </th>                     
                <th align="center" style="background-color:#FFFCFC;">
                <?php
				$tamano = getimagesize($ruta_logo_club);
					$ancho = $tamano[0];              //Ancho
					$alto = $tamano[1];  
				if($ancho>150):
					$tamano_logo = 'width="90" height="80"';
				endif;
				?>
                	<img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $tamano_logo; ?> />
                </th>
            </tr>
            <tr>
                <th>Hora</th>
                <?php foreach($array_encabezado as $nombre_encabezado): ?>            
                <th align="center"><?php 
                    $array_datos_encabezado = explode("|",$nombre_encabezado);
                    echo $array_datos_encabezado[0] . "<br>" . $array_datos_encabezado[1]; 				
                    ?>
                </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
      <?php 
	  
	  $hora_inicial = strtotime($hora_inicio_mostrar);
	  $hora_final = strtotime($hora_fin_mostrar);	 
	 //echo "<br>" . date("H:i:s",$hora_inicial) . ">=" . date("H:i:s",$hora_final);	
	  for($contador_hora = 1; $contador_hora<=50; $contador_hora++): 	    
	  	//Solo muestro las horas que faltan
	  	$hora_actual = strtotime("-1 hours",strtotime(date("H:i:s")));
		//$hora_actual = strtotime("-1 hours",strtotime("07:00:00"));
		//echo "<br>" . date("H:i:s",$hora_inicial) . ">=" . date("H:i:s",$hora_actual);		
	  	if($hora_inicial>=$hora_actual && $hora_inicial<=$hora_final):
				  ?>
					<tr>
					  <td><?php  					 
					  echo date("h:i a",$hora_inicial);
					  ?></td>
					   <?php foreach($array_encabezado as $nombre_encabezado): ?>            
					   <td>
							<?php 
							//consulto si tiene algo reservado						
							$array_datos_encabezado = explode("|",$nombre_encabezado);				
							echo $array_elemento_reserva[$array_datos_encabezado[2]][date("H:i:s",$hora_inicial)]; 
							?>
					   </td>
					   <?php endforeach; ?>
				  </tr>
      <?php 
	  	endif;
		$hora_inicial = strtotime("+".$intervalo_horas." minute",$hora_inicial);
	  endfor; ?>
      </tbody>
    </table>
    
   
    
     <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="../../dist/js/bootstrap.min.js"></script>
    <script src="../../assets/js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>

    <script src="jquery-scrolltofixed-min.js" type="text/javascript"></script>

	 <script src="assets/js/goheadfixed.js" type="text/javascript"></script>


    <script>
        jQuery( document ).ready(function(){
          
			

          $("html, body").animate({ scrollTop: $(document).height() }, 50000);
          setTimeout(function() {
             $('html, body').animate({scrollTop:0}, 50000); 
          },50000);


          setInterval(function(){
               // 50000 - it will take 4 secound in total from the top of the page to the bottom
              $("html, body").animate({ scrollTop: $(document).height() }, 50000);
              setTimeout(function() {
                 $('html, body').animate({scrollTop:0}, 50000); 
              },50000);

              
			  

          },10000);


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
		
		goheadfixed('table.fixed');

    </script>

  </body>
</html>
