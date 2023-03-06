#!/usr/bin/php -q

<?php

include("/home/http/miclubapp/httpdocs/admin/config.inc.php");

$Hora = date("H");
$Fecha = date("Y-m-d");
$FechaCierre = $Fecha;
$Dia = date("w");

if(isset($_GET["FechaFija"])):
    $CondicionFecha = " AND Fecha = '$_GET[FechaFija]'";  
    $FechaCierre = $_GET["FechaFija"];
endif;

if(isset($_GET["IDServicio"])):
    $SQLSorteosACorrer = "SELECT * FROM SorteosServicios WHERE IDServicio = $_GET[IDServicio]";
else:
    $SQLSorteosACorrer = "SELECT * FROM SorteosServicios WHERE HoraSorteo = '$Hora:00:00' AND DiaSorteo = '$Dia' AND Activo = 1";
endif;

$QRYSorteosACorrer = $dbo->query($SQLSorteosACorrer);

while($Sorteos = $dbo->fetchArray($QRYSorteosACorrer)):

    $IDClub = $Sorteos["IDClub"];

    $InfoToken = Token($IDClub);
    $Token = $InfoToken["response"][0]["Token"];   

    $ServicioSorteo = $Sorteos["IDServicio"];
    $ServicioReservas = $Sorteos["IDServicioAsociado"];
    $NumeroTurnos = $Sorteos["CantidadTurnos"];
    
    
    // SACAMOS LOS INCRITOS AL SERVICIO
    $SQLInscritos = "SELECT * FROM ReservaSorteo WHERE IDServicio = $ServicioSorteo AND Fecha > '$Fecha' $CondicionFecha ORDER BY Fecha ASC";
    $QRYInscritos = $dbo->query($SQLInscritos);
    while($Inscritos = $dbo->fetchArray($QRYInscritos)):
        $ArrayInscritos[$Inscritos["IDReservaSorteo"]] = $Inscritos;
        $ArraySorteados[$Inscritos["IDReservaSorteo"]] = 'N';
        $ArraySorteadoSocioFecha[$Inscritos["IDSocio"]][$Inscritos["Fecha"]] = 'N';
    endwhile;

    
    $ArrayInscritosValido = $ArrayInscritos;

    shuffle($ArrayInscritos);
    

    foreach($ArrayInscritos as $ID => $InfoInscrito):
        // BUSCAMOS LOS ELEMENTOS QUE TIENE
        $SQLElementos = "SELECT * FROM ReservaSorteoElemento WHERE IDReservaSorteo = $InfoInscrito[IDReservaSorteo]";
        $QRYElementos = $dbo->query($SQLElementos);
        while($Elemento = $dbo->fetchArray($QRYElementos)):
            $ArraySorteo[$Elemento["PosicionElemento"]][$InfoInscrito[Fecha]][] = $Elemento;            
            $ArrayElementoHoraSorteado[$Elemento["IDElemento"]][$Elemento["Hora"]][$InfoInscrito[Fecha]]["Tee1"] = 'N';            
            $ArrayElementoHoraSorteado[$Elemento["IDElemento"]][$Elemento["Hora"]][$InfoInscrito[Fecha]]["Tee10"] = 'N';  
            $ArrayFechas[$InfoInscrito[Fecha]] = $InfoInscrito[Fecha];          
        endwhile;        
    endforeach;

    sort($ArrayFechas); 

    $NumReserva = 1;

    $array_tee = array('Tee1','Tee10');
    
    foreach($array_tee as $Tee):
        foreach($ArrayFechas as $id => $Fecha):
            for($Posicion = 1; $Posicion <= $NumeroTurnos; $Posicion++):          
                foreach($ArraySorteo[$Posicion][$Fecha] as $id => $DatosSorteo):
                    if($ArraySorteadoSocioFecha[$ArrayInscritosValido[$DatosSorteo["IDReservaSorteo"]]["IDSocio"]][$Fecha] == 'N'):
                        if($ArraySorteados[$DatosSorteo["IDReservaSorteo"]] == 'N')://INSCRITO A SORTEO NO SE HA SORTEADO
                            if($ArrayElementoHoraSorteado[$DatosSorteo["IDElemento"]][$DatosSorteo["Hora"]][$Fecha][$Tee] == 'N')://ELEMENTO Y HORA NO SE HA ASIGNADO A UNA RESERVA
                                
                                $Invitados = array();
                                // INVITADOS
                                $SQLInvitados = "SELECT * FROM ReservaSorteoInvitado WHERE IDReservaSorteo = $DatosSorteo[IDReservaSorteo]";  
                                                
                                $QRYInvitados = $dbo->query($SQLInvitados);
                                while($Invitado = $dbo->fetchArray($QRYInvitados)):
        
                                    $Adicionales = array();
                                    
                                    $InfoInvitados["IDSocio"] = $Invitado["IDSocio"];
                                    $InfoInvitados["Nombre"] = $Invitado["Nombre"];
                                    $InfoInvitados["Correo"] = $Invitado["Correo"];
                                    $InfoInvitados["Cedula"] = $Invitado["Cedula"];
                                    $InfoInvitados["SeleccionadoGrupo"] = "";
                                    $InfoInvitados["Adicionales"] = $Adicionales;
        
                                    array_push($Invitados,$InfoInvitados);
                                endwhile;
        
                                $IDSocio = $ArrayInscritosValido[$DatosSorteo["IDReservaSorteo"]]["IDSocio"];  
                                $IDServicio = $ServicioReservas;    
                                
                                $IDElemento = $DatosSorteo["IDElemento"];  
                                
                                // BUSCAMOS EL ELEMENTO PAREJA DEL SERVICIO
                                $Identificador = $dbo->getFields("ServicioElemento","IdentificadorElemento","IDServicioElemento = $IDElemento");
                                $SQLElementoPareja = "SELECT * FROM ServicioElemento WHERE IdentificadorElemento = $Identificador AND IDClub = $IDClub AND IDServicioElemento <> $IDElemento";
                                $QRYElementoPareja = $dbo->query($SQLElementoPareja);
                                $DatoElemento = $dbo->fetchArray($QRYElementoPareja);
        
                                $IDElemento = $DatoElemento["IDServicioElemento"];
                                
                                $Hora = $DatosSorteo["Hora"];                            
                                $Invitados = json_encode($Invitados);

                                // VALIDAMOS QUE PARA LA FECHA Y LA HORA NO EXISTA UNA FECHA DE CIERRE

                                $SQLCierre = "SELECT * FROM ServicioCierre WHERE IDServicio = $IDServicio AND FechaInicio <= '$Fecha' AND FechaFin >= '$Fecha' AND HoraInicio <= '$Hora' AND HoraFin >= '$Hora' AND $Tee = 'S' AND IDServicioElemento LIKE '%$IDElemento%' AND CierrePorSorteo <> 1";
                                $QRYCierre = $dbo->query($SQLCierre);

                                if($dbo->rows($QRYCierre) <= 0):
        
                                    echo "<br>"; 
                                    echo "Numero Reserva: $NumReserva";
                                    echo "<br>";  
                                    echo "IDSocio: $IDSocio";
                                    echo "<br>";                    
                                    echo "IDServicio: $IDServicio";  
                                    echo "<br>";
                                    echo "IDElemento: $IDElemento";
                                    echo "<br>";
                                    echo "Fecha: $Fecha";
                                    echo "<br>";
                                    echo "Hora: $Hora";
                                    echo "<br>";
                                    echo "Tee: $Tee";
                                    echo "<br>";
                                    echo "Inivtados: $Inivtados";
                                    echo "<br>";
            
                                    $resp_reserva=ServicioCrearReserva($Token, $IDClub, $IDSocio, $IDServicio, $IDElemento, $Fecha, $Hora, $Invitados, $Tee);
                                    $array_resp_reserva=json_decode($resp_reserva);
                                    
                                    if($array_resp_reserva->success){
                                        echo "<br>Realizada "  .  $Fecha ." - ". $Hora ." - ". $Tee;
                                        $ArrayElementoHoraSorteado[$DatosSorteo["IDElemento"]][$DatosSorteo["Hora"]][$Tee] = 'S';
                                        $ArraySorteados[$DatosSorteo["IDReservaSorteo"]] = 'S';       
                                    }
                                    else{
                                        echo "<br>NO Realizada " .  $Fecha ." - ". $Hora ." - ". $Tee;
                                    }

            
                                    $NumReserva++;
                                    $UpdateRSorteo = "UPDATE ReservaSorteo  SET Sorteado = 1 WHERE IDReservaSorteo = $DatosSorteo[IDReservaSorteo]";
                                    $dbo->query($UpdateRSorteo);

                                endif;
                            endif;                              
                        endif;          
                    endif;   
                endforeach;
            endfor;
            $UltimaFecha = $Fecha;
        endforeach;
    endforeach;        
endwhile;

// BUSCAMOS LA FECHA DE CIERRE DEL SORTEO
echo "<br><br>";
echo $SQLCierre = "SELECT * FROM ServicioCierre WHERE CierrePorSorteo = 1 AND IDServicio = $ServicioReservas AND FechaInicio <= '$UltimaFecha' AND FechaFin >= '$UltimaFecha'";
$QRYCierre = $dbo->query($SQLCierre);
while($Dato = $dbo->fetchArray($QRYCierre)):
    //SUMAMOS UN DIA A LA ULTIMA FECHA
    $UltimaFecha = date("Y-m-d",strtotime($UltimaFecha . "+ 1 days")); 
    echo "<br><br>";
    echo $UpdateCierre = "UPDATE ServicioCierre SET FechaInicio = '$UltimaFecha' WHERE IDServicioCierre = $Dato[IDServicioCierre]";
    $dbo->query($UpdateCierre);
endwhile;


exit;

function ServicioCrearReserva($Token, $IDClub, $IDSocio, $IDServicio, $IDElemento, $Fecha, $Hora, $Invitados, $Tee)
{
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.miclubapp.com/services/club.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'action' => 'setreservageneral',
        'IDClub' => $IDClub,
        'IDSocio' => $IDSocio,
        'IDServicio' => $IDServicio,
        'IDElemento' => $IDElemento,
        'Fecha' => $Fecha,
        'Hora' => $Hora,
        'Tee' => $Tee,
        'Invitados' => $Invitados,
        'AppVersion' => '43',
        'Dispositivo' => 'iOS',
        'TipoApp' => 'Socio',
        'TokenID' => $Token),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
    echo "<br><br>";
    return $response;
}

function Token($IDClub)
{    

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.miclubapp.com/services/club.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'IDClub' => $IDClub,
        'action' => 'gettoken',
        'Usuario' => 'miempresa',
        'Clave' => '3empresa109#',
        'AppVersion' => '40'),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    // echo $response;
    $DATA = json_decode($response, true);
    return $DATA;

}

    










