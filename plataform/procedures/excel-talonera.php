<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
    $texto = str_replace("ñ", "&ntilde;", $texto);
    $texto = str_replace("á", "&aacute;", $texto);
    $texto = str_replace("é", "&eacute;", $texto);
    $texto = str_replace("í", "&iacute;", $texto);
    $texto = str_replace("ó", "&oacute;", $texto);
    $texto = str_replace("ú", "&uacute;", $texto);
    $texto = str_replace("Ñ", "&Ntilde;", $texto);
    $texto = str_replace("Á", "&Aacute;", $texto);
    $texto = str_replace("É", "&Eacute;", $texto);
    $texto = str_replace("Í", "&Iacute;", $texto);
    $texto = str_replace("Ó", "&Oacute;", $texto);
    $texto = str_replace("Ú", "&Uacute;", $texto);
    return $texto;
}

if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
    $condicion_fecha = " AND FechaTrCr BETWEEN '" . $_POST["FechaInicio"] . " 00:00.00' AND '" . $_POST["FechaFin"] . " 23:59:59'";
}

$sql_reporte = "SELECT 
                    IDSocioTalonera, IDTalonera, IDSocio, ValorPagado, CantidadTotal, CantidadPendiente, FechaCompra, FechaVencimiento, FechaUltimoUso, SaldoMonedero, MedioPago,
                    IF(Dirigida = 'S','Socio',IF(Dirigida = 'F','Familia',IF(Dirigida = 'M','Miembro',IF(Dirigida = 'S','Usuario','')))) as Dirigida,
                    IF(EstadoTransaccion = 'P','Pendiente',IF(EstadoTransaccion = 'A','Aprobado',IF(EstadoTransaccion = 'R','Rechazado',''))) as EstadoTransaccion,
                    IF(Pagado = 'S','Si',IF(Pagado = 'N','No','')) as Pagado, IF(Activo = 1,'Si','No') as Activo
                FROM SocioTalonera 
                WHERE IDClub = ".$_POST["IDClub"].$condicion_fecha." ORDER BY IDSocioTalonera DESC";

// echo $sql_reporte;
// exit();
$result_reporte = $dbo->query($sql_reporte);

$nombre = "Talonera_Reporte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='25'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    
    $html .= "<tr>";
    $html .= "<th></th>";
    $html .= "<th colspan='2'>INFORMACION SOCIO</th>";
    $html .= "<th colspan='16'>INFORMACION TALONERA</th>";
    $html .= "<th colspan='7'>INFORMACION PAGO</th>";
    $html .= "</tr>";
    $html .= "<tr>";

    $html .= "<th>ID_TALONERA</th>";
    //DATOS SOCIO
    $html .= "<th>DOCUMENTO SOCIO</th>";
    $html .= "<th>NOMBRE SOCIO</th>";
    //DATOS TALONERA
    $html .= "<th>NOMBRE TALONERA</th>";
    $html .= "<th>SERVICIO</th>";
    $html .= "<th>DESCRIPCION TALONERA</th>";
    $html .= "<th>ACTIVA</th>";
    $html .= "<th>DIRIGIDA</th>";
    $html .= "<th>CANTIDAD TOTAL</th>";
    $html .= "<th>CANTIDAD PENDIENTE</th>";
    $html .= "<th>FECHA COMPRA</th>";
    $html .= "<th>DURACION</th>";
    $html .= "<th>FECHA VENCIMIENTO</th>";
    $html .= "<th>FECHA ULTIMO USO</th>";
    $html .= "<th>SALDO MONEDERO</th>";
    $html .= "<th>VALOR SOCIO</th>";
    $html .= "<th>VALOR USUARIO</th>";
    $html .= "<th>VALOR GRUPO FAMILIAR</th>";
    $html .= "<th>VALOR POR MIEMBRO</th>";
    //DATOS PAGO
    $html .= "<th>VALOR PAGADO</th>";
    $html .= "<th>REFERENCIA</th>";
    $html .= "<th>MEDIO DE PAGO</th>";
    $html .= "<th>PAGADO</th>";
    $html .= "<th>ESTADO</th>";
    $html .= "<th>CREADO POR</th>";
    $html .= "<th>FECHA DE TRANSACCION</th>";

    $html .= "</tr>";

    while ($Datos = $dbo->fetchArray($result_reporte)) {

        //talonera
        $talonera = $dbo->fetchAll("Talonera", " IDTalonera = " . $Datos["IDTalonera"], "array");
        
        //Nombre de servicios
        if($talonera['TodosLosServicios'] == 1){

            $nombreServicios = "Todos los servicios";
        }
        else{

            $arrServicios = [$talonera['IDServicio']];
            $nomServicios = [];

            $sql_servicios = "SELECT IDServicio FROM TaloneraServicios WHERE IDTalonera = ".$Datos["IDTalonera"];
            $result_servicios = $dbo->query($sql_servicios);

            while ($servicios = $dbo->fetchArray($result_servicios)) {

                if(!in_array($arrServicios, $servicios['IDServicio']))
                    array_push($arrServicios,$servicios['IDServicio']);
            }
            
            foreach($arrServicios as $idServicio){

                $IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = $idServicio");
                $nombreServicio = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = ".$_POST["IDClub"]." AND IDServicioMaestro = $IDServicioMaestro");

                if(empty($nombreServicio)) 
                    $nombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = $IDServicioMaestro");

                array_push($nomServicios,$nombreServicio);
            }
            
            $nombreServicios = implode(',',$nomServicios);
            
        }

        //Socio
        $socio = $dbo->getFields("Socio", array("NumeroDocumento","CONCAT(Nombre,' ',Apellido) as Nombre"), "IDSocio = ". $Datos["IDSocio"]);
        
        //Pasarela de pagos
        $pagos = array();
        if(stripos(strtoupper($Datos["MedioPago"]),"CREDIBANCO") !== false)
            $pagos = $dbo->getFields("PagoCredibanco", array("NumeroTransaccion as Referencia","FechaTrCr"), "reserved12 = ".$Datos['IDSocioTalonera']);
        else if(stripos(strtoupper($Datos["MedioPago"]),"WOMPI") !== false)        
            $pagos = $dbo->getFields("PagosWompi", array("ReferenciaWompi as Referencia","FechaTrCr"), "IDReserva = ".$Datos['IDSocioTalonera']);

        unset($array_datos_seguimiento);
        $html .= "<tr>";
        //DATOS SOCIO
        $html .= "<td>" . $Datos["IDSocioTalonera"] . "</td>";
        $html .= "<td>" . $socio["NumeroDocumento"] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_decode(strtoupper($socio["Nombre"]))) . "</td>";
        //DATOS TALONERA
        $html .= "<td>" . remplaza_tildes(utf8_decode($talonera["NombreTalonera"])) . "</td>";
        $html .= "<td>" . $nombreServicios . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_decode($talonera["DescripcionTalonera"])) . "</td>"; 
        $html .= "<td>" . $Datos["Activo"] . "</td>";       
        $html .= "<td>" . remplaza_tildes(utf8_decode($Datos["Dirigida"])) . "</td>";
        $html .= "<td>" . $Datos["CantidadTotal"] . "</td>";
        $html .= "<td>" . $Datos["CantidadPendiente"] . "</td>";
        $html .= "<td>" . $Datos["FechaCompra"] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_decode(($talonera["Duracion"]) . " " . ($talonera["MedicionDuracion"])))  . "</td>";
        $html .= "<td>" . $Datos["FechaVencimiento"] . "</td>";
        $html .= "<td>" . $Datos["FechaUltimoUso"] . "</td>";
        $html .= "<td>" . $Datos["SaldoMonedero"] . "</td>";
        $html .= "<td>" . $talonera["ValorSocio"] . "</td>";
        $html .= "<td>" . $talonera["ValorUsuario"] . "</td>";
        $html .= "<td>" . $talonera["ValorGrupoFamiliar"] . "</td>";
        $html .= "<td>" . $talonera["ValorPorMiembro"] . "</td>";
        /*DATOS PAGO*/
        $html .= "<td>" . $Datos["ValorPagado"] . "</td>";
        $html .= "<td>" . $pagos["Referencia"] . "</td>";
        $html .= "<td>" . $Datos["MedioPago"] . "</td>";
        $html .= "<td>" . $Datos["Pagado"] . "</td>";
        $html .= "<td>" . $Datos["EstadoTransaccion"] . "</td>";
        $html .= "<td>" . $pagos["UsuarioTrCr"] . "</td>";
        $html .= "<td>" . $pagos["FechaTrCr"] . "</td>";

    }
    $html .= "</table>";

    //construimos el excel
    header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
    header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo $html;
    exit();
}