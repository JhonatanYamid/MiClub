 <?php 
 require(dirname(__FILE__) . "/../../admin/config.inc.php");
  
  
//sacamos todos los datos de las reservaciones pagadas
$datos_respuestas = "SELECT * FROM  ReservaHotel WHERE IDClub=194 AND Pagado='S'"; 
                        $datos1 = $dbo->query($datos_respuestas);
                       
                        //se recorre y se hace la operacion de restar las noches
                        while ($row = $dbo->fetchArray($datos1)) {
                            $id = $row["IDReserva"];
                            $fecha1 = $row["FechaInicio"];
                            $fecha2 = $row["FechaFin"];
                            $IDSocio = $row["IDSocio"];
                            $IDHabitacion = $row["IDHabitacion"];
                            
$dias = (strtotime($fecha1)-strtotime($fecha2))/86400;
$dias = abs($dias);
$dias = floor($dias); 
$dias= ($dias);

                $noches = "SELECT * FROM SocioHabitacion WHERE IDHabitacion=$IDHabitacion and  IDSocio=$IDSocio ";
                $cantidadnoches = $dbo->query($noches);
                $cantidad = $dbo->fetchArray($cantidadnoches);
               $numeronoches= $cantidad["Noches"];
               if($numeronoches>0 and $dias<=$numeronoches){
 $total=($numeronoches-$dias);
 }else{
 $total=$numeronoches;
 }
 $actualizarnoches = "UPDATE SocioHabitacion SET  Noches=$total WHERE IDHabitacion='$IDHabitacion' and IDSocio='$IDSocio'";
                            $qryHabitaciones = $dbo->query($actualizarnoches); 
                         
                          
                        }
                        //fin actualizacion
                        
                        echo "actualizacion realizada!";
                        
                        
                        
                        ?>
