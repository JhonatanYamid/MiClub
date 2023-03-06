<div id="timeline-1">
	<a href="procedures/excel-candidatos.php?IDOferta=<?php echo  $frm[$key]; ?>"><img src="assets/img/xls.gif">Exportar</a>
	<div class="row">
		<div class="col-xs-12 col-sm-10 col-sm-offset-1">
			<div class="timeline-container">
				<div class="timeline-label">
					<span class="label label-primary arrowed-in-right label-lg">
						<b><?= SIMUtil::get_traduccion('', '', 'candidatos', LANGSESSION); ?></b>
					</span>
				</div>

				<?
				$sql_candidato = "SELECT * FROM OfertaCandidato WHERE IDOferta = '" . $_GET[id] . "' Order By IDOfertaCandidato Desc";
				$qry_candidato = $dbo->query($sql_candidato);
				while ($row_candidato = $dbo->object($qry_candidato)) {
					$candidatos[$row_candidato->IDOfertaCandidato] = $row_candidato;
				}
				$datos_club = $dbo->fetchAll("Club", " IDClub = '" . SIMUser::get("club") . "' ", "array");
				if (isset($candidatos)) : ?>
					<?php foreach ($candidatos as $detalle) : ?>


						<div class="timeline-items">
							<div class="timeline-item clearfix">
								<div class="timeline-info">

									<img alt="<?php echo $datos_club[Nombre]; ?>" src="assets/avatars/avatar2.png" />


									<span class="label label-info label-sm"><?php echo substr($detalle->FechaTrCr, 10); ?></span>
								</div>

								<div class="widget-box transparent">
									<div class="widget-header widget-header-small">
										<h5 class="widget-title smaller">
											<a href="#" class="blue">
												<?php

												$nombre_cliente = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $detalle->IDUsuario . "'");

												echo "(Funcionario) " . (isset($nombre_cliente) ? $nombre_cliente  : '<em>N/A</em>');;

												?>
											</a>

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

									<div class="widget-body">
										<div class="widget-main">
											<?= SIMUtil::get_traduccion('', '', 'Recomendado', LANGSESSION); ?>: <?php echo utf8_encode($detalle->NombreRecomendado); ?><br>
											<?= SIMUtil::get_traduccion('', '', 'CargoActual', LANGSESSION); ?>: <?php echo utf8_encode($detalle->CargoActual); ?><br>
											<?= SIMUtil::get_traduccion('', '', 'RazonPostulacion', LANGSESSION); ?> : <?php echo utf8_encode($detalle->RazonPostulacion); ?><br>
											<?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?>: <?php echo utf8_encode($detalle->Telefono); ?><br>
											<?= SIMUtil::get_traduccion('', '', 'CorreoElectronico', LANGSESSION); ?>: <?php echo utf8_encode($detalle->CorreoElectronico); ?><br>
											<?= SIMUtil::get_traduccion('', '', 'Hojadevida', LANGSESSION); ?>:
											<?php
											if (!empty($detalle->Archivo)) { ?>
												<a target="_blank" href="<?php echo OFERTA_ROOT . $detalle->Archivo; ?>"><?= SIMUtil::get_traduccion('', '', 'Clicparaverarchivo', LANGSESSION); ?></a>
											<?php
											} else {
												echo "Sin archivo";
											}
											?>


											<div class="space-6"></div>
											<div class="widget-toolbox clearfix">
											</div>
										</div>
									</div>
									<div>
										<form class="form-horizontal formvalida" role="form" method="post" id="frmofertacandidatos" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

											<div class="form-group col-md-4">
												<label class="col-sm-4 control-label" for="Estado"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>
												<div class="col-sm-8">
													<select id="Estado" class="form-control buscador-selector" name="Estado" title="Estado" required>
														<option value=""></option>
														<option value="Aprobado" <?php if ($detalle->Estado == "Aprobado") echo "selected"; ?>>Aprobado</option>
														<option value="En Proceso" <?php if ($detalle->Estado == "En Proceso") echo "selected"; ?>>En Proceso</option>
														<option value="Finalizado" <?php if ($detalle->Estado == "Finalizado") echo "selected"; ?>>Finalizado</option>


													</select>
												</div>
											</div>
											<div class="form-group col-md-4">
												<input type="hidden" name="IDOfertaCandidato" value="<?php echo $_GET['id'] ?>">
												<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
												<input type="hidden" name="IDClub" id="ID" value="<?php echo $frm["IDClub"] ?>" />
												<input type="hidden" name="IDUsuario" id="IDUsuario" value="<?php echo $detalle->IDUsuario ?>">
												<input type="hidden" name="IDOfertaCandidato" id="IDOfertaCandidato" value="<?php echo $detalle->IDOfertaCandidato ?>">
												<input type="hidden" name="action" id="action" value="actualizarcandidato">

												<input class="btn btn-info btnEnviar" type="submit" rel="frmofertacandidatos" id="enviarcandidatos" name="enviarcandidatos" value="<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>">
												<!--<i class="ace-icon fa fa-check bigger-110"></i>-->


											</div>
										</form>
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