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
					<div class="tabbable">
						<ul class="nav nav-tabs" id="myTab">
							<li class="<?php if(empty($_GET[tabsocio])) echo "active"; ?>">
								<a data-toggle="tab" href="#home">
									<i class="green ace-icon fa fa-pencil-square-o bigger-120"></i>
									Carga Individual
								</a>
							</li>
							<?php if(SIMReg::get("club") == 8 || SIMReg::get("club") == 12){?>
								<li class="<?php if($_GET[tabsocio]=="cargaplanosocio") echo "active"; ?>">
									<a data-toggle="tab" href="#cargaplanosocio">
										<i class="green ace-icon fa fa-cloud-upload bigger-120"></i>
										Subir Lote	
									</a>
								</li>
							<?php } ?>							
						</ul>

						<div class="tab-content">
							<div id="home" class="tab-pane fade <?php if(empty($_GET[tabsocio])) echo "in active"; ?> ">
								<?php 
									include ("cargaIndividual.php"); 	
								?>
							</div>

							<div id="cargaplanosocio" class="tab-pane fade <?php if($_GET[tabsocio]=="cargaplanosocio") echo "in active"; ?> ">
								<?php 
									include ("cargaPermisoHandicap.php"); 
								?>
							</div>		
						</div>
					</div>					
				</div>
			</div>			
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>