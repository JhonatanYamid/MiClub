<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");


$table = "Ciudad";
$key = "IDCiudad";
//$where = " WHERE ";
$script = "ciudad";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM Ciudad WHERE IDCiudad = '" . $_POST["id"] . "' LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "FechaTrCr";
        $_GET['sord'] = "ASC";


        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {



                case 'Nombre':
                    $where .= " WHERE ( Nombre LIKE '%" . $search_object->data . "%' )";

                    break;

                case 'Indicativo':
                    $where .= " WHERE ( Indicativo LIKE '%" . $search_object->data . "%' )";

                    break;


                case 'Publicar':
                    $where .= " WHERE ( Publicar LIKE '%" . $search_object->data . "%' )";

                    break;

                default:
                    $where .=  $array_buqueda->groupOp . " " . $table . "." . $search_object->field . " LIKE '%" . $search_object->data . "%' ";
                    break;
            }
        } //end for




        break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {

            $where .= " AND ( PermitePais LIKE '%" . $qryString . "%' OR PermiteCiudad LIKE '%" . $qryString . "%' OR LabelBotonPais LIKE '%" . $qryString . "%' OR LabelBotonCiudad LIKE '%" . $qryString . "%')";
        } //end if
        break;

    case "autocomplete":
        $qryString = SIMNet::req("qryString");


        if (!empty($qryString)) {
            $sql_pais = "SELECT IDCiudad,Nombre 
                            FROM Ciudad 
                            WHERE Publicar='S' AND Nombre LIKE LOWER('%" . $qryString . "%')";
            $qry_pais = $dbo->query($sql_pais);

            while ($rowData = $dbo->fetchArray($qry_pais)) {
                $data[] = array(
                    'IDCiudad' => $rowData['IDCiudad'],
                    'Nombre' => $rowData['Nombre'],


                );
            }
        }
        echo json_encode($data);

        break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "FechaTrEd";
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




$sql = "SELECT " . $table . ".* FROM " . $table . $where  . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;

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

    $Pais = $dbo->getFields("Pais", "Nombre", "IDPais = '" . $row["IDPais"] . "'");
    $Departamento = $dbo->getFields("Departamento", "Nombre", "IDDepartamento = '" . $row["IDDepartamento"] . "'");



    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',


            "Nombre" => $row["Nombre"],
            "Departamento" => $Departamento,
            "Pais" => $Pais,
            "Indicativo" => $row["Indicativo"],
            "Publicar" => $row["Publicar"],


            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'

        );

    $i++;
}

echo json_encode($responce);
