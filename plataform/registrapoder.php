<?
	include( "procedures/general.php" );
	include( "cmp/seo.php" );

	if($_POST){

		//Verificar si el que otorga el poder ya no se lo otorgo a otro
		$IDVotacionPoder=$dbo->getFields( "VotacionPoder", "IDVotacionPoder", " (IDVotacionVotanteDelegaPoder = '" . $_POST["IDVotadorPadre"]. "' or IDVotacionVotante = '".$_POST["IDVotadorPadre"]."')  and IDVotacionEvento = '" . $_POST["IDVotacionEvento"] . "'" );
		if((int)$IDVotacionPoder>0){
			$mesaje_registro="<span style='color:#FF0000'>La persona ya habia otorgado el poder a otra persona</span>";
			}
		else{
			$frm = SIMUtil::varsLOG( $_POST );
			$datos_otorga =$dbo->fetchAll("VotacionVotante"," IDVotacionVotante = '".$_POST["IDVotadorPadre"]."' ","array");

			$frm["NumeroCasa"]	= $datos_otorga["NumeroCasa"];
			$frm["Coeficiente"] = $datos_otorga["Coeficiente"];
			$frm["Consejero"] = $datos_otorga["Consejero"];
			$frm["Moroso"] = $datos_otorga["Moroso"];
			$frm["Cedula"]=$frm["NumeroDocumento"];
			$IDVotacionEvento=$datos_otorga["IDVotacionEvento"];

			$IDSocio = $dbo->getFields( "Socio", "IDSocio", "NumeroDocumento = '" . $frm["NumeroDocumento"] . "' and IDClub = '".$frm["IDClub"]."'" );
			if((int)$IDSocio<=0){
				$resp=SIMWebServiceApp::set_socio($frm["IDClub"],$frm["Cedula"],$frm["Cedula"],$frm["Parentesco"],$frm["Genero"],$frm["Nombre"],$frm["Apellido"],$frm["FechaNacimiento"],$frm["Cedula"],$frm["CorreoElectronico"],
																		$frm["Telefono"],$frm["Celular"],$frm["Direccion"],$frm["TipoSocio"],"A","100",$frm["Cedula"],$frm["NumeroCasa"],$frm["Categoria"],"S");
				$IDSocio = $dbo->getFields( "Socio", "IDSocio", "NumeroDocumento = '" . $frm["Cedula"] . "' and IDClub = '".$frm["IDClub"]."'" );
			}
			 $UsuarioCrea=SIMUser::get("IDUsuario");
			 SIMUtil::ingreso_votante($frm["IDClub"],$IDVotacionEvento,$IDSocio,$frm["Nombre"],$frm["NumeroCasa"],$frm["Cedula"],$frm["Coeficiente"],$frm["Consejero"],$frm["Moroso"],$UsuarioCrea,"Externo");
			 $IDVotacionVotante = $dbo->getFields( "VotacionVotante", "IDVotacionVotante", "Cedula = '" . $frm["Cedula"] . "' and IDClub = '".$frm["IDClub"]."' and IDVotacionEvento = '".$IDVotacionEvento."'" );
				$sql_insert="INSERT INTO VotacionPoder (IDClub,IDVotacionEvento,IDVotacionVotante,IDVotacionVotanteDelegaPoder,IDUsuarioRegistra,FechaTrCr,UsuarioTrCr)
									 VALUES ('".$frm["IDClub"]."','".$IDVotacionEvento."','".$IDVotacionVotante."','".$_POST["IDVotadorPadre"]."','".$_POST["IDUsuarioRegistra"]."',NOW(),'".$_POST["IDUsuarioRegistra"]."') ";
			 $dbo->query($sql_insert);
			 $mesaje_registro="<span style='color:#627A54'>Registro Exitoso</span>";

	}



	}

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
									Carga Base Cartera
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->


								<div class="row">
									<div class="col-sm-12">

										<?php if($_GET["Tipo"]=="Propietario"){ ?>
										<div  class="form-group first ">

											<div  class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Otorgar a </label>

													<div class="col-sm-8">
														<input type="text" id="Accion" name="Accion" placeholder="Accion Nombre Apellido Numero Documento" class="col-xs-12 mandatory autocomplete-ajax-votante" title="Accion" value="<?php echo utf8_encode($r_socio["Nombre"] . $label_accion) ?>" >
														<input type="hidden" name="IDVotacionVotante" value="" id="IDVotacionVotante" class="mandatory" title="Socio">

													</div>
											</div>



										</div>

										<br><br>
										<div class="clearfix form-actions">
											<div class="col-xs-12 text-center">
											  <input type="hidden" name="IDVotadorPadre"  id="IDVotadorPadre" value="<?php echo $_GET["IDVotantePadre"] ?>" />
												<input type="hidden" name="IDClub"  id="IDClub" value="<?php echo $_GET["IDClub"] ?>" />
												<input type="hidden" name="IDVotacionEvento"  id="IDVotacionEvento" value="<?php echo $_GET["IDVotacionEvento"] ?>" />
												<input type="hidden" name="IDUsuarioRegistra"  id="IDUsuarioRegistra" value="<?php echo SIMUser::get("IDUsuario") ?>" />


												<button class="btn btn-info" id="btnRegistroPoder" type="button" rel="btnRegistroPoder" >
													<i class="ace-icon fa fa-check bigger-110"></i>
													Registrar poder
												</button>

												<div name='msgguardar' id='msgguardar'></div>



											</div>
										</div>

									<?php } else { ?>

									<form class="form-horizontal formvalida" role="form" method="post" id="frmExterno" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
										<div  class="form-group first ">

											<div  class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento </label>

													<div class="col-sm-8">
														<input type="number" id="NumeroDocumento" name="NumeroDocumento" placeholder="NumeroDocumento" class="col-xs-12 mandatory" title="NumeroDocumento" value="" >
													</div>
											</div>

											<div  class="col-xs-12 col-sm-6">
													<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

													<div class="col-sm-8">
														<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" >
													</div>
											</div>

										</div>

										<div class="clearfix form-actions">
											<div class="col-xs-12 text-center">
											  <input type="hidden" name="IDVotadorPadre"  id="IDVotadorPadre" value="<?php echo $_GET["IDVotantePadre"] ?>" />
												<input type="hidden" name="IDClub"  id="IDClub" value="<?php echo $_GET["IDClub"] ?>" />
												<input type="hidden" name="IDVotacionEvento"  id="IDVotacionEvento" value="<?php echo $_GET["IDVotacionEvento"] ?>" />
												<input type="hidden" name="IDUsuarioRegistra"  id="IDUsuarioRegistra" value="<?php echo SIMUser::get("IDUsuario") ?>" />


												<button class="btn btn-info btnEnviar" type="button" rel="frmExterno" >
													<i class="ace-icon fa fa-check bigger-110"></i>
													Registrar poder
												</button>

												<div name='msgguardar' id='msgguardar'><?php echo $mesaje_registro;  ?></div>



											</div>
										</div>
									</form>

									<?php } ?>





									</div><!-- /.col -->


								</div><!-- /.row -->

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<?
				include("cmp/footer_scripts.php");
				include("cmp/footer.php");

			?>
		</div><!-- /.main-container -->


	</body>
</html>
