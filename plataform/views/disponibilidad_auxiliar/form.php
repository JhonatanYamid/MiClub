
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>DISPONIBILIDAD AUXILIARES  - <?=$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $datos_servicio[$ids]["IDServicioMaestro"] . "'") ?>
		</h4>


	</div>

			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


			      <div id="AuxiliaroDisponibilidad">

          <form name="frmdisponibilidadauxiliar" id="frmdisponibilidadauxiliar" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "insert";
	              if( $_GET[IDAuxiliarDisponibilidad] )
                  {
	                          $EditDisponibilidad =$dbo->fetchAll("AuxiliarDisponibilidad"," IDAuxiliarDisponibilidad = '".$_GET[IDAuxiliarDisponibilidad]."' ","array");
                          $action = "update";
                          ?>
                          <input type="hidden" name="IDAuxiliarDisponibilidad" id="IDAuxiliarDisponibilidad" value="<?php echo $EditDisponibilidad[IDAuxiliarDisponibilidad]?>" />
                          <?php
                  }
                  ?>
                  <table id="simple-table" class="table table-striped table-bordered table-hover">
                  <tr>
                    <td width="37%">Nombre</td>
                    <td width="63%"><input type="text" name="Nombre" id="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $EditDisponibilidad["Nombre"] ?>">

                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">Dia a los que aplica
                    <?php
					$dias_aplica = $dbo->getFields( "AuxiliarDisponibilidadDetalle" , "IDDia" , "IDAuxiliarDisponibilidad = '" . $EditDisponibilidad["IDAuxiliarDisponibilidad"] . "'")?>
                    <!--
					<select name="IDDia" id="IDDia" class="popup">
                    	<option value="">[Seleccione]</option>
                        <?php foreach($Dia_array as $id_dia => $dia):  ?>
                        	<option value="<?php echo $id_dia; ?>" <?php if($id_dia==$dias_aplica) echo "selected"; ?>><?php echo $dia; ?></option>
                        <?php endforeach; ?>
                    </select>
                    -->					</td>
                    </tr>
                  <tr>
                    <td colspan="2">
                    <?php
					$array_dias=explode("|",$dias_aplica);
					array_pop($array_dias);
					foreach($Dia_array as $id_dia => $dia):  ?>
                    	<input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if(in_array($id_dia,$array_dias) && $dia!="") echo "checked"; ?>><?php echo $dia; ?>
                    <?php endforeach; ?>


                    </td>
                    </tr>
                  <tr>
                    <td colspan="2"><table width="100%" border="0">
                      <tbody>
                        <tr>
                          <td width="44%"><span class="columnafija">Hora Inicial </span></td>
                          <td width="56%"><span class="columnafija">Hora Final </span></td>
                        </tr>
                        <?php

						if ((int)$EditDisponibilidad["IDAuxiliarDisponibilidad"]>0):
							$contador_hora_guardada=1;
							$sql_servicio_disponibilidad = $dbo->query("Select * From AuxiliarDisponibilidadDetalle Where IDAuxiliarDisponibilidad = '".$EditDisponibilidad["IDAuxiliarDisponibilidad"]."'");

							while($r_disponibilidad = $dbo->fetchArray($sql_servicio_disponibilidad)):
								$array_intervalo[$contador_hora_guardada] = $r_disponibilidad["Intervalo"];
								$array_desde[$contador_hora_guardada] = $r_disponibilidad["HoraDesde"];
								$array_hasta[$contador_hora_guardada] = $r_disponibilidad["HoraHasta"];
								$contador_hora_guardada++;
							endwhile;

							$total_horarios = (int)$dbo->rows($sql_servicio_disponibilidad);
						endif;

						if ((int)$total_horarios==0)
							$maximo_horas = 3;
						else
							$maximo_horas = (int)$total_horarios + 3;



						for ($contador_horas=1; $contador_horas<=$maximo_horas; $contador_horas++): ?>
                        <tr>

                          <td><input type="time" name="HoraDesde<?=$contador_horas?>" id="HoraDesde<?=$contador_horas?>" class="input <?php if($contador_horas==1) echo "mandatory"; ?>" title="Hora desde" value="<?php echo $array_desde[$contador_horas]?>"></td>
                          <td><input type="time" name="HoraHasta<?=$contador_horas?>" id="HoraHasta<?=$contador_horas?>" class="input <?php if($contador_horas==1) echo "mandatory"; ?>" title="Hora hasta" value="<?php echo $array_hasta[$contador_horas]?>"></td>
                        </tr>
                        <?php endfor; ?>



                      </tbody>
                    </table></td>
                  </tr>
                  <tr>
                    <td colspan="2" class="columnafija">Aplica a los auxiliares / boleadores</td>
                    </tr>
                  <tr>
                    <td colspan="2" class="columnafija">


											<table class="table table-striped table-bordered table-hover">
                        	<tr>

									<?php
                                    $auxiliar_aplica = $dbo->getFields( "AuxiliarDisponibilidadDetalle" , "IDAuxiliar" , "IDAuxiliarDisponibilidad = '" . $EditDisponibilidad["IDAuxiliarDisponibilidad"] . "'");
                                    $array_auxiliar_guardados=explode("|",$auxiliar_aplica);
																		$r_auxiliar_servicio =& $dbo->all( "Auxiliar" , "IDServicio = '" .$_GET["ids"]  ."'");
																		$contador_elementos = 0;
																		  while( $r = $dbo->object( $r_auxiliar_servicio ) ): ?>
                                      <td>
                                      <input type="checkbox" name="IDAuxiliar[]" id="IDAuxiliar" value="<?php echo $r->IDAuxiliar; ?>" <?php if(in_array( $r->IDAuxiliar,$array_auxiliar_guardados)) echo "checked"; ?>><?php echo $r->Nombre; ?>
                                       </td>
                                    <?php
									$contador_elementos++;
									if($contador_elementos==4):
										echo "</tr><tr>";
										$contador_elementos=0;

									endif;
									endwhile; ?>

                             </tr>
                        </table>





                    </td>
                    </tr>
										<tr>
	                    <td class="columnafija">Orden</td>
	                    <td><span class="col-sm-8">
												<input type="number" name="Orden" id="Orden" class="input form-control" title="Orden" value="<?php echo $EditDisponibilidad["Orden"]?>">
											</td>
	                  </tr>
                  <tr>
                    <td class="columnafija">Activo</td>
                    <td><span class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["Activo"] , "Activo" , "title=\"Activo\"" )?></span></td>
                  </tr>
                  <tr>
                    <td colspan="2" class="columnafija" align="center"><input type="submit" class="submit" value="Crear"></td>
                    </tr>
                  </table>

                  <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $_GET["ids"]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                  <input type="hidden" name="contador_horas" id="contador_horas" value="<?php echo $contador_horas?>" />

              </form>
              <br />
			      </div>

				</div>
			</div>




		</div><!-- /.widget-main -->

<?
	include( "cmp/footer_scripts.php" );
?>
