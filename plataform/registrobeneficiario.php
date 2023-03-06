<?
	require( "admin/config.inc.php" );
	//include( "plataform/procedures/home_site.php" );
	include( "plataform/procedures/registrobeneficiario.php" );
	SIMUtil::cache();
	session_start();
	unset($_SESSION["validcaptcha"]);

	include( "cmp/seo.php" );

?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<style>
.slide_bijao {
  background:url("../img/cabezotebijaof.jpg") center center no-repeat;
  color:#FFFFFF;
  text-align:center;
  text-transform:uppercase;
  height:300px;
}
</style>
</head>
<body>
	
    
	<div id="home" class="jumbotron slide_bijao">
			<div class="container">
			</div>
	</div>
    
	
<!-- Start  section -->
<section id="contact">
	<div class="container">
		<header>
			<h2 class="wow fadeInDown"><a name="contacto" style="color:#5D9732">Registro Beneficiarios</a></h2>
			<p class="wow fadeInUp" data-wow-delay="0.1s">Por favor ingrese los datos de los beneficiarios que desea sean registrados en el sistema, estos datos pasaran por la aprobación de la administración.
			</p>
		</header>
		<form class="row formvalida" method="post" action="<?php echo SIMUtil::lastURI()?>">
			<div class="form-group col-md-6">
				<input name="Nombre" type="text" placeholder="Nombre Beneficiario" class="form-control mandatory" title="Nombre" required />
			</div>
            <div class="form-group col-md-6">
				<input name="Apellido" type="text" placeholder="Apellido Beneficiario" class="form-control mandatory" title="Apellido" required />
			</div>
			<div class="form-group col-md-6">
				<input name="Email" type="email" placeholder="Email Propietario" class="form-control mandatory" title="Email" required />
			</div>
			<div class="form-group col-md-6">
				<input name="UnidadResidencial" type="subject" placeholder="Unidad Residencial" class="form-control mandatory" title="Asunto" required />
			</div>
            <div class="form-group col-md-6">
				<input name="Telefono" type="text" placeholder="Telefono Beneficiario" class="form-control mandatory" title="Telefono" required />
			</div>
            <div class="form-group col-md-6">
				<input name="Parentesco" type="text" placeholder="Parentesco con el Propietario" class="form-control mandatory" title="Parentesco" required />
			</div>
			<div class="form-group col-md-12">
				<textarea name="Comentario" class="form-control" rows="10" placeholder="Mensaje" title="Mensaje" required></textarea>
			</div>
            <div class="form-group col-md-12">
            	  
	             <div class="g-recaptcha" data-sitekey="6LdtvCYTAAAAAH7jrlCcYTDQPnrUyx6kIG2k6NWm"></div>
            </div>
			<div class="form-group col-md-12">
				<input type="hidden" name="action" value="insert">
				<button class="btn btn-lg btn-primary" style="background-color:#5D9732">Enviar</button>
			</div>
		</form>
		<div class="address">
			<div class="row">
				<div class="col-md-4 text-center wow zoomIn">
					<i class="fa fa-phone circled" style="background-color:#5D9732"></i>
					<span> (507) 993-2368</span>
				</div>
				<div class="col-md-4 text-center wow zoomIn" data-wow-delay="0.2s">
					<i class="fa fa-envelope circled" style="background-color:#5D9732"></i>
					<span>info@bijao.com</span>
				</div>
				<div class="col-md-4 text-center wow zoomIn" data-wow-delay="0.4s">
					<i class="fa fa-globe circled" style="background-color:#5D9732"></i>
					<span>Panamá</span>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End contact section -->
<?
	include( "cmp/footer.php" );
?>
</body>
</html>