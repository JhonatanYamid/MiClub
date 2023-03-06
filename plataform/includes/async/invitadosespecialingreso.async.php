<?php

include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "SocioInvitadoEspecial";
$key = "IDSocioInvitadoEspecial";
$where = $table . ".IDClub = '" . SIMUser::get("club") . "'  ";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
	$oper = "search";

switch ($oper) {



	case "search":

		$filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
		$array_buqueda = json_decode($filters);
		foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
			switch ($search_object->field) {
				case 'NumeroDocumento':
				case 'Nombre':

					$where .= " AND ( I.Nombre LIKE '%" . $search_object->data . "%' OR I.Apellido LIKE '%" . $search_object->data . "%' OR I.NumeroDocumento LIKE '%" . $search_object->data . "%' )  ";
					break;

				case 'FechaInicio':

					$where .= " AND FechaInicio <= '$search_object->data' AND FechaFin >= '$search_object->data'  ";
					$fecha_inicio = $search_object->data;
					break;

				default:
					$where .=  $array_buqueda->groupOp . "  SocioInvitadoEspecial." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
					break;
			}

			if ($search_object->field == "FechaInicio" && !empty($search_object->data)) :
				$fecha_inicio = $search_object->data;
			endif;
		} //end for




		break;

	case "searchurl":
		$accion = $_GET["Accion"];
		if (!empty($accion))
			$where .= " AND ( Invitado.NumeroDocumento = '" . $accion . "' OR Accion = '" . $accion . "' )  ";
		break;

	default:
		$where .= " AND FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  ";
		$fecha_inicio = date("Y-m-d");
		break;
}



$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = 'FechaInicio'; // get index row - i.e. user click to sort
$sord = "DESC"; // get the direction
if (!$sidx) $sidx = "FechaInicio";
// connect to the database


//$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
//$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
	$total_pages = ceil($count / $limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page = $total_pages;
$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit))
	$limit = 1000000;

$sql = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado, I.NumeroDocumento, CONCAT(S.Nombre,' ',S.Apellido) as Nombre FROM " . $table . " , Invitado I, Socio S WHERE SocioInvitadoEspecial.IDSocio = S.IDSocio AND I.IDInvitado = SocioInvitadoEspecial.IDInvitado AND $where  AND SocioInvitadoEspecial.Ingreso = 'S' AND Salida != 'S' " . $condicion_id_invitacion . "  ORDER BY $key $sord " . $str_limit;

$result = $dbo->query($sql);


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {


	$responce->rows[$i]['id'] = $row[$key];

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if ($origen <> "mobile") :

		//// Consulto las reglas que aplica al socio para invitaciones
		//$array_datos_regla = SIMUtil::consulta_regla_invitacion($row["IDSocio"],SIMUser::get("club"));

		//  $numero_invitados_mes_permitido = $array_datos_regla["MaximoInvitadoSocio"];
		//  $numero_invitados_dia_permitido = $array_datos_regla["MaximoInvitadoDia"];
		// $numero_mismo_invitado_mes = $array_datos_regla["MaximoRepeticionInvitado"];
		// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
		// $cumplimiento_obligatorio_limite = $array_datos_regla["CumplimientoInvitados"];


		/*
			$numero_invitados_mes_permitido = $dbo->getFields( "Club" , "MaximoInvitadoSocio" , "IDClub = '".SIMUser::get("club")."'" );
			$numero_mismo_invitado_mes = $dbo->getFields( "Club" , "MaximoRepeticionInvitado" , "IDClub = '".SIMUser::get("club")."'" );
			// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
			$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".SIMUser::get("club")."'" );
			*/





		//Consulto cuantas veces la persona ha sido invitada en el mes
		$mes_invitacion = date("m");
		$dia_invitacion = date("d");
		$year_invitacion = date("Y");
		$hoy_invitacion = date("Y-m-d");
		/*
			$sql_numero_invitacion = $dbo->query("Select * From SocioInvitadoEspecial Where NumeroDocumento = '".$row["NumeroDocumento"]."' and MONTH(FechaInicio) = '".$mes_invitacion."' and Year(FechaInicio) = '".$year_invitacion."'IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
			$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
			//Consulto cuantas personas ha invitado el socio en el mes
			$sql_invitados_mes = $dbo->query("Select * From SocioInvitadoEspecial Where IDSocio = '".$row["IDSocio"]."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
			$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
			//Consulto cuantas personas ha invitado el socio en el dia
			$sql_invitados_dia = $dbo->query("Select * From SocioInvitadoEspecial Where IDSocio = '".$row["IDSocio"]."' and FechaInicio = '".$hoy_invitacion."' and IDClub = '".SIMUser::get("club")."' and Estado = 'I'");
			$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
			*/

		// $observacion = "";
		// if ((int)$numero_invitaciones < (int)$numero_mismo_invitado_mes) {
		// 	if ((int)$numero_invitados_mes_permitido > (int)$numero_invitados_mes) {
		// 		if ((int)$numero_invitados_dia_permitido > (int)$numero_invitados_dia) {
		// 			$color_fila = "#000000";
		// 		} else {
		// 			$$color_fila = "#EE080C";
		// 			$observacion = "Supera el max. de:" . $numero_invitados_mes_permitido . " invitac. dia";
		// 		}
		// 	} else {
		// 		$color_fila = "#EE080C";
		// 		$observacion = "Supera el max. de:" . $numero_invitados_mes_permitido . " invitac. mes";
		// 	}
		// } else {
		// 	$color_fila = "#EE080C";
		// 	$observacion = "Invitado mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
		// }

		$color_fila = "#000000";
		$observacion = "";


		$boton_registro_ingreso = '<a class="green btn btn-primary btn-sm" href="#" id="btnrealizarsalida"><i class="ace-icon fa fa-pencil-square-o bigger-130"/></a>';
		//$boton_registro_ingreso="Salida: " . $row["FechaSalida"];


		// $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $row["IDInvitado"] . "' ", "array");
		// $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row["IDSocio"] . "' ", "array");
		//Averiguo la cantidad del grupo familiar
		$sql_grupo = "Select IDSocioInvitadoEspecial From SocioInvitadoEspecial Where IDClub= '" . SIMUser::get('club') . "' AND IDPadre = '" . $row["IDInvitado"] . "' and IDInvitado <> '" . $row["IDInvitado"] . "' and FechaInicio = '" . $row["FechaInicio"] . "'";
		$result_grupo = $dbo->query($sql_grupo);
		$row_result_grupo = $dbo->rows($result_grupo);
		// $sqlPlaca = "select Placa from VehiculoInvitacion where IDVehiculo = '" . $row["IDVehiculo"] . "' order by IDSocioInvitadoEspecial desc LIMIT 1";
		// $q_placa = $dbo->query($sqlPlaca);
		// $placa = $dbo->assoc($q_placa);


		$responce->rows[$i]['cell'] = array(
			"IDSocioInvitadoEspecial" => $row["IDSocioInvitadoEspecial"],
			"Ingreso" => $boton_registro_ingreso,
			"NumeroDocumento" => "<font color='$color_fila'>" . $row["NumeroDocumento"] . "</font>",
			"Nombre" => "<font color='$color_fila'>" . utf8_encode($row["NombreInvitado"]) . "</font>",
			"Tipo" => "<font color='$color_fila'>" . addslashes($row["TipoInvitacion"]) . "</font>",
			// "Placa" => "<font color='$color_fila'>" . strtoupper($placa['Placa']) . "</font>",
			"Cantidad" => "<font color='$color_fila'>" . $row_result_grupo . "</font>",
			"FechaInicio" => "<font color='$color_fila'>" . SIMUtil::tiempo($row["FechaInicio"]) . "</font>",
			"Socio" => "<font color='$color_fila'>" . utf8_encode($row['Nombre']) . "</font>"
		);
	endif;

	$i++;
}

echo json_encode($responce);
