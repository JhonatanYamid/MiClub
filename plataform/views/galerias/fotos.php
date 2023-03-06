<div id="GaleriaGaleria">
	<table class="adminform">
		<tr>
			<th>&nbsp;<?= SIMUtil::get_traduccion('', '', 'Galeria', LANGSESSION); ?> <?php echo $frm["Nombre"] ?></th>
		</tr>
		<tr>
			<td>
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td width="283">
							<input type="button" name="masfotos" value="<?= SIMUtil::get_traduccion('', '', 'AgregarImagenes', LANGSESSION); ?>" id="masfotos" class="submit" />


						</td>
						<td>
							<table width="100%" style="display:none;" id="CargarImg" border="0">
								<form name="frmFoto" action="<?php echo $PHP_SELF ?>" method="post" enctype="multipart/form-data" class="formvalida">
									<tr>


										<?
										$numcols = 2;
										$contador = 1;
										for ($i = 1; $i <= 10; $i++) {
										?>
											<td>
												<input type="file" name="fichero_<?= $i ?>" id="req" />
											</td>
										<?
											if ($contador % $numcols == 0) {
												echo "</tr><tr>";
												$contador = 0;
											} //end if
											$contador++;
										} //end if
										?>


									</tr>
									<tr>
										<td colspan="<?= $numcols ?>">
											<input type="hidden" name="ID" value="<?php echo $frm[$key] ?>" />
											<input type="hidden" name="action" value="Galeria" />
											<input type="submit" name="submit" value="Cargar Imagenes" class="submit" />
										</td>
									</tr>
								</form>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%">
					<?
					if ($array_fotos != Null) {
					?>
						<tr>
							<?
							$cont = 1;
							$modulo = 0;
							foreach ($array_fotos as $clave_fotos => $valor_fotos) {
							?>
								<td align="center" valign="middle" nowrap class=row<? if (($cont % 2) == 0) echo "1";
																					else echo "2"; ?>>
									<? if (!empty($valor_fotos["Foto"])) {

										if (strstr(strtolower($valor_fotos["Foto"]), "http://"))
											$ruta = $valor_fotos["Foto"];
										else
											$ruta = GALERIA_ROOT . $valor_fotos["Foto"];

										//$ruta= GALERIA_ROOT . $valor_fotos["Foto"];
										$tam = @getimagesize($ruta);
										$w = $tam[0] + 105;
										$h = $tam[1] + 130;
									?>
										<a href="javascript:;" onclick="PopupPic('<?= $ruta ?>','$w','$w')">
											<img src='<?= $ruta ?>?<?= rand(1, 100); ?>' width="300" border=0>
										</a>
										<a href="<? echo $script . ".php?action=delfotogaleria&id=$frm[$key]&IDFoto=$valor_fotos[IDFoto]" ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

									<?
									} // END if
									?>

									<table border="0" cellspacing="4" cellpadding="0">


										<tr>
											<td width=70><b><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>:</b></td>
											<td><? echo $valor_fotos["Nombre"] ?></td>
										</tr>
										<tr>
											<td width=70><b>Like:</b></td>
											<td><?
												$sql_likes = "SELECT count(IDGaleriaLike) as TotalLike FROM GaleriaLike WHERE Version='1' AND MeGusta='S' AND IDFoto='" . $valor_fotos["IDFoto"] . "'";
												$result_likes = $dbo->query($sql_likes);
												$row_likes = $dbo->fetchArray($result_likes);
												echo $row_likes["TotalLike"]; ?></td>
										</tr>

										<!-- 	<tr>
											<td width=70><b><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>:</b></td>
											<td><? echo $valor_fotos["Nombre"] ?></td>
										</tr> -->
										<tr>
											<td width=70><b><?= SIMUtil::get_traduccion('', '', 'Tamaño', LANGSESSION); ?>:</b></td>
											<td><? echo $valor_fotos["FotoSize"] ?></td>
										</tr>
										<tr>
											<td width=70><b><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?>:</b></td>
											<td><? echo $valor_fotos["FotoType"] ?></td>
										</tr>
										<tr>
											<td width=70><b><?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>:</b></td>
											<td><input type="number" name="Orden<?php echo $valor_fotos['IDFoto'] ?>" id="Orden<?php echo $valor_fotos['IDFoto'] ?>" value="<?php echo $valor_fotos["Orden"] ?>"> </td>
										</tr>
										<tr>
											<td width=70><b><?= SIMUtil::get_traduccion('', '', 'Texto', LANGSESSION); ?>:</b></td>
											<td><textarea name="Descripcion<?php echo $valor_fotos['IDFoto'] ?>" id="Descripcion<?php echo $valor_fotos['IDFoto'] ?>" rows="4" cols="20"><?php echo $valor_fotos["Descripcion"] ?></textarea>
											</td>
										</tr>
										<tr>

											<td colspan="2" align="center"><input type="submit" class="submit guardar_fotogaleria" value="<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>" alt="<?php echo $valor_fotos['IDFoto'] ?>" /></td>
										</tr>
									</table>


							<?
								if (($cont % 2) == 0)
									echo "</td></tr><tr>";
								$cont++;
							} //end for
						} //end if

							?>
						</tr>
				</table>
			</td>
		</tr>
	</table>
</div>