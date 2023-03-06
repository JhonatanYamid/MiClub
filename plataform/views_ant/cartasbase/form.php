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
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
 									 <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

 									 <div class="col-sm-8">
 										 <!--
 																				 <select name = "IDSocio" id="IDSocio" <?php if($_GET["action"]!= "add") echo "disabled"; ?>>
 											 <option value=""></option>
 											 <?php
 									 $sql_socio_club = "Select * From Socio Where IDClub = '".SIMUser::get("club")."' Order by Apellido Asc";
 									 $qry_socio_club = $dbo->query($sql_socio_club);
 									 while ($r_socio = $dbo->fetchArray($qry_socio_club)): ?>
 											 <option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if($r_socio["IDSocio"]==$frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " .$r_socio["Nombre"]); ?></option>
 											 <?php
 										 endwhile;    ?>
 											 </select>
 																				 -->
 																					 <?php
 									 $sql_socio_club = "Select * From Socio Where IDSocio = '".$frm["IDSocio"]."'";
 									 $qry_socio_club = $dbo->query($sql_socio_club);
 									 $r_socio = $dbo->fetchArray($qry_socio_club);
 									 if(!empty($frm["IDSocio"])){
 										 $label_accion=" Accion: " . $r_socio["Accion"];
 										 if($frm[IDClub]==35)
 											 $label_accion=" Casa: " . $r_socio["Predio"];
 									 }
 									 ?>

 																					 <input type="text" id="Accion" name="Accion" placeholder="Accion Nombre Apellido Numero Documento" class="col-xs-12 mandatory autocomplete-ajax" title="Accion" <?php if($_GET["action"]!= "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " .$r_socio["Nombre"] . $label_accion) ?>" >
 																					 Busqueda por: Accion, Nombre, Apellido, Numero Documento
 										 <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">

 									 </div>
 							 </div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero </label>
										<div class="col-sm-8"><input type="text" id="Numero" name="Numero" placeholder="Numero" class="col-xs-12 Numero" title="Numero" value="<?php echo $frm["Numero"];?>"></div>
								</div>
							</div>



							<div class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombres </label>
										<div class="col-sm-8"><input type="text" id="Nombres" name="Nombres" placeholder="Nombres" class="col-xs-12 " title="Nombres" value="<?php echo $frm["Numero"];?>"></div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Carta </label>
										<div class="col-sm-8"><input type="text" id="Carta" name="Carta" placeholder="Carta" class="col-xs-12 " title="Carta" value="<?php echo $frm["Carta"];?>"></div>
								</div>
							</div>

							<div class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> PorVencer </label>
										<div class="col-sm-8"><input type="text" id="PorVencer" name="PorVencer" placeholder="PorVencer" class="col-xs-12 " title="PorVencer" value="<?php echo $frm["PorVencer"];?>"></div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dia30 </label>
										<div class="col-sm-8"><input type="text" id="Dia30" name="Dia30" placeholder="Dia30" class="col-xs-12 " title="Dia30" value="<?php echo $frm["Dia30"];?>"></div>
								</div>
							</div>

							<div class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dia60 </label>
										<div class="col-sm-8"><input type="text" id="Dia60" name="Dia60" placeholder="Dia60" class="col-xs-12 " title="Dia60" value="<?php echo $frm["Dia60"];?>"></div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dia90 </label>
										<div class="col-sm-8"><input type="text" id="Dia90" name="Dia90" placeholder="Dia90" class="col-xs-12 " title="Dia90" value="<?php echo $frm["Dia90"];?>"></div>
								</div>
							</div>

							<div class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dia120 </label>
										<div class="col-sm-8"><input type="text" id="Dia120" name="Dia120" placeholder="Dia120" class="col-xs-12 " title="Dia120" value="<?php echo $frm["Dia120"];?>"></div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mas120 </label>
										<div class="col-sm-8"><input type="text" id="Mas120" name="Mas120" placeholder="Mas120" class="col-xs-12 " title="Mas120" value="<?php echo $frm["Mas120"];?>"></div>
								</div>
							</div>

							<div class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> SaldoVencido60 </label>
										<div class="col-sm-8"><input type="text" id="SaldoVencido60" name="SaldoVencido60" placeholder="SaldoVencido60" class="col-xs-12 " title="SaldoVencido60" value="<?php echo $frm["SaldoVencido60"];?>"></div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> GeneralValor </label>
										<div class="col-sm-8"><input type="text" id="GeneralValor" name="GeneralValor" placeholder="GeneralValor" class="col-xs-12 " title="GeneralValor" value="<?php echo $frm["GeneralValor"];?>"></div>
								</div>
							</div>

							<div class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> FechaAbono </label>
										<div class="col-sm-8"><input type="text" id="FechaAbono" name="FechaAbono" placeholder="FechaAbono" class="col-xs-12 " title="FechaAbono" value="<?php echo $frm["FechaAbono"];?>"></div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> AbonoActual </label>
										<div class="col-sm-8"><input type="text" id="AbonoActual" name="AbonoActual" placeholder="AbonoActual" class="col-xs-12 " title="AbonoActual" value="<?php echo $frm["AbonoActual"];?>"></div>
								</div>
							</div>

							<div class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> NuevoSaldo </label>
										<div class="col-sm-8"><input type="text" id="NuevoSaldo" name="NuevoSaldo" placeholder="NuevoSaldo" class="col-xs-12 " title="NuevoSaldo" value="<?php echo $frm["NuevoSaldo"];?>"></div>
								</div>
							
							</div>




							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
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
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>
