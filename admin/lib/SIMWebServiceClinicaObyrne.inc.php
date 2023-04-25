<?php

class SIMWebServiceClinicaObyrne 
{ 

    public function get_fechas_disponibles_servicio($IDClub, $IDServicio,$FechaBuscar)
    {
        $dbo = &SIMDB::get();
        //Busco el elemento debe exitir un unico elemnto y el identificador debe ser el id externo
        $sql_elem="SELECT IdentificadorElemento FROM ServicioElemento WHERE IDServicio = '".$IDServicio."' and  IdentificadorElemento <> 0 LIMIT 1";
        $r_elem=$dbo->query($sql_elem);
        $row_elem=$dbo->fetchArray($r_elem);

        if((int)$row_elem["IdentificadorElemento"]>0){
            $idprofesional=$row_elem["IdentificadorElemento"];
            $response = array();
            $Fechas=array();
            $MesesMostrar=1;
            $FechaActual = date("Y-m-d");
            //Temporal
          //  $FechaActual = "2023-02-16";
            $FechaInicio = strtotime($FechaActual);

            for($i=1;$i<=$MesesMostrar;$i++){
                $MesConsultar=date("m",$FechaInicio);
                $YearConsultar=date("Y",$FechaInicio);
            //    $FechaInicio = strtotime("+1 month",$FechaInicio);
                $curl = curl_init();    
                curl_setopt_array($curl, array(
                        CURLOPT_URL => URL_CLINICA . '/searchCalendar/a/'.CUENTA_CLINICA.'?token='.TOKEN_CLINICA.'&entity='.$idprofesional.'&year='.$YearConsultar.'&month='.$MesConsultar.'',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_POSTFIELDS => false,
                        CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));

                $response_ws = curl_exec($curl);
                curl_close($curl);  
                $json = json_decode($response_ws);
                foreach($json->calendar as $fecha => $datos_fecha){
                    
                    if($fecha>=$FechaActual){
                        if(!empty($FechaBuscar)){
                            if($fecha==$FechaBuscar)
                                $MostrarFecha="S";
                            else    
                                $MostrarFecha="S";
                        }
                        else{
                            $MostrarFecha="S";
                        }

                        if($MostrarFecha=="S"){                        
                            $InfoFechas[Fecha] = $fecha;
                            $InfoFechas[Activo] = "S";
                            $InfoFechas[FechaReservar] = $fecha;
                            $InfoFechas[HoraReservar] = "06:00:00";
                            $InfoFechas[GMT] = "-05:00";
                            $InfoFechas[TiempoRestanteDias] = 0;
                            $InfoFechas[TiempoRestanteHoras] = 0;
                            $InfoFechas[TiempoRestanteMinutos] = 0;
                            $InfoFechas[TiempoRestanteSegundos] = 0;
                            $InfoFechas[TiempoRestanteMiliSegundos] = 0; 
                            array_push($Fechas, $InfoFechas);
                        }
                    }
                }
            }

            $ConfigRespuesta[IDServicio] = $IDServicio;
            $ConfigRespuesta[IDClub] = $IDClub;
            $ConfigRespuesta[Nombre] = "";
            $ConfigRespuesta[Fechas] = $Fechas;

            array_push($response, $ConfigRespuesta);

            $respuesta[message] = "Encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $response;

        }
        else{
            $respuesta[message] = "Encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $response;  
        }

        

        return $respuesta;

    }

    public function tipo_reserva($IDClub,$IDServicio)
    {
        $dbo = &SIMDB::get();
        //Busco el elemento debe exitir un unico elemnto y el identificador debe ser el id externo
        $sql_elem="SELECT IdentificadorElemento FROM ServicioElemento WHERE IDServicio = '".$IDServicio."' and  IdentificadorElemento <> 0 LIMIT 1";
        $r_elem=$dbo->query($sql_elem);
        $row_elem=$dbo->fetchArray($r_elem);

        if((int)$row_elem["IdentificadorElemento"]>0){
            $idprofesional= $row_elem["IdentificadorElemento"];
            $response = array();
            $response_tiporeservas=array();
            $Fechas=array();
            $MesesMostrar=2;
            $FechaActual = date("Y-m-d");
            //Temporal
          //  $FechaActual = "2023-02-16";        

            $FechaInicio = strtotime($FechaActual);
            
            for($i=1;$i<=$MesesMostrar;$i++){
                $MesConsultar=date("m",$FechaInicio);
                $YearConsultar=date("Y",$FechaInicio);
                $FechaInicio = strtotime("+1 month",$FechaInicio);
                $curl = curl_init();    
                curl_setopt_array($curl, array(
                        CURLOPT_URL => URL_CLINICA . '/searchCalendar/a/'.CUENTA_CLINICA.'?token='.TOKEN_CLINICA.'&entity='.$idprofesional.'&year='.$YearConsultar.'&month='.$MesConsultar.'',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_POSTFIELDS => false,
                        CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));



                $response_ws = curl_exec($curl);
                curl_close($curl);  
                $json = json_decode($response_ws);
                foreach($json->calendar as $fecha => $datos_fecha){
                    if($fecha>=$FechaActual){
                        foreach($datos_fecha[0]->types as $fecha => $datos_tipo){      
                        
                          if($idprofesional == 9){ //arturo obyrne
                        if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                         }
                       }                              

                 if($idprofesional == 15){ //DRA. KATIA VILLALOBOS
                       if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80 || $datos_tipo->id == 54 || $datos_tipo->id == 131 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                         }
                    }          
                       if($idprofesional == 18){ //DRA.MARIA ISABEL GARCÍA
                       if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80 || $datos_tipo->id == 54 || $datos_tipo->id == 131 ||  $datos_tipo->id == 194){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                         }
                    }          
 
                             if($idprofesional == 16){ //DRA.DRA. LILIANA SALAZAR
                       if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80 || $datos_tipo->id == 54 || $datos_tipo->id == 131 ||  $datos_tipo->id == 194 ||  $datos_tipo->id == 156 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                         }
                    }          
                     if($idprofesional == 19){ //DRA. PATRICIA MARIA EUGENIA ORTEGA HERNANDEZ
                       if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80 || $datos_tipo->id == 54 || $datos_tipo->id == 131 || $datos_tipo->id ==194  ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                         }
                    }          
                     if($idprofesional == 31700){ //DRA.Sandra Hoyos Lopez
                       if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80 || $datos_tipo->id == 54 || $datos_tipo->id == 131 || $datos_tipo->id ==194  ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                         }
                    }      
                       if($idprofesional == 34906){ //DRA, OSCAR ANDRES CHAMORRO
                       if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80 || $datos_tipo->id == 54 || $datos_tipo->id == 131 || $datos_tipo->id ==194 ||  $datos_tipo->id ==156 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                         }
                    }          
 
                    if($idprofesional == 31684){ //DRA,  DRA. NATHALIA LÓPEZ
                       if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80 || $datos_tipo->id == 54 || $datos_tipo->id == 131 || $datos_tipo->id ==194 ||  $datos_tipo->id ==156 ||  $datos_tipo->id ==199  ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                         }
                    }        
                      if($idprofesional == 20){ //TATIANA SOSA   FALTA AGREGAR LOS IDS DE PANCE
                        if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       }    
                       
                       
                       //ENFERMERIA SAN FERNANDO 
                       
 
            
            
                            if($idprofesional == 29516 || $idprofesional == 29517 ||$idprofesional == 29518 || $idprofesional == 29519 || $idprofesional == 35132 || $idprofesional == 35133 || $idprofesional == 29511 || $idprofesional == 29512  ){ //SILLA 1,2,3,4,5,6 Y CAMILLA 1,2
                            
 
                        if($datos_tipo->id == 145 || $datos_tipo->id == 147 || $datos_tipo->id == 148 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       }    
                       
  
   
      if($idprofesional == 29514 || $idprofesional == 29515 ||$idprofesional == 35143 || $idprofesional == 35144 || $idprofesional == 35145){ // CAMILLA 3,4,5,6,7
                             
                        if($datos_tipo->id == 146 || $datos_tipo->id == 187 || $datos_tipo->id == 188 || $datos_tipo->id == 198 || $datos_tipo->id == 200  ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       } 
                       
                          
                       
                       
                       
        if($idprofesional == 35145){ // CAMILLA 7, ESPECIAL OTRO CAMILLA 7
                             
                        if($datos_tipo->id == 149 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       }  
                       
                       
                            if($idprofesional == 35180){ // ENFERMERIA - ESTECK
 
                        if($datos_tipo->id == 185 || $datos_tipo->id == 197 || $datos_tipo->id == 153 || $datos_tipo->id == 179 || $datos_tipo->id == 186  ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       } 
                       
                                         
        if($idprofesional == 35146){ // HIDROVITALIS
                             
                        if($datos_tipo->id == 192 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       }  
                       
                       
                     /*ENFERMERIA PANCE */
                     
                     if($idprofesional == 35173){ // PANCE SILLA 1
                             
                        if($datos_tipo->id == 145 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       }  
                     if($idprofesional == 35174){ // PANCE SILLA 2
                             
                        if($datos_tipo->id == 200 || $datos_tipo->id == 187 || $datos_tipo->id == 146 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       }  
                       
                       
                     if($idprofesional == 35175){ // PANCE SILLA 3
                             
                        if($datos_tipo->id == 198 || $datos_tipo->id == 188   ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       }  
                       
                       //
                       
                       if($idprofesional == 28816){ // PANCE ENFERMERIA 
                             
                        if($datos_tipo->id == 197 || $datos_tipo->id == 186 || $datos_tipo->id == 179  || $datos_tipo->id == 153 ){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                          }
                       }  
                       
                       
                       
                          if($idprofesional <= 0 ){ //POR DEFECTO
                      //  if($datos_tipo->id == 2 || $datos_tipo->id == 55 || $datos_tipo->id == 20 || $datos_tipo->id == 5 || $datos_tipo->id == 80){
                               
                            if(!in_array($datos_tipo->id,$array_insertados)){
                                //$tiporeserva["IDClub"] = $IDClub;
                                $tiporeserva["IDServicio"] = $IDServicio;
                                $tiporeserva["IDServicioTipoReserva"] = (string)$datos_tipo->id;
                                $tiporeserva["Nombre"] = $datos_tipo->name;
                                array_push($response_tiporeservas, $tiporeserva);    
                                $array_insertados[]=$datos_tipo->id;
                            } 
                       //   }
                       }      
                         
 
                    
                        }    
                    }
                }
            }

            return $response_tiporeservas;
        }
        return $response_tiporeservas;
    }

    public function get_disponibilidad_elemento_servicio($IDClub, $Fecha, $IDServicio,$IDTipoReserva)
    {

        $dbo = &SIMDB::get();
        //Busco el elemento debe exitir un unico elemnto y el identificador debe ser el id externo
        $sql_elem="SELECT IdentificadorElemento, Descripcion FROM ServicioElemento WHERE IDServicio = '".$IDServicio."' and  IdentificadorElemento <> 0 LIMIT 1";
        $r_elem=$dbo->query($sql_elem);
        $row_elem=$dbo->fetchArray($r_elem);
        $IDElemento=$row_elem["IdentificadorElemento"];
        $idprofesional= $row_elem["IdentificadorElemento"];
        
         if($row_elem["Descripcion"] == "SAN FERNANDO"):
         $puntoservicio=1;
         elseif($row_elem["Descripcion"] == "PANCE"):
         $puntoservicio=2; 
         else:
         $puntoservicio=0; 
         endif; 
        $tipocita=$IDTipoReserva;
        $turnos=2;
 
        

//medicas san fernando
if($IDTipoReserva == 191):
$duracion=20;
elseif($IDTipoReserva == 205):
$duracion=15;
elseif($IDTipoReserva == 208):
$duracion=15;
elseif($IDTipoReserva == 206):
$duracion=15;
elseif($IDTipoReserva == 207):
$duracion=15;
elseif($IDTipoReserva == 209):
$duracion=20;
elseif($IDTipoReserva == 210):
$duracion=15;
elseif($IDTipoReserva == 211):
$duracion=40;
elseif($IDTipoReserva == 212):
$duracion=20;

//enfermeria san fernando 
elseif($IDTipoReserva == 145):
$duracion=60;
elseif($IDTipoReserva == 147):
$duracion=30;
elseif($IDTipoReserva == 148):
$duracion=30;
elseif($IDTipoReserva == 146):
$duracion=45;
elseif($IDTipoReserva == 200):
$duracion=60;
elseif($IDTipoReserva == 187):
$duracion=60;
elseif($IDTipoReserva == 188):
$duracion=60;
elseif($IDTipoReserva == 198):
$duracion=15;
// rectal - vaginal uretral
elseif($IDTipoReserva == 149):
$duracion=120;
elseif($IDTipoReserva == 185):
$duracion=30;
elseif($IDTipoReserva == 197):
$duracion=15;
elseif($IDTipoReserva == 179):
$duracion=15;
elseif($IDTipoReserva == 186):
$duracion=45;
elseif($IDTipoReserva == 153):
$duracion=15;
elseif($IDTipoReserva == 192):
$duracion=40;


//medicas pance
elseif($IDTipoReserva == 191):
$duracion=20;
elseif($IDTipoReserva == 205):
$duracion=15;
elseif($IDTipoReserva == 208):
$duracion=15;
elseif($IDTipoReserva == 206):
$duracion=15;
elseif($IDTipoReserva == 207):
$duracion=15;
elseif($IDTipoReserva == 209):
$duracion=20;
elseif($IDTipoReserva == 210):
$duracion=15;
elseif($IDTipoReserva == 211):
$duracion=40;
elseif($IDTipoReserva == 212):
$duracion=20;

//enfermeria pance
elseif($IDTipoReserva == 145):
$duracion=60;
elseif($IDTipoReserva == 147):
$duracion=30;
elseif($IDTipoReserva == 148):
$duracion=30;
elseif($IDTipoReserva == 146):
$duracion=45;
elseif($IDTipoReserva == 200):
$duracion=60;
elseif($IDTipoReserva == 187):
$duracion=60;
elseif($IDTipoReserva == 188):
$duracion=60;
elseif($IDTipoReserva == 198):
$duracion=15;
// rectal - vaginal uretral
elseif($IDTipoReserva == 149):
$duracion=120;
elseif($IDTipoReserva == 185):
$duracion=30;
elseif($IDTipoReserva == 197):
$duracion=15;
elseif($IDTipoReserva == 179):
$duracion=15;
elseif($IDTipoReserva == 186):
$duracion=45;
elseif($IDTipoReserva == 153):
$duracion=15;
elseif($IDTipoReserva == 192):
$duracion=40;



endif;
 
 
        $response = array();        
        $Disponibilidad = array();        
        $DisponibilidadFinal = array();    
        $Cuenta = 0;
 
  
            $response_tiporeservas=array();
            $Fechas=array();
            $MesesMostrar=1;
            $FechaActual = date("Y-m-d");
            //Temporal
          //  $FechaActual = "2023-02-16";        
            $FechaInicio = strtotime($FechaActual);
            
            for($i=1;$i<=$MesesMostrar;$i++){
                $MesConsultar=date("m",$FechaInicio);
                $YearConsultar=date("Y",$FechaInicio);
                $FechaInicio = strtotime("+1 month",$FechaInicio);
                $curl = curl_init();    
                curl_setopt_array($curl, array(
                        CURLOPT_URL => URL_CLINICA . '/searchCalendar/a/'.CUENTA_CLINICA.'?token='.TOKEN_CLINICA.'&entity='.$idprofesional.'&year='.$YearConsultar.'&month='.$MesConsultar.'',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_POSTFIELDS => false,
                        CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));



                $response_ws = curl_exec($curl);
                curl_close($curl);  
                $json = json_decode($response_ws);
                foreach($json->calendar as $fecha => $datos_fecha){
                  if($fecha==$Fecha){
                  $total= count($datos_fecha);
                  for($i=0; $i<$total; $i++):
                  
                  $hora_fija= date("Y-m-d H:i:s");
//$hora_fija= "02:30:00";
            
 
               
                  $array_fecha=explode(" ",$datos_fecha[$i]->date);
                    $FechaServicio=$array_fecha[0];
                    $HoraServicio=$array_fecha[1];
                  $datos_fecha[$i]->id; 
                  $actualidad= date("Y-m-d");
                if ($FechaServicio>=$actualidad){ 
                 if ($datos_fecha[$i]->date >= $hora_fija){
                  foreach($datos_fecha[$i]->types as $fecha => $datos_tipo){                            
                            if($datos_tipo->id == $IDTipoReserva):
                              $Mostrar="S";
                              $EstadoTurno="disponible";
                              $info= $datos_tipo->id;
                               $info_name= $datos_tipo->id;
                            
                            endif;
                            
                            
                            }
                   
                    }
                }   
                else{
                    $Mostrar="N";
                    $EstadoTurno="no disponible";
                }
                       
                         if($Mostrar=="S"): 
                    $InfoDisponibilidad[Hora] = $HoraServicio;
                    $InfoDisponibilidad[GMT] = "-05:00";
 

                    if($EstadoTurno == "disponible"):
                        $Disponible = "S";
                        $Socio = ""; 
                        $IDSocio = ""; 
                        $IDReserva = ""; 
                        $IDSocioBeneficiario = ""; 
                        $LabelDisponible = "Disponible";
                    else:
                        $Disponible = "N";
                        $Socio = ""; 
                        $IDSocio = "";
                        $IDReserva = "";
                        $IDSocioBeneficiario = "";
                        $LabelDisponible = "No disponible";
                    endif;

                    $InfoDisponibilidad[Disponible] = $Disponible;
                    $InfoDisponibilidad[Socio] = $Socio;
                    $InfoDisponibilidad[IDSocio] = $IDSocio;
                    $InfoDisponibilidad[ModalidadEsquiSocio] = "";
                    $InfoDisponibilidad[IDReserva] = $IDReserva;
                    $InfoDisponibilidad[IDSocioBeneficiario] = $IDSocioBeneficiario;
                    $InfoDisponibilidad[MaximoPersonaTurno] = "0";
                    $InfoDisponibilidad[NumeroInvitadoClub] = "0";
                    $InfoDisponibilidad[NumeroInvitadoExterno] = "0";
                    $InfoDisponibilidad[NumeroMinimoInvitadoClub] = "0";
                    $InfoDisponibilidad[NumeroMinimoInvitadoExterno] = "0";
                    $InfoDisponibilidad[IDDisponibilidad] = "";
                    $InfoDisponibilidad[PermiteRepeticion] = "N";
                    $InfoDisponibilidad[MedicionRepeticion] = "";
                    $InfoDisponibilidad[FechaFinRepeticion] = "";
                    $InfoDisponibilidad[Georeferenciacion] = "N";
                    $InfoDisponibilidad[Latitud] = "0";
                    $InfoDisponibilidad[Longitud] = "0";
                    $InfoDisponibilidad[Rango] = "0";
                    $InfoDisponibilidad[MensajeFueraRango] = "";
                    $InfoDisponibilidad[LabelDisponible] = $LabelDisponible;
                    $InfoDisponibilidad[IDElemento] = (string)$datos_fecha[$i]->id;
                    $InfoDisponibilidad[NombreElemento] = "Turno";
                    $InfoDisponibilidad[IDUsuario] = "";
                    $InfoDisponibilidad[PermiteReservarUsuario] = "N";
                    $InfoDisponibilidad[ColorLetra] = "#000000";
                    $InfoDisponibilidad[ColorFondo] = "#ffffff";
                    $InfoDisponibilidad[Foto] = "";
                    $InfoDisponibilidad[ModalidadElemento] = "";
                    $InfoDisponibilidad[MaximoInvitadosSalon] = "0";
                    $InfoDisponibilidad[OrdenElemento] = "";
                    $InfoDisponibilidad[PermiteListaEspera] = "N";
                    $InfoDisponibilidad[LabelTituloHora] = "";
                    $InfoDisponibilidad[MostrarBotonCumplida] = "S";
                    $InfoDisponibilidad[IDAuxiliar] = "";
                    $InfoDisponibilidad[MostrarBotonInscritos] = "N";
                    $InfoDisponibilidad[LabelBotonInscritos] = "";
                    $InfoDisponibilidad[Inscritos] = [];

                    array_push($Disponibilidad, $InfoDisponibilidad);
                
                $Cuenta++;
            endif;
 
             
                  
                  endfor;
 
        array_push($DisponibilidadFinal, $Disponibilidad);
 
        $ConfigRespuesta[IDClub] = $IDClub;
        $ConfigRespuesta[IDServicio] = $IDServicio;
        $ConfigRespuesta[Fecha] = $FechaServicio;
        $ConfigRespuesta[Disponibilidad] = $DisponibilidadFinal;
        $ConfigRespuesta[name] = "";
        
        array_push($response, $ConfigRespuesta);
        
        $respuesta[message] = "Encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;
                  
                  
                    }
                    }
                    }
                    
                    

        return $respuesta;
 

    }
      public function Crear_cita($tipocita, $puntoservicio, $turno, $telefono, $identificacion, $reservacion)
    {
        $curl = curl_init();
  
  

        $POST = 'token='.TOKEN_CLINICA.'&point='.$puntoservicio.'&slot='.$turno.'&type='.$tipocita.'&phone='.$telefono.'&identity='.$identificacion.'&reservation=0'; 
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(

        CURLOPT_URL =>  URL_CLINICA . '/appointment/a/'.CUENTA_CLINICA.'?'.$POST,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Cookie: pms-symfony=ir31gsakjmfk5cp3dnk3lq00n1'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
         $json = json_decode($response);
 
                    
                      if ($json->message == "Slot created successfully" ):
            
                    $respuesta["message"] = "Reserva creada correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;	
            
                elseif($json->message == "El turno ya fue ocupado"):
            
                    $respuesta["message"] = "Lo sentimos, el turno ya fue ocupado";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;	
                    		
                else:
                
                    $respuesta["message"] = $json->message;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;	
                    		
                endif;
                
                
        return  $respuesta;
    }
    
     public function Consultar_cita($IDClub, $datos_socio)
    {   
        $response_datos=array();
        $identificacion=$datos_socio["NumeroDocumento"];
        $points=1;
        while ($points<=2): 
          $curl = curl_init();    
                curl_setopt_array($curl, array(
                        CURLOPT_URL => URL_CLINICA . '/appointment/a/'.CUENTA_CLINICA.'?token='.TOKEN_CLINICA.'&identity='.$identificacion.'&point='.$points.'&last_apt=0',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_POSTFIELDS => false,
                        CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));
 
                $response_ws = curl_exec($curl);
                curl_close($curl);  
                $json1 = json_decode($response_ws); 
        $ConReserva="N";
        
        
        foreach($json1 as $json){
   foreach($json as $datos_reserva):
            (string)$datos_reserva->id;
            (string)$datos_reserva->date;
 
            $ConReserva="S";
            
            $Hora=$datos_reserva->horainicial;
            $array_hora=explode(" ",$datos_reserva->date);
            $Hora=$array_hora[1]; 
          /*  $Hora = substr($Hora, 0, -3);
            $Horario = substr($Hora, 0, -3);
            if($Horario>=12):
            $Hora=$Hora." pm";
            
            else:
            
            $Hora=$Hora." am";
            endif; */
            $Fecha=$array_hora[0]; 
            //valores fijos de pruebas 
 
            $ConfigRespuesta[IDClub] = $IDClub;
            $ConfigRespuesta[IDSocio] = $datos_socio["IDSocio"];
            $ConfigRespuesta[Socio] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
            $ConfigRespuesta[IDReserva] = (string)$datos_reserva->id."|".$datos_reserva->point."|".$datos_reserva->attId;
            $ConfigRespuesta[IDServicio] = "131";
            $ConfigRespuesta[Icono] = "";
            $ConfigRespuesta[NombreServicio] = $datos_reserva->name.  " ";
            $ConfigRespuesta[NombreServicioPersonalizado] = $datos_reserva->name .  " ";
            $ConfigRespuesta[IDElemento] = "";
            $ConfigRespuesta[NombreElemento] = $datos_reserva->point ." ";
            $ConfigRespuesta[Fecha] = (string)$Fecha;
            $ConfigRespuesta[Tee] = "";
            $ConfigRespuesta[CantidadInvitadoSalon] = "";
            $ConfigRespuesta[PagadaOnline] = "";
            $ConfigRespuesta[FechaTransaccion] = "";
            $ConfigRespuesta[IDServicioTipoReserva] = $datos_reserva->attId .  "";
            $ConfigRespuesta[MensajeTransaccion] = "";
            $ConfigRespuesta[LabelElementoSocio] = "";
            $ConfigRespuesta[LabelElementoExterno] ="";
            $ConfigRespuesta[PermiteEditarAuxiliar] = "N";
            $ConfigRespuesta[PermiteEditarAdicionales] = "";
            $ConfigRespuesta[PermiteListaEsperaAuxiliar] = "";
            $ConfigRespuesta[MultipleAuxiliar] = "";
            $ConfigRespuesta[LabelReconfimarBoton] = "";
            $ConfigRespuesta[PermiteReconfirmar] = "";
            $ConfigRespuesta[LabelInvitados] = "Agregar Acompañante";
            $ConfigRespuesta[AdicionalesObligatorio] = "";
            $ConfigRespuesta[TextoLegal] = "";
            $ConfigRespuesta[OcultarBotonEditarInvitados] = "S";
            $ConfigRespuesta[LabelElemento] = "";
            $ConfigRespuesta[OcultarHora] = "";
            $ConfigRespuesta[PermiteInvitadoExternoCedula] = "";
            $ConfigRespuesta[PermiteInvitadoExternoCorreo] = "";
            $ConfigRespuesta[PermiteInvitadoExternoFechaNacimiento] = "";
            $ConfigRespuesta[InvitadoExternoPago] = "";
            $ConfigRespuesta[LabelInvitadoExternoPago] = "";
            $ConfigRespuesta[InvitadoExternoValor] = "";
            $ConfigRespuesta[EliminarParaTodosOParaMi] = "";
            $ConfigRespuesta[MensajeEliminarParaTodosOParaMi] = "";
            $ConfigRespuesta[BotonEliminarReserva] = "";
            $ConfigRespuesta[LabelEliminarParaMi] = "";
            $ConfigRespuesta[LabelEliminarParaTodos] = "";
            $ConfigRespuesta[CamposDinamicosInvitadoExternoHabilitado] = "";
            $ConfigRespuesta[BotonEditarAdicionales] = "";
            $ConfigRespuesta[LabelAdicionales] = "";
            $ConfigRespuesta[EncabezadoAdicionales] = "";
            $ConfigRespuesta[LabelSeleccioneAdicionales] = "";
            $ConfigRespuesta[MensajeAdicionalesObligatorio] = "";
            $ConfigRespuesta[PermiteEditarReserva] = "";
            $ConfigRespuesta[PermiteAdicionarCaddies] = "";
            $ConfigRespuesta[LabelAdicionarCaddies] = "";
            $ConfigRespuesta[ObligatorioSeleccionarCaddie] = "";
            $ConfigRespuesta[MensajeCaddiesObligatorio] = "";
            $ConfigRespuesta[LabelTicketsDescuento] = "";
            $ConfigRespuesta[TipoBotonInvitacion] = "";
            $ConfigRespuesta[LabelAuxiliar] = "";
            $ConfigRespuesta[ListaAuxiliar] = array();
            $ConfigRespuesta[Hora] = $Hora;
            $ConfigRespuesta[GMT] = "-05:00";
            $ConfigRespuesta[HoraFin] = "";
            $ConfigRespuesta[NumeroInvitadoClub] = "";
            $ConfigRespuesta[NumeroInvitadoExterno] = "";
            $ConfigRespuesta[Beneficiario] = "";            
            $ConfigRespuesta[Invitados] = array();
            $ConfigRespuesta[ReservaAsociada] = array();
            $ConfigRespuesta[Adicionales] = array();
            $ConfigRespuesta[CamposReserva] = array();
            $ConfigRespuesta[idTipoReserva] = (string)$datos_reserva->id;
            $ConfigRespuesta[idSubTipoReserva] = "";
            array_push($response_datos, $ConfigRespuesta);
            
            endforeach;
        }
        $points++;
        endwhile;
        
        if($ConReserva=="S"){
            $respuesta[message] = "Encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $response_datos;
        }
        else{
            $reserva["IDClub"] = "";
            $reserva["IDSocio"] = "";
            $reserva["IDReserva"] = "";
            $reserva["IDServicio"] = "";
            $id_servicio_maestro = "";
            $reserva["NombreServicio"] = "";
            $reserva["IDElemento"] = "";
            $reserva["NombreElemento"] = "";
            $reserva["Fecha"] = "";
            $reserva["Tee"] = "";
            array_push($response_datos, $reserva);
            $respuesta["message"] = "No tienes citas programadas.";
            $respuesta["success"] = true;
            $respuesta["response"] = $response_datos;
        }


        return $respuesta;



        //return (array)$DATOS;
    }

      public function Eliminar_cita($tipocita, $puntoservicio, $turno, $opcion, $identificacion)
    {
        $curl = curl_init();
  
  

        $POST = 'token='.TOKEN_CLINICA.'&point='.$puntoservicio.'&slot='.$turno.'&type='.$tipocita.'&modify=1&identity='.$identificacion.'&reason=5'; 
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(

        CURLOPT_URL =>  URL_CLINICA . '/appointment/a/'.CUENTA_CLINICA.'?'.$POST,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_HTTPHEADER => array(
            'Cookie: pms-symfony=ir31gsakjmfk5cp3dnk3lq00n1'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
         $json = json_decode($response);
 
                    
                      if ($json->message == "Slot canceled" ):
            
                    $respuesta["message"] = "Reserva eliminada correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null; 
                else:
                    $respuesta["message"] = $POST;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;	
                    		
                endif;
                
                
        return  $respuesta;
    }
    
    
 

     public function Consultar_paciente($documento)
    {   
        $response_datos=array();

          $curl = curl_init();    
                curl_setopt_array($curl, array(
                        CURLOPT_URL => URL_CLINICA_PACIENTES . '/patient/a/'.CUENTA_CLINICA.'/'.$documento.'?token='.TOKEN_CLINICA,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_POSTFIELDS => false,
                        CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));
 
                $response_ws = curl_exec($curl);
                curl_close($curl);  
 
                return $response_ws;
                }
}




