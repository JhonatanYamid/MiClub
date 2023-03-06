<?
  require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
  //Copio los datos del dia a la tabla de VarnishStat
					$time_start = SIMUtil::getmicrotime();

$YearMenorEdad=date("Y")-18;

if(empty($_GET["IDClub"]))
  $_GET["IDClub"]=9;

  $datos_cub=$dbo->fetchAll( "Club", " IDClub = '" . $_GET["IDClub"] . "' ", "array" );
  $logo_club = CLUB_ROOT.$datos_cub["FotoDiseno1"];
  $ruta_logo_club = CLUB_DIR.$datos_cub["FotoDiseno1"];

  $sql_predio="SELECT Predio FROM Predio WHERE 1 Group BY Predio Order by Predio ";
  $r_predio=$dbo->query($sql_predio);
  while($row_predio=$dbo->fetchArray($r_predio)){
    $array_predio[]=$row_predio["Predio"];
  }

  $sql_socios_ocp="SELECT Socios
                   FROM SocioOcupacion
                   WHERE Fecha = '".date("Y-m-d")."' and IDClub = '".$_GET["IDClub"]."' and Socios <> ''
                   ORDER BY IDSocioOcupacion DESC
                   LIMIT 1 ";
  $r_soc=$dbo->query($sql_socios_ocp);
  $row_soc=$dbo->fetchArray($r_soc);

   $sql_acceso_ocp="SELECT IDLogAcceso
                    FROM SocioOcupacion
                    WHERE Fecha = '".date("Y-m-d")."' and IDClub = '".$_GET["IDClub"]."' and IDLogAcceso <> ''
                    ORDER BY IDSocioOcupacion DESC
                    LIMIT 1 ";
  $r_acce=$dbo->query($sql_acceso_ocp);
  $row_acceso=$dbo->fetchArray($r_acce);


//Socios
$sql_soc="SELECT  IDSocio, Predio,FechaNacimiento FROM Socio WHERE IDSocio in (".$row_soc["Socios"].")";
$r_soc=$dbo->query($sql_soc);
while($row_soc=$dbo->fetchArray($r_soc)){
  $year_nac=substr($row_soc["FechaNacimiento"],0,4);
  if((int)$year_nac>=$YearMenorEdad && (int)$year_nac!=0 ){
    $MenorEdad="S";
  }
  else{
    $MenorEdad="N";
  }

  if(empty($row_soc["Predio"])){
    //Busco en acceso a cual casa ingreso
    $sql_log="SELECT Predio FROM LogAcceso WHERE IDInvitacion = '".$row_soc["IDSocio"]."' and Tipo = 'Socio' ORDER BY IDLogAcceso LIMIT 1";
    $r_log=$dbo->query($sql_log);
    $row_log=$dbo->fetchArray($r_log);
    if(!empty($row_log["Predio"])){
      $Predio=$row_log["Predio"];
    }
  }
  else{
    $Predio=$row_soc["Predio"];
  }

  if(!empty($Predio)){
    if(!in_array($Predio,$array_predio)){
      $array_predio[]=$Predio;
    }
    //Inserto los datos del predio
    $array_ocupacion_predio[$Predio]["Socio"]++;
    if($MenorEdad=="S"){
      $array_ocupacion_predio[$Predio]["SocioNino"]++;
    }
  }
}

//Invitados
$sql_inv="SELECT  IDInvitacion,Tipo,Predio,CamposAcceso FROM LogAcceso WHERE IDLogAcceso in (".$row_acceso["IDLogAcceso"].") and Tipo <> 'Socio'  ";
//$sql_inv="SELECT  IDLogAcceso,IDInvitacion,Tipo,Predio,CamposAcceso FROM LogAcceso WHERE IDLogAcceso in ('4339759') and Tipo <> 'Socio'";
$r_inv=$dbo->query($sql_inv);
while($row_inv=$dbo->fetchArray($r_inv)){
  $Predio="";
  $array_otros_datos=explode("||",$row_inv["CamposAcceso"]);
  foreach($array_otros_datos as $datos_acceso){
    $pos = strpos($datos_acceso, "edad");
    if ($pos === false) {
      $MenorEdadInv="N";
    } else {
      $array_dato=explode(":",$datos_acceso);
      if($array_dato[1]=="S" || $array_dato[1]=="S|"){
        $MenorEdadInv="S";
      }
      else{
        $MenorEdadInv="N";
      }
    }
  }

  if(!empty($row_inv["Predio"])){
    $Predio=$row_soc["Predio"];
  }

  if(!empty($Predio)){
    if(!in_array($Predio,$array_predio)){
      $array_predio[]=$Predio;
    }
    echo "<br>PREDIO " . $Predio;
    //Inserto los datos del predio
    $array_ocupacion_predio[$Predio]["Invitado"]++;
    if($MenorEdadInv=="S"){
      $array_ocupacion_predio[$Predio]["InvitadoNino"]++;
    }

    //$array_ocupacion_predio[$Predio]["Invitado"]++;
    //$array_ocupacion_predio[$Predio]["InvitadoNino"]++;

  }

}

$array_colores=array("#EFC1E6","#2F64C8","#6DEC32","#FEFBB5","#428BCA");






?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Reporte Diario de casas ocupadas <?php echo $datos_cub["Nombre"]; ?></title>

	<link href='https://fonts.googleapis.com/css?family=Raleway:200,400,300,700,500,600' rel='stylesheet' type='text/css'>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="dist/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>


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

th {
  background: #fff;
  color: #000;
  font-weight: bold;
  text-align:center;
}
td, th {
  padding: 3px;
  border: 1px solid #ccc;
  text-align: center;
  font-size:<?php if(SIMUser::get("club")==34) echo "16px"; else echo "10px"; ?>;
  margin: 0;
  word-wrap: break-word;/*Si el contenido supera el tamano, adiciona a una nueve linea**/
  font-weight: bold;
}
tr{
  height: 10px;
}

thead{
}

.rosado{
  background-color: #EFC1E6;
}

.azul{
  background-color: #2F64C8;
  color:#FFF;
}

.verde{
  background-color: #6DEC32;
}

.amarillo{
  background-color: #FEFBB5;
}
.blanco{
  background-color: #FFF;
}
.rojo{
  background-color: #F4535F;
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
                <th width="80%">
                <h3 class="fecha">Reporte Diario de casas ocupadas <?php echo $datos_cub["Nombre"] ." " . SIMUtil::tiempo( date( "Y-m-d H:i:s" ) ) ?></h3>
                </th>
                <th align="right" style="background-color:<?php if($color_personalizado=="S") echo $colortv; else echo "#FFFCFC"; ?>;" <?php if($columnas>=10): $columna_ultima = ((int)count($elementos[$ids])-$columnas_titulo)+1; echo "colspan = '".$columna_ultima."' "; endif;  ?>>
                <?php
				$tamano = getimagesize($ruta_logo_club);
					$ancho = $tamano[0];              //Ancho
					$alto = $tamano[1];
				if($ancho>115):
					$tamano_logo = 'width="90" height="40"';
				endif;
				?>

                	<img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $tamano_logo; ?> />
                </th>
            </tr>
            </thead>
            <tr>
                <th colspan="2">

                    <table>
                      <tr>
                        <td valign="top">

                            <?php
                            $contador_predio=0;
                            $predio_ant="";
                            $contador_color=0;
                            $total_mtv=0;
                            $total_casa=0;
                            $es_casa="";
                            foreach ($array_predio as $predio){

                              $array_predio=explode("-",$predio);
                              if($array_predio[0]=="MTV" || $array_predio[0]=="MVT"){
                                $total_mtv++;
                                $es_casa="N";
                              }
                              else{
                                $total_casa++;
                                $es_casa="S";
                              }

                              if($array_predio[0]!=$predio_ant){
                                $predio_ant=$array_predio[0];
                                $color_celda=$array_colores[$contador_color];
                                $contador_color++;
                                if($contador_color>4){
                                  $contador_color=0;
                                }
                              }

                              if($contador_predio==0){
                                echo "<table>
                                  <tr class='azul' >
                                    <td>CASA</td>
                                    <td>OCU</td>
                                    <td>AD.SO</td>
                                    <td>NI.SO</td>
                                    <td>AD.INV</td>
                                    <td>NI.INV</td>
                                  </tr>";
                              }

                              if($es_casa=="S"){
                                $total_casa_adulto+=$array_ocupacion_predio[$predio]["Socio"];
                                $total_casa_nino+=$array_ocupacion_predio[$predio]["SocioNino"];
                                $total_casa_invitado+=$array_ocupacion_predio[$predio]["Invitado"];
                                $total_casa_invitado_nino+=$array_ocupacion_predio[$predio]["InvitadoNino"];
                              }
                              else{
                                $total_mtv_adulto+=$array_ocupacion_predio[$predio]["Socio"];
                                $total_mtv_nino+=$array_ocupacion_predio[$predio]["SocioNino"];
                                $total_mtv_invitado+=$array_ocupacion_predio[$predio]["Invitado"];
                                $total_mtv_invitado_nino+=$array_ocupacion_predio[$predio]["InvitadoNino"];
                              }
                                ?>
                              <tr>
                                <td style="background-color:<?php echo $color_celda; ?>">
                                  <?php echo $predio ?>
                                </td>
                                <td>
                                  <?php
                                  $Tot=(int)$array_ocupacion_predio[$predio]["Socio"]+(int)$array_ocupacion_predio[$predio]["SocioNino"]+(int)$array_ocupacion_predio[$predio]["Invitado"]+(int)$array_ocupacion_predio[$predio]["InvitadoNino"];
                                  echo $Tot;
                                  ?>
                                </td>
                                <td><?php echo $array_ocupacion_predio[$predio]["Socio"]; ?></td>
                                <td><?php echo $array_ocupacion_predio[$predio]["SocioNino"]; ?></td>
                                <td><?php echo $array_ocupacion_predio[$predio]["Invitado"]; ?></td>
                                <td><?php echo $array_ocupacion_predio[$predio]["InvitadoNino"]; ?></td>
                              </tr>
                          <?php
                          $contador_predio++;
                          if($contador_predio==140){
                              $contador_predio=0;
                              echo "</table></td><td valign='top'>";
                          }

                        } ?>
                          </table>

                        </td>
                      </tr>
                    </table>



                    <table>
                      <tr>
                        <td>
                          <table>
                            <tr>
                              <td class="rosado">TOTAL CASAS</td>
                              <td class="rosado">284</td>
                            </tr>
                            <tr>
                              <td class="rosado">EN CONSTRUCCION</td>
                              <td class="rosado">17</td>
                            </tr>
                            <tr>
                              <td class="rosado">OCUPADAS</td>
                              <td class="amarillo"><?php echo $total_casa; ?></td>
                            </tr>
                            <tr>
                              <td class="rosado">ADULTO SOCIO</td>
                              <td class="amarillo"><?php echo $total_casa_adulto ?></td>
                            </tr>
                            <tr>
                              <td class="rosado">NIÑO SOCIO</td>
                              <td class="amarillo"><?php echo $total_casa_nino; ?></td>
                            </tr>
                            <tr>
                              <td class="rosado">ADULTO INVITADO</td>
                              <td class="amarillo"><?php echo $total_casa_invitado; ?></td>
                            </tr>
                            <tr>
                              <td class="rosado">NIÑO INVITADO</td>
                              <td class="amarillo"><?php echo $total_casa_invitado_nino; ?></td>
                            </tr>
                          </table>
                        </td>
                        <td>
                          <table>
                            <tr>
                              <td class="azul">TOTAL MTV</td>
                              <td class="azul">33</td>
                            </tr>
                            <tr>
                              <td class="azul">EN CONSTRUCCION</td>
                              <td class="azul">0</td>
                            </tr>
                            <tr>
                              <td class="azul">OCUPADAS</td>
                              <td class="amarillo"><?php echo $total_mtv; ?></td>
                            </tr>
                            <tr>
                              <td class="azul">ADULTO SOCIO</td>
                              <td class="amarillo"><?php echo $total_mtv_adulto; ?></td>
                            </tr>
                            <tr>
                              <td class="azul">NIÑO SOCIO</td>
                              <td class="amarillo"><?php echo $total_mtv_nino; ?></td>
                            </tr>
                            <tr>
                              <td class="azul">ADULTO INVITADO</td>
                              <td class="amarillo"><?php echo $total_mtv_invitado; ?></td>
                            </tr>
                            <tr>
                              <td class="azul">NIÑO INVITADO</td>
                              <td class="amarillo"><?php echo $total_mtv_invitado_nino; ?></td>
                            </tr>
                          </table>
                        </td>
                        <td>
                          <table>
                            <tr>
                              <td class="verde">TOTAL CASAS Y APTOS</td>
                              <td class="verde">317</td>
                            </tr>
                            <tr>
                              <td class="verde">EN CONSTRUCCION</td>
                              <td class="verde">17</td>
                            </tr>
                            <tr>
                              <td class="verde">OCUPADAS</td>
                              <td class="amarillo"><?php echo $tot=(int)$total_mtv+(int)$total_casa; ?></td>
                            </tr>
                            <tr>
                              <td class="verde">ADULTO SOCIO</td>
                              <td class="amarillo"><?php echo $tot=(int)$total_casa_adulto+(int)$total_mtv_adulto; ?></td>
                            </tr>
                            <tr>
                              <td class="verde">NIÑO SOCIO</td>
                              <td class="amarillo"><?php echo $tot=(int)$total_casa_nino+(int)$total_mtv_nino; ?></td>
                            </tr>
                            <tr>
                              <td class="verde">ADULTO INVITADO</td>
                              <td class="amarillo"><?php echo $tot=(int)$total_casa_invitado+(int)$total_mtv_invitado; ?></td>
                            </tr>
                            <tr>
                              <td class="verde">NIÑO INVITADO</td>
                              <td class="amarillo"><?php echo $tot=(int)$total_casa_invitado_nino+(int)$total_mtv_invitado_nino; ?></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    <table>










              </th>



          </tr>
      </tbody>
    </table>
<?

  $time_end = SIMUtil::getmicrotime();
	$time = $time_end - $time_start;
	$time = number_format($time,3);
	SIMUtil::display_msg("Tiempo de Procesamiento $time Segundos");
?>
  </body>
</html>
