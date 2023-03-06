<?php
	include( "procedures/general.php" );
	include( "cmp/seo.php" );
	$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);
	$json = file_get_contents('https://miclubapp.com/services/club.php?key=CEr0CLUB&action=getfactura&IDSocio='.$_GET["IDSocio"].'&IDClub='.$_GET["IDClub"], false, stream_context_create($arrContextOptions));
  $objDatos = json_decode($json);
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
									FACTURA
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->


								<div class="row">
									<div class="col-sm-12">

																		<?php if(!empty($_GET["IDSocio"])){ ?>




																	  <table id="simple-table" class="table table-striped table-bordered table-hover">
																			<?php
																			$contador=1;
																			if(isset($objDatos)):
																			 if($objDatos->success && isset($objDatos->response[0])):
																				 foreach($objDatos->response as $contenido):
																					 foreach($contenido->Categorias as $categoria):
																						 foreach($categoria->Facturas as $facturas):
																							 if($contador<=30): // Maximo mustro 3 meses ?>
																							 <tr>
				                                         <td><?php echo $categoria->Nombre; ?></td>
				                                         <td>
																									 <?php echo $facturas->Fecha; ?><br>
																									 <?php echo $facturas->NumeroFactura; ?><br>
																									 <?php echo $facturas->ValorFactura; ?><br>
																									 <?php echo $facturas->Almacen; ?>
																									 <?php //print_r($objDatos); ?>
																								 </td>
																								 <td>
																									 <?php
																									 $json_detalle = file_get_contents('https://miclubapp.com/services/club.php?key=CEr0CLUB&action=getdetallefactura&IDSocio='.$_GET["IDSocio"].'&IDClub='.$_GET["IDClub"].'&IDFactura='.$facturas->IDFactura, false, stream_context_create($arrContextOptions));
																									 $objDatosDetalle = json_decode($json_detalle);
																									 if(isset($objDatosDetalle)){
							 																			 if($objDatosDetalle->success && isset($objDatosDetalle->response[0])){
							 																				 foreach($objDatosDetalle->response as $contenido_detalle){
																												 	print_r($contenido_detalle->CuerpoFactura);
							 																				}
																										}
																									}


																									 print_r($objDatosDetalle->response->CuerpoFactura);
																									 ?>
																								 </td>
				                                       </tr>
																							 <?php
																							 endif;
																							 $contador++;
																						 endforeach;
																					 endforeach;
																				 endforeach;
																			 endif;
																			endif;
																			?>
																	 <?php } ?>
																 	</table>


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
