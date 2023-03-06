<?php
session_start();
ini_set('display_errors', 1); 
//ini_set('memory_limit','200M');
include("config.inc.php");
//json general para contenido de app mobile
//header("Content-type: application/json; charset=utf-8");


//$fechaencuestion = $_GET[fecha];
//Datos Recetas

	//foreach($_POST as $key=>$val)
    	//$_POST[$key] = SIMUtil::antiinjection($val);
	
	
	
	$talla = $_POST["Talla"];
	
	$referencia = $_POST[Referencia];	
	
        $SqlPuntoVenta = "SELECT IDPuntoVenta FROM PuntoVenta WHERE Publicar = 'S'
       AND IDPuntoVenta in ('1','10','17','4','6','29','28','22','15','27','26','7','13','3','8','5','9')
         ORDER BY RAND()";
        
        $QryPuntoVenta = $dbo->query( $SqlPuntoVenta );
        $NumPuntoVenta = $dbo->rows( $QryPuntoVenta );
		                                                $array_puntos_de_venta = array(1=>1,10=>10,17=>17,4=>4,6=>6,29=>29,28=>28,22=>22,15=>15,27=>27,26=>26,7=>7,13=>13,3=>3,8=>8,5=>5,9=>9);
	
	if( sizeof( $array_puntos_de_venta ) > 0 )
						{
            foreach($array_puntos_de_venta as $key_puntos_de_venta => $datos_puntos_de_venta)
							{$puntos["IDPuntoVenta"] = $key_puntos_de_venta;
                
//			$SqlExistencias = "SELECT Re.IDPrecio, PunVe.IDCiudad as IDCiudadPunVe, PunVe.IDPuntoVenta as IDPuntoVentaPunVe, PunVe.Nombre as NombrePunVe, Re.Numero as NumeroRe, Ta.Nombre as NombreTa, CodEsp.Existencias as ExistenciasCodEsp, CodEsp.Maximo as MaximoCodEsp, CodEsp.Minimo as MinimoCodEsp
//			FROM 
//			Referencia as Re, 
//			CodificacionEspecifica as CodEsp, 
//			Talla as Ta, 
//			PuntoVenta as PunVe,
//			PuntoVentaReferencia as PunVeRe 
//			WHERE 
//			Re.IDReferencia = PunVeRe.IDReferencia
//			AND PunVe.IDPuntoVenta = PunVeRe.IDPuntoVenta
//			AND CodEsp.IDPuntoVentaReferencia = PunVeRe.IDPuntoVentaReferencia
//			AND Ta.IDTalla = CodEsp.IDTalla
//			AND	SUBSTR(Re.Numero,1,6) = '".$referencia."'
//			AND Ta.Nombre = '".$talla."'
//			AND PunVe.IDPuntoVenta = '".$puntos["IDPuntoVenta"]."'
//			
//			";
                $SqlExistencias = "SELECT Re.IDPrecio, PunVe.IDCiudad as IDCiudadPunVe, PunVe.IDPuntoVenta as IDPuntoVentaPunVe, PunVe.Nombre as NombrePunVe, Re.Numero as NumeroRe, Ta.Nombre as NombreTa, CodEsp.Existencias as ExistenciasCodEsp, CodEsp.Maximo as MaximoCodEsp, CodEsp.Minimo as MinimoCodEsp
			FROM 
			Referencia as Re, 
			CodificacionEspecifica as CodEsp, 
			Talla as Ta, 
			PuntoVenta as PunVe,
			PuntoVentaReferencia as PunVeRe 
			WHERE 
			Re.IDReferencia = PunVeRe.IDReferencia
			AND PunVe.IDPuntoVenta = PunVeRe.IDPuntoVenta
			AND CodEsp.IDPuntoVentaReferencia = PunVeRe.IDPuntoVentaReferencia
			AND Ta.IDTalla = CodEsp.IDTalla
			AND	SUBSTR(Re.Numero,1,6) = '".$referencia."'
			AND PunVe.IDPuntoVenta = '".$puntos["IDPuntoVenta"]."'
			
			";
			$QryExistencias = $dbo->query( $SqlExistencias );
			$NumExistencias = $dbo->rows( $QryExistencias );
			
			if( $NumExistencias > 0 )
			{
				
				$Existencias = $dbo->fetchArray( $QryExistencias );
                                
                              
                                    
                                    
                        $QryPrecio = $dbo->query( "SELECT ValorVenta, Descuento FROM Precio WHERE IDPrecio = '".$Existencias["IDPrecio"]."'" );     
                        $PrecioArray = $dbo->fetchArray( $QryPrecio );
			$precio_anterior =  $PrecioArray["ValorVenta"];
                        
			if($PrecioArray["Descuento"] != 0)
                            $PrecioArray["ValorVenta"] = $PrecioArray["ValorVenta"]-(($PrecioArray["ValorVenta"]*$PrecioArray["Descuento"])/100); 
                                
				?>
				{
					"IDPuntoVenta": "<?php echo $Existencias["IDPuntoVentaPunVe"]?>",
					"IDCiudad": "<?php echo $Existencias["IDCiudadPunVe"]?>",
					"PuntoVenta": "<?php echo $Existencias["NombrePunVe"]?>",
					"Referencia": "<?php echo $Existencias["NumeroRe"]?>",
					"Talla": "<?php echo $Existencias["NombreTa"]?>",
					"Existencias": "2",
					"Maximo": "<?php echo $Existencias["MaximoCodEsp"]?>",
					"Minimo": "<?php echo $Existencias["MinimoCodEsp"]?>",
					"tallaenviada": "<?php echo $talla?>",
                                        "ValorVenta": "<?php echo $PrecioArray["ValorVenta"]?>",
                                        "PrecioAnterior": "<?php echo $precio_anterior?>",
                                        "Descuento": "<?php echo $PrecioArray["Descuento"]?>",
					"referenciaenviada": "<?php echo $referencia?>"
				
				}
				<?
							   exit;
								
                                
                                
                             
			}
			
            }
	}
	
		if($Existencias["NombreTa"] == 0)
		{
                   
                      $SqlPrecioNuevo = "SELECT Re.IDPrecio, Re.Saldo
			FROM 
			Referencia as Re 
			
			WHERE 
			Re.Numero = '".$referencia."'
                        AND Re.Publicar = 'S'
                        AND Re.Saldo = 'N'
			";
                                        $QryPrecioNuevo = $dbo->query($SqlPrecioNuevo);
                                        $PrecioNuevo =  $dbo->fetchArray( $QryPrecioNuevo );
                    
                     $precio = $PrecioNuevo["IDPrecio"]; 
                        $QryPrecio = $dbo->query( "SELECT ValorVenta, Descuento FROM Precio WHERE IDPrecio = '".$precio."'" );     
                        $PrecioArray = $dbo->fetchArray( $QryPrecio );
			$precio_anterior =  $PrecioArray["ValorVenta"];
                        
			if($PrecioArray["Descuento"] != 0)
                            $PrecioArray["ValorVenta"] = $PrecioArray["ValorVenta"]-(($PrecioArray["ValorVenta"]*$PrecioArray["Descuento"])/100); 
                    
		?>
                    {
                    "IDPuntoVenta": "0",
                    "IDCiudad": "0",
                    "PuntoVenta": "0",
                    "Referencia": "0",
                    "Talla": "0",
                    "Existencias": "5",
                    "Maximo": "0",
                    "Minimo": "0",
                    "tallaenviada": "<?php echo $talla?>",
                    "ValorVenta": "<?php echo $PrecioArray["ValorVenta"]?>",
                                        "PrecioAnterior": "<?php echo $precio_anterior?>",
                                        "Descuento": "<?php echo $PrecioArray["Descuento"]?>",
                "referenciaenviada": "<?php echo $referencia?>"

                    }
                <?	
		exit;
		}
		
        
?>