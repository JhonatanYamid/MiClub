<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

			<div class="col-sm-8">
				<!--
                                          <select name = "IDSocio" id="IDSocio" <?php if ($_GET["action"] != "add") echo "disabled"; ?>>
										    <option value=""></option>
										    <?php
											$sql_socio_club = "Select * From Socio Where IDClub = '" . SIMUser::get("club") . "' Order by Apellido Asc";
											$qry_socio_club = $dbo->query($sql_socio_club);
											while ($r_socio = $dbo->fetchArray($qry_socio_club)) : ?>
										    <option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if ($r_socio["IDSocio"] == $frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]); ?></option>
										    <?php
											endwhile;    ?>
									      </select>
                                          -->
				<?php
				$sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
				$qry_socio_club = $dbo->query($sql_socio_club);
				$r_socio = $dbo->fetchArray($qry_socio_club); ?>

				<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo $r_socio["Apellido"] . " " . $r_socio["Nombre"] ?>">
				<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Categoria </label>

			<div class="col-sm-8"><?php echo SIMHTML::formPopUp("SeccionClasificados", "Nombre", "Nombre", "IDSeccionClasificados", $frm["IDSeccionClasificados"], "[Seleccione categoria", "popup mandatory", "title = \"Categoria\"", " and IDClub = '" . SIMUser::get("club") . "'") ?></div>
		</div>

	</div>







	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
			<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion</label>

			<div class="col-sm-8">
				<textarea rows="5" cols="50" id="Descripcion" name="Descripcion" class="input"><?php echo $frm["Descripcion"] ?></textarea>
			</div>
		</div>

	</div>

	<!-- 	<div class="form-group first ">

		Descripcion
		<div class="col-sm-12"> -->
	<!--<textarea rows="2" cols="50" id="TextoCorreoSocio" title="Texto Correo Socio" name="TextoCorreoSocio" class="input"><?php echo $frm["TextoCorreoSocio"] ?></textarea>-->
	<!-- 			<?php
						$oCuerpo = new FCKeditor("Descripcion");
						$oCuerpo->BasePath = "js/fckeditor/";
						$oCuerpo->Height = 400;

						//$oCuerpo->EnterMode = "p";
						$oCuerpo->Value =  $frm["Descripcion"];
						$oCuerpo->Create();
						?>
 -->

	<!-- 
		</div>

	</div> -->

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono </label>
			<div class="col-sm-8"><input type="number" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 mandatory" title="Telefono" value="<?php echo $frm["Telefono"]; ?>"></div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>
			<div class="col-sm-8"><input type="text" id="Email" name="Email" placeholder="Email" class="col-xs-12 mandatory" title="Email" value="<?php echo $frm["Email"]; ?>"></div>
		</div>
	</div>

	<div class="form-group first ">

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>

			<div class="col-sm-8">
				<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>">
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>

			<div class="col-sm-8">
				<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo $frm["FechaFin"] ?>">
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor </label>
			<div class="col-sm-8"><input type="number" id="Valor" name="Valor" placeholder="Valor" class="col-xs-12 " title="Valor" value="<?php echo $frm["Valor"]; ?>"></div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado </label>
			<?php echo SIMHTML::formPopUp("EstadoClasificado", "Nombre", "Nombre", "IDEstadoClasificado", $frm["IDEstadoClasificado"], "[Seleccione estado", "popup mandatory", "title = \"Estado\"") ?>
		</div>
	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar Notificación ? </label>

			<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?></div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Whatsapp </label>
			<div class="col-sm-8"><input type="text" id="Whatsapp" name="Whatsapp" placeholder="Whatsapp" class="col-xs-12 " title="Whatsapp" value="<?php echo $frm["Whatsapp"]; ?>"></div>
		</div>
	</div>




	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 1 </label>
			<input name="Foto1" id=file class="" title="Foto1" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto1"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto1"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 2 </label>
			<input name="Foto2" id=file class="" title="Foto2" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto2"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto2"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto2]&campo=Foto2&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 3</label>
			<input name="Foto3" id=file class="" title="Foto3" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto3"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto3"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto3]&campo=Foto3&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 4</label>
			<input name="Foto4" id=file class="" title="Foto4" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto4"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto4"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto4]&campo=Foto4&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

	</div>

	<div class="form-group first ">
		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 5</label>
			<input name="Foto5" id=file class="" title="Foto5" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto5"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto5"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto5]&campo=Foto5&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 6</label>
			<input name="Foto6" id=file class="" title="Foto6" type="file" size="25" style="font-size: 10px">
			<div class="col-sm-8">
				<? if (!empty($frm["Foto6"])) {
					echo "<img src='" . CLASIFICADOS_ROOT . $frm["Foto6"] . "' >";
				?>
					<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto6]&campo=Foto6&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
				<?
				} // END if
				?>
			</div>
		</div>

	</div>



	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
			<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																	else echo $frm["IDClub"];  ?>" />
			<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
				<i class="ace-icon fa fa-check bigger-110"></i>
				<?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
			</button>
			<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
			<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
		</div>
	</div>
</form>