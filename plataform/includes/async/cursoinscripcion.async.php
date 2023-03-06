<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();



$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "CursoInscripcion";
$key = "IDCursoInscripcion";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' ";
$script = "cursoinscripcion";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
	$oper = "search";

if (!empty($_GET["Socio"])) {
	$sql_busq = "SELECT * From Socio Where (Nombre LIKE '%" . $_GET["Socio"] . "%' or apellido like '%" . $_GET["Socio"] . "%') and IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_socio[] = $row_busq["IDSocio"];
	endwhile;
	if (count($array_id_socio) > 0) :
		$id_socios_buscar = implode(",", $array_id_socio);
	else :
		$id_socios_buscar = 0;
	endif;
	$where .= " AND   IDSocio in (" . $id_socios_buscar . ")";
}

if (!empty($_GET["Documento"])) {
	$sql_busq = "SELECT * From Socio Where NumeroDocumento = '" . $_GET["Documento"] . "' and IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_socio[] = $row_busq["IDSocio"];
	endwhile;
	if (count($array_id_socio) > 0) :
		$id_socios_buscar = implode(",", $array_id_socio);
	else :
		$id_socios_buscar = 0;
	endif;
	$where .= " AND   IDSocio in (" . $id_socios_buscar . ")";
}

if (!empty($_GET["IDCursoNivel"])) {
	$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoNivel CN, CursoHorario CH
							 Where CN.IDCursoNivel=CH.IDCursoNivel
							 AND CN.IDCursoNivel = '" . $_GET["IDCursoNivel"] . "' and CH.IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_horario[] = $row_busq["IDCursoHorario"];
	endwhile;
	if (count($array_id_horario) > 0) :
		$id_horario_buscar = implode(",", $array_id_horario);
	else :
		$id_horario_buscar = 0;
	endif;
	$where .= " AND   IDCursoHorario in (" . $id_horario_buscar . ")";
}

if (!empty($_GET["Curso"])) {
	$sql_busq = "SELECT * From CursoHorario Where Nombre LIKE '%" . $_GET["Curso"] . "%' and IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_horario[] = $row_busq["IDCursoHorario"];
	endwhile;
	if (count($array_id_horario) > 0) :
		$id_horario_buscar = implode(",", $array_id_horario);
	else :
		$id_horario_buscar = 0;
	endif;
	$where .= " AND   IDCursoHorario in (" . $id_horario_buscar . ")";
}

if (!empty($_GET["IDCursoSede"])) {
	$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoSede CS, CursoHorario CH
							 Where CS.IDCursoSede=CH.IDCursoSede
							 AND CS.IDCursoSede = '" . $_GET["IDCursoSede"] . "' and CH.IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_horario[] = $row_busq["IDCursoHorario"];
	endwhile;
	if (count($array_id_horario) > 0) :
		$id_horario_buscar = implode(",", $array_id_horario);
	else :
		$id_horario_buscar = 0;
	endif;
	$where .= " AND   IDCursoHorario in (" . $id_horario_buscar . ")";
}

if (!empty($_GET["IDCursoTipo"])) {
	$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoTipo CT, CursoHorario CH
							 Where CT.IDCursoTipo=CH.IDCursoTipo
							 AND CT.IDCursoTipo = '" . $_GET["IDCursoTipo"] . "' and CH.IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_horario[] = $row_busq["IDCursoHorario"];
	endwhile;
	if (count($array_id_horario) > 0) :
		$id_horario_buscar = implode(",", $array_id_horario);
	else :
		$id_horario_buscar = 0;
	endif;
	$where .= " AND   IDCursoHorario in (" . $id_horario_buscar . ")";
}

if (!empty($_GET["FechaInicio"])) {
	$sql_busq = "SELECT DISTINCT(IDCursoCalendario) From CursoCalendario CC
							 Where CC.FechaInicio = '" . $_GET["FechaInicio"] . "' and CC.IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_calendario[] = $row_busq["IDCursoCalendario"];
	endwhile;
	if (count($array_id_calendario) > 0) :
		$id_calendario_buscar = implode(",", $array_id_calendario);
	else :
		$id_calendario_buscar = 0;
	endif;
	$where .= " AND   IDCursoCalendario in (" . $id_calendario_buscar . ")";
}

if (!empty($_GET["Hora"])) {
	$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoHorario CH
							 Where CH.HoraDesde = '" . $_GET["Hora"] . "' and CH.IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_horario[] = $row_busq["IDCursoHorario"];
	endwhile;
	if (count($array_id_horario) > 0) :
		$id_horario_buscar = implode(",", $array_id_horario);
	else :
		$id_horario_buscar = 0;
	endif;
	$where .= " AND   IDCursoHorario in (" . $id_horario_buscar . ")";
}

if (!empty($_GET["IDCursoEntrenador"])) {
	$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoEntrenador CE, CursoHorario CH
							 Where CE.IDCursoEntrenador=CH.IDCursoEntrenador
							AND CE.IDCursoEntrenador = '" . $_GET["IDCursoEntrenador"] . "' and CH.IDClub = '" . SIMUser::get("club") . "'";
	$result_busq = $dbo->query($sql_busq);
	while ($row_busq = $dbo->fetchArray($result_busq)) :
		$array_id_horario[] = $row_busq["IDCursoHorario"];
	endwhile;
	if (count($array_id_horario) > 0) :
		$id_horario_buscar = implode(",", $array_id_horario);
	else :
		$id_horario_buscar = 0;
	endif;
	$where .= " AND   IDCursoHorario in (" . $id_horario_buscar . ")";
}

if (!empty($_GET["FechaInicioExportar"]) && !empty($_GET["FechaFinExportar"])) {

	$where .= "  and CursoInscripcion.FechaTrCr  >= '" . $_GET["FechaInicioExportar"] . " 00:00:00'  and CursoInscripcion.FechaTrCr <= '" . $_GET["FechaFinExportar"] . " 23:59:59'";
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
$row = $dbo->fetchArray($result);
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


if (SIMUser::get("IDPerfil") <= 1) {
	$condicion_horario = "";
} else {
	$array_sede_usuario = explode("|", SIMUser::get("IDCursoSede"));
	foreach ($array_sede_usuario as $id_sede => $sede) {
		if (!empty($sede)) {
			$array_id_sede[] = $sede;
		}
	}
	if (count($array_id_sede) > 0)
		$id_consulta_sede = implode(",", $array_id_sede);
	else
		$id_consulta_sede = 0;

	$condicion_sede = " and IDCursoSede = '" . $id_consulta_sede . "' ";
	$sql_cursos_horario = "SELECT IDCursoHorario FROM CursoHorario WHERE IDCursoSede in (" . $id_consulta_sede . ")";
	$r_cursos_horario = $dbo->query($sql_cursos_horario);
	while ($row_cursos_horario = $dbo->fetchArray($r_cursos_horario)) {
		$array_id_horario[] = $row_cursos_horario["IDCursoHorario"];
	}
	if (count($array_id_horario) > 0)
		$condicion_horario = " and IDCursoHorario in ( " . implode(',', $array_id_horario) . ") ";
	else
		$condicion_horario = " and IDCursoHorario in ( 0 ) ";
}




$sql = "SELECT " . $table . ".* FROM " . $table . $where . $condicion_horario . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//exit;
//var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');


$sql_entrenador = "SELECT * FROM CursoEntrenador WHERE IDClub = '" . SIMUser::get("club") . "'";
$r_entrenador = $dbo->query($sql_entrenador);
while ($row_entrenador = $dbo->fetchArray($r_entrenador)) {
	$array_entrenador[$row_entrenador["IDCursoEntrenador"]] = $row_entrenador["Nombre"];
}

$sql_nivel = "SELECT * FROM CursoNivel WHERE IDClub = '" . SIMUser::get("club") . "'";
$r_nivel = $dbo->query($sql_nivel);
while ($row_nivel = $dbo->fetchArray($r_nivel)) {
	$array_nivel[$row_nivel["IDCursoNivel"]] = $row_nivel["Nombre"];
}

$sql_edad = "SELECT * FROM CursoEdad WHERE IDClub = '" . SIMUser::get("club") . "'";
$r_edad = $dbo->query($sql_edad);
while ($row_edad = $dbo->fetchArray($r_edad)) {
	$array_edad[$row_edad["IDCursoEdad"]] = $row_edad["Nombre"];
}

$sql_sede = "SELECT * FROM CursoSede WHERE IDClub = '" . SIMUser::get("club") . "'";
$r_sede = $dbo->query($sql_sede);
while ($row_sede = $dbo->fetchArray($r_sede)) {
	$array_sede[$row_sede["IDCursoSede"]] = $row_sede["Nombre"];
}

$sql_tipo = "SELECT * FROM CursoTipo WHERE IDClub = '" . SIMUser::get("club") . "'";
$r_tipo = $dbo->query($sql_tipo);
while ($row_tipo = $dbo->fetchArray($r_tipo)) {
	$array_tipo[$row_tipo["IDCursoTipo"]] = $row_tipo["Nombre"];
}

$sql_calendario = "SELECT * FROM CursoCalendario WHERE IDClub = '" . SIMUser::get("club") . "'";
$r_calendario = $dbo->query($sql_calendario);
while ($row_calendario = $dbo->fetchArray($r_calendario)) {
	$array_calendario[$row_calendario["IDCursoCalendario"]] = $row_calendario;
}


while ($row = $dbo->fetchArray($result)) {
	$responce->rows[$i]['id'] = $row[$key];

	$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row["IDSocio"] . "' ", "array");
	$datos_curso_horario = $dbo->fetchAll("CursoHorario", " IDCursoHorario = '" . $row["IDCursoHorario"] . "' ", "array");
	$datos_curso_calendario = $dbo->fetchAll("CursoCalendario", " IDCursoHorario = '" . $row["IDCursoHorario"] . "' ", "array");



	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if ($origen <> "mobile")
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			"Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"Numero" => $row["IDCursoInscripcion"],
			"Socio" => utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]),
			"Curso" => utf8_encode($datos_curso_horario["Nombre"] . "..."),
			"Nivel" => utf8_encode($array_nivel[$datos_curso_horario["IDCursoNivel"]]),
			"Sede" => $array_sede[$datos_curso_horario["IDCursoSede"]],
			"Dia" => $array_tipo[$datos_curso_horario["IDCursoTipo"]],
			"Hora" => $datos_curso_horario["HoraDesde"],
			"Entrenador" => $array_entrenador[$datos_curso_horario["IDCursoEntrenador"]],
			"FechaInicio" => $array_calendario[$row["IDCursoCalendario"]]["FechaInicio"],
			"EstadoInscripcion" => $row["EstadoInscripcion"],
			"Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
		);

	$i++;
}

echo json_encode($responce);
