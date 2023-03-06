<div id="timeline-1">
	<div class="row">
		<div class="col-xs-12 col-sm-10 col-sm-offset-1">
			<div class="timeline-container">
				<div class="timeline-label">
					<span class="label label-primary arrowed-in-right label-lg">
						<b><?= SIMUtil::get_traduccion('', '', 'Comentarios', LANGSESSION); ?></b>
					</span>
				</div>

				<?
				$sql_pregunta = "SELECT * FROM PostuladoComentario WHERE IDPostulado = '" . $_GET[id] . "' Order By IDPostuladoComentario Desc";
				$qry_pregunta = $dbo->query($sql_pregunta);
				while ($row_pregunta = $dbo->object($qry_pregunta)) {
					$preguntas[$row_pregunta->IDPostuladoComentario] = $row_pregunta;
				}
				$datos_club = $dbo->fetchAll("Club", " IDClub = '" . SIMUser::get("club") . "' ", "array");
				if (isset($preguntas)) : ?>
					<?php foreach ($preguntas as $detalle) : ?>


						<div class="timeline-items">
							<div class="timeline-item clearfix">
								<div class="timeline-info">

									<img alt="<?php echo $datos_club[Nombre]; ?>" src="assets/avatars/avatar2.png" />


									<span class="label label-info label-sm"><?php echo substr($detalle->FechaPregunta, 10); ?></span>
								</div>

								<div class="widget-box transparent">
									<div class="widget-header widget-header-small">
										<h5 class="widget-title smaller">
											<a href="#" class="blue">
												<?php
												$datos_socio_comen = $dbo->fetchAll("Socio", " IDSocio = '" . $detalle->IDSocio . "' ", "array");

												$nombre_cliente = $datos_socio_comen["Nombre"] . " " . $datos_socio_comen["Apellido"];
												echo "(socio) " . (isset($nombre_cliente) ? $nombre_cliente . " " . $apellido_cliente : '<em>N/A</em>');;

												?>
											</a>
											<span class="grey"><?php echo $detalle->Comentario; ?></span>
										</h5>

										<span class="widget-toolbar no-border">
											<i class="ace-icon fa fa-clock-o bigger-110"></i>
											<?php echo $detalle->FechaTrCr; ?>
										</span>

										<span class="widget-toolbar">
											<a href="#" data-action="collapse">
												<i class="ace-icon fa fa-chevron-up"></i>
											</a>
										</span>
									</div>
								</div>
							</div>





						</div><!-- /.timeline-items -->

				<?php
					endforeach;
				endif;
				?>
			</div><!-- /.timeline-container -->


		</div>
	</div>
</div>