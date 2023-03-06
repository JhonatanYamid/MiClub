<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get = SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$condicion_usuario = " or IDUsuarioCreacion = '" . SIMUser::get("IDUsuario") . "'";

if (SIMUser::get("IDPerfil") > 1) :
    //Consulto las areas
    $sql_area_usuario = "Select * From UsuarioAreaFuncionario Where IDUsuario = '" . SIMUser::get("IDUsuario") . "'";
    $result_area_usuario = $dbo->query($sql_area_usuario);
    while ($row_area = $dbo->fetchArray($result_area_usuario)) :
        $array_areas[] = $row_area["IDArea"];
    endwhile;
    if (count($array_areas) > 0) :
        $id_areas = implode(",", $array_areas);
        $condicion_area = " and (PqrFuncionario.IDArea in (" . $id_areas . ") " . $condicion_usuario . ")";
    elseif (SIMUser::get("IDPerfil") <= 1) :
        $condicion_area = " and PqrFuncionario.IDArea > 0";
    elseif (SIMUser::get("IDPerfil") > 1) :
        $id_areas = 0;
        $condicion_area = " and (PqrFuncionario.IDArea in (0) " . $condicion_usuario . ")";
    endif;

endif;

$table = "PqrFuncionario";
$key = "IDPqr";
$where = " WHERE " . $table . ".IDClub = '" . SIMUser::get("club") . "' " . $condicion_area .  " AND IDPqrEstado = '3'";
$script = "pqrfuncionario";

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
                case 'Funcionario':

                    $where .= " AND (  U.Nombre LIKE '%" . $search_object->data . "%' )  ";
                    break;

                case 'Area':
                    //busco el area
                    $sql_area_pqr = "Select * From AreaFuncionario Where Nombre LIKE '%" . $search_object->data . "%'";
                    $result_area_pqr = $dbo->query($sql_area_pqr);
                    while ($row_area_pqr = $dbo->fetchArray($result_area_pqr)) :
                        $array_id_area[] = $row_area_pqr["IDArea"];
                    endwhile;
                    if (count($array_id_area) > 0) :
                        $id_areas_buscar = implode(",", $array_id_area);
                    else :
                        $id_areas_buscar = 0;
                    endif;

                    $where .= " AND   PqrFuncionario.IDArea in (" . $id_areas_buscar . ")";

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
                    $sql_tipo_pqr = "Select * From TipoPqrFuncionario Where Nombre LIKE '%" . $search_object->data . "%' and IDClub = '" . SIMUser::get("club") . "'";
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

                case 'Funcionario':
                    //busco el tipo
                    $sql_func = "Select * From Usuario Where Nombre LIKE '%" . $search_object->data . "%' and IDClub = '" . SIMUser::get("club") . "'";
                    $result_func = $dbo->query($sql_func);
                    while ($row_func = $dbo->fetchArray($result_func)) :
                        $array_id_func[] = $row_func["IDUsuario"];
                    endwhile;
                    if (count($array_id_func) > 0) :
                        $id_func_buscar = implode(",", $array_id_func);
                    else :
                        $id_func_buscar = 0;
                    endif;

                    $where .= " AND   ( IDUsuario in (" . $id_func_buscar . ") or IDUsuarioCreacion in (" . $id_func_buscar . "))";

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

            $where .= " AND ( Asunto LIKE '%" . $qryString . "%' or Descripcion LIKE '%" . $qryString . "%'  )  ";
        } //end if
        break;
}

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) {
    $sidx = "IDPqr";
}

// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . ", Usuario U " . $where . " and U.IDUsuario = PqrFuncionario.IDUsuarioCreacion");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) {
    $page = $total_pages;
}

$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
    $limit = 1000000;
}

$sql = "SELECT " . $table . ".* FROM " . $table . ", Usuario U " . $where . " and U.IDUsuario = PqrFuncionario.IDUsuarioCreacion ORDER BY " . $sidx . " " . $sord . ", FIELD  (IDPqrEstado,'1','4','5','2','3'),IDPqrEstado ASC LIMIT " . $start . "," . $limit;


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
            "Tipo" => "<font color='" . $color_linea . "'>" . $dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $row["IDTipoPqr"] . "'") . "</font>",
            "Area" => "<font color='" . $color_linea . "'>" . utf8_encode($dbo->getFields("AreaFuncionario", "Nombre", "IDArea = '" . $row["IDArea"] . "'")) . "</font>",
            "Funcionario" => "<font color='" . $color_linea . "'>" . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row["IDUsuarioCreacion"] . "'") . "</font>",
            "Asunto" => "<font color='" . $color_linea . "'>" . utf8_encode($row["Asunto"]) . "</font>",
            "Descripcion" => "<font color='" . $color_linea . "'>" . utf8_encode(substr($row["Descripcion"], 0, 50)) . "</font>",
            "Estado" => "<font color='" . $color_linea . "'>" . $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row["IDPqrEstado"] . "'") . "</font>",
            "Calificacion" => "<font color='" . $color_linea . "'>" . $calificacion . "</font>",
            "Eliminar" => $btn_eliminar,
        );
    }

    $i++;
}

echo json_encode($responce);
