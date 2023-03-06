<?php
include("../../config.inc.php");
   
    $conexion2=mysql_connect("localhost","almacenescaprino","c4prin0") or die("Problemas en la conexion2");
    mysql_select_db("Caprino",$conexion2) or die("Problemas en la selección de la base de datos");
     
	 
	 $QryUp = $dbo->query( "UPDATE Producto SET Publicar = 'S'" );

$insert_count = 0;
$delete_count = 0;
$update_count = 0;

//$QryProdu = $dbo->query( "SELECT * FROM Producto WHERE IDCategoria not in ('25','33')" );
$QryCountProdu = $dbo->query( "SELECT * FROM Producto WHERE 1" );
$total = $dbo->rows( $QryCountProdu );
$sub = $total/4;

//echo "SELECT * FROM Producto WHERE 1 LIMIT ".round($sub).",".$total;
echo "SELECT * FROM Producto WHERE 1 LIMIT ".round($sub * 3).",".$total;


$QryProdu = $dbo->query( "SELECT * FROM Producto WHERE 1 LIMIT ".round($sub * 3).",".$total);




if( $dbo->rows( $QryProdu ) > 0 )
{
	while( $ArrayQryGEO = $dbo->fetchArray( $QryProdu ) )
	{
            	$QryProduColor = '';
		 
             //   if($ArrayQryGEO["IDCategoria"] != 25 && $ArrayQryGEO["IDCategoria"] != 33)
             //   {
                 /*dfssdfsd*/
                $ArrayQryGEO[IDProducto];
			$QryProduColor = $dbo->query( "SELECT * FROM ProductoColor WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."'" );
			
                      
                        if( $dbo->rows( $QryProduColor ) > 0 )
			{   
                                $precio = '';
				$referencia = "";
				while( $ArrayProduColor = $dbo->fetchArray( $QryProduColor ) )
				{
					//$ArrayProduColor["Nombre"] = SIMUtil::antiinjection($ArrayProduColor["Nombre"]);
				$referencia = str_replace(" ","",str_replace("-","",$ArrayProduColor[Nombre]));	
				
                                if($referencia != "")
                                {  
                              $SqlPrecioNuevo = "SELECT Re.IDPrecio, Re.Saldo, Re.IDColor
                                FROM 
                                Referencia as Re 

                                WHERE 
                                Re.Numero LIKE '".$referencia."%'
                                AND Re.Publicar = 'S'
                               ";  
                                $QryPrecioNuevo = mysql_query( $SqlPrecioNuevo ,$conexion2) or die("Problemas en conexion2:".mysql_error());
                                $PrecioNuevo = mysql_fetch_array( $QryPrecioNuevo );
                                //$saldo_new = $PrecioNuevo["Saldo"];
                                 $precio = $PrecioNuevo["IDPrecio"]."<br />";      

                                        $QryPrecio = mysql_query( "SELECT ValorVenta, Descuento FROM Precio WHERE IDPrecio = '".$precio."'" ,$conexion2) or die("Problemas en conexion2:".mysql_error());
                                        $PrecioArray = mysql_fetch_array( $QryPrecio );
                                        $precio_anterior =  $PrecioArray["ValorVenta"];

                                        if($PrecioArray["Descuento"] != 0)
                                            $PrecioArray["ValorVenta"] = $PrecioArray["ValorVenta"]-(($PrecioArray["ValorVenta"]*$PrecioArray["Descuento"])/100);     
                                
                                        $QryColorete = mysql_query( "SELECT DescripcionLarga FROM Color WHERE IDColor = '".$PrecioNuevo["IDColor"]."'" ,$conexion2) or die("Problemas en conexion2:".mysql_error());
                                        $ColoreteArray = mysql_fetch_array( $QryColorete );
                                        
                                $QryProdu_Color_Actualizar = $dbo->query( "UPDATE ProductoColor SET Color = '".$ColoreteArray["DescripcionLarga"]."', PrecioAnterior = '".$precio_anterior."', ValorUnitario = '".$PrecioArray["ValorVenta"]."', Descuento = '".$PrecioArray["Descuento"]."', FechaProceso = '".date("Y-m-d h:i:s")."' WHERE IDColor = '".$ArrayProduColor[IDColor]."'");				
		                
                                
                                     $QryTalla = '';
					
					$saldo_new = '';
                                        $precio =  '';
                                        
					$QryTalla = $dbo->query( "SELECT * FROM Talla WHERE Publicar = 'S'" );
					if( $dbo->rows( $QryTalla ) > 0 )
					{
						$talla = '';
						
						while( $ArrayProduColorTalla = $dbo->fetchArray( $QryTalla ) )
						{
							$talla = $ArrayProduColorTalla[Nombre];
							
					
					
						$SqlPuntoVenta = "";
						$QryPuntoVenta = "";
						$SqlPuntoVenta = "SELECT IDPuntoVenta FROM PuntoVenta WHERE Publicar = 'S'
						AND IDPuntoVenta in ('1','10','17','4','6','29','28','22','15','27','26','7','13','3','8','5','9')
						
						";
                                                
                                                $array_puntos_de_venta = array(1=>1,10=>10,17=>17,4=>4,6=>6,29=>29,28=>28,22=>22,15=>15,27=>27,26=>26,7=>7,13=>13,3=>3,8=>8,5=>5,9=>9);
                                                
						//$QryPuntoVenta = mysql_query( $SqlPuntoVenta ,$conexion2) or die("Problemas en conexion2:".mysql_error());
						$listo = 0;
						if( sizeof( $array_puntos_de_venta ) > 0 )
						{
							
							 $SqlExistencias = "";
							$listo = 0;
                                                        $unidades = 0;
							foreach($array_puntos_de_venta as $key_puntos_de_venta => $datos_puntos_de_venta)
							{
                                                            $puntos["IDPuntoVenta"] = $key_puntos_de_venta;
                                                            
                                                            
							 $SqlExistencias = "SELECT Re.Numero as NumeroRe, Ta.Nombre as NombreTa, CodEsp.Existencias as ExistenciasCodEsp, CodEsp.Maximo as MaximoCodEsp, CodEsp.Minimo as MinimoCodEsp, Re.IDPrecio, Re.Saldo
                                                        FROM 
                                                        Referencia as Re, 
                                                        CodificacionEspecifica as CodEsp, 
                                                        Talla as Ta,
                                                        PuntoVentaReferencia as PunVeRe 
                                                        WHERE 
                                                        Re.IDReferencia = PunVeRe.IDReferencia
                                                        AND '".$puntos["IDPuntoVenta"]."' = PunVeRe.IDPuntoVenta
                                                        AND CodEsp.IDPuntoVentaReferencia = PunVeRe.IDPuntoVentaReferencia
                                                        AND Ta.IDTalla = CodEsp.IDTalla
                                                        AND Re.Numero LIKE '".$referencia."%'
                                                        AND Ta.Nombre = '".$talla."'

                                                        ";
                                                        //AND Re.Saldo = 'N'
                                                        $Existencias = "";
                                                        $QryExistencias = "";
                                                        $QryInsert = "";
                                                        $QryInsert2 = "";
                                                       
                                                        
                                                      
								$QryExistencias = mysql_query( $SqlExistencias ,$conexion2) or die("Problemas en conexion2:".mysql_error());
								if(mysql_num_rows( $QryExistencias ) > 0)
								{
								
                                                                    
									$Existencias = mysql_fetch_array( $QryExistencias );
                                	
									if($precio == '')
									{
										$precio = $Existencias["IDPrecio"];
										
									}//END IF PRECIO
                                                                        
									if($saldo_new == '')
									{
										$saldo_new = $Existencias["Saldo"];
										
									}//END IF SALDO
								
                                                                        if($Existencias["ExistenciasCodEsp"] > 0)
									{
                                                                        $unidades= $unidades + $Existencias["ExistenciasCodEsp"];
                                                                         $Existencias["ExistenciasCodEsp"];
                                                                       }//END IF EXISTENCIAS
									else if($Existencias["ExistenciasCodEsp"] <= 1)
									{
									
										if($listo == 1)
											$listo = 1;
										else
											$listo = 0;
									}//END IF NO EXISTENCIAS
                                                                        if($unidades > 2)
									{
                                                                                if($listo == 0)
                                                                                    $listo = 1;
									}
								}//END IF GRANDE
					 }////END WHILE PUNTOS DE VENTA
							 
						//	echo $unidades;
                                                    
                                                                if($listo == 1)
								{
										$QryInsert = $dbo->query( "SELECT * FROM TallaProductoColor WHERE IDTalla = '".$ArrayProduColorTalla[IDTalla]."' AND IDColor = '".$ArrayProduColor[IDColor]."' AND IDProducto = '".$ArrayQryGEO[IDProducto]."'" );
										if( $dbo->rows( $QryInsert ) == 0 )
										{
											//echo "Inserto Producto: ".$ArrayQryGEO[IDProducto]." Talla: ".$ArrayProduColorTalla[IDTalla]." Color: ".$ArrayProduColor[IDColor];
											$insert_count++;
											$dbo->query( "INSERT INTO TallaProductoColor (IDTalla,IDColor,IDProducto) VALUES ('".$ArrayProduColorTalla[IDTalla]."','".$ArrayProduColor[IDColor]."','".$ArrayQryGEO[IDProducto]."')" );
										}
									
									
								}
								else
								{
									$QryInsert2 = $dbo->query( "SELECT * FROM TallaProductoColor WHERE IDTalla = '".$ArrayProduColorTalla[IDTalla]."' AND IDColor = '".$ArrayProduColor[IDColor]."' AND IDProducto = '".$ArrayQryGEO[IDProducto]."'" );
										if( $dbo->rows( $QryInsert2 ) > 0 )
										{
											//echo "Elimino Producto: ".$ArrayQryGEO[IDProducto]." Talla: ".$ArrayProduColorTalla[IDTalla]." Color: ".$ArrayProduColor[IDColor];
											$delete_count++;
											$dbo->query( "DELETE FROM TallaProductoColor WHERE IDTalla = '".$ArrayProduColorTalla[IDTalla]."' AND IDColor = '".$ArrayProduColor[IDColor]."' AND IDProducto = '".$ArrayQryGEO[IDProducto]."'" );
										}
								}
							
							
						}//END IF HAY PUNTOS DE VENTA
					
					
					
                                                                       
					
							
						}//while
                                                
                                                
                                                 
                                                
					}//if talla
					
			
                                }//if existe 
					
					
				}//while
			}//if	
			
                        
                        
                        
                        
                        
                        
                        
                        /*dfssdfsd*/
                        
                        
            //    }
                        
                   $SqlPrecioNuevo = "SELECT Re.IDPrecio, Re.Saldo
			FROM 
			Referencia as Re 
			
			WHERE 
			Re.Numero LIKE '".$ArrayQryGEO["Nombre"]."%'
                        AND Re.Publicar = 'S'
			";
                                        $QryPrecioNuevo = mysql_query( $SqlPrecioNuevo ,$conexion2) or die("Problemas en conexion2:".mysql_error());
                                        $PrecioNuevo = mysql_fetch_array( $QryPrecioNuevo );
			//$saldo_new = $PrecioNuevo["Saldo"];
                       $precio = $PrecioNuevo["IDPrecio"];      
                       
                         
			$QryPrecio = mysql_query( "SELECT ValorVenta, Descuento FROM Precio WHERE IDPrecio = '".$precio."'" ,$conexion2) or die("Problemas en conexion2:".mysql_error());
			$PrecioArray = mysql_fetch_array( $QryPrecio );
			$precio_anterior =  $PrecioArray["ValorVenta"];
                        
			if($PrecioArray["Descuento"] != 0)
			{
                            $PrecioArray["ValorVenta"] = $PrecioArray["ValorVenta"]-(($PrecioArray["ValorVenta"]*$PrecioArray["Descuento"])/100); 
                            
			}               
                            
			$QryProdu_Estado = $dbo->query( "SELECT * FROM TallaProductoColor WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."'");
			if( $dbo->rows( $QryProdu_Estado ) > 0 )
			{	//echo "UPDATE Producto SET Publicar = 'S', ValorUnitario = '".$PrecioArray["ValorVenta"]."', Descuento = '".$PrecioArray["Descuento"]."', Saldos = '".$saldo_new."', FechaProceso = '".date("Y-m-d h:i:s")."' WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."' <br />";
				$update_count++;
                               //  echo "UPDATE Producto SET Publicar = 'S', PrecioAnterior = '".$precio_anterior."', ValorUnitario = '".$PrecioArray["ValorVenta"]."', Descuento = '".$PrecioArray["Descuento"]."', Saldos = '".$saldo_new."', FechaProceso = '".date("Y-m-d h:i:s")."' WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."'<br />";
				$QryProdu_Estado_Actualizar = $dbo->query( "UPDATE Producto SET Publicar = 'S', PrecioAnterior = '".$precio_anterior."', ValorUnitario = '".$PrecioArray["ValorVenta"]."', Descuento = '".$PrecioArray["Descuento"]."', Saldos = '".$saldo_new."', FechaProceso = '".date("Y-m-d h:i:s")."' WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."'");				
			}
			else
			{
				//echo "UPDATE Producto SET Publicar = 'N', ValorUnitario = '".$PrecioArray["ValorVenta"]."', Descuento = '".$PrecioArray["Descuento"]."', Saldos = '".$saldo_new."', FechaProceso = '".date("Y-m-d h:i:s")."' WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."' <br />";
				$update_count++;
                      //  echo "UPDATE Producto SET Publicar = 'N', PrecioAnterior = '".$precio_anterior."', ValorUnitario = '".$PrecioArray["ValorVenta"]."', Descuento = '".$PrecioArray["Descuento"]."', Saldos = '".$saldo_new."', FechaProceso = '".date("Y-m-d h:i:s")."' WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."' <br />";
					$QryProdu_Estado_Actualizar = $dbo->query( "UPDATE Producto SET Publicar = 'N', PrecioAnterior = '".$precio_anterior."', ValorUnitario = '".$PrecioArray["ValorVenta"]."', Descuento = '".$PrecioArray["Descuento"]."', Saldos = '".$saldo_new."', FechaProceso = '".date("Y-m-d h:i:s")."' WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."'");	
			}
			
			
					
		
	}//while
}//if


	// $QryUp = $dbo->query( "UPDATE Producto SET Publicar = 'N' WHERE Saldos = 'S'" );

echo "INSERT: ". $insert_count."<br />";
echo "DELETE: ". $delete_count."<br />";
echo "UPDATE: ". $update_count."<br />";

mysql_close ($conexion2);


$QryProdu = $dbo->query( "SELECT * FROM Producto WHERE IDProducto not in (SELECT IDProducto FROM ProductoColor as a WHERE a.IDProducto = Producto.IDProducto)" );
if( $dbo->rows( $QryProdu ) > 0 )
{
	while( $ArrayQryGEO = $dbo->fetchArray( $QryProdu ) )
	{
           $QryProdu_Estado_Actualizar = $dbo->query( "UPDATE Producto SET Publicar = 'N' WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."'");	

        }
        
}

$QryProdu = $dbo->query( "SELECT * FROM Producto WHERE Valor = '0'" );
if( $dbo->rows( $QryProdu ) > 0 )
{
	while( $ArrayQryGEO = $dbo->fetchArray( $QryProdu ) )
	{
           $QryProdu_Estado_Actualizar = $dbo->query( "UPDATE Producto SET Publicar = 'N' WHERE IDProducto = '".$ArrayQryGEO[IDProducto]."'");	

        }
        
}


	$dbo =& SIMDB::get();
	$dbo->close();
?>
