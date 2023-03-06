<?php
if ($_GET["IDTipoBusqueda"] == 1 && $Nombre <> ""):
	echo "<h1>" .  SIMUtil::get_traduccion('', '', 'Nombredelempleadoes', LANGSESSION) . ": " . $Nombre . "</h1>" 
?>
<h3>Tiene aún <?= $CantidadEntradas; ?> Tiquetes Disponibles</h3>
	<section class="principal">
		<section class="formulario1">
			<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

				<div class="form-group first ">
					<div class="clearfix form-actions">
						<div class="col-xs-12 text-center">
							<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
							<input type="hidden" name="action" id="action" value="usarTiquete" />
							<input type="hidden" name="Cedula" id="Cedula" value="<?php echo $Cedula ?>" />
							<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																					else echo $frm["IDClub"];  ?>" />
							<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
								<i class="ace-icon fa fa-check bigger-110"></i>
								Usar tiquete
								<!-- <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?> -->
							</button>

						</div>
					</div>
				</div>

			</form>
		</section>
	</section>
<?php
else:
	echo '<h1>Por favor ingrese un número de documento</h1>';
endif;
	include("cmp/footer_grid.php");
	?>