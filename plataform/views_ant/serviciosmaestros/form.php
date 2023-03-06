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
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono </label>

										<div class="col-sm-8">
											<? if (!empty($frm[Icono])) {
					echo "<img src='".SERVICIO_ROOT."$frm[Icono]' width=55 >";
					?>
			    <a
					href="<? echo $script.".php?action=delfoto&foto=$frm[Icono]&campo=Icono&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
			    <?
				}// END if
				?>
			    <input name="Icono" id=file class=""	 title="Icono" type="file" size="25" style="font-size: 10px">


										</div>
								</div>

							</div>




							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> General </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["General"] , 'General' , "class='input mandatory'" ) ?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Elemento Inicial </label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formPopUp( "ServicioInicial" , "Nombre" , "Nombre" , "IDServicioInicial" , $frm["IDServicioInicial"] , "[Seleccione el Servicio Inicial]" , "popup form-control" , "title = \"Servicio Inicial\"" )?>
										</div>
								</div>

							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Elemento </label>

										<div class="col-sm-8">
											<input id=LabelElemento type=text size=25  name=LabelElemento class="input" title="Label Elemento" value="<?=$frm[LabelElemento] ?>">
							              <br>(Label utilizado en el boton app)
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pemite Auxiliares (Ej: Boleadores) </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteAuxiliar"] , 'PermiteAuxiliar' , "class='input mandatory'" ) ?>
										</div>
								</div>

							</div>



						<div  class="form-group first ">

    		                    <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Reservar Cancha automaticamente (solo para clases) </label>

										<div class="col-sm-8">
                                        <select name="IDServicioMaestroReservar" id="IDServicioMaestroReservar" class="form-control" title="Servicio Rezervar">
                                        	<option value=""></option>
                                            <?php $sql_maestros = $dbo->query("Select * From ServicioMaestro Where Publicar = 'S'");
												  while ($row_serviciomaestro = $dbo->fetchArray($sql_maestros)): ?>
                                                  <option value="<?php echo $row_serviciomaestro["IDServicioMaestro"]; ?>" <?php if($row_serviciomaestro["IDServicioMaestro"]==$frm["IDServicioMaestroReservar"]) echo "selected"; ?>><?php echo $row_serviciomaestro["Nombre"]; ?></option>
												  <?php endwhile; ?>
                                        </select>
											<?php //echo SIMHTML::formPopUp( "ServicioMaestro" , "Nombre" , "Nombre" , "IDServicioMaestroReservar" , $frm["IDServicioMaestroReservar"] , "[Seleccione Servicio]" , "popup form-control" , "title = \"Servicio\"" )?>
										</div>
								</div>


								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Reservas 2 turnos cuando el numero de invitados sea: </label>

										<div class="col-sm-8">
											<input id="InvitadoTurnos" type="number" size=25  name="InvitadoTurnos" class="col-xs-12" title="Invitado Turnos" value="<?=$frm[InvitadoTurnos] ?>">
										</div>
								</div>

							</div>


                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Auxiliar (Boleador) </label>

										<div class="col-sm-8">
											<input id=LabelAuxiliar type=text size=25  name=LabelAuxiliar class="input" title="Label Auxiliar" value="<?=$frm[LabelAuxiliar] ?>">
							              <br>(Label utilizado en el boton app)
										</div>
								</div>

                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Tipo Turno (Sencillos, dobles, etc) </label>

										<div class="col-sm-8">
											<input id=LabelTipoReserva type=text size=25  name=LabelTipoReserva class="input" title="Label Tipo Reserva" value="<?=$frm[LabelTipoReserva] ?>">
							              <br>(Label utilizado en el boton app)
										</div>
								</div>



							</div>


                            <div  class="form-group first ">


								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pemite Tipo Turnos (Ej: Sencillos, dobles, etc) </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteTipoReserva"] , 'PermiteTipoReserva' , "class='input mandatory'" ) ?>
										</div>
								</div>

                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ver Horarios en acordeon? </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["HorarioAcordeon"] , 'HorarioAcordeon' , "class='input mandatory'" ) ?>
										</div>
								</div>

							</div>

                            <div  class="form-group first ">

	                            <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pemite Asignar reserva a otro Beneficiario? </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PermiteBeneficiario"] , 'PermiteBeneficiario' , "class='input '" ) ?>
										</div>
								</div>

                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label agregar beneficiario </label>

										<div class="col-sm-8">
											<input id=LabelBeneficiario type=text size=25  name=LabelBeneficiario class="input" title="Label Beneficiario" value="<?=$frm[LabelBeneficiario] ?>">
										</div>
								</div>


							</div>



                <div  class="form-group first ">


                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Si es una reserva multiple solo mostrar fecha en la que empieza reserva? </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["SoloFechaSeleccionada"] , 'SoloFechaSeleccionada' , "class='input mandatory'" ) ?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label reserva multiple </label>

										<div class="col-sm-8">
											<input id=LabelReservaMultiple type=text size=25  name=LabelReservaMultiple class="input" title="Label Reserva Multiple" value="<?=$frm["LabelReservaMultiple"] ?>">
										</div>
								</div>





							</div>


            <div  class="form-group first ">

							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pemite Reserva de Grupos ? </label>

									<div class="col-sm-8">
										<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["ReservaGrupos"] , 'ReservaGrupos' , "class='input '" ) ?>
									</div>
							</div>


								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

										<div class="col-sm-8">
											<? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?>
										</div>
								</div>


							</div>

							<div  class="form-group first ">




	                               <div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>

											<div class="col-sm-8">
											  <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
											</div>
									</div>



								</div>





							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>


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
