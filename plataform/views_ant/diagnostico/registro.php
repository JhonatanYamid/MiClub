<form name="frmexportapqr" id="frmexportapqrSocio" method="post" enctype="multipart/form-data" action="procedures/excel-diagnostico-respuesta.php">
	<table>
		<tr>
			<td><input type="text" id="FechaInicioSocio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>"></td>
			<td><input type="text" id="FechaFinSocio" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d")  ?>"></td>
			<td>
				<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
				<input type="hidden" name="IDPerfil" id="IDPerfil" value="<?php echo SIMUser::get("IDPerfil"); ?>">
				<input type="hidden" name="IDDiagnostico" id="IDDiagnostico" value="<?php echo $frm[$key]; ?>">
				<input class="btn btn-info" type="button" name="exppqr" id="exppqr" value="<?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?>">
				<!-- <a href="procedures/excel-pqr.php?IDClub=<?php echo SIMUser::get("club"); ?>&IDUsuario=<?php echo SIMUser::get("IDUsuario"); ?>&IDPerfil=<?php echo SIMUser::get("IDPerfil"); ?>"><img src="assets/img/xls.gif" >Exportar</a>-->
			</td>
		<tr>
	</table>
</form>