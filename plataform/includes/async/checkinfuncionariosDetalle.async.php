<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
setlocale(LC_TIME, 'es_ES.UTF-8');

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$columns = array();
$origen = SIMNet::req("origen");

$type = $frm_get["type"];
$id = $frm_get["id"];
$IDClub = SIMUser::get("club");
$hoy = date('Y-m-d');

$table = "CheckinFuncionarios";
$key = "IDCheckinFuncionarios";
$where = " WHERE IDClub = $IDClub AND IDUsuario = $id ";
$script = "checkinfuncionarios";

$oper = SIMNet::req("oper");
if (SIMNet::req("_search") == "true")
    $oper = "search";

switch ($oper) {

    case "del":

        $sql_delete = "DELETE FROM CheckinFuncionarios WHERE IDCheckinFuncionarios = '" . $_POST["id"] . "' LIMIT 1";
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
                case 'qryString':
                    $where .= " AND ( FechaEntrada LIKE '%" . $search_object->data . "%' )";
                // $where .= " AND ( NombreVisitante LIKE '%" . $search_object->data . "%' OR FechaDeInicio LIKE '%" . $search_object->data . "%' OR FechaFinal LIKE '%"  . $search_object->data . "%' ) ";
                break;
            
                case 'Fecha':
                    $where .= " AND DATE_FORMAT(FechaEntrada,'%Y-%m-%d') LIKE ('%" . $search_object->data . "%') ";
                break;
            
                case 'Estado':
                    $where .= " AND LOWER(IF(Estado = 1,'Pendiente',IF(Estado = 2,'Aprobado','No Aprobado'))) LIKE LOWER('%" . $search_object->data . "%') ";
                break;

				case 'Plan':

					$sqlTurnos = "SELECT IDTurnos FROM Turnos WHERE LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%') AND IDClub = $IDClub";
					$resultTurnos = $dbo->query($sqlTurnos);

					while ($rowTurnos = $dbo->fetchArray($resultTurnos)) :
						$arrayTurnos[] = $rowTurnos["IDTurnos"];
					endwhile;
					
					$idsTurnos = count($arrayTurnos) > 0 ? implode(",", $arrayTurnos) : 0;
					
					$sqlDiasnl = "SELECT IDDiaNoLaboral FROM DiaNoLaboral WHERE LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%') AND IDClub = $IDClub";
					$resultDiasnl = $dbo->query($sqlDiasnl);

					while ($rowDiasnl = $dbo->fetchArray($resultDiasnl)) :
						$arrayDiasnl[] = $rowDiasnl["IDDiaNoLaboral"];
					endwhile;
					
                    $sinPlan = SIMUtil::get_traduccion('', '', 'Sinplanificacion', LANGSESSION);
                    $idsDiasnl = count($arrayDiasnl) > 0 ? implode(",", $arrayDiasnl) : 0;

                    $sqlPlan = "SELECT IDPlanificacionDiaria 
                                    FROM PlanificacionDiaria 
                                    WHERE (IDTurnos in($idsTurnos) OR IDDiaNoLaboral in($idsDiasnl)) AND IDClub = $IDClub";

					$resultPlan = $dbo->query($sqlPlan);

                    while ($rowPlan = $dbo->fetchArray($resultPlan)) :
						$arrayPlan[] = $rowPlan["IDPlanificacionDiaria"];
					endwhile;
					
                    $idsPlan = count($arrayPlan) > 0 ? implode(",", $arrayPlan) : 0;

                    $sinPlan = SIMUtil::get_traduccion('', '', 'Sinplanificacion', LANGSESSION);
                    
					$where .= " AND (IDPlanificacionDiaria in($idsPlan) OR LOWER('$sinPlan') LIKE LOWER('%" . $search_object->data . "%')) ";

				break;
            
                default:
                    $where .=  $array_buqueda->groupOp . "  LOWER(".$tabla . $search_object->field . ") LIKE LOWER('%" . $search_object->data . "%') ";
                break;
            }
        } //end for

    break;

    case "searchurl":
        $qryString = SIMNet::req("qryString");
        if (!empty($qryString)) {
            $sqlUsuario = "SELECT IDUsuario FROM Usuario WHERE LOWER(Nombre) LIKE LOWER('%" . $search_object->data . "%') AND IDClub = $IDClub";
            $resultUsuario = $dbo->query($sqlUsuario);

            while ($rowUsuario = $dbo->fetchArray($resultUsuario)) :
                $arrayIds[] = $rowUsuario["IDUsuario"];
            endwhile;
            
            $idsUsuario = count($arrayIds) > 0 ? implode(",", $arrayIds) : 0;

            $where .= " AND IDUsuario in($idsUsuario) ";
        }
    break;

    case "searchDate":
        $FechaInicio = $frm_get["inicio"];
        $FechaFin = $frm_get["fin"];
        
        if (!empty($FechaInicio) || !empty($FechaFin)) {
            $where .= " AND DATE(FechaEntrada) BETWEEN DATE('$FechaInicio') AND DATE('$FechaFin')";
        } 

    break;

    case "aprobarTodo":
        $idClub = SIMNet::req("club");
        $idUsuario= SIMNet::req("id");

        $sql = "UPDATE CheckinFuncionarios SET Estado = 2 WHERE IDClub = $idClub AND IDUsuario = $idUsuario AND Estado = 1 ";
        $query = $dbo->query($sql);

        echo ($query) ? '1' : '0';
        exit;
    break;

    case "cambiaEstado":
        $estado = SIMNet::req("estado");
        $comentario = SIMNet::req("comentario");
        $idUpdate= SIMNet::req("ID");

        $sql_cambio = "UPDATE CheckinFuncionarios SET  Estado = '$estado', ComentarioRevision = '$comentario', FechaCambioEstado = '$hoy' WHERE IDCheckinFuncionarios = $idUpdate";
        $query = $dbo->query($sql_cambio);
    
        echo ($query) ? '1' : '0';
        exit;
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

$sql = "SELECT IDCheckinFuncionarios, IDUsuario, IDPlanificacionDiaria, FechaEntrada, FechaSalida, Entrada, Salida, 
               Estado, DATE_FORMAT(FechaEntrada,'%Y-%m-%d') as Fecha, Estado
        FROM CheckinFuncionarios
        " . $where . " ORDER BY $sidx $sord LIMIT " . $start . "," . $limit;

// echo $sql;
// exit;
$result = $dbo->query($sql);
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;

$dataUsuario = $dbo->fetchAll($type, "ID" . $type . "=" . $id, "array");

$i = 0;
while ($row = $dbo->fetchArray($result)) {

    $responce->rows[$i]['id'] = $row[$key];
    $Dia = ucfirst(strftime('%A', strtotime($row['Fecha'])));
    $nmPlan = SIMUtil::get_traduccion('', '', 'Sinplanificacion', LANGSESSION);

    $novedades = [];
    $txtNovedades = "";
    $timeCero = "00:00:00";
    $timeTotal = $timeCero;
    $totalNov = $timeCero;
    $extraTime = $timeCero;
    $noLaborado = $timeCero;
    $timeAlmuerzo = $timeCero;
    $timeLaborado = $timeCero;
    $now = new DateTime();

    // si no se encuentra una entrada registrada pone la fecha en 0
    $entrada = $row['Entrada'] ? $row['FechaEntrada'] : "";
    // si no se encuentra una salida final registrada pone la fecha en 0
    $salida = $row['Salida'] ? $row['FechaSalida'] : "";

    $entradaPlan = "";
    $salidaPlan = "";

    $sqlNov = "SELECT FechaInicio, FechaFin, Observaciones, Afecta FROM NovedadFuncionarios 
                WHERE IDUsuario = $id AND IDClub = $IDClub AND DATE(FechaInicio) = DATE('".$row['FechaEntrada']."')";
    $rNov = $dbo->query($sqlNov);
    $rCount = $dbo->rows($rNov);
    
    while ($rowNov = $dbo->fetchArray($rNov)) {
        if($txtNovedades != "")
            $txtNovedades .= "<BR>";
        
        //si la novedad inicia antes de la ultima salida regisrada y afecta el tiempo trabajado se agrega al arreglo para descontar del tiempo total
        if((strtotime($rowNov['FechaInicio']) < strtotime($salida)) && $rowNov['Afecta'] == 1){    
            $color = "red";
            array_push($novedades,SIMUtil::diferencia_datetime_general($rowNov['FechaInicio'],$rowNov['FechaFin']));                
        }
        else{
            $color = "green";
        }
        
        $txtNovedades .= "<font color='$color'>".$rowNov['Observaciones']."</font>";
    }

    $totalNov = $rCount > 0 ? SIMUtil::sumar_horas($novedades) : $totalNov;

    //verifica que exista un planificacion programada
    if($row['IDPlanificacionDiaria'] > 0){
        $planDia = $dbo->getFields("PlanificacionDiaria", array("IDTurnos","IDDiaNoLaboral","DiaLaboral"), "IDPlanificacionDiaria = ".$row['IDPlanificacionDiaria']);
        $dialaboral = $planDia['DiaLaboral'];

        //si el dia es laboral trae los registros de ingresos y salidas ademas del nombre del turno
        if($planDia['DiaLaboral']){

            $turno = $dbo->getFields("Turnos", array("Nombre","Entrada","Salida","AlmuerzoInicio","AlmuerzoFin"), "IDTurnos = ".$planDia['IDTurnos']);

            $nmPlan = $turno['Nombre'];
            $entradaPlan = date('Y-m-d',strtotime($entrada))." ".$turno['Entrada'];
            $salidaPlan = date('Y-m-d',strtotime($entrada))." ".$turno['Salida'];

            if(strtotime($salidaPlan) < strtotime($entradaPlan))
                $salidaPlan = date ('Y-m-d H:i:s',strtotime("+1 day",strtotime ($salidaPlan))); 

            // si no se encuentra una entrada registrada toma la fecha de entrada del plan
            $entrada = $row['Entrada'] ? $row['FechaEntrada'] : $entradaPlan;
            // si no se encuentra una salida final registrada toma la fecha de salida del plan
            $salida = $row['Salida'] ? $row['FechaSalida'] : $salidaPlan;

            if(strtotime($salida) >= strtotime($now)){

                $timeLaborado = SIMUtil::diferencia_datetime_general($entrada,$salida);
                
                $inicioAlmuerzo = $turno['AlmuerzoInicio'];
                $finAlmuerzo = $turno['AlmuerzoFin'];

                //calcula el tiempo de almuerzo
                $timeAlmuerzo = SIMUtil::diferencia_datetime_general($inicioAlmuerzo,$finAlmuerzo);

                //resta el total de tiempo en novedades al tiempo laborado
                $timeLaborado = strtotime($timeLaborado) > strtotime($totalNov) ? SIMUtil::restar_horas($timeLaborado,$totalNov) : '00:00:00';

                //resta el total del tiempo del almuerzo en el plan 
                $timeLaborado = strtotime($timeLaborado) > strtotime($timeAlmuerzo) ? SIMUtil::restar_horas($timeLaborado,$timeAlmuerzo) : '00:00:00';

                //calcula el total de horas laborales planificadas
                $timePlan = SIMUtil::diferencia_datetime_general($entradaPlan,$salidaPlan);
                $timePlan = SIMUtil::restar_horas($timePlan,$timeAlmuerzo);

                //calcula la diferencia entre las horas trabajadas y las horas programadas
                $timeTotal = SIMUtil::diferencia_datetime_general($timeLaborado,$timePlan);

                //si el tiempo trabajado es mayor al planeado lo pone en horas extra, en caso contrario lo pone en tiempo no trabajado
                if(strtotime($timeLaborado) > strtotime($timePlan))
                    $extraTime = $timeTotal;
                else
                    $noLaborado = $timeTotal;
    
            }
                    
        }  
        else{

            $nmPlan = $dbo->getFields("DiaNoLaboral", "Nombre", "IDDiaNoLaboral = ".$planDia['IDDiaNoLaboral']);

            if($row['Entrada'] && $row['Salida']){
                if(strtotime($row['FechaSalida']) >= strtotime($now)){
                    //calcula el tiempo entre la hora de entrada y la hora de salida
                    $timeLaborado = SIMUtil::diferencia_datetime_general($row['FechaEntrada'],$row['FechaSalida']);

                    $extraTime = $timeLaborado;
                    $extraTxt = SIMUtil::get_traduccion('', '', 'dianolaboral', LANGSESSION);
                }
            }

        }
    }

    $btnSave = "";
    $Estados = SIMResources::$estado_checkin_laboral;
    $btn_incluir = "";
    if($row['Estado'] == 1){
        
        foreach ($Estados as $e => $estado) {
            $checkEstado = ($e == $row["Estado"]) ? 'checked' : '';
            $btn_incluir .= '<input type="radio" value="' . $e . '" class="" name="Estado' . $row[$key] . '"  campo="Estado" id="Estado' . $row[$key] . '" ' . $checkEstado . ' ' . $disabled . '>' . $estado . '<br>';
        }
        $btn_incluir .= "<div name='msgupdate" . $row[$key] . "' id='msgupdate" . $row[$key] . "'></div>";
        $htmlComentario = '<textarea id="ComentarioRevision' . $row[$key] . '" name="ComentarioRevision' . $row[$key] . '" cols="12" rows="3" class="col-12 col-xs-12" title="Comentario"  ' . $disabled . '>' . $row["ComentarioRevision"] . '</textarea>';

        $btnSave ='<a title="Guardar" class="blue update_registro_funcionarios" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-save bigger-130"/></a>';

    }else{
        $btn_incluir = $Estados[$row['Estado']];
        $htmlComentario = $row["ComentarioRevision"];
    }

    $btnEdit = '<a title="Editar" class="green" href="' . $script . '.php?action=edit&id=' . $row[$key] . '' . '"><i class="ace-icon fa fa-pencil bigger-130"/></a>';
    $btnDel = '<a title="Eliminar" class="red eliminar_registro" rel=' . $table . ' id=' . $row[$key] . ' lang = ' . $script . ' href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>';

    //verifica que no esten vacias
    $entradaPlan = $entradaPlan != '' ? (new DateTime($entradaPlan))->format('h:i:s A') : '00:00:00';
    $salidaPlan = $salidaPlan != '' ? (new DateTime($salidaPlan))->format('h:i:s A') : '00:00:00';
    $timeAlmuerzo = $timeAlmuerzo != '' ? $timeAlmuerzo : '00:00:00';    
    $entrada = $entrada != '' ? (new DateTime($entrada))->format('h:i:s A'): '00:00:00';
    $salida = $salida != '' ? (new DateTime($salida))->format('h:i:s A') : '00:00:00';

    $class = "a-edit-modal btnAddReg";
    $attr = "rev=\"reload_grid\"";
    if ($origen <> "mobile")
        $responce->rows[$i]['cell'] = array(
            $key => $row[$key],
            "Fecha" => $row['Fecha'],
            "Dia" => $Dia,
            "Plan" => $nmPlan,
            "EntradaPlan" => $entradaPlan,
            "Entrada" => $entrada,
            "TiempoAlmuerzo" => $timeAlmuerzo,
            "SalidaPlan" => $salidaPlan,
            "Salida" => $salida,
            "TiempoAlmuerzo" => $timeAlmuerzo,
            "Novedades" => $txtNovedades,
            "TiempoNovedades" => $totalNov,
            "TiempoLaborado" => $timeLaborado,
            "Extra" => $extraTime,
            "NoLaboral" => $noLaborado,
            "Estado" => $btn_incluir,
            "ComentarioRevision" => $htmlComentario,
            "Accion" => $btnSave."&nbsp&nbsp".$btnEdit."&nbsp&nbsp".$btnDel
        );

    $i++;
}

echo json_encode($responce);
