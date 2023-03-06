<?

	$url_search = "";
	if( SIMNet::req("action") == "search" )
	{
		$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
	}//end if

?>

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>REPORTE OCUPACIÓN
		</h4>
	</div>

	<!-- <div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">


				</div>
			</div>
		</div>
	</div> -->
</div>

<style>


.report .header {
    height: 50px;
    padding: 5px;
    width: 100%;
}

/*
Generic Styling, for Desktops/Laptops
*/
.report table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;/**Forzamos a que las filas tenga el mismo ancho**/
    width: 100%; /*El ancho que necesitemos*/
}

.report th {
  background: #fff;
  color: #000;
  font-weight: bold;
  text-align:center;
}
.report td, .report th {
  padding: 3px;
  border: 1px solid #ccc;
  text-align: center;
  font-size:<?php if(SIMUser::get("club")==34) echo "16px"; else echo "12px"; ?>;
  margin: 0;
  word-wrap: break-word;/*Si el contenido supera el tamano, adiciona a una nueve linea**/
  font-weight: bold;
}
.report tr{
  height: 10px;
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


<?

	$date_paraiso= $_GET['Fecha'];

	if(isset($date_paraiso)){
		echo "La fecha seleccionada es ".$date_paraiso."<br>";

    // $sql = "SELECT *  FROM Ocupacion ";
		$sql = "SELECT *  FROM Ocupacion WHERE Fecha = '$date_paraiso' ORDER BY IDOcupacion DESC limit 1 ";
		$result = $dbo->query( $sql );
		// var_dump($result);

		if($row = $dbo->fetchArray($result)) {
      echo "Reporte ocupación <br>";

				$total_casas= $row['Casas'];
				$porcentaje_casas= $row['CasasPorcentaje'];
				$total_casa_construida= $row['CasasConstruidas'];
				$porcentaje_casa_construida= $row['CasasConstruidasPorcentaje'];
				$total_casa_en_construccion= $row['CasasConstruccion'];
				$porcentaje_casa_en_construccion= $row['CasasConstruccionPorcentaje'];
				$total_casas_ocupadas= $row['CasasOcupadas'];
				$porcentaje_casas_ocupadas= $row['CasasOcupadasPorcentaje'];

				$total_multi= $row['Multivillas'];
				$porcentaje_multi= $row['MultivillasPorcentajes'];
				$total_multi_construida= $row['MultiConstruidas'];
				$porcentaje_multi_construida= $row['MultiConstruidasPOrcentajes'];
				$total_multi_en_construccion= $row['MultiConstruccion'];
				$porcentaje_multi_en_construccion= $row['MultiConstruccionPorcentaje'];
				$total_multi_ocupadas= $row['MultiOcupadas'];
				$porcentaje_multi_ocupadas= $row['MultiOcupadasPorcentaje'];


				$total_viviendas= $row['TotalViviendas'];
				$porcentaje_viviendas= $row['TotalViviendasPorcentaje'];
				$total_viviendas_construidas= $row['TotalViviendaConstruida'];
				$porcentaje_viviendas_construidas= $row['TotalViviendaConstruidaPorcentaje'];
				$total_viviendas_en_construccion= $row['TotalViviendaConstruccion'];
				$porcentaje_viviendas_en_construccion= $row['TotalViviendaConstruccionPorcentaje'];
				$total_vivienda_ocupadas= $row['TotalViviendaOcupada'];
				$porcentaje_vivienda_ocupadas= $row['TotalViviendaOcupadaPorcentaje'];

				$total_viviendas_ocupadas= $total_casas_ocupadas + $total_multi_ocupadas + $total_vivienda_ocupadas;
				$porcentaje_viviendas_ocupadas= $row['TotalViviendaPorcentaje'];

				$total_casa_adultos_socios = $row['CasaAdultoSocio'];
				$total_casa_ninos_socios = $row['CasaNinoSocio'];
				$total_casa_adultos_invitado = $row['CasaAdultoInvitado'];
				$total_casa_nino_invitado = $row['CasaNinoInvitado'];
				$total_poblacion_casas = $total_casa_adultos_socios + $total_casa_ninos_socios + $total_casa_adultos_invitado + $total_casa_nino_invitado;

				$total_multi_adultos_socios = $row['MultiAdultoSocio'];
				$total_multi_nino_socios = $row['MultiNinoSocio'];
				$total_multi_adultos_invitado = $row['MultiAdultoInvitado'];
				$total_multi_nino_invitado = $row['MultiNinoInvitado'];
				$total_poblacion_multi = $total_multi_adultos_socios + $total_multi_nino_socios + $total_multi_adultos_invitado + $total_multi_nino_invitado;

				$total_adulto_socio = $row['TotalAdultoSocio'];
				$total_nino_socio = $row['TotalNinoSocio'];
				$total_adulto_invitado = $row['TotalAdultoInvitado'];
				$total_nino_invitado = $row['TotalNinoInvitado'];
				$total_personas_casa_multi = $total_adulto_socio + $total_nino_socio + $total_adulto_invitado + $total_nino_invitado;

				$total_personas_empleados = $row['TotalPersonasEmpleados'];
				$total_personas_socios_invitados= $row['TotalPersonasSocioseInvitados'];
				$gran_total_personas = $row['GranTotalPersonas'];

				// var_dump($row);
				// echo "<br>";
				?>
<div class="row">
<h2>Reporte </h2>
<table class="fixed report">

            <tr>
                <th colspan="2">

                <table>
                  <tr>
                      <td valign="top">

                        <table>
                            <tr class="azul">
                              <td>CASAS</td>
                              <td><?php echo $total_casas; ?></td>
                              <td><?php echo $porcentaje_casas; ?></td>
                            </tr>
                            <tr class="rosado">
                              <td >Construidas</td>
                              <td><?php echo $total_casa_construida; ?></td>
                              <td><?php echo $porcentaje_casa_construida; ?></td>
                            </tr>
                            <tr class="amarillo">
                              <td>En construccion</td>
                              <td><?php echo $total_casa_en_construccion; ?></td>
                              <td><?php echo $porcentaje_casa_en_construccion; ?>%</td>
                            </tr>
                            <tr class="verde">
                              <td>Ocupadas</td>
                              <td><?php echo $total_casas_ocupadas ?></td>
                              <td><?php echo $porcentaje_casas_ocupadas ?>%</td>
                            </tr>
                        </table>
                        <br>
                        <table>
                            <tr class="azul">
                              <td>MULTIVILLAS</td>
                              <td><?php echo $total_multi; ?></td>
                              <td><?php echo $porcentaje_multi; ?>%</td>
                            </tr>
                            <tr class="rosado">
                              <td >Construidas</td>
                              <td><?php echo $total_multi_construida; ?></td>
                              <td><?php echo $porcentaje_multi_construida; ?>%</td>
                            </tr>
                            <tr class="amarillo">
                              <td>En construccion</td>
                              <td><?php echo $total_multi_en_construccion; ?></td>
                              <td><?php echo $porcentaje_multi_en_construccion; ?>%</td>
                            </tr>
                            <tr class="verde">
                              <td>Ocupadas</td>
                              <td><?php echo $total_multi_ocupadas ?></td>
                              <td><?php echo $porcentaje_multi_ocupadas ?>%</td>
                            </tr>
                        </table>
                        <br>
                        <table>
                            <tr class="azul">
                              <td>TOTAL VIVIENDAS</td>
                              <td><?php echo $total_viviendas; ?></td>
                              <td><?php echo $porcentaje_viviendas; ?>%</td>
                            </tr>
                            <tr class="rosado">
                              <td >Construidas</td>
                              <td><?php echo $total_viviendas_construidas; ?></td>
                              <td><?php echo $porcentaje_viviendas_construidas; ?>%</td>
                            </tr>
                            <tr class="amarillo">
                              <td>En construccion</td>
                              <td><?php echo $total_viviendas_en_construccion; ?></td>
                              <td><?php echo $porcentaje_viviendas_en_construccion; ?>%</td>
                            </tr>
                            <tr class="verde">
                              <td>Ocupadas</td>
                              <td><?php echo $total_vivienda_ocupadas ?></td>
                              <td><?php echo $porcentaje_vivienda_ocupadas ?>%</td>
                            </tr>
                        </table>

                        <br>

                        <table>
                            <tr >
                                <td class="azul">Total Viviendas ocupadas</td>
                                <td class="verde"><?php echo $total_viviendas_ocupadas; ?></td>
                            </tr>
                            <tr class="rosado">
                                <td>Porcentaje</td>
                                <td><?php echo $porcentaje_viviendas_ocupadas; ?>%</td>
                            </tr>
                        </table>

                          <br>


                      </td>
                      <td valign="top">
                        <table>
                            <tr class="azul">
                                <td colspan="2">POBLACION CASAS</td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos socios</td>
                                <td><?php echo $total_casa_adultos_socios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños socios</td>
                                <td><?php echo $total_casa_ninos_socios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos invitados</td>
                                <td><?php echo $total_casa_adultos_invitado; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños invitados</td>
                                <td><?php echo $total_casa_nino_invitado; ?></td>
                            </tr>
                            <tr class="verde">
                                <td>Total personas</td>
                                <td><?php echo $total_poblacion_casas; ?></td>
                            </tr>

                        </table>
                        <br>
                        <table>
                            <tr class="azul">
                                <td colspan="2">POBLACION MULTIVILLAS</td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos socios</td>
                                <td><?php echo $total_multi_adultos_socios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños socios</td>
                                <td><?php echo $total_multi_nino_socios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos invitados</td>
                                <td><?php echo $total_multi_adultos_invitado; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños invitados</td>
                                <td><?php echo $total_multi_nino_invitado; ?></td>
                            </tr>
                            <tr class="verde">
                                <td>Total personas</td>
                                <td><?php echo $total_poblacion_multi; ?></td>
                            </tr>

                        </table>

                        <br>

                        <table>
                            <tr class="azul">
                                <td colspan="2">TOTAL POBLACION </td>
                            </tr>

                            <tr class="blanco">
                                <td>Adultos socios</td>
                                <td><?php echo $total_adulto_socio; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños socios</td>
                                <td><?php echo $total_nino_socio; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos invitados</td>
                                <td><?php echo $total_adulto_invitado; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños invitados</td>
                                <td><?php echo $total_nino_invitado; ?></td>
                            </tr>
                            <tr class="verde">
                                <td>Total personas</td>
                                <td><?php echo $total_personas_casa_multi; ?></td>
                            </tr>

                        </table>
                      </td>
                      <td valign="top">

												<?php if(empty($row["Personas"])){ ?>

													<table>
	                              <tr class="azul">
	                                  <td colspan="2">
	                                    <?php echo $row["DetallePersonas"]; ?><br>
	                                  </td>
	                              </tr>
	                        </table>

												<?php }else{ ?>
													<table>
															<?php
															$array_adentro_tipo=json_decode($row["Personas"]);
															foreach ($array_adentro_tipo as $key => $value) {
																if(!empty($key)){ ?>
																<tr class="azul">
																		<td colspan="2">
																			<?php echo $key; ?><br>
																			<table>
																				<?php foreach ($value as $key_c => $value_c) {
																						$array_total_clasif=explode("|",$value_c);
																						if($array_total_clasif[0]!="InvitadoAcceso" && $array_total_clasif[0]!="Socio"){
																					?>
																						<tr class="blanco">
																							<td style="color:#000"><?php echo $array_total_clasif[0];  ?></td>
																							<td style="color:#000"><?php echo $array_total_clasif[1]; ?></td>
																						</tr>
																						<?php
																						}
																					} ?>
																			</table>


																		</td>
																</tr>
															<?php
															}
															} ?>
													</table>

												<?php } ?>

                        <br>


												<br>
                        <table>
                            <tr>
                                <td class="azul">Total personas empleados</td>
                                <td class="azul"><?php echo $total_personas_empleados; ?></td>
                            </tr>

                            <tr>
                                <td class="azul">Total personas socios e invitados</td>
                                <td class="azul"><?php echo $total_personas_socios_invitados; ?></td>
                            </tr>
                            <tr>
                                <td class="azul">Total personas</td>
                                <td class="azul"><?php echo $gran_total_personas; ?></td>
                            </tr>

                        </table>
                        <br>

                      </td>
                  </tr>

                </table>



              </th>



          </tr>
      </tbody>
    </table>



</div>
				<?

		}else {
			echo "No hay reportes para la fecha seleccionada <br>";
		}

	}else {
		echo "Seleccione una fecha <br>";
	}

	include( "cmp/footer_grid.php" );
?>
