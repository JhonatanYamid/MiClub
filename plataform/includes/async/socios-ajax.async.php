<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Socio";
$key = "IDSocio";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' AND IDEstadoSocio <> 2 ";
$script = "socios";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
	$oper = "search";

switch ($oper) {

	case "del":

		$sql_delete = "DELETE FROM Socio WHERE IDSocio = '" . $_POST["id"] . "' AND IDClub = '" . SIMUser::get("club") . "'  LIMIT 1";
		//echo "<br>";
		$qry_delete = $dbo->query($sql_delete);

		$_GET["page"] = 1;
		$_GET['rows'] = 100;
		$_GET['sidx'] = "Nombre ASC";
		$_GET['sord'] = "ASC";


		break;

	case "search":

		$filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
		$array_buqueda = json_decode($filters);
		foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
			switch ($search_object->field) {
				case 'qryString':
					$where .= " AND ( Socio.Nombre LIKE '%" . $search_object->data . "%' OR Socio.Apellido LIKE '%" . $search_object->data . "%' OR Socio.NumeroDocumento LIKE '" . $search_object->data . "%' OR Accion LIKE '" . $search_object->data . "%' OR Email LIKE '" . $search_object->data . "%' OR Predio LIKE '" . $search_object->data . "%' )  ";
					break;

				case "Estado":

					//busco el estado
					$sql_estado_pqr = "Select * From EstadoSocio Where Nombre = '" . $search_object->data . "'";
					$result_estado_pqr = $dbo->query($sql_estado_pqr);
					while ($row_estado_pqr = $dbo->fetchArray($result_estado_pqr)) :
						$array_id_estado[] = $row_estado_pqr["IDEstadoSocio"];
					endwhile;
					if (count($array_id_estado) > 0) :
						$id_estado_buscar = implode(",", $array_id_estado);
					else :
						$id_estado_buscar = 0;
					endif;

					$where .= " AND   IDEstadoSocio in (" . $id_estado_buscar . ")";

					break;

				default:
					$where .=  $array_buqueda->groupOp . "  Socio." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
					break;
			}
		} //end for




		break;

	case "searchurl":
		$qryString = SIMNet::req("qryString");
		$datos = explode(" ", $qryString);

		$idClub = SIMUser::get("club");
		$idPadre = SIMUtil::IdPadre($idClub);
		$clubHijos = SIMUtil::ObtenerHijosClubPadre($idPadre);

		$allSocios = $dbo->getFields("Club", "CargarSociosHijos", "IDClub = " . $idPadre);

		if ($allSocios == 'S' && !empty($clubHijos)) {
			$clubes = implode(',', array_values($clubHijos));
			$where = " WHERE IDClub in ($clubes,$idPadre) ";
		}


		// buscador socios
		if (count($datos) == 1) {
			$where .= " AND ( LOWER(Socio.Nombre) LIKE LOWER('%" . $datos[0] . "%') OR LOWER(Socio.Apellido) LIKE LOWER('%" . $datos[0] . "%') OR Socio.NumeroDocumento LIKE '" . $qryString . "%' OR Accion LIKE '" . $qryString . "%' OR Email LIKE '" . $qryString . "%' OR Predio LIKE '" . $qryString . "%'  OR AccionPadre LIKE '%" . $qryString . "%')";
		} else 
		if (count($datos) == 2) {
			$where .= " AND ( LOWER(Socio.Nombre)LIKE  LOWER('%" . $datos[0] . "%') AND LOWER(Socio.Apellido) LIKE LOWER('%" . $datos[1] . "%') OR Socio.NumeroDocumento LIKE '" . $qryString . "%' OR Accion LIKE '" . $qryString . "%' OR Email LIKE '" . $qryString . "%' OR Predio LIKE '" . $qryString . "%' OR AccionPadre LIKE '%" . $qryString . "%')";
		}

		for ($i = 1; $i < count($datos); $i++) {
			if (!empty($datos[$i])) {
				//$where .= " OR Socio.Nombre like '%" . $datos[$i] . "%'";
			}
		}
		//} //end if
		break;
	case "searchurlaccion":
		$qryString = SIMNet::req("qryString");
		if (!empty($qryString)) {

			$where .= " AND (  Accion LIKE '" . $qryString . "%' )   ";
		} //end if
		break;

	case "form":
		$proceso = SIMNet::req("proceso");

		switch ($proceso) {

			case 'departamentos':
				$idPais = SIMNet::req("idPais");
				$idDepartamento = SIMNet::req("idDepartamento");

				echo SIMHTML::formPopup('DepartamentoDian', 'Nombre', 'Nombre', 'IDDepartamentoDian', $idDepartamento, '[Seleccione Departamento]', '', 'onchange="selCiudades()"', "AND IDPaisDian = $idPais");
				exit;
				break;

			case 'ciudades':
				$idDepartamento = SIMNet::req("idDepartamento");
				$idCiudad = SIMNet::req("idCiudad");

				echo SIMHTML::formPopup('CiudadDian', 'Nombre', 'Nombre', 'IDCiudadDian', $idCiudad, '[Seleccione Ciudad]', '', '', "AND IDDepartamentoDian = $idDepartamento");
				exit;
				break;
		}

		break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
// connect to the database



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


//$limit = 58;
$sql = "SELECT Socio.IDSocio, IDEstadoSocio, Nombre, Apellido, Accion, TipoSocio, Celular, NumeroDocumento, Email,PermiteReservar, CONCAT( Socio.Nombre, ' ', Socio.Apellido ) AS Socio FROM " . $table . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;
//var_dump($sql);
/* echo $sql;
exit; */
$result = $dbo->query($sql);

$responce = "";



$responce->page = (int)$page;
$responce->total = (int)$total_pages;
$responce->records = (int)$count;
$i = 0;
$btn_eliminar_3 = string;
$hoy = date('Y-m-d');

$sql_estado = "SELECT Nombre,IDEstadoSocio From EstadoSocio WHERE IDClub = 0 OR IDClub = " . SIMUser::get('club');
$r_estado_socio = $dbo->query($sql_estado);
while ($row_estado = $dbo->fetchArray($r_estado_socio)) {
	$array_estado[$row_estado["IDEstadoSocio"]] = $row_estado["Nombre"];
}



while ($row = $dbo->fetchArray($result)) {
	$btn_incluir = '';

	$sqlEstados = "SELECT * FROM EstadoSocio WHERE IDClub = 0 OR IDClub = " . SIMUser::get('club');
	$qryEstado = $dbo->query($sqlEstados);

	while ($Estados = $dbo->fetchArray($qryEstado)) {
		if ($Estados["IDEstadoSocio"] == $row["IDEstadoSocio"])
			$checkEstado = 'checked';
		else
			$checkEstado = '';

		$btn_incluir .= '<input type="radio" value="' . $Estados['IDEstadoSocio'] . '" class="btncambiosocio" name="estado' . $row[$key] . '"  campo = "Estado" idsocio="' . $row[$key] . '" ' . $checkEstado . '>' . $Estados['Nombre'] . '<br>';
	}

	$btn_incluir .= "<div name='msgupdate" . $row[$key] . "' id='msgupdate" . $row[$key] . "'></div>";

	if ($row["PermiteReservar"] == 'S') {
		$checkPermiteSi = 'checked';
		$checkPermiteNo = '';
	} else {
		$checkPermiteNo = 'checked';
		$checkPermiteSi = '';
	}

	$btn_permitir = '<input type="radio" value="S" class="btncambiosocio" name="permite' . $row[$key] . '"  campo = "PermiteReserva" idsocio="' . $row[$key] . '" ' . $checkPermiteSi . '>Si<br>';
	$btn_permitir .= '<input type="radio" value="N" class="btncambiosocio" name="permite' . $row[$key] . '"  campo = "PermiteReserva" idsocio="' . $row[$key] . '" ' . $checkPermiteNo . '>No<br>';
	$btn_permitir .= "<div name='msgupdateReserva" . $row[$key] . "' id='msgupdateReserva" . $row[$key] . "'></div>";


	$responce->rows[$i]['id'] = $row[$key];

	$Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoBorrar");
	if ((int)SIMUser::get("IDPerfil") <= 1 || $Permiso == 1) :
		$btn_eliminar_3 = "<a class='red eliminar_registro' rel=" . $table . " id=" . $row[$key] . " lang = " . $script . " href='#'><i class='ace-icon fa fa-trash-o bigger-130'/></a>";
	else :
		$btn_eliminar_3 = '';
	endif;

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";
	if ($origen <> "mobile")
		$responce->rows[$i]['cell'] = array(
			$key => $row[$key],
			//"Editar" => '<a class="green" href="'.$script.'.php?action=edit&id='.$row[$key].''.'"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
			"TipoSocio" => $row["TipoSocio"],
			"Accion" => $row["Accion"],
			"Nombre" =>  $row["Nombre"],
			"Apellido" => $row["Apellido"],
			"Celular" => $row["Celular"],
			"NumeroDocumento" => $row["NumeroDocumento"],
			"Email" => $row["Email"],
			"Estado" => $btn_incluir,
			"PermiteReservar" => $btn_permitir,
			"Eliminar" => $btn_eliminar_3,
		);

	$i++;
}
echo json_encode($responce);
