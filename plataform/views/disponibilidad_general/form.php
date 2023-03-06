
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>DISPONIBILIDAD  - <?=$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $datos_servicio[$ids]["IDServicioMaestro"] . "'") ?>
		</h4>


	</div>

			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


			      <div id="ServicioDisponibilidad">

          <form name="frmdisponibilidad" id="frmdisponibilidad" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "insert";

                  if( $_GET[IDDisponibilidad] )
                  {
                          $EditDisponibilidad =$dbo->fetchAll("Disponibilidad"," IDDisponibilidad = '".$_GET[IDDisponibilidad]."' ","array");
                          $action = "update";
                          ?>
                          <input type="hidden" name="IDDisponibilidad" id="IDDisponibilidad" value="<?php echo $EditDisponibilidad[IDDisponibilidad]?>" />
                          <?php
                  }
                  ?>
                  <table id="simple-table" class="table table-striped table-bordered table-hover">
                  <tr>
                    <td width="37%">Nombre</td>
                    <td colspan="2"><input type="text" name="Nombre" id="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $EditDisponibilidad["Nombre"] ?>">

                    </td>
                  </tr>
                  <tr>
                    <td colspan="3">Dia a los que aplica
                    <?php
					$dias_aplica = $dbo->getFields( "ServicioDisponibilidad" , "IDDia" , "IDDisponibilidad = '" . $EditDisponibilidad["IDDisponibilidad"] . "'")?>
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
                    <td colspan="3">
                    <?php
					$array_dias=explode("|",$dias_aplica);
					array_pop($array_dias);
					foreach($Dia_array as $id_dia => $dia):  ?>
                    	<input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if(in_array($id_dia,$array_dias) && $dia!="") echo "checked"; ?>><?php echo $dia; ?>
                    <?php endforeach; ?>


                    </td>
                    </tr>
                  <tr>
                    <td colspan="3"><table width="100%" border="0">
                      <tbody>
                        <tr>
                          <td width="44%"><span class="columnafija">Hora Inicial </span></td>
                          <td width="56%"><span class="columnafija">Hora Final </span></td>
                          <?php
                          //Solo para golf pregunto tee
		 					if($datos_servicio[$ids]["IDServicioMaestro"]==15 || $datos_servicio[$ids]["IDServicioMaestro"]==27 || $datos_servicio[$ids]["IDServicioMaestro"]==28 || $datos_servicio[$ids]["IDServicioMaestro"]==30): ?>
                          <td width="56%"><span class="columnafija">Hora Par </span></td>
                          <?php endif; ?>
                        </tr>
                        <?php

						if ((int)$EditDisponibilidad["IDDisponibilidad"]>0):
							$contador_hora_guardada=1;
							$sql_servicio_disponibilidad = $dbo->query("Select * From ServicioDisponibilidad Where IDDisponibilidad = '".$EditDisponibilidad["IDDisponibilidad"]."' order By HoraDesde Asc");

							while($r_disponibilidad = $dbo->fetchArray($sql_servicio_disponibilidad)):
								$array_intervalo[$contador_hora_guardada] = $r_disponibilidad["Intervalo"];
								$array_desde[$contador_hora_guardada] = $r_disponibilidad["HoraDesde"];
								$array_hasta[$contador_hora_guardada] = $r_disponibilidad["HoraHasta"];
								$array_par[$contador_hora_guardada] = $r_disponibilidad["HoraPar"];
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
                          <?php
                          //Solo para golf pregunto tee
		 					if($datos_servicio[$ids]["IDServicioMaestro"]==15 || $datos_servicio[$ids]["IDServicioMaestro"]==27 || $datos_servicio[$ids]["IDServicioMaestro"]==28 || $datos_servicio[$ids]["IDServicioMaestro"]==30): ?>
                          <td width="56%">
                              <input type="time" name="HoraPar<?=$contador_horas?>" id="HoraPar<?=$contador_horas?>" class="input <?php if($contador_horas==1) echo "mandatory"; ?>" title="Hora Par" value="<?php echo $array_par[$contador_horas]?>">
                          </td>
                          <?php endif; ?>
                        </tr>
                        <?php endfor; ?>



                      </tbody>
                    </table></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="columnafija">Aplica a los elementos</td>
                    </tr>
                  <tr>
                    <td colspan="3" class="columnafija">
                    	<table class="table table-striped table-bordered table-hover">
                        	<tr>

									<?php
                                    $elemento_aplica = $dbo->getFields( "ServicioDisponibilidad" , "IDServicioElemento" , "IDDisponibilidad = '" . $EditDisponibilidad["IDDisponibilidad"] . "'");
                                    $array_elementos_guardados=explode("|",$elemento_aplica);
                                    $r_elemento_servicio =& $dbo->all( "ServicioElemento" , "IDServicio = '" .$_GET["ids"]  ."' Order by Orden");
									  $contador_elementos = 0;
                                      while( $r = $dbo->object( $r_elemento_servicio ) ): ?>
                                      <td>
                                      <input type="checkbox" name="IDServicioElemento[]" id="IDServicioElemento" value="<?php echo $r->IDServicioElemento; ?>" <?php if(in_array( $r->IDServicioElemento,$array_elementos_guardados)) echo "checked"; ?>><?php echo $r->Nombre; ?>
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

                    <?php
					//Solo para golf pregunto tee
					if($datos_servicio[$ids]["IDServicioMaestro"]==15 || $datos_servicio[$ids]["IDServicioMaestro"]==27 || $datos_servicio[$ids]["IDServicioMaestro"]==28 || $datos_servicio[$ids]["IDServicioMaestro"]==30):
						$tee1_aplica = $dbo->getFields( "ServicioDisponibilidad" , "Tee1" , "IDDisponibilidad = '" . $EditDisponibilidad["IDDisponibilidad"] . "'");
						$tee10_aplica = $dbo->getFields( "ServicioDisponibilidad" , "Tee10" , "IDDisponibilidad = '" . $EditDisponibilidad["IDDisponibilidad"] . "'");
					?>
                    <tr>
                    <td colspan="3" class="columnafija">Aplica a los Tee:&nbsp;&nbsp;
                    	<input type="checkbox" name="Tee1" id="Tee1" class="" value="S" <?php if($tee1_aplica=="S") echo "checked"; ?> > Tee1
                        <input type="checkbox" name="Tee10" id="Tee10" class="" value="S" <?php if($tee10_aplica=="S") echo "checked"; ?>> Tee 10
                    </td>
                    </tr>
                    <?php endif; ?>
                  <tr>
                    <td class="columnafija">Tiempo de anticipación al primer turno para reservar</td>
                    <td width="34%">Tiempo Cancelacion </td>
                    <td width="29%">Intervalo de Turnos (minutos)</td>
                  </tr>
                  <tr>
                    <td class="columnafija">
                    <input type="number" name="Anticipacion" id="Anticipacion" class="col-xs-4 mandatory" title="Anticipacion" value="<?php echo $EditDisponibilidad["Anticipacion"] ?>">
                    <select name="MedicionTiempoAnticipacion" id="MedicionTiempoAnticipacion" class="mandatory" title="Opcion Tiempo Anticipacion">
                      <option value=""></option>
                      <option value="Minutos" <?php if($EditDisponibilidad["MedicionTiempoAnticipacion"]=="Minutos") echo "selected";  ?>>Minutos</option>
                      <option value="Horas" <?php if($EditDisponibilidad["MedicionTiempoAnticipacion"]=="Horas") echo "selected";  ?>>Horas</option>
                      <option value="Dias" <?php if($EditDisponibilidad["MedicionTiempoAnticipacion"]=="Dias") echo "selected";  ?>>Dias</option>
                    </select>
                    </td>
                    <td>
                    <input type="number" name="TiempoCancelacion" id="TiempoCancelacion" class="col-xs-4 mandatory" title="Tiempo Cancelacion" value="<?php echo $EditDisponibilidad["TiempoCancelacion"] ?>">
                    <select name="MedicionTiempo" id="MedicionTiempo" class="mandatory" title="Opcion Tiempo Cancelacion">
                      <option value=""></option>
                      <option value="Minutos" <?php if($EditDisponibilidad["MedicionTiempo"]=="Minutos") echo "selected";  ?>>Minutos</option>
                      <option value="Horas" <?php if($EditDisponibilidad["MedicionTiempo"]=="Horas") echo "selected";  ?>>Horas</option>
                      <option value="Dias" <?php if($EditDisponibilidad["MedicionTiempo"]=="Dias") echo "selected";  ?>>Dias</option>
                    </select></td>
                    <td>
                    <input type="number" name="Intervalo" id="Intervalo" class="col-xs-4 mandatory" title="Intervalo" value="<?php echo $EditDisponibilidad["Intervalo"] ?>">
                    </td>
                  </tr>
                  <tr>
                    <td>Numero maximo invitados Club</td>
                    <td><label for="form-field-1">Numero maximo invitados Externo</label></td>
                    <td><span class="columnafija">Permitir reservar antes de (si es 10 min,  el turno de las 3 se puede reservar max hasta las 2:50)</span></td>
                  </tr>
                  <tr>
                    <td><span class="columnafija">
                      <input type="number" name="NumeroInvitadoClub" id="NumeroInvitadoClub" class="col-xs-4 mandatory" title="Numero Invitado Club" value="<?php echo $EditDisponibilidad["NumeroInvitadoClub"] ?>">
                    </span></td>
                    <td><span class="columnafija">
                      <input type="number" name="NumeroInvitadoExterno" id="NumeroInvitadoExterno" class="col-xs-4 mandatory" title="Numero Invitado Externo" value="<?php echo $EditDisponibilidad["NumeroInvitadoExterno"] ?>">
                    </span></td>
                    <td>
                    <input type="number" name="AnticipacionTurno" id="AnticipacionTurno" class="col-xs-4 mandatory" title="Anticipacion Turno" value="<?php echo $EditDisponibilidad["AnticipacionTurno"] ?>">
                    <select name="MedicionTiempoAnticipacionTurno" id="MedicionTiempoAnticipacionTurno" class="mandatory" title="Opcion Tiempo Anticipacion Turno">
                      <option value=""></option>
                      <option value="Minutos" <?php if($EditDisponibilidad["MedicionTiempoAnticipacionTurno"]=="Minutos") echo "selected";  ?>>Minutos</option>
                      <option value="Horas" <?php if($EditDisponibilidad["MedicionTiempoAnticipacionTurno"]=="Horas") echo "selected";  ?>>Horas</option>
                      <option value="Dias" <?php if($EditDisponibilidad["MedicionTiempoAnticipacionTurno"]=="Dias") echo "selected";  ?>>Dias</option>
                    </select></td>
                  </tr>
                  <tr>
                    <td>Numero minimo de invitados Club</td>
                    <td>Numero minimo de invitados Externo</td>
                  </tr>
                  <tr>
                    <td><span class="columnafija">
                      <input type="number" name="NumeroMinimoInvitadoClub" id="NumeroMinimoInvitadoClub" class="col-xs-4 mandatory" title="Numero Invitado Club" value="<?php echo $EditDisponibilidad["NumeroMinimoInvitadoClub"] ?>">
                    </span></td>
                    <td><span class="columnafija">
                      <input type="number" name="NumeroMinimoInvitadoExterno" id="NumeroMinimoInvitadoExterno" class="col-xs-4 mandatory" title="Numero Invitado Externo" value="<?php echo $EditDisponibilidad["NumeroMinimoInvitadoExterno"] ?>">
                    </span></td>                    
                  </tr>
                  <tr>
                    <td>Pemitir Repetir reservas</td>
                    <td>Minimo de personas para poder hacer la reserva (incluyendo el socio)</td>
                    <td>Maximo de invitados permitidos (sin incluir al socio)</td>
                  </tr>
                  <tr>
                    <td>

                    <span class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["PermiteRepeticion"] , "PermiteRepeticion" , "title=\"PermiteRepeticion\"" )?> <span class="columnafija">
                     <div id="div_repeticion" <?php if($EditDisponibilidad["PermiteRepeticion"]=="N" || empty($EditDisponibilidad["PermiteRepeticion"])) echo "style='display:none'";  ?> >
                     <!--
                      <select name="MedicionRepeticion" id="MedicionRepeticion" class="" title="Opcion Medicion Repeticion">
                        <option value=""></option>
                        <option value="Diario" <?php if($EditDisponibilidad["MedicionRepeticion"]=="Diario") echo "selected";  ?>>Diario</option>
                        <option value="Semanal" <?php if($EditDisponibilidad["MedicionRepeticion"]=="Semanal") echo "selected";  ?>>Semanal</option>
                        <option value="Mensual" <?php if($EditDisponibilidad["MedicionRepeticion"]=="Mensual") echo "selected";  ?>>Mensual</option>
                      </select>
                      -->
                      Hasta:<br>
                      <!-- <input type="text" id="FechaFinRepeticion" name="FechaFinRepeticion" placeholder="Fecha Fin " class="col-xs-12 calendar" title="Fecha Fin Repeticion" value="<?php echo $EditDisponibilidad["FechaFinRepeticion"] ?>" > -->
                      <input type="number" name="NumeroRepeticion" id="NumeroRepeticion" class="col-xs-4" title="Numero Repeticion" value="<?php echo $EditDisponibilidad["NumeroRepeticion"] ?>">
                      <select name="MedicionRepeticion" id="MedicionRepeticion" class="" title="Opcion Medicion Repeticion">
                        <option value=""></option>
                        <option value="Dia" <?php if($EditDisponibilidad["MedicionRepeticion"]=="Dia") echo "selected";  ?>>Dias</option>
                        <option value="Semana" <?php if($EditDisponibilidad["MedicionRepeticion"]=="Semana") echo "selected";  ?>>Semanas</option>
                        <option value="Mes" <?php if($EditDisponibilidad["MedicionRepeticion"]=="Mes") echo "selected";  ?>>Meses</option>
                      </select>
                  	</div>

                    </span></span></td>
                    <td><span class="columnafija">
                      <input type="number" name="MinimoInvitados" id="MinimoInvitados" class="col-xs-4 mandatory" title="Minimo Invitados" value="<?php echo $EditDisponibilidad["MinimoInvitados"] ?>">
                    </span></td>
                    <td><span class="columnafija">
                      <input type="number" name="MaximoInvitados" id="MaximoInvitados" class="col-xs-4 mandatory" title="Maximo Invitados" value="<?php echo $EditDisponibilidad["MaximoInvitados"] ?>">
                    </span></td>
                  </tr>
                  <tr>
                    <td>Numero de reservas permitido por dia por socio</td>
                    <td>Si solo se permite una reserva por dia: Perrmitir reservar despues de cumplir turno</td>
                    <td>Permite Reservas Seguidas a la misma accion?</td>
                    </tr>
                  <tr>
                    <td>
                    <span class="columnafija">
                      <input type="number" name="MaximoReservaDia" id="MaximoReservaDia" class="col-xs-4 mandatory" title="Reservas Por Dia por Socio" value="<?php echo $EditDisponibilidad["MaximoReservaDia"] ?>">
                    </span>
                    </td>
                    <td><span class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["PermiteReservaCumplirTurno"] , "PermiteReservaCumplirTurno" , "title=\"Permite Reservar Cumplir Turno\"" )?>
                    <span class="columnafija">
                    <div id="div_tiempo_despues" <?php if($EditDisponibilidad["PermiteReservaCumplirTurno"]=="N" || empty($EditDisponibilidad["PermiteReservaCumplirTurno"])) echo "style='display:none'";  ?> >
                    Cuanto tiempo despues de cumplir primer turno permite reservar nuevamente?:<br>

                      <input type="number" name="TiempoDespues" id="TiempoDespues" class="col-xs-4" title="Reserva Despues de." value="<?php echo $EditDisponibilidad["TiempoDespues"] ?>">
                    <select name="MedicionTiempoDespues" id="MedicionTiempoDespues" class="form-control" title="MedicionTiempoDespues">
                      <option value=""></option>
                      <option value="Minutos" <?php if($EditDisponibilidad["MedicionTiempoDespues"]=="Minutos") echo "selected";  ?>>Minutos</option>
                      <option value="Horas" <?php if($EditDisponibilidad["MedicionTiempoDespues"]=="Horas") echo "selected";  ?>>Horas</option>
                      <option value="Dias" <?php if($EditDisponibilidad["MedicionTiempoDespues"]=="Dias") echo "selected";  ?>>Dias</option>
                    </select>
                    </div>
                    </span></span>

                    </td>
                    <td><span class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["PermiteReservaSeguida"] , "PermiteReservaSeguida" , "title=\"PermiteReservaSeguida\"" )?></span></td>
                    </tr>
                  <tr>
                    <td>Permite Reservas Seguidas al nucleo familiar o beneficiario</td>
                    <td class="columnafija">Pemitir  al usuario eliminar/modificar reserva cuando sea realizada por el starter?</td>
                    <td>Cupos por turno (cuantos personas pueden reservar en la misma hora, Es mas de 1 cuando el servicio es para clases de Gimnasia, Zumba, etc)</td>
                    </tr>
                  <tr>
                    <td><span class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["PermiteReservaSeguidaNucleo"] , "PermiteReservaSeguidaNucleo" , "title=\"PermiteReservaSeguidaNucleo\"" )?></span></td>
                    <td class="columnafija"><span class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["PermiteEliminarCreadaStarter"] , "PermiteEliminarCreadaStarter" , "title=\"Permite eliminar/modificar reserva  cuando es creada por Starter\"" )?></span></td>
                    <td><span class="columnafija">
                      <input type="number" name="Cupos" id="Cupos" class="col-xs-4 mandatory" title="Cupos" value="<?php echo $EditDisponibilidad["Cupos"] ?>" />
                    </span></td>
                    </tr>
                  <tr>
                    <td>Solo reservas por Geolocalizacion</td>
                    <td class="columnafija">Permitir reservar despu&eacute;s de (si es 10 min el turno de las 3 se puede reservar maximo hasta las 3:10)</td>
                    <td>Este horario solo aplica para el administrador?(no para el app)</td>
                    </tr>
                  <tr>
                    <td><span class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["Georeferenciacion"] , "Georeferenciacion" , "title=\"Georeferenciacion\"" )?></span></td>
                    <td class="columnafija"><span class="col-sm-8">
                      <input type="number" name="MinutoPosteriorTurno" id="MinutoPosteriorTurno" class="col-xs-4 mandatory" title="Minuto Posterior Turno" value="<?php echo $EditDisponibilidad["MinutoPosteriorTurno"] ?>">
minutos</span></td>
                    <td><span class="col-sm-8">
                      <input type="radio" name="SoloAdmin" id="SoloAdmin" value="S" class='input' <?php if($EditDisponibilidad["SoloAdmin"]=="S") echo "checked"; ?>>
                      S
  <input type="radio" name="SoloAdmin" id="SoloAdmin" value="N" class='input' <?php if($EditDisponibilidad["SoloAdmin"]=="N") echo "checked"; ?>>
                      N
  <?php //echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["Activo"] , "Activo" , "title=\"Activo\"" )?>
                    </span></td>
                    </tr>

										<tr>
											<td>Activo</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											</tr>

										<tr>
	                    <td><span class="col-sm-8">
	                      <input type="radio" name="Activo" id="Activo" value="S" class='input' <?php if($EditDisponibilidad["Activo"]=="S") echo "checked"; ?>>
	                      S
	  <input type="radio" name="Activo" id="Activo" value="N" class='input' <?php if($EditDisponibilidad["Activo"]=="N") echo "checked"; ?>>
	                      N
	  <?php //echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["Activo"] , "Activo" , "title=\"Activo\"" )?>
	                    </span>
										</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
	                    </tr>

                  <tr>
                    <td colspan="3" class="columnafija" align="center"><input type="submit" class="submit" value="Crear"></td>
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
