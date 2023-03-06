<?php
$estados = [
	"P" => "Pendiente",
	"R" => "Recibido"
];

$consultSocio = "SELECT Nombre, Apellido 
	FROM Socio 
	WHERE IDSocio={$frm['IDSocio']}";
$socioQuery = $dbo->query($consultSocio);
$socio = $dbo->fetch($socioQuery);
$frm["Socio"] = "{$socio["Nombre"]} {$socio["Apellido"]}";

if ($frm["Estado"] == "R") {
	$fechaIngreso = $frm["FechaHoraIngreso"];
	$fechaIngreso = explode(" ", $fechaIngreso);
	$frm["FechaIngreso"] = $fechaIngreso[0];
	$frm["HoraIngreso"] = $fechaIngreso[1];
}

?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="POST" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="Socio" placeholder="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax" value="<?php echo $frm["Socio"] ?>">
									<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">
								
								</div>
							</div>
							  <!-- prueba --> 

         <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apartamento </label>

                        <div class="col-sm-8">
                            <input type="text" id="Accion" name="Accion" placeholder="Apartamento" class="col-xs-12 autocomplete-ajax_predio" title="apartamento">
                            <input type="hidden" name="IDSocios" value="" id="IDSocios"  title="Socio">
 
                        </div> 
                        
                           </div> 
                           <div class="col-xs-12 col-sm-6">
							<br>
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Empresa', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" class="col-xs-12 mandatory" id="Empresa" name="Empresa" placeholder="<?= SIMUtil::get_traduccion('', '', 'Ingreselaempresa', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Empresa', LANGSESSION); ?>" value="<?php echo $frm["Empresa"]; ?>"></div>
							</div>
						 

<!-- --> 
<?php
$club=SIMUser::get("club");
if($club==119){

?>

							<div class="col-xs-12 col-sm-6">
							<br>
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Entragado por </label> 
								<div class="col-sm-8">
									<select name="EntregaRealizadaPor" class="form-control"  >
									          <?php if($frm["EntregaRealizadaPor"]==1){
									           echo '
									           <option value="1" selected >Empresa domiciliaria</option>
										   <option value="2">Personal SMR</option>';
									          }else{
									             echo '
									           <option value="1" >Empresa domiciliaria</option>
										   <option value="2" selected >Personal SMR</option>';
									          }?>
									 
 
									</select>
								</div>
							</div>
							<?php
							}
							?>
							
						</div>
						
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Número de documento</label>
								<div class="col-sm-8"><input type="text" class="col-xs-12 mandatory" id="Documento" name="Documento" placeholder="<?= SIMUtil::get_traduccion('', '', 'Ingreseeldocumento', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Documento', LANGSESSION); ?>" value="<?php echo $frm["Documento"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Nombredeldomiciliario', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" class="col-xs-12 mandatory" id="Nombres" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'Ingreseelnombredeldomiciliario', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Nombredeldomiciliario', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
							</div>
							
						</div>
						
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha de ingreso</label>
								<div class="col-sm-8"><input type="date"   id="Fecha" name="Fecha" placeholder="" class="col-xs-12 mandatory" min="<?php $hoy= date("Y-m-d"); echo $hoy;?>" readonly title=" <?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>" value="<?php
                if(empty($frm["Fecha"])){
                echo $hoy;
                }else{ echo $frm["Fecha"]; 
                
                }?>"></div>
				 
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Horadeingresoaproximada', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="time" class="col-xs-12 " id="Hora" name="Hora" placeholder="" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Horadeingresoaproximada', LANGSESSION); ?>" value="<?php echo $frm["Hora"]; ?>"></div>
							</div>
						</div>
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<select name="Estado" id="Estado" class="form-control">
										<?php foreach ($estados as $keyEstado => $value) { ?>
											<option <?php if ($frm['Estado'] == $keyEstado) {
														echo " selected ";
													} ?>value="<?php echo $keyEstado ?>"><?php echo $value ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div id="Ingreso" class="form-group first <?php if ($frm["Estado"] != "R") {
																		echo "hidden";
																	} ?>">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fechadeingreso', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="date" class="col-xs-12" id="FechaIngreso" min="<?php $hoy= date("Y-m-d"); echo $hoy;?>"  name="FechaIngreso" placeholder="" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Fechadeingreso', LANGSESSION); ?>" value="<?php
                if(empty($frm["FechaIngreso"])){
                echo $hoy;
                }else{ echo $frm["FechaIngreso"]; 
                
                }?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Fechadeingreso', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="time" class="col-xs-12" id="HoraIngreso" name="HoraIngreso" placeholder="" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'HoraIngreso', LANGSESSION); ?>" value="<?php echo $frm["HoraIngreso"]; ?>">
								</div>
							</div>
							
							
						</div>
						 <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Observaciones', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <textarea name="Observaciones" id="Observaciones" cols="33" rows="5"><?php echo $frm["Observaciones"] ?></textarea>

            </div>
        </div>
        


						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="id" id="id" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
																						 <br> <br>
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>
								<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>

<script type="text/javascript">
	$("#Estado").change(function() {
		let estado = $("#Estado").val();

		let dateAhora = new Date();

		fechaAhora = dateAhora.toISOString(dateAhora);
		horaAhora = dateAhora.toTimeString(dateAhora);

		fechaAhora = fechaAhora.split("T")[0];
		horaIngreso = horaAhora.split(" ")[0];

		if (estado == "R") {
			$("#Ingreso").removeClass("hidden");
			$("#FechaIngreso").val(fechaAhora);
			$("#HoraIngreso").val(horaIngreso);
		} else if (estado == "P") {
			$("#Ingreso").addClass("hidden");
		}

	});
</script>
