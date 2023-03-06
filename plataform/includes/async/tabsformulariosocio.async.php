<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$table = "CamposFormularioSocio";
$key = "IDCamposFormularioSocio";
$where = " WHERE 1=1 ";

$script = "camposformulariosocio";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM CamposFormularioSocio WHERE IDCamposFormularioSocio = '" . $_POST["id"] . "' LIMIT 1";
        //echo "<br>";
        $qry_delete = $dbo->query($sql_delete);

        $_GET["page"] = 1;
        $_GET['rows'] = 100;
        $_GET['sidx'] = "Nombre";
        $_GET['sord'] = "ASC";

        break;

    case "search":

        $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
        $array_buqueda = json_decode($filters);
        foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
            switch ($search_object->field) {
                case 'qryString':

                    $where .= " AND ( Nombre LIKE '%" . $search_object->data . "%' )";
                    break;
                case 'Grupo':
                        $sqlGrupo = "SELECT IDGruposFormularioSocio FROM GruposFormularioSocio WHERE Nombre LIKE '%". $search_object->data ."%'";
                        $resultGrupo = $dbo->query($sqlGrupo);
                        
                        while ($rowGrupo = $dbo->fetchArray($resultGrupo)){
                            $array_idGrupo[] = $rowGrupo["IDGruposFormularioSocio"];
                        }

                        if (count($array_idGrupo) > 0)
                            $idGrupo = implode(",", $array_idGrupo);
                        else
                            $idGrupo = 0;

                        $where .= " AND IDGruposFormularioSocio in (" . $idGrupo . ")";
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

            $where .= " AND ( Nombre LIKE '%" . $qryString . "%'  )  ";
        } //end if
        break;
    
    //Selecciona o elimina la seleccion del campo escogido segun su estado. 
    case "Sel":
        $id_sel = $_GET['id'];
        
        $sql_sel = "SELECT COUNT(*) as count FROM CampoSocioClub WHERE IDClub = ".SIMUser::get("club")." AND IDCampoFormularioSocio = ".$id_sel;
        $res_sel = $dbo->query($sql_sel);
        $row_sel = $dbo->fetchArray($res_sel);
        $count_sel = $row_sel['count'];

        if($count_sel > 0){
            $sql_op = "DELETE FROM CampoSocioClub WHERE IDCampoFormularioSocio = $id_sel AND IDClub = ".SIMUser::get("club");
        }else{
            $sql_op = "INSERT INTO CampoSocioClub (IDCampoFormularioSocio,IDClub) VALUES ($id_sel, ".SIMUser::get("club").")";
        }
        $result = $dbo->query($sql_op);
        echo true;

        break;
    
    //selecciona todos los campos si marca como check o elimina la seleccion en caso contrario
    case "SelAll":

        $status = $_GET['status'];
        $IDClub = SIMUser::get("club");

        if($status == 'true'){
            $j = 1;
            $sqlAll = "SELECT IDCamposFormularioSocio FROM CamposFormularioSocio 
                   WHERE (IDCLub = $IDClub OR IDCLub = 8) AND IDCamposFormularioSocio NOT IN (SELECT IDCampoFormularioSocio FROM CampoSocioClub WHERE IDCLub = $IDClub)";

            $resAll = $dbo->query($sqlAll);   
            $cont = $dbo->rows($resAll);

            if($cont > 0){
                $sqlIns = "INSERT INTO CampoSocioClub (IDCampoFormularioSocio,IDClub) VALUES ";
            
                while ($rowAll = $dbo->fetchArray($resAll)) {
                    $sqlIns .= "(".$rowAll['IDCamposFormularioSocio'].",$IDClub)";
            
                    if($j < $cont)
                        $sqlIns .= ",";

                    $j++;
                }

                $resIns = $dbo->query($sqlIns);
            }

        }else{
            $sqlDel = "DELETE FROM CampoSocioClub WHERE IDClub = $IDClub AND 
                      IDCampoFormularioSocio NOT IN (SELECT IDCamposFormularioSocio 
                                                    FROM CamposFormularioSocio WHERE Obligatorio = 'S')";
            $resDel = $dbo->query($sqlDel);
        }
        
        echo true;
        
        break;

    //Retorna la informacion para autocompletar los input de nombretabla y nombrecampo
    case "autocomplete":
        $qryString = SIMNet::req("qryString");
        $tipo = SIMNet::req("tipo");
        $tablaName = SIMNet::req("tablaName");
        
        $arrayRes = array();
        
        if($tipo == 'tabla'){
            if (!empty($qryString)) {
                $sql_Tabla = "SELECT TABLE_NAME as nombreTabla
                            FROM INFORMATION_SCHEMA.tables 
                            WHERE TABLE_SCHEMA = 'miclubappdev' AND LOWER(TABLE_NAME) LIKE LOWER('%".$qryString."%')";
                $qry_Tabla = $dbo->query($sql_Tabla);

                while ($r_Tabla = $dbo->fetchArray($qry_Tabla)){
                    $arrayRes[] = $r_Tabla["nombreTabla"];
                }
            }
        }else{
            if (!empty($tablaName)){
                $sql_Campo = "SELECT COLUMN_NAME as CampoName 
                                FROM INFORMATION_SCHEMA.columns 
                                WHERE TABLE_SCHEMA = 'miclubappdev' AND TABLE_NAME = '".$tablaName."' AND LOWER(COLUMN_NAME) LIKE LOWER('%".$qryString."%')";

                $qry_Campo = $dbo->query($sql_Campo);

                while ($r_Campo = $dbo->fetchArray($qry_Campo)){
                    $arrayRes[] = $r_Campo["CampoName"];
                }
            }
        }
            
        echo json_encode($arrayRes);
        break;
}


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if (!$sidx) $sidx = "Nombre";
// connect to the database

$result = $dbo->query("SELECT COUNT(*) AS count FROM " . $table . $where . " ");
$row = $dbo->fetchArray($result);
$count = $row['count'];

if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 1;
}
if ($page > $total_pages) $page = $total_pages;
$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit))
    $limit = 1000000;

$sql = "SELECT * FROM " . $table . $where . " ORDER BY $sidx $sord " . $str_limit;
//exit;
// var_dump($sql);
$result = $dbo->query($sql);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$hoy = date('Y-m-d');
while ($row = $dbo->fetchArray($result)) {
    $responce->rows[$i]['id'] = $row[$key];

    $sql_camp = "SELECT COUNT(*) as count FROM CampoSocioClub WHERE IDClub = ".SIMUser::get("club")." AND IDCampoFormularioSocio = ".$row[$key];
    $res = $dbo->query($sql_camp);
    $row_camp = $dbo->fetchArray($res);
    $count_camp = $row_camp['count'];

    $sel = false;
    if($count_camp > 0){
        $sel = true;
    }

    $arrTipos = array(
        "Texto en una línea" => "text",
        "Texto en párrafo" => "textarea", 
        "Múltiples opciones" => "radio",
        "Casillas de verificación" => "checkbox",
        "Menú desplegable" => "select",
        "Número" => "number",
        "Fecha" => "date",
        "Hora" => "time",
        "Correo electrónico" => "email",
        "Contraseña" => "password",
        "Imagen" => "file"
    );

    $tipoOp = array_search($row["Tipo"], $arrTipos); 

    $sqlGrupo = "SELECT Nombre FROM GruposFormularioSocio WHERE IDGruposFormularioSocio = ".$row['IDGruposFormularioSocio'];
    $resGrupo = $dbo->query($sqlGrupo);
    $rowGrupo = $dbo->fetchArray($resGrupo);
    $nombreGrupo = $rowGrupo ['Nombre'];

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile") {
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Editar" => '<a class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>',
            'Seleccionar'=> $sel,
            'Nombre'=> $row["Nombre"],
            'CampoKey'=> $row["CampoKey"],
            'Tipo'=> $tipoOp,
            'Grupo'=> $nombreGrupo,
            'Obligatorio'=> $row["Obligatorio"],
            'Activo'=> $row["Activo"],
            "Eliminar" => '<a class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
        );
    }
    $i++;
}

echo json_encode($responce);
