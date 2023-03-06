<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

$IDClub = $_POST["IDClub"];
$IDUsuario = empty($_POST["IDUsuario"]) ? 0 : $_POST["IDUsuario"];
$where = " WHERE IDClub = $IDClub ";

if($IDUsuario > 0)
    $where .= "AND IDUsuario = $IDUsuario ";


function remplaza_tildes($texto)
{
    $texto = str_replace("ñ", "&ntilde;", $texto);
    $texto = str_replace("á", "&aacute;", $texto);
    $texto = str_replace("é", "&eacute;", $texto);
    $texto = str_replace("í", "&iacute;", $texto);
    $texto = str_replace("ó", "&oacute;", $texto);
    $texto = str_replace("ú", "&uacute;", $texto);
    return $texto;
}

function saber_dia($nombredia)
{
    $dias = array('', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
    $fecha = $dias[date('N', strtotime($nombredia))];
    return $fecha;
}

$FechaInicio = $_POST["FechaInicio"];
$FechaFin = $_POST["FechaFinal"];

if (!empty($FechaInicio) || !empty($FechaFin)) {
    $where .= " AND DATE(FechaEntrada) BETWEEN DATE('$FechaInicio') AND DATE('$FechaFin')";
} 

$sql_reporte = "SELECT IDCheckinFuncionarios, IDUsuario, IDPlanificacionDiaria, FechaEntrada, FechaSalida, Entrada, Salida, DATE_FORMAT(FechaEntrada,'%Y-%m-%d') as Fecha, DATE_FORMAT(FechaCambioEstado,'%Y-%m-%d') as FechaCambioEstado, 
                    IF(Estado = 1,'Pendiente',IF(Estado = 2,'Aprobado','No Aprobado')) AS Estado , ComentarioRevision, UsuarioTrCr
                FROM CheckinFuncionarios
                " . $where . " ORDER BY IDUsuario,FechaEntrada ASC";
// echo $sql_reporte;
// exit;

/* print_r($_POST);
exit; */

$result_reporte = $dbo->query($sql_reporte);

$nombre = "chekinfuncionarios_Reporte:" . date("Y_m_d");

$Numreg = $dbo->rows($result_reporte);

if ($Numreg > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='21'>Se encontraron " . $Numreg . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";

    $html .= "<th>NOMBRE </th>";
    $html .= "<th>DOCUMENTO </th>";
    $html .= "<th>FECHA </th>";
    $html .= "<th>DIA </th>";
    $html .= "<th>PLAN DEL DIA </th>";
    $html .= "<th>HORA ENTRADA ESTABLECIDA</th>";
    $html .= "<th>FECHA-HORA ENTRADA</th>";
    $html .= "<th>HORA SALIDA ESTABLECIDA</th>";
    $html .= "<th>FECHA-HORA SALIDA</th>";
    $html .= "<th>TIEMPO DE ALMUERZO</th>";
    $html .= "<th>NOVEDAD<BR>(Rojo:Afecta el tiempo laborado, Verde:No afecta)</th>";
    $html .= "<th>TIEMPO DE NOVEDES</th>";
    $html .= "<th>TIEMPO TOTAL LABORADO</th>";
    $html .= "<th>TIEMPO EXTRA</th>";
    $html .= "<th>TIPO DE TIEMPO EXTRA</th>";
    $html .= "<th>TIEMPO NO LABORADO</th>";
    $html .= "<th>TIPO DE TIEMPO NO LABORADO</th>";
    $html .= "<th>ESTADO</th>";
    $html .= "<th>COMENTARIO REVISION</th>";
    $html .= "<th>FECHA CAMBIO ESTADO</th>";
    $html .= "<th>REGISTRA</th>";

    $html .= "</tr>";
    while ($row = $dbo->fetchArray($result_reporte)) {

        //dia de la semana
        $Dia = saber_dia($row['Fecha']);

        //Consulta los datos del usuario
        $usuario = $dbo->getFields("Usuario", array("Nombre","NumeroDocumento"), "IDUsuario = ".$row['IDUsuario']);

        $nmPlan = SIMUtil::get_traduccion('', '', 'Sinplanificacion', LANGSESSION);
        $novedades = [];
        $txtNovedades = "";
        $extraTxt = "";
        $noLaboradoTxt = "";
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
                    WHERE IDUsuario = $IDUsuario AND IDClub = $IDClub AND DATE(FechaInicio) = DATE('".$row['FechaEntrada']."')";

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
                    if(strtotime($timeLaborado) > strtotime($timePlan)){
                        $extraTime = $timeTotal;

                        if(strtotime($entrada) < strtotime($entradaPlan) && strtotime($salida) > strtotime($salidaPlan)){

                            $extraTxt = SIMUtil::get_traduccion('', '', 'entradaysalida', LANGSESSION);

                        }else if(strtotime($entrada) < strtotime($entradaPlan)){

                            $extraTxt = SIMUtil::get_traduccion('', '', 'Entrada', LANGSESSION);

                        }else if (strtotime($salida) > strtotime($salidaPlan)){

                            $extraTxt = SIMUtil::get_traduccion('', '', 'Salida', LANGSESSION);  
                            
                        }
                    }else{
                        $noLaborado = $timeTotal;

                        if(strtotime($entrada) > strtotime($entradaPlan) && strtotime($salida) < strtotime($salidaPlan)){

                            $noLaboradoTxt = SIMUtil::get_traduccion('', '', 'entradaysalida', LANGSESSION);

                        }else if(strtotime($entrada) > strtotime($entradaPlan)){

                            $noLaboradoTxt = SIMUtil::get_traduccion('', '', 'Entrada', LANGSESSION);

                        }else if (strtotime($salida) < strtotime($salidaPlan)){

                            $noLaboradoTxt = SIMUtil::get_traduccion('', '', 'Salida', LANGSESSION);  
                            
                        }
                    }
                    
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

        $html .= "<tr>";

        //verifica que no esten vacias
        $entradaPlan = $entradaPlan != '' ? (new DateTime($entradaPlan))->format('h:i:s A') : '00:00:00';
        $salidaPlan = $salidaPlan != '' ? (new DateTime($salidaPlan))->format('h:i:s A') : '00:00:00';
        $timeAlmuerzo = $timeAlmuerzo != '' ? $timeAlmuerzo : '00:00:00';    
        $entrada = $entrada != '' ? (new DateTime($entrada))->format('h:i:s A'): '00:00:00';
        $salida = $salida != '' ? (new DateTime($salida))->format('h:i:s A') : '00:00:00';

        // Escribe los datos en la tabla
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($usuario['Nombre']))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($usuario['NumeroDocumento']))) . "</td>";
        $html .= "<td>" . $row['Fecha'] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($Dia))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($nmPlan))) . "</td>";
        $html .= "<td>" . $entradaPlan . "</td>";
        $html .= "<td>" . $entrada . "</td>";
        $html .= "<td>" . $salidaPlan . "</td>";
        $html .= "<td>" . $salida . "</td>";
        $html .= "<td>" . $timeAlmuerzo . "</td>";
        $html .= "<td>" . $txtNovedades . "</td>";
        $html .= "<td>" . $totalNov . "</td>";
        $html .= "<td>" . $timeLaborado . "</td>";
        $html .= "<td>" . $extraTime . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($extraTxt))) . "</td>";
        $html .= "<td>" . $noLaborado . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($noLaboradoTxt))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($row['Estado']))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($row['ComentarioRevision']))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($row['FechaCambioEstado']))) . "</td>";
        $html .= "<td>" . remplaza_tildes($row["UsuarioTrCr"]) . "</td>";
    }

    $html .= "</table>";

    //construimos el excel
    header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
    header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>

    <body>
        <?php
        echo $html;
        ?>
    </body>

    </html>
<?php
    exit();
}
?>