<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor ?>

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


					<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">



							<div  class="form-group first">

                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre</label>

										<div class="col-sm-8">
											<input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $frm["Nombre"] ?>" />
										</div>
								</div>

							</div>


            <div  class="form-group first" style="padding-left: 300px;">
										<div class="col-sm-8" >
											<?php
												echo $frm["TextoCarta"];
											?>
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
