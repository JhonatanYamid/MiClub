<?
	include( "procedures/general.php" );
	include( "procedures/actualizacionHoras.php" );
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
									Actualizacion Horas
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->


								<div class="row">
									<div class="col-sm-12">




										<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlano" name="frmSocioPlano" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

 <table id="simple-table" class="table table-striped table-bordered table-hover">
  <tr>
    <td> 
      <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
          <td colspan="2"> Estructura del Archivo </td>
          </tr>
        <tr>
          <td>1</td>
          <td>*Nombre del puesto</td>
        </tr>
        <tr>
          <td>2</td>
          <td>*Numero de cedula</td>
        </tr>
        <tr>
          <td>3</td>
          <td>*Apellidos y nombres</td>
        </tr>
    

      </table>
    </td>
    <td valign="top"> 
      <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
          <td>Archivo Excel</td>
          <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
            <input type="hidden" name="action" id="action" value="cargarplano" />
            <input type="submit" class="submit" value="Cargar">
          </td>
        </tr>
      </table>
    </td>
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
				include("cmp/footer.php");
			?>
		</div><!-- /.main-container -->


	</body>
</html>
