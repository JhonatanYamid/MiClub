<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

//if(SIMUser::get("IDPerfil") == 11 || SIMUser::get("IDPerfil") == 2 || SIMUser::get("IDPerfil") == 22 || SIMUser::get("IDPerfil") == 14):
if (SIMUser::get("IDPerfil") != 0) :
    //Consulto las areas
    $sql_area_usuario = "Select * From UsuarioArea Where IDUsuario = '" . SIMUser::get("IDUsuario") . "'";
    $result_area_usuario = $dbo->query($sql_area_usuario);
    while ($row_area = $dbo->fetchArray($result_area_usuario)) :
        $array_areas[] = $row_area["IDArea"];
    endwhile;
    if (count($array_areas) > 0) :
        $id_areas = implode(",", $array_areas);
    endif;
    $condicion_area = " and IDArea in (" . $id_areas . ")";
endif;

$table = "Pqr";
$key = "IDPqr";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' " . $condicion_area . " AND IDPqrEstado = '3'";
$script = "pqr";

if (!empty($_GET["Numero"])) {
    $where .= " AND Numero LIKE '%" . $_GET["Numero"] . "%'";
}
if (!empty($_GET["Fecha"])) {
    $where .= " AND Fecha LIKE '%" . $_GET["Fecha"] . "%'";
}

if (!empty($_GET["Tipo"])) {
    $sql_tipo_pqr = "Select * From TipoPqr Where Nombre LIKE '%" . $_GET["Tipo"] . "%' and IDClub = '" . SIMUser::get("club") . "'";
    $result_tipo_pqr = $dbo->query($sql_tipo_pqr);
    while ($row_tipo_pqr = $dbo->fetchArray($result_tipo_pqr)) :
        $array_id_tipo[] = $row_tipo_pqr["IDTipoPqr"];
    endwhile;
    if (count($array_id_tipo) > 0) :
        $id_tipo_buscar = implode(",", $array_id_tipo);
    else :
        $id_tipo_buscar = 0;
    endif;

    $where .= " AND   IDTipoPqr in (" . $id_tipo_buscar . ")";
}

if (!empty($_GET["Area"])) {
    //busco el area
    $sql_area_pqr = "Select * From Area Where Nombre LIKE '%" . $_GET["Area"] . "%'";
    $result_area_pqr = $dbo->query($sql_area_pqr);
    while ($row_area_pqr = $dbo->fetchArray($result_area_pqr)) :
        $array_id_area[] = $row_area_pqr["IDArea"];
    endwhile;
    if (count($array_id_area) > 0) :
        $id_areas_buscar = implode(",", $array_id_area);
    else :
        $id_areas_buscar = 0;
    endif;

    $where .= " AND   IDArea in (" . $id_areas_buscar . ")";
}

if (!empty($_GET["TipoSocio"])) {

    $where .= " AND (  S.TipoSocio LIKE '%" . $_GET["TipoSocio"] . "%'  )  ";
}
if (!empty($_GET["Socio"])) {
    $where .= " AND (  S.Nombre LIKE '%" . $_GET["Socio"] . "%' or S.Apellido LIKE '%" . $_GET["Socio"] . "%'  )  ";
}

if (!empty($_GET["Predio"])) {
    $where .= " AND (  S.Predio LIKE '%" . $_GET["Predio"] . "%' )  ";
}

if (!empty($_GET["Descripcion"])) {
    $where .= " AND (  Descripcion LIKE '%" . $_GET["Descripcion"] . "%' )  ";
}

if (!empty($_GET["IDPqrEstado"])) {
    //busco el estado


    $where .= " AND (  IDPqrEstado LIKE '%" . $_GET["IDPqrEstado"] . "%' )  ";
}

if (!empty($_GET["Asunto"])) {
    $where .= " AND (  Asunto LIKE '%" . $_GET["Asunto"] . "%' )  ";
}
if (!empty($_GET["Responsable"])) {
    //busco el tipo
    $sql_area_pqr = "Select * From Area Where (Nombre LIKE '%" . $_GET["Responsable"] . "%' or  Responsable LIKE '%" . $_GET["Responsable"] . "%' ) and IDClub = '" . SIMUser::get("club") . "'";
    $result_area_pqr = $dbo->query($sql_area_pqr);
    while ($row_area_pqr = $dbo->fetchArray($result_area_pqr)) :
        $array_id_area[] = $row_area_pqr["IDArea"];
    endwhile;
    if (count($array_id_area) > 0) :
        $id_area_buscar = implode(",", $array_id_area);
    else :
        $id_area_buscar = 0;
    endif;

    $where .= " AND   IDArea in (" . $id_area_buscar . ")";
}

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true") {
    $oper = "search";
}



switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM Usuario WHERE IDUsuario = '" . $_POST["id"] . "' LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "Apellido ASC, Nombre";
        $_GET['sord'] = "ASC";

        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
                case 'Socio':

                    $where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' or S.Apellido LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'Predio':

                    $where .= " AND (  S.Predio LIKE '%" . $search_object->data . "%' )  ";
                    break;

                case 'TipoSocio':

                    $where .= " AND (  S.TipoSocio LIKE '%" . $search_object->data . "%'  )  ";
                    break;

                case 'Area':
                    //busco el area
                    $sql_area_pqr = "Select * From Area Where Nombre LIKE '%" . $search_object->data . "%'";
                    $result_area_pqr = $dbo->query($sql_area_pqr);
                    while ($row_area_pqr = $dbo->fetchArray($result_area_pqr)) :
                        $array_id_area[] = $row_area_pqr["IDArea"];
                    endwhile;
                    if (count($array_id_area) > 0) :
                        $id_areas_buscar = implode(",", $array_id_area);
                    else :
                        $id_areas_buscar = 0;
                    endif;

                    $where .= " AND   IDArea in (" . $id_areas_buscar . ")";

                    break;

                case 'Estado':
                    //busco el estado
                    $sql_estado_pqr = "Select * From PqrEstado Where Nombre LIKE '%" . $search_object->data . "%'";
                    $result_estado_pqr = $dbo->query($sql_estado_pqr);
                    while ($row_estado_pqr = $dbo->fetchArray($result_estado_pqr)) :
                        $array_id_estado[] = $row_estado_pqr["IDPqrEstado"];
                    endwhile;
                    if (count($array_id_estado) > 0) :
                        $id_estado_buscar = implode(",", $array_id_estado);
                    else :
                        $id_estado_buscar = 0;
                    endif;

                    $where .= " AND   IDPqrEstado in (" . $id_estado_buscar . ")";

                    break;

                case 'Tipo':
                    //busco el tipo
                    $sql_tipo_pqr = "Select * From TipoPqr Where Nombre LIKE '%" . $search_object->data . "%' and IDClub = '" . SIMUser::get("club") . "'";
                    $result_tipo_pqr = $dbo->query($sql_tipo_pqr);
                    while ($row_tipo_pqr = $dbo->fetchArray($result_tipo_pqr)) :
                        $array_id_tipo[] = $row_tipo_pqr["IDTipoPqr"];
                    endwhile;
                    if (count($array_id_tipo) > 0) :
                        $id_tipo_buscar = implode(",", $array_id_tipo);
                    else :
                        $id_tipo_buscar = 0;
                    endif;

                    $where .= " AND   IDTipoPqr in (" . $id_tipo_buscar . ")";

                    break;

                case 'Responsable':
                    //busco el tipo
                    $sql_area_pqr = "Select * From Area Where (Nombre LIKE '%" . $search_object->data . "%' or  Responsable LIKE '%" . $search_object->data . "%' ) and IDClub = '" . SIMUser::get("club") . "'";
                    $result_area_pqr = $dbo->query($sql_area_pqr);
                    while ($row_area_pqr = $dbo->fetchArray($result_area_pqr)) :
                        $array_id_area[] = $row_area_pqr["IDArea"];
                    endwhile;
                    if (count($array_id_area) > 0) :
                        $id_area_buscar = implode(",", $array_id_area);
                    else :
                        $id_area_buscar = 0;
                    endif;

                    $where .= " AND   IDArea in (" . $id_area_buscar . ")";

                    break;

                default:
                    $where .= $array_buqueda->groupOp . " " . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for

        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");

        if (!empty($qryString)) {

            $where .= " AND ( Asunto LIKE '%" . $qryString . "%' or Descripcion LIKE '%" . $qryString . "%' or Numero LIKE '%" . $qryString . "%'  )  ";
        } //end if


}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) {
    $sidx = "IDPqr";
}

// connect to the database

//echo "SELECT COUNT(*) AS count FROM " . $table . $where . "    ";
$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . "    ");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 1;
}
if ($page > $total_pages) {
    $page = $total_pages;
}

$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
    $limit = 1000000;
}

$sql = "SELECT " . $table . ".* FROM " . $table . ", Socio S " . $where . $where_filtro . " and S.IDSocio = Pqr.IDSocio " . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;

/* var_dump($sql);
exit; */
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

    switch ($row["Tipo"]) {
        case "P":
            $tipo_pqr = "Peticion";
            break;
        case "Q":
            $tipo_pqr = "Queja";
            break;
        case "R":
            $tipo_pqr = "Reclamo";
            break;
    }

    if ($row["IDPqrEstado"] == 1) {
        $color_linea = "#F43125";
    } elseif ($row["IDPqrEstado"] == 5) {
        $color_linea = "#f27a0a";
    } elseif ($row["IDPqrEstado"] == 3) {
        $color_linea = "#31a32f";
    } else {
        $color_linea = "#2e49a3";
    }

    //Para el Rancho todos pueden cancelar reserva
    if (SIMUser::get("IDPerfil") <= 1) :
        $btn_eliminar = '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
    else :
        $btn_eliminar = '';
    endif;

    if ((int) $row["Calificacion"] > 0) {
        $calificacion = $row["Calificacion"];
    } else {
        $calificacion = "";
    }

    if ($origen != "mobile") {
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            "Numero" => "<font color='" . $color_linea . "'>" . $row["Numero"] . "</font>",
            "Fecha" => "<font color='" . $color_linea . "'>" . substr($row["Fecha"], 0, 10) . "</font>",
            "Tipo" => "<font color='" . $color_linea . "'>" . $dbo->getFields("TipoPqr", "Nombre", "IDTipoPqr = '" . $row["IDTipoPqr"] . "'") . "</font>",
            "Area" => "<font color='" . $color_linea . "'>" . $dbo->getFields("Area", "Nombre", "IDArea = '" . $row["IDArea"] . "'") . "</font>",
            "Responsable" => "<font color='" . $color_linea . "'>" . utf8_encode($dbo->getFields("Area", "Responsable", "IDArea = '" . $row["IDArea"] . "'")) . "</font>",
            "TipoSocio" => "<font color='" . $color_linea . "'>" . utf8_encode($dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $row["IDSocio"] . "'")) . "</font>",
            "Socio" => "<font color='" . $color_linea . "'>" . $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $row["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $row["IDSocio"] . "'") . "</font>",
            "Predio" => "<font color='" . $color_linea . "'>" . $dbo->getFields("Socio", "Predio", "IDSocio='" . $row["IDSocio"] . "'") . "</font>",
            "Asunto" => "<font color='" . $color_linea . "'>" . $row["Asunto"] . "</font>",
            "Descripcion" => "<font color='" . $color_linea . "'>" . utf8_encode(substr($row["Descripcion"], 0, 20)) . "</font>",
            "Estado" => "<font color='" . $color_linea . "'>" . $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row["IDPqrEstado"] . "'") . "</font>",
            "Calificacion" => "<font color='" . $color_linea . "'>" . $calificacion . "</font>",
            "Eliminar" => $btn_eliminar,
        );
    }

    $i++;
}


echo json_encode($responce);
