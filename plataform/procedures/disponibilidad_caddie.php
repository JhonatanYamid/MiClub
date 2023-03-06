<?php

$ids = SIMNet::req("ids");


$action = SIMNet::req("action");
switch ($action) {


	case 'insert':
		$frm = SIMUtil::varsLOG($_POST);

		// Inserto el encabezado
		$id_disponibilidad = $dbo->insert($frm, "CaddieDisponibilidad", "IDCaddieDisponibilidad");

		//Inserto el detalle
		foreach ($frm["IDDia"] as $Dia_seleccion) :
			$array_dia[] = $Dia_seleccion;
		endforeach;
		if (count($array_dia) > 0) :
			$id_dia = implode("|", $array_dia) . "|";;
		endif;
		$frm["IDDia"] = $id_dia;
		$frm["IDCaddieDisponibilidad"] = $id_disponibilidad;
		//Caddiees
		foreach ($frm["IDCaddie"] as $IDCaddie) :
			$array_Caddie[] = $IDCaddie;
		endforeach;
		if (count($array_Caddie) > 0) :
			$ID_Caddie = implode("|", $array_Caddie) . "|";
		endif;
		$frm["IDCaddie"] = $ID_Caddie;

		for ($contador_horas = 1; $contador_horas <= $frm["contador_horas"]; $contador_horas++) :
			$campos_intervalo = "Intervalo" .	$contador_horas;
			$campos_desde = "HoraDesde" .	$contador_horas;
			$campos_hasta = "HoraHasta" .	$contador_horas;
			if (!empty($frm[$campos_desde]) && !empty($frm[$campos_hasta])) :
				$campos_intervalo = "Intervalo" .	$contador_horas;
				$frm["HoraDesde"] = $frm[$campos_desde];
				$frm["HoraHasta"] = $frm[$campos_hasta];
				$id = $dbo->insert($frm, "CaddieDisponibilidadDetalle", "IDCaddieDisponibilidadDetalle");
			endif;
		endfor;

?>
		<script>
			window.top.location.href = "serviciosclub.php?action=edit&tab=Caddie&ids=<?php echo $frm[IDServicio] ?>";
		</script>
	<?php
		SIMNotify::capture("La disponibilidad se ha creado correctamente", "info alert-success");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'DisponibilidadCreada', LANGSESSION));
		//SIMHTML::jsRedirect( "serviciosclub.php?action=edit&tab=disponibilidad&ids=".$frm[IDServicio] );
		exit;


		break;


	case "update":
		$frm = SIMUtil::varsLOG($_POST);

		// Actualizo encabezado
		$dbo->update($frm, "CaddieDisponibilidad", "IDCaddieDisponibilidad", $frm[IDCaddieDisponibilidad]);

		//Actualizo el detalle
		foreach ($frm["IDDia"] as $Dia_seleccion) :
			$array_dia[] = $Dia_seleccion;
		endforeach;

		if (count($array_dia) > 0) :
			$id_dia = implode("|", $array_dia) . "|";
		endif;
		$frm["IDDia"] = $id_dia;

		//Caddiees
		foreach ($frm["IDCaddie"] as $IDCaddie) :
			$array_Caddie[] = $IDCaddie;
		endforeach;
		if (count($array_Caddie) > 0) :
			$ID_Caddie = implode("|", $array_Caddie) . "|";
		endif;
		$frm["IDCaddie"] = $ID_Caddie;


		//Borro anterior
		$dbo->query("Delete From CaddieDisponibilidadDetalle Where IDCaddieDisponibilidad  = '" . $frm[IDCaddieDisponibilidad] . "'");
		for ($contador_horas = 1; $contador_horas <= $frm["contador_horas"]; $contador_horas++) :
			$campos_intervalo = "Intervalo" .	$contador_horas;
			$campos_desde = "HoraDesde" .	$contador_horas;
			$campos_hasta = "HoraHasta" .	$contador_horas;
			if (!empty($frm[$campos_desde]) && !empty($frm[$campos_hasta])) :
				$frm["Intervalo"] = $frm[$campos_intervalo];
				$frm["HoraDesde"] = $frm[$campos_desde];
				$frm["HoraHasta"] = $frm[$campos_hasta];
				$id = $dbo->insert($frm, "CaddieDisponibilidadDetalle", "IDCaddieDisponibilidadDetalle");
			endif;
		endfor;

	?>
		<script>
			window.top.location.href = "serviciosclub.php?action=edit&tab=Caddiees&ids=<?php echo $frm[IDServicio] ?>";
		</script>
<?php
		SIMNotify::capture("La disponibilidad se ha creado correctamente", "info alert-success");
		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Disponibilidadactualizada', LANGSESSION));
		//SIMHTML::jsRedirect( "serviciosclub.php?action=edit&tab=disponibilidad&ids=".$frm[IDServicio] );
		exit;

		break;
} //end switch



if (empty($view))
	$view = "views/disponibilidad_caddie/form.php";




?>