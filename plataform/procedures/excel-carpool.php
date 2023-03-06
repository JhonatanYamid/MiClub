<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

$IDClub = $_POST["IDClub"];
$where = " WHERE IDClub = $IDClub ";

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
$IDTipoVehiculo = $_POST['IDTipoVehiculo'];
$calificacioninicial = $_POST['calificacioninicial'];
$calificacionfinal = $_POST['calificacionfinal'];
$IDMotivosCalificacion = $_POST['IDMotivosCalificacion'];
$Persona = $_POST['Persona'];
$Estado = $_POST['Estado'];

if (!empty($FechaInicio) && !empty($FechaFin)) {
    $where .= " AND DATE(Fecha) BETWEEN DATE('$FechaInicio') AND DATE('$FechaFin') ";
}

if(!empty($IDTipoVehiculo)){
    $where .= " AND IDTipoVehiculo = $IDTipoVehiculo ";
}

if(!empty($calificacioninicial) && !empty($calificacionfinal)){
    $arrIdsCal = [];

    $sqlCal = "SELECT sel.IDViaje, sel.Calificacion
                FROM 
                    (SELECT sv.IDViaje, ROUND(AVG(cc.Calificacion),0) as Calificacion
                    FROM CalificacionCarPool as cc, SolicitudViaje as sv
                    WHERE cc.IDSolicitudViaje = sv.IDSolicitudViaje
                    GROUP BY cc.Calificacion) as sel
                WHERE sel.Calificacion BETWEEN $calificacioninicial AND $calificacionfinal ";

    $resultCal = $dbo->query($sqlCal);
    while($rowCal = $dbo->fetchArray($resultCal)) {
        array_push($arrIdsCal, $rowCal['IDViaje']);
    }
    
    $idsCal = count($arrIdsCal) > 0 ? implode(",",$arrIdsCal) : 0;

    $where .= " AND IDViaje IN($idsCal) ";
}

if(!empty($IDMotivosCalificacion)){
    $arrIdsMot = [];

    $sqlMot = "SELECT sv.IDViaje
                FROM CalificacionCarPool as cc, SolicitudViaje as sv
                WHERE 
                    cc.IDSolicitudViaje = sv.IDSolicitudViaje AND 
                    cc.IDMotivosCalificacion = $IDMotivosCalificacion";

    $resultMot = $dbo->query($sqlMot);
    while($rowMot = $dbo->fetchArray($resultMot)) {
        array_push($arrIdsMot, $rowMot['IDViaje']);
    }
    
    $idsMot = count($arrIdsMot) > 0 ? implode(",",$arrIdsMot) : 0;

    $where .= " AND IDViaje IN($idsMot) ";
}

if(!empty($Persona)){
    $arrIdsUs = [];
    $arrIdsSoc = [];

    //Busca en Usuarios
    $sqlUsuario = "SELECT IDUsuario FROM Usuario WHERE LOWER(Nombre) LIKE LOWER('%$Persona%') AND IDClub = $IDClub";
    $resUsuario = $dbo->query($sqlUsuario);
    while($rowUsuario = $dbo->fetchArray($resUsuario)) {
        array_push($arrIdsUs, $rowUsuario['IDUsuario']);
    }
    
    $idsUsuario = count($arrIdsUs) > 0 ? implode(",",$arrIdsUs) : 0;

    //Busca en Socios
    $sqlSocio = "SELECT IDSocio FROM Socio WHERE (LOWER(Nombre) LIKE LOWER('%$Persona%') OR LOWER(Apellido) LIKE LOWER('%$Persona%')) AND IDClub = $IDClub";
    $resSocio = $dbo->query($sqlSocio);
    while($rowSocio = $dbo->fetchArray($resSocio)) {
        array_push($arrIdsSoc, $rowSocio['IDSocio']);
    }
    
    $idsSocio = count($arrIdsSoc) > 0 ? implode(",",$arrIdsSoc) : 0;

    $where .= " AND (IDUsuario IN($idsUsuario) OR IDSocio IN($idsSocio)) ";	
}

if(!empty($Estado)){
    $where .= " AND Estado = $Estado ";
}


$sql_reporte = "SELECT IDViaje, IDClub, IDSocio, IDUsuario, IDTipoVehiculo, Fecha, Hora, LugarEncuentro, Sentido, Direccion, 
                    CuposTotales, CuposDisponibles, Modelo, Color, ValorCupo, Estado
                FROM Viaje $where ORDER BY Fecha ASC";
// echo $sql_reporte;
// exit;

/* print_r($_POST);
exit; */

$result_reporte = $dbo->query($sql_reporte);

$nombre = "carroscompartidos_Reporte:" . date("Y_m_d");

$Numreg = $dbo->rows($result_reporte);

if ($Numreg > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='18'>Se encontraron " . $Numreg . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";

    $html .= "<th>CREADO POR</th>";
    $html .= "<th>TIPO</th>";
    $html .= "<th>ORIGEN</th>";
    $html .= "<th>DESTINO</th>";
    $html .= "<th>FECHA</th>";
    $html .= "<th>DIA</th>";
    $html .= "<th>HORA</th>";
    $html .= "<th>LUGAR DE ENCUENTRO</th>";
    $html .= "<th>VEHICULO</th>";
    $html .= "<th>CUPOS TOTALES</th>";
    $html .= "<th>CUPOS DISPONIBLES</th>";
    $html .= "<th>VALOR DEL CUPO</th>";
    $html .= "<th>ESTADO</th>";
    $html .= "<th>CALIFICACION PROMEDIO</th>";
    $html .= "<th colspan='4'>MOTIVOS DE CALIFICACION</th>";
    
    while ($row = $dbo->fetchArray($result_reporte)) {
        $html .= "<tr>";

        $id = $row['IDViaje'];
        
        if($row['IDSocio'] > 0){
            $tipo = 'Socio';
            $persona = $dbo->getFields("Socio", "CONCAT(Nombre,' ', Apellido)", "IDSocio = ".$row['IDSocio']);
        }else{
            $tipo = 'Usuario';
            $persona = $dbo->getFields("Usuario", "Nombre", "IDUsuario = ".$row['IDUsuario']);
        }

        $club = $dbo->getFields("Club", "Nombre", "IDClub = ".$row['IDClub']);
        $origen = $row['Sentido'] == 1 ? $row['Direccion'] : $club;
        $destino = $row['Sentido'] == 1 ? $club : $row['Direccion'];
        
        //dia de la semana
        $dia = saber_dia($row['Fecha']);
        $fecha = date('d-m-Y', strtotime($row['Fecha']));
        $hora = date('h:i a', strtotime($row['hora'])); 

        $tipovehiculo = $dbo->getFields("TipoVehiculo", "Nombre", "IDTipoVehiculo = ".$row['IDTipoVehiculo']);

        if($row['Estado'] == 1){
            $color = "orange";
            $estado = "Abierto";
        }
        else if($row['Estado'] == 2){
            $color = "green";
            $estado = "Cerrado";
        }
        else{
            $color = "red";
            $estado = "Cancelado";
        }

        $c_estado =  "<font color='$color'>$estado</font>";

        $sqlCal = "SELECT ROUND(AVG(Calificacion),2) AS Calificacion, GROUP_CONCAT(DISTINCT Nombre ORDER BY Nombre ASC SEPARATOR ',<br>') as MotivosCalificacion 
                    FROM CalificacionCarPool as cc, MotivosCalificacion as mc, SolicitudViaje as sv, Viaje as v
                    WHERE 
                        cc.IDMotivosCalificacion = mc.IDMotivosCalificacion AND 
                        cc.IDSolicitudViaje = sv.IDSolicitudViaje AND 
                        sv.IDViaje = v.IDViaje AND 
                        sv.IDViaje = $id ";

        $resultCal = $dbo->query($sqlCal);
        $rowCal = $dbo->fetchArray($resultCal);
        $NumCal = $dbo->rows($resultCal);

        $calificacion = $NumCal > 0 ? $rowCal["Calificacion"] : "Sin calificacion";
        $motivo = $NumCal > 0 ? $rowCal["MotivosCalificacion"] : "Sin calificacion";

        // Escribe los datos en la tabla
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($persona))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($tipo))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($origen))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($destino))) . "</td>";
        $html .= "<td>" . $fecha . "</td>";
        $html .= "<td>" . $dia . "</td>";
        $html .= "<td>" . $hora . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($row["LugarEncuentro"]))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($tipovehiculo))) . "</td>";
        $html .= "<td>" . $row["CuposTotales"] . "</td>";
        $html .= "<td>" . $row["CuposDisponibles"] . "</td>";
        $html .= "<td>" . $row["ValorCupo"] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($c_estado))) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($calificacion))) . "</td>";
        $html .= "<td colspan='4'>" . remplaza_tildes(utf8_encode(strtoupper($motivo))) . "</td>";

        $html .= "</tr>";
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