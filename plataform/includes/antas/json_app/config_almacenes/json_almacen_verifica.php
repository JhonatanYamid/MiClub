<?php
session_start();
ini_set('display_errors', 1); 
//ini_set('memory_limit','200M');
include("config.inc.php");
//json general para contenido de app mobile
//header("Content-type: application/json; charset=utf-8");


//$fechaencuestion = $_GET[fecha];
//Datos Recetas

	foreach($_POST as $key=>$val)
    	$_POST[$key] = SIMUtil::antiinjection($val);
	
	
	
	
	$referencia = trim($_POST["Referencia"]);		
	
	
        $SqlPuntoVenta = "SELECT IDPuntoVenta FROM PuntoVenta WHERE Publicar = 'S'
        AND IDPuntoVenta in ('1','10','17','4','6','22','15','27','26','13','3','8','5')
         ORDER BY RAND()";
        $QryPuntoVenta = $dbo->query( $SqlPuntoVenta );
        $NumPuntoVenta = $dbo->rows( $QryPuntoVenta );
			
	if( $NumPuntoVenta > 0 )
	{
            while($puntos = $dbo->fetchArray( $QryPuntoVenta ))
            {
		
			 $SqlExistencias = "SELECT PunVe.IDCiudad as IDCiudadPunVe, PunVe.IDPuntoVenta as IDPuntoVentaPunVe, PunVe.Nombre as NombrePunVe, Re.Numero as NumeroRe, Ta.Nombre as NombreTa, CodEsp.Existencias as ExistenciasCodEsp, CodEsp.Maximo as MaximoCodEsp, CodEsp.Minimo as MinimoCodEsp
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
			AND CodEsp.Existencias > 0
			";
			$QryExistencias = $dbo->query( $SqlExistencias );
			$NumExistencias = $dbo->rows( $QryExistencias );
			
			if( $NumExistencias > 0 )
			{
				
				$Existencias = $dbo->fetchArray( $QryExistencias );
                                
                if($Existencias["ExistenciasCodEsp"] <= $Existencias["MaximoCodEsp"] && $Existencias["ExistenciasCodEsp"] >= $Existencias["MinimoCodEsp"])
                                
				?>
				{
					"Talla": "<?php echo $Existencias["NombreTa"]?>"
				
				}
				<?
				
                                
                                
                               
			}
			
            }
	}
	
		
		
        
?>