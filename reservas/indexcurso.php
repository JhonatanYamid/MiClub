<?php
require("../admin/config.inc.php");
include("procedures/login.php");


if ($_GET["IDClub"] && $_GET["IDClub"] == 249) {
	include("cmp/seocotopaxi.php");
} else {
	include("cmp/seoliga.php");
}
// Si la peticion viene por app realizo el login automatico
if ($_GET["TipoApp"] = "App" && $_GET["ClaveApp"] == "Ingres04pp$" && !empty($_GET["IDSocio"]) && !empty($_GET["IDClub"])) {
	$simsession_socio = new SIMSession(SESSION_LIMIT);
	$link_index = "indexcurso.php";
	$cliente_data = $dbo->fetchAll("Socio", "IDSocio = '" . $_GET["IDSocio"] . "'", "object");
	$simsession_socio->clean_cliente();
	if ($cliente_data) {
		$usuariosave = addslashes(serialize($cliente_data));
		if ($simsession_socio->crear_cliente($cliente_data->IDSocio, $usuariosave)) {		//si el usuario es club crea la sesion del club
			$_SESSION["club"] = $cliente_data->IDClub;
			header("location: buscadorcursomobile.php");
		} else {
			header("location: " . $link_index . "?msg=noexiste&IDClub=" . $_POST["IDClub"]);
		}
	} else {
		header("location: " . $link_index . "?msg=noexiste&IDClub=" . $_POST["IDClub"]);
	}
}
//Fin login automatico


if (empty($_GET["IDClub"])) :
	echo "Club desconocido";
	exit;
endif;


?>



</head>

<body>

	<div id="cont_general">
		<?php
		if ($_GET["IDClub"] && $_GET["IDClub"] == 249) {
			include("cmp/menucotopaxi.php");
		} else {
			include("cmp/menuliga.php");
		}


		?>
		<div id="cuerpo">

			<div class="cont_central">

				<div id="titulos_internas">INGRESO</div>

				<?php if ($_GET["msg"] == "noexiste") : ?>
					<div id="titulos_internas">Datos incorrectos por favor verifique</div>
				<?php endif; ?>

				<div id="txt_internas">






					<form name="frmGeneral" id="frmGeneral" method="post" action="validausuario.php" class="formvalida">

						<input type="hidden" name="form" value="formContacto">
						<input type="text" style="display:none;" name="xvar">
						<div class="cont_1_form_pie">
							<label class="etiqueta_form_vive_interna">Usuario</label>
							<input type="text" name="Usuario" class="campo_form_pie" />
						</div>
						<div class="cont_1_form_pie">
							<label class="etiqueta_form_vive_interna">Clave</label>
							<input type="password" name="Password" class="campo_form_pie" />
						</div>

						<input type="submit" class="enviar_contacto" id="enviar_contacto" />
						<input type="hidden" name="action" value="Iniciar">
						<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $_GET["IDClub"]; ?>">
					</form>


				</div>


			</div>




			<?php
			if ($_GET["IDClub"] && $_GET["IDClub"] == 249) {
				include("cmp/footercotopaxi.php");
			} else {
				include("cmp/footerliga.php");
			}
			?>
		</div>