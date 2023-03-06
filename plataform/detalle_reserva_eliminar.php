<?
	include( "procedures/general.php" );
	include( "procedures/reserva.php" );
	include( "cmp/seo.php" );
?>
	</head>

	<body class="no-skin">


		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>


			<div class="main-content">
				<div class="main-content-inner">

					<div class="page-content">

						<?
						SIMNotify::each();
						?>


						<div class="page-header">
							<h1>
								Home
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?=$array_clubes[ SIMUser::get("club") ]["Nombre"] ?>
									<i class="ace-icon fa fa-angle-double-right"></i>
									DETALLE RESERVA
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->


								<div class="row">
									<div class="col-sm-12">


                                    <form id="frmDeleteReserva" name="frmDeleteReserva" action="" method="post" enctype="multipart/form-data">

                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                     <tr>
                                     	<td>Numero Reserva</td>
                                        <td><?php echo $detalle_reserva["IDReservaGeneral"]; ?></td>
                                     </tr>
                                     <tr>
                                       <td>Fecha / Hora</td>
                                       <td><?php echo $detalle_reserva["Fecha"] . " " .  $detalle_reserva["Hora"]; ?></td>
                                     </tr>
                                     <tr>
                                       <td>Club</td>
                                       <td><?php echo $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$detalle_reserva["IDClub"]."'" ); ?></td>
                                     </tr>
                                     <tr>
                                       <td>Socio</td>
                                       <td><?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ); ?></td>
                                     </tr>
                                     <tr>
                                       <td>Servicio</td>
                                       <td>

																				 <?php

																				 $id_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '".$detalle_reserva["IDServicio"]."'" );


									 										$nombre_servicio_personalizado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDClub = '".SIMUser::get("club")."' and IDServicioMaestro = '" . $id_maestro . "'" );
									 										if(empty($nombre_servicio_personalizado))
									 											$nombre_servicio_personalizado =$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$id_maestro."'" );

									 									   	echo $nombre_servicio_personalizado;



									   ?></td>
                                     </tr>
                                     <tr>
                                       <td>Elemento</td>
                                       <td><?php echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '".$detalle_reserva["IDServicioElemento"]."'" ); ?></td>
                                     </tr>
                                     <?php if ($detalle_reserva["IDAuxiliar"]>0): ?>
                                     <tr>
                                       <td>Auxiliar</td>
                                       <td><?php echo $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '".$detalle_reserva["IDAuxiliar"]."'" ); ?></td>
                                     </tr>
                                     <?php endif; ?>

                                     <?php if ($detalle_reserva["IDTipoModalidadEsqui"]>0): ?>
                                     <tr>
                                       <td>Modalidad</td>
                                       <td><?php echo $dbo->getFields( "TipoModalidadEsqui" , "Nombre" , "IDTipoModalidadEsqui = '".$detalle_reserva["IDTipoModalidadEsqui"]."'" ); ?></td>
                                     </tr>
                                     <?php endif; ?>

                                     <?php if (!empty($detalle_reserva["Tee"])): ?>
                                     <tr>
                                       <td>Tee</td>
                                       <td><?php echo $detalle_reserva["Tee"]; ?></td>
                                     </tr>
                                      <?php endif; ?>

                                      <?php if (!empty($detalle_reserva["Observaciones"])): ?>
                                     <tr>
                                       <td>Observaciones</td>
                                       <td><?php echo $detalle_reserva["Observaciones"]; ?></td>
                                     </tr>
                                     <?php endif; ?>

                                     <?php if(count($array_invitados)>0 || $detalle_reserva["IDServicio"] == "24"): ?>

                                     <tr>
                                     	<td>Invitados</td>
                                        <td>
                                        <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8"  multiple >
                                        	<?php
											$item=1;
                                        	foreach($array_invitados as $id_invitado => $datos_invitado):
													$item--;
													if($datos_invitado["IDSocio"]>0):
														$nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ));
													?>
														<option value="<?php echo "socio-".$datos_invitado["IDSocio"]; ?>"><?php echo $nombre_socio; ?></option>
                                                    <?php
													else: ?>
                                                    	<option value="<?php echo "externo-".$datos_invitado["Nombre"]; ?>"><?php echo $datos_invitado["Nombre"]; ?></option>
                                                    <?php
													endif;
											endforeach;
											?>
                                        </select>
                                        <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">


                                        </td>
                                     </tr>




                                     <?php endif; ?>
                                     <tr>
                                       <td colspan="2">Razon de la cancelacion de la reserva</td>
                                      </tr>
                                     <tr>
                                       <td colspan="2">
                                       <textarea name="RazonCancelacion" id="RazonCancelacion" class="form-control" required></textarea>
                                       </td>
                                     </tr>


                                      <tr>
                                       <td align="center" colspan="2">
                                       <input type="hidden" name="action" id="action" value="delete_reserva">
                                       <input type="hidden" name="UsuarioElimina" id="UsuarioElimina" value="<?php echo $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".SIMUser::get("IDUsuario")."'" ); ?>">
                                       <input type="hidden" name="IDReservaGeneral" id="IDReservaGeneral" value="<?php echo $detalle_reserva["IDReservaGeneral"]; ?>">
                                       <input type="submit" name="elimina_reserva" id="elimina_reserva" value="Elimina Reserva"> </td>
                                      </tr>


                                    </table>
                                    </form>






									</div><!-- /.col -->


								</div><!-- /.row -->

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->


            <?
			include( "cmp/footer_scripts.php" );
			?>

			<?
				include("cmp/footer.php");
			?>
		</div><!-- /.main-container -->


	</body>
</html>
