<?
	require( "admin/config.inc.php" );
	include( "plataform/procedures/actualizasociocolombia.php" );
	SIMUtil::cache();
	session_start();
	unset($_SESSION["validcaptcha"]);

	include( "cmp/seo.php" );

?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<style>
.slide_bijao {
  background:url("../img/bannercfoto.jpg") center center no-repeat;
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
			<h2 class="wow fadeInDown"><a name="contacto" style="color:#5D9732">Actualizacion de Datos</a></h2>
			<p class="wow fadeInUp" data-wow-delay="0.1s">Por favor ingrese los siguientes datos.
			</p>
		</header>
		<form class="row formvalida" method="post" action="<?php echo SIMUtil::lastURI()?>">
			<div class="form-group col-md-6">
					<!-- <input name="RelacionSocio" type="text" placeholder="Relacion con el Socio" class="form-control mandatory" title="Relacion con el Socio" required />-->
					<select name="RelacionSocio" id="RelacionSocio" class="form-control mandatory" style="height:55px;line-height:55;background:#f4f4f4;">
						  <option value="">Tipo</option>
							<option value="TITULAR">TITULAR</option>
							<option value="CONYUGE">CÓNYUGE DE TITULAR</option>
							<option value="HERMANA">HERMANA DE TITULAR</option>
							<option value="HIJO">HIJO(A) DE TITULAR</option>
							<option value="MADRE">MADRE DE TITULAR</option>
					</select>
			</div>
			<div class="form-group col-md-6">
				<input name="Nombre" type="text" placeholder="Nombre Completo" class="form-control mandatory" title="Nombre Completo" required />
			</div>
			<div class="form-group col-md-6">
				<input name="NumeroDocumento" type="text" placeholder="Numero Documento" class="form-control mandatory" title="Numero Documento" required />
			</div>

			<div class="form-group col-md-6">
				<input name="Email" type="email" placeholder="Correo Electronico" class="form-control mandatory" title="Correo Electronico" required />
			</div>
			<div class="form-group col-md-6">
				<input name="Direccion" type="text" placeholder="Direccion" class="form-control mandatory" title="Direccion" required />
			</div>

      <div class="form-group col-md-6">
				<input name="Celular" type="text" placeholder="Celular" class="form-control mandatory" title="Celular" required />
			</div>
			<div class="form-group col-md-6">
				<input name="TelefonoFijo" type="text" placeholder="Telefono Fijo" class="form-control" title="TelefonoFijo" required />
			</div>
      <div class="form-group col-md-6">
				<input name="RedesSociales" type="text" placeholder="Redes Sociales" class="form-control" title="Redes Sociales"/>
			</div>
			<div class="form-group col-md-6">
				Fecha Nacimiento<input name="FechaNacimiento" type="date" placeholder="Fecha Nacimiento" class="form-control" title="Fecha Nacimiento" required />
			</div>
			<div class="form-group col-md-12">
				<textarea name="Comentario" class="form-control" rows="3" placeholder="Mensaje" title="Mensaje"></textarea>
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
					<span> (57-2) 608 76 00</span>
				</div>
				<div class="col-md-4 text-center wow zoomIn" data-wow-delay="0.2s">
					<i class="fa fa-envelope circled" style="background-color:#5D9732"></i>
					<span>gersecre@clubcolombia.org</span>
				</div>
				<div class="col-md-4 text-center wow zoomIn" data-wow-delay="0.4s">
					<i class="fa fa-globe circled" style="background-color:#5D9732"></i>
					<span>Avenida 3a Norte # 16N-23</span>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End contact section -->
<?
	include( "cmp/footercolombia.php" );
?>
</body>
</html>
