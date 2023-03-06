<?php
$IDLukerEmpleado = $_GET["id"];
$sql = "SELECT * FROM LukerEstudio WHERE IDLukerEmpleado=$IDLukerEmpleado";
$queryEstudios = $dbo->query($sql);
$estudios = $dbo->fetch($queryEstudios);
$estudios = isset($estudios["IDLukerEstudio"]) ? [$estudios] : $estudios;

foreach($estudios as $frm):	
?>
<hr>
<div class="container">
	<div class="">		
		<div class="">
			<div class="">
				<div class="">
					<div class="">
						<form class="" role="form" method="post" id="frm_estudio" action="" enctype="multipart/form-data">				
								<div class="row">																
										<div class="form-group col-md-4">
											<label for="UGN1_CODIGO"> País donde estudió*  </label>
											<input type="text" id="UGN1_CODIGO" name="UGN1_CODIGO" placeholder="" class="form-control" title="País donde estudió" maxlength="60" value="<?php echo $frm["UGN1_CODIGO_DETALLE"] ?>"disabled> 
										</div>									
										<div  class="form-group col-md-4">
											<label for="UGN2_CODIGO"> Departamento donde estudió  </label>																														
											<input type="text" id="UGN2_CODIGO" name="UGN2_CODIGO" placeholder="" class="form-control" title="Departamento donde estudió" maxlength="60" value="<?php echo $frm["UGN2_CODIGO_DETALLE"] ?>"disabled> 
										</div>
										<div  class="form-group col-md-4">
											<label for="UGN3_CODIGO"> Ciudad donde estudió  </label>																														
											<input type="text" id="UGN3_CODIGO" name="UGN3_CODIGO" placeholder="" class="form-control" title="Ciudad donde estudió" maxlength="60" value="<?php echo $frm["UGN3_CODIGO_DETALLE"] ?>"disabled> 
										</div>
								</div>
								<div class="row">																
										<div class="form-group col-md-4">
											<label for="TERC_DOCUMENTO"> Institución </label>																													
											<input type="text" id="TERC_DOCUMENTO" name="TERC_DOCUMENTO" placeholder="" class="form-control" title="Institución" maxlength="60" value="<?php echo $frm["TERC_DOCUMENTO_DETALLE"] ?>"disabled> 
										</div>									
										<div  class="form-group col-md-4">
											<label for="ESXB_TITULO"> Título </label>																														
											 <input type="text" id="ESXB_TITULO" name="ESXB_TITULO" placeholder="" class="form-control" title="Título" maxlength="60" value="<?php echo $frm["ESXB_TITULO"] ?>"disabled> 
										</div>										
								</div>
								<div class="row">															
										<div  class="form-group col-md-4">
											<label for="UGN2_CODIGO"> Fecha terminación </label>																														
											 <input type="date" id="ESXB_FECHA_RET" name="ESXB_FECHA_RET" placeholder="" class="form-control" title="Fecha terminación"  value="<?php echo $frm["ESXB_FECHA_RET"] ?>"disabled> 
										</div>										
								</div>
								<div class="row">															
										<div  class="form-group col-md-6">
											<label for="ESXB_IDIOMAS"> Observaciones </label>
											<input type="hidden" id="EMP_CODIGO" name="EMP_CODIGO" placeholder="" class="" title="EMP_CODIGO"  value="<?php echo $frm["EMP_CODIGO"] ?>" disabled>
											<textarea id="ESXB_IDIOMAS" name="ESXB_IDIOMAS" class="form-control" title="Observaciones" maxlength="100" disabled><?php echo $frm["ESXB_IDIOMAS"] ?></textarea> 
										</div>										
								</div>
								<div class="row">
											<div  class="form-group col-md-4">
												<label for=""> Adjunto: </label>
												<a href="<?php echo URLROOT. "file/luker/actualizacionempleados/estudios/". $frm["Archivo"]?>" width="200">Descargar archivo</a>
											</div>
									
								</div>							
						</form>												
						</div>
					</div>
				</div>
			</div><!-- /.widget-main -->
		</div><!-- /.widget-body -->
	</div><!-- /.widget-box -->
</div>

<?php endforeach; ?>

<hr>
<a href="actualizacionempleados.php?action=edit&id=<?php echo $IDLukerEmpleado?>" type="button" id="" class="btn btn-secondary btn-lg btn-block">Regresar a empleado</a>
<hr>

<?
	//include( "cmp/footer_scripts.php" );
?>

<script type="text/javascript">	
	

</script>