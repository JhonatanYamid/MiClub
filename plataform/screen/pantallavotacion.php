<?

  require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
  //require( "../admin/config.inc.php" );


  $logo_club = CLUB_ROOT.$dbo->getFields( "Club" , "Foto" , "IDClub = '".$_GET["IDClub"]."'" );
  $ruta_logo_club = CLUB_DIR.$dbo->getFields( "Club" , "Foto" , "IDClub = '".$_GET["IDClub"]."'" );
  $datos_evento=$dbo->fetchAll( "VotacionEvento", " IDVotacionEvento = '" . $_GET["IDVotacionEvento"] . "' ", "array" );
  $sql_quorum="SELECT IDVotacionVotante,Coeficiente From VotacionVotante WHERE IDVotacionEvento = '".$_GET["IDVotacionEvento"]."' and Presente = 'S'";
  $suma_coeficiente=0;
  $suma_presentes=0;
  $suma_representados=0;
  $r_quorum=$dbo->query($sql_quorum);
  while($row_quorum = $dbo->fetchArray($r_quorum)){
    $suma_coeficiente+=$row_quorum["Coeficiente"];
    $suma_presentes++;
    $sql_poder="SELECT IDVotacionVotanteDelegaPoder
                FROM VotacionPoder
                WHERE IDVotacionVotante = '".$row_quorum["IDVotacionVotante"]."'";
    $r_poder=$dbo->query($sql_poder);
    while($row_poder = $dbo->fetchArray($r_poder)){
      $datos_delega=$dbo->fetchAll( "VotacionVotante", " IDVotacionVotante = '" . $row_poder["IDVotacionVotanteDelegaPoder"] . "' ", "array" );
      $suma_coeficiente+=$datos_delega["Coeficiente"];
      $suma_representados++;
    }
  }




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Votaciones</title>
    <link href='https://fonts.googleapis.com/css?family=Raleway:200,400,300,700,500,600' rel='stylesheet' type='text/css'>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="dist/css/bootstrap-theme.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
    .banner {}

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
        table-layout: fixed;
        /**Forzamos a que las filas tenga el mismo ancho**/
        width: 100%;
        /*El ancho que necesitemos*/
    }

    /* Zebra striping */
    tr:nth-of-type(odd) {
        background: #eee;
    }

    th {
        background: #fff;
        color: #000;
        font-weight: bold;
        text-align: center;
    }

    td,
    th {
        padding: 6px;
        border: 1px solid #ccc;
        text-align: center;
        font-size: <?php if(SIMUser::get("club")==34) echo "16px";
        else echo "12px";
        ?>;
        margin: 0;
        word-wrap: break-word;
        /*Si el contenido supera el tamano, adiciona a una nueve linea**/
        font-weight: bold;
    }

    thead {}

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
                <th>
                    <h3 class="fecha">
                        <? echo $datos_evento["Nombre"] . " " . SIMUtil::tiempo( date( "Y-m-d" ) ) ?>
                    </h3>
                </th>
                <th align="center" style="background-color:<?php if($color_personalizado=="S") echo $colortv; else echo "#FFFCFC"; ?>;" <?php if($columnas>=10): $columna_ultima = ((int)count($elementos[$ids])-$columnas_titulo)+1; echo "colspan = '".$columna_ultima."' "; endif;  ?>> <?php
                $tamano = getimagesize($ruta_logo_club);
                  $ancho = $tamano[0];              //Ancho
                  $alto = $tamano[1];
                if($ancho>155):
                  $tamano_logo = 'width="135" height="60"';
                endif;
                ?> <img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $tamano_logo; ?> />
                </th>
            </tr>
        </thead>
        <tr>
            <th colspan="2">
                <table>
                    <tr style="background-color:#F5F5BF;">
                        <td>QUÓRUM:</td>
                        <td>
                            <h1><?php echo $suma_coeficiente; ?></h1>
                        </td>
                        <td>Total Personas presentes:</td>
                        <td><?php echo $suma_presentes; ?></td>
                        <td>Total Representados:</td>
                        <td><?php echo $suma_representados; ?></td>
                    </tr>
                </table>
            </th>
        </tr>
        <tbody> 
          <?php
          $sql_votaciones="SELECT V.* FROM VotacionEventoVotacion VEV, Votacion V WHERE V.IDVotacion=VEV.IDVotacion and VEV.IDVotacionEvento = '".$_GET["IDVotacionEvento"]."' and V.MostrarResultados='S'";
          $r_votaciones=$dbo->query($sql_votaciones);
          while($row_votacion=$dbo->FetchArray($r_votaciones)){
            $maximo_colum=2;
            $contador_colum=0;?> 
            <tr>
              <th valign="top" colspan="2">
                <table>
                    <tr style="background-color:#E8F5EA;">
                        <td colspan="<?php echo $maximo_colum; ?>"><?php echo $row_votacion["Nombre"]; ?></td>
                    </tr>
                    <tr> <?php
                    $sql_preguntas="SELECT IDPregunta, EtiquetaCampo,Valores FROM PreguntaVotacion WHERE IDVotacion = '".$row_votacion["IDVotacion"]."' Order by Orden";
                    $r_preguntas=$dbo->query($sql_preguntas);
                    while($row_preguntas=$dbo->fetchArray($r_preguntas)){
                      $contador_colum++;
                      $TotalVotos=0;
                      $array_etiqueta=array();
                      $array_votos=array();
                      $array_datos=array();
                      $array_id_preguntas[]=$row_preguntas["IDPregunta"];
                      $array_etiqueta_preguntas[$row_preguntas["IDPregunta"]]=$row_preguntas["EtiquetaCampo"];
                      ?> <td> <?php
                          //echo $row_preguntas["EtiquetaCampo"]."<br><br>";
                          $valor_mayor=0;
                          $valores_respuesta=explode("|",$row_preguntas["Valores"]);
                          if(count($valores_respuesta)>0){
                          foreach($valores_respuesta as $valor){
                            if(!empty($valor)){
                              $array_etiqueta[]="'".$valor."'";
                              //echo " | " . $valor . "=";
                              //Resultados

                              $sql_total_votos="SELECT SUM(PesoVoto) as TotalVotos, Valor
                                              FROM VotacionRespuesta
                                              WHERE IDPregunta = '".$row_preguntas["IDPregunta"]."' and Valor= '".$valor."'";
                              $r_total_votos=$dbo->query($sql_total_votos);
                              $row_total_votos=$dbo->fetchArray($r_total_votos);

                              $sql_resultado="SELECT SUM(PesoVoto) as TotalVotos, Valor
                                              FROM VotacionRespuesta
                                              WHERE IDPregunta = '".$row_preguntas["IDPregunta"]."' and Valor= '".$valor."'";
                              $r_resultado=$dbo->query($sql_resultado);
                              $row_resultado=$dbo->fetchArray($r_resultado);
                              //echo $row_resultado["TotalVotos"] . " | ";
                              $array_votos[]=number_format($row_resultado["TotalVotos"],'3','.',',');
                              if((int)$row_resultado["TotalVotos"]>$valor_mayor){
                                $valor_mayor=(int)$row_resultado["TotalVotos"];
                              }
                              $valor_mayor=
                              $TotalVotos+=$row_total_votos["TotalVotos"];
                            }
                          }
                        }
                        ?> <?php
                        $valores = implode(",",$array_votos);
                        $array_datos[] = "{
                          label: '".$row_preguntas["EtiquetaCampo"]."',
                          backgroundColor: [randomColor(),randomColor(),randomColor(),randomColor(),randomColor(),randomColor()],
                          data: [".$valores."]
                        }";


                        $array_grafica[$row_preguntas["IDPregunta"]]='var barChartData'.$row_preguntas["IDPregunta"].' = {
                                  labels: ['.implode(",",$array_etiqueta).'],
                                  datasets: [
                              '.implode(",",$array_datos).'
                            ]
                      };';

                        ?>  
                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                          <tr>
                            <td>Total Votos</td>
                            <td><?php echo $TotalVotos; ?></td>
                          </tr>
                          <tr>
                            <td colspan="2"><?php echo $array_etiqueta_preguntas[$row_preguntas["IDPregunta"]]; ?></td>                            
                          </tr>
                        </table>
                            <div style="width: 100%;">                                 
                                <canvas id="canvas<?php echo $row_preguntas["IDPregunta"]; ?>" ></canvas>
                            </div>
                        </td> <?php
                      if($contador_colum==3){
                        echo "</tr><tr>";
                        $contador_colum=0;
                      }
                    } ?> </tr>
                </table>
              </th> <?php } ?>
            </tr>
        </tbody>
    </table>
    <!-- basic scripts -->
    <!-- <![endif]-->
    <!--[if IE]>
    <script src="../assets/js/jquery.1.11.1.min.js"></script>
    <![endif]-->
    <!--[if !IE]> -->
    <script type="text/javascript">
    window.jQuery || document.write("<script src='../assets/js/jquery.min.js'>" + "<" + "/script>");
    </script>
    <!-- <![endif]-->
    <!--[if IE]>
    <script type="text/javascript">
     window.jQuery || document.write("<script src='../assets/js/jquery1x.min.js'>"+"<"+"/script>");
    </script>
    <![endif]-->
    <!-- Char -->
    <script src="../assets/js/Chart.js-master/dist/Chart.bundle.js"></script>
    <script>
    var randomScalingFactor = function() {
        return Math.round(Math.random() * 100);
    };
    var randomColorFactor = function() {
        return Math.round(Math.random() * 255);
    };
    var randomColor = function() {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',.7)';
    };
    <?php foreach($array_grafica as $datos_graph){
              echo $datos_graph;
          } ?>
    window.onload = function() {
        <?php foreach ($array_id_preguntas as $id_pregunta) { ?>
        var ctx = document.getElementById("canvas<?php echo $id_pregunta;  ?>").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData<?php echo $id_pregunta;  ?>,
            options: {
                //Elements options apply to all of the options unless overridden in a dataset
                //In this case, we are setting the border of each bar to be 2px wide and green
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgb(0, 255, 0)',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0,
                            stepSize: <?php echo $valor_mayor;  ?>,
                        },
                        position: 'left',
                        gridLines: {
                            display: false
                        },
                    }]
                    
                },
                legend: {
                    position: 'top',
                    display: true
                },
                title: {
                    display: false,
                    text: '<?php echo $array_etiqueta_preguntas[$id_pregunta]; ?>',
                    fontSize: 18,
                    fullSize: false,
                   
                },
                animation: {
                    duration: 500,
                    easing: "easeOutQuart",
                    onComplete: function() {
                        var ctx = this.chart.ctx;
                        ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        this.data.datasets.forEach(function(dataset) {
                            for (var i = 0; i < dataset.data.length; i++) {
                                var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
                                    scale_max = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._yScale.maxHeight;
                                ctx.fillStyle = '#444';
                                var y_pos = model.y - 5;
                                // Make sure data value does not get overflown and hidden
                                // when the bar's value is too close to max value of scale
                                // Note: The y value is reverse, it counts from top down
                                if ((scale_max - model.y) / scale_max >= 0.93) y_pos = model.y + 20;
                                ctx.fillText(dataset.data[i], model.x, y_pos);
                            }
                        });
                    }
                }
            }
        });
        <?php } ?>
    };
    </script>
</body>

</html>