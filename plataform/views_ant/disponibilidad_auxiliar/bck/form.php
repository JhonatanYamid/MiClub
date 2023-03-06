
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
                        </tr>
                        <?php 
						
						if ((int)$EditDisponibilidad["IDDisponibilidad"]>0):
							$contador_hora_guardada=1;						
							$sql_servicio_disponibilidad = $dbo->query("Select * From ServicioDisponibilidad Where IDDisponibilidad = '".$EditDisponibilidad["IDDisponibilidad"]."'");
							
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
                    <td colspan="3" class="columnafija">Aplica a los elementos</td>
                    </tr>
                  <tr>
                    <td colspan="3" class="columnafija">
                    <?php  $elemento_aplica = $dbo->getFields( "ServicioDisponibilidad" , "IDServicioElemento" , "IDDisponibilidad = '" . $EditDisponibilidad["IDDisponibilidad"] . "'");
					$array_elementos_guardados=explode("|",$elemento_aplica);   					
					$r_elemento_servicio =& $dbo->all( "ServicioElemento" , "IDServicio = '" .$_GET["ids"]  ."'");
					  while( $r = $dbo->object( $r_elemento_servicio ) ): ?>
				      <input type="checkbox" name="IDServicioElemento[]" id="IDServicioElemento" value="<?php echo $r->IDServicioElemento; ?>" <?php if(in_array( $r->IDServicioElemento,$array_elementos_guardados)) echo "checked"; ?>><?php echo $r->Nombre; ?>
			     	<?php endwhile; ?></td>
                    </tr>
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
                    <td>Numero invitados Club</td>
                    <td><label for="form-field-1">Numero invitados Externo</label></td>
                    <td><span class="columnafija">Permitir reservar despu&eacute;s de</span></td>
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
                    <td class="columnafija">Activo</td>
                    <td colspan="2"><span class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $EditDisponibilidad["Activo"] , "Activo" , "title=\"Activo\"" )?></span></td>
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