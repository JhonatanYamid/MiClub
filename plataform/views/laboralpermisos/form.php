<?php
$estadosLaboral =  SIMResources::$estado_laboral;
$motivo = SIMResources::$motivo_laboral;

if(!empty($frm["IDUsuario"])){
	$frm["TipoSolicitante"] = "Usuario";	
}else if(!empty($frm["IDSocio"])){
	$frm["TipoSolicitante"] = "Socio";		
}
if(!empty($frm["FechaInicio"])){
	$frm["FechaInicio"] = date("Y-m-d", strtotime($frm["FechaInicio"]));
}
if(!empty($frm["FechaFin"])){
	$frm["FechaFin"] = date("Y-m-d", strtotime($frm["FechaFin"]));
}
if(empty($frm["IDEstado"])){
	$frm["IDEstado"] = 1;
}
if($frm["TipoSolicitante"] == "Usuario") {	
	$query = "SELECT Nombre FROM Usuario WHERE IDUsuario={$frm["IDUsuario"]}";
	$consulta = $dbo->query($query);
	$consulta = $dbo->fetch($consulta);
	$frm["SolicitanteUsuario"] = $consulta["Nombre"];
	$inputShow = "Usuario";
} else if($frm["TipoSolicitante"] == "Socio"){
	$query = "SELECT Nombre FROM Socio WHERE IDSocio={$frm["IDSocio"]}";
	$consulta = $dbo->query($query);
	$consulta = $dbo->fetch($consulta);
	$frm["SolicitanteSocio"] = $consulta["Nombre"];
	$inputShow = "Socio";
}
?>

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get( "title" ))?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
							
							<div class="widget-header widget-header-large">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-info green"></i>
									Información por parte del solicitante
								</h3>
							</div>							
							<div  class="form-group first ">
    	                        <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Solicitante:  </label>
										<div class="col-sm-8">
											<label for="Usuario">Usuario</label>									
											<input <?php if($frm["TipoSolicitante"]=="Usuario"){echo "checked";}?> type="radio" id="Usuario" name="TipoSolicitante" value="Usuario" title="Tipo Solicitante" <?php if($_GET["action"]=="edit"){echo " disabled";}?>>
											<label for="Socio">Socio</label>									
											<input <?php if($frm["TipoSolicitante"]=="Socio"){echo "checked";}?> type="radio" id="Socio" name="TipoSolicitante"  value="Socio" title="Tipo Solicitante" <?php if($_GET["action"]=="edit"){echo " disabled";}?>>
											
											<!--? echo SIMHTML::formradiogroup( array_flip( ["Socio", "Usuario"] ) , $tipoSolicitante , 'TipoSolicitante' , "class='input mandatory'" ) ?-->
																										
									</div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitante </label>
										<div class="col-sm-8">								
                                			<input type="text" id="solicitanteUsuario" name="solicitante" placeholder="Usuario" class="col-xs-12 autocomplete-ajax-funcionario-laboralUsuario <?php if($inputShow!="Usuario"){ echo "hidden";}?>" title="Solicitante"  value="<?php echo $frm["SolicitanteUsuario"] ?>" <?php if($_GET["action"]=="edit"){echo " disabled";}?>>                                			
											<input type="text" id="solicitanteSocio" name="solicitante" placeholder="Socio" class="col-xs-12 autocomplete-ajax  <?php if($inputShow!="Socio"){ echo "hidden";}?>" title="Solicitante"  value="<?php echo $frm["SolicitanteSocio"] ?>" <?php if($_GET["action"]=="edit"){echo " disabled";}?>>                                			
											<input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario"  title="Usuario" >
											<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio"  title="Socio">
										</div>
								</div>
							</div>
							<div class="form-group first ">							
								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Motivo </label>
									<div class="col-sm-8">
										<select name="IDMotivo" id="IDMotivo" class="form-control" <?php if($_GET["action"]=="edit"){echo " disabled";}?>>											
											<?php foreach($motivo as $key => $value){ ?>
											<option <?php if($frm['IDMotivo']==$key) {echo " selected "; }?>value="<?php echo $key?>"><?php echo $value?></option>                               
											<?php } ?>
										</select>    							
									</div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Remunerado:  </label>
										<div class="col-sm-8">																					
											<? echo SIMHTML::formradiogroup( array_flip(  array_flip( SIMResources::$sino ) ) , $frm["Remunerado "] , 'Remunerado' , "class='input'" ) ?>																										
									</div>
								</div>							
							</div>
						    <div  class="form-group first ">                        		
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Días </label>
										<div class="col-sm-8">
                                            <input type="text" id="DiasHabiles" name="DiasHabiles" placeholder="DiasHabiles" class="col-xs-12 mandatory" title="DiasTomar" value="<?php echo $frm["DiasHabiles"];?>" <?php if($_GET["action"]=="edit"){echo " disabled";}?>>
    									</div>
								</div>                                
                            </div>
                            <div  class="form-group first ">
    	                         <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio:  </label>
										<div class="col-sm-8">
                                            <input type="text" id="FechaInicio" name="FechaInicio" placeholder="fecha Inico" class="col-xs-12 calendar mandatory" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"];?>" <?php if($_GET["action"]=="edit"){echo " disabled";}?>>
                                        </div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>
										<div class="col-sm-8">
											<input type="text" id="FechaFinMostrar" name="FechaFinMostrar" placeholder="Fecha Fin" class="col-xs-12 mandatory calendar" title="Fecha Fin" value="<?php echo $frm["FechaFin"];?>" disabled>
										</div>
								</div>
							</div>
							<div  class="form-group first ">
    	                         <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Comentario solicitante:  </label>
										<div class="col-sm-8">
											<textarea id="Comentario" name="Comentario" cols="10" rows="5" class="col-xs-12 mandatory" title="Comentario" <?php if($_GET["action"]=="edit"){echo " disabled";}?>><?php echo $frm["Comentario"]; ?></textarea>
                                        </div>
								</div>								
							</div>
							<div class="widget-header widget-header-large">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-exclamation-circle green"></i>
									Información por parte del club
								</h3>
							</div>
							<div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado </label>
									<div class="col-sm-8">
										<select name="IDEstado" id="IDEstado" class="form-control" <?php if($_GET["action"]!="edit"){echo " disabled";}?>>											
											<?php foreach($estadosLaboral as $key => $value){ ?>
											<option <?php if($frm['IDEstado']==$key) {echo " selected "; }?>value="<?php echo $key?>"><?php echo $value?></option>                               
											<?php } ?>
										</select>    							
									</div>
								</div>
								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Comentario aprobador: </label>
									<div class="col-sm-8">
										<textarea id="ComentarioAprobacion" name="ComentarioAprobacion" cols="10" rows="5" class="col-xs-12 <?php if($_GET["action"]=="edit"){echo 'mandatory';}?>" title="ComentarioAprobacion" <?php if($_GET["action"]!="edit"){echo " disabled";}?>><?php echo $frm["ComentarioAprobacion"]; ?></textarea>
									</div>
								</div>
							</div>
							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="FechaFin" id="FechaFin" value="<?php echo $frm["FechaFin"] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[ $key ] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[ $key ] ?>" />
								</div>
							</div>
                        </form>
                    </div>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>

<script type="text/javascript">	

	$("#Usuario").click(function(){
		$("#solicitanteUsuario").removeClass("hidden");					
		$("#solicitanteSocio").addClass("hidden");	
		
	});

	$("#Socio").click(function(){		
		$("#solicitanteSocio").removeClass("hidden");					
		$("#solicitanteUsuario").addClass("hidden");
	});

	$("#FechaInicio, #DiasHabiles").change(function(){				
		fechaInicio = $("#FechaInicio").val();
		dias = $("#DiasHabiles").val();		

		if(fechaInicio!=undefined && dias!=undefined){
			$.ajax({
				url: "includes/async/<?php echo $script; ?>.async.php?oper=caculafechafin&fechainicio=" + fechaInicio + "&dias=" + dias,
				success: function(result){				
					$("#FechaFin, #FechaFinMostrar").val(result.response)
					
				},
				error: function(error){
					console.log(error);
				}
			});
		}
	})


</script>