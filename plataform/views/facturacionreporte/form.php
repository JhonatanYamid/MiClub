  
<?
	// include('estilos.php');
	
if($_GET["action"]):
$opcion=$_GET["action"];
$newmode="insert";
endif; 

	$arrOpciones = $hijos;
	$hoy = date("Y-m-d");
	$tipo = 1;//club tipo sede o club sin sedes
 
	if($IDClub == $idPadre && !empty($arrOpciones)){
		$tipo = 2;//club tipo padre

		$sqlSede = "SELECT r.IDClub, c.Nombre
					FROM  ResolucionFactura as r, Club as c
					WHERE r.IDClub = c.IDClub AND r.Activo = 'S' AND c.IDClubPadre = $idPadre";

		$qrySede = $dbo->query($sqlSede);
	}
?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
						<!-- INFORMACION FACTURA -->
						<div class="widget-header widget-header-large no-margin">
							<h4 class="widget-title grey lighter">
								<i class="ace-icon fa fa-file-text green"></i>  <?php echo  $reporte?>
							</h4>
						</div>
						
<?php 
if($opcion=="mediospago"):    

                                $club= $frm['IDClub'];
				$inicio= $frm['FechaCreacion'];
				$fin= $frm['FechaVence'];
?>						

<br><br>
						<div class="form-group first ">
							<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
											if($respuesta): 
											if($club==157){
							  echo '<option value="157" selected>TODOS</option>';
							  }
 
											while ($rSede = $dbo->fetchArray($qrySede)){  
												
					 if($club==$rSede['IDClub']){ 	 
					 echo '<option value="' .$rSede['IDClub']. '" selected>' .$rSede['Nombre'].'</option>'
 
					 ;}else{ 
 
					 echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													}
													
												 
													  
												}
												else:
																					  echo '<option value="157' .$rSede['IDClub']. '" selected>TODOS</option>';
												while ($rSede = $dbo->fetchArray($qrySede)){  
												
 
													echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													
												 
													  
												}
													endif;
											?>
										</select>
									</div>
								</div>

							<? }else{ ?>

								<div class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreClub"></label>
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
 
									 
								</div>
							<? } ?>
					  	 	<input type="hidden" name="reporte" id="IDClub" value="<?php echo $reporte?>" />
							<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ordenar por: </label>
									<div class="col-sm-8">
										<select name = "Orden" id="Orden">
										
										
											<?
											
											if($respuesta): 
											
											if($orden=="cantidad"){
										echo '<option value="'.$orden.'" selected>CANTIDAD DE VECES ULTILIZADO</option>
										    <option value="totalpagado" >VALOR TOTAL PAGADO</option> ';
										
											}else{
										 echo '<option value="'.$orden.'" selected>VALOR TOTAL PAGADO</option> 
										   <option value="cantidad" >CANTIDAD DE VECES ULTILIZADO</option>';
											}
 
												else:
												 echo"
							  <option value='cantidad'selected>CANTIDAD DE VECES ULTILIZADO</option>
							  <option value='totalpagado' >VALOR TOTAL PAGADO</option>  ";
							  endif;
												 
											?>
										</select>
									</div>
								</div>
						</div>
						  
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> Fecha incial: </label>
								<div class="col-sm-8">
			<input type="text" id="FechaCreacion" name="FechaCreacion" title="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" class="col-xs-12 calendar" 
			
			value="<?
			if($respuesta): 
			echo $inicio;
			else:
			echo $hoy;
			 endif; ?>" />
								</div>
 
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha final: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaVence" name="FechaVence" title="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?  	 	if($respuesta): 
			echo $fin;
			else:
			echo $hoy;
			 endif;
									?>" />
								</div>
							</div>
						</div>
						<br><br><br><br>
						<center>
					 <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
					 <input type="hidden" name="opcion"  value="<?php echo $opcion ?>" />
					 
						 <button type="submit" class="btn btn-primary"  name="<?php echo $opcion; ?>" >Generar reporte</button> </center>
						</form>
						<br><br>
						<!-- DATATABLE DEL REPORTE -->
						
					<?php
					
					if($respuesta) :  
					include("r_mediosdepagos.php");
					endif;
					?> 
 		
						<!-- FIN DE LA TABLA REPORTE -->
						
						
						
						
						
						<br><br><br><br><br><br><br><br><br><br>
						
<?php 
elseif($_GET["action"]=="productos"): 
?>						
						
						
<br><br>
						<div class="form-group first ">
							<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
											if($respuesta): 
											if($club==157){

							  echo '<option value="157" selected>TODOS</option>';
							  }
 
											while ($rSede = $dbo->fetchArray($qrySede)){  
												
					 if($club==$rSede['IDClub']){ 	 
					 echo '<option value="' .$rSede['IDClub']. '" selected>' .$rSede['Nombre'].'</option>'
 
					 ;}else{ 
 
					 echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													}
													
												 
													  
												}
												else:
																					  echo '<option value="157' .$rSede['IDClub']. '" selected>TODOS</option>';
												while ($rSede = $dbo->fetchArray($qrySede)){  
												
 
													echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													
												 
													  
												}
													endif;
											?>
										</select>
									</div>
								</div>

							<? }else{ ?>

								<div class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreClub"></label>
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
 
									 
								</div>
							<? } ?>
					  	 	<input type="hidden" name="reporte" id="IDClub" value="<?php echo $reporte?>" />
							 
						</div>
						  
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> Fecha incial: </label>
								<div class="col-sm-8">
			<input type="text" id="FechaCreacion" name="FechaCreacion" title="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" class="col-xs-12 calendar" 
			
			value="<?
			if($respuesta): 
			echo $inicio;
			else:
			echo $hoy;

			 endif; ?>" />
								</div>
 
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha final: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaVence" name="FechaVence" title="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?  	 	if($respuesta): 

			echo $fin;
			else:
			echo $hoy;
			 endif;
									?>" />
								</div>
							</div>
						</div>
						<br><br><br><br>
						<center>
					 <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
					 <input type="hidden" name="opcion"  value="<?php echo $opcion ?>" />
						 <button type="submit" class="btn btn-primary"  name="<?php echo $opcion; ?>">Generar reporte</button> </center>
						</form>
						<br><br>
						<!-- DATATABLE DEL REPORTE -->
						
					<?php
					
					if($respuesta) :  
					include("r_productos.php");
					endif;
					?> 
					
					
						
<?php 
elseif($_GET["action"]=="detalleventa"): 
?>												
					 	
<br><br>
						<div class="form-group first ">
							<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
											if($respuesta): 
											if($club==157){

							  echo '<option value="157" selected>TODOS</option>';
							  }
 
											while ($rSede = $dbo->fetchArray($qrySede)){  
												
					 if($club==$rSede['IDClub']){ 	 
					 echo '<option value="' .$rSede['IDClub']. '" selected>' .$rSede['Nombre'].'</option>'
 
					 ;}else{ 
 
					 echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													}
													
												 
													  
												}
												else:
																					  echo '<option value="157' .$rSede['IDClub']. '" selected>TODOS</option>';
												while ($rSede = $dbo->fetchArray($qrySede)){  
												
 
													echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													
												 
													  
												}
													endif;
											?>
										</select>
									</div>
								</div>

							<? }else{ ?>

								<div class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreClub"></label>
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
 
									 
								</div>
							<? } ?>
					  	 	<input type="hidden" name="reporte" id="IDClub" value="<?php echo $reporte?>" />
							 
						</div>
						  
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> Fecha incial: </label>
								<div class="col-sm-8">
			<input type="text" id="FechaCreacion" name="FechaCreacion" title="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" class="col-xs-12 calendar" 
			
			value="<?
			if($respuesta): 
			echo $inicio;
			else:
			echo $hoy;

			 endif; ?>" />
								</div>
 
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha final: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaVence" name="FechaVence" title="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?  	 	if($respuesta): 

			echo $fin;
			else:
			echo $hoy;
			 endif;
									?>" />
								</div>
							</div>
						</div>
						<br><br><br><br>
						<center>
					 <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
					 <input type="hidden" name="opcion"  value="<?php echo $opcion ?>" />
						 <button type="submit" class="btn btn-primary"  name="<?php echo $opcion; ?>">Generar reporte</button> </center>
						</form>
						<br><br>
						<!-- DATATABLE DEL REPORTE -->
						
					<?php
					
					if($respuesta) :  
					include("r_detalle_venta.php");
					endif;
					?> 
					
							
						
<?php 
elseif($_GET["action"]=="porvendedor"): 
 
?>												
								
		 <br><br>
						<div class="form-group first ">
							<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>

									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
											if($respuesta): 
											if($club==157){

							  echo '<option value="157" selected>TODOS</option>';
							  }
 
											while ($rSede = $dbo->fetchArray($qrySede)){  
												
					 if($club==$rSede['IDClub']){ 	 
					 echo '<option value="' .$rSede['IDClub']. '" selected>' .$rSede['Nombre'].'</option>'
 
					 ;}else{ 
 
					 echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													}
													
												 
													  
												}
												else:
																					  echo '<option value="157' .$rSede['IDClub']. '" selected>TODOS</option>';
												while ($rSede = $dbo->fetchArray($qrySede)){  
												
 
													echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													
												 
													  
												}
													endif;
											?>
										</select>
									</div>
								</div>

							<? }else{ ?>

								<div class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreClub"></label>
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
 
									 
								</div>
							<? } ?>
					  	 	<input type="hidden" name="reporte" id="IDClub" value="<?php echo $reporte?>" />
							 
						</div>
						  
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> Fecha incial: </label>
								<div class="col-sm-8">
			<input type="text" id="FechaCreacion" name="FechaCreacion" title="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" class="col-xs-12 calendar" 
			
			value="<?
			if($respuesta): 
			echo $inicio;
			else:

			echo $hoy;

			 endif; ?>" />
								</div>
 
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha final: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaVence" name="FechaVence" title="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?  	 	if($respuesta): 


			echo $fin;
			else:
			echo $hoy;
			 endif;

									?>" />
								</div>
							</div>
						</div>
						<br><br><br><br>
						<center>
					 <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
					 <input type="hidden" name="opcion"  value="<?php echo $opcion ?>" />
						 <button type="submit" class="btn btn-primary"  name="<?php echo $opcion; ?>">Generar reporte</button> </center>
						</form>
						<br><br>
						<!-- DATATABLE DEL REPORTE -->
						
					<?php
					
					if($respuesta) :  
					include("r_por_vendedor.php");
					endif;
					?> 
					
<?php 
elseif($_GET["action"]=="afiliadosactivos" or $_GET["action"]=="afiliadosnuevos"): 
?>

	 <br><br>
						<div class="form-group first ">
							<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>

									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
											if($respuesta): 
											if($club==157){

							  echo '<option value="157" selected>TODOS</option>';
							  }
 
											while ($rSede = $dbo->fetchArray($qrySede)){  
												
					 if($club==$rSede['IDClub']){ 	 
					 echo '<option value="' .$rSede['IDClub']. '" selected>' .$rSede['Nombre'].'</option>'
 
					 ;}else{ 
 
					 echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													}
													
												 
													  
												}
												else:
																					  echo '<option value="157' .$rSede['IDClub']. '" selected>TODOS</option>';
												while ($rSede = $dbo->fetchArray($qrySede)){  
												
 
													echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													
												 
													  
												}
													endif;
											?>
										</select>
									</div>
								</div>

							<? }else{ ?>

								<div class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreClub"></label>
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
 
									 
								</div>
							<? } ?>
					  	 	<input type="hidden" name="reporte" id="IDClub" value="<?php echo $reporte?>" />
							 
						</div>
						  
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> Fecha incial: </label>
								<div class="col-sm-8">
			<input type="text" id="FechaCreacion" name="FechaCreacion" title="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" class="col-xs-12 calendar" 
			
			value="<?
			if($respuesta): 
			echo $inicio;
			else:

			echo $hoy;

			 endif; ?>" />
								</div>
 
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha final: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaVence" name="FechaVence" title="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?  	 	if($respuesta): 


			echo $fin;
			else:
			echo $hoy;
			 endif;

									?>" />
								</div>
							</div>
						</div>
						<br><br><br><br>
						<center>
					 <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
					 <input type="hidden" name="opcion"  value="<?php echo $opcion ?>" />
						 <button type="submit" class="btn btn-primary"  name="<?php echo $opcion; ?>">Generar reporte</button> </center>
						</form>
						<br><br>
						<!-- DATATABLE DEL REPORTE -->
						
					<?php
					
					if($respuesta) :  
					include("r_afilidados_activos.php");
					endif;
					?> 
					
			
<?php 
elseif($_GET["action"]=="afiliadosvencidos"): 
  
?>	
 <br><br>
						<div class="form-group first ">
							<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>

									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
											if($respuesta): 
											if($club==157){

							  echo '<option value="157" selected>TODOS</option>';
							  }
 
											while ($rSede = $dbo->fetchArray($qrySede)){  
												
					 if($club==$rSede['IDClub']){ 	 
					 echo '<option value="' .$rSede['IDClub']. '" selected>' .$rSede['Nombre'].'</option>'
 
					 ;}else{ 
 
					 echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													}
													
												 
													  
												}
												else:
																					  echo '<option value="157' .$rSede['IDClub']. '" selected>TODOS</option>';
												while ($rSede = $dbo->fetchArray($qrySede)){  
												
 
													echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													
												 
													  
												}
													endif;
											?>
										</select>
									</div>
								</div>

							<? }else{ ?>

								<div class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreClub"></label>
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
 
									 
								</div>
							<? } ?>
					  	 	<input type="hidden" name="reporte" id="IDClub" value="<?php echo $reporte?>" />
							 
						</div>
						  
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> Fecha incial: </label>
								<div class="col-sm-8">
			<input type="text" id="FechaCreacion" name="FechaCreacion" title="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" class="col-xs-12 calendar" 
			
			value="<?
			if($respuesta): 
			echo $inicio;
			else:

			echo $hoy;

			 endif; ?>" />
								</div>
 
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha final: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaVence" name="FechaVence" title="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?  	 	if($respuesta): 


			echo $fin;
			else:
			echo $hoy;
			 endif;

									?>" />
								</div>
							</div>
						</div>
						<br><br><br><br>
						<center>
					 <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
					 <input type="hidden" name="opcion"  value="<?php echo $opcion ?>" />
						 <button type="submit" class="btn btn-primary"  name="<?php echo $opcion; ?>">Generar reporte</button> </center>
						</form>
						<br><br>
						<!-- DATATABLE DEL REPORTE -->
						
					<?php
					
					if($respuesta) :  
					include("r_afilidados_vencidos.php");
					endif; 
					?>	
					
<?php 
elseif($_GET["action"]=="reportefinanciero"): 
  
?>	
 <br><br>
						<div class="form-group first ">
							<? if($IDClub == $idPadre && !empty($arrOpciones)){ ?>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>

									<div class="col-sm-8">
										<select name = "IDClub" id="IDClub">
											<?
											if($respuesta): 
											if($club==157){

							  echo '<option value="157" selected>TODOS</option>';
							  }
 
											while ($rSede = $dbo->fetchArray($qrySede)){  
												
					 if($club==$rSede['IDClub']){ 	 
					 echo '<option value="' .$rSede['IDClub']. '" selected>' .$rSede['Nombre'].'</option>'
 
					 ;}else{ 
 
					 echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													}
													
												 
													  
												}
												else:
																					  echo '<option value="157' .$rSede['IDClub']. '" selected>TODOS</option>';
												while ($rSede = $dbo->fetchArray($qrySede)){  
												
 
													echo '<option value="' .$rSede['IDClub']. '">' .$rSede['Nombre'].'</option>';
													
												 
													  
												}
													endif;
											?>
										</select>
									</div>
								</div>

							<? }else{ ?>

								<div class="col-xs-12 col-sm-6">

									<label class="col-sm-4 control-label no-padding-right"> <?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>: </label>
									<label class="col-sm-8 tallest" id="nombreClub"></label>
									<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
 
									 
								</div>
							<? } ?>
					  	 	<input type="hidden" name="reporte" id="IDClub" value="<?php echo $reporte?>" />
							 
						</div>
						  
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right"> Fecha incial: </label>
								<div class="col-sm-8">
			<input type="text" id="FechaCreacion" name="FechaCreacion" title="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaCreacion', LANGSESSION); ?>" class="col-xs-12 calendar" 
			
			value="<?
			if($respuesta): 
			echo $inicio;
			else:

			echo $hoy;

			 endif; ?>" />
								</div>
 
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha final: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaVence" name="FechaVence" title="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'fechadevencimiento', LANGSESSION); ?>" class="col-xs-12 calendar" value="<?  	 	if($respuesta): 


			echo $fin;
			else:
			echo $hoy;
			 endif;

									?>" />
								</div>
							</div>
						</div>
						<br><br><br><br>
						<center>
					 <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
					 <input type="hidden" name="opcion"  value="<?php echo $opcion ?>" />
						 <button type="submit" class="btn btn-primary"  name="<?php echo $opcion; ?>">Generar reporte</button> </center>
						</form>
						<br><br>
						<!-- DATATABLE DEL REPORTE -->
						
					<?php
					
					if($respuesta) :  
					include("reporte_financiero.php");
					endif; 
					?>	
					
									
<?

endif;
	include("cmp/footer_scripts.php");
	include('js/general.php');
	include('js/producto.php');
	include('js/beneficiarios.php');
	include('js/descuentos.php');
	include('js/pagos.php');
?>
